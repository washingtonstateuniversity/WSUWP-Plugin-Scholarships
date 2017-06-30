<?php

class WSUWP_Scholarship_Post_Type {
	/**
	 * @var WSUWP_Scholarship_Post_Type
	 */
	private static $instance;

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the content type of a scholarship.
	 */
	public static $post_type_slug = 'scholarship';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Major taxonomy.
	 */
	public static $taxonomy_slug_major = 'major';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Citizenship taxonomy.
	 */
	public static $taxonomy_slug_citizenship = 'citizenship';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public static $taxonomy_slug_gender = 'gender-identity';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public static $taxonomy_slug_ethnicity = 'ethnicity';

	/**
	 * @since 0.0.3
	 *
	 * @var string Slug for tracking the Grade Level taxonomy.
	 */
	public static $taxonomy_slug_grade = 'scholarship-grade';

	/**
	 * @since 0.0.1
	 *
	 * @var array A list of post meta keys associated with scholarships.
	 */
	public $post_meta_keys = array(
		'scholarship_gpa',
		'scholarship_age_min',
		'scholarship_age_max',
		'scholarship_deadline',
		'scholarship_amount',
		'scholarship_essay',
		'scholarship_state',
		'scholarship_app_paper',
		'scholarship_app_online',
		'scholarship_site',
		'scholarship_email',
		'scholarship_phone',
		'scholarship_address',
		'scholarship_org_name',
		'scholarship_org',
		'scholarship_org_site',
		'scholarship_org_email',
		'scholarship_org_phone',
	);

	/**
	 * @since 0.0.1
	 *
	 * @var array A list of states for the State of Residence field.
	 */
	public static $states = array(
		'Washington',
		'Non-Washington',
	);

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSUWP_Scholarship_Post_Type
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Scholarship_Post_Type();
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
		add_action( 'init', array( $this, 'register_content_type' ), 12 );
		add_action( 'init', array( $this, 'register_taxonomies' ), 12 );
		add_action( 'init', array( $this, 'register_taxonomies_for_scholarships' ), 12 );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'add_meta_boxes_' . self::$post_type_slug, array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'sfs_theme_header_elements', array( $this, 'header_elements' ) );
		add_filter( 'nav_menu_css_class', array( $this, 'scholarship_menu_class' ), 10, 3 );
		add_filter( 'manage_' . self::$post_type_slug . '_posts_columns', array( $this, 'scholarship_columns' ) );
		add_action( 'manage_' . self::$post_type_slug . '_posts_custom_column', array( $this, 'manage_scholarship_columns' ), 10, 2 );
		add_filter( 'manage_edit-' . self::$post_type_slug . '_sortable_columns', array( $this, 'manage_scholarship_sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'deadline_orderby' ) );
		add_filter( 'wp_revisions_to_keep', array( $this, 'scholarship_revisions_to_keep' ), 10, 2 );
	}

	/**
	 * Register a content type to track information about scholarships.
	 *
	 * @since 0.0.1
	 */
	public function register_content_type() {
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
			'has_archive' => true,
		);

		register_post_type( self::$post_type_slug, $args );
	}

	/**
	 * Register taxonomies that will be attached to the scholarship content type.
	 *
	 * @since 0.0.1
	 */
	public function register_taxonomies() {
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

		register_taxonomy( self::$taxonomy_slug_major, self::$post_type_slug, $args );

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

		register_taxonomy( self::$taxonomy_slug_citizenship, self::$post_type_slug, $args );

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

		register_taxonomy( self::$taxonomy_slug_gender, self::$post_type_slug, $args );

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

		register_taxonomy( self::$taxonomy_slug_ethnicity, self::$post_type_slug, $args );

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

		register_taxonomy( self::$taxonomy_slug_grade, self::$post_type_slug, $args );
	}

	/**
	 * Add support for WSU University Taxonomies to the scholarship content type.
	 *
	 * @since 0.0.7
	 */
	public function register_taxonomies_for_scholarships() {
		register_taxonomy_for_object_type( 'wsuwp_university_location', self::$post_type_slug );
		register_taxonomy_for_object_type( 'wsuwp_university_org', self::$post_type_slug );
	}

	/**
	 * Register the scholarship post type.
	 *
	 * @since 0.0.1
	 */
	public function register_meta() {
		$args = array(
			'show_in_rest' => true,
			'single' => true,
		);

		$args['description'] = 'Minimum GPA';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_gpa', $args );

		$args['description'] = 'Minimum age';
		$args['type'] = 'int';
		$args['sanitize_callback'] = 'absint';
		register_meta( 'post', 'scholarship_age_min', $args );

		$args['description'] = 'Maximum age';
		$args['type'] = 'int';
		$args['sanitize_callback'] = 'absint';
		register_meta( 'post', 'scholarship_age_max', $args );

		$args['description'] = 'Scholarship application deadline';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_deadline', $args );

		$args['description'] = 'Scholarship amount';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_amount', $args );

		$args['description'] = 'Essay requirement';
		$args['type'] = '';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_checkbox';
		register_meta( 'post', 'scholarship_essay', $args );

		$args['description'] = "Applicant's state of residence";
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_state';
		register_meta( 'post', 'scholarship_state', $args );

		$args['description'] = 'Paper application availability';
		$args['type'] = '';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_checkbox';
		register_meta( 'post', 'scholarship_app_paper', $args );

		$args['description'] = 'Online application availability';
		$args['type'] = '';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_checkbox';
		register_meta( 'post', 'scholarship_app_online', $args );

		$args['description'] = 'Scholarship website';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'esc_url_raw';
		register_meta( 'post', 'scholarship_site', $args );

		$args['description'] = 'Scholarship email address';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_email';
		register_meta( 'post', 'scholarship_email', $args );

		$args['description'] = 'Scholarship phone number';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_phone', $args );

		$args['description'] = 'Scholarship mailing address';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_address', $args );

		$args['description'] = 'Granting organization name';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_org_name', $args );

		$args['description'] = 'About the granting organization';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'wp_kses_post';
		register_meta( 'post', 'scholarship_org', $args );

		$args['description'] = 'Granting organization website';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'esc_url_raw';
		register_meta( 'post', 'scholarship_org_site', $args );

		$args['description'] = 'Granting organization email address';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_email';
		register_meta( 'post', 'scholarship_org_email', $args );

		$args['description'] = 'Granting organization phone number';
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'sanitize_text_field';
		register_meta( 'post', 'scholarship_org_phone', $args );
	}

	/**
	 * Enqueue the styles for the scholarship information metabox.
	 *
	 * @since 0.0.1
	 *
	 * @param string $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && get_current_screen()->id !== self::$post_type_slug ) {
			return;
		}

		wp_enqueue_style( 'wsuwp-scholarship-admin', plugins_url( 'css/scholarships-admin.css', dirname( __FILE__ ) ) );
	}

	/**
	 * Add the metabox used to capture scholarship information.
	 *
	 * @since 0.0.1
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'wsuwp-scholarship-meta',
			'Scholarship Information',
			array( $this, 'display_scholarship_meta_box' ),
			self::$post_type_slug,
			'normal',
			'high'
		);

		add_meta_box(
			'wsuwp-scholarship-granter-meta',
			'About the Granting Organization',
			array( $this, 'display_granter_meta_box' ),
			self::$post_type_slug,
			'normal',
			'default'
		);
	}

	/**
	 * Display the metabox used to capture scholarship information.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Post $post Object for the post currently being edited.
	 */
	public function display_scholarship_meta_box( $post ) {
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
					<?php foreach ( self::$states as $state_option ) { ?>
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
	 * Display the metabox used to capture granting organization information.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Post $post Object for the post currently being edited.
	 */
	public function display_granter_meta_box( $post ) {
		$org_name = get_post_meta( $post->ID, 'scholarship_org_name', true );
		$org = get_post_meta( $post->ID, 'scholarship_org', true );
		$org_site = get_post_meta( $post->ID, 'scholarship_org_site', true );
		$org_email = get_post_meta( $post->ID, 'scholarship_org_email', true );
		$org_phone = get_post_meta( $post->ID, 'scholarship_org_phone', true );
		?>

		<input type="text" class="widefat" name="scholarship_org_name" placeholder="Name" value="<?php echo esc_attr( $org_name ); ?>" />

		<?php wp_editor( $org, 'scholarship_org', array( 'textarea_rows' => 7 ) ); ?>

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

	/**
	 * @param string $value The unsanitized checkbox value.
	 *
	 * @since 0.0.1
	 *
	 * @return string 1 or false.
	*/
	public static function sanitize_checkbox( $value ) {
		if ( '1' === $value ) {
			$value = '1';
		} else {
			$value = false;
		}

		return $value;
	}

	/**
	 * @param string $state The unsanitized State value.
	 *
	 * @since 0.0.1
	 *
	 * @return string the sanitized State value.
	*/
	public static function sanitize_state( $state ) {
		if ( false === in_array( $state, WSUWP_Scholarship_Post_Type()->states, true ) ) {
			$state = false;
		}

		return $state;
	}

	/**
	 * Save the information assigned to the scholarship.
	 *
	 * @since 0.0.1
	 *
	 * @param int     $post_id ID of the post being saved.
	 * @param WP_Post $post    Post object of the post being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( self::$post_type_slug !== $post->post_type ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( ! isset( $_POST['_wsu_scholarship_meta_nonce'] ) || ! wp_verify_nonce( $_POST['_wsu_scholarship_meta_nonce'], 'save-wsu-scholarship-meta' ) ) {
			return;
		}

		$keys = get_registered_meta_keys( 'post' );

		foreach ( $this->post_meta_keys as $key ) {
			if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] && isset( $keys[ $key ] ) && isset( $keys[ $key ]['sanitize_callback'] ) ) {
				update_post_meta( $post_id, $key, $_POST[ $key ] );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 *
	 * @since 0.0.1
	 */
	public function wp_enqueue_scripts() {
		if ( is_singular( self::$post_type_slug ) ) {
			wp_enqueue_style( 'wsuwp-scholarship', plugins_url( 'css/scholarship.css', dirname( __FILE__ ) ), array( 'spine-theme' ), WSUWP_Scholarships::$version );
		}
	}

	/**
	 * Add 'section-scholarships' as a body class when individual scholarships are being displayed.
	 *
	 * @since 0.0.1
	 *
	 * @param array $classes Current body classes.
	 *
	 * @return array Modified body classes.
	 */
	public function body_class( $classes ) {
		if ( is_singular( self::$post_type_slug ) ) {
			$classes[] = 'section-scholarships';
		}

		return $classes;
	}

	/**
	 * Output custom page headers when viewing an individual scholarship.
	 *
	 * @since 0.0.1
	 *
	 * @param array $headers Current header element values.
	 *
	 * @return array Modified header element values.
	 */
	public function header_elements( $headers ) {
		if ( is_singular( self::$post_type_slug ) ) {
			$headers['page_sup'] = 'Scholarship';
			$headers['page_sub'] = 'Details';
		}

		return $headers;
	}

	/**
	 * Add the 'active' class to the scholarship search menu item when viewing an individual scholarship.
	 *
	 * @since 0.0.1
	 *
	 * @param array    $classes Current list of nav menu classes.
	 * @param WP_Post  $item    Post object representing the menu item.
	 * @param stdClass $args    Arguments used to create the menu.
	 *
	 * @return array Modified list of nav menu classes.
	 */
	public function scholarship_menu_class( $classes, $item, $args ) {
		$spine_menu = in_array( $args->menu, array( 'site', 'offsite' ), true );
		$options = get_option( 'scholarships_settings' );

		if ( $spine_menu && $options && isset( $options['search_page'] ) ) {
			$scholarship = is_singular( self::$post_type_slug );
			$scholarship_search_page = ( get_permalink( $options['search_page'] ) === $item->url );

			if ( $scholarship && $scholarship_search_page ) {
				$classes[] = 'active';
			}
		}

		return $classes;
	}

	/**
	 * Unset the 'posts' column and add a 'deadline' column on the 'All Scholarships' page.
	 *
	 * @since 0.0.4
	 *
	 * @param array $columns Default columns shown in the manage terms table.
	 *
	 * @return array $columns Columns to be shown in the manage terms table.
	 */
	public function scholarship_columns( $columns ) {
		unset( $columns['date'] );

		$columns['deadline'] = 'Deadline';

		return $columns;
	}

	/**
	 * Displays content for custom columns on the 'All Scholarships' taxonomy page.
	 *
	 * @since 0.0.4
	 *
	 * @param string $column_name The name of the column.
	 * @param int    $post_id     The ID of the current post.
	 */
	function manage_scholarship_columns( $column_name, $post_id ) {
		if ( 'deadline' === $column_name ) {
			$deadline_value = get_post_meta( $post_id, 'scholarship_deadline', true );
			$deadline = new DateTime( $deadline_value );
			$today = new DateTime( 'now' );

			if ( $today > $deadline ) {
				echo '<span style="color:#c60c30;">' . esc_html( $deadline_value ) . '</span>';
			} else {
				echo esc_html( $deadline_value );
			}
		}
	}

	/**
	 * Allow for sorting scholarships by the 'Deadline' column.
	 *
	 * @since 0.0.4
	 *
	 * @param array $sortable_columns The default array of sortable columns.
	 *
	 * @return array $sortable_columns Modified array of sortable columns.
	 */
	public function manage_scholarship_sortable_columns( $sortable_columns ) {
		$sortable_columns['deadline'] = 'deadline';

		return $sortable_columns;
	}

	/**
	 * Modify the 'All Scholarships' listing when it is sorted by deadline.
	 *
	 * @since 0.0.4
	 *
	 * @param WP_Query $query Current query object to be modified.
	 */
	public function deadline_orderby( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'deadline' === $orderby ) {
			$query->set( 'meta_key', 'scholarship_deadline' );
			$query->set( 'orderby', 'meta_value_num date' );
		}
	}

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
	public function scholarship_revisions_to_keep( $num, $post ) {
		if ( self::$post_type_slug === $post->post_type ) {
			$num = 1;
		}

		return $num;
	}
}