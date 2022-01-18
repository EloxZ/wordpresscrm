<?php

/**
 * GDPR Compliance Module
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Generates a localized retention notice message
 */
function wplc_gdpr_generate_retention_agreement_notice($wplc_settings = false,$replace_placeholders=true)
{
  if (!$wplc_settings) {
    $wplc_settings = TCXSettings::getSettings();
  }
  if (!empty($wplc_settings->wplc_gdpr_notice_text)) {
    $localized_notice = $wplc_settings->wplc_gdpr_notice_text;
  } else {
    $localized_notice = __("I agree that my personal data to be processed and for the use of cookies in order to engage in a chat processed by %%COMPANY%%, for the purpose of %%PURPOSE%%, for the time of %%PERIOD%% day(s) as per the GDPR.", 'wp-live-chat-support');

  }
	if($replace_placeholders) {
		$company_replacement = isset( $wplc_settings->wplc_gdpr_notice_company ) ? stripslashes( $wplc_settings->wplc_gdpr_notice_company ) : get_bloginfo( 'name' );
		$purpose_replacement = isset( $wplc_settings->wplc_gdpr_notice_retention_purpose ) ? $wplc_settings->wplc_gdpr_notice_retention_purpose : __( 'Chat/Support', 'wp-live-chat-support' );
		$period_replacement  = isset( $wplc_settings->wplc_gdpr_notice_retention_period ) ? intval( $wplc_settings->wplc_gdpr_notice_retention_period ) : 30;
		if ( $period_replacement < 1 ) {
			$period_replacement = 1;
		}
		if ( $period_replacement > 730 ) {
			$period_replacement = 730;
		}
		$localized_notice = str_replace( "%%COMPANY%%", $company_replacement, $localized_notice );
		$localized_notice = str_replace( "%%PURPOSE%%", $purpose_replacement, $localized_notice );
		$localized_notice = str_replace( "%%PERIOD%%", $period_replacement, $localized_notice );
	}
  $localized_notice = apply_filters('wplc_gdpr_retention_agreement_notice_filter', $localized_notice);
  return htmlentities($localized_notice);
}

add_filter('wplc_gdpr_create_opt_in_checkbox_filter', 'wplc_gdpr_add_wplc_privacy_notice', 10, 1);
/**
 * WPLC Compliance notice and link to policy
 */
function wplc_gdpr_add_wplc_privacy_notice($content)
{
  $wplc_settings = TCXSettings::getSettings();
  $localized_content = '';
  if ($wplc_settings->wplc_channel==='mcu') {
    $link = '<a href="https://www.3cx.com/wp-live-chat/privacy-policy/" target="_blank">' . __('Privacy Policy', 'wp-live-chat-support') . '</a>';
    $localized_content = __('We use 3CX Live Chat as our live chat platform. By clicking below to submit this form, you acknowledge that the information you provide now and during the chat will be transferred to WP Live Chat by 3CX for processing in accordance with their %%POLICY_LINK%%.', 'wp-live-chat-support');
    $localized_content = str_replace("%%POLICY_LINK%%", $link, htmlentities($localized_content));
  }
  $html = "<div class='wplc_gdpr_privacy_notice'>$localized_content</div>";
  return $content.$html;
}

