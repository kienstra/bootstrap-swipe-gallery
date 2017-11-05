<?php
/**
 * Tests for class Plugin.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Tests for class Plugin.
 *
 * @package BootstrapSwipeGallery
 */
class Test_Class_Plugin extends \WP_UnitTestCase {

	/**
	 * Instance of plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = Plugin::get_instance();
	}

	/**
	 * Test construct().
	 *
	 * @see Plugin::__construct().
	 */
	public function test_get_instance() {
		$this->assertEquals( Plugin::get_instance(), $this->plugin );
		$this->assertEquals( __NAMESPACE__ . '\Plugin', get_class( Plugin::get_instance() ) );
		$this->assertEquals( '1.0.4', Plugin::VERSION );
		$this->assertEquals( plugins_url( Plugin::SLUG ), $this->plugin->location );
	}

	/**
	 * Test init().p
	 *
	 * @see Plugin::init().
	 */
	public function test_init() {
		$this->plugin->init();
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Modal_Carousel' ) );

		$this->assertEquals( __NAMESPACE__ . '\Modal_Setup', get_class( $this->plugin->components->modal_setup ) );
		$this->assertEquals( __NAMESPACE__ . '\Options', get_class( $this->plugin->components->options ) );

		$this->assertEquals( 10, has_action( 'init', array( $this->plugin, 'textdomain' ) ) );
	}

	/**
	 * Test load_files().
	 *
	 * @see Plugin::load_files().
	 */
	public function test_load_files() {
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Assets' ) );
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Modal_Carousel' ) );
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Modal_Setup' ) );
		$this->assertTrue( class_exists( __NAMESPACE__ . '\Options' ) );
	}

	/**
	 * Test init_classes().
	 *
	 * @see Plugin::init_classes().
	 */
	public function test_init_classes() {
		$this->plugin->init_classes();
		$this->assertEquals( __NAMESPACE__ . '\Assets', get_class( $this->plugin->components->assets ) );
		$this->assertEquals( __NAMESPACE__ . '\Modal_Setup', get_class( $this->plugin->components->modal_setup ) );
		$this->assertEquals( __NAMESPACE__ . '\Options', get_class( $this->plugin->components->options ) );
		$this->assertEquals( 10, has_action( 'loop_end', array( $this->plugin->components->modal_setup, 'echo_galleries' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->plugin->components->options, 'plugin_page' ) ) );
	}

}
