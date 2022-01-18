<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class TCXChatHelper {

	public static function update_chat_statuses( $chats, $set_last_activity = true ) {
		foreach ( $chats as $chat ) {
			$chat->status = self::update_chat_status( $chat, null, $set_last_activity );
		}

		return $chats;
	}

	public static function update_chat_status( $chat, $last_access = null, $set_last_activity = true ) {
		$id         = $chat->id;
		$timestamp  = $last_access == null ? strtotime( $chat->last_active_timestamp ) : strtotime( $last_access );
		$datenow    = current_time( 'timestamp', true );
		$difference = $datenow - $timestamp;
		$new_status = $chat->status;

		switch ( intval( $chat->status ) ) {
			case ChatStatus::ACTIVE:
			case ChatStatus::PENDING_AGENT:
			case ChatStatus::BROWSE:
				$new_status = self::get_time_based_status_changes( $chat, $difference );
				switch ( $new_status ) {
					case ChatStatus::ENDED_DUE_CLIENT_INACTIVITY:
					case ChatStatus::ENDED_DUE_AGENT_INACTIVITY:
						self::end_chat( $id, $new_status );
						break;
					default:
						self::set_chat_status( $id, $new_status, $set_last_activity );
						break;
				}
				break;
			case ChatStatus::MISSED:
			case 1:
			case 4:
			case 7:
			case ChatStatus::ENDED_DUE_CLIENT_INACTIVITY:
			case ChatStatus::ENDED_DUE_AGENT_INACTIVITY:
			case ChatStatus::ENDED_BY_CLIENT:
			case ChatStatus::ENDED_BY_AGENT:
				if ( $chat->completed == 0 && $difference >= 60 ) {
					self::set_chat_completed( $id );
				}
				break;
		}

		return $new_status;

	}

	public static function end_chat( $cid, $status ) {
		$wplc_settings   = TCXSettings::getSettings();
		$ending_statuses = array(
			ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
			ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
			ChatStatus::ENDED_BY_AGENT,
			ChatStatus::ENDED_BY_CLIENT,
			ChatStatus::ENDED_DUE_BLOCK
		);
		if ( ! in_array( $status, $ending_statuses ) ) {
			return false;
		}
		if ( self::set_chat_status( $cid, $status ) ) {
			if ( $wplc_settings->wplc_send_transcripts_when_chat_ends ) {
				TCXTranscriptsHelper::wplc_send_transcript( $cid );
			}

			return true;
		} else {
			return false;
		}
	}

	public static function get_visitors_count() {
		global $wpdb;

		return TCXChatData::get_visitors_count( $wpdb );
	}

	public static function set_typing_indicator( $user, $cid, $type ) {
		global $wpdb;
		$cid   = intval( $cid );
		$cdata = TCXChatData::get_chat( $wpdb, $cid );
		$other = maybe_unserialize( $cdata->other );

		if ( isset( $other['typing'][ $user ] ) && $other['typing'][ $user ] == $type ) {
			/* same state, ignore */
			return "already";
		} else {
			$other['typing'][ $user ] = $type;
			TCXChatData::update_chat_property( $wpdb, $cid, "other", maybe_serialize( $other ) );

			return $cid;
		}
	}

	public static function get_chat_messages( $cid, $type = "", $refersTo = "ANYONE" ) {
		global $wpdb;
		$wplc_settings = TCXSettings::getSettings();
		if ( $wplc_settings->wplc_channel == 'mcu' ) {
			$results = TCXChatData::get_chat_messages( $wpdb, $cid, array(), $refersTo );
		} else {
			switch ( $type ) {
				case "READ":
					$results = TCXChatData::get_chat_messages( $wpdb, $cid, array( 1 ), $refersTo );
					break;
				case "NON_READ":
					$results = TCXChatData::get_chat_messages( $wpdb, $cid, array( 0 ), $refersTo );
					break;
				default :
					$results = TCXChatData::get_chat_messages( $wpdb, $cid, array(), $refersTo );
					break;
			}
		}

		if ( ! isset( $results[0] ) ) {
			return false;
		} else {
			return $results;
		}
	}

	public static function get_chat_including_messages( $cid ) {
		global $wpdb;
		$db_results = TCXChatData::get_session_details( $wpdb, $cid );

		$results             = array();
		$results['messages'] = array();
		foreach ( $db_results as $key => $db_result ) {
			if ( $key == 0 ) {
				$session                    = new TCXChatSession();
				$session->id                = $db_result->session_id;
				$session->timestamp         =  date('Y-m-d H:i:s',strtotime($db_result->timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ;
				$session->end_timestamp     =  date('Y-m-d H:i:s',strtotime($db_result->last_active_timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ;
				$session->name              = $db_result->name;
				$session->email             = $db_result->email;
				$session->url               = $db_result->url;
				$session->client_data       = json_decode( $db_result->client_data, true );
				$session->rating            = is_null( $db_result->rating ) ? - 1 : intval( $db_result->rating );
				$session->rating_comments   = $db_result->comments;
				$session->avatar_name_alias = TCXUtilsHelper::wplc_isDoubleByte( $session->name ) ? 'Visitor' : $session->name;

				$session->custom_fields = null;
				$session_other_data     = maybe_unserialize( $db_result->other_data );
				if ( is_array( $session_other_data ) ) {
					if ( array_key_exists( 'custom_fields', $session_other_data ) ) {
						$session->custom_fields = $session_other_data['custom_fields'];
					}
				}

				$results['session'] = $session;
			}
			//Caution data loads from left join query for performance in order to avoid multiple queries
			// it's possible to there is no messages data.
			if ( $db_result->message_id != null ) {
				$message             = new TCXChatMessage();
				$message->id         = $db_result->message_id;
				$message->originates = $db_result->originates;
				$message->from       = $db_result->msgfrom;
				$message->session_id = $db_result->session_id;
				$message->timestamp  = date('Y-m-d H:i:s',strtotime($db_result->message_timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ;
				$message->set_message( $db_result->msg );

				$results['messages'][ $key ] = $message;
			}
		}

		return $results;
	}

	public static function set_chat_external_session( $cid, $external_session ) {
		global $wpdb;

		return TCXChatData::update_chat_property( $wpdb, $cid, 'session', $external_session );
	}

	public static function mark_messages_as_read( $mids ) {
		global $wpdb;

		TCXChatData::update_batch_message_property( $wpdb, $mids, 'status', 1 );

		return "ok";
	}

	public static function set_last_active_now( $cid, $user_type = null ) {
		global $wpdb;
		TCXChatData::update_chat_property( $wpdb, $cid, 'last_active_timestamp', current_time( 'mysql', true ) );
		$data = array(
			'last_active_timestamp' => current_time( 'mysql', true )
		);
		if ( $user_type != null ) {
			$data['last_action_by'] = $user_type;
		}

		TCXChatData::update_chat( $wpdb, $cid, $data );
	}

	public static function set_agent_id( $cid, $aid ) {
		global $wpdb;
		$chat = TCXChatData::get_chat( $wpdb, $cid );
		if ( $chat ) {
			$incompatible_statuses = array(ChatStatus::ENDED_DUE_BLOCK,ChatStatus::ENDED_BY_AGENT,ChatStatus::ENDED_DUE_AGENT_INACTIVITY,ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,ChatStatus::MISSED,ChatStatus::OLD_ENDED,ChatStatus::NOT_STARTED);
			if (! in_array(intval( $chat->status ),$incompatible_statuses) ) {
				return TCXChatData::update_chat_property( $wpdb, $cid, 'agent_id', intval( $aid ) );
			}
		} else {
			return false;
		}
	}

	public static function set_messages_agent_id( $cid, $aid ) {
		global $wpdb;
		$result = TCXChatData::get_chat_messages( $wpdb, $cid, array(), $refersTo = '0' );
		$mids   = array_map( function ( $result ) {
			return $result->id;
		}, $result );
		if ( $result ) {
			TCXChatData::update_batch_message_property( $wpdb, $mids, 'ato', $aid );
		}
	}

	public static function encrypt_msg( $plaintext ) {
		$message       = array(
			'e' => 0,
			'm' => $plaintext
		);
		$wplc_settings = TCXSettings::getSettings();
		if ( $wplc_settings->wplc_enable_encryption && ! empty( $wplc_settings->wplc_encryption_key ) ) {
			$message = array(
				'e' => 1,
				'm' => TCXEncryptHelper::encrypt( $plaintext )
			);
		}

		return $message;
	}

	public static function decrypt_msg( $input ) {
		$messages = maybe_unserialize( $input );
		if ( is_array( $messages ) ) {
			/** Check already in place to determine if a message was previously encrypted */
			if ( $messages['e'] == 1 ) {
				/* This message was encrypted */
				return TCXEncryptHelper::decrypt( $messages['m'] );
			} else {
				return $messages['m'];
			}
		} else {
			return $input;
		}
	}

	public static function set_chat_status( $cid, $status, $set_last_activity = true ) {
		global $wpdb;
		$change_status = TCXChatData::update_chat_status_with_flow_check( $wpdb, $cid, $status );
		if ( $change_status !== false && $change_status > 0 ) {
			if ( $set_last_activity ) {
				TCXChatHelper::set_last_active_now( $cid );
			}
			$action_data = array(
				'chat_session_id'    => $cid,
				'message_id'         => null,
				'sender'             => UserTypes::SYSTEM,
				'recipient'          => null,
				'action_type'        => ActionTypes::CHANGE_STATUS,
				'data'               => json_encode( array( 'new_status' => $status ) ),
				'timestamp_added_at' => TCXUtilsHelper::current_mysql_time_with_ms()//current_time( 'mysql' )
			);

			return TCXActionQueueData::add_action_in_queue( $wpdb, $action_data );
		} else {
			return false;
		}

	}

	public static function set_chat_state( $id, $state ) {
		global $wpdb;

		return TCXChatData::update_chat_property( $wpdb, $id, 'state', $state );
	}

	public static function set_chat_completed( $id ) {
		global $wpdb;

		return TCXChatData::update_chat_property( $wpdb, $id, 'completed', 1 );
	}

	public static function complete_all_ended_chats() {
		global $wpdb;
		$ignored_statuses = array(
			ChatStatus::BROWSE,
			ChatStatus::ACTIVE,
			ChatStatus::NOT_STARTED,
			ChatStatus::PENDING_AGENT
		);

		$chats = TCXChatData::get_incomplete_chats( $wpdb, - 1, $ignored_statuses );
		foreach ( $chats as $chat ) {
			self::set_chat_completed( $chat->id );
		}
	}

	public static function set_chat_user_data( $cid, $status = ChatStatus::BROWSE, $sessioncode ) {
		global $wpdb;

		$user_data = array(
			'ip'         => TCXUtilsHelper::get_user_ip(),
			'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ),
			'country'    => TCXUtilsHelper::wplc_geo_ip()
		);

		$update_values = array(
			'url'                   => sanitize_text_field( $_SERVER['HTTP_REFERER'] ),
			'last_active_timestamp' => current_time( 'mysql', true ),
			'last_action_by'        => UserTypes::CLIENT,
			'ip'                    => json_encode( $user_data ),
			'status'                => $status,
			'session'               => $sessioncode,
		);

		TCXChatData::update_chat( $wpdb, $cid, $update_values );

		return $user_data;
	}

	public static function add_chat( $name, $email, $session, $is_mobile = false, $department_id = 0 ) {
		global $wpdb;

		$user_data = array(
			'ip'         => TCXUtilsHelper::get_user_ip(),
			'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] )
		);
		/* user types
		 * 1 = new
		 * 2 = returning
		 * 3 = timed out
		 */
		$other = array(
			"user_type" => 1
		);

		if ( $is_mobile ) {
			$other['user_is_mobile'] = true;
		} else {
			$other['user_is_mobile'] = false;
		}

		$wplc_chat_session_data = array(
			'status'                => ChatStatus::BROWSE,
			'timestamp'             => current_time( 'mysql', true ),
			'name'                  => $name,
			'email'                 => $email,
			'session'               => $session,
			'ip'                    => json_encode( $user_data ),
			'url'                   => sanitize_text_field( $_SERVER['HTTP_REFERER'] ),
			'last_active_timestamp' => current_time( 'mysql', true ),
			'department_id'         => $department_id,
			'other'                 => maybe_serialize( $other ),
		);

		$lastid = TCXChatData::add_chat( $wpdb, $wplc_chat_session_data );

		return $lastid;
	}

	public static function user_initiate_chat( $name, $email, $cid, $department, $customFieldsValues ) {

		global $wpdb;

		$user_data = array(
			'ip'         => TCXUtilsHelper::get_user_ip(),
			'user_agent' => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] )
		);

		if ( $cid != null ) {
			$chat_other                  = self::get_chat_other_information( $cid );
			$chat_other['custom_fields'] = $customFieldsValues;
			TCXChatData::update_chat( $wpdb, $cid, array(
				'status'                => ChatStatus::PENDING_AGENT,
				'timestamp'             => current_time( 'mysql', true ),
				'name'                  => $name,
				'email'                 => $email,
				'ip'                    => json_encode( $user_data ),
				'url'                   => sanitize_text_field( $_SERVER['HTTP_REFERER'] ),
				'last_active_timestamp' => current_time( 'mysql', true ),
				'department_id'         => $department,
				'other'                 => maybe_serialize( $chat_other )
			) );

			TCXWebhookHelper::send_webhook( WebHookTypes::CHAT_REQUEST, array( "chat_id" => $cid ) );
			self::new_chat_email( $name, $email );

			return $cid;
		}
	}

	public static function get_time_based_status_changes( $chat, $difference ) {
		$result = $chat->status;
		switch ( $chat->status ) {
			case ChatStatus::PENDING_AGENT:
				if ( $difference > 500 ) {
					$result = ChatStatus::MISSED;
				}
				break;
			case ChatStatus::BROWSE:
				if ( $difference > 300 ) {
					$result = ChatStatus::NOT_STARTED;
				}
				break;
			case ChatStatus::ACTIVE:
				if ( $difference > 1000 ) {
					if ( $chat->last_action_by == UserTypes::CLIENT ) {
						$result = ChatStatus::ENDED_DUE_AGENT_INACTIVITY;
					} else if ( $chat->last_action_by == UserTypes::AGENT ) {
						$result = ChatStatus::ENDED_DUE_CLIENT_INACTIVITY;
					}
				}
				break;
			default:
				break;
		}

		return $result;
	}

	public static function add_chat_message( $from, $cid, $msg, $file_data = null, $agent_sender_id = 0 ) {
		global $wpdb;
		$chat = TCXChatData::get_chat( $wpdb, $cid );

		if ( $from == UserTypes::AGENT ) {
			$agent = TCXAgentsHelper::get_agent($agent_sender_id);
			$fromname           = isset($agent->display_name) ? $agent->display_name : "Admin";
			$agent_recipient_id = 0;
		} else if ( $from == UserTypes::CLIENT ) {
			$fromname           = TCXUtilsHelper::that_or_default_setting( $chat->name, 'wplc_user_default_visitor_name' );
			$agent_recipient_id = $chat->agent_id;
		}

		$result                     = - 1;
		$message_encrypted          = TCXChatHelper::encrypt_msg( $msg );
		$message_properties         = new stdClass();
		$message_properties->isFile = $file_data != null;
		$message_properties->file   = $file_data;

		$insert_result = TCXChatData::add_chat_message( $wpdb, $cid, $fromname, $message_encrypted, $from, $message_properties, $agent_sender_id, $agent_recipient_id );
		if ( $insert_result !== false ) {
			$result = $wpdb->insert_id;
			if($message_properties->isFile) {
				TCXChatData::add_chat_file( $wpdb, $cid, $result, $file_data->FileName, $file_data->FileLink );
			}
			if ( ( $from == UserTypes::CLIENT && $agent_recipient_id > 0 ) || $from != UserTypes::CLIENT ) {
				$action_data = array(
					'chat_session_id'    => $cid,
					'message_id'         => $result,
					'sender'             => $from,
					'recipient'          => $from == UserTypes::CLIENT ? $agent_recipient_id : $chat->session,
					'action_type'        => ActionTypes::NEW_MESSAGE,
					'data'               => json_encode( $message_encrypted ),
					'message_properties' => $message_properties->isFile ? json_encode( $message_properties ) : null,
					'timestamp_added_at' => TCXUtilsHelper::current_mysql_time_with_ms()//current_time( 'mysql' )
				);
				TCXActionQueueData::add_action_in_queue( $wpdb, $action_data );
			}
		}
		TCXChatHelper::set_last_active_now( sanitize_text_field( $cid ), TCXAgentsHelper::is_agent() ? UserTypes::AGENT : UserTypes::CLIENT );

		return $result;
	}

	public static function get_queued_actions( $session_id, $recipient, $change_code, $clean_previous = false ) {
		global $wpdb;
		if ( ( $change_code != "NONE" && $clean_previous ) ) {
			TCXActionQueueData::remove_actions_from_queue( $wpdb, $session_id, $recipient, $change_code );
		} else if ( $change_code == "NONE" ) {
			TCXActionQueueData::clean_actions_from_queue( $wpdb, $session_id, $recipient );
			$action_data = array(
				'chat_session_id'    => $session_id,
				'message_id'         => null,
				'sender'             => UserTypes::SYSTEM,
				'recipient'          => $recipient,
				'action_type'        => ActionTypes::START_QUEUE,
				'timestamp_added_at' => TCXUtilsHelper::current_mysql_time_with_ms()//current_time( 'mysql' )
			);
			TCXActionQueueData::add_action_in_queue( $wpdb, $action_data );
		}

		return TCXActionQueueData::get_actions_from_queue( $wpdb, $session_id, $recipient, $change_code );
	}

	public static function get_message_file( $message ) {
		$file = null;
		if ( $message->other ) {
			$message_properties = json_decode( $message->other );
			if ( isset( $message_properties->isFile ) && isset( $message_properties->file ) ) {
				$file = $message_properties->file;
			}
		}

		return $file;
	}

	public static function get_chat_other_information( $cid, $chat = null ) {
		global $wpdb;
		if ( $chat === null ) {
			$chat = TCXChatData::get_chat( $wpdb, $cid );
		}

		return maybe_unserialize( $chat->other );

	}

	public static function generate_transcript( $chat_messages ) {
		$transcript = "<p>";
		array_walk( $chat_messages, function ( $chat_message, $index ) use ( &$transcript ) {
			$message       = new TCXChatMessage();
			$message->id   = $chat_message->message_id;
			$message->from = $chat_message->msgfrom;
			$message->set_message( $chat_message->msg );

			$transcript .= "<strong>" . $message->from . ": </strong>" . preg_replace( "/\r|\n|\r\n/", '<br/>', $message->get_message() ) . "<br/>";
		} );
		$transcript .= "</p>";

		return $transcript;
	}

	public static function get_client_chat_list( $db_chats ) {
		$results = array();
		foreach ( $db_chats as $key => $db_result ) {
			$session_session                    = new TCXChatSession();
			$session_session->id                = $db_result->id;
			$session_session->name              = esc_html( $db_result->name );
			$session_session->email             = esc_html( $db_result->email );
			$session_session->status            = $db_result->status;
			$session_session->url               = esc_url_raw( $db_result->url );
			$session_session->timestamp         = date('Y-m-d H:i:s',strtotime($db_result->timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ;
			$session_session->rating            = $db_result->rating===null?-1:$db_result->rating;
			$session_session->avatar_name_alias = TCXUtilsHelper::wplc_isDoubleByte( $session_session->name ) ? 'Visitor' : $session_session->name;
			$results[ $key ]                    = $session_session;
		}

		return $results;
	}

	private static function new_chat_email( $name, $email ) {
		$wplc_settings = TCXSettings::getSettings();
		if ( $wplc_settings->wplc_pro_chat_notification ) {
			$subject = sprintf( __( 'Incoming chat from %s (%s) on %s', 'wp-live-chat-support' ),
				$name,
				$email,
				get_option( 'blogname' )
			);

			$msg = sprintf( __( '%s (%s) wants to chat with you.', 'wp-live-chat-support' ),
					$name,
					$email
			       ) . '<br/><br/>';

			$msg .= sprintf( __( 'Log in: %s', 'wp-live-chat-support' ),
				wp_login_url()
			);

			$headers = 'From: ' . $wplc_settings->wplc_pro_chat_email_address . "\r\n" .
			           'Reply-To: ' . $wplc_settings->wplc_pro_chat_email_address . "\r\n" .
			           'content-type: text/html' . "\r\n" .
			           'X-Mailer: PHP/' . phpversion();

			return wp_mail( get_option( 'admin_email' ), $subject, $msg, $headers );
		}

		return true;
	}

	public static function module_db_integration() {
		global $wpdb;
		global $wplc_tblname_chats;
		global $wplc_tblname_msgs;
		global $wplc_tblname_actions_queue;
		global $wplc_tblname_files;

		$sql = "
        CREATE TABLE `" . $wplc_tblname_chats . "` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `timestamp` datetime NOT NULL,
          `name` varchar(700) NOT NULL,
          `email` varchar(700) NOT NULL,
          `ip` varchar(700) NOT NULL,
          `status` int(11) NOT NULL,
          `state` int(11) NOT NULL,
          `completed` int(11) NOT NULL DEFAULT '0',
          `session` varchar(100) NOT NULL,
          `url` varchar(700) NOT NULL,
          `last_active_timestamp` datetime NOT NULL,
          `last_action_by` INT(11),
          `agent_id` INT(11) NOT NULL,
          `other` LONGTEXT NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
    ";
		dbDelta( $sql );

		$sql = '
        CREATE TABLE `' . $wplc_tblname_msgs . '` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `chat_sess_id` int(11) NOT NULL,
          `msgfrom` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
          `msg` LONGTEXT CHARACTER SET utf8mb4 NOT NULL,
          `timestamp` datetime NOT NULL,
          `status` INT(3) NOT NULL,
          `originates` INT(3) NOT NULL,
          `other` LONGTEXT NOT NULL,
          `afrom` INT(10) NOT NULL,
          `ato` INT(10) NOT NULL,
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
    ';

		@dbDelta( $sql );

		$results = $wpdb->get_results( "DESC $wplc_tblname_msgs" );
		$founded = 0;
		foreach ( $results as $row ) {
			if ( $row->Field == "from" ) {
				$founded ++;
			}
		}

		if ( $founded > 0 ) {
			$wpdb->query( "ALTER TABLE " . $wplc_tblname_msgs . " CHANGE `from` `msgfrom` varchar(150)" );
		}

		@dbDelta( $sql );

		$sql = "
        CREATE TABLE `" . $wplc_tblname_actions_queue . "` (
          `chat_session_id`  int(11) NOT NULL,
          `message_id`  int(11) NULL,
          `sender` int(11) ,
          `recipient`  varchar(20) CHARACTER SET utf8mb4 NULL,
          `action_type`  varchar(20) CHARACTER SET utf8mb4 NOT NULL,
          `data`  LONGTEXT CHARACTER SET utf8mb4 NOT NULL,
          `message_properties` varchar(1000) CHARACTER SET utf8mb4 NULL,
          `timestamp_added_at` datetime(3) NOT NULL,
          `code` varchar(15) CHARACTER SET utf8mb4 NOT NULL,
          KEY idx_chat_session_id (`chat_session_id`),
          KEY idx_recipient (`recipient`),
          KEY code (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;
    ";
		@dbDelta( $sql );

		$sql = "
        CREATE TABLE `" . $wplc_tblname_files . "` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `message_id`  int(11) NOT NULL,
          `cid` int(11) NOT NULL,
          `filename`  varchar(500) CHARACTER SET utf8mb4 NULL,
          `fileurl`  varchar(2000) CHARACTER SET utf8mb4 NOT NULL,
          KEY idx_cid (`cid`),
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  AUTO_INCREMENT=1;
    ";

		dbDelta( $sql );

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$sql     = " SHOW COLUMNS FROM {$wpdb->prefix}wplc_chat_sessions WHERE `Field` = 'agent_id'";
				$results = $wpdb->get_results( $sql );
				if ( ! $results ) {
					$sql = "ALTER TABLE {$wpdb->prefix}wplc_chat_sessions ADD `agent_id` INT(11) NOT NULL ;";
					$wpdb->query( $sql );
				}

				$department_field_sql = " SHOW COLUMNS FROM {$wpdb->prefix}wplc_chat_sessions WHERE `Field` = 'department_id'";
				$results              = $wpdb->get_results( $department_field_sql );
				if ( ! $results ) {
					$department_field_sql = "ALTER TABLE {$wpdb->prefix}wplc_chat_sessions ADD `department_id` INT(11) NOT NULL ;";
					$wpdb->query( $department_field_sql );
				}
			}
			switch_to_blog( $old_blog );
		} else {
			$sql     = " SHOW COLUMNS FROM $wplc_tblname_chats WHERE `Field` = 'agent_id'";
			$results = $wpdb->get_results( $sql );
			if ( ! $results ) {
				$sql = "ALTER TABLE `$wplc_tblname_chats` ADD `agent_id` INT(11) NOT NULL ;";
				$wpdb->query( $sql );
			}

			$department_field_sql = " SHOW COLUMNS FROM $wplc_tblname_chats WHERE `Field` = 'department_id'";
			$results              = $wpdb->get_results( $department_field_sql );
			if ( ! $results ) {
				$department_field_sql = "ALTER TABLE `$wplc_tblname_chats` ADD `department_id` INT(11) NOT NULL ;";
				$wpdb->query( $department_field_sql );
			}
		}
	}

}