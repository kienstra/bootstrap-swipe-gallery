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
	 */
	public function init() {
		$this->load_files();
		$this->init_classes();
		register_activation_hook( __FILE__, array( $this, 'deactivate_if_early_wordpress_version' ) );
		register_activation_hook( __FILE__, array( $this, 'activate_with_default_options' ) );
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_asset' ) );
	}

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

	public function deactivate_if_early_wordpress_version() {
		if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
		}
	}

	public function activate_with_default_options() {
		add_option( 'bsg_plugin_options', array( $this->components['options']->carousel_option, $this->components['options']->default_option ) );
	}

	public function textdomain() {
		load_plugin_textdomain( $this->slug );
	}

	public function enqueue_assets() {
		if ( isset( $post ) && $this->post_should_have_a_swipe_gallery( $post ) ) {
			wp_enqueue_style( $this->slug . '-carousel', plugins_url( '/css/bsg-carousel.css', __FILE__ ), $this->version );
			wp_enqueue_script( 'jquery' );
			// MIT license: https://jquery.org/license/
			wp_enqueue_script( $this->slug . '-jquery-mobile-swipe', plugins_url( '/js/jquery.mobile.custom.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->slug . '-modal-setup', plugins_url( '/js/gallery-modal.js', __FILE__ ), array( 'jquery', $this->slug . '-jquery-mobile-swipe' ), $this->version, true );
		}
	}

	public function localize_asset() {
		$do_allow = ( $this->components['modal_setup']->do_make_carousel_of_post_images() ) ? true : false;
		wp_localize_script(
			$this->slug . '-modal-setup',
			'bsg_do_allow',
			array(
				'post_image_carousels' => $do_allow,
			)
		);
	}

	public function post_should_have_a_swipe_gallery() {
		return ( $this->components['modal_setup']->post_has_a_gallery() || $this->components['modal_setup']->do_make_carousel_of_post_images() );
	}

	public function post_has_a_gallery() {
		$galleries = get_post_galleries( get_post(), false );
		if ( is_array( $galleries ) && ( ! array() === $galleries ) ) {
			return true;
		}
	}

}
