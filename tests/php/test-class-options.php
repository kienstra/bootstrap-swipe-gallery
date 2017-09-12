<?php
/**
 * Tests for class Options.
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Tests for class Options.
 *
 * @package BootstrapSwipeGallery
 */
class Test_Class_Options extends \WP_UnitTestCase {

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
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = Plugin::get_instance();
		$this->instance = $this->plugin->components['options'];
	}

	/**
	 * Test construct().
	 *
	 * @see Options::__construct().
	 */
	public function test_construct() {
		$this->assertEquals( __NAMESPACE__ . '\Options', get_class( $this->instance ) );
		$this->assertEquals( 'bsg_plugin_options', $this->instance->plugin_options );
		$this->assertEquals( 'bsg_options_page', $this->instance->options_page );
		$this->assertEquals( 'bsg_allow_carousel_for_all_post_images', $this->instance->carousel_option );
		$this->assertEquals( '0', $this->instance->default_option );
	}

	/**
	 * Test init().
	 *
	 * @see Options::init().
	 */
	public function test_init() {
		$this->instance->init();
		$this->assertEquals( 10, has_filter( 'admin_menu', array( $this->instance, 'plugin_page' ) ) );
		$this->assertEquals( 10, has_filter( 'admin_init', array( $this->instance, 'settings_setup' ) ) );
		$this->assertEquals( 2, has_filter( 'plugin_action_links', array( $this->instance, 'settings_link' ) ) );
	}

	/**
	 * Test plugin_page().
	 *
	 * @see Options::plugin_page().
	 */
	public function test_plugin_page() {
		global $submenu;
		$administrator = $this->factory()->user->create( array(
			'role' => 'administrator',
		) );
		wp_set_current_user( $administrator );
		$this->instance->plugin_page();
		$plugin_submenu = reset( $submenu['options-general.php'] );
		$this->assertEquals( 'Swipe Gallery', $plugin_submenu[0] );
		$this->assertEquals( 'manage_options', $plugin_submenu[1] );
		$this->assertEquals( $this->instance->plugin_options, $plugin_submenu[2] );
		$this->assertEquals( 'Bootstrap Swipe Gallery Settings', $plugin_submenu[3] );
	}

	/**
	 * Test plugin_options_page().
	 *
	 * @see Options::plugin_options_page().
	 */
	public function test_plugin_options_page() {
		ob_start();
		$this->instance->plugin_options_page();
		$markup = ob_get_clean();

		$this->assertContains( '<div class="wrap">', $markup );
		$this->assertContains( 'Bootstrap Swipe Gallery', $markup );
		$this->assertContains( $this->instance->plugin_options, $markup );
		$this->assertContains( '<input name="Submit" type="submit" value="Save Changes" class="button-primary" />', $markup );
	}

	/**
	 * Test is_one_or_zero().
	 *
	 * @see Options::is_one_or_zero().
	 */
	public function test_is_one_or_zero() {
		$this->assertFalse( $this->instance->is_one_or_zero( 1 ) );
		$this->assertFalse( $this->instance->is_one_or_zero( 0 ) );
		$this->assertFalse( $this->instance->is_one_or_zero( true ) );
		$this->assertFalse( $this->instance->is_one_or_zero( '' ) );
		$this->assertTrue( $this->instance->is_one_or_zero( '0' ) );
		$this->assertTrue( $this->instance->is_one_or_zero( '1' ) );
	}

	/**
	 * Test settings_setup().
	 *
	 * @see Options::settings_setup().
	 */
	public function test_settings_setup() {
		global $new_whitelist_options, $wp_registered_settings, $wp_settings_sections, $wp_settings_fields;
		$this->instance->settings_setup();
		$plugin_whitelist = $new_whitelist_options[ $this->instance->plugin_options ];

		$this->assertTrue( is_array( $plugin_whitelist ) );
		$this->assertEquals( $this->instance->plugin_options, reset( $plugin_whitelist ) );

		$plugin_options = $wp_settings_sections[ $this->instance->plugin_options ];
		$primary_slug = 'bsg_plugin_primary';
		$primary = $plugin_options[ $primary_slug ];
		$plugin_settings = $wp_registered_settings[ $this->instance->plugin_options ];

		$this->assertTrue( is_array( $plugin_settings ) );
		$this->assertTrue( is_array( $primary ) );
		$this->assertEquals( '__return_false', $primary['callback'] );
		$this->assertEquals( $primary_slug, $primary['id'] );
		$this->assertEquals( 'Settings', $primary['title'] );

		$carousel_setting = $wp_settings_fields[ $this->instance->plugin_options ][ $primary_slug ][ $this->instance->carousel_option ];

		$this->assertTrue( is_array( $carousel_setting ) );
		$this->assertEquals( array(), $carousel_setting['args'] );
		$this->assertEquals( array( $this->instance, 'settings_display' ), $carousel_setting['callback'] );
		$this->assertEquals( $this->instance->carousel_option, $carousel_setting['id'] );
		$this->assertEquals( 'Create pop-up for all post and page images, not just galleries', $carousel_setting['title'] );
	}

	/**
	 * Test settings_display().
	 *
	 * @see Options::settings_display().
	 */
	public function test_settings_display() {
		ob_start();
		$this->instance->settings_display();
		$markup = ob_get_clean();

		$this->assertContains( $this->instance->plugin_options . '[' . $this->instance->carousel_option . ']', $markup );
		$this->assertContains( '1', $markup );
		$this->assertContains( '<input type="checkbox" name="', $markup );
	}

	/**
	 * Test validate_options().
	 *
	 * @see Options::validate_options().
	 */
	public function test_validate_options() {

		$empty_option = array();
		$this->assertEquals( array(), $this->instance->validate_options( $empty_option ) );

		$invalid_option_string = array(
			$this->instance->carousel_option => 'true',
		);
		$this->assertEquals( array(), $this->instance->validate_options( $invalid_option_string ) );

		$invalid_option_boolean = array(
			$this->instance->carousel_option => false,
		);
		$this->assertEquals( array(), $this->instance->validate_options( $invalid_option_boolean ) );

		$valid_option_default = array(
			$this->instance->carousel_option => '1',
		);
		$this->assertEquals( $valid_option_default, $this->instance->validate_options( $valid_option_default ) );

		$valid_option_checked = array(
			$this->instance->carousel_option => '1',
		);
		$this->assertEquals( $valid_option_checked, $this->instance->validate_options( $valid_option_checked ) );
	}

	/**
	 * Test settings_link().
	 *
	 * @see Options::settings_link().
	 */
	public function test_settings_link() {
		$incorrect_plugin_file = 'foo-plugin/foo-plugin.php';
		$filtered_actions = $this->instance->settings_link( array(), $incorrect_plugin_file );
		$this->assertFalse( isset( $filtered_actions['settings'] ) );

		$correct_plugin_file = $this->plugin->slug . '/' . $this->plugin->slug . '.php';
		$actions_with_settings = $this->instance->settings_link( array(), $correct_plugin_file );
		$this->assertEquals( '<a href="options-general.php?page=bsg_options_page">Settings</a>', $actions_with_settings['settings'] );
	}

	/**
	 * Test allow_carousel_for_post_images().
	 *
	 * @see Options::allow_carousel_for_post_images().
	 */
	public function test_allow_carousel_for_post_images() {
		add_option(
			$this->instance->plugin_options,
			array(
				$this->instance->carousel_option => '0',
			)
		);
		$this->assertFalse( $this->instance->allow_carousel_for_post_images() );

		update_option(
			$this->instance->plugin_options,
			array(
				$this->instance->carousel_option => '1',
			)
		);
		$this->assertTrue( $this->instance->allow_carousel_for_post_images() );

		update_option(
			$this->instance->plugin_options,
			array(
				$this->instance->carousel_option => true,
			)
		);
		$this->assertFalse( $this->instance->allow_carousel_for_post_images() );
	}

}
