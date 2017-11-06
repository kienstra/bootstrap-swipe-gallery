<?php
/**
 * Tests for class Modal_Setup.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Tests for class Modal_Setup.
 *
 * @package BootstrapSwipeGallery
 */
class Test_Class_Modal_Setup extends \WP_UnitTestCase {
	/**
	 * Instance of plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Instance of tested class.
	 *
	 * @var object
	 */
	public $instance;

	/**
	 * Galleries to pass to a filter.
	 *
	 * @var array
	 */
	public $mock_galleries;

	/**
	 * Mock URL for an image.
	 *
	 * @var string
	 */
	public $url_1 = '/example/path';

	/**
	 * Mock URL for a second image.
	 *
	 * @var string
	 */
	public $url_2 = 'baz/path';

	/**
	 * Image ID, created with a factory method.
	 *
	 * @var integer
	 */
	public $image_1;

	/**
	 * Image ID for second image, created with a factory method.
	 *
	 * @var integer
	 */
	public $image_2;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = Plugin::get_instance();
		$this->instance = $this->plugin->components->modal_setup;
	}

	/**
	 * Test construct().
	 *
	 * @see Modal_Setup::__construct().
	 */
	public function test_construct() {
		$this->assertEquals( __NAMESPACE__ . '\Modal_Setup', get_class( $this->instance ) );
		$this->assertEquals( Plugin::get_instance(), $this->instance->plugin );
	}

	/**
	 * Test init().
	 *
	 * @see Modal_Setup::init().
	 */
	public function test_init() {
		$this->instance->init();
		$this->assertEquals( 10, has_filter( 'loop_end', array( $this->instance, 'echo_galleries' ) ) );
		$this->assertEquals( 10, has_filter( 'loop_end', array( $this->instance, 'create_carousel' ) ) );
	}

	/**
	 * Test create_galleries().
	 *
	 * @see Modal_Setup::create_galleries().
	 */
	public function test_echo_galleries() {
		global $post;
		$this->set_mock_images();

		$post = $this->factory()->post->create( array( // WPCS: override ok.
			'post_content' => '[gallery ids="' . strval( $this->image_1 ) . ',' . strval( $this->image_2 ) . '"]',
		) );
		$this->mock_galleries = array(
			array(
				'columns' => 4,
				'ids'     => strval( $this->image_1 ) . ',' . strval( $this->image_2 ),
				'src'     => array(
					$this->url_1,
					$this->url_2,
				),
			),
		);
		add_filter( 'get_post_galleries', array( $this, 'add_post_galleries' ) );
		ob_start();
		$this->instance->echo_galleries();
		$markup = ob_get_clean();
		$this->assertContains( $this->url_1, $markup );
		$this->assertContains( $this->url_2, $markup );
		$this->assertContains( '<div class="modal-dialog modal-lg">', $markup );
		$this->assertContains( 'gallery-1', $markup );
	}

	/**
	 * Add post galleries, in order to use them in a test.
	 *
	 * @param array $galleries Image galleries.
	 * @return array $mock_galleries Mock image galleries for testing.
	 */
	public function add_post_galleries( $galleries ) {
		return $this->mock_galleries;
	}

	/**
	 * Test echo_modal_carousel().
	 *
	 * @see Modal_Setup::echo_modal_carousel().
	 */
	public function test_echo_modal_carousel() {
		$this->set_mock_images();
		ob_start();
		$this->instance->echo_modal_carousel( array( $this->image_1, $this->image_2 ) );
		$markup = ob_get_clean();

		$this->assertContains( $this->url_1, $markup );
		$this->assertContains( $this->url_2, $markup );
	}

	/**
	 * Test get_image_ids_from_gallery().
	 *
	 * @see Modal_Setup::get_image_ids_from_gallery().
	 */
	public function test_get_image_ids_from_gallery() {
		$image_1 = '1234';
		$image_2 = '9876';
		$gallery = array(
			'ids' => $image_1 . ',' . $image_2,
		);
		$image_ids = $this->instance->get_image_ids_from_gallery( $gallery );
		$this->assertEquals( array( $image_1, $image_2 ), $image_ids );
	}

	/**
	 * Test create_carousel().
	 *
	 * @see Modal_Setup::create_carousel().
	 */
	public function test_create_carousel() {
		global $post, $wp_query;

		$this->set_mock_images();
		$post = $this->factory()->post->create( array( // WPCS: override ok.
			'post_content' => 'wp-image-' . strval( $this->image_1 ) . ' wp-image-' . strval( $this->image_2 ),
		) );

		ob_start();
		$this->instance->create_carousel();
		$empty_markup = ob_get_clean();
		$this->assertEmpty( $empty_markup );

		$wp_query->is_single = true; // WPCS: override ok.
		update_option(
			$this->instance->plugin->components->options->plugin_options,
			array(
				$this->instance->plugin->components->options->carousel_option => '1',
			)
		);
		ob_start();
		$this->instance->create_carousel();
		$markup = ob_get_clean();
		$this->assertEmpty( $markup );
	}

	/**
	 * Test do_make_carousel_of_post_images().
	 *
	 * @see Modal_Setup::do_make_carousel().
	 */
	public function test_do_make_carousel_of_post_images() {
		global $post, $wp_query;
		$this->assertFalse( $this->instance->do_make_carousel() );
		$wp_query->is_single = true;
		$wp_query->is_page = true;
		$this->assertFalse( $this->instance->do_make_carousel() );

		update_option(
			$this->instance->plugin->components->options->plugin_options,
			array(
				$this->instance->plugin->components->options->carousel_option => '1',
			)
		);
		$post = $this->factory()->post->create( array( // WPCS: override OK.
			'post_content' => 'wp-image-3 wp-image-4',
		) );
		setup_postdata( $post );
		$this->assertTrue( $this->instance->do_make_carousel() );
	}

	/**
	 * Create and set the mock images.
	 *
	 * @return void
	 */
	public function set_mock_images() {
		$this->image_1 = $this->factory()->attachment->create_object(
			$this->url_1,
			0,
			array(
				'post_mime_type' => 'image/jpeg',
			)
		);
		$this->image_2 = $this->factory()->attachment->create_object(
			$this->url_2,
			0,
			array(
				'post_mime_type' => 'image/jpeg',
			)
		);
	}
}
