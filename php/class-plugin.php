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
	 * Key for the carousel options.
	 *
	 * @var string
	 */
	public $carousel_option = 'bsg_allow_carousel_for_all_post_images';

	/**
	 * Default value for the carousel option.
	 *
	 * @see $carousel_option
	 * @var string
	 */
	public $default_option = '0';

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
	 * Instantiate the class.
	 */
	private function __construct() {
		register_activation_hook( __FILE__, array( $this, 'deactivate_if_early_wordpress_version' ) );
		register_activation_hook( __FILE__, array( $this, 'activate_with_default_options' ) );
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_asset' ) );
	}

	public function deactivate_if_early_wordpress_version() {
		if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) );
		}
	}

	public function activate_with_default_options() {
		add_option( 'bsg_plugin_options', array( $this->carousel_option, $this->default_option ) );
	}

	public function textdomain() {
		load_plugin_textdomain( $this->slug );
	}

	public function load_files() {
		require_once __DIR__ . '/class-modal-carousel.php';
		require_once __DIR__ . '/gallery-modal-setup.php';
		require_once __DIR__ . '/class-options.php';
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
		$do_allow = ( $this->do_make_carousel_of_post_images() ) ? true : false;
		wp_localize_script(
			$this->slug . '-modal-setup',
			'bsg_do_allow',
			array(
				'post_image_carousels' => $do_allow,
			)
		);
	}

	public function post_should_have_a_swipe_gallery() {
		return ( $this->post_has_a_gallery() || $this->do_make_carousel_of_post_images() );
	}

	public function post_has_a_gallery() {
		$galleries = get_post_galleries( get_post(), false );
		if ( is_array( $galleries ) && ( ! array() === $galleries ) ) {
			return true;
		}
	}

	public function do_make_carousel_of_post_images() {
		return (
			( is_single() || is_page() )
			&&
			$this->options_allow_carousel_for_all_post_images()
			&&
			! empty( bsg_get_image_ids() )
		);
	}

	public function options_allow_carousel_for_all_post_images() {
		$plugin_options = get_option( 'bsg_plugin_options' );
		if ( isset( $plugin_options[ $this->carousel_option ] ) ) {
			return ( '1' === $plugin_options[ $this->carousel_option ] );
		} else {
			return false;
		}
	}

}
