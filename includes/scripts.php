<?php namespace WSUWP\Plugin\Scholarships;

class Scripts {

	public static function register_block_editor_assets() {

		$wds_version = get_theme_mod( 'wsu_wds_version', '2.x' );
		$editor_asset = include plugin_dir_path( dirname( __FILE__ ) ) . 'assets/dist/index.asset.php';

		// register editor assets
		wp_register_script(
			'wsuwp-plugin-scholarships-scripts',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/dist/index.js',
			$editor_asset['dependencies'],
			$editor_asset['version'],
			true,
		);

		wp_register_style(
			'wsuwp-plugin-scholarships-styles',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/dist/index.css',
			array(),
			$editor_asset['version']
		);

		// register front-end assets
		wp_register_script(
			'wsu_design_system_script_scholarships_list',
			'https://cdn.web.wsu.edu/designsystem/' . $wds_version . '/dist/bundles/standalone/scholarship-list/scripts.js',
			array(),
			WSUWPPLUGINGUTENBERGVERSION,
			true
		);

		wp_register_style(
			'wsu_design_system_script_scholarships_list',
			'https://cdn.web.wsu.edu/designsystem/' . $wds_version . '/dist/bundles/standalone/scholarship-list/styles-wds.css',
			array(),
			WSUWPPLUGINGUTENBERGVERSION
		);

	}


	public static function admin_enqueue_scripts( $hook ) {

		if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
			$script  = 'const WSUWP_SCHOLARSHIPS_PLUGIN_DATA = {';
			$script .= 'siteUrl: "' . site_url() . '",';
			$script .= 'wpVersion: "' . get_bloginfo( 'version' ) . '",';
			$script .= 'postTypeEnabled: ' . (int) get_option( 'wsu_scholarships_plugin_enable_post_type', 0 ) . ',';
			$script .= '};';

			wp_add_inline_script( 'wsuwp-plugin-scholarships-scripts', $script, 'before' );
		}

	}


	public static function init() {

		add_action( 'init', __CLASS__ . '::register_block_editor_assets' );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

	}
}

Scripts::init();
