<?php
/**
 * Free Downloads - WooCommerce - Capture Email Form
 * 
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user_id = get_current_user_id();
$user_email = '';
if ( $current_user_id ) {
	$user = get_user_by( 'ID', $current_user_id );
	$user_email = $user->user_email;
}

$options = get_option( 'somdn_pro_track_settings' );
$subscribe_option = isset( $options['somdn_capture_email_subscribe'] ) ? $options['somdn_capture_email_subscribe'] : 0 ;
$newsletter_general_options = get_option( 'somdn_pro_newsletter_general_settings' );

$email_title = isset( $options['somdn_capture_email_title'] ) ? $options['somdn_capture_email_title'] : '' ;
$email_body = isset( $options['somdn_capture_email_body'] ) ? $options['somdn_capture_email_body'] : '' ;
$email_placeholder = isset( $options['somdn_capture_email_placeholder'] ) ? $options['somdn_capture_email_placeholder'] : '' ;
$email_button = isset( $options['somdn_capture_email_button'] ) ? $options['somdn_capture_email_button'] : '' ;

$email_name_pl = isset( $newsletter_general_options['somdn_newsletter_fname_placeholder'] ) ? $newsletter_general_options['somdn_newsletter_fname_placeholder'] : '' ;

$email_lname = isset( $newsletter_general_options['somdn_newsletter_lname'] ) ? $newsletter_general_options['somdn_newsletter_lname'] : '' ;
$email_lname_pl = isset( $newsletter_general_options['somdn_newsletter_lname_placeholder'] ) ? $newsletter_general_options['somdn_newsletter_lname_placeholder'] : '' ;
$email_lname_req = isset( $newsletter_general_options['somdn_newsletter_lname_required'] ) ? $newsletter_general_options['somdn_newsletter_lname_required'] : '' ;

$email_tel = isset( $newsletter_general_options['somdn_newsletter_tel'] ) ? $newsletter_general_options['somdn_newsletter_tel'] : '' ;
$email_tel_pl = isset( $newsletter_general_options['somdn_newsletter_tel_placeholder'] ) ? $newsletter_general_options['somdn_newsletter_tel_placeholder'] : '' ;
$email_tel_req = isset( $newsletter_general_options['somdn_newsletter_tel_required'] ) ? $newsletter_general_options['somdn_newsletter_tel_required'] : '' ;

$email_company = isset( $newsletter_general_options['somdn_newsletter_company'] ) ? $newsletter_general_options['somdn_newsletter_company'] : '' ;
$email_company_pl = isset( $newsletter_general_options['somdn_newsletter_company_placeholder'] ) ? $newsletter_general_options['somdn_newsletter_company_placeholder'] : '' ;
$email_company_req = isset( $newsletter_general_options['somdn_newsletter_company_required'] ) ? $newsletter_general_options['somdn_newsletter_company_required'] : '' ;

$email_website = isset( $newsletter_general_options['somdn_newsletter_website'] ) ? $newsletter_general_options['somdn_newsletter_website'] : '' ;
$email_website_pl = isset( $newsletter_general_options['somdn_newsletter_website_placeholder'] ) ? $newsletter_general_options['somdn_newsletter_website_placeholder'] : '' ;
$email_website_req = isset( $newsletter_general_options['somdn_newsletter_website_required'] ) ? $newsletter_general_options['somdn_newsletter_website_required'] : '' ;

$newsletter_display = isset( $newsletter_general_options['somdn_newsletter_display_type'] ) ? $newsletter_general_options['somdn_newsletter_display_type'] : 0 ;
$sub_text = isset( $newsletter_general_options['somdn_newsletter_text'] ) ? $newsletter_general_options['somdn_newsletter_text'] : '' ;

if ( empty( $email_title ) ) {
	$email_title = __( 'Download FREE!', 'somdn-pro' );
} else {
	$email_title = esc_html( $email_title );
}

if ( empty( $sub_text ) ) {
	$sub_text = __( 'Subscribe to our newsletter', 'somdn-pro' );
} else {
	$sub_text = esc_html( $sub_text );
}	


if ( empty( $email_name_pl ) ) {
	$email_name_pl = __( 'Your first name...', 'somdn-pro' );
} else {
	$email_name_pl = esc_html( $email_name_pl );
}

if ( empty( $email_placeholder ) ) {
	$email_placeholder = __( 'Email address...', 'somdn-pro' );
} else {
	$email_placeholder = esc_html( $email_placeholder );
}

if ( ! empty( $email_lname ) ) {
	if ( empty( $email_lname_pl ) ) {
		$email_lname_pl = __( 'Your last name...', 'somdn-pro' );
	} else {
		$email_lname_pl = esc_html( $email_lname_pl );
	}
}

if ( ! empty( $email_tel ) ) {
	if ( empty( $email_tel_pl ) ) {
		$email_tel_pl = __( 'Your telephone number...', 'somdn-pro' );
	} else {
		$email_tel_pl = esc_html( $email_tel_pl );
	}
}

if ( ! empty( $email_company ) ) {
	if ( empty( $email_company_pl ) ) {
		$email_company_pl = __( 'Your company name...', 'somdn-pro' );
	} else {
		$email_company_pl = esc_html( $email_company_pl );
	}
}

if ( ! empty( $email_website ) ) {
	if ( empty( $email_website_pl ) ) {
		$email_website_pl = __( 'Your website...', 'somdn-pro' );
	} else {
		$email_website_pl = esc_html( $email_website_pl );
	}
}	

$product_name = get_the_title( $product_id );

$allowed_tags = somdn_get_allowed_html_tags();

if ( empty( $email_body ) ) {
	$product_name_wrap = '<strong>' . $product_name . '</strong>';
	$email_body = sprintf( __( 'To download %s today just enter your email address!', 'somdn-pro' ), $product_name_wrap );
	$email_body = wpautop( $email_body );
	$email_body = wpautop( wp_kses( $email_body, $allowed_tags ) );
} else {
	$email_body_str = str_replace( "{product}", $product_name, $email_body );
	$email_body = wpautop( wp_kses( $email_body_str, $allowed_tags ) );
}

$title_bg = isset( $options['somdn_capture_email_title_bg'] ) ? $options['somdn_capture_email_title_bg'] : '#2679ce' ;
$title_colour = isset( $options['somdn_capture_email_title_colour'] ) ? $options['somdn_capture_email_title_colour'] : '#fff' ;

if ( empty( $email_button ) ) {
	$email_button = __( 'Submit & Download', 'somdn-pro' );
} else {
	$email_button = esc_html( $email_button );
}

?>

<div class="somdn-capture-email-wrap">

	<div class="somdn-capture-email-wrap-bg"></div>

	<div class="somdn-capture-email-wrap-form-wrap">

		<div class="somdn-capture-email-wrap-form" style="background-color: <?php echo $title_bg; ?>!important;">

			<div class="somdn-capture-email-header">
				<h3 style="color: <?php echo $title_colour; ?>!important;"><?php echo $email_title; ?></h3>
				<span class="dashicons dashicons-no" style="color: <?php echo $title_colour; ?>!important;"></span>
			</div>

			<div class="somdn-capture-email-body">

				<div class="somdn-capture-email-left">

					<?php

						$src = '';

						if ( ! empty( $variation_id ) ) {
							$src = get_the_post_thumbnail_url( $variation_id );
						}

						if ( empty( $src ) ) {
							$src = get_the_post_thumbnail_url( $product_id );
						}

						if ( empty( $src ) ) {
							$src = get_the_post_thumbnail_url( $product_id );
						}

						if ( empty( $src ) ) {
							$src = wc_placeholder_img_src();
						}

						echo '<img class="somdn-capture-email-image" src="' . $src . '">';

					?>

				</div>

				<div class="somdn-capture-email-right">

					<?php do_action( 'somdn_capture_before_text_wrap', $current_user_id, $user_email ); ?>

					<div class="somdn-capture-email-text-wrap">
						<?php echo $email_body; ?>
					</div>

					<?php do_action( 'somdn_capture_before_email_wrap', $current_user_id, $user_email ); ?>

					<?php if ( ! empty( $subscribe_option ) ) { ?>

						<div class="somdn-capture-name-input-wrap">
							<input type="text" name="somdn_download_user_name" class="somdn-download-user-name somdn-capture-required" value="" placeholder="<?php echo $email_name_pl; ?>">
						</div>

						<?php if ( ! empty( $email_lname ) ) { ?>
		
							<div class="somdn-capture-name-input-wrap">
								<input type="text" name="somdn_download_user_lname" class="somdn-download-user-name<?php if ( ! empty( $email_lname_req ) ) echo ' somdn-capture-required'; ?>" value="" placeholder="<?php echo $email_lname_pl; ?>">
							</div>

						<?php } ?>

						<?php if ( ! empty( $email_tel ) ) { ?>
							<div class="somdn-capture-name-input-wrap">
								<input type="text" name="somdn_download_user_tel" class="somdn-download-user-name<?php if ( ! empty( $email_tel_req ) ) echo ' somdn-capture-required'; ?>" value="" placeholder="<?php echo $email_tel_pl; ?>">
							</div>
						<?php } ?>

						<?php if ( ! empty( $email_company ) ) { ?>
							<div class="somdn-capture-name-input-wrap">
								<input type="text" name="somdn_download_user_company" class="somdn-download-user-name<?php if ( ! empty( $email_company_req ) ) echo ' somdn-capture-required'; ?>" value="" placeholder="<?php echo $email_company_pl; ?>">
							</div>
						<?php } ?>

						<?php if ( ! empty( $email_website ) ) { ?>
							<div class="somdn-capture-name-input-wrap">
								<input type="text" name="somdn_download_user_website" class="somdn-download-user-name<?php if ( ! empty( $email_website_req ) ) echo ' somdn-capture-required'; ?>" value="" placeholder="<?php echo $email_website_pl; ?>">
							</div>
						<?php } ?>

					<?php } ?>

					<div class="somdn-capture-email-input-wrap">
						<input type="email" name="somdn_download_user_email" class="somdn-download-user-email somdn-capture-required" value="<?php echo $user_email; ?>" placeholder="<?php echo $email_placeholder; ?>">
					</div>

					<?php if ( ! empty( $subscribe_option ) ) { ?>

					<?php $subscribe_checked = empty( $newsletter_display ) ? false : true ; ?>

						<?php switch ( $newsletter_display ) {

							case 0:
								// Checkbox & Text ?>

								<div class="somdn-capture-name-input-wrap somdn-capture-checkbox-wrap">
									<label for="somdn_capture_email_subscribe_<?php echo $product_id; ?>">
										<input type="checkbox" name="somdn_capture_email_subscribe" id="somdn_capture_email_subscribe_<?php echo $product_id; ?>" value="1" class="somdn-checkbox-auto-blank">
										<span><?php echo $sub_text; ?></span>
									</label>
								</div>

								<?php break;

							case 3:
								// Required Checkbox & Text ?>

								<div class="somdn-capture-name-input-wrap somdn-capture-checkbox-wrap">
									<label for="somdn_capture_email_subscribe_<?php echo $product_id; ?>">
										<input type="checkbox" name="somdn_capture_email_subscribe" id="somdn_capture_email_subscribe_<?php echo $product_id; ?>" value="1" class="somdn-capture-required somdn-checkbox-auto-blank">
										<span><?php echo $sub_text; ?></span>
									</label>
								</div>

								<?php break;

							case 1:
								// Text Only ?>

								<div class="somdn-capture-name-input-wrap somdn-capture-checkbox-wrap">
									<input style="display: none;" type="checkbox" name="somdn_capture_email_subscribe" id="somdn_capture_email_subscribe" value="1" checked="checked">
									<p><?php echo $sub_text; ?></p>
								</div>

								<?php break;

							case 2:
								// No text shown ?>

								<div class="somdn-capture-name-input-wrap somdn-capture-checkbox-wrap" style="display: none;">
									<input style="display: none;" type="checkbox" name="somdn_capture_email_subscribe" id="somdn_capture_email_subscribe" value="1" checked="checked">
								</div>

								<?php break;

							default:
								// Checkbox & Text ?>

								<div class="somdn-capture-name-input-wrap somdn-capture-checkbox-wrap">
									<label for="somdn_capture_email_subscribe">
										<input type="checkbox" name="somdn_capture_email_subscribe" id="somdn_capture_email_subscribe" value="1">
										<span><?php echo $sub_text; ?></span>
									</label>
								</div>

								<?php break;
						} ?>

					<?php } ?>

					<?php do_action( 'somdn_capture_before_button_wrap', $current_user_id, $user_email ); ?>

					<div class="somdn-capture-email-button-wrap">
						<button class="button somdn-capture-email-button"><?php echo $email_button; ?></button>
					</div>

				</div>

			</div>

		</div>

	</div>

</div>