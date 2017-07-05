<?php
/*
Plugin Name: WSU Scholarships
Version: 0.0.6
Description: A WordPress plugin for managing a collection of scholarships.
Author: washingtonstateuniversity, philcable
Author URI: https://web.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/WSUWP-Plugin-Scholarships
*/

namespace WSU\Scholarships;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin uses namespaces and requires PHP 5.3 or greater.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', create_function( '',
	"echo '<div class=\"error\"><p>" . __( 'WSUWP Plugin Skeleton requires PHP 5.3 to function properly. Please upgrade PHP or deactivate the plugin.', 'wsuwp-plugin-skeleton' ) . "</p></div>';" ) );
	return;
} else {
	add_action( 'plugins_loaded', 'WSU\Scholarships\bootstrap' );

	/**
	 * Provide the plugin version for enqueued scripts and styles.
	 *
	 * @since 0.0.7
	 *
	 * @return string
	 */
	function plugin_version() {
		return '0.0.7';
	}

	/**
	 * Starts things up.
	 *
	 * @since 0.0.7
	 */
	function bootstrap() {
		include_once dirname( __FILE__ ) . '/includes/scholarship-post-type.php';
		include_once dirname( __FILE__ ) . '/includes/scholarship-settings.php';
		include_once dirname( __FILE__ ) . '/includes/scholarship-shortcodes.php';
		include_once dirname( __FILE__ ) . '/includes/scholarship-contributor-role.php';
	}
}