<?php

add_action( 'admin_menu' , 'bsg_plugin_page' );
function bsg_plugin_page() {
	add_options_page(
		__( 'Bootstrap Swipe Gallery Settings' , 'bootstrap-swipe-gallery' ) ,
		__( 'Swipe Gallery' , 'bootstrap-swipe-gallery' ) ,
		'manage_options' ,
		'bsg_options_page' ,
		'bsg_plugin_options_page_text' );
}

function bsg_plugin_options_page_text() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Bootstrap Swipe Gallery</h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'bsg_plugin_options' ); ?>
			<?php do_settings_sections( 'bsg_options_page' ); ?>
			<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
		</form>
	</div>
<?php
}

function bsg_is_one_or_zero( $value ) {
	return ( '1' == $value ) || ( '0' == $value );
}

add_action( 'admin_init' , 'bsg_settings_setup' );
function bsg_settings_setup() {
	register_setting( 'bsg_plugin_options' , 'bsg_plugin_options' , 'bsg_plugin_validate_options' );

	function bsg_plugin_validate_options( $input ) {
		$setting_key =	'bsg_allow_carousel_for_all_post_images';
		if ( bsg_is_one_or_zero( $input[ $setting_key ] ) ) {
			$validated[ $setting_key ] = $input[ $setting_key ];
		}
		return $validated;
	}

	add_settings_section( 'bsg_plugin_primary' , __( 'Settings' , 'bootstrap-swipe-gallery' ) ,
			 'bsg_plugin_section_text', 'bsg_options_page'	);

	function bsg_plugin_section_text() {
		return;
	}

	add_settings_field( 'bsg_allow_carousel_for_all_post_images' , __( 'Create pop-up for all post and page images, not just galleries' , 'bootstrap-swipe-gallery' ) , 'bsg_output_callback' , 'bsg_options_page' , 'bsg_plugin_primary' );

	function bsg_output_callback() {
		$name = 'bsg_plugin_options[bsg_allow_carousel_for_all_post_images]';
		$options = get_option( 'bsg_plugin_options' );
		$allow_carousel_all_posts = isset( $options[ 'bsg_allow_carousel_for_all_post_images' ] ) ? $options[ 'bsg_allow_carousel_for_all_post_images' ] : '0';

		?>
			<input type="checkbox" name="<?php echo $name; ?>" <?php checked( $allow_carousel_all_posts , '1' , true ); ?> value="1"/>
	<?php
	}
}

// Add settings link on the main plugin page
add_filter( 'plugin_action_links' , 'bsg_add_settings_link' , 2 , 2 );
function bsg_add_settings_link( $actions, $file ) {
if ( false !== strpos( $file, BSG_PLUGIN_SLUG ) ) {
		$actions[ 'settings' ] = '<a href="options-general.php?page=bsg_options_page">Settings</a>';
	}
	return $actions;
}