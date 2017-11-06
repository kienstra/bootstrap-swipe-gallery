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
	const VERSION = '1.0.5';

	/**
	 * Plugin slug.
	 *
	 * @const string
	 */
	const SLUG = 'bootstrap-swipe-gallery';

	/**
	 * URL of the plugin.
	 *
	 * @var object
	 */
	public $location;

	/**
	 * Instantiated plugin classes.
	 *
	 * @var object
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
		$this->location = plugins_url( self::SLUG );
		add_action( 'init', array( $this, 'textdomain' ) );
	}

	/**
	 * Load the plugin files.
	 *
	 * @return void
	 */
	public function load_files() {
		require_once __DIR__ . '/class-assets.php';
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
		load_plugin_textdomain( Plugin::SLUG );
	}
}
