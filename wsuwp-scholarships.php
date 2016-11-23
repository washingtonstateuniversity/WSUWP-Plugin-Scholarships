<?php
/*
Plugin Name: WSU Scholarships
Version: 0.0.2
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
