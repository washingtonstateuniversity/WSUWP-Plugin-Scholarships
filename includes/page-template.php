<?php namespace WSUWP\Plugin\Scholarships;

class Page_Template {

	public static function append_meta_content( $content ) {

		if ( ! is_admin() && is_singular( \WSU\Scholarships\Post_Type\post_type_slug() ) && is_main_query() ) {
			// $post = get_post();
			global $post;
			$id = $post->ID;

			$deadline = get_post_meta( $id, 'scholarship_deadline', true );
			$amount = get_post_meta( $id, 'scholarship_amount', true );
			$paper = get_post_meta( $id, 'scholarship_app_paper', true );
			$online = get_post_meta( $id, 'scholarship_app_online', true );
			$site = get_post_meta( $id, 'scholarship_site', true );
			$email = get_post_meta( $id, 'scholarship_email', true );
			$phone = get_post_meta( $id, 'scholarship_phone', true );
			$address = get_post_meta( $id, 'scholarship_address', true );
			$org_name = get_post_meta( $id, 'scholarship_org_name', true );
			$org = get_post_meta( $id, 'scholarship_org', true );
			$org_site = get_post_meta( $id, 'scholarship_org_site', true );
			$org_email = get_post_meta( $id, 'scholarship_org_email', true );
			$org_phone = get_post_meta( $id, 'scholarship_org_phone', true );			

			$new_content = '<div class="wsu-row wsu-row--sidebar-right">';
				$new_content .= '<div class="wsu-column">';
				$new_content .= $content;

				if ( $org_name || $org || $org_site || $org_email || $org_phone ) {
					$granter = ( $org_name ) ? $org_name : 'the granter';
					$new_content .= '<h2>About ' . esc_html( $granter ) . '</h2>';					
	
					if ( $org ) {
						$new_content .= wp_kses_post( wpautop( $org ) );
					}

					if($org_email || $org_site || $org_phone){
						$new_content .= '<ul>';
						$new_content .= $org_site ? '<li><strong>Web:</strong> <a href="' . esc_url( $org_site ) . '">' . esc_html( $org_site ) .'</a></li>' : '';
						$new_content .= $org_email ? '<li><strong>Email:</strong> <a href="mailto:' . esc_attr( $org_email ) . '">' . esc_html( $org_email ) .'</a></li>' : '';
						$new_content .= $org_phone ? '<li><strong>Phone:</strong> ' . esc_html( $org_phone ) . '</li>' : '';
						$new_content .= '</ul>';
					}
				}

				$new_content .= '</div>';
				$new_content .= '<div class="wsu-column">';

				if($site){
					$new_content .= '<div class="wsu-cta  wsu-cta--width-full">';
					$new_content .= '<a href="' . esc_url( $site ) . '" class="wsu-button  wsu-button--size-small">Apply</a>';
					$new_content .= '</div>';
				}

				if ( $deadline ) {
					$date = \DateTime::createFromFormat( 'Y-m-d', $deadline );
					$deadline_display = ( $date instanceof DateTime ) ? $date->format( 'm/d/Y' ) : $deadline;
					$new_content .= '<p><strong>Deadline:</strong> ' . esc_html( $deadline_display ) . '</p>';
				}
	
				if ( $amount ) {
					$amount_pieces = explode( '-', $amount );
					$numeric_amount = str_replace( ',', '', $amount_pieces[0] );
					$prepend = ( is_numeric( $numeric_amount ) ) ? '$' : '';
					$new_content .= '<p><strong>Amount:</strong> ' . esc_html( $prepend . $amount ) . '</p>';
				}
	
				if ( $paper ) {
					$new_content .= '<p><strong>Paper Application Available</strong></p>';
				}
	
				if ( $online ) {
					$new_content .= '<p><strong>Online Application Available</strong></p>';
				}

				$new_content .= '</div>';
			$new_content .= '</div>';			

			return $new_content;
		}

		return $content;

	}


	public static function init() {

		add_filter( 'the_content', array( __CLASS__, 'append_meta_content' ), 1 );

	}
}

Page_Template::init();
