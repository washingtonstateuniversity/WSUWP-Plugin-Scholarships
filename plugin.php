<?php
/*
Plugin Name: WSU Scholarships
Version: 0.2.2
Description: A WordPress plugin for managing a collection of scholarships.
Author: washingtonstateuniversity, philcable
Author URI: https://web.wsu.edu/
Plugin URI: https://github.com/washingtonstateuniversity/WSUWP-Plugin-Scholarships
Text Domain: wsu-scholarships
*/

namespace WSU\Scholarships;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin uses namespaces and requires PHP 5.3 or greater.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>' . esc_html__( 'WSU Scholarships requires PHP 5.3 to function properly. Please upgrade PHP or deactivate the plugin.', 'wsu-scholarships' ) . '</p></div>';
		}
	);
	return;
} else {
	add_action( 'plugins_loaded', 'WSU\Scholarships\bootstrap' );
	add_action( 'after_setup_theme', 'WSU\Scholarships\bootstrap_wds' );

	/**
	 * Provide the plugin version for enqueued scripts and styles.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function plugin_version() {
		return '0.2.1';
	}

	/**
	 * Starts things up.
	 *
	 * @since 0.1.0
	 */
	function bootstrap() {
		include_once __DIR__ . '/includes/scholarship-post-type.php';
		include_once __DIR__ . '/includes/scholarship-settings.php';
		include_once __DIR__ . '/includes/scholarship-shortcodes.php';
		include_once __DIR__ . '/includes/scholarship-contributor-role.php';
	}

	// load files associated with the Gutenberg WDS
	function bootstrap_wds() {
		if ( defined( 'ISWDS' ) ) {
			include_once __DIR__ . '/includes/settings.php';
			include_once __DIR__ . '/includes/scripts.php';
			include_once __DIR__ . '/blocks/scholarships-search/block.php';
			include_once __DIR__ . '/blocks/scholarships-list/block.php';

			if ( 1 === (int) get_option( 'wsu_scholarships_plugin_enable_post_type', 0 ) ) {
				include_once __DIR__ . '/includes/rest-api.php';
				include_once __DIR__ . '/includes/page-template.php';
			}
		}
	}
}
