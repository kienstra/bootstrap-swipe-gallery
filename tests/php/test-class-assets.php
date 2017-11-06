<?php
/**
 * Tests for class Assets.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Tests for class Assets.
 *
 * @package BootstrapSwipeGallery
 */
class Test_Class_Assets extends \WP_UnitTestCase {
	/**
	 * Instance of Assets.
	 *
	 * @var object
	 */
	public $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$plugin = Plugin::get_instance();
		$plugin->init();
		$this->instance = $plugin->components->assets;
	}

	/**
	 * Test __construct().
	 *
	 * @see Assets::__construct().
	 */
	public function test_construct() {
		$this->assertEquals( __NAMESPACE__ . '\Assets', get_class( $this->instance ) );
	}

	/**
	 * Test init().
	 *
	 * @see Assets::init().
	 */
	public function test_init() {
		$this->instance->init();
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $this->instance, 'enqueue_assets' ) ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $this->instance, 'inline_script' ) ) );
	}

	/**
	 * Test enqueue_assets().
	 *
	 * @see Assets::enqueue_assets().
	 */
	public function test_enqueue_assets() {
		$this->create_post_with_gallery();
		$this->instance->enqueue_assets();
	}

	/**
	 * Test register_assets().
	 *
	 * @see Assets::register_assets().
	 */
	public function test_register_assets() {
		$this->instance->register_assets();
		$styles = wp_styles();
		$carousel_style = $styles->registered[ Assets::CAROUSEL_SLUG ];
		$this->assertEquals( Assets::CAROUSEL_SLUG, $carousel_style->handle );
		$this->assertEquals( array(), $carousel_style->deps );
		$this->assertContains( '/css/bsg-carousel.css', $carousel_style->src );
		$this->assertEquals( Plugin::VERSION, $carousel_style->ver );

		$scripts = wp_scripts();
		$mobile_swipe = $scripts->registered[ Assets::MOBILE_SWIPE_SLUG ];
		$this->assertEquals( Assets::MOBILE_SWIPE_SLUG, $mobile_swipe->handle );
		$this->assertEquals( array( 'jquery' ), $mobile_swipe->deps );
		$this->assertContains( '/js/jquery.mobile.custom.min.js', $mobile_swipe->src );
		$this->assertEquals( Plugin::VERSION, $mobile_swipe->ver );

		$modal_setup = $scripts->registered[ Assets::MODAL_SETUP_SLUG ];
		$this->assertEquals( Assets::MODAL_SETUP_SLUG, $modal_setup->handle );
		$this->assertEquals( array( 'jquery', Assets::MOBILE_SWIPE_SLUG ), $modal_setup->deps );
		$this->assertContains( '/js/gallery-modal.js', $modal_setup->src );
		$this->assertEquals( Plugin::VERSION, $modal_setup->ver );
	}

	/**
	 * Test localize_asset
	 *
	 * @see Assets::localize_asset()
	 * @return void
	 */
	public function test_inline_script() {
		$this->create_post_without_gallery();
		do_action( 'wp_enqueue_scripts' );
		$data = wp_scripts()->registered[ Assets::MODAL_SETUP_SLUG ]->extra['after'];
		$this->assertContains( 'bsgGalleryModal', end( $data ) );
	}

	/**
	 * Test has_swipe_gallery().
	 *
	 * @see Assets::has_swipe_gallery().
	 */
	public function test_has_swipe_gallery() {
		$this->create_post_without_gallery();
		$this->assertFalse( $this->instance->has_swipe_gallery() );
		$this->create_post_with_gallery();
		$this->assertTrue( $this->instance->has_swipe_gallery() );
	}

	/**
	 * Create a post without gallery shortcode.
	 *
	 * @return void
	 */
	public function create_post_without_gallery() {
		global $post;
		$post_id = $this->factory()->post->create();
		$post = get_post( $post_id ); // WPCS: global override OK.
	}

	/**
	 * Create a post that has a gallery shortcode.
	 *
	 * @return void
	 */
	public function create_post_with_gallery() {
		global $post;
		$post_content = '[gallery ids="52,16,62]';
		$post_id = $this->factory()->post->create( array(
			'post_content' => $post_content,
		) );
		$post = get_post( $post_id ); // WPCS: global override OK.
	}
}
