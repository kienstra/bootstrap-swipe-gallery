<?php
/**
 * Main class for the assets.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Assets class.
 */
class Assets {

	/**
	 * Instance of the plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Slug for the carousel stylesheet.
	 *
	 * @const string.
	 */
	const CAROUSEL_SLUG = 'bsg-carousel';

	/**
	 * Slug for the jQuery mobile swipe script.
	 *
	 * @const string.
	 */
	const MOBILE_SWIPE_SLUG = 'bsg-jquery-mobile-swipe';

	/**
	 * Slug for the modal setup script.
	 *
	 * @const string.
	 */
	const MODAL_SETUP_SLUG = 'bsg-modal-setup';

	/**
	 * Assets constructor.
	 *
	 * @param object $plugin Instance of the plugin.
	 */
	function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Init the assets.
	 *
	 * Load the files, instantiate the classes, and call their init() methods.
	 * And register the main plugin actions.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_asset' ) );
	}

	/**
	 * Conditionally enqueue the plugin assets.
	 *
	 * Only if the page will have a swipe gallery.
	 * The enqueued files include a vendor jQuery Mobile file, with an MIT license.
	 *
	 * @see https://jquery.org/license.
	 * @return void
	 */
	public function enqueue_assets() {
		$this->register_assets();
		if ( $this->has_swipe_gallery() ) {
			wp_enqueue_style( self::CAROUSEL_SLUG );
			wp_enqueue_script( self::MOBILE_SWIPE_SLUG );
			wp_enqueue_script( self::MODAL_SETUP_SLUG );
		}
	}

	/**
	 * Conditionally enqueue the plugin assets.
	 *
	 * Only if the page will have a swipe gallery.
	 * The enqueued files include a vendor jQuery Mobile file, with an MIT license.
	 *
	 * @see https://jquery.org/license.
	 * @return void
	 */
	public function register_assets() {
		wp_register_style( self::CAROUSEL_SLUG, plugins_url( $this->plugin->slug . '/css/bsg-carousel.css' ), array(), $this->plugin->version );
		wp_register_script( self::MOBILE_SWIPE_SLUG, plugins_url( $this->plugin->slug . '/js/jquery.mobile.custom.min.js' ), array( 'jquery' ), $this->plugin->version, true );
		wp_register_script( self::MODAL_SETUP_SLUG, plugins_url( $this->plugin->slug . '/js/gallery-modal.js' ), array( 'jquery', self::MOBILE_SWIPE_SLUG ), $this->plugin->version, true );
	}

	/**
	 * Localize a value for the main JavaScript file.
	 *
	 * The file needs access to a value for whether or not it should make a carousel of all post images.
	 * If this is true, clicking an image will trigger opening a modal with a carousel.
	 *
	 * @return void
	 */
	public function localize_asset() {
		wp_localize_script(
			self::MODAL_SETUP_SLUG,
			'bsgDoAllow',
			array(
				'postImageCarousels' => intval( $this->plugin->components->modal_setup->do_make_carousel_of_post_images() ),
			)
		);
	}

	/**
	 * Whether the post should have a swipe gallery.
	 *
	 * This is needed to determine if this should enqueue the JavaScript file.
	 * It's based on whether or not the post has a gallery and whether post images should have a swipe gallery.
	 *
	 * @return bool $should_have_gallery
	 */
	public function has_swipe_gallery() {
		$post = get_post();
		if ( isset( $post->post_content ) ) {
			return ( has_shortcode( $post->post_content, 'gallery' ) || $this->plugin->components->modal_setup->do_make_carousel_of_post_images() );
		}
		return false;
	}

}
