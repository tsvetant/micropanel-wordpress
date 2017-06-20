<?php
/*
Plugin Name: MicroPanel for WordPress
Plugin URI: http://gamebird-studios.com/
Description: A small styling plugin for WordPress admin panel.
Version: 1.0.0
Author: Gamebird Studios
Author URI: http://gamebird-studios.com/
*/
define('MICROPANEL_VERSION', '1.0.0');
define('MICROPANEL_PLUGIN_URL', plugin_dir_url( __FILE__ ));


/*
** Loading styles
*/
function micropanel_style(){
	wp_enqueue_script("jquery");
	wp_enqueue_script("bootstrap-tooltip", MICROPANEL_PLUGIN_URL . 'assets/js/tooltip.js', array('jquery'));
	wp_enqueue_script('disableautologin-script',MICROPANEL_PLUGIN_URL . 'assets/js/disableautologin.js', array( 'jquery' ));
    wp_enqueue_style( 'open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic', false);
    wp_enqueue_style( 'micropanel-base', MICROPANEL_PLUGIN_URL.'assets/css/base.min.css', false, MICROPANEL_VERSION);
    wp_enqueue_style( 'micropanel', MICROPANEL_PLUGIN_URL.'assets/css/micropanel.min.css', false, MICROPANEL_VERSION);

}
function fb_move_admin_bar() {
    wp_enqueue_style( 'open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic', false);
    echo "<link rel='stylesheet' id='genericons-css'  href='".MICROPANEL_PLUGIN_URL."assets/css/micropanel-front.css' type='text/css' media='all' />";
}


/*
** Fix admin avatar resolution
*/
function modify_admin_bar( $wp_admin_bar ){
	$wp_admin_bar->remove_menu( 'my-account' );
	$user_id      = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url  = get_edit_profile_url( $user_id );
	if ( ! $user_id )
		return;
	$avatar = get_avatar( $user_id, 42 );
	$howdy  = sprintf( __('Howdy, %1$s'), $current_user->display_name );
	$class  = empty( $avatar ) ? '' : 'with-avatar';

	$wp_admin_bar->add_menu( 
		array(
			'id'        => 'my-account',
			'parent'    => 'top-secondary',
			'title'     => $howdy . $avatar,
			'href'      => $profile_url,
			'meta'      => array(
				'class'     => $class,
				'title'     => __('My Account'),
			),
		)
	);
}




function eg_settings_api_init() {
 	add_settings_section('micropanel_settings', 'MicroPanel Options', 'eg_setting_section_callback_function', 'general'); // Add the section to reading settings so we can add our fields to it
 		
 		add_settings_field('micropanel_enable', 'Enable MicroPanel', 'micropanel_enable_callback', 'general', 'micropanel_settings'); 
 		add_settings_field('micropanel_adminbar_front', 'Enable admin bar styling', 'micropanel_adminbar_front_callback', 'general', 'micropanel_settings'); 
 		
 		register_setting('general','micropanel_enable'); 
 		register_setting('general','micropanel_adminbar_front'); 

 	add_option( 'micropanel_enable', '1', '', 'yes' );
 	add_option( 'micropanel_adminbar_front', '1', '', 'yes' );
}// eg_settings_api_init()
function eg_setting_section_callback_function() {
}
function micropanel_enable_callback() {
	echo '<p class="description"><label><input name="micropanel_enable" id="gv_thumbnails_insert_into_excerpt" type="checkbox" value="1" class="code" ' . checked( 1, get_option('micropanel_enable'), false ) . ' /> Enable MicroPanel Admin style for WordPres</label></p>';
}
function micropanel_adminbar_front_callback() {
	echo '<p class="description"><label><input name="micropanel_adminbar_front" id="gv_thumbnails_insert_into_excerpt" type="checkbox" value="1" class="code" ' . checked( 1, get_option('micropanel_adminbar_front'), false ) . ' /> Enable MicroPanel Admin Bar styling for the Frontend</label></p>';
}


/*
** Adding the actions
*/
if(get_option('micropanel_enable') == 1) {
	
	// Disable color scheme selector when the theme is active
	function admin_color_scheme() {
	   global $_wp_admin_css_colors;
	   $_wp_admin_css_colors = 0;
	}

	add_action( 'admin_head', 'admin_color_scheme' );
	add_action( 'admin_bar_menu', 'modify_admin_bar', 8 );
	add_action( 'admin_enqueue_scripts', 'micropanel_style', 20 );
	add_action( 'customize_register', 'micropanel_style', 20 );

	add_action( 'login_enqueue_scripts', 'micropanel_style', 8 );	// LOGIN STYLE + JQUERY

	if(get_option('micropanel_adminbar_front') == 1){
		add_action( 'wp_head', 'fb_move_admin_bar', 119 );
	}
}
/*
** Add general settings
*/
add_action('admin_init', 'eg_settings_api_init');
?>