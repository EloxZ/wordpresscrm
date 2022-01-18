<?php


class TCXWebhookHelper {

	public static function send_webhook( $event_code, $payload ) {
		global $wpdb;
		$wplc_webhook_events = TCXWebhookHelper::getWebhookActionsDictionary();
		$error_found = true;

		if ( isset( $event_code ) && isset( $payload ) ) {
			$event_code = intval( $event_code );
			if ( array_key_exists( $event_code, $wplc_webhook_events ) ) {
				$matches = TCXWebhooksData::get_webhook_by_event( $wpdb, intval( $event_code ) );
				if ( $matches !== false ) {
					//fire off the hooks
					foreach ( $matches as $webhook ) {
						$target_url = isset( $webhook->url ) ? $webhook->url : false;
						$method     = isset( $webhook->method ) && $webhook->method === "GET" ? "GET" : "POST";

						if ( ! is_array( $payload ) ) {
							$payload = array( "other" => $payload );
						}

						$payload = array(
							"event"     => $wplc_webhook_events[ intval( $event_code ) ],
							"data"      => json_encode( $payload ),
							"time_sent" => time()
						);

						if ( $target_url !== false && $target_url !== "" ) {
							if ( $method === "POST" ) {
								/** Replaced with WP HTTP API Calls */

								$response = wp_remote_post(
									$target_url,
									array(
										'method'      => 'POST',
										'timeout'     => 45,
										'redirection' => 5,
										'httpversion' => '1.0',
										'blocking'    => true,
										'headers'     => array(),
										'body'        => $payload,
										'cookies'     => array()
									)
								);

								if ( !is_wp_error( $response ) ) {
									$error_found = false;
								}
							} else {
								/** Replaced with WP HTTP API Calls */

								$get_data = http_build_query( $payload );
								$response = wp_remote_get( $target_url . "?" . $get_data );
								if ( !is_wp_error( $response ) ) {
									$error_found = false;
								}
							}
						}
					}
				}
			}
		}
		if($error_found)
		{
			//TODO: Handle errors, maybe some database logging here.
		}
	}

	public static function getWebhookActionsDictionary()
	{
		return array(
			WebHookTypes::AGENT_LOGIN => __("Agent Login", 'wp-live-chat-support'),
			WebHookTypes::NEW_VISITOR => __("New Visitor", 'wp-live-chat-support'),
			WebHookTypes::CHAT_REQUEST => __("Chat Request", 'wp-live-chat-support'),
			WebHookTypes::AGENT_ACCEPT => __("Agent Accept", 'wp-live-chat-support'),
			WebHookTypes::SETTINGS_CHANGED => __("Settings Changed", 'wp-live-chat-support')
		);
	}


	public static function module_db_integration()
	{
		global $wplc_webhooks_table;
		$sql = "
        CREATE TABLE `" . $wplc_webhooks_table . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `url` varchar(700) NULL,
            `action` int(11) NULL, 
            `method` varchar(70) NULL,
            PRIMARY KEY  (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
      ";

		dbDelta($sql);
	}



}