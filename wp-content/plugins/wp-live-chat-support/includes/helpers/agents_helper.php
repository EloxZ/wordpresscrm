<?php

add_action( 'wp_logout', array( 'TCXAgentsHelper', 'agent_logout' ) );
add_action( "admin_init", array( "TCXAgentsHelper", "update_agent_usage" ) );

class TCXAgentsHelper {

	public static function get_online_agent_users() {
		$agents        = TCXAgentsHelper::get_online_users_with_timeout_check();
		$online_agents = array();
		foreach ( $agents as $v ) {
			if ( TCXAgentsHelper::is_agent_accepting( $v->ID ) ) {
				$online_agents[] = $v;
			}
		}

		return $online_agents;
	}

	public static function get_online_users_with_timeout_check() {
		$result = array();
		$agents = get_users( array( 'meta_key' => 'wplc_chat_agent_online' ) );
		if ( ! is_array( $agents ) ) {
			$agents = array();
		}
		foreach ( $agents as $agent ) {
			$check = get_user_meta( $agent->ID, "wplc_chat_agent_online" );
			if ( $check && $check[0] && time() - $check[0] < 300 ) {
				$result[] = $agent;
			} else {
				delete_user_meta( $agent->ID, "wplc_chat_agent_online" ); // force offline
			}
		}

		return $result;
	}

	public static function is_agent( $uid = 0 ) {
		if ( empty( $uid ) ) {
			$user = wp_get_current_user();
		} else {
			$user = get_user_by( 'id', $uid );
		}
		if ( $user ) {
			return $user->has_cap( 'wplc_ma_agent' );
		}

		return false;
	}

	public static function is_agent_accepting( $uid = 0 ) {
		if ( empty( $uid ) ) {
			$uid = get_current_user_id();
		}
		$wplc_settings = TCXSettings::getSettings();
		$choose_array  = TCXAgentsHelper::get_agents_accepting_chats();
		/*if ( ! isset( $choose_array[ $uid ] ) ) {
			$choose_array[ $uid ] = true;
			update_option( "WPLC_CHOOSE_ACCEPTING", $choose_array );
		}*/
		if ( ! $wplc_settings->wplc_allow_agents_set_status && ! $choose_array[ $uid ] ) { // force online if agents cannot set status
			$choose_array[ $uid ] = true;
			update_option( "WPLC_CHOOSE_ACCEPTING", $choose_array );
		}

		return array_key_exists( $uid, $choose_array ) ? $choose_array[ $uid ] : false;
	}

	public static function get_agents_accepting_chats() {
		$choose_array = get_option( "WPLC_CHOOSE_ACCEPTING" );
		if ( ! is_array( $choose_array ) ) {
			$choose_array = array();
		}

		return $choose_array;
	}

	public static function set_agent_accepting( $uid, $online ) {
		$choose_array = self::get_agents_accepting_chats();
		if ( ! isset( $choose_array[ $uid ] ) || $choose_array[ $uid ] != boolval( $online ) ) {
			$choose_array[ $uid ] = boolval( $online );
			update_option( "WPLC_CHOOSE_ACCEPTING", $choose_array );
		}
	}

	public static function agent_is_online( $uid ) {
		return in_array( $uid, array_map( function ( $agent ) {
			return $agent->ID;
		}, self::get_online_agent_users() ) );
	}

	public static function get_agent_users() {
		return get_users( array( 'meta_key' => 'wplc_ma_agent' ) );
	}

	public static function get_online_agent_users_count() {
		return count( self::get_online_agent_users() );
	}

	public static function exist_agent_online() {
		return self::get_online_agent_users_count() > 0;
	}

	public static function exist_available_agent() {
		return self::exist_agent_online() && TCXUtilsHelper::wplc_check_chatbox_enabled_business_hours();
	}

	public static function update_agent_time( $uid = 0 ) {
		if ( empty( $uid ) ) {
			$uid = get_current_user_id();
		}
		update_user_meta( $uid, "wplc_chat_agent_online", time() );
	}

	public static function agent_logout($user_id) {
		delete_user_meta( $user_id, "wplc_chat_agent_online" );
	}

	public static function set_user_as_agent( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		$user = get_userdata( $user_id );
		if ( $user !== false ) {
			update_user_meta( $user_id, 'wplc_ma_agent', '1' );
			$wplc_ma_user = new WP_User( $user_id );
			$wplc_ma_user->add_cap( 'wplc_ma_agent' );
			if ( ! in_array( 'administrator', (array) $user->roles ) ) {
				$wplc_ma_user->add_cap( 'wplc_cap_show_history' );
				$wplc_ma_user->add_cap( 'wplc_cap_show_offline' );
			}
			TCXAgentsHelper::update_agent_time( $user_id );
		}
	}

	public static function revoke_agent_from_user( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		if ( current_user_can( 'wplc_cap_admin' ) ) {
			$user = get_userdata( $user_id );
			if ( $user !== false ) {
				$wplc_ma_user = new WP_User( $user_id );
				$wplc_ma_user->add_cap( 'wplc_ma_agent', false );
				if ( ! in_array( 'administrator', (array) $user->roles ) ) {
					$wplc_ma_user->add_cap( 'wplc_cap_show_history', false );
					$wplc_ma_user->add_cap( 'wplc_cap_show_offline', false );
				}
				delete_user_meta( $user_id, "wplc_ma_agent" );
				delete_user_meta( $user_id, "wplc_chat_agent_online" );
			}
		}
	}

	public static function set_agent_tagline( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		$predefined_response = wp_kses( nl2br( $_POST['wplc_user_tagline'] ), array( 'br' ) );
		update_user_meta( $user_id, 'wplc_user_tagline', $predefined_response );
	}

	public static function set_agent_department( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'wplc_user_department', intval( $_POST['wplc_user_department'] ) );

	}

	public static function update_agent_usage() {
		if ( TCXAgentsHelper::is_agent() ) {
			TCXAgentsHelper::update_agent_time();
			TCXAgentsHelper::get_online_users_with_timeout_check();
		}
	}

	public static function get_agent( $agent_id ) {
		$result      = null;
		$user_object = get_user_by( "id", $agent_id );
		if ( is_object( $user_object ) ) {
			$result = $user_object->data;
		}

		return $result;

	}


	public static function new_agent_email( $username, $name, $password, $email ) {

		$wplc_settings=TCXSettings::getSettings();
		$subject = sprintf( __( 'Welcome on %s', 'wp-live-chat-support' ),
			get_option( 'blogname' )
		);

		$msg = sprintf( __( '%s welcome! You can find below your credentials. You are able to change your password after first login.', 'wp-live-chat-support' ),
				$name
		       ) . '<br/><br/>';


		$msg .= sprintf( __( 'Agent login url: %s', 'wp-live-chat-support' ),
				wp_login_url()
		        ).'<br/>';

		$msg .= sprintf( __( 'Username: %s', 'wp-live-chat-support' ),
			$username
		).'<br/>';

		$msg .= sprintf( __( 'Password: %s', 'wp-live-chat-support' ),
				$password
		        ).'<br/>';



		$headers = 'From: ' . $wplc_settings->wplc_pro_chat_email_address . "\r\n" .
		           'Reply-To: ' . $wplc_settings->wplc_pro_chat_email_address . "\r\n" .
		           'content-type: text/html' . "\r\n" .
		           'X-Mailer: PHP/' . phpversion();

		return wp_mail( $email, $subject, $msg, $headers );
	}

}