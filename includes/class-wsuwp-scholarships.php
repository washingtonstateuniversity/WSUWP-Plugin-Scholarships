<?php

class WSUWP_Scholarships {
	/**
	 * @var WSUWP_Scholarships
	 */
	private static $instance;

	/**
	 * Tracks the version number of the plugin for script enqueues.
	 *
	 * @since 0.0.2
	 *
	 * @var string
	 */
	public static $version = '0.0.6';

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSUWP_Scholarships
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Scholarships();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.0.1
	 */
	public function setup_hooks() {
		require_once( dirname( __FILE__ ) . '/class-wsuwp-scholarship-post-type.php' );
		require_once( dirname( __FILE__ ) . '/class-wsuwp-scholarship-settings.php' );
		require_once( dirname( __FILE__ ) . '/class-wsuwp-scholarship-shortcodes.php' );

		add_action( 'init', 'WSUWP_Scholarship_Post_Type' );
		add_action( 'init', 'WSUWP_Scholarship_Settings' );
		add_action( 'init', 'WSUWP_Scholarship_Shortcodes' );
	}
}
