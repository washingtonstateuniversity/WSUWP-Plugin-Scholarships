<?php

class WSUWP_Scholarship_Contributor_Role {
	/**
	 * @since 0.0.7
	 *
	 * @var WSUWP_Scholarship_Contributor_Role
	 */
	private static $instance;

	/**
	 * @since 0.0.7
	 *
	 * @var string The name of the Scholarship Contributor role.
	 */
	public $scholarship_contributor = 'wsuwp_scholarship_contributor';

	/**
	 * Maintain and return the one instance and initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.7
	 *
	 * @return \WSUWP_Scholarship_Contributor_Role
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSUWP_Scholarship_Contributor_Role();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Adds the hooks used to create and manage the Scholarship Contributor role and capabilities.
	 *
	 * @since 0.0.7
	 */
	public function setup_hooks() {
		add_action( 'init', array( $this, 'add_scholarship_contributor_role' ) );
		add_action( 'init', array( $this, 'map_scholarship_contributor_capabilities' ), 13 );
		add_action( 'pre_get_posts', array( $this, 'filter_list_tables' ) );
	}

	/**
	 * Adds the Scholarship Contributor role.
	 *
	 * @since 0.0.7
	 */
	public function add_scholarship_contributor_role() {
		if ( array_key_exists( $this->scholarship_contributor, WP_Roles()->get_names() ) ) {
			return;
		}

		add_role(
			WSUWP_Scholarship_Contributor_Role()->scholarship_contributor,
			'Scholarship Contributor',
			array(
				'create_scholarships' => true,
				'edit_scholarships' => true,
				'read' => true,
				'upload_files' => true,
			)
		);
	}

	/**
	 * Maps the Scholarship Contributor role capabilities to the scholarship post type.
	 *
	 * @since 0.0.7
	 */
	public function map_scholarship_contributor_capabilities() {
		$user = wp_get_current_user();

		if ( ! in_array( $this->scholarship_contributor, (array) $user->roles, true ) ) {
			return;
		}

		$scholarships = get_post_type_object( WSUWP_Scholarship_Post_Type::$post_type_slug );

		if ( $scholarships ) {
			$scholarships->cap->create_posts = 'create_scholarships';
			$scholarships->cap->edit_posts = 'edit_scholarships';
		}

		$taxonomies = get_taxonomies( array(), 'objects' );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomy->cap->assign_terms = 'edit_scholarships';
			}
		}
	}

	/**
	 * Filters the media library view for users with the Scholarship Contributor role.
	 *
	 * @since 0.0.7
	 *
	 * @param WP_Query $query
	 */
	public function filter_list_tables( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$user = wp_get_current_user();

		if ( ! in_array( $this->scholarship_contributor, (array) $user->roles, true ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'upload' === $screen->id || ( isset( $_REQUEST['action'] ) && 'query-attachments' === $_REQUEST['action'] ) ) { //@codingStandardsIgnoreLine
			$query->set( 'author', $user->ID );
		}
	}
}
