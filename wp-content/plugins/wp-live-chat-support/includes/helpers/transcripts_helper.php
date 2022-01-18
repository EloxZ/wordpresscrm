<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode('wplc_et_transcript', array( 'TCXTranscriptsHelper', 'wplc_transcript_get_transcript' ));
add_shortcode( 'wplc_et_transcript_footer_text', array( 'TCXTranscriptsHelper', 'wplc_transcript_get_footer_text' ) );
add_shortcode( 'wplc_et_transcript_header_text', array( 'TCXTranscriptsHelper', 'wplc_transcript_get_header_text' ) );

class TCXTranscriptsHelper {
	public static function wplc_transcript_get_transcript() {
		global $current_chat_id;

		if ( $current_chat_id > 0 ) {
			$chat_data = TCXChatHelper::get_chat_including_messages( $current_chat_id );

			$output_html =  TCXUtilsHelper::evaluate_php_template( WPLC_PLUGIN_DIR . "/includes/templates/transcript_body_tmpl.php", $chat_data );

			return $output_html;
		} else {
			return "0";
		}
	}


	public static function wplc_transcript_get_footer_text() {
		$wplc_settings = TCXSettings::getSettings();

		return html_entity_decode( stripslashes( $wplc_settings->wplc_et_email_footer ) );
	}


	public static function wplc_transcript_get_header_text() {
		global $wpdb;
		global $current_chat_id;
		$wplc_settings = TCXSettings::getSettings();

		$from_email = "Unknown@unknown.com";
		$from_name  = "User";
		if ( $current_chat_id > 0 ) {
			$chat_data = TCXChatData::get_chat( $wpdb, $current_chat_id );
			if ( isset( $chat_data->email ) ) {
				$from_email = $chat_data->email;
			}
			if ( isset( $chat_data->name ) ) {
				$from_name = $chat_data->name;
			}
		}

		return "<h3>" . $from_name . " (" . $from_email . ")" . "</h3>" . html_entity_decode( stripslashes( $wplc_settings->wplc_et_email_header ) );
	}

	public static function wplc_send_transcript( $cid ) {
		global $wpdb;
		global $current_chat_id;
		$current_chat_id = $cid;
		$wplc_settings   = TCXSettings::getSettings();
		$chat            = TCXChatData::get_chat( $wpdb, $current_chat_id );

		$body = html_entity_decode( stripslashes( $wplc_settings->wplc_et_email_body ) );
		$body = do_shortcode( $body );

		if ( $wplc_settings->wplc_send_transcripts_to === 'admin' ) {
			$user = get_user_by( 'id', $chat->agent_id );
			if(is_object($user)) {
				$to = $user->user_email;
			}else
			{
				$to =get_option('admin_email');
			}
		} else {
			if($wplc_settings->wplc_require_user_info === 'both' || $wplc_settings->wplc_require_user_info === 'email') {
				$to = $chat->email;
			}
			else
			{
				//No email to send.
				return true;
			}
		}

		$subject = sprintf( __( 'Your chat transcript from %1$s', 'wp-live-chat-support' ), get_bloginfo( 'url' ) );
		$headers = 'From: '.$wplc_settings->wplc_pro_chat_email_address . "\r\n" .
		           'Reply-To: '.$wplc_settings->wplc_pro_chat_email_address . "\r\n" .
		           'content-type: text/html' . "\r\n" .
		           'X-Mailer: PHP/' . phpversion();
		return wp_mail( $to, $subject, $body, $headers );

	}
}