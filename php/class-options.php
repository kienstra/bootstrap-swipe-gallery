<?php
/**
 * Options class
 *
 * @package BootstrapSwipeGallery
 */

namespace BootstrapSwipeGallery;

/**
 * Adds an options page.
 */
class Options {
	/**
	 * Instance of the plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Key for the plugin options.
	 *
	 * @var string
	 */
	public $plugin_options = 'bsg_plugin_options';

	/**
	 * Key for the plugin options.
	 *
	 * @var string
	 */
	public $options_page = 'bsg_options_page';

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
	 * Options constructor.
	 *
	 * @param object $plugin Instance of the plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add action and filter hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'plugin_page' ) );
		add_action( 'admin_init', array( $this, 'settings_setup' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 2, 2 );
	}

	/**
	 * Add the plugin options page.
	 *
	 * @return void
	 */
	public function plugin_page() {
		add_options_page(
			__( 'Bootstrap Swipe Gallery Settings', 'bootstrap-swipe-gallery' ),
			__( 'Swipe Gallery', 'bootstrap-swipe-gallery' ),
			'manage_options',
			$this->plugin_options,
			array( $this, 'plugin_options_page' )
		);
	}

	/**
	 * Echo the markup for the plugin options page.
	 *
	 * This appears in 'Settings' > 'Swipe Gallery.'
	 * And it controls whether to create a carousel for non-gallery post images.
	 *
	 * @return void
	 */
	public function plugin_options_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Bootstrap Swipe Gallery', 'bootstrap-swipe-gallery' ); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields( $this->plugin_options ); ?>
				<?php do_settings_sections( $this->plugin_options ); ?>
				<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
			</form>
		</div>
		<?php
	}

	/**
	 * Validate whether the plugin option entry is '1' or '0.'
	 *
	 * @param string $value Settings input, to be evaluated.
	 * @return boolean $is_one_or_zero Whether the $value is '1' or '0.'
	 */
	public function is_one_or_zero( $value ) {
		return ( '1' === $value ) || ( '0' === $value );
	}

	/**
	 * Register plugin options setting, and add the settings section and fields.
	 *
	 * @return void
	 */
	public function settings_setup() {
		register_setting( $this->plugin_options, $this->plugin_options, array( $this, 'validate_options' ) );
		add_settings_section(
			'bsg_plugin_primary',
			__( 'Settings', 'bootstrap-swipe-gallery' ),
			'__return_false',
			$this->plugin_options
		);
		add_settings_field(
			$this->carousel_option,
			__( 'Create pop-up for all post and page images, not just galleries', 'bootstrap-swipe-gallery' ),
			array( $this, 'settings_display' ),
			$this->plugin_options,
			'bsg_plugin_primary'
		);
	}

	/**
	 * Output the markup for the carousel option.
	 *
	 * This displays in 'Settings' > 'Swipe Gallery.'
	 * And it controls whether to create a carousel for all post images.
	 *
	 * @return void
	 */
	public function settings_display() {
		$name = $this->plugin_options . '[' . $this->carousel_option . ']';
		$options = get_option( $this->plugin_options, $this->default_option );
		$allow_carousel_all_posts = isset( $options[ $this->carousel_option ] ) ? $options[ $this->carousel_option ] : $this->default_option;
		echo '<input type="checkbox" name="' . esc_attr( $name ) . '"' . checked( $allow_carousel_all_posts, '1', false ) . ' value="1"/>';
	}

	/**
	 * Validate the user input in the plugin option.
	 *
	 * @param array $input Option values, as input by user.
	 * @return array $validated Populated with the value from $input, if it's valid.
	 */
	public function validate_options( $input ) {
		$validated = array();
		$is_valid = (
			isset( $input[ $this->carousel_option ] )
			&&
			(
				( '1' === $input[ $this->carousel_option ] )
				||
				( '0' === $input[ $this->carousel_option ] )
			)
		);

		if ( $is_valid ) {
			$validated[ $this->carousel_option ] = $input[ $this->carousel_option ];
		}
		return $validated;
	}

	/**
	 * Add a 'Settings' link on the main plugin page
	 *
	 * @param array  $actions Links to plugin actions.
	 * @param string $plugin_file The plugin file's path.
	 * @return array $actions This plugin's actions, possibly including the new 'Settings' link.
	 */
	public function settings_link( $actions, $plugin_file ) {
		if ( false !== strpos( $plugin_file, $this->plugin->location ) ) {
			$actions['settings'] = '<a href="options-general.php?page=bsg_options_page">' . esc_html__( 'Settings', 'bootstrap-swipe-gallery' ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Whether the options allow a carousels for all post images.
	 *
	 * This gets the value of the option in 'Settings' > 'Swipe Gallery.'
	 * Carousels of image galleries are supported by default.
	 * But this option controls whether all images in a post should be in a modal carousel.
	 *
	 * @return bool $all_carousel_all_for_post_images Whether to output a carousel for all post images.
	 */
	public function allow_carousel_for_post_images() {
		$plugin_options = get_option( $this->plugin_options );
		if ( isset( $plugin_options[ $this->carousel_option ] ) ) {
			return ( '1' === $plugin_options[ $this->carousel_option ] );
		} else {
			return false;
		}
	}
}
