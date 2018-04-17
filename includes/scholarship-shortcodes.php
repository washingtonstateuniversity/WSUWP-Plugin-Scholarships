<?php

namespace WSU\Scholarships\Shortcodes;

add_shortcode( 'wsuwp_scholarships', 'WSU\Scholarships\Shortcodes\display_wsuwp_scholarships' );
/**
 * Displays a form for browsing scholarships.
 *
 * @since 0.0.1
 */
function display_wsuwp_scholarships() {
	wp_enqueue_style( 'wsuwp-scholarships', plugins_url( 'css/scholarships.css', dirname( __FILE__ ) ), array( 'spine-theme' ), \WSU\Scholarships\plugin_version() );
	wp_enqueue_script( 'wsuwp-scholarships', plugins_url( 'js/scholarships.min.js', dirname( __FILE__ ) ), array( 'jquery' ), \WSU\Scholarships\plugin_version(), true );
	wp_localize_script( 'wsuwp-scholarships', 'scholarships', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'wsuwp-scholarships' ),
	) );

	$grade = false;
	$gpa = false;
	$state = false;
	$citizenship = false;

	// @codingStandardsIgnoreStart
	if ( isset( $_GET['gpa'] ) ) {
		$gpa = sanitize_text_field( $_GET['gpa'] );
	}

	if ( isset( $_GET['state'] ) ) {
		$state = urldecode( $_GET['state'] );
	}

	if ( isset( $_GET['grade'] ) ) {
		$grade_terms = get_terms( array(
			'hide_empty' => false,
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
			'fields' => 'ids',
		) );

		if ( in_array( absint( $_GET['grade'] ), $grade_terms, true ) ) {
			$grade = absint( $_GET['grade'] );
		}
	}

	if ( isset( $_GET['citizenship'] ) ) {
		$citizenship_terms = get_terms( array(
			'hide_empty' => false,
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
			'fields' => 'ids',
		) );

		if ( in_array( absint( $_GET['citizenship'] ), $citizenship_terms, true ) ) {
			$citizenship = absint( $_GET['citizenship'] );
		}
	}
	// @codingStandardsIgnoreEnd

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
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
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
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
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
				<?php foreach ( \WSU\Scholarships\Post_Type\states() as $state_option ) { ?>
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
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_major(),
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
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_gender(),
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
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_ethnicity(),
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

	<svg id="scholarship-svg-symbols" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
		<defs>
			<symbol id="icon-wsu-scholarship" viewBox="0 0 44 44">
				<path fill="#a60f2d" d="M26.3 43.4s2.2-1 3.5-4.6a9 9 0 0 1 .7 4.5 28.6 28.6 0 0 1-4.2 0zm9.3-7.3C29 37 28 23.4 28 23.4s2.2 7 6.8 6.7c4.7-.2 3.4-7.6 3.4-7.6s4.6 12.6-2.7 13.6zM6 33a56 56 0 0 1-6.2 1.5S3.5 31.7 6.2 23L9 25.5 8.4 27a12.3 12.3 0 0 1 1.6 3 7.9 7.9 0 0 0-.2-7l-.4 1-1-.9-1.7-1.6a22.3 22.3 0 0 1 3.3-6.2l.2.2 2 2.4-.6 1a26 26 0 0 1 2.6 3.3A15 15 0 0 0 14 16l-1 .9-2.2-2.7a25 25 0 0 1 10-6.4 7.4 7.4 0 0 0-.6.9c-1.3 1.7-2.6 5-1.5 10.1l.7 3.2c.5 2.4 1.2 5 1.4 6.8.4 3.5 0 5.8-1.1 7a5 5 0 0 1-4 1.2v-.8a18.2 18.2 0 0 0-.5-4.5l-.5-1.7-.8 1.6c-1.2 2.5-5.3 8.8-10.7 10A18 18 0 0 0 6 33z"/>
				<path fill="#a60f2d" d="M36.9 9.7v-.3L43 8v-.6l-6.5.8v-.4l5.7-2.3L42 5l-6.1 1.9a2.9 2.9 0 0 0-1.2-1.2c-2.3.2-4.4.3-6.3.6L29.6.6H29l-1.9 5.9-1 .1L27.6.2h-.7L24.8 7h-.1a6.8 6.8 0 0 0-3.4 2.6C20.2 11 19 13.9 20 18.7l.7 3.1c.6 2.4 1.2 5.1 1.4 6.9.5 4 0 6.6-1.4 8.1-1.3 1.3-3.1 1.8-5.7 1.6h-.7v-.7a13.4 13.4 0 0 0 .1-1.4 17.8 17.8 0 0 0-.1-2.4 23.2 23.2 0 0 1-7.5 7.8A110.3 110.3 0 0 1 20.5 43l.7.1h.3a10.7 10.7 0 0 0 1.6 0h.3c3.2-.6 6-3 5.2-8.4-.9-6.5-2-10.6-2.3-13.7-.4-3.8 3-9.7 8.7-7a9.3 9.3 0 0 1 2 3.1 8.7 8.7 0 0 0-.3-4A6.7 6.7 0 0 0 37 11l6.4-.3V10zm-6.5 2.4a9.2 9.2 0 0 0-5.5 1.4 7.3 7.3 0 0 0-2 2.4 4 4 0 0 1 1.6-3.9 8.3 8.3 0 0 1 6.3-.3c.6.3.2.4-.4.4z"/>
			</symbol>
		</defs>
	</svg>
	<?php
	$html = ob_get_contents();

	ob_end_clean();

	return $html;
}

add_shortcode( 'wsuwp_search_scholarships', 'WSU\Scholarships\Shortcodes\display_wsuwp_search_scholarships' );
/**
 * Displays a form for searching scholarships.
 *
 * @since 0.0.2
 */
function display_wsuwp_search_scholarships() {
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
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
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
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
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
				<?php foreach ( \WSU\Scholarships\Post_Type\states() as $state_option ) { ?>
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

add_action( 'wp_ajax_nopriv_set_scholarships', 'WSU\Scholarships\Shortcodes\ajax_callback' );
add_action( 'wp_ajax_set_scholarships', 'WSU\Scholarships\Shortcodes\ajax_callback' );
/**
 * Handles the ajax callback for populating a list of scholarships.
 *
 * @since 0.0.1
 */
function ajax_callback() {
	check_ajax_referer( 'wsuwp-scholarships', 'nonce' );

	// Initial scholarships query arguments.
	$scholarships_query_args = array(
		'orderby' => 'title',
		'order' => 'ASC',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'post_type' => \WSU\Scholarships\Post_Type\post_type_slug(),
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
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
			'fields' => 'ids',
		) );

		if ( in_array( absint( $_POST['grade'] ), $grade, true ) ) {
			$scholarships_query_args['tax_query'][] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
					'field' => 'term_id',
					'terms' => $_POST['grade'],
				),
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_grade(),
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
	if ( $_POST['state'] && in_array( $_POST['state'], \WSU\Scholarships\Post_Type\states(), true ) ) {
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
			'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
			'fields' => 'ids',
		) );

		if ( in_array( absint( $_POST['citizenship'] ), $citizenship, true ) ) {
			$scholarships_query_args['tax_query'][] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
					'field' => 'term_id',
					'terms' => $_POST['citizenship'],
				),
				array(
					'taxonomy' => \WSU\Scholarships\Post_Type\taxonomy_slug_citizenship(),
					'field' => 'term_id',
					'terms' => array_diff( $citizenship, array( $_POST['citizenship'] ) ),
					'operator' => 'NOT IN',
				),
			);
		}
	}

	$scholarships = array();

	$scholarships_query = new \WP_Query( $scholarships_query_args );

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

			$classes = implode( get_post_class( $meta_classes ), ' ' );

			$post = '<article class="' . esc_attr( $classes ) . '" data-scholarship="' . esc_attr( $i ) . '" data-amount="' . esc_attr( $amount_data_value ) . '" data-deadline="' . esc_attr( $deadline_data_value ) . '">';
			$post .= '<header class="name">';
			if ( has_tag( 'wsu' ) ) {
				$post .= '<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23">';
				$post .= '<title>WSU Scholarship</title>';
				$post .= '<use xlink:href="#icon-wsu-scholarship" />';
				$post .= '</svg> ';
			}
			$post .= '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a></header>';
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
