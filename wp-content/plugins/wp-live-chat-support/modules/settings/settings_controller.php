<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsController extends BaseController {

	public function __construct( $alias ) {
		parent::__construct( __( "Settings", 'wp-live-chat-support' ), $alias );
		$this->init_actions();
		$this->parse_action( $this->available_actions );
	}

	private function load_tabs() {
		$tab_array = array(
			0 => (object) array(
				"id"    => "tab-general",
				"icon"  => 'fas fa-cog',
				"label" => __( "General Settings", 'wp-live-chat-support' ),
				"view"  => "settings_partials/general_settings.php",
			),
			1 => (object) array(
				"id"    => "tab-chat",
				"icon"  => 'far fa-envelope',
				"label" => __( "Chat Box", 'wp-live-chat-support' ),
				"view"  => "settings_partials/chatbox_settings.php",
			),
			2 => (object) array(
				"id"    => "tab-offline",
				"icon"  => 'fa fa-book',
				"label" => __( "Offline Messages", 'wp-live-chat-support' ),
				"view"  => "settings_partials/offline_messages_settings.php",
			),
			3 => (object) array(
				"id"    => "tab-style",
				"icon"  => 'fas fa-pencil-alt',
				"label" => __( "Styling", 'wp-live-chat-support' ),
				"view"  => "settings_partials/styling_settings.php",
			),
			4 => (object) array(
				"id"    => "tab-agents",
				"icon"  => 'fa fa-users',
				"label" => __( "Agents", 'wp-live-chat-support' ),
				"view"  => "settings_partials/agents_settings.php",
			),
			5 => (object) array(
				"id"    => "tab-blocked",
				"icon"  => 'fa fa-gavel',
				"label" => __( "Blocked Visitors", 'wp-live-chat-support' ),
				"view"  => "settings_partials/blocked_visitors_settings.php",
			),

			6 => (object) array(
				"id"    => "tab-encryption",
				"icon"  => 'fa fa-lock',
				"label" => __( "Encryption", 'wp-live-chat-support' ),
				"view"  => "settings_partials/encryption_settings.php",
			),

			7  => (object) array(
				'id'    => 'tab-schedule',
				'icon'  => 'fas fa-clock',
				'label' => __( "Chat Operating Hours", 'wp-live-chat-support' ),
				"view"  => "settings_partials/business_hours_settings.php",
			),
			8  => (object) array(
				"id"    => "tab-departments",
				"icon"  => 'fa fa-university',
				"label" => __( "Departments", 'wp-live-chat-support' ),
				"view"  => "settings_partials/departments_settings.php",
			),
			10 => (object) array(
				"id"    => "tab-advanced",
				"icon"  => 'fa fa-bolt',
				"label" => __( "Advanced Features", 'wp-live-chat-support' ),
				"view"  => "settings_partials/advanced_settings.php",
			),
			11 => (object) array(
				"id"    => "tab-privacy",
				"icon"  => 'fa fa-eye',
				"label" => __( "Privacy", 'wp-live-chat-support' ),
				"view"  => "settings_partials/privacy_settings.php",
			),
			12 => (object) array(
				'id'    => 'tab-gutenberg',
				'icon'  => 'fas fa-comment-dots',
				'label' => __( 'Gutenberg Blocks', 'wp-live-chat-support' ),
				"view"  => "settings_partials/gutenberg_settings.php",
			),
			13 => (object) array(
				'id'    => 'tab-embed-code',
				'icon'  => 'fas fa-code',
				'label' => __( 'Embed Code', 'wp-live-chat-support' ),
				"view"  => "settings_partials/embed_code.php",
			),
		);

		$tab_array = array_filter( $tab_array, function ( $tab ) {
			$result = true;
			if ( $tab->id == 'tab-advanced' ) {
				$result = count( explode( ',', WPLC_ENABLE_CHANNELS ) ) > 1;
			} else if ( $tab->id == 'tab-embed-code' ) {
				$result = $this->wplc_settings->wplc_channel == 'phone';
			} else if ( in_array( $tab->id, array(
				'tab-agents',
				'tab-blocked',
				'tab-encryption',
				'tab-departments'
			) ) ) {
				$result = $this->wplc_settings->wplc_channel != 'phone';
			}

			return $result;
		} );

		return $tab_array;
	}

	private function load_post_types() {
		$result = array_map( function ( $post_type ) {
			$post_type_result           = new stdClass();
			$post_type_result->name     = $post_type->name;
			$post_type_result->excluded = in_array( $post_type->name, $this->wplc_settings->wplc_exclude_post_types );

			return $post_type_result;
		}, get_post_types(
			array(
				'_builtin' => false,
				'public'   => true,
			),
			'objects'
		) );

		return $result;
	}

	private function load_pages() {
		$result = array_map( function ( $page ) {
			$page_result           = new stdClass();
			$page_result->id       = $page->ID;
			$page_result->name     = trim( $page->post_title ) !== "" ? esc_html( $page->post_title ) : __( "no title", 'wp-live-chat-support' );
			$page_result->included = in_array( $page->ID, explode( ",", $this->wplc_settings->wplc_include_on_pages ) );
			$page_result->excluded = in_array( $page->ID, explode( ",", $this->wplc_settings->wplc_exclude_from_pages ) );

			return $page_result;
		}, get_pages() );

		return $result;
	}

	private function load_ringtones() {
		$result                       = new stdClass();
		$result->ringtones            = TCXRingtonesHelper::get_available_sounds( WPLC_PLUGIN_DIR . "/includes/sounds/", 'default_ring' );
		$result->messagetones         = TCXRingtonesHelper::get_available_sounds( WPLC_PLUGIN_DIR . "/includes/sounds/message/", 'default_messagetone' );
		$result->ringtone_selected    = isset( $this->wplc_settings->wplc_ringtone ) ? TCXRingtonesHelper::get_ringtone_name( $this->wplc_settings->wplc_ringtone ) : '';
		$result->messagetone_selected = isset( $this->wplc_settings->wplc_messagetone ) ? TCXRingtonesHelper::get_messagetone_name( $this->wplc_settings->wplc_messagetone ) : '';

		return $result;
	}

	private function load_users() {
		$agents        = TCXAgentsHelper::get_agent_users();
		$users         = get_users();
		$notAgentUsers = array();

		foreach ( $users as $user ) {
			$user->isOnline = TCXAgentsHelper::agent_is_online( $user->ID );
			if ( ! $user->has_cap( "wplc_ma_agent" ) ) {
				$notAgentUsers[] = $user;
			}
		}

		return (object) array(
			"Agents"        => $agents,
			"NotAgentUsers" => $notAgentUsers,
		);

	}

	private function load_blocked_ips() {
		$ip_addresses = $this->wplc_settings->wplc_banned_ips;

		$result = array_map( function ( $ip ) {
			return esc_textarea( $ip );
		}, $ip_addresses );

		return implode( "\r\n", $result );
	}

	private function load_nonces() {
		$result                    = new stdClass();
		$result->encryption_key    = wp_create_nonce( 'generate_new_encryption_key' );
		$result->node_server_token = wp_create_nonce( 'generate_new_token' );

		return $result;
	}

	private function load_departments() {
		return TCXDepartmentsData::get_departments( $this->db );
	}

	private function load_gutenberg_settings() {
		$this->wplc_settings->wplc_gutenberg_settings['default_logo'] = WPLC_PLUGIN_URL . 'images/wplc_loading.png';
		$this->wplc_settings->wplc_gutenberg_settings['text'] = esc_html( $this->wplc_settings->wplc_gutenberg_settings['text'] );
		$this->wplc_settings->wplc_gutenberg_settings['icon'] = isset($this->wplc_settings->wplc_gutenberg_settings['icon'] )? esc_html( $this->wplc_settings->wplc_gutenberg_settings['icon'] ):'';
		$this->wplc_settings->wplc_gutenberg_settings['custom_html'] = stripslashes( $this->wplc_settings->wplc_gutenberg_settings['custom_html'] );
		$this->wplc_settings->wplc_gutenberg_settings['theme'] = esc_attr( $this->wplc_settings->wplc_gutenberg_settings['theme'] );
		$this->wplc_settings->wplc_gutenberg_settings['size'] = esc_attr( $this->wplc_settings->wplc_gutenberg_settings['size'] );
		return $this->wplc_settings->wplc_gutenberg_settings;
	}

	private function load_pbx_mode() {
		$mode     = 'none';
		$mask_str = $this->wplc_settings->wplc_allow_call ? '1' : '0';
		$mask_str .= $this->wplc_settings->wplc_allow_chat ? '1' : '0';
		$mask_str .= $this->wplc_settings->wplc_allow_video ? '1' : '0';
		switch ( $mask_str ) {
			case '111':
				$mode = 'all';
				break;
			case '011':
				$mode = 'videochat';
				break;
			case '110':
				$mode = 'phonechat';
				break;
			case '010':
				$mode = 'chat';
				break;
			case '100':
				$mode = 'phone';
				break;
		}

		return $mode;
	}

	private function load_icons() {
		$result                       = array();
		$result["default_icon"]       = file_get_contents( WPLC_PLUGIN_DIR . '/images/svgs/wplc_icon.svg' );
		$result["bubble_icon"]        = file_get_contents( WPLC_PLUGIN_DIR . '/images/svgs/wplc_icon_bubble.svg' );
		$result["double_bubble_icon"] = file_get_contents( WPLC_PLUGIN_DIR . '/images/svgs/wplc_icon_double_bubble.svg' );

		return $result;
	}

	private function load_languages(){
		$result = array();

		array_push($result,(object)array('name' => 'English', 'alias' => 'en'));
		array_push($result,(object)array('name' => 'Spanish', 'alias' => 'es'));
		array_push($result,(object)array('name' => 'German', 'alias' => 'de'));
		array_push($result,(object)array('name' => 'French', 'alias' => 'fr'));
		array_push($result,(object)array('name' => 'Italian', 'alias' => 'it'));
		array_push($result,(object)array('name' => 'Polish', 'alias' => 'pl'));
		array_push($result,(object)array('name' => 'Russian', 'alias' => 'ru'));
		array_push($result,(object)array('name' => 'Portugal', 'alias' => 'pt'));
		array_push($result,(object)array('name' => 'Chinese', 'alias' => 'zh'));

		return $result;
	}

	private function validate_business_hours( $schedules ) {
		$found_conflict  = false;
		$unable_to_parse = false;
		if ( is_array( $schedules ) ) {
			foreach ( $schedules as $day => $dayschedule ) {
				if ( is_array( $dayschedule ) ) {
					$dayschedule = array_map( function ( $span ) use ( &$unable_to_parse ) {
						$result       = new stdClass();
						$result->code = $span->code;
						if ( ! is_int( $span->from->h + 0 ) || ! is_int( $span->from->m + 0 ) || ! is_int( $span->to->m + 0 ) || ! is_int( $span->to->m + 0 ) ) {
							$unable_to_parse = true;
						} else {
							$result->start = gmmktime( $span->from->h, $span->from->m, 0, 1, 1, 2000 );
							$result->end   = gmmktime( $span->to->h, $span->to->m, 0, 1, 1, 2000 );
						}

						return $result;
					}, $dayschedule );

					if ( ! $unable_to_parse ) {
						foreach ( $dayschedule as $working_span_a ) {
							if ( $working_span_a->start >= $working_span_a->end ) {
								$found_conflict = true;
								break;
							}
							foreach ( $dayschedule as $working_span_b ) {
								if ( $working_span_b->start >= $working_span_b->end ) {
									$found_conflict = true;
									break;
								}
								if ( $working_span_a->code != $working_span_b->code ) {
									if ( $this->check_conflict( $working_span_a, $working_span_b ) ) {
										$found_conflict = true;
										break;
									}
								}
							}
							if ( $found_conflict ) {
								break;
							}
						}
					}
					if ( $found_conflict || $unable_to_parse ) {
						break;
					}
				}
			}
		}

		return $found_conflict ? 'CONFLICT' : ( $unable_to_parse ? 'PARSE_ERROR' : 'OK' );
	}

	private function validate_facebook_url( $fb_url ) {
		return preg_match( "/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i", $fb_url ) === 1;
	}

	private function validate_twitter_url( $tw_url ) {
		return preg_match( "/^(https?:\/\/)?((w{3}\.)?)twitter.com\/.*/i", $tw_url ) === 1;
	}

	private function check_conflict( $span_a, $span_b ) {
		$result = false;
		if ( $span_a->start < $span_b->start ) {
			if ( $span_a->end > $span_b->start ) {
				$result = true;
			}
		} else {
			if ( $span_b->end > $span_a->start ) {
				$result = true;
			}
		}

		return $result;
	}

	public function save_settings( $data ) {
		$settings_types = TCXSettings::getSettingsTypes();
		$error          = $this->validation( $data );
		if ( $error->ErrorFound ) {
			$this->view_data["error"] = $error;

			return;
		}
		$data     = $this->pre_save_handle( $data );
		$settings = TCXSettings::getSettings();

		foreach ( $data as $key => $value ) {
			$setting_value = TCXUtilsHelper::get_setting_value_sanitized( $value, $key, $settings_types );
			if ( $setting_value !== null ) {
				$settings->$key = $setting_value;
			}
		}
		update_option( 'WPLC_JSON_SETTINGS', TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $settings ), JSON_UNESCAPED_UNICODE ) );
		wp_cache_delete( 'alloptions', 'options' );

		TCXUtilsHelper::generate_settings_guidJS();

		TCXWebhookHelper::send_webhook( WebHookTypes::SETTINGS_CHANGED, array( "user_id" => get_current_user_id() ) );
		//vd($data, true);
	}

	public function view( $return_html = false, $add_wrapper = true ) {
		global $wplc_base_file;
		$this->view_data["channel_url"]           = $this->wplc_settings->wplc_channel_url . '#' . $this->wplc_settings->wplc_chat_party;
		$this->view_data["chat_client_component"] = new ChatClientController( "chat-embed-code", "embed_view" );
		$this->view_data["nonces"]                = $this->load_nonces();
		$this->view_data["tabs"]                  = $this->load_tabs();
		$this->view_data["wp_posts_types"]        = $this->load_post_types();
		$this->view_data["wp_pages"]              = $this->load_pages();
		$this->view_data["wplc_ringtones"]        = $this->load_ringtones();
		$this->view_data["blocked_ips"]           = $this->load_blocked_ips();
		$this->view_data["departments"]           = $this->load_departments();

		$users_data                                      = $this->load_users();
		$this->view_data["agents_array"]                 = $users_data->Agents;
		$this->view_data["not_agent_users"]              = $users_data->NotAgentUsers;
		$this->view_data["times"]                        = TCXUtilsHelper::get_times_array();
		$this->view_data["business_hours_overlap_found"] = false;

		$this->view_data["wplc_environment"] = intval( $this->wplc_settings->wplc_environment );

		$this->view_data["node_server_token"] = TCXUtilsHelper::node_server_token_get();
		$this->view_data["typing_enable"]     = ! ( $this->wplc_settings->wplc_gdpr_enabled || ! $this->wplc_settings->wplc_channel === 'mcu' );
		$this->view_data["gutenberg"]         = $this->load_gutenberg_settings();
		$this->view_data["save_action_url"]   = $this->wplc_settings->getSaveUrl();
		$this->view_data["wplc_pbx_mode"]     = $this->load_pbx_mode();

		$this->view_data["function_time_limit_missing"] = ! function_exists( 'set_time_limit' );
		$this->view_data["config_safe_mode_enabled"]    = ini_get( 'safe_mode' );

		//Only show config warnings messages to Legacy users as they will be affected, not Node users.
		$this->view_data["show_config_warning"] = is_admin() && $this->wplc_settings->wplc_channel != 'mcu' && ( $this->view_data["function_time_limit_missing"] || $this->view_data["config_safe_mode_enabled"] );

		$this->view_data["call_us_file"]        = wplc_plugins_url( '/modules/chat_client/js/callus.js', $wplc_base_file );
		$chat_server_error                      = get_option( 'WPLC_NO_SERVER_MATCH', false );
		$this->view_data["disable_chat_server"] = $chat_server_error === true || $chat_server_error === 'true';

		$this->view_data["icons"] = $this->load_icons();

		$this->view_data["agent_logo"]=$this->wplc_settings->wplc_agent_logo!==''? trim( urldecode( $this->wplc_settings->wplc_agent_logo ) ) : wplc_plugins_url( '/images/operatorIcon.png', $wplc_base_file );
		$this->view_data["agent_logo_value"]=$this->wplc_settings->wplc_agent_logo!==''? trim( urldecode( $this->wplc_settings->wplc_agent_logo ) ) : '';

		$this->view_data["themes"] = TCXTheme::available_themes();

		$this->view_data["wplc_languages"] = $this->load_languages();

		//We are adding schedule on javascript here as an exception to general practice of loading data when page loads because schedule is part of save and if we load it in
		// page after a save action we are getting previous values till next page refresh
		wp_localize_script( 'wplc-settings', 'bh_schedules', $this->wplc_settings->wplc_bh_schedule );

		return $this->load_view( plugin_dir_path( __FILE__ ) . "settings_view.php", $return_html, $add_wrapper );
	}

	private function init_actions() {
		$saveParams                = [];
		$saveParams[]              = isset( $_POST ) && ! empty( $_POST ) ? $_POST : null;
		$this->available_actions[] = new TCXPageAction( "save_settings", 9, "saveSettings", 'save_settings', $saveParams );
	}

	private function validation( $data ) {
		$result = new TCXError();
		if ( $data == null ) {
			$result->ErrorFound      = true;
			$result->ErrorHandleType = "Redirect";
			$result->ErrorData->url  = admin_url( "admin.php?page=wplivechat-menu-settings" );
		} else {
			if ( $this->wplc_settings->wplc_channel === 'phone' ) {
				if ( ! $this->validate_pbx_url( $data["wplc_channel_url"] ) ) {
					$result = TCXError::createShowError( __( "PBX Url is invalid.", 'wp-live-chat-support' ) );
				}
			} else if ( $this->wplc_settings->wplc_channel !== 'phone' ) {
				if ( strlen( $data["wplc_pro_na"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Intro message', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_new_chat_ringer_count"] ) == 0 || intval( $data["wplc_new_chat_ringer_count"] ) < 0 || intval( $data["wplc_new_chat_ringer_count"] ) > 20 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' must has a value between %s and %s.", 'wp-live-chat-support' ), __( 'Number of chat rings', 'wp-live-chat-support' ), '0', '20' ) );
				} else if ( !$this->validate_offline_emails($data["wplc_pro_chat_email_address"] ) ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' contains invalid mail address.", 'wp-live-chat-support' ), __( 'Send to agent(s)', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_offline_finish_message"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Submitted message', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_ringtone"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Incoming chat ring tone', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_messagetone"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Incoming message tone', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_default_department"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Default Department', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_send_transcripts_to"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Send transcripts to', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_et_email_header"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Email header', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_et_email_footer"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Email footer', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_et_email_body"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Email body', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_text_chat_ended"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'On chat end message', 'wp-live-chat-support' ) ) );
				}
			}
			if ( ! $result->ErrorFound ) {
				$business_hours_validation = $data["wplc_bh_enable"] === "1" ? $this->validate_business_hours( json_decode( stripslashes( $data["wplc_bh_schedule"] ) ) ) : "";
				if ( ! in_array( $data["wplc_channel"], explode( ',', WPLC_ENABLE_CHANNELS ) ) ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' is not valid.", 'wp-live-chat-support' ), __( "Select your chat server", 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_settings_enabled"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Chat enabled', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_settings_align"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Alignment', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_settings_base_color"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Chat main color', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_settings_agent_color"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Agent message color', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_settings_client_color"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Client message color', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_require_user_info"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Required Chat Box Fields', 'wp-live-chat-support' ) ) );
				} else if($data["wplc_require_user_info"]!=='name' && $data["wplc_require_user_info"]!=='both'){
					if ( strlen( $data["wplc_user_default_visitor_name"] ) == 0 ) {
						$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Default visitor name', 'wp-live-chat-support' ) ) );
					} else if ($data["wplc_require_user_info"]==='none' && strlen( $data["wplc_user_alternative_text"] ) == 0 ) {
						$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Replacement Text', 'wp-live-chat-support' ) ) );
					}
				} else if ( strlen( $data["wplc_chat_title"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Chat box title', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_button_start_text"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Start chat button label', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_chat_intro"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Chat box intro', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_welcome_msg"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Welcome message', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_animation"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Chat box animation', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_user_no_answer"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Agent no answer message', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_gdpr_notice_company"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Organization name', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_gdpr_notice_retention_purpose"] ) == 0 ) {
					$result = TCXError::createShowError( sprintf( __( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Data retention purpose', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_agent_default_name"] ) > 250 ) {
					$result = TCXError::createShowError( sprintf( __( "'%s' field can't be longer than 250 characters.", 'wp-live-chat-support' ), __( 'Default agent\'s name', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_gdpr_notice_text"] ) > 1000 ) {
					$result = TCXError::createShowError( sprintf( __( "'%s' field can't be longer than 1000 characters.", 'wp-live-chat-support' ), __( 'GDPR notice to visitors', 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_social_fb"] ) > 0 && ! $this->validate_facebook_url( $data["wplc_social_fb"] ) ) {
					$result = TCXError::createShowError( sprintf( __( "'%s' field does not contains a valid FaceBook Url", 'wp-live-chat-support' ), __( "Facebook URL", 'wp-live-chat-support' ) ) );
				} else if ( strlen( $data["wplc_social_tw"] ) > 0 && ! $this->validate_twitter_url( $data["wplc_social_tw"] ) ) {
					$result = TCXError::createShowError( sprintf( __( "'%s' field does not contains a valid Twitter Url", 'wp-live-chat-support' ), __( "Twitter URL", 'wp-live-chat-support' ) ) );
				} else if ( $business_hours_validation === "CONFLICT" ) {
					$result = TCXError::createShowError( __( "There are conflicts between scheduled chat operating hours.", 'wp-live-chat-support' ) );
				} else if ( $business_hours_validation === "PARSE_ERROR" ) {
					$result = TCXError::createShowError( __( "Operating hours inserted are invalid.", 'wp-live-chat-support' ) );
				}
			}
		}

		return $result;
	}

	private function pre_save_handle( $data ) {
		if ( ! array_key_exists( 'wplc_channel', $data ) ) {
			$data['wplc_channel'] = 'mcu';
		}

		if ( $data['wplc_channel'] != $this->wplc_settings->wplc_channel ) {
			TCXChatHelper::complete_all_ended_chats();
		}

		if ( $data['wplc_channel'] === 'phone' ) {
			$url_data                 = TCXUtilsHelper::wplc_parse_click2callUrl( $data['wplc_channel_url'] );
			$data['wplc_chat_party']  = $url_data['chat_party'];
			$data['wplc_channel_url'] = $url_data['channel_url'];
			$data['wplc_files_url']   = $url_data['files_url'];
			$data['wplc_allow_department_selection']   = false;
		} else {
			$data['wplc_chat_party']  = '';
			$data['wplc_channel_url'] = admin_url( 'admin-ajax.php' );
			$data['wplc_files_url']   = WPLC_PLUGIN_URL;
		}


		$data['wplc_include_on_pages']   = array_key_exists( 'wplc_include_on_pages', $data ) && is_array( $data['wplc_include_on_pages'] ) ? sanitize_text_field( implode( ",", $data['wplc_include_on_pages'] ) ) : "";
		$data['wplc_exclude_from_pages'] = array_key_exists( 'wplc_exclude_from_pages', $data ) && is_array( $data['wplc_exclude_from_pages'] ) ? sanitize_text_field( implode( ",", $data['wplc_exclude_from_pages'] ) ) : "";
		$data['wplc_exclude_post_types'] = array_key_exists( 'wplc_exclude_post_types', $data ) ? $data['wplc_exclude_post_types'] : array();

		if ( array_key_exists( 'wplc_pbx_mode', $data ) ) {
			$pbx_mode_settings        = TCXUtilsHelper::wplc_parse_pbx_mode( $data['wplc_pbx_mode'] );
			$data['wplc_allow_chat']  = $pbx_mode_settings['chat'];
			$data['wplc_allow_call']  = $pbx_mode_settings['call'];
			$data['wplc_allow_video'] = $pbx_mode_settings['video'];
		}


		if ( array_key_exists( 'wplc_banned_ips', $data ) ) {
			$banned_ips              = array_map( 'sanitize_textarea_field', explode( "\r\n", $data['wplc_banned_ips'] ) );
			$data['wplc_banned_ips'] = $banned_ips;
		} else {
			$data['wplc_banned_ips'] = array();
		}

		if ( ( array_key_exists( 'wplc_agents_to_add', $data ) && ! empty( $data["wplc_agents_to_add"] ) )
		     || ( array_key_exists( 'wplc_agents_to_remove', $data ) && ! empty( $data["wplc_agents_to_remove"] ) ) ) {
			$this->handle_agents_changes( $data["wplc_agents_to_add"], $data["wplc_agents_to_remove"] );
		}

		if ( $data["wplc_gdpr_notice_text"] === wplc_gdpr_generate_retention_agreement_notice( $this->wplc_settings ) ) {
			unset( $data["wplc_gdpr_notice_text"] );
		}

		if ( $data["wplc_theme"] && $data["wplc_theme"] !== 'custom' ) {
			$theme = TCXTheme::get_theme($data["wplc_theme"]);
			if($theme!==null)
			{
				$data['wplc_settings_base_color']= $theme->base_color;
				$data['wplc_settings_button_color']= $theme->button_color;
				$data['wplc_settings_agent_color']= $theme->agent_color;
				$data['wplc_settings_client_color']= $theme->client_color;
			}
		}


		return $data;
	}

	private function handle_agents_changes( $to_add, $to_remove ) {
		foreach ( array_unique( explode( ',', $to_remove ) ) as $agent_to_remove ) {
			TCXAgentsHelper::revoke_agent_from_user( $agent_to_remove );
		}

		foreach ( array_unique( explode( ',', $to_add ) ) as $agent_to_add ) {
			TCXAgentsHelper::set_user_as_agent( $agent_to_add );
		}

	}

	private function validate_pbx_url( $pbxUrl ) {
		return preg_match( "/^(http:\/\/|https:\/\/){1}(([\-\.]?)[a-zA-Z0-9.-])+(:[0-9]{1,5})?(\/[a-zA-Z0-9-._~:\/?#@!$&*=;+%()']*)?\/callus\/#([a-zA-Z0-9.-])*$/", $pbxUrl ) === 1;

	}

	private function validate_offline_emails($emails_literal){
		$result = true;
		if ( strlen($emails_literal ) == 0 ) {
			$result = false;
		}else {
			$email_addresses = preg_split('(;|,)', $emails_literal);
			if ($email_addresses) {
				foreach($email_addresses as $email) {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$result = false;
						break;
					}
				}
			}else{
				$result = false;
			}
		}
		return $result;
	}

}