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
	public $version = '0.0.5';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the content type of a scholarship.
	 */
	public $content_type_slug = 'scholarship';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Major taxonomy.
	 */
	public $taxonomy_slug_major = 'major';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Citizenship taxonomy.
	 */
	public $taxonomy_slug_citizenship = 'citizenship';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public $taxonomy_slug_gender = 'gender-identity';

	/**
	 * @since 0.0.1
	 *
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public $taxonomy_slug_ethnicity = 'ethnicity';

	/**
	 * @since 0.0.3
	 *
	 * @var string Slug for tracking the Grade Level taxonomy.
	 */
	public $taxonomy_slug_grade = 'scholarship-grade';

	/**
	 * @since 0.0.1
	 *
	 * @var array A list of post meta keys associated with scholarships.
	 */
	var $post_meta_keys = array(
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
	var $states = array(
		'Washington',
		'Non-Washington',
	);

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
		add_action( 'init', array( $this, 'register_content_type' ), 12 );
		add_action( 'init', array( $this, 'register_taxonomies' ), 12 );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'add_meta_boxes_' . $this->content_type_slug, array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'wsuwp_scholarships', array( $this, 'display_wsuwp_scholarships' ) );
		add_shortcode( 'wsuwp_search_scholarships', array( $this, 'display_wsuwp_search_scholarships' ) );
		add_action( 'wp_ajax_nopriv_set_scholarships', array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_set_scholarships', array( $this, 'ajax_callback' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'sfs_theme_header_elements', array( $this, 'header_elements' ) );
		add_filter( 'nav_menu_css_class', array( $this, 'scholarship_menu_class' ), 10, 3 );
		add_filter( "manage_{$this->content_type_slug}_posts_columns", array( $this, 'scholarship_columns' ) );
		add_action( "manage_{$this->content_type_slug}_posts_custom_column", array( $this, 'manage_scholarship_columns' ), 10, 2 );
		add_filter( "manage_edit-{$this->content_type_slug}_sortable_columns", array( $this, 'manage_scholarship_sortable_columns' ) );
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

		register_post_type( $this->content_type_slug, $args );
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

		register_taxonomy( $this->taxonomy_slug_major, $this->content_type_slug, $args );

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

		register_taxonomy( $this->taxonomy_slug_citizenship, $this->content_type_slug, $args );

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

		register_taxonomy( $this->taxonomy_slug_gender, $this->content_type_slug, $args );

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

		register_taxonomy( $this->taxonomy_slug_ethnicity, $this->content_type_slug, $args );

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

		register_taxonomy( $this->taxonomy_slug_grade, $this->content_type_slug, $args );
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
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && get_current_screen()->id !== $this->content_type_slug ) {
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
			$this->content_type_slug,
			'normal',
			'high'
		);

		add_meta_box(
			'wsuwp-scholarship-granter-meta',
			'About the Granting Organization',
			array( $this, 'display_granter_meta_box' ),
			$this->content_type_slug,
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
					<?php foreach ( $this->states as $state_option ) { ?>
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
		if ( false === in_array( $state, WSUWP_Scholarships()->states, true ) ) {
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

		if ( $this->content_type_slug !== $post->post_type ) {
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
	 * Register settings for the Scholarship Settings admin page.
	 *
	 * @since 0.0.1
	 */
	public function register_settings() {
		register_setting(
			'settings',
			'scholarships_settings'
		);

		add_settings_section(
			'url',
			null,
			null,
			'settings'
		);

		add_settings_field(
			'search_page',
			'Search Page',
			array( $this, 'search_page_dropdown' ),
			'settings',
			'url',
			array(
				'label_for' => 'search_page',
			)
		);
	}

	/**
	 * Output for the Search Page URL field.
	 *
	 * @since 0.0.1
	 *
	 * @param array $args Extra arguments used when outputting the field.
	 */
	public function search_page_dropdown( $args ) {
		$options = get_option( 'scholarships_settings' );
		$search_page_id = ( $options && isset( $options[ $args['label_for'] ] ) ) ? $options[ $args['label_for'] ] : 0;
		?>
		<select name="scholarships_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
			<option value="">- Select -</option>
			<?php
			$pages = get_pages();
			foreach ( $pages as $page ) {
				// selected stuff
				?><option value="<?php echo esc_attr( $page->ID ); ?>"<?php selected( $search_page_id, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option><?php
			}
			?>
		</select>
		<p class="description">Select the page that is using the <code>[wsuwp_scholarships]</code> shortcode.</p>
		<?php
	}

	/**
	 * Create an admin page for Scholarship Settings.
	 *
	 * @since 0.0.1
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=' . $this->content_type_slug,
			'Scholarship Database Settings',
			'Settings',
			'manage_options',
			'settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Display the Scholarships Settings page.
	 *
	 * @since 0.0.1
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				'scholarships_settings_messages',
				'scholarships_settings_message',
				'Settings Saved',
				'updated'
			);
		}

		settings_errors( 'scholarships_settings_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'settings' );
					do_settings_sections( 'settings' );
					submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 *
	 * @since 0.0.1
	 */
	public function wp_enqueue_scripts() {
		$post = get_post();

		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wsuwp_scholarships' ) ) {
			wp_enqueue_style( 'wsuwp-scholarships', plugins_url( 'css/scholarships.css', dirname( __FILE__ ) ), array( 'spine-theme' ), $this->version );
			wp_enqueue_script( 'wsuwp-scholarships', plugins_url( 'js/scholarships.js', dirname( __FILE__ ) ), array( 'jquery' ), $this->version, true );
			wp_localize_script( 'wsuwp-scholarships', 'scholarships', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wsuwp-scholarships' ),
			) );
		}

		if ( is_singular( $this->content_type_slug ) ) {
			wp_enqueue_style( 'wsuwp-scholarship', plugins_url( 'css/scholarship.css', dirname( __FILE__ ) ), array( 'spine-theme' ), $this->version );
		}
	}

	/**
	 * Display a form for browsing scholarships.
	 *
	 * @since 0.0.1
	 */
	public function display_wsuwp_scholarships() {
		$grade = '';
		$gpa = ( isset( $_GET['gpa'] ) ) ? sanitize_text_field( $_GET['gpa'] ) : '';
		$state = ( isset( $_GET['state'] ) && in_array( urldecode( $_GET['state'] ), $this->states, true )  ) ? urldecode( $_GET['state'] ) : '';
		$citizenship = '';

		if ( isset( $_GET['grade'] ) ) {
			$grade_terms = get_terms( array(
				'taxonomy' => $this->taxonomy_slug_grade,
				'fields' => 'ids',
			) );

			if ( in_array( urldecode( $_GET['grade'] ), $grade_terms, true ) ) {
				$grade = urldecode( $_GET['grade'] );
			}
		}

		if ( isset( $_GET['citizenship'] ) ) {
			$citizenship_terms = get_terms( array(
				'taxonomy' => $this->taxonomy_slug_citizenship,
				'fields' => 'ids',
			) );

			if ( in_array( urldecode( $_GET['citizenship'] ), $citizenship_terms, true ) ) {
				$citizenship = urldecode( $_GET['citizenship'] );
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
						'taxonomy' => $this->taxonomy_slug_grade,
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
						'taxonomy' => $this->taxonomy_slug_citizenship,
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
					<?php foreach ( $this->states as $state_option ) { ?>
						<option value="<?php echo esc_attr( $state_option ); ?>"<?php selected( $state, $state_option ); ?>><?php echo esc_html( $state_option ); ?></option>
					<?php } ?>
				</select>
			</div>

			<input type="submit" value="Go">

		</form>

		<p class="wsuwp-scholarships-toggle-filters display-after-submit">
			<a href="#">Advanced filters</a>
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
				'taxonomy' => $this->taxonomy_slug_major,
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
				'taxonomy' => $this->taxonomy_slug_gender,
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
				'taxonomy' => $this->taxonomy_slug_ethnicity,
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
						'taxonomy' => $this->taxonomy_slug_grade,
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
						'taxonomy' => $this->taxonomy_slug_citizenship,
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
					<?php foreach ( $this->states as $state_option ) { ?>
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
			'post_type' => $this->content_type_slug,
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
				'taxonomy' => $this->taxonomy_slug_grade,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_POST['grade'] ), $grade, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => $this->taxonomy_slug_grade,
						'field' => 'term_id',
						'terms' => $_POST['grade'],
					),
					array(
						'taxonomy' => $this->taxonomy_slug_grade,
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
		if ( $_POST['state'] && in_array( $_POST['state'], $this->states, true ) ) {
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
				'taxonomy' => $this->taxonomy_slug_citizenship,
				'fields' => 'ids',
			) );

			if ( in_array( absint( $_POST['citizenship'] ), $citizenship, true ) ) {
				$scholarships_query_args['tax_query'][] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => $this->taxonomy_slug_citizenship,
						'field' => 'term_id',
						'terms' => $_POST['citizenship'],
					),
					array(
						'taxonomy' => $this->taxonomy_slug_citizenship,
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
		if ( is_singular( $this->content_type_slug ) ) {
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
		if ( is_singular( $this->content_type_slug ) ) {
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
			$scholarship = is_singular( $this->content_type_slug );
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
			echo esc_html( get_post_meta( $post_id, 'scholarship_deadline', true ) );
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
		if ( $this->content_type_slug === $post->post_type ) {
			$num = 1;
		}

		return $num;
	}
}
