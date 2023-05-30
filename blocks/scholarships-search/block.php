<?php namespace WSUWP\Plugin\Scholarships\Blocks;

class Block_WSUWP_Scholarships_Search {

	protected static $block_name    = 'wsuwp/scholarships-search';
	protected static $default_attrs = array(
		'className' => '',
		'search_page_url' => '',
		'grade_levels' => array(),
		'citizenship' => array(),
		'states' => array(),
		'data_source' => 'local',
		'custom_data_source' => '',
	);


	public static function render( $attrs, $content = '' ) {

		$options = get_option( 'scholarships_settings' );

		if ( ! $options || ! isset( $options['search_page'] ) ) {
			return '';
		}

		// extend default data attributes and filter out non-data attributes
		$data = array_filter(
			array_merge( self::$default_attrs, $attrs ),
			function( $k ) {
				return array_key_exists( $k, self::$default_attrs );
			},
			ARRAY_FILTER_USE_KEY
		);

		// populate data for select fields
		$data = 'custom' === $attrs['data_source'] ? self::getDataFromCustomSource( $data, $attrs['custom_data_source'] ) : self::getDataFromLocal( $data );

		// render default template
		ob_start();

		include __DIR__ . '/templates/default.php';

		return ob_get_clean();

	}


	private static function getDataFromCustomSource( $data, $data_source ) {

		$response = wp_remote_get( $data_source . '/wp-json/wsu-scholarships/v1/get-filters?is-search-block=true' );

		if ( ! is_wp_error( $response ) ) {
			$response_data = json_decode( trim( $response['body'] ) );
			$data['search_page_url'] = $response_data->searchPage;
			$data['grade_levels'] = $response_data->gradeLevels;
			$data['citizenship'] = $response_data->citizenship;
			$data['states'] = $response_data->states;
		}

		return $data;

	}


	private static function getDataFromLocal( $data ) {

		$options = get_option( 'scholarships_settings' );
		$data['search_page_url'] = get_permalink( $options['search_page'] );
		$data['grade_levels'] = get_terms(
			array(
				'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
				'hide_empty' => 0,
				'orderby' => 'term_id',
			)
		);
		$data['citizenship'] = get_terms(
			array(
				'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
				'hide_empty' => 0,
			)
		);
		$data['states'] = \WSU\Scholarships\Post_Type\states();

		return $data;

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


	public static function init() {

		add_action( 'init', __CLASS__ . '::register_block' );

	}

}

Block_WSUWP_Scholarships_Search::init();
