<?php

namespace WSU\Scholarships\Contributor_Role;

/**
 * Provides the name of the Scholarship Contributor role.
 *
 * @since 0.1.0
 *
 * @return string
 */
function scholarship_contributor() {
	return 'wsuwp_scholarship_contributor';
}

add_action( 'init', 'WSU\Scholarships\Contributor_Role\add_scholarship_contributor_role' );
/**
 * Adds the Scholarship Contributor role.
 *
 * @since 0.1.0
 */
function add_scholarship_contributor_role() {
	if ( array_key_exists( scholarship_contributor(), \WP_Roles()->get_names() ) ) {
		return;
	}

	add_role(
		scholarship_contributor(),
		'Scholarship Contributor',
		array(
			'create_scholarships' => true,
			'edit_scholarships' => true,
			'read' => true,
			'upload_files' => true,
		)
	);
}

add_action( 'init', 'WSU\Scholarships\Contributor_Role\map_scholarship_contributor_capabilities', 13 );
/**
 * Maps the Scholarship Contributor role capabilities to the scholarship post type.
 *
 * @since 0.1.0
 */
function map_scholarship_contributor_capabilities() {
	$user = wp_get_current_user();

	if ( ! in_array( scholarship_contributor(), (array) $user->roles, true ) ) {
		return;
	}

	$scholarships = get_post_type_object( \WSU\Scholarships\Post_Type\post_type_slug() );

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

add_action( 'pre_get_posts', 'WSU\Scholarships\Contributor_Role\filter_list_tables' );
/**
 * Filters the media library view for users with the Scholarship Contributor role.
 *
 * @since 0.1.0
 *
 * @param WP_Query $query
 */
function filter_list_tables( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$user = wp_get_current_user();

	if ( ! in_array( scholarship_contributor(), (array) $user->roles, true ) ) {
		return;
	}

	if ( 'attachment' === $query->query['post_type'] ) {
		$query->set( 'author', $user->ID );
	}
}
