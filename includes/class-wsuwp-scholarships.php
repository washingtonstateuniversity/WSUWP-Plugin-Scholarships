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
		add_action( 'init', array( $this, 'register_taxonomy' ), 15 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'add_meta_boxes_scholarship', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
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
				'author',
			),
			'taxonomies' => array(
				'wsuwp_university_org'
			),
			'has_archive' => true,
		);

		register_post_type( $this->content_type_slug, $args );
	}

	/**
	 * Register a scholarship eligibility taxonomy that will be attached to the scholarship content types.
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => 'Eligibility',
			'singular_name' => 'Criteria',
			'all_items'     => 'All Criteria',
			'edit_item'     => 'Edit Criteria',
			'view_item'     => 'View Criteria',
			'update_item'   => 'Update Criteria',
			'add_new_item'  => 'Add New Criteria',
			'new_item_name' => 'New Criteria Name',
			'parent_item'   => 'Parent Criteria',
			'search_items'  => 'Search Criteria',
			'not_found'     => 'No criteria found',
		);
		$args = array(
			'labels'            => $labels,
			'description'       => 'Scholarship eligibility requirements.',
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
		);
		register_taxonomy( $this->taxonomy_slug, $this->content_type_slug, $args );
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
	}

	/**
	 * Display the metabox used to capture scholarship information.
	 *
	 * @param WP_Post $post Object for the post currently being edited.
	 */
	public function display_scholarship_meta_box( $post ) {
		$gpa = get_post_meta( $post->ID, '_wsuwp_scholarship_gpa', true );
		$age_min = get_post_meta( $post->ID, '_wsuwp_scholarship_age_min', true );
		$age_max = get_post_meta( $post->ID, '_wsuwp_scholarship_age_max', true );
		$eligibility = get_post_meta( $post->ID, '_wsuwp_scholarship_eligibility', true );
		$deadline = get_post_meta( $post->ID, '_wsuwp_scholarship_deadline', true );
		$amount = get_post_meta( $post->ID, '_wsuwp_scholarship_amount', true );
		$paper = get_post_meta( $post->ID, '_wsuwp_scholarship_application_paper', true );
		$online = get_post_meta( $post->ID, '_wsuwp_scholarship_application_online', true );
		$site = get_post_meta( $post->ID, '_wsuwp_scholarship_site', true );
		$email = get_post_meta( $post->ID, '_wsuwp_scholarship_email', true );
		$phone = get_post_meta( $post->ID, '_wsuwp_scholarship_phone', true );
		$address = get_post_meta( $post->ID, '_wsuwp_scholarship_address', true );
		$details = get_post_meta( $post->ID, '_wsuwp_scholarship_details', true );
		$org = get_post_meta( $post->ID, '_wsuwp_scholarship_org', true );
		$org_site = get_post_meta( $post->ID, '_wsuwp_scholarship_org_site', true );
		$org_email = get_post_meta( $post->ID, '_wsuwp_scholarship_org_email', true );
		$org_phone = get_post_meta( $post->ID, '_wsuwp_scholarship_org_phone', true );

		wp_nonce_field( 'save-wsu-scholarship-meta', '_wsu_scholarship_meta_nonce' );

		?>
		<div class="wsuwp-scholarship-fieldset">

			<input type="text" class="widefat" name="wsuwp_scholarship_gpa" placeholder="Minimum GPA" value="<?php echo esc_attr( $gpa ); ?>" />

			<input type="number" class="widefat" name="wsuwp_scholarship_age_min" placeholder="Minimum Age" value="<?php echo esc_attr( $age_min ); ?>" />

			<input type="number" class="widefat" name="wsuwp_scholarship_age_max" placeholder="Maximum Age" value="<?php echo esc_attr( $age_max ); ?>" />

		</div>

		<p>Specific Eligibility Details</p>

		<textarea class="wsuwp-scholarship-textarea" name="wsuwp_scholarship_eligibility"><?php echo esc_textarea( $eligibility ); ?></textarea>

		<div class="wsuwp-scholarship-fieldset">

			<input type="text" class="widefat" name="wsuwp_scholarship_deadline" placeholder="Deadline (mm/dd/yyy)" value="<?php echo esc_attr( $deadline ); ?>" pattern="\d{2}/\d{2}/\d{4}" />

			<input type="text" class="widefat" name="wsuwp_scholarship_amount" placeholder="Amount" value="<?php echo esc_attr( $amount ); ?>" />

		</div>

		<p>Application availability</p>

		<label><input value="1" type="checkbox" name="wsuwp_scholarship_application_paper"<?php checked( $paper, 1 ); ?>> Paper</label><br />

		<label><input value="1" type="checkbox" name="wsuwp_scholarship_application_online"<?php checked( $online, 1 ); ?>> Online</label>

		<p>Contact</p>

		<div class="wsuwp-scholarship-fieldset">

			<input type="url" class="widefat" name="wsuwp_scholarship_site" placeholder="Website" pattern="https?://.+" value="<?php echo esc_attr( $site ); ?>" />

			<input type="email" class="widefat" name="wsuwp_scholarship_email" placeholder="Email" value="<?php echo esc_attr( $email ); ?>" />

			<input type="tel" class="widefat" name="wsuwp_scholarship_phone" placeholder="Phone (555-555-5555)" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $phone ); ?>" />

			<input type="text" class="widefat" name="wsuwp_scholarship_address" placeholder="Address" value="<?php echo esc_attr( $address ); ?>" />

		</div>

		<p>More Details</p>

		<textarea class="wsuwp-scholarship-textarea" name="wsuwp_scholarship_details"><?php echo esc_textarea( $details ); ?></textarea>

		<p>About the organization</p>

		<textarea class="wsuwp-scholarship-textarea" name="wsuwp_scholarship_org"><?php echo esc_textarea( $org ); ?></textarea>

		<p>Organization Contact</p>

		<div class="wsuwp-scholarship-fieldset">

			<input type="url" class="widefat" name="wsuwp_scholarship_org_site" placeholder="Website" value="<?php echo esc_attr( $org_site ); ?>" />

			<input type="email" class="widefat" name="wsuwp_scholarship_org_email" placeholder="Email" value="<?php echo esc_attr( $org_email ); ?>" />

			<input type="tel" class="widefat" name="wsuwp_scholarship_org_phone" placeholder="Phone (555-555-5555)" pattern="\d{3}[\-]\d{3}[\-]\d{4}" value="<?php echo esc_attr( $org_phone ); ?>" />

		</div>
		<?php
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

		if ( ! isset( $_POST['_wsu_scholarship_meta_nonce'] ) || false === wp_verify_nonce( $_POST['_wsu_scholarship_meta_nonce'], 'save-wsu-scholarship-meta' ) ) {
			return;
		}

		if ( isset( $_POST['wsuwp_scholarship_gpa'] ) && ! empty( trim( $_POST['wsuwp_scholarship_gpa'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_gpa', sanitize_text_field( $_POST['wsuwp_scholarship_gpa'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_gpa' );
		}

		if ( isset( $_POST['wsuwp_scholarship_age_min'] ) && ! empty( trim( $_POST['wsuwp_scholarship_age_min'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_age_min', absint( $_POST['wsuwp_scholarship_age_min'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_age_min' );
		}

		if ( isset( $_POST['wsuwp_scholarship_age_max'] ) && ! empty( trim( $_POST['wsuwp_scholarship_age_max'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_age_max', absint( $_POST['wsuwp_scholarship_age_max'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_age_max' );
		}

		if ( isset( $_POST['wsuwp_scholarship_eligibility'] ) && ! empty( trim( $_POST['wsuwp_scholarship_eligibility'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_eligibility', wp_kses_post( $_POST['wsuwp_scholarship_eligibility'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_eligibility' );
		}

		if ( isset( $_POST['wsuwp_scholarship_deadline'] ) && ! empty( trim( $_POST['wsuwp_scholarship_deadline'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_deadline', sanitize_text_field( $_POST['wsuwp_scholarship_deadline'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_deadline' );
		}

		if ( isset( $_POST['wsuwp_scholarship_amount'] ) && ! empty( trim( $_POST['wsuwp_scholarship_amount'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_amount', sanitize_text_field( $_POST['wsuwp_scholarship_amount'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_amount' );
		}

		if ( isset( $_POST['wsuwp_scholarship_application_paper'] ) && '1' === $_POST['wsuwp_scholarship_application_paper'] ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_application_paper', 1 );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_application_paper' );
		}

		if ( isset( $_POST['wsuwp_scholarship_application_online'] ) && '1' === $_POST['wsuwp_scholarship_application_online'] ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_application_online', 1 );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_application_online' );
		}

		if ( isset( $_POST['wsuwp_scholarship_site'] ) && ! empty( trim( $_POST['wsuwp_scholarship_site'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_site', esc_url_raw( $_POST['wsuwp_scholarship_site'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_site' );
		}

		if ( isset( $_POST['wsuwp_scholarship_email'] ) && ! empty( trim( $_POST['wsuwp_scholarship_email'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_email', sanitize_email( $_POST['wsuwp_scholarship_email'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_email' );
		}

		if ( isset( $_POST['wsuwp_scholarship_phone'] ) && ! empty( trim( $_POST['wsuwp_scholarship_phone'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_phone', sanitize_text_field( $_POST['wsuwp_scholarship_phone'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_phone' );
		}

		if ( isset( $_POST['wsuwp_scholarship_address'] ) && ! empty( trim( $_POST['wsuwp_scholarship_address'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_address', sanitize_text_field( $_POST['wsuwp_scholarship_address'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_address' );
		}

		if ( isset( $_POST['wsuwp_scholarship_details'] ) && ! empty( trim( $_POST['wsuwp_scholarship_details'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_details', wp_kses_post( $_POST['wsuwp_scholarship_details'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_details' );
		}

		if ( isset( $_POST['wsuwp_scholarship_org'] ) && ! empty( trim( $_POST['wsuwp_scholarship_org'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_org', wp_kses_post( $_POST['wsuwp_scholarship_org'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_org' );
		}

		if ( isset( $_POST['wsuwp_scholarship_org_site'] ) && ! empty( trim( $_POST['wsuwp_scholarship_org_site'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_org_site', esc_url_raw( $_POST['wsuwp_scholarship_org_site'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_org_site' );
		}

		if ( isset( $_POST['wsuwp_scholarship_org_email'] ) && ! empty( trim( $_POST['wsuwp_scholarship_org_email'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_org_email', sanitize_email( $_POST['wsuwp_scholarship_org_email'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_org_email' );
		}

		if ( isset( $_POST['wsuwp_scholarship_org_phone'] ) && ! empty( trim( $_POST['wsuwp_scholarship_org_phone'] ) ) ) {
			update_post_meta( $post_id, '_wsuwp_scholarship_org_phone', sanitize_text_field( $_POST['wsuwp_scholarship_org_phone'] ) );
		} else {
			delete_post_meta( $post_id, '_wsuwp_scholarship_org_phone' );
		}

	}
}
