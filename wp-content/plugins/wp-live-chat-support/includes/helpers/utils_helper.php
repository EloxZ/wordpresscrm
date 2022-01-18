<?php

class TCXUtilsHelper {

	public static function get_browser_image( $string, $size ) {
		switch ( $string ) {
			case "Internet Explorer":
				return "internet-explorer_" . $size . "x" . $size . ".png";
				break;
			case "Mozilla Firefox":
				return "firefox_" . $size . "x" . $size . ".png";
				break;
			case "Opera":
				return "opera_" . $size . "x" . $size . ".png";
				break;
			case "Google Chrome":
				return "chrome_" . $size . "x" . $size . ".png";
				break;
			case "Safari":
				return "safari_" . $size . "x" . $size . ".png";
				break;
			case "Other browser":
				return "web_" . $size . "x" . $size . ".png";
				break;
			default:
				return "web_" . $size . "x" . $size . ".png";
				break;
		}
	}

	public static function get_browser_string( $user_agent ) {
		if ( strpos( $user_agent, 'MSIE' ) !== false ) {
			return 'Internet Explorer';
		} elseif ( strpos( $user_agent, 'Trident' ) !== false ) //For Supporting IE 11
		{
			return 'Internet Explorer';
		} elseif ( strpos( $user_agent, 'Edge' ) !== false ) {
			return 'Internet Explorer';
		} elseif ( strpos( $user_agent, 'Firefox' ) !== false ) {
			return 'Mozilla Firefox';
		} elseif ( strpos( $user_agent, 'Chrome' ) !== false ) {
			return 'Google Chrome';
		} elseif ( strpos( $user_agent, 'Opera Mini' ) !== false ) {
			return "Opera";
		} elseif ( strpos( $user_agent, 'Opera' ) !== false ) {
			return "Opera";
		} elseif ( strpos( $user_agent, 'Safari' ) !== false ) {
			return "Safari";
		} else {
			return 'Other browser';
		}
	}

	public static function generate_encryption_key() {
		return md5( mt_rand() ) . md5( mt_rand() );
	}

	public static function that_or_default_setting( $that, $default_setting ) {
		$result = trim( $that );
		if ( empty( $result ) ) {
			$result = TCXSettings::getSettings()->$default_setting;
		}

		return $result;
	}

	public static function get_times_array() {
		$hours = array();
		for ( $i = 0; $i <= 23; $i ++ ) {
			$hours[] = sprintf( '%02d', $i );
		}
		$minutes = array();
		for ( $i = 0; $i <= 59; $i ++ ) {
			$minutes[] = sprintf( '%02d', $i );
		}
		$time = array(
			'hours'   => $hours,
			'minutes' => $minutes
		);

		return $time;
	}

	public static function get_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			$ip = "0";
		}

		return sanitize_text_field( $ip );
	}

	public static function filter_data_map_array( $data_map, $properties ) {
		$data = array_filter( $data_map, function ( $field, $key ) use ( $properties ) {
			if ( is_array( $properties ) ) {
				return in_array( $key, array_keys( $properties ) );
			} else {
				return $key == $properties;
			}
		}, ARRAY_FILTER_USE_BOTH );

		$result = array_map( function ( $field ) {
			return $field['format'];
		}, $data );

		if ( is_array( $properties ) ) {
			$ordered_result = array();
			foreach ( array_keys( $properties ) as $value ) {
				if ( array_key_exists( $value, $result ) ) {
					$ordered_result[ $value ] = $result[ $value ];
				}
			}

			return $ordered_result;
		} else {
			return $result;
		}
	}

	public static function update_stats( $sec ) {
		$wplc_stats = get_option( "wplc_stats" );
		if ( $wplc_stats ) {
			if ( isset( $wplc_stats[ $sec ]["views"] ) ) {
				$wplc_stats[ $sec ]["views"]         = $wplc_stats[ $sec ]["views"] + 1;
				$wplc_stats[ $sec ]["last_accessed"] = date( "Y-m-d H:i:s" );
			} else {
				$wplc_stats[ $sec ]["views"]          = 1;
				$wplc_stats[ $sec ]["last_accessed"]  = date( "Y-m-d H:i:s" );
				$wplc_stats[ $sec ]["first_accessed"] = date( "Y-m-d H:i:s" );
			}

		} else {

			$wplc_stats[ $sec ]["views"]          = 1;
			$wplc_stats[ $sec ]["last_accessed"]  = date( "Y-m-d H:i:s" );
			$wplc_stats[ $sec ]["first_accessed"] = date( "Y-m-d H:i:s" );

		}
		update_option( "wplc_stats", $wplc_stats );

	}

	public static function check_page_action( $page, $action = null ) {
		return ( isset( $_GET["page"] )
		         && $_GET["page"] == $page
		         && ( $action == null || (
					isset( $_GET["wplc_action"] ) && $_GET["wplc_action"] == $action
				)
		         )
		);
	}

	public static function generate_csv( $data, $return_stream = true ) {
		if ( is_object( $data ) ) {
			$data_array = TCXUtilsHelper::convertToArray( $data );
		} else {
			$data_array = $data;
		}

		$csv_memory_stream = fopen( 'php://output', 'rw' );
		fputcsv( $csv_memory_stream, array_keys( current( $data_array ) ) );

		foreach ( $data_array as $row ) {
			fputcsv( $csv_memory_stream, $row );
		}

		if ( $return_stream ) {
			return $csv_memory_stream;
		} else {
			rewind( $csv_memory_stream );
			$csv = stream_get_contents( $csv_memory_stream );
			fclose( $csv_memory_stream );

			return $csv;
		}
	}

	public static function convertToArray( $object ) {
		$result = is_array( $object ) ? $object : array();
		if ( is_object( $object ) ) {
			foreach ( $object as $key => $value ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	public static function try_base64_decode( $data, &$output ) {
		$output = base64_decode( $data );
		if ( base64_encode( $output ) === $data ) {
			return true;
		} else {
			$output = null;

			return false;
		}
	}

	public static function try_json_decode( $string, &$output ) {
		$output = json_decode( $string );
		if ( json_last_error() == JSON_ERROR_NONE ) {
			return true;
		} else {
			$output = TCXUtilsHelper::wplc_json_decode( $string );
			if ( json_last_error() == JSON_ERROR_NONE ) {
				return true;
			} else {
				$output = null;

				return false;
			}
		}
	}

	public static function setup_polling( $wplc_settings ) {
		$iterations = $wplc_settings->wplc_iterations;

		/* time in microseconds between long poll loop (lower number = higher resource usage) */
		define( 'WPLC_DELAY_BETWEEN_LOOPS', $wplc_settings->wplc_delay_between_loops * 1000 );
		/* this needs to take into account the previous constants so that we dont run out of time, which in turn returns a 503 error */
		define( 'WPLC_TIMEOUT', ( ( WPLC_DELAY_BETWEEN_LOOPS * $iterations ) / 1000000 ) * 2 );

		/* we're using PHP 'sleep' which may lock other requests until our script wakes up. Call this function to ensure that other requests can run without waiting for us to finish */
		session_write_close();

		if ( defined( 'WPLC_TIMEOUT' ) ) {
			@set_time_limit( WPLC_TIMEOUT );
		} else {
			@set_time_limit( 120 );
		}

		return $iterations;
	}

	public static function generateRandomString( $length = 10, $useLetters = true ) {
		$characters       = '0123456789';
		$characters       = $characters . ( $useLetters ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '' );
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}

	public static function current_mysql_time_with_ms() {

		$type = 'Y-m-d H:i:s.u';

		$datetime = current_time( $type, true );

		return $datetime;
	}

	public static function evaluate_php_template( $path, $args ) {
		foreach ( $args as $key => $value ) {
			${$key} = $value;
		}

		ob_start();
		include( $path );
		$var = ob_get_contents();
		ob_end_clean();

		return $var;
	}

	public static function node_server_token_get( $reset = false ) {
		$tk = '';
		if ( ! $reset ) {
			$tk = get_option( "wplc_node_server_secret_token" );
		}
		if ( empty( $tk ) ) {
			$tk = self::node_server_token_create();
			update_option( "wplc_node_server_secret_token", $tk );
		}

		return $tk;
	}

	public static function node_server_token_create() {
		$the_code = rand( 0, 1000 ) . rand( 0, 1000 ) . rand( 0, 1000 ) . rand( 0, 1000 ) . rand( 0, 1000 );
		$the_time = time();
		$token    = md5( $the_code . $the_time );

		return $token;
	}

	public static function wplc_check_chatbox_enabled_business_hours() {
		$result        = false;
		$wplc_settings = TCXSettings::getSettings();
		if ( $wplc_settings->wplc_bh_enable ) {
			$now_wp        = current_time( 'timestamp' ); // unix timestamp adjusted to wp timezone, considering also DST
			$now           = ( new DateTime( "now", new DateTimeZone( "UTC" ) ) )->getTimestamp(); // for sure UTC, no DST, bypassing PHP timezone configuration
			$skew          = $now - $now_wp; // difference from wordpress time and UTC
			$now_dayofweek = gmdate( 'w', $now_wp );
			$now_day       = gmdate( 'd', $now_wp );
			$now_month     = gmdate( 'm', $now_wp );
			$now_year      = gmdate( 'Y', $now_wp );

			// calculate time in UTC then add skew, so comparison is between UTC timestamps
			if ( array_key_exists( $now_dayofweek, $wplc_settings->wplc_bh_schedule ) && is_array( $wplc_settings->wplc_bh_schedule[ $now_dayofweek ] ) ) {
				foreach ( $wplc_settings->wplc_bh_schedule[ $now_dayofweek ] as $schedule ) {
					$t1 = $skew + gmmktime( $schedule['from']['h'], $schedule['from']['m'], 0, $now_month, $now_day, $now_year );
					$t2 = $skew + gmmktime( $schedule['to']['h'], $schedule['to']['m'], 59, $now_month, $now_day, $now_year );
					if ( $now >= $t1 && $now <= $t2 ) {
						$result = true;
						break;
					}
				}
			}

		} else {
			$result = true;
		}

		return $result;
	}

	public static function wplc_add_jquery_validation() {
		global $wplc_base_file;
		wp_register_script( 'wplc_jquery_validation', wplc_plugins_url( '/js/vendor/jquery-validation/jquery.validate.min.js', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc_jquery_validation' );

		wp_register_script( 'wplc_jquery_validation_additional_methods', wplc_plugins_url( '/js/vendor/jquery-validation/additional-methods.min.js', $wplc_base_file ), array( 'jquery_validation' ), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc_jquery_validation_additional_methods' );

		if ( file_exists( plugin_dir_path( $wplc_base_file ) . 'js/vendor/jquery-validation/localization/messages_' . get_locale() . '.min.js' ) ) {
			wp_register_script( 'wplc_jquery_validation_language_pack', wplc_plugins_url( '/js/vendor/jquery-validation/localization/messages_' . get_locale() . '.min.js', $wplc_base_file ), array( 'jquery_validation' ), WPLC_PLUGIN_VERSION, true );
			wp_enqueue_script( 'wplc_jquery_validation_language_pack' );
		}

		wp_register_style( 'wplc_jquery_validation_css', wplc_plugins_url( '/css/wplc_validator_styles.css', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
		wp_enqueue_style( 'wplc_jquery_validation_css' );

	}

	public static function wplc_is_user_banned() {
		$wplc_settings = TCXSettings::getSettings();
		$banned        = false;
		if ( ! empty( $wplc_settings->wplc_banned_ips ) ) {
			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) {
				$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip_address = $_SERVER['REMOTE_ADDR'];
			}

			$banned = in_array( $ip_address, $wplc_settings->wplc_banned_ips );
		}

		return $banned;
	}

	public static function get_client_dictionary() {
		$result                                      = new stdClass();
		$wplc_settings                               = TCXSettings::getSettings();
        self::set_dictionary_if_not_empty($result,"OfflineFormTitle",$wplc_settings->wplc_pro_na);
        self::set_dictionary_if_not_empty($result,"OfflineFormFinishMessage",$wplc_settings->wplc_offline_finish_message);
        self::set_dictionary_if_not_empty($result,"OfflineFormEmailMessage",$wplc_settings->wplc_offline_email_message);
        self::set_dictionary_if_not_empty($result,"OfflineFormNameMessage",$wplc_settings->wplc_offline_name_message);
        self::set_dictionary_if_not_empty($result,"OfflineFormMaximumCharactersReached",$wplc_settings->wplc_offline_length_error);
        self::set_dictionary_if_not_empty($result,"OfflineFormInvalidEmail",$wplc_settings->wplc_offline_email_invalid);
        self::set_dictionary_if_not_empty($result,"OfflineFormInvalidName",$wplc_settings->wplc_offline_name_invalid);
		$result->RateMessage                         = $wplc_settings->wplc_rate_message;
		$result->RateCommentsMessage                 = $wplc_settings->wplc_rate_comments_message;
		$result->RateFeedbackRequestMessage          = $wplc_settings->wplc_rate_feedback_request_message;
		$result->AuthFieldsReplacement               = $wplc_settings->wplc_user_alternative_text;
		if ( false ) { //postponed for later version
			$result->AuthFormNameMessage  = $wplc_settings->wplc_auth_name_message;
			$result->AuthFormEmailMessage = $wplc_settings->wplc_auth_email_message;
		}
		$result->FirstResponse                       = $wplc_settings->wplc_pro_auto_first_response_chat_msg;
		$result->ChatTitle                           = $wplc_settings->wplc_chat_title;
		$result->ChatIntro                           = $wplc_settings->wplc_chat_intro;
		$result->StartButtonText                     = $wplc_settings->wplc_button_start_text;
		$result->ChatWelcomeMessage                  = $wplc_settings->wplc_welcome_msg;
		$result->ChatNoAnswerMessage                 = $wplc_settings->wplc_user_no_answer;
		$result->InactivityMessage                   = 'Chat session closed due to inactivity. Try again later.';
		$result->ChatEndMessage                      = $wplc_settings->wplc_text_chat_ended;
		$result->GreetingMessage                     = $wplc_settings->wplc_greeting_message;
		$result->GreetingOfflineMessage              = $wplc_settings->wplc_offline_greeting_message;
		$result->CallTitle                           = $wplc_settings->wplc_call_title;

		return $result;
	}

	private static function set_dictionary_if_not_empty(&$dict, $key,$value){
	    if(!empty($value))
        {
            $dict->$key = $value;
        }
    }

	public static function wplc_geo_ip() {
		$wplc_settings  = TCXSettings::getSettings();
		$country        = new stdClass();
		$country->name  = '';
		$country->code  = '';
		$country->image = '';
		if ( $wplc_settings->wplc_use_geolocalization && function_exists( 'geoip_detect2_get_info_from_current_ip' ) ) {
			$record = geoip_detect2_get_info_from_current_ip(); //geoip_detect2_get_info_from_ip( '4.4.4.4', null );
			if ( $record && $record->country ) {
				$country->code = strtolower( $record->country->isoCode );
				$country->name = $record->country->names['en'] != null ? $record->country->names['en'] : '';
				if ( $country->name !== '' ) {
					$country->image = WPLC_PLUGIN_URL . "images/flags/" . $country->code . ".png";
					if ( $record->city ) {
						$country->name .= ' - ' . $record->city->names['en'];
					}
				}
			}
		}
		return $country;
	}

	public static function wplc_check_http_data( $key ) {
		$result = null;
		if ( isset( $_GET[ $key ] ) ) {
			$result = $_GET[ $key ];
		} else if ( isset( $_POST[ $key ] ) ) {
			$result = $_POST[ $key ];
		}

		return $result;

	}

	public static function wplc_get_pager( $data_query, $rows_per_page = 20 ) {
		global $wpdb;
		$data_query           = preg_replace( "/(?<=select)(.*?(?![^(]*\)))(?=from)/i", ' count(*) ', $data_query );
		$pager                = new TCXPager();
		$pager->rows_per_page = $rows_per_page; // number of rows per page
		$pager->current_page  = isset( $_GET['pagenum'] ) && intval( $_GET['pagenum'] ) > 0 ? absint( $_GET['pagenum'] ) : 1;
		$pager->offset        = ( $pager->current_page - 1 ) * $pager->rows_per_page;
		$pager->total_rows    = $wpdb->get_var( $data_query );
		$pager->pages_counter = ceil( $pager->total_rows / $pager->rows_per_page );

		return $pager;
	}

	public static function wplc_include_chat_on_page( $page_id, $include_on_pages, $exclude_from_pages ) {
		$result = true;

		if ( in_array( $page_id, $exclude_from_pages ) ) {
			$result = false;
		} else if ( ! empty( $include_on_pages ) && ! in_array( $page_id, $include_on_pages ) ) {
			$result = false;
		}

		return $result;
	}

	public static function wplc_include_chat_on_post_type( $post_type, $exclude_from_post_types ) {
		$result = true;

		if ( ! empty( $exclude_from_post_types ) && in_array( $post_type, $exclude_from_post_types ) ) {
			$result = false;
		}

		return $result;
	}

	public static function wplc_show_chat_client() {
		$result            = true;
		$wplc_settings     = TCXSettings::getSettings();
		$current_view_id   = get_the_ID();
		$current_post_type = get_post_type( $current_view_id );

		$include_on_pages = array();
		if ( strlen( $wplc_settings->wplc_include_on_pages ) > 0 ) {
			$include_on_pages = explode( ',', $wplc_settings->wplc_include_on_pages );
		}

		$exclude_from_pages = array();
		if ( strlen( $wplc_settings->wplc_exclude_from_pages ) > 0 ) {
			$exclude_from_pages = explode( ',', $wplc_settings->wplc_exclude_from_pages );
		}
		$wplc_compatibility = wplc_check_version_compatibility();
		if ( is_admin() ||
		     wp_doing_ajax() ||
		     ( $wplc_settings->wplc_exclude_home && ( is_home() || is_front_page() || $_SERVER['REQUEST_URI'] == '/' ) ) ||
		     ( $wplc_settings->wplc_exclude_archive && is_archive() ) ||
		     ! self::wplc_include_chat_on_page( $current_view_id, $include_on_pages, $exclude_from_pages ) ||
		     ! self::wplc_include_chat_on_post_type( $current_post_type, $wplc_settings->wplc_exclude_post_types ) ||
		     // ( $wplc_settings->wplc_hide_when_offline && ! TCXAgentsHelper::exist_available_agent() ) ||
		     (TCXUtilsHelper::wplc_is_user_banned() && $wplc_settings->wplc_channel!='phone')||
		     ! $wplc_compatibility->wp ||
		     ! $wplc_compatibility->php ||
		     ! $wplc_compatibility->ie
		) {
			$result = false;
		}

		return $result;
	}

	public static function wplc_color_by_string( $str ) {
		$code = dechex( crc32( $str ) );
		$code = substr( $code, 0, 6 );

		return $code;
	}

	public static function wplc_parse_click2callUrl( $url ) {
		$result                = array();
		$c2c_url               = parse_url( esc_url_raw( $url ) );
		$result['chat_party']  = array_key_exists( 'fragment', $c2c_url ) ? $c2c_url['fragment'] : '';
		$result['channel_url'] = ( array_key_exists( 'scheme', $c2c_url ) ? $c2c_url['scheme'] : '' ) . "://" . $c2c_url['host'] . ( array_key_exists( 'port', $c2c_url ) ? ":" . $c2c_url['port'] : '' ) . ( array_key_exists( 'path', $c2c_url ) ? $c2c_url['path'] : '' );
		$result['files_url']   = ( array_key_exists( 'scheme', $c2c_url ) ? $c2c_url['scheme'] : '' ) . "://" . $c2c_url['host'] . ( array_key_exists( 'port', $c2c_url ) ? ":" . $c2c_url['port'] : '' );

		return $result;
	}

	public static function wplc_parse_pbx_mode( $mode ) {
		$result = array();
		switch ( $mode ) {
			case 'all':
				$result['call']  = true;
				$result['chat']  = true;
				$result['video'] = true;
				break;
			case 'videochat':
				$result['call']  = false;
				$result['chat']  = true;
				$result['video'] = true;
				break;
			case 'phonechat':
				$result['call']  = true;
				$result['chat']  = true;
				$result['video'] = false;
				break;
			case 'chat':
				$result['call']  = false;
				$result['chat']  = true;
				$result['video'] = false;
				break;
			case 'phone':
				$result['call']  = true;
				$result['chat']  = false;
				$result['video'] = false;
				break;
		}

		return $result;
	}

	public static function wplc_set_pbx_mode_settings( $call, $chat, $video ) {
		TCXSettings::setSettingValue( 'wplc_allow_chat', $chat );
		TCXSettings::setSettingValue( 'wplc_allow_call', $call );
		TCXSettings::setSettingValue( 'wplc_allow_video', $video );
	}

	public static function get_mcu_data( $wplc_socket_url, $wplc_chat_server_session, $force_update = false, $force_reset = false ) {
		$wplc_settings = TCXSettings::getSettings();
		$guid          = get_option( 'WPLC_GUID' );
		$force_update  = $force_update || $force_reset ||
		                 WPLC_CHAT_SERVER != $wplc_settings->wplc_cluster_manager_route_server ||
		                 empty( $wplc_settings->wplc_socket_url ) ||
		                 empty( $wplc_settings->wplc_chat_server_session ) ||
		                 empty( $guid );

		$result = array(
			"socket_url"          => '',
			"chat_server_session" => ''
		);
		if ( $wplc_settings->wplc_channel === 'mcu' ) {
			$result = array(
				"socket_url"          => $wplc_socket_url,
				"chat_server_session" => $wplc_chat_server_session
			);

			if ( $force_update ) {
				wplc_check_guid( true );
				$guid   = get_option( 'WPLC_GUID' );
				$result = self::wplc_get_mcu_data_from_cm( $guid, true, $force_reset );
			}
		}

		return $result;
	}

	public static function wplc_get_mcu_data_from_cm( $guid, $return_result = false, $force_reset = false ) {
		$result   = array(
			"socket_url"          => '',
			"chat_server_session" => ''
		);
		$version  = $force_reset ? '0.0.0.0' : WPLC_PLUGIN_VERSION;
		$response = wp_remote_get( WPLC_CHAT_SERVER . '?website=' . get_option( 'siteurl' ) . '&guid=' . $guid . '&pluginversion=' . $version );
		if ( is_array( $response ) ) {
			if ( $response['response']['code'] == "200" ) {
				$data = json_decode( $response['body'], true );
				if ( $data && isset( $data['result'] ) ) {
					if ( $data['result'] && isset( $data['chatServer'] ) ) {
						TCXSettings::setSettingValue( "wplc_cluster_manager_route_server", WPLC_CHAT_SERVER );
						TCXSettings::setSettingValue( "wplc_socket_url", 'wss://' . $data['chatServer'] );
						TCXSettings::setSettingValue( "wplc_chat_server_session", $data['sessionId'] );
						if ( $return_result ) {
							$result["socket_url"]          = 'wss://' . $data['chatServer'];
							$result["chat_server_session"] = $data['sessionId'];
						}
						update_option( 'WPLC_CM_SESSION_CHECK', time() );
						update_option( 'WPLC_NO_SERVER_MATCH', false );
						if ( $force_reset ) {
							$result = self::wplc_get_mcu_data_from_cm( $guid, $return_result );
						}
					} else if ( ! $data['result'] && isset( $data['errorCode'] ) ) {
						switch ( intval( $data['errorCode'] ) ) {
							/*ERROR_GUID_NOT_FOUND = 20000*/
							/*ERROR_URL_MISMATCH = 20001*/
							/*ERROR_NO_SERVER_AVAILABLE = 20002*/
							/*ERROR_BOT_DETECTED = 20003*/
							case 20000:
							case 20001:
								self::clean_chat_activation();
								break;
							case 20002:
								self::clean_chat_activation();
								update_option( 'WPLC_NO_SERVER_MATCH', true );
								break;
							case 20004:
								break;
							default:
								self::clean_chat_activation();
								break;
						}
					} else {
						self::clean_chat_activation();
					}
				}
			}
		}
		if ( $return_result ) {
			return $result;
		} else {
			return true;
		}
	}

	public static function clean_chat_activation() {
		update_option( 'WPLC_GUID', '' );
		TCXSettings::setSettingValue( "wplc_socket_url", '' );
		TCXSettings::setSettingValue( "wplc_chat_server_session", '' );
	}

	public static function wplc_load_chat_js_data( $is_chat_page = false ) {
		global $wpdb;
		$wplc_current_user     = wp_get_current_user();
		$wplc_settings         = TCXSettings::getSettings();
		$wplc_chat_server_data = TCXUtilsHelper::get_mcu_data( $wplc_settings->wplc_socket_url, $wplc_settings->wplc_chat_server_session );
		$script_data           = array(
			'remove_agent'            => __( 'Remove', 'wp-live-chat-support' ),
			'nonce'                   => wp_create_nonce( "wplc" ),
			'user_id'                 => $wplc_current_user->ID,
			'agent_name'              => $wplc_settings->wplc_show_agent_name ? $wplc_current_user->display_name : $wplc_settings->wplc_agent_default_name,
			'agent_email'             => $wplc_settings->wplc_show_agent_name ? $wplc_current_user->user_email : 'NoEmail',
			'agent_department'        => get_user_meta( $wplc_current_user->ID, 'wplc_user_department', true ),
			'typing_string'           => __( 'Typing...', 'wp-live-chat-support' ),
			'accepting_status'        => __( 'Status (Online)', 'wp-live-chat-support' ),
			'accepting_chats'         => __( 'Online', 'wp-live-chat-support' ),
			'not_accepting_chats'     => __( 'Offline', 'wp-live-chat-support' ),
			'not_accepting_status'    => __( 'Status (Offline)', 'wp-live-chat-support' ),
			'agent_online_singular'   => __( 'Chat Agent Online', 'wp-live-chat-support' ),
			'agent_online_plural'     => __( 'Chat Agents Online', 'wp-live-chat-support' ),
			'offline_message'         => "<p><span class='offline-status'>" . __( "You have set your status to offline. To view visitors and accept chats please set your status to online using the switch on the top admin bar.", 'wp-live-chat-support' ) . "</span></p>",
			'agent_accepts_data'      => TCXAgentsHelper::is_agent_accepting( $wplc_current_user->ID ),
			'extra_data'              => array( 'agent_id' => $wplc_current_user->ID ),
			'ringtone'                => TCXRingtonesHelper::get_ringtone_url( $wplc_settings->wplc_ringtone ),
			'ringer_count'            => array( 'value' => intval( $wplc_settings->wplc_new_chat_ringer_count ) ),
			"ring_file"               => TCXRingtonesHelper::get_messagetone_url( $wplc_settings->wplc_messagetone, WPLC_PLUGIN_URL . 'includes/sounds/general/Default_message.mp3' ),
			"action_buttons"          => array(
				"Accept" => __( "Accept Chat", 'wp-live-chat-support' ),
				"Open"   => __( "Open Chat", 'wp-live-chat-support' ),
			),
			"only_agents_notice"      => __( 'Only chat agents can accept chats', 'wp-live-chat-support' ),
			'in_progress_notice'      => __( "In progress with another agent", 'wp-live-chat-support' ),
			'chat_closed'             => __( "Chat session ended", "wp-live-chat-support" ),
			'images_url'              => WPLC_PLUGIN_URL . "images/",
			'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
			'chat_list_url'           => admin_url( 'admin.php?page=wplivechat-menu' ),
			'chat_box_url'            => admin_url( 'admin.php?page=wplivechat-chatbox' ),
			"enable_ring"             => $wplc_settings->wplc_enable_msg_sound,
			"enable_new_visitor_ring" => $wplc_settings->wplc_enable_visitor_sound,
			"enable_files"            => $wplc_settings->wplc_ux_file_share,
			"channel"                 => $wplc_settings->wplc_channel,
			"socket_url"              => esc_url_raw( $wplc_chat_server_data["socket_url"], [ "wss" ] ) . '/chatchannel?aid=' . $wplc_current_user->ID . '&pid=' . get_option( 'WPLC_GUID' ),
			"chat_server_session"     => $wplc_chat_server_data["chat_server_session"],
			"portal_id"               => get_option( 'WPLC_GUID' ),
			"show_date"               => $wplc_settings->wplc_show_date,
			"show_time"               => $wplc_settings->wplc_show_time,
			"show_name"               => $wplc_settings->wplc_show_name,
			"show_avatar"             => $wplc_settings->wplc_show_avatar,
			"wplc_protocol"           => ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https' : 'http',
			"wplc_not_business_hours" => $wplc_settings->wplc_settings_enabled == 1 && ! TCXUtilsHelper::wplc_check_chatbox_enabled_business_hours(),
			"wplc_is_chat_page"       => $is_chat_page,
			"wplc_departments"        => TCXDepartmentsData::get_departments( $wpdb ),
		);

		/*if ( TCXAgentsHelper::is_agent_accepting( get_current_user_id() ) ) {
			$script_data["agent_accepts_data"] = true;
		} else {
			$script_data["agent_accepts_data"] = false;
		}*/

		return $script_data;
	}

	public static function wplc_load_chat_js( $is_chat_page = false ) {
		global $wplc_base_file;
		wp_register_script( 'wplc-md5', wplc_plugins_url( '/js/vendor/md5/md5.min.js', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc-md5' );

		wp_register_script( 'wplc-websocket', wplc_plugins_url( '/modules/agent_chat/js/mcu_websocket.js', $wplc_base_file ), array(
			'jquery'
		), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc-websocket' );

		wp_register_script( 'wplc-agent-chat', wplc_plugins_url( '/modules/agent_chat/js/agent_chat.js', $wplc_base_file ), array(
			"wplc-initiate-admin",
			"wplc-md5",
			'wplc-websocket'
		), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc-agent-chat' );
		$script_data = self::wplc_load_chat_js_data( $is_chat_page );
		wp_localize_script( 'wplc-agent-chat', 'localization_data', $script_data );

	}

	public static function wplc_json_encode( $value, $options = 0, $depth = 512 ) {
		$utf8_value = self::wplc_utf8_encode( $value );

		return json_encode( $utf8_value, $options, $depth );
	}

	public static function wplc_json_decode( $json, $assoc = false, $depth = 512, $options = 0 ) {
		$result_utf8_encoded = json_decode( $json, $assoc, $depth, $options );

		return self::wplc_utf8_decode( $result_utf8_encoded );

	}

	public static function wplc_utf8_encode( $mixed ) {
		if ( is_array( $mixed ) ) {
			foreach ( $mixed as $key => $value ) {
				$mixed[ $key ] = self::wplc_utf8_encode( $value );
			}
		} else if ( is_object( $mixed ) ) {
			foreach ( $mixed as $key => $value ) {
				$mixed->$key = self::wplc_utf8_encode( $value );
			}
		} else if ( is_string( $mixed ) ) {
			return utf8_encode( $mixed );
		}

		return $mixed;
	}

	public static function wplc_utf8_decode( $mixed ) {
		if ( is_array( $mixed ) ) {
			foreach ( $mixed as $key => $value ) {
				$mixed[ $key ] = self::wplc_utf8_decode( $value );
			}
		} else if ( is_object( $mixed ) ) {
			foreach ( $mixed as $key => $value ) {
				$mixed->$key = self::wplc_utf8_decode( $value );
			}
		} else if ( is_string( $mixed ) ) {
			return utf8_decode( $mixed );
		}

		return $mixed;
	}

	public static function wplc_get_page_hook( $menu_slug ) {
		$page_part = str_replace( ' ', '-', strtolower( sanitize_title( __( 'Live Chat', 'wp-live-chat-support' ) ) ) );
		$result    = $page_part . '_page_' . $menu_slug;

		return $result;
	}

	public static function wplc_isDoubleByte( $value ) {
		if ( mb_strlen( $value, 'UTF-8' ) != strlen( $value ) ) {
			return true;
		}

		return false;
	}

	public static function wplc_check_channel_change_on_save( $saved_channel ) {
		$result = $saved_channel;
		if ( ! empty( $_GET['wplc_action'] ) && $_GET['wplc_action'] == 'save_settings' && ! empty( $_POST['wplc_channel'] ) ) {
			$result = $_POST['wplc_channel'];
		}

		return $result;
	}

	public static function get_setting_value_sanitized( $value, $setting_key, $setting_types ) {
		$result = null;
		if ( array_key_exists( $setting_key, $setting_types ) ) {
			switch ( $setting_types[ $setting_key ] ) {
				case "string":
					$result = stripslashes( sanitize_text_field( trim( strval( $value ) ) ) );
					break;
				case "boolean":
					$result = boolval( $value );
					break;
				case "integer":
					$result = intval( $value );
					break;
				case "url":
					$result = esc_url( trim( $value ) );
					break;
				case "socket-url":
					$result = esc_url( trim( $value ), [ "wss" ] );
					break;
				case "base64-url":
					$result = esc_url( trim( base64_decode( $value ) ) );
					break;
				case "html":
					$result = wp_filter_post_kses( strval( $value ) );
					break;
				case "json":
					$result = is_string($value)? json_decode( stripslashes( strval( $value ) ), true ):$value;
					array_walk_recursive($result,function(&$val){
						if(is_string($val))
						{
							$val = stripslashes( sanitize_text_field( trim( strval( $val ) ) ) );
						}else if (is_int($val))
						{
							$val = intval( $val );
						}
					});
					break;
				case "array-settings":
					$result = array();
					foreach ( $value as $k => $v ) {
						$result[ $k ] = self::get_setting_value_sanitized( $v, $setting_key . '>' . $k, $setting_types );
					}
					break;
				case "array-string":
					$result = array();
					foreach ( $value as $k => $v ) {
						$result[ $k ] = utf8_encode( stripslashes( sanitize_text_field( trim( strval( $v ) ) ) ) );
					}
					$result = array_filter( $result, function ( $item ) use ( $value ) {
						return in_array( $item, $value );
					} );
					break;
				case "array-int":
					$result = array();
					foreach ( $value as $k => $v ) {
						$result[ $k ] = intval( $value );
					}
					$result = array_filter( $result, function ( $item ) use ( $value ) {
						return in_array( $item, $value );
					} );
					break;
				case "array-boolean":
					$result = array();
					foreach ( $value as $k => $v ) {
						$result[ $k ] = strlen( $v ) > 0 ? boolval( $v ) : false;
					}
					$result = array_filter( $result, function ( $item ) use ( $value ) {
						return in_array( $item, $value );
					} );
					break;
				case "multi-array-integer":
					array_walk_recursive( $value, function ( &$item, $key ) {
						$item = intval( $item );
					} );
					$result = $value;
					break;
				default:
					$result = utf8_encode( stripslashes( sanitize_text_field( trim( strval( $value ) ) ) ) );
					break;
			}

			return $result;
		}
	}

	public static function attachment_url_to_path( $url )
	{
		$parsed_url = parse_url( $url );
		if(empty($parsed_url['path'])) return false;
		$file = ABSPATH . ltrim( $parsed_url['path'], '/');
		if (file_exists( $file)) return $file;
		return false;
	}

	public static function check_registered_script_by_url($url_regex){
		$wp_scripts = wp_scripts();
		$found=false;
		foreach($wp_scripts->registered as $script)
		{
			if (preg_match($url_regex, $script->src)){
				$found = true;
				break;
			}
		}
		return $found;
	}

	public static function generate_settings_guidJS(){
		if(wp_mkdir_p(WP_CONTENT_DIR.'/wplc_data')) {

			$fileTemplate = "const wplc_assets_guid = '" . TCXUtilsHelper::generateRandomString( 16 ) . "';
						const callusElements = document.getElementsByTagName(\"call-us\");
						Array.prototype.forEach.call(callusElements, function (callus) {
							callus.setAttribute('assets-guid', wplc_assets_guid);
						});";

			file_put_contents( WP_CONTENT_DIR.'/wplc_data/check.js', $fileTemplate );
		}

	}

    public static function delete_dir($dirPath) {
        if (! is_dir($dirPath)) {
           return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::delete_dir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}




