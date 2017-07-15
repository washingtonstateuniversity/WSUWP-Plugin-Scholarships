<?php

namespace WSU\Scholarships\Settings;

add_action( 'admin_init', 'WSU\Scholarships\Settings\register_settings' );
/**
 * Registers settings for the Scholarship Settings admin page.
 *
 * @since 0.0.1
 */
function register_settings() {
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

	add_settings_section(
		'menu_item',
		null,
		null,
		'settings'
	);

	add_settings_field(
		'search_page',
		'Results Page',
		'WSU\Scholarships\Settings\search_page_dropdown',
		'settings',
		'url',
		array(
			'label_for' => 'search_page',
		)
	);

	add_settings_field(
		'active_menu_item',
		'Active Menu Item',
		'WSU\Scholarships\Settings\active_menu_item_page_dropdown',
		'settings',
		'menu_item',
		array(
			'label_for' => 'active_menu_item',
		)
	);
}

/**
 * Outputs the Search Page URL field.
 *
 * @since 0.0.1
 *
 * @param array $args Extra arguments used when outputting the field.
 */
function search_page_dropdown( $args ) {
	$options = get_option( 'scholarships_settings' );
	$search_page_id = ( $options && isset( $options[ $args['label_for'] ] ) ) ? $options[ $args['label_for'] ] : 0;
	?>
	<select name="scholarships_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="">- Select -</option>
		<?php
		$pages = get_pages();
		foreach ( $pages as $page ) {
			?><option value="<?php echo esc_attr( $page->ID ); ?>"<?php selected( $search_page_id, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option><?php
		}
		?>
	</select>
	<p class="description">Select the page that is using the <code>[wsuwp_scholarships]</code> shortcode.</p>
	<?php
}

/**
 * Outputs the Search Page URL field.
 *
 * @since 0.0.1
 *
 * @param array $args Extra arguments used when outputting the field.
 */
function active_menu_item_page_dropdown( $args ) {
	$options = get_option( 'scholarships_settings' );
	$menu_item_id = ( $options && isset( $options[ $args['label_for'] ] ) ) ? $options[ $args['label_for'] ] : 0;
	?>
	<select name="scholarships_settings[<?php echo esc_attr( $args['label_for'] ); ?>]">
		<option value="">- Select -</option>
		<?php
		$pages = get_pages();
		foreach ( $pages as $page ) {
			?><option value="<?php echo esc_attr( $page->ID ); ?>"<?php selected( $menu_item_id, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option><?php
		}
		?>
	</select>
	<p class="description">Select the page to mark as the active menu item when the search results page or an individual scholarship is being viewed.</p>
	<?php
}

add_action( 'admin_menu', 'WSU\Scholarships\Settings\add_settings_page' );
/**
 * Creates an admin page for Scholarship Settings.
 *
 * @since 0.0.1
 */
function add_settings_page() {
	add_submenu_page(
		'edit.php?post_type=' . \WSU\Scholarships\Post_Type\post_type_slug(),
		'Scholarship Database Settings',
		'Settings',
		'manage_options',
		'settings',
		'WSU\Scholarships\Settings\settings_page'
	);
}

/**
 * Displays the Scholarships Settings page.
 *
 * @since 0.0.1
 */
function settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) { //@codingStandardsIgnoreLine
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
