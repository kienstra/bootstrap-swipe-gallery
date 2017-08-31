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
		register_activation_hook( __FILE__, array( $this, 'modal_option' ) );
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
		$this->components['options'] = new Options( $this );
		$this->components['modal_setup'] = new Modal_Setup( $this );
		$this->components['options']->init();
		$this->components['modal_setup']->init();
	}

	/**
	 * Adds the option for whether to display modals for all images.
	 *
	 * And sets a default value.
	 * This appears in 'Settings' > 'Swipe Gallery.'
	 *
	 * @return void
	 */
	public function modal_option() {
		add_option( 'bsg_plugin_options', array( $this->components['options']->carousel_option, $this->components['options']->default_option ) );
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
		if ( isset( $post ) && $this->post_should_have_a_swipe_gallery( $post ) ) {
			wp_enqueue_style( $this->slug . '-carousel', plugins_url( '/css/bsg-carousel.css', __FILE__ ), $this->version );
			wp_enqueue_script( $this->slug . '-jquery-mobile-swipe', plugins_url( '/js/jquery.mobile.custom.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->slug . '-modal-setup', plugins_url( '/js/gallery-modal.js', __FILE__ ), array( 'jquery', $this->slug . '-jquery-mobile-swipe' ), $this->version, true );
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
		$do_allow = ( $this->components['modal_setup']->do_make_carousel_of_post_images() ) ? true : false;
		wp_localize_script(
			$this->slug . '-modal-setup',
			'bsgDoAllow',
			array(
				'postImageCarousels' => $do_allow,
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
		return ( $this->components['modal_setup']->post_has_a_gallery() || $this->components['modal_setup']->do_make_carousel_of_post_images() );
	}

	/**
	 * Whether the current post has a gallery of images.
	 *
	 * @return bool $has_gallery Whether the post has a gallery of images.
	 */
	public function post_has_a_gallery() {
		$galleries = get_post_galleries( get_post(), false );
		if ( is_array( $galleries ) && ( ! array() === $galleries ) ) {
			return true;
		}
	}

}
