<?php namespace WSUWP\Plugin\Scholarships;

class Rest_API {

	public static function get_filters( \WP_REST_Request $request ) {

		$params = $request->get_query_params();
		$is_search_block = $params['is-search-block'] ?? 'false';

		$data = array();

		$data['gradeLevels'] = get_terms(
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

		if ( 'false' === $is_search_block ) {
			$data['requirements'] = array(
				array(
					'key' => 'no-essay',
					'label' => 'No essay requirement',
				),
				array(
					'key' => 'paper',
					'label' => 'Paper application form',
				),
				array(
					'key' => 'online',
					'label' => 'Online application form',
				),
			);

			$data['majors'] = get_terms(
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_major(),
					'hide_empty' => 0,
				)
			);

			$data['identities'] = get_terms(
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_gender(),
					'hide_empty' => 0,
				)
			);

			$data['ethnicities'] = get_terms(
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_ethnicity(),
					'hide_empty' => 0,
				)
			);
		}

		return new \WP_REST_Response(
			$data,
			200
		);

	}


	public static function get_scholarships( \WP_REST_Request $request ) {

		$params = $request->get_query_params();
		$filter_grade = $params['grade'];
		$filter_gpa = $params['gpa'];
		$filter_citizenship = $params['citizenship'];
		$filter_state = $params['state'];
		$filter_requirements = $params['requirements'];
		$filter_majors = $params['majors'];
		$filter_identities = $params['identities'];
		$filter_ethnicities = $params['ethnicities'];
		$filter_from_wsu = $params['fromWSU'];

		$posts_per_page = $params['postsPerPage'] ?? 20;
		$orderby = $params['orderBy'] ?? 'title';
		$order = $params['order'] ?? 'ASC';
		$page = $params['page'] ?? 1;

		// $data = array( $params, $orderby, $order );
		$data = array();

		// Initial scholarships query arguments.
		$scholarships_query_args = array(
			'post_status' => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged' => intval( $page ),
			'post_type' => \WSU\Scholarships\Post_Type\post_type_slug(),
			'orderby' => array(
				'title' => $order,
			),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					'deadline_clause' => array(
						'key' => 'scholarship_deadline',
						'value' => gmdate( 'Y-m-d' ),
						'type' => 'date',
						'compare' => '>=',
					),
					array(
						'key' => 'scholarship_deadline',
						'compare' => 'NOT EXISTS',
					),
				),
				array(
					'relation' => 'OR',
					'amount_clause_not_exists' => array(
						'key' => 'scholarship_amount',
						'type' => 'numeric',
						'compare' => 'NOT EXISTS',
					),
					'amount_clause' => array(
						'key' => 'scholarship_amount',
						'compare' => 'EXISTS',
					),
				),
			),
		);

		// Determine sort order
		if ( 'scholarship_amount' === $orderby ) {
			$scholarships_query_args['orderby'] = array(
				'amount_clause_not_exists' => $order, // 😵 HACK: Forces the records without the meta value to be grouped together and the rest sorted numerically (since the type is numeric).
				'amount_clause' => 'desc' === $order ? 'ASC' : 'DESC', // More of the hack: sort the ones that do have the meta value as a string. Groups together ones that include actual string values.
				'title' => 'ASC',
			);
		} elseif ( 'scholarship_deadline' === $orderby ) {
			$scholarships_query_args['orderby'] = array(
				'deadline_clause' => $order,
				'title' => 'ASC',
			);
		}

		// Grade Level meta parameters.
		if ( $filter_grade ) {
			$grade = get_terms(
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
					'fields' => 'ids',
				)
			);

			if ( in_array( absint( $filter_grade ), $grade, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
						'field' => 'term_id',
						'terms' => $filter_grade,
					),
					array(
						'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
						'field' => 'term_id',
						'terms' => array_diff( $grade, array( $filter_grade ) ),
						'operator' => 'NOT IN',
					),
				);
			}
		}

		// GPA meta parameters.
		if ( $filter_gpa ) {
			$scholarships_query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => 'scholarship_gpa',
					'value' => sanitize_text_field( $filter_gpa ),
					'type' => 'DECIMAL(10,2)',
					'compare' => '<=',
				),
				array(
					'key' => 'scholarship_gpa',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// State of Residence meta parameters.
		if ( $filter_state && in_array( $filter_state, \WSU\Scholarships\Post_Type\states(), true ) ) {
			$scholarships_query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => 'scholarship_state',
					'value' => $filter_state,
				),
				array(
					'key' => 'scholarship_state',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// Citizenship taxonomy parameters.
		if ( $filter_citizenship ) {
			$citizenship = get_terms(
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
					'fields' => 'ids',
				)
			);

			if ( in_array( absint( $filter_citizenship ), $citizenship, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
						'field' => 'term_id',
						'terms' => $filter_citizenship,
					),
					array(
						'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
						'field' => 'term_id',
						'terms' => array_diff( $citizenship, array( $filter_citizenship ) ),
						'operator' => 'NOT IN',
					),
				);
			}
		}

		// Major taxonomy parameters.
		if ( $filter_majors ) {
			$scholarships_query_args['tax_query'][] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_major(),
					'field' => 'term_id',
					'terms' => explode( ',', $filter_majors ),
					'operator' => 'IN',
				),
			);
		}

		// Identities taxonomy parameters.
		if ( $filter_identities ) {
			$scholarships_query_args['tax_query'][] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_gender(),
					'field' => 'term_id',
					'terms' => explode( ',', $filter_identities ),
					'operator' => 'IN',
				),
			);
		}

		// Ethnicities taxonomy parameters.
		if ( $filter_ethnicities ) {
			$scholarships_query_args['tax_query'][] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_ethnicity(),
					'field' => 'term_id',
					'terms' => explode( ',', $filter_ethnicities ),
					'operator' => 'IN',
				),
			);
		}

		// Requirements meta parameters.
		if ( $filter_requirements ) {
			$requirments = explode( ',', $filter_requirements );
			$filter_array = array(
				'relation' => 'OR',
			);

			if ( in_array( 'no-essay', $requirments, true ) ) {
				$filter_array[] = array(
					'relation' => 'OR',
					array(
						'key' => 'scholarship_essay',
						'value' => 0,
						'compare' => '=',
					),
					array(
						'key'     => 'scholarship_essay',
						'compare' => 'NOT EXISTS',
					),
				);
			}

			if ( in_array( 'paper', $requirments, true ) ) {
				$filter_array[] = array(
					'relation' => 'OR',
					array(
						'key' => 'scholarship_app_paper',
						'value' => 1,
						'compare' => '=',
					),
					array(
						'key'     => 'scholarship_app_paper',
						'compare' => 'EXISTS',
					),
				);
			}

			if ( in_array( 'online', $requirments, true ) ) {
				$filter_array[] = array(
					'relation' => 'OR',
					array(
						'key' => 'scholarship_app_online',
						'value' => 1,
						'compare' => '=',
					),
					array(
						'key'     => 'scholarship_app_online',
						'compare' => 'EXISTS',
					),
				);
			}

			$scholarships_query_args['meta_query'][] = $filter_array;
		}

		if ( $filter_from_wsu ) {
			$values = explode( ',', $filter_from_wsu );
			$filter_array = array(
				'relation' => 'OR',
			);

			if ( in_array( 'from-wsu', $values, true ) ) {
				$filter_array[] = array(
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => 'wsu',
					'operator' => 'IN',
				);
			}

			if ( in_array( 'outside-wsu', $values, true ) ) {
				$filter_array[] = array(
					'taxonomy' => 'post_tag',
					'field' => 'slug',
					'terms' => 'wsu',
					'operator' => 'NOT IN',
				);
			}

			$scholarships_query_args['tax_query'][] = $filter_array;
		}

		$scholarships = array();

		$scholarships_query = new \WP_Query( $scholarships_query_args );

		// $data['query'] = $scholarships_query->request; // for debugging
		$data['showingCount'] = $scholarships_query->post_count;
		$data['totalCount'] = $scholarships_query->found_posts;
		$data['numberOfPages'] = $scholarships_query->max_num_pages;

		try {
			while ( $scholarships_query->have_posts() ) {
				$scholarships_query->the_post();
				$deadline = get_post_meta( get_the_ID(), 'scholarship_deadline', true );
				$amount = get_post_meta( get_the_ID(), 'scholarship_amount', true );
				$essay = get_post_meta( get_the_ID(), 'scholarship_essay', true );
				$paper = get_post_meta( get_the_ID(), 'scholarship_app_paper', true );
				$online = get_post_meta( get_the_ID(), 'scholarship_app_online', true );
				$state = get_post_meta( get_the_ID(), 'scholarship_state', true );
				$site = get_post_meta( get_the_ID(), 'scholarship_site', true );

				// Parse Amount value for javascript sorting.
				$amount_pieces = explode( '-', $amount );
				$numeric_amount = str_replace( ',', '', $amount_pieces[0] );
				$amount_data_value = ( $amount && is_numeric( $numeric_amount ) ) ? $numeric_amount : 0;
				$display_amount = '';
				if ( $amount ) {
					$prepend = ( is_numeric( $numeric_amount ) ) ? '$' : '';
					$display_amount = esc_html( $prepend . $amount );
				}

				// Parse Deadline value for javascript sorting.
				$deadline_data_value = ( $deadline ) ? str_replace( '-', '', $deadline ) : 0;

				// Parse deadline for display.
				$date = \DateTime::createFromFormat( 'Y-m-d', $deadline );
				$deadline_display = ( $date instanceof \DateTime ) ? $date->format( 'm/d/Y' ) : $deadline;

				// Additional classes for meta data.
				$meta_classes = array();

				if ( ! $essay ) {
					$meta_classes[] = 'meta-no-essay';
				}

				if ( $paper ) {
					$meta_classes[] = 'meta-paper';
				}

				if ( $online ) {
					$meta_classes[] = 'meta-online';
				}

				if ( $state ) {
					$meta_classes[] = 'meta-' . esc_attr( $state );
				}

				$classes = get_post_class( $meta_classes );

				$scholarship = array(
					'data' => $classes,
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'permalink' => get_the_permalink(),
					// 'amount' => esc_attr( $amount_data_value ),
					'displayAmount' => $display_amount,
					// 'deadline' => esc_attr( $deadline_data_value ),
					'displayDeadline' => $deadline ? esc_html( $deadline_display ) : 'Varies',
					'applyLink' => esc_url( $site ),
				);

				$scholarships[] = $scholarship;
			}

			wp_reset_postdata();
		} catch ( Exception $e ) {
			return new \WP_Error( 'error', 'Sorry, something went wrong.', array( 'status' => 500 ) );
		}

		$data['scholarships'] = $scholarships;

		return new \WP_REST_Response(
			$data,
			200
		);

	}


	public static function register_endpoints() {

		register_rest_route(
			'wsu-scholarships/v1',
			'get-scholarships',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_scholarships' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wsu-scholarships/v1',
			'get-filters',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_filters' ),
				'permission_callback' => '__return_true',
			)
		);

	}


	public static function init() {

		add_action( 'rest_api_init', __CLASS__ . '::register_endpoints' );

	}
}

Rest_API::init();
