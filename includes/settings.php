<?php namespace WSUWP\Plugin\Scholarships;

class Settings {

	private static $options_group = 'writing';


	public static function register_settings() {

		// create section
		add_settings_section(
			self::$options_group,
			'Scholarships Settings',
			'',
			'writing'
		);

		// register fields
		register_setting( self::$options_group, 'wsu_scholarships_plugin_enable_post_type' );

		// add fields
		add_settings_field(
			'wsu_scholarships_plugin_enable_post_type',
			'Enable Scholarships Post Type',
			__CLASS__ . '::input_checkbox',
			'writing',
			self::$options_group,
			array(
				'id'          => 'enable-scholarships-post-type-input',
				'label'       => 'Yes',
				'label_for'   => 'wsu_scholarships_plugin_enable_post_type',
				'class'       => '',
				'description' => '',
				'default_value' => 0,
			)
		);

	}


	public static function input_checkbox( $args ) {

		$option = get_option( $args['label_for'], $args['default_value'] );
		$checked_attr = checked( 1, (int) $option, false );

		$html  = '<input type="checkbox" id="' . esc_attr( $args['id'] ) . '" name="' . esc_html( $args['label_for'] ) . '" value="1" ' . $checked_attr . '/>';
		$html .= '<label for="' . esc_attr( $args['id'] ) . '">' . esc_attr( $args['label'] ) . '</label>';
		$html .= '<p class="description">' . $args['description'] . '</p>';

		echo $html;

	}


	public static function init() {

		add_action( 'admin_init', __CLASS__ . '::register_settings' );

	}
}

Settings::init();
