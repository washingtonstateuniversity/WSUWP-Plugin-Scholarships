<?php
/*
Plugin Name: WSU Scholarships
Version: 0.0.6
Description: Provides a content type for publishing and managing a collection of scholarships.
Author: washingtonstateuniversity, philcable
Author URI: https://web.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/WSUWP-Plugin-Scholarships
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// The core plugin class.
require dirname( __FILE__ ) . '/includes/class-wsuwp-scholarships.php';

add_action( 'after_setup_theme', 'WSUWP_Scholarships' );
/**
 * Start things up.
 *
 * @return \WSUWP_Scholarships
 */
function WSUWP_Scholarships() {
	return WSUWP_Scholarships::get_instance();
}

/**
 * Retrieve the instance of the scholarship post type and meta data handler.
 *
 * @since 0.0.7
 *
 * @return WSUWP_Scholarship_Post_Type
 */
function WSUWP_Scholarship_Post_Type() {
	return WSUWP_Scholarship_Post_Type::get_instance();
}

/**
 * Retrieve the instance of the scholarship settings.
 *
 * @since 0.3.0
 *
 * @return WSUWP_Scholarship_Settings
 */
function WSUWP_Scholarship_Settings() {
	return WSUWP_Scholarship_Settings::get_instance();
}

/**
 * Retrieve the scholarship shortcodes.
 *
 * @since 0.3.0
 *
 * @return WSUWP_Scholarship_Shortcodes
 */
function WSUWP_Scholarship_Shortcodes() {
	return WSUWP_Scholarship_Shortcodes::get_instance();
}
