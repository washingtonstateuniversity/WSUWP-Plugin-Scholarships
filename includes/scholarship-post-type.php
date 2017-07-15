<?php

namespace WSU\Scholarships\Post_Type;

/**
 * Provides the Scholarship post type slug.
 *
 * @since 0.0.1
 *
 * @return string
 */
function post_type_slug() {
	return 'scholarship';
}

/**
 * Provides the Major taxonomy slug.
 *
 * @since 0.0.1
 *
 * @return string
 */
function taxonomy_slug_major() {
	return 'major';
}

/**
 * Provides the Citizenship taxonomy slug.
 *
 * @since 0.0.1
 *
 * @return string
 */
function taxonomy_slug_citizenship() {
	return 'citizenship';
}

/**
 * Provides the Gender Identity taxonomy slug.
 *
 * @since 0.0.1
 *
 * @return string
 */
function taxonomy_slug_gender() {
	return 'gender-identity';
}

/**
 * Provides the Gender Identity taxonomy slug.
 *
 * @since 0.0.1
 *
 * @return string
 */
function taxonomy_slug_ethnicity() {
	return 'ethnicity';
}

/**
 * Provides the Grade Level taxonomy slug.
 *
 * @since 0.0.3
 *
 * @return string
 */
function taxonomy_slug_grade() {
	return 'scholarship-grade';
}

/**
 * Provides an array of post meta keys associated with scholarships.
 *
 * @since 0.0.1
 *
 * @return array
 */
function post_meta_keys() {
	return array(
		'scholarship_gpa' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_age_min' => array(
			'type' => 'int',
			'sanitize_callback' => 'absint',
		),
		'scholarship_age_max' => array(
			'type' => 'int',
			'sanitize_callback' => 'absint',
		),
		'scholarship_deadline' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_amount' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_essay' => array(
			'type' => 'boolean',
			'sanitize_callback' => 'WSU\Scholarships\Post_Type\sanitize_checkbox',
		),
		'scholarship_state' => array(
			'type' => 'string',
			'sanitize_callback' => 'WSU\Scholarships\Post_Type\sanitize_state',
		),
		'scholarship_app_paper' => array(
			'type' => 'boolean',
			'sanitize_callback' => 'WSU\Scholarships\Post_Type\sanitize_checkbox',
		),
		'scholarship_app_online' => array(
			'type' => 'boolean',
			'sanitize_callback' => 'WSU\Scholarships\Post_Type\sanitize_checkbox',
		),
		'scholarship_site' => array(
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
		),
		'scholarship_email' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_email',
		),
		'scholarship_phone' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_address' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_org_name' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'scholarship_org' => array(
			'type' => 'string',
			'sanitize_callback' => 'wp_kses_post',
		),
		'scholarship_org_site' => array(
			'type' => 'string',
			'sanitize_callback' => 'esc_url_raw',
		),
		'scholarship_org_email' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_email',
		),
		'scholarship_org_phone' => array(
			'type' => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		),
	);
}

/**
 * Provides an array of options for the Residence field.
 *
 * @since 0.0.1
 *
 * @return array
 */
function states() {
	return array(
		'Washington',
		'Non-Washington',
	);
}

/**
 * Sanitizes the value of checkbox meta fields.
 *
 * @param string $value The unsanitized value.
 *
 * @since 0.0.1
 *
 * @return string|boolean
*/
function sanitize_checkbox( $value ) {
	if ( '1' === $value ) {
		$value = '1';
	} else {
		$value = false;
	}

	return $value;
}

/**
 * Sanitizes the value of the State of Residence meta field.
 *
 * @param string $state The unsanitized value.
 *
 * @since 0.0.1
 *
 * @return string|boolean
*/
function sanitize_state( $state ) {
	if ( false === in_array( $state, states(), true ) ) {
		$state = false;
	}

	return $state;
}

add_action( 'init', 'WSU\Scholarships\Post_Type\register_post_type', 12 );
/**
 * Registers a post type for tracking information about scholarships.
 *
 * @since 0.0.1
 */
function register_post_type() {
	$labels = array(
		'name' => 'Scholarships',
		'singular_name' => 'Scholarship',
		'all_items' => 'All Scholarships',
		'view_item' => 'View Scholarship',
		'add_new_item' => 'Add New Scholarship',
		'edit_item' => 'Edit Scholarship',
		'update_item' => 'Update Scholarship',
		'search_items' => 'Search Scholarships',
		'not_found' => 'No Scholarships found',
		'not_found_in_trash' => 'No Scholarships found in Trash',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Aid granted to a student to support his or her education.',
		'public' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-awards',
		'supports' => array(
			'title',
			'editor',
			'revisions',
		),
		'taxonomies' => array(
			'post_tag',
		),
		'show_in_rest' => true,
	);

	\register_post_type( post_type_slug(), $args );
}

add_action( 'init', 'WSU\Scholarships\Post_Type\register_taxonomies', 12 );
/**
 * Registers taxonomies that will be attached to the scholarship post type.
 *
 * @since 0.0.1
 */
function register_taxonomies() {
	$labels = array(
		'name' => 'Major',
		'singular_name' => 'Major',
		'all_items' => 'All Majors',
		'edit_item' => 'Edit Major',
		'view_item' => 'View Major',
		'update_item' => 'Update Major',
		'add_new_item' => 'Add New Major',
		'new_item_name' => 'New Major Name',
		'search_items' => 'Search Majors',
		'popular_items' => 'Popular Majors',
		'separate_items_with_commas' => 'Separate majors with commas',
		'add_or_remove_items' => 'Add or remove majors',
		'choose_from_most_used' => 'Choose from the most used majors',
		'not_found' => 'No majors found',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Scholarship major criteria.',
		'public' => true,
		'hierarchical' => true,
		'show_admin_column' => true,
	);

	register_taxonomy( taxonomy_slug_major(), post_type_slug(), $args );

	$labels = array(
		'name' => 'Citizenship',
		'singular_name' => 'Citizenship',
		'all_items' => 'All Citizenship',
		'edit_item' => 'Edit Citizenship',
		'view_item' => 'View Citizenship',
		'update_item' => 'Update Citizenship',
		'add_new_item' => 'Add New Citizenship',
		'new_item_name' => 'New Citizenship Name',
		'search_items' => 'Search Citizenship',
		'popular_items' => 'Popular Citizenships',
		'separate_items_with_commas' => 'Separate citizenships with commas',
		'add_or_remove_items' => 'Add or remove citizenships',
		'choose_from_most_used' => 'Choose from the most used citizenships',
		'not_found' => 'No citizenship found',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Scholarship citizenship criteria.',
		'public' => true,
		'hierarchical' => false,
		'show_admin_column' => true,
	);

	register_taxonomy( taxonomy_slug_citizenship(), post_type_slug(), $args );

	$labels = array(
		'name' => 'Gender Identity',
		'singular_name' => 'Gender Identity',
		'all_items' => 'All Gender Identities',
		'edit_item' => 'Edit Gender Identity',
		'view_item' => 'View Gender Identity',
		'update_item' => 'Update Gender Identity',
		'add_new_item' => 'Add New Gender Identity',
		'new_item_name' => 'New Gender Identity Name',
		'search_items' => 'Search Gender Identities',
		'popular_items' => 'Popular Gender Identities',
		'separate_items_with_commas' => 'Separate gender identities with commas',
		'add_or_remove_items' => 'Add or remove gender identities',
		'choose_from_most_used' => 'Choose from the most used gender identities',
		'not_found' => 'No gender identities found',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Scholarship gender identity criteria.',
		'public' => true,
		'hierarchical' => false,
		'show_admin_column' => true,
	);

	register_taxonomy( taxonomy_slug_gender(), post_type_slug(), $args );

	$labels = array(
		'name' => 'Ethnicity',
		'singular_name' => 'Ethnicity',
		'all_items' => 'All Ethnicities',
		'edit_item' => 'Edit Ethnicity',
		'view_item' => 'View Ethnicity',
		'update_item' => 'Update Ethnicity',
		'add_new_item' => 'Add New Ethnicity',
		'new_item_name' => 'New Ethnicity Name',
		'search_items' => 'Search Ethnicities',
		'popular_items' => 'Popular Ethnicities',
		'separate_items_with_commas' => 'Separate ethnicities with commas',
		'add_or_remove_items' => 'Add or remove ethnicities',
		'choose_from_most_used' => 'Choose from the most used ethnicities',
		'not_found' => 'No ethnicities found',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Scholarship ethnicity criteria.',
		'public' => true,
		'hierarchical' => false,
		'show_admin_column' => true,
	);

	register_taxonomy( taxonomy_slug_ethnicity(), post_type_slug(), $args );

	/**
	 * @since 0.0.3
	 */
	$labels = array(
		'name' => 'Grade Level',
		'singular_name' => 'Grade Level',
		'all_items' => 'All Grade Levels',
		'edit_item' => 'Edit Grade Level',
		'view_item' => 'View Grade Level',
		'update_item' => 'Update Grade Level',
		'add_new_item' => 'Add New Grade Level',
		'new_item_name' => 'New Grade Level Name',
		'search_items' => 'Search Grade Levels',
		'popular_items' => 'Popular Grade Levels',
		'separate_items_with_commas' => 'Separate grade levels with commas',
		'add_or_remove_items' => 'Add or remove grade levels',
		'choose_from_most_used' => 'Choose from the most used grade levels',
		'not_found' => 'No grade levels found',
	);

	$args = array(
		'labels' => $labels,
		'description' => 'Scholarship grade level criteria.',
		'public' => true,
		'hierarchical' => false,
		'show_admin_column' => true,
	);

	register_taxonomy( taxonomy_slug_grade(), post_type_slug(), $args );

	register_taxonomy_for_object_type( 'wsuwp_university_location', post_type_slug() );
	register_taxonomy_for_object_type( 'wsuwp_university_org', post_type_slug() );
}

add_action( 'init', 'WSU\Scholarships\Post_Type\register_meta' );
/**
 * Registers the scholarship meta.
 *
 * @since 0.0.1
 */
function register_meta() {
	foreach ( post_meta_keys() as $key => $args ) {
		$args['single'] = true;
		\register_meta( 'post', $key, $args );
	}
}

add_action( 'admin_enqueue_scripts', 'WSU\Scholarships\Post_Type\admin_enqueue_scripts', 10 );
/**
 * Enqueues the styles for the scholarship information metabox.
 *
 * @since 0.0.1
 *
 * @param string $hook
 */
function admin_enqueue_scripts( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && get_current_screen()->id !== post_type_slug() ) {
		return;
	}

	wp_enqueue_style( 'wsuwp-scholarship-admin', plugins_url( 'css/scholarships-admin.css', dirname( __FILE__ ) ), array(), \WSU\Scholarships\plugin_version() );
}


add_action( 'add_meta_boxes_' . post_type_slug(), 'WSU\Scholarships\Post_Type\add_meta_boxes', 10 );
/**
 * Adds the metaboxes used to capture scholarship information.
 *
 * @since 0.0.1
 */
function add_meta_boxes() {
	add_meta_box(
		'wsuwp-scholarship-meta',
		'Scholarship Information',
		'WSU\Scholarships\Post_Type\display_scholarship_meta_box',
		post_type_slug(),
		'normal',
		'high'
	);

	add_meta_box(
		'wsuwp-scholarship-granter-meta',
		'About the Granting Organization',
		'WSU\Scholarships\Post_Type\display_granter_meta_box',
		post_type_slug(),
		'normal',
		'default'
	);
}

/**
 * Displays the metabox used to capture scholarship information.
 *
 * @since 0.0.1
 *
 * @param WP_Post $post Object for the post currently being edited.
 */
function display_scholarship_meta_box( $post ) {
	$gpa = get_post_meta( $post->ID, 'scholarship_gpa', true );
	$age_min = get_post_meta( $post->ID, 'scholarship_age_min', true );
	$age_max = get_post_meta( $post->ID, 'scholarship_age_max', true );
	$deadline = get_post_meta( $post->ID, 'scholarship_deadline', true );
	$amount = get_post_meta( $post->ID, 'scholarship_amount', true );
	$essay = get_post_meta( $post->ID, 'scholarship_essay', true );
	$state = get_post_meta( $post->ID, 'scholarship_state', true );
	$paper = get_post_meta( $post->ID, 'scholarship_app_paper', true );
	$online = get_post_meta( $post->ID, 'scholarship_app_online', true );
	$site = get_post_meta( $post->ID, 'scholarship_site', true );
	$email = get_post_meta( $post->ID, 'scholarship_email', true );
	$phone = get_post_meta( $post->ID, 'scholarship_phone', true );
	$address = get_post_meta( $post->ID, 'scholarship_address', true );

	wp_nonce_field( 'save-wsu-scholarship-meta', '_wsu_scholarship_meta_nonce' );
	?>
	<div class="wsuwp-scholarship-fieldset">
		<label>Minimum GPA<br />
			<input type="text" class="widefat" name="scholarship_gpa" value="<?php echo esc_attr( $gpa ); ?>" />
		</label>

		<label>Minimum Age<br />
			<input type="number" class="widefat" name="scholarship_age_min" value="<?php echo esc_attr( $age_min ); ?>" />
		</label>

		<label>Maximum Age<br />
			<input type="number" class="widefat" name="scholarship_age_max" value="<?php echo esc_attr( $age_max ); ?>" />
		</label>

	</div>

	<div class="wsuwp-scholarship-fieldset">

		<label>Deadline (yyyy-mm-dd)<br />
			<input type="text" class="widefat" name="scholarship_deadline" value="<?php echo esc_attr( $deadline ); ?>" pattern="\d{4}-\d{2}-\d{2}" />
		</label>

		<label>Amount<br />
			<input type="text" class="widefat" name="scholarship_amount" value="<?php echo esc_attr( $amount ); ?>" />
		</label>

	</div>

	<div class="wsuwp-scholarship-fieldset">

		<div>

			<p>Eligibility Requirements</p>

			<label><input value="1" type="checkbox" name="scholarship_essay"<?php checked( $essay, 1 ); ?> /> Essay</label><br />

			<select name="scholarship_state">
				<option value="">State of Residence</option>
				<?php foreach ( states() as $state_option ) { ?>
					<option value="<?php echo esc_attr( $state_option ); ?>"<?php selected( $state, $state_option ); ?>><?php echo esc_html( $state_option ); ?></option>
				<?php } ?>
			</select>

		</div>

		<div>

			<p>Application availability</p>

			<label><input value="1" type="checkbox" name="scholarship_app_paper"<?php checked( $paper, 1 ); ?> /> Paper</label><br />

			<label><input value="1" type="checkbox" name="scholarship_app_online"<?php checked( $online, 1 ); ?> /> Online</label>

		</div>

	</div>

	<p><strong>Contact</strong></p>

	<div class="wsuwp-scholarship-fieldset">

		<label>Website<br />
			<input type="url" class="widefat" name="scholarship_site" pattern="https?://.+" value="<?php echo esc_attr( $site ); ?>" />
		</label>

		<label>Email<br />
			<input type="email" class="widefat" name="scholarship_email" value="<?php echo esc_attr( $email ); ?>" />
		</label>

		<label>Phone (555-555-5555)<br />
			<input type="tel" class="widefat" name="scholarship_phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $phone ); ?>" />
		</label>

		<label>Address<br />
			<input type="text" class="widefat" name="scholarship_address" value="<?php echo esc_attr( $address ); ?>" />
		</label>

	</div>
	<?php
}

/**
 * Displays the metabox used to capture granting organization information.
 *
 * @since 0.0.1
 *
 * @param WP_Post $post Object for the post currently being edited.
 */
function display_granter_meta_box( $post ) {
	$org_name = get_post_meta( $post->ID, 'scholarship_org_name', true );
	$org = get_post_meta( $post->ID, 'scholarship_org', true );
	$org_site = get_post_meta( $post->ID, 'scholarship_org_site', true );
	$org_email = get_post_meta( $post->ID, 'scholarship_org_email', true );
	$org_phone = get_post_meta( $post->ID, 'scholarship_org_phone', true );
	?>

	<input type="text" class="widefat" name="scholarship_org_name" placeholder="Name" value="<?php echo esc_attr( $org_name ); ?>" />

	<?php
	wp_editor( $org, 'scholarship_org', array(
		'textarea_rows' => 7,
	) );
	?>

	<p><strong>Contact</strong></p>

	<div class="wsuwp-scholarship-fieldset">

		<label>Website<br />
			<input type="url" class="widefat" name="scholarship_org_site" value="<?php echo esc_attr( $org_site ); ?>" />
		</label>

		<label>Email<br />
			<input type="email" class="widefat" name="scholarship_org_email" value="<?php echo esc_attr( $org_email ); ?>" />
		</label>

		<label>Phone (555-555-5555)<br />
			<input type="tel" class="widefat" name="scholarship_org_phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $org_phone ); ?>" />
		</label>

	</div>
	<?php
}

add_action( 'save_post', 'WSU\Scholarships\Post_Type\save_post', 10, 2 );
/**
 * Saves the information assigned to the scholarship.
 *
 * @since 0.0.1
 *
 * @param int     $post_id ID of the post being saved.
 * @param WP_Post $post    Post object of the post being saved.
 */
function save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( post_type_slug() !== $post->post_type ) {
		return;
	}

	if ( 'auto-draft' === $post->post_status ) {
		return;
	}

	if ( ! isset( $_POST['_wsu_scholarship_meta_nonce'] ) || ! wp_verify_nonce( $_POST['_wsu_scholarship_meta_nonce'], 'save-wsu-scholarship-meta' ) ) {
		return;
	}

	$keys = get_registered_meta_keys( 'post' );

	foreach ( post_meta_keys() as $key => $args ) {
		if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] && isset( $args['sanitize_callback'] ) ) {
			update_post_meta( $post_id, $key, $_POST[ $key ] );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'WSU\Scholarships\Post_Type\wp_enqueue_scripts' );
/**
 * Enqueue the scripts and styles used on the front end.
 *
 * @since 0.0.1
 */
function wp_enqueue_scripts() {
	if ( is_singular( post_type_slug() ) ) {
		wp_enqueue_style( 'wsuwp-scholarship', plugins_url( 'css/scholarship.css', dirname( __FILE__ ) ), array( 'spine-theme' ), \WSU\Scholarships\plugin_version() );
	}
}

add_filter( 'body_class', 'WSU\Scholarships\Post_Type\body_class' );
/**
 * Add 'section-scholarships' as a body class when individual scholarships are being displayed.
 *
 * @since 0.0.1
 *
 * @param array $classes Current body classes.
 *
 * @return array Modified body classes.
 */
function body_class( $classes ) {
	if ( is_singular( post_type_slug() ) ) {
		$classes[] = 'section-scholarships';
	}

	return $classes;
}

add_filter( 'sfs_theme_header_elements', 'WSU\Scholarships\Post_Type\header_elements' );
/**
 * Output custom page headers when viewing an individual scholarship.
 *
 * @since 0.0.1
 *
 * @param array $headers Current header element values.
 *
 * @return array Modified header element values.
 */
function header_elements( $headers ) {
	if ( is_singular( post_type_slug() ) ) {
		$headers['page_sup'] = 'Scholarship';
		$headers['page_sub'] = 'Details';
	}

	return $headers;
}

add_filter( 'nav_menu_css_class', 'WSU\Scholarships\Post_Type\menu_class', 11, 3 );
/**
 * Add the 'active' class to a menu item when the search results page or an individual scholarship is viewed.
 *
 * @since 0.0.1
 *
 * @param array    $classes Current list of nav menu classes.
 * @param WP_Post  $item    Post object representing the menu item.
 * @param stdClass $args    Arguments used to create the menu.
 *
 * @return array Modified list of nav menu classes.
 */
function menu_class( $classes, $item, $args ) {
	$spine_menu = in_array( $args->menu, array( 'site', 'offsite' ), true );
	$options = get_option( 'scholarships_settings' );

	if ( $spine_menu && $options && isset( $options['active_menu_item'] ) ) {
		$scholarship = is_singular( post_type_slug() );
		$search_results = ( isset( $options['search_page'] ) && is_page( $options['search_page'] ) );
		$active_item = ( get_permalink( $options['active_menu_item'] ) === $item->url );

		if ( $active_item && ( $scholarship || $search_results ) ) {
			$classes[] = 'active';
		}
	}

	return $classes;
}

add_filter( 'manage_' . post_type_slug() . '_posts_columns', 'WSU\Scholarships\Post_Type\columns' );
/**
 * Unset the 'posts' column and add a 'deadline' column on the 'All Scholarships' page.
 *
 * @since 0.0.4
 *
 * @param array $columns Default columns shown in the manage terms table.
 *
 * @return array $columns Columns to be shown in the manage terms table.
 */
function columns( $columns ) {
	unset( $columns['date'] );

	$columns['deadline'] = 'Deadline';

	return $columns;
}

add_action( 'manage_' . post_type_slug() . '_posts_custom_column', 'WSU\Scholarships\Post_Type\manage_columns', 10, 2 );
/**
 * Displays content for custom columns on the 'All Scholarships' taxonomy page.
 *
 * @since 0.0.4
 *
 * @param string $column_name The name of the column.
 * @param int    $post_id     The ID of the current post.
 */
function manage_columns( $column_name, $post_id ) {
	if ( 'deadline' === $column_name ) {
		$deadline_value = get_post_meta( $post_id, 'scholarship_deadline', true );
		$deadline = new \DateTime( $deadline_value );
		$today = new \DateTime( 'now' );

		if ( $today > $deadline ) {
			echo '<span style="color:#c60c30;">' . esc_html( $deadline_value ) . '</span>';
		} else {
			echo esc_html( $deadline_value );
		}
	}
}

add_filter( 'manage_edit-' . post_type_slug() . '_sortable_columns', 'WSU\Scholarships\Post_Type\manage_sortable_columns' );
/**
 * Allow for sorting scholarships by the 'Deadline' column.
 *
 * @since 0.0.4
 *
 * @param array $sortable_columns The default array of sortable columns.
 *
 * @return array $sortable_columns Modified array of sortable columns.
 */
function manage_sortable_columns( $sortable_columns ) {
	$sortable_columns['deadline'] = 'deadline';

	return $sortable_columns;
}

add_action( 'pre_get_posts', 'WSU\Scholarships\Post_Type\deadline_orderby' );
/**
 * Modify the 'All Scholarships' listing when it is sorted by deadline.
 *
 * @since 0.0.4
 *
 * @param WP_Query $query Current query object to be modified.
 */
function deadline_orderby( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'deadline' === $orderby ) {
		$query->set( 'meta_key', 'scholarship_deadline' );
		$query->set( 'orderby', 'meta_value_num date' );
	}
}

add_filter( 'wp_revisions_to_keep', 'WSU\Scholarships\Post_Type\revisions_to_keep', 10, 2 );
/**
 * Limit scholarship revisions to 1.
 *
 * Revision support has been added to the 'scholarship' post type so that
 * 'Last Updated' data is provided, so only one revision needs to be kept.
 * The revisions link in the publish block is hidden via css.
 *
 * @since 0.0.4
 *
 * @param int     $num  Number of revisions to keep.
 * @param WP_Post $post Current post object.
 *
 * @return int $num Number of revisions to keep.
 */
function revisions_to_keep( $num, $post ) {
	if ( post_type_slug() === $post->post_type ) {
		$num = 1;
	}

	return $num;
}

add_action( 'rest_api_init', 'WSU\Scholarships\Post_Type\register_api_fields' );
/**
 * Register the custom meta fields attached to a REST API response containing scholarship data.
 *
 * @since 0.0.7
 */
function register_api_fields() {
	$args = array(
		'get_callback' => 'WSU\Scholarships\Post_Type\get_api_meta_data',
		'update_callback' => null,
		'schema' => null,
	);

	foreach ( post_meta_keys() as $key => $_args ) {
		register_rest_field( post_type_slug(), $key, $args );
	}
}

/**
 * Return the value of a post meta field sanitized against a whitelist with the provided method.
 *
 * @since 0.0.7
 *
 * @param array           $object  The current post being processed.
 * @param string          $key     Name of the field being retrieved.
 * @param WP_Rest_Request $request The full current REST request.
 *
 * @return mixed Meta data associated with the post and field name.
 */
function get_api_meta_data( $object, $key, $request ) {
	if ( ! array_key_exists( $key, post_meta_keys() ) ) {
		return '';
	}

	$sanitize_callback = post_meta_keys()[ $key ]['sanitize_callback'];
	$meta_value = get_post_meta( $object['id'], $key, true );

	if ( 'sanitize_text_field' === $sanitize_callback || 'WSU\Scholarships\Post_Type\sanitize_checkbox' === $sanitize_callback || 'WSU\Scholarships\Post_Type\sanitize_state' === $sanitize_callback ) {
		return esc_html( $meta_value );
	}

	if ( 'absint' === $sanitize_callback ) {
		return absint( $meta_value );
	}

	if ( 'esc_url_raw' === $sanitize_callback ) {
		return esc_url( $meta_value );
	}

	if ( 'sanitize_email' === $sanitize_callback ) {
		return sanitize_email( $meta_value );
	}

	if ( 'wp_kses_post' === $sanitize_callback ) {
		return wp_kses_post( apply_filters( 'the_content', $meta_value ) );
	}

	return '';
}
