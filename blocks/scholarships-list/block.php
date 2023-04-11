<?php namespace WSUWP\Plugin\Scholarships\Blocks;

class Block_WSUWP_Scholarships_List {

	protected static $block_name    = 'wsuwp/scholarships-list';
	protected static $default_attrs = array(
		'className' => '',
		'data_source' => 'local',
		'custom_data_source' => '',
	);


	public static function render( $attrs, $content = '' ) {

		ob_start();

		include __DIR__ . '/templates/default.php';

		return ob_get_clean();

	}


	public static function register_block() {

		register_block_type(
			self::$block_name,
			array(
				'render_callback' => array( __CLASS__, 'render' ),
				'api_version'     => 2,
				'editor_script'   => 'wsuwp-plugin-scholarships-scripts',
				'editor_style'    => 'wsuwp-plugin-scholarships-styles',
			)
		);

	}


	public static function enqueue_frontend_assets() {

		if ( is_singular() ) {
			$id = get_the_ID();

			if ( has_block( self::$block_name, $id ) ) {
				wp_enqueue_script( 'wsu_design_system_script_scholarships_list' );
				wp_enqueue_style( 'wsu_design_system_script_scholarships_list' );
			}
		}

	}


	public static function init() {

		add_action( 'init', __CLASS__ . '::register_block' );
		add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_frontend_assets' ) );

	}

}

Block_WSUWP_Scholarships_List::init();
