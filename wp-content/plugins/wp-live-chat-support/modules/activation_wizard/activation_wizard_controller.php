<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActivationWizardController extends BaseController {

	public function __construct( $alias ) {
		if ( get_option( "WPLC_SETUP_WIZARD_RUN", 'NOTEXIST' ) === "1" ) {
			exit( wp_redirect( admin_url( 'admin.php?page=wplivechat-menu' ) ) );
		}
		parent::__construct( __( "Activation Wizard", 'wp-live-chat-support' ), $alias );
		$this->init_actions();
		$this->parse_action( $this->available_actions );
	}

	private function load_steps() {
		//Channels :: wp,phone,mcu
		$steps_array = array(
			0 => (object) array(
				"id"           => "step-channel",
				"icon"         => 'progress1.svg',
				"label"        => __( "Select Channel", 'wp-live-chat-support' ),
				"view"         => "wizard_partials/channel_selection.php",
				"channels"     => "*",
				"jsvalidation" => ""
			),
			1 => (object) array(
				"id"           => "step-agents",
				"icon"         => 'progress2.svg',
				"label"        => __( "Invite Agents", 'wp-live-chat-support' ),
				"view"         => "wizard_partials/invite_agents.php",
				"channels"     => "wp,mcu",
				"jsvalidation" => ""
			),
			2 => (object) array(
				"id"           => "step-pbx",
				"icon"         => 'progress2.svg',
				"label"        => __( "PBX Settings", 'wp-live-chat-support' ),
				"view"         => "wizard_partials/pbx_settings.php",
				"channels"     => "phone",
				"jsvalidation" => "validatePbx"
			),
			3 => (object) array(
				"id"       => "step-auth",
				"icon"     => 'progress3.svg',
				"label"    => __( "Chat Settings", 'wp-live-chat-support' ),
				"channels" => "phone",
				/*"view"         => "wizard_partials/auth_settings.php",
				"jsvalidation" => "",*/
				"children" => (object) array(
					1 => (object) array(
						"id"           => "step-auth-first",
						"view"         => "wizard_partials/auth_settings.php",
						"channels"     => "phone",
						"jsvalidation" => "",
					),
					2 => (object) array(
						"id"           => "step-auth-second",
						"view"         => "wizard_partials/call_settings.php",
						"channels"     => "phone",
						"jsvalidation" => "",
					)
				)
			),
			4 => (object) array(
				"id"           => "step-styling",
				"icon"         => 'progress4.svg',
				"label"        => __( "Style Settings", 'wp-live-chat-support' ),
				"view"         => "wizard_partials/style_settings.php",
				"channels"     => "*",
				"jsvalidation" => ""
			),
			5 => (object) array(
				"id"           => "step-finish",
				"icon"         => 'progress6.svg',
				"label"        => __( "Finish", 'wp-live-chat-support' ),
				"view"         => "wizard_partials/wizard_finish.php",
				"channels"     => "*",
				"jsvalidation" => ""
			),
		);

		array_walk( $steps_array, function ( $step ) use ( &$result ) {
			$channels = explode( ',', $step->channels );
			foreach ( $channels as $channel ) {
				if ( $channel == "*" || $channel == $this->wplc_settings->wplc_channel ) {
					$result[] = $step;
					break;
				}
			}
		} );

		return $steps_array;
	}

	public function save_activation_settings( $data ) {

		$result                      = array();
		$result['Agents']            = array();
		$result['Agents']['Error']   = array();
		$result['Agents']['Success'] = array();

		if ( array_key_exists( 'agentEntry', $data ) && is_array( $data['agentEntry'] ) ) {
			foreach ( $data['agentEntry'] as $agent ) {
				if ( empty( $agent['Username'] ) || empty( $agent['Name'] ) || empty( $agent['Email'] ) || empty( $agent['AgentRole'] ) ) {
					continue;
				}
				if ( get_user_by( 'login', $agent['Username'] ) !== false ) {
					$result['Agents']['Error'][ $agent['Username'] ] = "There is already exists a user with username " . esc_html( $agent['Username'] );
					continue;
				} else {
					$existing_user_with_email = get_user_by( 'email', $agent['Email'] );
					if ( $existing_user_with_email !== false ) {
						TCXAgentsHelper::set_user_as_agent( $existing_user_with_email->ID );
						update_user_meta( $existing_user_with_email->ID, 'first_name', sanitize_text_field( $agent['Name'] ) );
						$result['Agents']['Success'][ $agent['Username'] ] = esc_html( $agent['Username'] );
					} else {
						$agentPass   = TCXUtilsHelper::generateRandomString( 8 );
						$new_user_id = wp_create_user( sanitize_user( $agent['Username'] ), $agentPass, sanitize_email( $agent['Email'] ) );
						if ( $new_user_id instanceof WP_Error ) {
							$result['Agents']['Error'][ $agent['Username'] ] = $new_user_id->get_error_message();
							continue;
						} else {
							TCXAgentsHelper::set_user_as_agent( $new_user_id );
							update_user_meta( $new_user_id, 'first_name', sanitize_text_field( $agent['Name'] ) );
							$new_user = get_user_by( 'id', $new_user_id );

							if ( $agent['AgentRole'] === 'admin' ) {
								$new_user->set_role( 'administrator' );
							} else {
								$new_user->set_role( 'contributor' );
							}

							TCXAgentsHelper::new_agent_email( sanitize_user( $agent['Username'] ), sanitize_text_field( $agent['Name'] ), $agentPass, sanitize_email( $agent['Email'] ) );
							$result['Agents']['Success'][ $agent['Username'] ] = esc_html( $agent['Username'] );
						}
					}
				}
			}
		}

		if ( array_key_exists( 'wplc_pbx_exist', $data ) ) {
			TCXSettings::setSettingValue( 'wplc_channel', $data['wplc_pbx_exist'] );
			$result['Channel'] = true;
		}

		if ( array_key_exists( 'wplc_theme', $data ) ) {
			if ( TCXSettings::setTheme( $data["wplc_theme"] ) ) {
				$result['Theme'] = true;
			}

			if ( $data["wplc_theme"] === 'custom' ) {
				if ( array_key_exists( 'wplc_settings_base_color', $data ) ) {
					$color = sanitize_hex_color( $data['wplc_settings_base_color'] );
					if ( $color !== '' && $color !== null ) {
						TCXSettings::setSettingValue( 'wplc_settings_base_color', $color );
						$result['BaseColor'] = true;
					} else {
						$result['BaseColor'] = false;
					}
				}

				if ( array_key_exists( 'wplc_settings_agent_color', $data ) ) {
					$color = sanitize_hex_color( $data['wplc_settings_agent_color'] );
					if ( $color !== '' && $color !== null ) {
						TCXSettings::setSettingValue( 'wplc_settings_agent_color', $color );
						$result['AgentColor'] = true;
					} else {
						$result['AgentColor'] = false;
					}
				}

				if ( array_key_exists( 'wplc_settings_client_color', $data ) ) {
					$color = sanitize_hex_color( $data['wplc_settings_client_color'] );
					if ( $color !== '' && $color !== null ) {
						TCXSettings::setSettingValue( 'wplc_settings_client_color', $color );
						$result['ClientColor'] = true;
					} else {
						$result['ClientColor'] = false;
					}
				}

				if ( array_key_exists( 'wplc_settings_button_color', $data ) ) {
					$color = sanitize_hex_color( $data['wplc_settings_button_color'] );
					if ( $color !== '' && $color !== null ) {
						TCXSettings::setSettingValue( 'wplc_settings_button_color', $color );
						$result['ButtonsColor'] = true;
					} else {
						$result['ButtonsColor'] = false;
					}
				}
			}
		}


		if ( array_key_exists( 'clickToTalkUrl', $data ) ) {
			$url_data = TCXUtilsHelper::wplc_parse_click2callUrl( $data['clickToTalkUrl'] );
			TCXSettings::setSettingValue( 'wplc_chat_party', $url_data['chat_party'] );
			TCXSettings::setSettingValue( 'wplc_channel_url', $url_data['channel_url'] );
			TCXSettings::setSettingValue( 'wplc_files_url', $url_data['files_url'] );
			$result['clickToTalkUrl'] = true;
		}
		else
        {
            TCXSettings::setSettingValue( 'wplc_files_url',WPLC_PLUGIN_URL );
        }

		if ( array_key_exists( 'wplc_c2c_mode', $data ) ) {
			$settings = TCXUtilsHelper::wplc_parse_pbx_mode( $data['wplc_c2c_mode'] );
			TCXUtilsHelper::wplc_set_pbx_mode_settings( $settings['call'], $settings['chat'], $settings['video'] );
			$result['c2cMode'] = true;
		}

		if ( array_key_exists( 'wplc_auth_mode', $data ) ) {
			TCXSettings::setSettingValue( 'wplc_require_user_info', $data['wplc_auth_mode'] );
			$result['c2cAuthType'] = true;
		}

		if ( array_key_exists( 'wplc_direct_call_mode', $data ) ) {
			TCXSettings::setSettingValue( 'wplc_direct_call_enabled', $data['wplc_direct_call_mode']=="1" );
			$result['directCall'] = true;
		}

		$this->view_data['activation_result'] = $result;
		TCXSettings::setSettingValue( 'wplc_settings_enabled', 1 );
		update_option( "WPLC_SETUP_WIZARD_RUN", true );

		wp_cache_delete( 'alloptions', 'options' );
	}

	public function view( $return_html = false, $add_wrapper = true ) {
		if ( $this->selected_action->name == "save_activation_settings" ) {
			$this->view_data['single_settings'] = array(
				'Channel'        => __( 'Channel Selection', 'wp-live-chat-support' ),
				'BaseColor'      => __( 'Chat base Color', 'wp-live-chat-support' ),
				'ButtonsColor'   => __( 'Chat buttons color', 'wp-live-chat-support' ),
				'AgentColor'     => __( 'Agent chat bubble', 'wp-live-chat-support' ),
				'ClientColor'    => __( 'Visitor chat bubble', 'wp-live-chat-support' ),
				'clickToTalkUrl' => __( 'Click2Talk url', 'wp-live-chat-support' ),
				'c2cMode'        => __( 'Chat mode', 'wp-live-chat-support' ),
				'c2cAuthType'    => __( 'Chat authentication fields', 'wp-live-chat-support' ),
				'Theme'          => __( "Theme", "wp-live-chat-support" ),
				'directCall'     => __( "Direct Calls", "wp-live-chat-support" ),
			);
			$this->view_data['fully_completed'] = count( $this->view_data['activation_result']['Agents']['Error'] ) == 0;
			$this->view_data['new_agents']      = count( $this->view_data['activation_result']['Agents']['Success'] ) == 0;

			if ( $this->wplc_settings->wplc_channel === 'mcu' ) {
				TCXUtilsHelper::get_mcu_data( $this->wplc_settings->wplc_socket_url, $this->wplc_settings->wplc_chat_server_session, true );
			} else {
				wplc_check_guid( true );
			}

			return $this->load_view( plugin_dir_path( __FILE__ ) . "success_wizard_view.php", $return_html, $add_wrapper );
		} else {
			$this->view_data["steps"]           = $this->load_steps();
			$this->view_data["saveUrl"]         = admin_url( "admin.php?page=wplc-getting-started&wplc_action=save_activation_settings&nonce=" . wp_create_nonce( "saveActivationSettings" ) );
			$this->view_data["active_channels"] = count( explode( ',', str_replace( 'wp,', '', WPLC_ENABLE_CHANNELS ) ) );

			$this->view_data["themes"] = TCXTheme::available_themes();

			return $this->load_view( plugin_dir_path( __FILE__ ) . "activation_wizard_view.php", $return_html, $add_wrapper );
		}
	}

	private function init_actions() {
		$this->available_actions   = [];
		$this->available_actions[] = new TCXPageAction( "save_activation_settings", 9, "saveActivationSettings", 'save_activation_settings', array( 0 => $_POST ) );

	}

}


?>