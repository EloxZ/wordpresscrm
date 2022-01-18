<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ChatClientController extends BaseController {

	public function __construct( $alias, $custom_view = null ) {
		parent::__construct( __( "Chat Client", 'wp-live-chat-support' ), $alias, $custom_view );
	}


	private function get_minimized_status() {
		switch ( $this->wplc_settings->wplc_auto_pop_up ) {
			case 1:
				$minimized = 'mobile';
				break;
			case 2:
				$minimized = 'desktop';
				break;
			case 3:
				$minimized = 'none';
				break;
			default:
				$minimized = 'both';
				break;
		}

		return $minimized;
	}

	private function get_chat_animation() {
		$result = "none";
		switch ( $this->wplc_settings->wplc_animation ) {
			case "animation-1":
				$result = "slideUp";
				break;
			case "animation-2":
				switch ( $this->wplc_settings->wplc_settings_align ) {
					case "2":
					case "4":
						$result = "slideLeft";
						break;
					case "1":
					case "3":
					default:
						$result = "slideRight";
						break;
				}
				break;
			case "animation-3":
				$result = "fadeIn";
				break;
			default:
				$result = "none";
				break;
		}

		return $result;

	}

	private function generate_position_style() {
		$result = "";
		switch ( $this->wplc_settings->wplc_settings_align ) {
			case "1":
				if ( $this->wplc_settings->wplc_settings_minimized_style === 'Bubble' ) {
					$result = "left: 20px; bottom: 20px;";
				} else {
					$result = "left: 20px; bottom: 0px;";
				}
				break;
			case "2":
				if ( $this->wplc_settings->wplc_settings_minimized_style === 'Bubble' ) {
					$result = "right: 20px; bottom: 20px;";
				} else {
					$result = "right: 20px; bottom: 0px;";
				}
				break;
			case "3":
				$result = "left: 20px; bottom: 200px;";
				break;
			case "4":
				$result = "right: 20px; bottom: 200px;";
				break;
			default:
				$result = "bottom: 0; right: 6px;";
				break;
		}

		return $result;
	}

	private function get_integration_links() {
		return (object) array(
			"facebook" => esc_attr( $this->wplc_settings->wplc_social_fb ),
			"twitter"  => esc_attr( $this->wplc_settings->wplc_social_tw )
		);
	}

	public function embed_view() {
		$embed_code = $this->view( true, true, true );
		$embed_code = preg_replace( '/(\S+)=""/', '', $embed_code );
		echo $embed_code;
	}

	public function view( $return_html = false, $add_wrapper = true, $embed_code = false ) {
		if ( $this->wplc_settings->wplc_display_to_loggedin_only && ! is_user_logged_in() ) {
			return;
		}
		$this->view_data["chat_icon"]         = esc_url_raw( $this->wplc_settings->wplc_chat_icon );
		$this->view_data["chat_icon_type"]    = esc_attr( $this->wplc_settings->wplc_chat_icon_type );
		$this->view_data["chat_logo"]         = esc_url_raw( $this->wplc_settings->wplc_chat_logo );
		$this->view_data["agent_logo"]        = esc_url_raw( $this->wplc_settings->wplc_agent_logo );
		$this->view_data["agent_name"]        = $this->wplc_settings->wplc_agent_default_name;
		$this->view_data["auth_type"]         = sanitize_text_field( $this->wplc_settings->wplc_require_user_info );
		$this->view_data["position_style"]    = $this->generate_position_style();
		$this->view_data["animation"]         = $this->get_chat_animation();
		$this->view_data["integrations"]      = $this->get_integration_links();
		$this->view_data["minimized"]         = $this->get_minimized_status();
		$this->view_data["popup_when_online"] = $this->wplc_settings->wplc_auto_pop_up_online ? "true" : "false";
		$this->view_data["is_enable"]         = $this->wplc_settings->wplc_settings_enabled == "1" ? "true" : "false";
		$this->view_data["enable_mobile"]     = $this->wplc_settings->wplc_enabled_on_mobile ? "true" : "false";
		$this->view_data["enable_poweredby"]  = $this->wplc_settings->wplc_powered_by ? "true" : "false";
		$this->view_data["enable_msg_sounds"] = $this->wplc_settings->wplc_enable_msg_sound ? "true" : "false";
		$this->view_data["channel"]           = $this->wplc_settings->wplc_channel;

		$this->view_data["message_sound"] = isset( $this->wplc_settings->wplc_messagetone ) ? TCXRingtonesHelper::get_messagetone_url( $this->wplc_settings->wplc_messagetone ) : '';
		$this->view_data["wp_url"]        = admin_url( 'admin-ajax.php' );
		switch ( $this->wplc_settings->wplc_channel ) {
			case 'phone':
				$c2c_url                        = parse_url( esc_url_raw( $this->wplc_settings->wplc_channel_url ) );
				$this->view_data["channel_url"] = ( array_key_exists( 'scheme', $c2c_url ) ? $c2c_url['scheme'] : '' ) . "://" . $c2c_url['host'] . ( array_key_exists( 'port', $c2c_url ) ? ":" . $c2c_url['port'] : '' );
				break;
			case 'wp':
				$this->view_data["channel_url"] = esc_url_raw( $this->wplc_settings->wplc_channel_url );
				break;
			case 'mcu':
				$wplc_chat_server_data                  = TCXUtilsHelper::get_mcu_data( $this->wplc_settings->wplc_socket_url, $this->wplc_settings->wplc_chat_server_session );
				$this->view_data["channel_url"]         = esc_url_raw( $wplc_chat_server_data["socket_url"], [ "wss" ] );
				$this->view_data["chat_server_session"] = $wplc_chat_server_data["chat_server_session"];
				break;
		}

		$this->view_data["files_url"] = esc_url_raw( $this->wplc_settings->wplc_files_url );
		$this->view_data["secret"]    = wp_create_nonce( "wplc" );
		$this->view_data["chatParty"] = $this->wplc_settings->wplc_chat_party;

		$theme                                 = TCXThemeHelper::get_theme( $this->wplc_settings->wplc_theme );
		$this->view_data["agentColor"]             = $theme->agent_color;
		$this->view_data["clientColor"]        = $theme->client_color;
		$this->view_data["baseColor"]          = $theme->base_color;
		$this->view_data["buttonColor"]        = $theme->button_color;
		$this->view_data["gdpr_enabled"]       = $this->wplc_settings->wplc_gdpr_enabled == '1' ? "true" : "false";
		$this->view_data["gdpr_message"]       = wplc_gdpr_generate_retention_agreement_notice( $this->wplc_settings );
		$this->view_data["enable_direct_call"] = $this->wplc_settings->wplc_channel == "phone" && $this->wplc_settings->wplc_direct_call_enabled == "1" ? "true" : "false";
		$this->view_data["files_enabled"]      = $this->wplc_settings->wplc_channel != "phone" && $this->wplc_settings->wplc_ux_file_share == '1' ? "true" : "false";
		$this->view_data["rating_enabled"]     = $this->wplc_settings->wplc_channel != "phone" && $this->wplc_settings->wplc_ux_exp_rating == '1' ? "true" : "false";
		$this->view_data["ga_enabled"]         = $this->wplc_settings->wplc_enable_ga == '1' ? "true" : "false";

		$this->view_data["departments_enabled"] = $this->wplc_settings->wplc_allow_department_selection == '1' ? "true" : "false";
		$this->view_data["chatDelay"]           = intval( $this->wplc_settings->wplc_chat_delay ) * 1000;
		$this->view_data["chat_height"]         = $this->wplc_settings->wplc_chatbox_height == 0 ? $this->wplc_settings->wplc_chatbox_absolute_height . 'px'
			: $this->wplc_settings->wplc_chatbox_height * 95 / 100 . 'vh';
		$this->view_data["minimizedStyle"]      = $this->get_minimized_style();
		$this->view_data["showAgentsName"]      = $this->wplc_settings->wplc_show_agent_name == '1' ? "true" : "false";
		$this->view_data["visitor_name"]        = '';
		$this->view_data["visitor_email"]       = '';
		if ( ! $embed_code ) {
			if ( $this->wplc_settings->wplc_loggedin_user_info == '1' && is_user_logged_in() ) {
				$logged_in                        = wp_get_current_user();
				$this->view_data["visitor_name"]  = $logged_in->display_name;
				$this->view_data["visitor_email"] = $logged_in->user_email;
			} else if ( sanitize_text_field( $this->wplc_settings->wplc_require_user_info ) === 'none' || sanitize_text_field( $this->wplc_settings->wplc_require_user_info ) === 'email' ) {
				$this->view_data["visitor_name"]  = $this->wplc_settings->wplc_user_default_visitor_name;
				$this->view_data["visitor_email"] = '';
			}
		}

		$this->view_data["onlyPhone"]             = $this->wplc_settings->wplc_channel == "phone" && $this->wplc_settings->wplc_allow_chat == '0';
		$this->view_data["allowCalls"]            = $this->wplc_settings->wplc_channel == "phone" && $this->wplc_settings->wplc_allow_call == '1' ? "true" : "false";
		$this->view_data["allowVideo"]            = $this->wplc_settings->wplc_channel == "phone" && $this->wplc_settings->wplc_allow_video == '1' ? "true" : "false";
		$this->view_data["acknowledgeReceived"]   = $this->wplc_settings->wplc_channel == "phone" ? "true" : "false";
		$this->view_data["greetingMode"]          = $this->wplc_settings->wplc_greeting_mode;
		$this->view_data["offlineGreetingMode"]   = $this->wplc_settings->wplc_offline_greeting_mode;
		$this->view_data["ignoreQueueOwnership"]  = $this->wplc_settings->wplc_ignore_queue_ownership == '1' ? "true" : "false";
		$this->view_data["offline_enabled"]       = $this->wplc_settings->wplc_hide_when_offline == '1' ? "false" : "true";
		$this->view_data["messageDateFormat"]     = $this->get_date_format();
		$this->view_data["messageUserinfoFormat"] = $this->get_user_info_format();
		$this->view_data['chatLang']              = $this->wplc_settings->wplc_language;

		return $this->load_view( plugin_dir_path( __FILE__ ) . "chat_client_view.php", $return_html, $add_wrapper );
	}

	private function get_date_format() {
		$result = "none";
		if ( $this->wplc_settings->wplc_show_date && $this->wplc_settings->wplc_show_time ) {
			$result = "both";
		} else if ( $this->wplc_settings->wplc_show_date ) {
			$result = "date";
		} else if ( $this->wplc_settings->wplc_show_time ) {
			$result = "time";
		}
		return $result;
	}

	private function get_user_info_format() {
		$result = "none";
		if ( $this->wplc_settings->wplc_show_name && $this->wplc_settings->wplc_show_avatar ) {
			$result = "both";
		} else if ( $this->wplc_settings->wplc_show_avatar ) {
			$result = "avatar";
		} else if ( $this->wplc_settings->wplc_show_name ) {
			$result = "name";
		}

		return $result;
	}

	private function get_minimized_style() {
		$result = 'BubbleRight';
		switch ( $this->wplc_settings->wplc_settings_align ) {
			case "1":
			case "3":
				$result = "BubbleLeft";
				break;
			case "2":
			case "4":
				$result = "BubbleRight";
				break;
			default:
				$result = "BubbleRight";
				break;
		}

		return $result;
	}

	private function get_shadow_color( $hexColor ) {
		$hexColor = str_replace( '#', '', $hexColor );
		// Convert string to 3 decimal values (0-255)
		$rgb = array_map( 'hexdec', str_split( $hexColor, 2 ) );

		$rgb[0] += 34;
		$rgb[1] += 34;
		$rgb[2] += 17;

		$result = implode( '', array_map( 'dechex', $rgb ) );

		return '#' . $result;
	}

	private function get_secondary_gradient_color( $rgb, $darker = 1.5 ) {
		$hash = ( strpos( $rgb, '#' ) !== false ) ? '#' : '';
		$rgb  = ( strlen( $rgb ) == 7 ) ? str_replace( '#', '', $rgb ) : ( ( strlen( $rgb ) == 6 ) ? $rgb : false );
		if ( strlen( $rgb ) != 6 ) {
			return $hash . '000000';
		}
		$darker = ( $darker > 1 ) ? $darker : 1;

		list( $R16, $G16, $B16 ) = str_split( $rgb, 2 );

		$R = sprintf( "%02X", floor( hexdec( $R16 ) / $darker ) );
		$G = sprintf( "%02X", floor( hexdec( $G16 ) / $darker ) );
		$B = sprintf( "%02X", floor( hexdec( $B16 ) / $darker ) );

		return $hash . $R . $G . $B;
	}

}


?>