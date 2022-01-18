<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class TCXOfflineMessagesHelper {
	public static function module_db_integration() {
		global $wplc_tblname_offline_msgs;
		$sql = "
        CREATE TABLE `" . $wplc_tblname_offline_msgs . "` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `timestamp` datetime NOT NULL,
          `name` varchar(700) NOT NULL,
          `email` varchar(700) NOT NULL,
          `phone` varchar(50) NOT NULL,
          `message` varchar(700) NOT NULL,
          `ip` varchar(700) NOT NULL,
          `user_agent` varchar(700) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
    ";
		dbDelta( $sql );

	}

	public static function generate_offline_respond_html( $mail_body, $wplc_user_name, $wplc_user_email, $mail_subject ) {
		preg_match_all( '/{(.*?)}/', $mail_body, $matches );

		if ( isset( $matches[1] ) ) {

			foreach ( $matches[1] as $key => $match ) {

				if ( $mail_body ) {

					if ( $matches[1][ $key ] == 'wplc-user-name' ) {

						$mail_body = str_replace( $matches[0][ $key ], $wplc_user_name, $mail_body );

					} else if ( $matches[1][ $key ] == 'wplc-email-address' ) {

						$mail_body = str_replace( $matches[0][ $key ], $wplc_user_email, $mail_body );

					}

				}

			}
		}
		$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <body>
    <table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: sans-serif;">
      <tbody>
        <tr>
          <td width="100%" style="padding: 30px 20px 100px 20px;">
            <table cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate;">
              <tbody>
                <tr>
                  <td style="padding-bottom: 20px;">
                    <p>' . $mail_subject . '</p>
                    <hr>
                  </td>
                </tr>
              </tbody>
            </table>
            <table id="" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; font-size: 14px;">
              <tbody>
                <tr>
                  <td class="sortable-list ui-sortable">' . nl2br( $mail_body ) . '</td>
                </tr>
              </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; max-width:100%;">
              <tbody>
                <tr>
                  <td style="padding-top:20px;">
                    <table border="0" cellpadding="0" cellspacing="0" class="" width="100%">
                      <tbody>
                        <tr>
                          <td id=""><p>' . site_url() . '</p></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>';

		return $body;
	}

	public static function send_offline_message_autorespond( $wplc_user_name,$wplc_user_email ) {

		$wplc_settings = TCXSettings::getSettings();

		if ( $wplc_settings->wplc_autorespond_settings['wplc_ar_enable'] ) {
			$mail_html = self::generate_offline_respond_html( $wplc_settings->wplc_autorespond_settings['wplc_ar_body'],
				$wplc_user_name,
				$wplc_user_email,
				$wplc_settings->wplc_autorespond_settings['wplc_ar_subject'] );

			$headers[] = 'Content-type: text/html';
			if ( ! empty( $wplc_settings->wplc_autorespond_settings['wplc_ar_from_name'] ) && ! empty( $wplc_settings->wplc_autorespond_settings['wplc_ar_from_email'] ) ) {
				$headers[] = 'Reply-To: ' . $wplc_settings->wplc_autorespond_settings['wplc_ar_from_name'] . ' <' . $wplc_settings->wplc_autorespond_settings['wplc_ar_from_email'] . '>';
			}
			if ( ! wp_mail( $wplc_user_email, $wplc_settings->wplc_autorespond_settings['wplc_ar_subject'], $mail_html, $headers ) ) {
				$error = date( "Y-m-d H:i:s" ) . " WP-Mail Failed to send \n";
				error_log( $error );
			}

		}
		return;
	}

	public static function send_offline_notification_mail( $wplc_user_name,$wplc_user_email,$wplc_user_phone,$wplc_offline_message ) {

		$wplc_settings = TCXSettings::getSettings();

		if (isset($wplc_settings->wplc_pro_chat_email_address)) {
			$email_address = $wplc_settings->wplc_pro_chat_email_address;
		} else {
			$email_address = get_option('admin_email');
		}
		$email_addresses = preg_split('(;|,)', $email_address);

		$subject = __("3CX Live Chat - Offline Message from ", 'wp-live-chat-support');
		if ( isset( $wplc_settings->wplc_pro_chat_email_offline_subject) ) {
			$subject = stripslashes( $wplc_settings->wplc_pro_chat_email_offline_subject);
		}

		$subject = $subject.' [ '.$wplc_user_name.' ] ';

		$message = __("Name", 'wp-live-chat-support').": $wplc_user_name \n".
		           __("Email", 'wp-live-chat-support').": $wplc_user_email\n".
		           __("Phone", 'wp-live-chat-support').": $wplc_user_phone \n".
		           __("Message", 'wp-live-chat-support').": $wplc_offline_message\n\n".
		           __("Via 3CX Live Chat", 'wp-live-chat-support');


		$mail_html = self::generate_offline_respond_html( $message,
			$wplc_user_name,
			$wplc_user_email,
			$subject );

		$headers[] = 'Content-type: text/html';
		$headers[] = 'Reply-To: '.$wplc_user_name.'<'.$wplc_user_email.'>';
		if ($email_addresses) {
			foreach($email_addresses as $email) {
				if (!wp_mail($email, $subject, $mail_html, $headers)) {
					$error = date("Y-m-d H:i:s") . " WP-Mail Failed to send \n";
					error_log($error);
				}
			}
		}

		return;
	}


}