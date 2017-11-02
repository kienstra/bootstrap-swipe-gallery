<?php
/**
 * Main class for the Bootstrap Swipe Gallery plugin
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Main plugin class
 */
class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0.4';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $slug = 'bootstrap-swipe-gallery';

	/**
	 * Instantiated plugin classes.
	 *
	 * @var array
	 */
	public $components;

	/**
	 * Get the instance of this plugin
	 *
	 * @return object $instance Plugin instance.
	 */
	public static function get_instance() {
		static $instance;

		if ( ! $instance instanceof Plugin ) {
			$instance = new Plugin();
		}

		return $instance;
	}

	/**
	 * Init the plugin.
	 *
	 * Load the files, instantiate the classes, and call their init() methods.
	 * And register the main plugin actions.
	 *
	 * @return void
	 */
	public function init() {
		$this->load_files();
		$this->init_classes();
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_asset' ) );
	}

	/**
	 * Load the plugin files.
	 *
	 * @return void
	 */
	public function load_files() {
		require_once __DIR__ . '/class-modal-carousel.php';
		require_once __DIR__ . '/class-modal-setup.php';
		require_once __DIR__ . '/class-options.php';
	}

	/**
	 * Instantiate the plugin classes, and call their init() methods.
	 *
	 * @return void
	 */
	public function init_classes() {
		$this->components = new \stdClass();
		$this->components->options = new Options( $this );
		$this->components->modal_setup = new Modal_Setup( $this );
		$this->components->assets = new Assets( $this );
		$this->components->options->init();
		$this->components->modal_setup->init();
		$this->components->assets->init();
	}

	/**
	 * Load the plugin's textdomain.
	 *
	 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( $this->slug );
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
		if ( $this->post_should_have_a_swipe_gallery() ) {
			wp_enqueue_style( $this->slug . '-carousel', plugins_url( $this->slug . '/css/bsg-carousel.css' ), $this->version );
			wp_enqueue_script( $this->slug . '-jquery-mobile-swipe', plugins_url( $this->slug . '/js/jquery.mobile.custom.min.js' ), array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->slug . '-modal-setup', plugins_url( $this->slug . '/js/gallery-modal.js' ), array( 'jquery', $this->slug . '-jquery-mobile-swipe' ), $this->version, true );
		}
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
			$this->slug . '-modal-setup',
			'bsgDoAllow',
			array(
				'postImageCarousels' => intval( $this->components['modal_setup']->do_make_carousel_of_post_images() ),
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
	public function post_should_have_a_swipe_gallery() {
		return ( $this->post_has_a_gallery() || $this->components['modal_setup']->do_make_carousel_of_post_images() );
	}

	/**
	 * Whether the current post has a gallery of images.
	 *
	 * @return bool $has_gallery Whether the post has a gallery of images.
	 */
	public function post_has_a_gallery() {
		$galleries = get_post_galleries( get_post(), false );
		if ( is_array( $galleries ) && ( array() !== $galleries ) ) {
			return true;
		}
		return false;
	}

}
