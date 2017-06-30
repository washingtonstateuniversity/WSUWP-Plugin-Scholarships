<?php

class WSUWP_Scholarship_Shortcodes {
	/**
	 * @var WSUWP_Scholarship_Shortcodes
	 *
	 * @since 0.0.7
	 */
	private static $instance;

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.7
	 *
	 * @return \WSUWP_Scholarship_Shortcodes
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Scholarship_Shortcodes();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.0.7
	 */
	public function setup_hooks() {
		add_shortcode( 'wsuwp_scholarships', array( $this, 'display_wsuwp_scholarships' ) );
		add_shortcode( 'wsuwp_search_scholarships', array( $this, 'display_wsuwp_search_scholarships' ) );
		add_action( 'wp_ajax_nopriv_set_scholarships', array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_set_scholarships', array( $this, 'ajax_callback' ) );
	}

	/**
	 * Display a form for browsing scholarships.
	 *
	 * @since 0.0.1
	 */
	public function display_wsuwp_scholarships() {
		wp_enqueue_style( 'wsuwp-scholarships', plugins_url( 'css/scholarships.css', dirname( __FILE__ ) ), array( 'spine-theme' ), WSUWP_Scholarships::$version );
		wp_enqueue_script( 'wsuwp-scholarships', plugins_url( 'js/scholarships.min.js', dirname( __FILE__ ) ), array( 'jquery' ), WSUWP_Scholarships::$version, true );
		wp_localize_script( 'wsuwp-scholarships', 'scholarships', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'wsuwp-scholarships' ),
		) );

		$grade = '';
		$gpa = ( isset( $_GET['gpa'] ) ) ? sanitize_text_field( $_GET['gpa'] ) : '';
		$state = ( isset( $_GET['state'] ) && in_array( urldecode( $_GET['state'] ), WSUWP_Scholarship_Post_Type::$states, true )  ) ? urldecode( $_GET['state'] ) : '';
		$citizenship = '';

		if ( isset( $_GET['grade'] ) ) {
			$grade_terms = get_terms( array(
				'hide_empty' => false,
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_GET['grade'] ), $grade_terms, true ) ) {
				$grade = absint( $_GET['grade'] );
			}
		}

		if ( isset( $_GET['citizenship'] ) ) {
			$citizenship_terms = get_terms( array(
				'hide_empty' => false,
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_GET['citizenship'] ), $citizenship_terms, true ) ) {
				$citizenship = absint( $_GET['citizenship'] );
			}
		}

		ob_start();
		?>
		<p>Tell us about yourself using the form below to help us find scholarships you might be eligible for, or <a class="wsuwp-scholarships-all" href="#">browse all scholarships &raquo;</a></p>

		<p>All fields are optional.</p>
		<form class="wsuwp-scholarships-form flex-form">

			<div class="select-wrap">
				<select id="wsuwp-scholarship-grade-level">
					<option value="">- Current grade level -</option>
					<?php
					$grade_level = get_terms( array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
						'hide_empty' => 0,
						'orderby' => 'term_id',
					) );

					if ( ! empty( $grade_level ) ) {
						foreach ( $grade_level as $grade_level_term ) {
							?>
							<option value="<?php echo esc_attr( $grade_level_term->term_id ); ?>"<?php selected( $grade, $grade_level_term->term_id ); ?>><?php echo esc_html( $grade_level_term->name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>

			<input type="text" id="wsuwp-scholarship-gpa" placeholder="G.P.A." value="<?php echo esc_attr( $gpa ); ?>" maxlength="4" />

			<div class="select-wrap">
				<select id="wsuwp-scholarship-citizenship">
					<option value="">- Citizenship -</option>
					<?php
					$citizenship_terms = get_terms( array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
						'hide_empty' => 0,
					) );

					if ( ! empty( $citizenship_terms ) ) {
						foreach ( $citizenship_terms as $citizenship_term ) {
							?>
							<option value="<?php echo esc_attr( $citizenship_term->term_id ); ?>"<?php selected( $citizenship, $citizenship_term->term_id ); ?>><?php echo esc_html( $citizenship_term->name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>

			<div class="select-wrap">
				<select id="wsuwp-scholarship-state">
					<option value="">- Residency -</option>
					<?php foreach ( WSUWP_Scholarship_Post_Type::$states as $state_option ) { ?>
						<option value="<?php echo esc_attr( $state_option ); ?>"<?php selected( $state, $state_option ); ?>><?php echo esc_html( $state_option ); ?></option>
					<?php } ?>
				</select>
			</div>

			<input type="submit" value="Go">

		</form>

		<p class="wsuwp-scholarships-toggle-filters display-after-submit">
			<a href="#">Filter results</a>
		</p>

		<div class="wsuwp-scholarships-filters">

			<p class="wsuwp-scholarships-filters-prefix">Only show scholarships:</p>

			<div class="wsuwp-scholarship-misc">
				<p>with</p>
				<ul>
					<li>
						<input type="checkbox" value=".meta-no-essay" id="no-essay" />
						<label for="no-essay">No essay requirement</label>
					</li>
					<li>
						<input type="checkbox" value=".meta-paper" id="paper" />
						<label for="paper">Paper application form</label>
					</li>
					<li>
						<input type="checkbox" value=".meta-online" id="online" />
						<label for="online">Online application form</label>
					</li>
				</ul>
			</div>

			<?php
			$major = get_terms( array(
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_major,
				'hide_empty' => 0,
			) );

			if ( ! empty( $major ) ) {
			?>
				<div class="wsuwp-scholarship-major">
					<p>for the following majors</p>
					<ul>
					<?php foreach ( $major as $major_option ) { ?>
						<li>
							<input type="checkbox" value=".major-<?php echo esc_attr( $major_option->slug ); ?>" id="<?php echo esc_attr( $major_option->slug ); ?>" />
							<label for="<?php echo esc_attr( $major_option->slug ); ?>"><?php echo esc_html( $major_option->name ); ?></label>
						</li>
					<?php } ?>
					</ul>
				</div>
			<?php } ?>

			<?php
			$gender = get_terms( array(
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_gender,
				'hide_empty' => 0,
			) );

			if ( ! empty( $gender ) ) {
			?>
				<div class="wsuwp-scholarship-gender">
					<p>for people who identify as</p>
					<ul>
					<?php foreach ( $gender as $gender_option ) { ?>
						<li>
							<input type="checkbox" value=".gender-identity-<?php echo esc_attr( $gender_option->slug ); ?>" id="<?php echo esc_attr( $gender_option->slug ); ?>" />
							<label for="<?php echo esc_attr( $gender_option->slug ); ?>"><?php echo esc_html( $gender_option->name ); ?></label>
						</li>
					<?php } ?>
					</ul>
				</div>
			<?php } ?>

			<?php
			$ethnicity = get_terms( array(
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_ethnicity,
				'hide_empty' => 0,
			) );

			if ( ! empty( $ethnicity ) ) {
			?>
				<div  class="wsuwp-scholarship-ethnicity">
					<p>for people who are</p>
					<ul>
					<?php foreach ( $ethnicity as $ethnicity_option ) { ?>
						<li>
							<input type="checkbox" value=".ethnicity-<?php echo esc_attr( $ethnicity_option->slug ); ?>" id="<?php echo esc_attr( $ethnicity_option->slug ); ?>" />
							<label for="<?php echo esc_attr( $ethnicity_option->slug ); ?>"><?php echo esc_html( $ethnicity_option->name ); ?></label>
						</li>
					<?php } ?>
					</ul>
				</div>
			<?php } ?>

		</div>

		<p class="wsuwp-scholarships-count display-after-submit"><span></span> scholarships found</p>

		<div class="wsuwp-scholarships-header display-after-submit">
			<div class="name">
				<a href="#" class="sorted">Scholarship</a>
			</div>
			<div class="amount">
				<a href="#">Amount</a>
			</div>
			<div class="deadline">
				<a href="#">Deadline</a>
			</div>
		</div>

		<div class="wsuwp-scholarships"></div>

		<div class="wsuwp-scholarships-tools">
			<a class="back-to-top" title="Back to top" href="#">Back to top</a>
		</div>
		<?php
		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}

	/**
	 * Display a form for searching scholarships.
	 *
	 * @since 0.0.2
	 */
	public function display_wsuwp_search_scholarships() {
		$options = get_option( 'scholarships_settings' );

		if ( ! $options || ! isset( $options['search_page'] ) ) {
			return '';
		}

		$search_page_url = get_permalink( $options['search_page'] );

		ob_start();
		?>
		<form class="wsuwp-scholarships-form flex-form" action="<?php echo esc_url( $search_page_url ); ?>">

			<div class="select-wrap">
				<select id="wsuwp-scholarship-grade-level" name="grade">
					<option value="">- Current grade level -</option>
					<?php
					$grade_level = get_terms( array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
						'hide_empty' => 0,
						'orderby' => 'term_id',
					) );

					if ( ! empty( $grade_level ) ) {
						foreach ( $grade_level as $grade_level_option ) {
							?>
							<option value="<?php echo esc_attr( $grade_level_option->term_id ); ?>"><?php echo esc_html( $grade_level_option->name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>

			<input type="text" id="wsuwp-scholarship-gpa" name="gpa" placeholder="G.P.A." value="" maxlength="4" />

			<div class="select-wrap">
				<select id="wsuwp-scholarship-citizenship" name="citizenship">
					<option value="">- Citizenship -</option>
					<?php
					$citizenship = get_terms( array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
						'hide_empty' => 0,
					) );

					if ( ! empty( $citizenship ) ) {
						foreach ( $citizenship as $citizenship_option ) {
							?>
							<option value="<?php echo esc_attr( $citizenship_option->term_id ); ?>"><?php echo esc_html( $citizenship_option->name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>

			<div class="select-wrap">
				<select id="wsuwp-scholarship-state" name="state">
					<option value="">- Residency -</option>
					<?php foreach ( WSUWP_Scholarship_Post_Type::$states as $state_option ) { ?>
						<option value="<?php echo esc_attr( $state_option ); ?>"><?php echo esc_html( $state_option ); ?></option>
					<?php } ?>
				</select>
			</div>

			<input type="submit" value="Go">

		</form>
		<?php
		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}

	/**
	 * Handle the ajax callback for populating a list of scholarships.
	 *
	 * @since 0.0.1
	 */
	public function ajax_callback() {
		check_ajax_referer( 'wsuwp-scholarships', 'nonce' );

		// Initial scholarships query arguments.
		$scholarships_query_args = array(
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'post_type' => WSUWP_Scholarship_Post_Type::$post_type_slug,
			'meta_query' => array(
				array(
					'relation' => 'OR',
					array(
						'key' => 'scholarship_deadline',
						'value' => date( 'Y-m-d' ),
						'type' => 'date',
						'compare' => '>=',
					),
					array(
						'key' => 'scholarship_deadline',
						'compare' => 'NOT EXISTS',
					),
				),
			),
		);

		// Grade Level meta parameters.
		if ( $_POST['grade'] ) {
			$grade = get_terms( array(
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_POST['grade'] ), $grade, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
						'field' => 'term_id',
						'terms' => $_POST['grade'],
					),
					array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_grade,
						'field' => 'term_id',
						'terms' => array_diff( $grade, array( $_POST['grade'] ) ),
						'operator' => 'NOT IN',
					),
				);
			}
		}

		// GPA meta parameters.
		if ( $_POST['gpa'] ) {
			$scholarships_query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => 'scholarship_gpa',
					'value' => sanitize_text_field( $_POST['gpa'] ),
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
		if ( $_POST['state'] && in_array( $_POST['state'], WSUWP_Scholarship_Post_Type::$states, true ) ) {
			$scholarships_query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => 'scholarship_state',
					'value' => $_POST['state'],
				),
				array(
					'key' => 'scholarship_state',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// Citizenship taxonomy parameters.
		if ( $_POST['citizenship'] ) {
			$citizenship = get_terms( array(
				'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_POST['citizenship'] ), $citizenship, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
						'field' => 'term_id',
						'terms' => $_POST['citizenship'],
					),
					array(
						'taxonomy' => WSUWP_Scholarship_Post_Type::$taxonomy_slug_citizenship,
						'field' => 'term_id',
						'terms' => array_diff( $citizenship, array( $_POST['citizenship'] ) ),
						'operator' => 'NOT IN',
					),
				);
			}
		}

		$scholarships = array();

		$scholarships_query = new WP_Query( $scholarships_query_args );

		if ( $scholarships_query->have_posts() ) {
			$i = 0;
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

				// Parse Deadline value for javascript sorting.
				$deadline_data_value = ( $deadline ) ? str_replace( '-', '', $deadline ) : 0;

				// Parse deadline for display.
				$date = DateTime::createFromFormat( 'Y-m-d', $deadline );
				$deadline_display = ( $date instanceof DateTime ) ? $date->format( 'm/d/Y' ) : $deadline;

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

				$classes = implode( get_post_class( $meta_classes ), ' ' );

				$post = '<article class="' . esc_attr( $classes ) . '" data-scholarship="' . esc_attr( $i ) . '" data-amount="' . esc_attr( $amount_data_value ) . '" data-deadline="' . esc_attr( $deadline_data_value ) . '">';
				$post .= '<header class="name"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></header>';
				$post .= '<div class="amount">';

				if ( $amount ) {
					$prepend = ( is_numeric( $numeric_amount ) ) ? '$' : '';
					$post .= esc_html( $prepend . $amount );
				}

				$post .= '</div>';
				$post .= '<div class="deadline">';

				if ( $deadline ) {
					$post .= esc_html( $deadline_display );
				} else {
					$post .= 'Varies';
				}

				$post .= '</div>';

				$post .= '<div class="apply">';

				if ( $site ) {
					$post .= '<a target="_blank" href="' . esc_url( $site ) . '">Apply</a>';
				}

				$post .= '</div>';

				$post .= '</article';

				$scholarships[] = $post;

				$i++;
			}

			wp_reset_postdata();
		} else {
			$scholarships = '<p>Sorry, no scholarships were found. Please try changing your search or <a class="wsuwp-scholarships-all" href="#">browsing all scholarships &raquo;</a></p>';
		}

		echo wp_json_encode( $scholarships );

		exit();
	}
}
