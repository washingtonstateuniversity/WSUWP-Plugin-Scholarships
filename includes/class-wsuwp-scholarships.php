<?php

class WSUWP_Scholarships {
	/**
	 * @var WSUWP_Scholarships
	 */
	private static $instance;

	/**
	 * @var string Slug for tracking the content type of a scholarship.
	 */
	public $content_type_slug = 'scholarship';

	/**
	 * @var string Slug for tracking the scholarship eligibility taxonomy.
	 */
	public $taxonomy_slug = 'eligibility';

	/**
	 * @var string Slug for tracking the Major taxonomy.
	 */
	public $taxonomy_slug_major = 'major';

	/**
	 * @var string Slug for tracking the Citizenship taxonomy.
	 */
	public $taxonomy_slug_citizenship = 'citizenship';

	/**
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public $taxonomy_slug_gender = 'gender-identity';

	/**
	 * @var string Slug for tracking the Gender Identity taxonomy.
	 */
	public $taxonomy_slug_ethnicity = 'ethnicity';

	/**
	 * @var array A list of post meta keys associated with scholarships.
	 */
	var $post_meta_keys = array(
		'scholarship_gpa',
		'scholarship_age_min',
		'scholarship_age_max',
		'scholarship_deadline',
		'scholarship_amount',
		'scholarship_essay',
		'scholarship_enrolled',
		'scholarship_year',
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
	 * @var array A list of states for the State of Residence field.
	 */
	var $states = array(
		'Alabama',
		'Alaska',
		'Arizona',
		'Arkansas',
		'California',
		'Colorado',
		'Connecticut',
		'Delaware',
		'Florida',
		'Georgia',
		'Hawaii',
		'Idaho',
		'Illinois',
		'Indiana',
		'Iowa',
		'Kansas',
		'Kentucky',
		'Louisiana',
		'Maine',
		'Maryland',
		'Massachusetts',
		'Michigan',
		'Minnesota',
		'Mississippi',
		'Missouri',
		'Montana',
		'Nebraska',
		'Nevada',
		'New Hampshire',
		'New Jersey',
		'New Mexico',
		'New York',
		'North Carolina',
		'North Dakota',
		'Ohio',
		'Oklahoma',
		'Oregon',
		'Pennsylvania',
		'Rhode Island',
		'South Carolina',
		'South Dakota',
		'Tennessee',
		'Texas',
		'Utah',
		'Vermont',
		'Virginia',
		'Washington',
		'West Virginia',
		'Wisconsin',
		'Wyoming',
	);

	/**
	 * @var array A list of classes for the Years in School field.
	 */
	var $years = array(
		'Freshman',
		'Sophmore',
		'Junior',
		'Senior',
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
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_shortcode( 'wsuwp_scholarships', array( $this, 'display_wsuwp_scholarships' ) );
		add_action( 'wp_ajax_nopriv_set_scholarships', array( $this, 'ajax_callback' ) );
		add_action( 'wp_ajax_set_scholarships', array( $this, 'ajax_callback' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'the_content', array( $this, 'add_scholarship_content' ), 999, 1 );
	}

	/**
	 * Register a content type to track information about scholarships.
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
			'hierarchical' => false,
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
			'name' => 'Ethicity',
			'singular_name' => 'Ethicity',
			'all_items' => 'All Ethicities',
			'edit_item' => 'Edit Ethicity',
			'view_item' => 'View Ethicity',
			'update_item' => 'Update Ethicity',
			'add_new_item' => 'Add New Ethicity',
			'new_item_name' => 'New Ethicity Name',
			'search_items' => 'Search Ethicities',
			'popular_items' => 'Popular Gender Ethnicities',
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
	}

	/**
	 * Register the degree program factsheet post type.
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

		$args['description'] = 'Applicant must be enrolled';
		$args['type'] = '';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_checkbox';
		register_meta( 'post', 'scholarship_enrolled', $args );

		$args['description'] = "Applicant's year in school";
		$args['type'] = 'string';
		$args['sanitize_callback'] = 'WSUWP_Graduate_Degree_Programs::sanitize_year_in_school';
		register_meta( 'post', 'scholarship_year', $args );

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
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && get_current_screen()->id !== $this->content_type_slug ) {
			return;
		}

		wp_enqueue_style( 'wsuwp-scholarship-admin', plugins_url( 'css/scholarships-admin.css', dirname( __FILE__ ) ) );
	}

	/**
	 * Add the metabox used to capture scholarship information.
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
	 * @param WP_Post $post Object for the post currently being edited.
	 */
	public function display_scholarship_meta_box( $post ) {
		$gpa = get_post_meta( $post->ID, 'scholarship_gpa', true );
		$age_min = get_post_meta( $post->ID, 'scholarship_age_min', true );
		$age_max = get_post_meta( $post->ID, 'scholarship_age_max', true );
		$deadline = get_post_meta( $post->ID, 'scholarship_deadline', true );
		$amount = get_post_meta( $post->ID, 'scholarship_amount', true );
		$essay = get_post_meta( $post->ID, 'scholarship_essay', true );
		$enrolled = get_post_meta( $post->ID, 'scholarship_enrolled', true );
		$year = get_post_meta( $post->ID, 'scholarship_year', true );
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

			<input type="text" class="widefat" name="scholarship_gpa" placeholder="Minimum GPA" value="<?php echo esc_attr( $gpa ); ?>" />

			<input type="number" class="widefat" name="scholarship_age_min" placeholder="Minimum Age" value="<?php echo esc_attr( $age_min ); ?>" />

			<input type="number" class="widefat" name="scholarship_age_max" placeholder="Maximum Age" value="<?php echo esc_attr( $age_max ); ?>" />

		</div>

		<div class="wsuwp-scholarship-fieldset">

			<input type="text" class="widefat" name="scholarship_deadline" placeholder="Deadline (yyyy-mm-dd)" value="<?php echo esc_attr( $deadline ); ?>" pattern="\d{4}-\d{2}-\d{2}" />

			<input type="text" class="widefat" name="scholarship_amount" placeholder="Amount" value="<?php echo esc_attr( $amount ); ?>" />

		</div>

		<div class="wsuwp-scholarship-fieldset">

			<div>

				<p>Eligibility Requirements</p>

				<label><input value="1" type="checkbox" name="scholarship_essay"<?php checked( $essay, 1 ); ?> /> Essay</label><br />

				<label><input value="1" type="checkbox" name="scholarship_enrolled"<?php checked( $enrolled, 1 ); ?> /> Must be currently enrolled</label><br />

				<select name="scholarship_year">
					<option>Year in School</option>
					<?php foreach ( $this->years as $year_option ) { ?>
						<option value="<?php echo esc_attr( $year_option ); ?>"<?php selected( $year, $year_option ); ?>><?php echo esc_html( $year_option ); ?></option>
					<?php } ?>
				</select><br />

				<select name="scholarship_state">
					<option>State of Residence</option>
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

		<p>Contact</p>

		<div class="wsuwp-scholarship-fieldset">

			<input type="url" class="widefat" name="scholarship_site" placeholder="Website" pattern="https?://.+" value="<?php echo esc_attr( $site ); ?>" />

			<input type="email" class="widefat" name="scholarship_email" placeholder="Email" value="<?php echo esc_attr( $email ); ?>" />

			<input type="tel" class="widefat" name="scholarship_phone" placeholder="Phone (555-555-5555)" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $phone ); ?>" />

			<input type="text" class="widefat" name="scholarship_address" placeholder="Address" value="<?php echo esc_attr( $address ); ?>" />

		</div>
		<?php
	}

	/**
	 * Display the metabox used to capture granting organization information.
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

		<p>Contact</p>

		<div class="wsuwp-scholarship-fieldset">

			<input type="url" class="widefat" name="scholarship_org_site" placeholder="Website" value="<?php echo esc_attr( $org_site ); ?>" />

			<input type="email" class="widefat" name="scholarship_org_email" placeholder="Email" value="<?php echo esc_attr( $org_email ); ?>" />

			<input type="tel" class="widefat" name="scholarship_org_phone" placeholder="Phone (555-555-5555)" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $org_phone ); ?>" />

		</div>
		<?php
	}

	/**
	 * @param string $value The unsanitized checkbox value.
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
	 * @param string $state The unsanitized Year in School value.
	 *
	 * @return string the sanitized Year in School value.
	*/
	public static function sanitize_year_in_school( $year ) {
		if ( in_array( $year, $this->years ) ) {
			$year = $year;
		} else {
			$year = false;
		}

		return $year;
	}

	/**
	 * @param string $state The unsanitized State value.
	 *
	 * @return string the sanitized State value.
	*/
	public static function sanitize_state( $state ) {
		if ( in_array( $state, $this->states ) ) {
			$state = $state;
		} else {
			$state = false;
		}

		return $state;
	}

	/**
	 * Save the information assigned to the scholarship.
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
			if ( isset( $_POST[ $key ] ) && isset( $keys[ $key ] ) && isset( $keys[ $key ][ 'sanitize_callback'] ) ) {
				update_post_meta( $post_id, $key, $_POST[ $key ] );
			} else {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 */
	public function wp_enqueue_scripts() {
		$post = get_post();

		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wsuwp_scholarships' ) ) {
			wp_enqueue_style( 'wsuwp-scholarships', plugins_url( 'css/scholarships.css', dirname( __FILE__ ) ), array( 'spine-theme' ) );
			wp_enqueue_script( 'wsuwp-scholarships', plugins_url( 'js/scholarships.js', dirname( __FILE__ ) ), array( 'jquery' ), false, true );
			wp_localize_script( 'wsuwp-scholarships', 'scholarships', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wsuwp-scholarships' ),
			) );
		}

		if ( is_singular( $this->content_type_slug ) ) {
			wp_enqueue_style( 'wsuwp-scholarship', plugins_url( 'css/scholarship.css', dirname( __FILE__ ) ), array( 'spine-theme' ) );
		}
	}

	/**
	 * Display a form for browsing scholarships.
	 */
	public function display_wsuwp_scholarships() {
		ob_start();
		?>
		<p>Tell us about yourself using the form below to help us find scholarships you might be eligible for, or <a href="<?php echo esc_url( get_post_type_archive_link( $this->content_type_slug ) ); ?>">browse all scholarships &raquo;</a></p>

		<p>All fields are optional.</p>
		<form class="wsuwp-scholarships-form">

			<p>
				<label for="wsuwp-scholarship-age">Age</label><br />
				<input type="number" value="" id="wsuwp-scholarship-age">
			</p>

			<p>
				<label for="wsuwp-scholarship-gpa">G.P.A.</label><br />
				<input type="text" value="" id="wsuwp-scholarship-gpa" maxlength="4">
			</p>

			<p>
				Are you enrolled at WSU?<br />
				<label><input type="radio" name="wsuwp-scholarship-enrolled" value="yes"> Yes</label>
				<label><input type="radio" name="wsuwp-scholarship-enrolled" value="no"> No</label>
			</p>

			<p>
				Are you a U.S. resident?<br />
				<label><input type="radio" name="wsuwp-scholarship-resident" value="yes"> Yes</label>
				<label><input type="radio" name="wsuwp-scholarship-resident" value="no"> No</label>
			</p>

			<input type="submit" value="Go">

		</form>

		<div class="wsuwp-scholarships-filters">

			<?php
			$eligibility = get_terms( array(
				'taxonomy' => $this->taxonomy_slug,
			) );

			if ( ! empty( $eligibility ) ) {
			?>
				<p>Only show me scholarships with the following requirements</p>
				<ul class="wsuwp-scholarship-eligibility">
				<?php foreach ( $eligibility as $criteria ) { ?>
					<li>
						<input type="checkbox" value="<?php echo esc_attr( $criteria->slug ); ?>" id="<?php echo esc_attr( $criteria->slug ); ?>" />
						<label for="<?php echo esc_attr( $criteria->slug ); ?>"><?php echo esc_html( $criteria->name ); ?></label>
					</li>
				<?php } ?>
				</ul>
			<?php
			}
			?>

		</div>

		<div class="wsuwp-scholarships-header">
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
		<?php
		$html = ob_get_contents();

		ob_end_clean();

		return $html;
	}

	/**
	 * Handle the ajax callback for populating a list of scholarships.
	 */
	public function ajax_callback() {
		check_ajax_referer( 'wsuwp-scholarships', 'nonce' );

		$scholarships = array();

		// Initial scholarships query arguments.
		$scholarships_query_args = array(
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => -1,
			'post_type' => $this->content_type_slug,
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => '_wsuwp_scholarship_deadline',
					'value' => date( 'Y-m-d' ),
					'type' => 'date',
					'compare' => '>=',
				),
				array(
					'key' => '_wsuwp_scholarship_deadline',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		// Age parameters.
		if ( $_POST['age'] && is_numeric( $_POST['age'] ) ) {
			$scholarships_query_args['meta_query'][] = array(
				array(
					'relation' => 'OR',
					array(
						'key' => '_wsuwp_scholarship_age_min',
						'value' => sanitize_text_field( $_POST['age'] ),
						'type' => 'numeric',
						'compare' => '<=',
					),
					array(
						'key' => '_wsuwp_scholarship_age_min',
						'compare' => 'NOT EXISTS',
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key' => '_wsuwp_scholarship_age_max',
						'value' => sanitize_text_field( $_POST['age'] ),
						'type' => 'numeric',
						'compare' => '>=',
					),
					array(
						'key' => '_wsuwp_scholarship_age_max',
						'compare' => 'NOT EXISTS',
					),
				),
			);
		}

		// GPA parameters.
		if ( $_POST['gpa'] ) {
			$scholarships_query_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => '_wsuwp_scholarship_gpa',
					'value' => sanitize_text_field( $_POST['gpa'] ),
					'type' => 'DECIMAL(10,2)',
					'compare' => '<=',
				),
				array(
					'key' => '_wsuwp_scholarship_gpa',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// Taxonomy parameters.
		if ( $_POST['enrolled'] && 'no' === $_POST['enrolled'] ) {
			$scholarships_query_args['tax_query'][] = array(
				'taxonomy' => $this->taxonomy_slug,
				'field' => 'slug',
				'terms' => 'enrolled',
				'operator' => 'NOT IN',
			);
		}

		if ( $_POST['resident'] && 'no' === $_POST['resident'] ) {
			$scholarships_query_args['tax_query'][] = array(
				'taxonomy' => $this->taxonomy_slug,
				'field' => 'slug',
				'terms' => 'resident',
				'operator' => 'NOT IN',
			);
		}

		$scholarships_query = new WP_Query( $scholarships_query_args );

		if ( $scholarships_query->have_posts() ) {
			$i = 0;
			while ( $scholarships_query->have_posts() ) {
				$scholarships_query->the_post();
				$deadline = get_post_meta( get_the_ID(), '_wsuwp_scholarship_deadline', true );
				$amount = get_post_meta( get_the_ID(), '_wsuwp_scholarship_amount', true );

				// Parse Amount value for javascript sorting.
				$amount_pieces =  explode( '-', $amount );
				$numeric_amount = str_replace( ',', '', $amount_pieces[0] );
				$amount_data_value = ( $amount && is_numeric( $numeric_amount ) ) ? $numeric_amount : 0;

				// Parse Deadline value for javascript sorting.
				$deadline_data_value = ( $deadline ) ? str_replace( '-', '', $deadline ) : 0;

				// Parse deadline for display
				$date = DateTime::createFromFormat( 'Y-m-d', $deadline );
				$deadline_display = ( $date instanceof DateTime ) ? $date->format('m/d/Y') : $deadline;
				?>
				<article <?php post_class(); ?> data-scholarship="<?php echo esc_html( $i ); ?>" data-amount="<?php echo esc_attr( $amount_data_value ); ?>" data-deadline="<?php echo esc_attr( $deadline_data_value ); ?>">
					<header>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</header>
					<div class="amount"><?php
					if ( $amount ) {
						$prepend = ( is_numeric( $numeric_amount ) ) ? '$' : '';
						echo esc_html( $prepend . $amount );
					}
					?></div>
					<div class="deadline"><?php
					if ( $deadline ) {
						echo esc_html( $deadline_display );
					}
					?></div>
				</article>
				<?php
				$i++;
			}

			wp_reset_postdata();
		} else {
			echo '<p>Sorry, no scholarships were found. Please try changing your search or <a href="#">browsing all scholarships &raquo;</a></p>';
		}

		exit();
	}

	/**
	 * Add content areas for custom meta data when the Scholarship content type is being displayed.
	 *
	 * @param string $content Current object content.
	 *
	 * @return string Modified content.
	 */
	public function add_scholarship_content( $content ) {
		if ( false === is_singular( $this->content_type_slug ) ) {
			return $content;
		}

		$deadline = get_post_meta( get_the_ID(), 'scholarship_deadline', true );
		$amount = get_post_meta( get_the_ID(), 'scholarship_amount', true );
		$paper = get_post_meta( get_the_ID(), 'scholarship_app_paper', true );
		$online = get_post_meta( get_the_ID(), 'scholarship_app_online', true );
		$site = get_post_meta( get_the_ID(), 'scholarship_site', true );
		$email = get_post_meta( get_the_ID(), 'scholarship_email', true );
		$phone = get_post_meta( get_the_ID(), 'scholarship_phone', true );
		$address = get_post_meta( get_the_ID(), 'scholarship_address', true );
		$org_name = get_post_meta( get_the_ID(), 'scholarship_org_name', true );
		$org = get_post_meta( get_the_ID(), 'scholarship_org', true );
		$org_site = get_post_meta( get_the_ID(), 'scholarship_org_site', true );
		$org_email = get_post_meta( get_the_ID(), 'scholarship_org_email', true );
		$org_phone = get_post_meta( get_the_ID(), 'scholarship_org_phone', true );
		$added_html = '';

		if ( $deadline ) {
			$date = DateTime::createFromFormat( 'Y-m-d', $deadline );
			$deadline_display = ( $date instanceof DateTime ) ? $date->format('m/d/Y') : $deadline;
			$added_html .= '<p><strong>Deadline:</strong> ' . esc_html( $deadline_display ) . '</p>';
		}

		if ( $amount ) {
			$amount_pieces =  explode( '-', $amount );
			$numeric_amount = str_replace( ',', '', $amount_pieces[0] );
			$prepend = ( is_numeric( $numeric_amount ) ) ? '$' : '';
			$added_html .= '<p><strong>Amount:</strong> ' . esc_html( $prepend . $amount ) . '</p>';
		}

		if ( $paper ) {
			$added_html .= '<p><strong>Paper Application Available</strong></p>';
		}

		if ( $online ) {
			$added_html .= '<p><strong>Online Application Available</strong></p>';
		}

		if ( $site || $email || $phone || $address ) {
			$added_html .= '<p><strong>Contact information:</strong></p>';
			$added_html .= '<ul>';

			if ( $site ) {
				$added_html .= '<li><a href="' . esc_url( $site ) . '">' . esc_html( $site ) . '</a></li>';
			}

			if ( $email ) {
				$added_html .= '<li><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
			}

			if ( $phone ) {
				$added_html .= '<li>' . esc_html( $phone ) . '</li>';
			}

			if ( $address ) {
				$added_html .= '<li>' . esc_html( $address ) . '</li>';
			}

			$added_html .= '</ul>';
		}

		if ( $org_name || $org || $org_site || $org_email || $org_phone ) {
			$granter = ( $org_name ) ? $org_name : 'the granter';
			$added_html .= '<p><strong>About ' . esc_html( $granter ) . '</strong></p>';

			if ( $org ) {
				$added_html .= wpautop( wp_kses_post( $org ) );
			}

			$added_html .= '<ul>';

			if ( $org_site ) {
				$added_html .= '<li><strong>Web:</strong> <a href="' . esc_url( $org_site ) . '">' . esc_html( $org_site ) . '</a></li>';
			}

			if ( $org_email ) {
				$added_html .= '<li><strong>Email:</strong> <a href="mailto:' . esc_attr( $org_email ) . '">' . esc_html( $org_email ) . '</a></li>';
			}

			if ( $org_phone ) {
				$added_html .= '<li><strong>Phone:</strong> ' . esc_html( $org_phone ) . '</li>';
			}

			$added_html .= '</ul>';
		}

		return $content . $added_html;
	}

	/**
	 * Add body classes for the site domain and path to help with targeting on multiple
	 * sites using this theme.
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( is_singular( $this->content_type_slug ) ) {
			$classes[] = 'tagged-blue';
		}

		return $classes;
	}
}
