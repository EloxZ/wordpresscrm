<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXUpdater {

	private $wplc_migration_functions;

	public function __construct() {
		$this->wplc_migration_functions = array(
			"9.0.0" => array( "wplc_migrate_settings_9_0", "migrate_old_user_info", "wplc_cleanup_old_options","wplc_migrate_old_business_hours", "wplc_migrate_quick_responses" ),
			"9.0.4" => array( "wplc_migrate_settings_9_0_4" ),
			"9.0.8" => array( "wplc_fix_trailing_zeros_business_hours" ),
			"9.0.25" => array( "wplc_migrate_settings_9_0_25" ),
			"9.1.1" => array( "wplc_migrate_settings_9_1_1","wplc_upgrade_tables_to_utf8mb4" ),
			"9.2.0" => array( "wplc_migrate_settings_9_2_0" ),
			"9.3.0" => array( "wplc_migrate_settings_9_3_0" ),
			"9.4.0" => array( "wplc_migrate_settings_9_4_0","wplc_init_files_table_data" ),
		);
	}

	public function versionMigration( $wplc_settings ) {


		$old_version = get_option( "wplc_current_version", WPLC_PLUGIN_VERSION );
		if ( $old_version != WPLC_PLUGIN_VERSION ) {
			//Already exists a version so we have to skip activation wizard mode.
			update_option( "WPLC_SETUP_WIZARD_RUN", true );
		}

		$keys          = array_keys( $this->wplc_migration_functions );
		$filtered_keys = array_filter( $keys, function ( $key ) use ( $old_version ) {
			return version_compare( $key, $old_version ) >= 0;
		} );
		uksort( $filtered_keys, "version_compare" );


		$filtered_migration_functions = array();
		foreach ( $filtered_keys as $key ) {
			$filtered_migration_functions[] = $this->wplc_migration_functions[ $key ];
		}

		foreach ( $filtered_migration_functions as $migration_functions ) {
			foreach ( $migration_functions as $func ) {
				$dbSettings    = TCXSettings::getDbSettings();
				$wplc_settings = $this->$func( $dbSettings, $wplc_settings );
			}
		}
		update_option( "wplc_current_version", WPLC_PLUGIN_VERSION );
		TCXSettings::initSettings();
	}

	public function wplc_migrate_quick_responses( $dbSettings, $wplc_settings ) {

		global $wpdb;
		$quick_responses = get_posts( array(
				'post_type'   => 'wplc_quick_response',
				'post_status' => array(
					'publish',
					'pending',
					'draft',
					'auto-draft',
					'future',
					'private',
					'inherit',
					'trash'
				)
			)
		);
		foreach ( $quick_responses as $quick_response ) {
			$sort_num                         = get_post_meta( $quick_response->ID, "wplc_quick_response_number", true );
			$quick_response_to_save           = new TCXQuickResponse();
			$quick_response_to_save->id       = - 1;
			$quick_response_to_save->title    = $quick_response->post_title;
			$quick_response_to_save->response = $quick_response->post_content;
			$quick_response_to_save->sort     = intval( $sort_num == '' ? '0' : $sort_num );
			$quick_response_to_save->status   = $quick_response->post_status == 'publish' ? 1 : 0;

			TCXQuickResponsesData::add_quick_response( $wpdb, $quick_response_to_save );
			wp_delete_post( $quick_response->ID, true );
		}
		if ( post_type_exists( 'wplc_quick_response' ) ) {
			unregister_post_type( 'wplc_quick_response' );
		}

		$users = get_users();
		foreach ( $users as $user ) {
			$user->remove_cap( "edit_wplc_quick_response" );
			$user->remove_cap( "edit_other_wplc_quick_response" );
			$user->remove_cap( "publish_wplc_quick_response" );
			$user->remove_cap( "read_wplc_quick_response" );
			$user->remove_cap( "read_private_wplc_quick_response" );
			$user->remove_cap( "delete_wplc_quick_response" );
		}

		return $wplc_settings;
	}

	public function wplc_migrate_settings_9_0( $dbSettings, $wplc_settings ) {
		$wplc_settings->wplc_delay_between_loops = 500;

		if ( isset( $dbSettings['wplc_pro_fst1'] ) ) {
			$wplc_settings->wplc_chat_title = $dbSettings['wplc_pro_fst1'];
		}

		if ( isset( $dbSettings['wplc_pro_intro'] ) ) {
			$wplc_settings->wplc_chat_intro = $dbSettings['wplc_pro_intro'];
		}

		if ( isset( $dbSettings['wplc_pro_sst1'] ) ) {
			$wplc_settings->wplc_button_start_text = $dbSettings['wplc_pro_sst1'];
		}

		if ( isset( $dbSettings['wplc_pro_offline3'] ) ) {
			$wplc_settings->wplc_offline_finish_message = $dbSettings['wplc_pro_offline3'];
		}

		if ( isset( $dbSettings['wplc_chat_icon'] ) ) {
			$wplc_settings->wplc_chat_icon = wplc_protocol_agnostic_url( $dbSettings['wplc_chat_icon'] );
		} else {
			$wplc_settings->wplc_chat_icon = wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/wplc_icon.png' );
		}

		$wplc_settings->wplc_channel_url = admin_url( 'admin-ajax.php' );
		if ( isset( $dbSettings['wplc_use_node_server'] ) && $dbSettings['wplc_use_node_server'] == 1 ) {
			$wplc_settings->wplc_channel = 'mcu';
		}

		if ( isset( $dbSettings['wplc_bh_days'] ) && ! is_array( $dbSettings['wplc_bh_days'] ) ) {
			$result = array();
			$length = strlen( $dbSettings['wplc_bh_days'] );
			for ( $i = 0; $i < $length; $i ++ ) {
				$result[ $i ] = intval( $dbSettings['wplc_bh_days'][ $i ] );
			}
			$wplc_settings->wplc_bh_days = $result;
		}

		if ( isset( $dbSettings['wplc_theme'] ) && $dbSettings['wplc_theme'] != "theme-6" ) {
			switch ( $dbSettings['wplc_theme'] ) {
				case 'theme-1':
					$wplc_settings->wplc_settings_base_color   = "#DB0000";
					$wplc_settings->wplc_settings_agent_color  = "#FFF";
					$wplc_settings->wplc_settings_client_color = "#000";
					break;
				case 'theme-2':
					$wplc_settings->wplc_settings_base_color   = "#000";
					$wplc_settings->wplc_settings_agent_color  = "#FFF";
					$wplc_settings->wplc_settings_client_color = "#888";
					break;
				case 'theme-3':
					$wplc_settings->wplc_settings_base_color   = "#B97B9D";
					$wplc_settings->wplc_settings_agent_color  = "#FFF";
					$wplc_settings->wplc_settings_client_color = "#EEE";
					break;
				case 'theme-4':
					$wplc_settings->wplc_settings_base_color   = "#1A14DB";
					$wplc_settings->wplc_settings_agent_color  = "#FDFDFF";
					$wplc_settings->wplc_settings_client_color = "#7F7FB3";
					break;
				case 'theme-5':
					$wplc_settings->wplc_settings_base_color   = "#3DCC13";
					$wplc_settings->wplc_settings_agent_color  = "#FDFDFF";
					$wplc_settings->wplc_settings_client_color = "#EEE";
					break;
				default:
					$wplc_settings->wplc_settings_base_color   = "#0596d4";
					$wplc_settings->wplc_settings_agent_color  = "#FFF";
					$wplc_settings->wplc_settings_client_color = "#06B4FF";
					break;
			}
		} else {
			if ( isset( $dbSettings['wplc_settings_color1'] ) ) {
				$wplc_settings->wplc_settings_base_color = "#" . $dbSettings['wplc_settings_color1'];
			}

			if ( isset( $dbSettings['wplc_settings_color2'] ) ) {
				$wplc_settings->wplc_settings_agent_color = "#" . $dbSettings['wplc_settings_color2'];
			}

			if ( isset( $dbSettings['wplc_settings_color3'] ) ) {
				$wplc_settings->wplc_settings_client_color = "#" . $dbSettings['wplc_settings_color3'];
			}
		}

		if ( ! isset( $dbSettings['wplc_exclude_post_types'] ) || ! is_array( $dbSettings['wplc_exclude_post_types'] ) ) {
			$wplc_settings->wplc_exclude_post_types = array();
		} else {
			$wplc_settings->wplc_exclude_post_types = $dbSettings['wplc_exclude_post_types'];
		}


		if ( isset( $dbSettings['wplc_animation'] ) && $dbSettings['wplc_animation'] === '' ) {
			$wplc_settings->wplc_animation = 'animation-4';
		}

		if ( isset( $dbSettings['wplc_bh_schedule'] ) && ! empty( $dbSettings['wplc_bh_schedule'] ) ) {
			$scheduleResult = array();
			foreach ( $dbSettings['wplc_bh_schedule'] as $day => $daySchedule ) {
				$scheduleResult[ $day ] = array();
				if ( is_array( $daySchedule ) && ! empty( $daySchedule ) ) {
					foreach ( $daySchedule as $schedule ) {
						if ( is_array( $schedule ) && ! empty( $schedule ) ) {
							if ( isset( $schedule['hs'] ) && isset( $schedule['ms'] ) && isset( $schedule['he'] ) && isset( $schedule['me'] ) ) {
								$scheduleMigrated          = new stdClass();
								$scheduleMigrated->code    = TCXUtilsHelper::generateRandomString( 6, false );
								$scheduleMigrated->from    = new stdClass();
								$scheduleMigrated->from->h = str_pad( $schedule['hs'], 2, "0", STR_PAD_LEFT);
								$scheduleMigrated->from->m = str_pad( $schedule['ms'], 2, "0", STR_PAD_LEFT);
								$scheduleMigrated->to      = new stdClass();
								$scheduleMigrated->to->h   = str_pad( $schedule['he'], 2, "0", STR_PAD_LEFT);
								$scheduleMigrated->to->m   = str_pad( $schedule['me'], 2, "0", STR_PAD_LEFT);
								$scheduleResult[ $day ][]  = $scheduleMigrated;
							}
						}
					}
				}
			}
			$wplc_settings->wplc_bh_schedule = $scheduleResult;
		}

		$wplc_gutenberg_settings = get_option( "wplc_gutenberg_settings" );
		if ( ! empty( $wplc_gutenberg_settings ) ) {
			$wplc_settings->wplc_gutenberg_settings = array(
				"enable"      => boolval( $wplc_gutenberg_settings['wplc_gutenberg_enable'] ),
				"size"        => $wplc_gutenberg_settings['wplc_gutenberg_size'],
				"logo"        => $wplc_gutenberg_settings['wplc_gutenberg_logo'],
				"text"        => $wplc_gutenberg_settings['wplc_gutenberg_text'],
				"enable_icon" => boolval( $wplc_gutenberg_settings['wplc_gutenberg_enable_icon'] ),
				"icon"        => $wplc_gutenberg_settings['wplc_gutenberg_icon'],
				"custom_html" => $wplc_gutenberg_settings['wplc_custom_html']
			);
		}
		delete_option( "wplc_gutenberg_settings" );

		$wplc_auto_respond_settings = get_option( "WPLC_AUTO_RESPONDER_SETTINGS" );
		if ( ! empty( $wplc_auto_respond_settings ) ) {
			$wplc_settings->wplc_autorespond_settings = array(
				"wplc_ar_enable"     => array_key_exists( 'wplc_ar_enable', $wplc_auto_respond_settings ) ? boolval( $wplc_auto_respond_settings['wplc_ar_enable'] ) : false,
				"wplc_ar_from_name"  => array_key_exists( 'wplc_ar_from_name', $wplc_auto_respond_settings ) ? $wplc_auto_respond_settings['wplc_ar_from_name'] : $wplc_settings->wplc_autorespond_settings['wplc_ar_from_name'],
				"wplc_ar_from_email" => array_key_exists( 'wplc_ar_from_email', $wplc_auto_respond_settings ) ? $wplc_auto_respond_settings['wplc_ar_from_email'] : $wplc_settings->wplc_autorespond_settings['wplc_ar_from_email'],
				"wplc_ar_subject"    => array_key_exists( 'wplc_ar_subject', $wplc_auto_respond_settings ) ? $wplc_auto_respond_settings['wplc_ar_subject'] : $wplc_settings->wplc_autorespond_settings['wplc_ar_subject'],
				"wplc_ar_body"       => array_key_exists( 'wplc_ar_body', $wplc_auto_respond_settings ) ? $wplc_auto_respond_settings['wplc_ar_body'] : $wplc_settings->wplc_autorespond_settings['wplc_ar_body'],
			);
		}
		delete_option( "WPLC_AUTO_RESPONDER_SETTINGS" );

		$wplc_banned_ips = get_option( "WPLC_BANNED_IP_ADDRESSES" );
		if ( ! empty( $wplc_banned_ips ) ) {
			$ips = maybe_unserialize( $wplc_banned_ips );
			if ( is_array( $ips ) ) {
				$wplc_settings->wplc_banned_ips = $ips;
			} else {
				$wplc_settings->wplc_banned_ips = array();
			}
		}
		delete_option( "WPLC_BANNED_IP_ADDRESSES" );

		$old_settings = get_option( "WPLC_SETTINGS" );
		if ( ! empty( $old_settings ) ) {
			add_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
			delete_option( "WPLC_SETTINGS" );
		} else {
			update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		}

		$activator = wp_get_current_user();
		TCXAgentsHelper::set_user_as_agent( $activator->ID );

		return $wplc_settings;
	}

	public function wplc_migrate_settings_9_0_4( $dbSettings, $wplc_settings ) {

		if ( $dbSettings['wplc_channel'] === 'phone' ) {
			$channel_url                   = $dbSettings['wplc_channel_url'] . '#' . $dbSettings['wplc_chat_party'];
			$url_data                      = TCXUtilsHelper::wplc_parse_click2callUrl( $channel_url );
			$wplc_settings->wplc_files_url = $url_data['files_url'];
		} else {
			$wplc_settings->wplc_files_url = WPLC_PLUGIN_URL;
		}

		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );

		return $wplc_settings;
	}

	public function wplc_migrate_old_business_hours( $dbSettings, $wplc_settings ) {
		if ( isset( $dbSettings['wplc_bh_interval'] ) ) {
			switch ( $dbSettings['wplc_bh_interval'] ) {
				case 0:
					$wplc_settings->wplc_bh_days = '1111111';
					break;
				case 1:
					$wplc_settings->wplc_bh_days = '0111110';
					break;
				case 2:
					$wplc_settings->wplc_bh_days = '1000001';
					break;
			}
		}

		return $wplc_settings;
	}

	public function migrate_old_user_info( $dbSettings, $wplc_settings ) {
		if ( isset( $dbSettings['wplc_require_user_info'] ) ) {
			if ( $dbSettings['wplc_require_user_info'] == '1' ) {
				$wplc_settings->wplc_require_user_info = 'both';
			}
			if ( $dbSettings['wplc_require_user_info'] == '0' ) {
				$wplc_settings->wplc_require_user_info = 'none';
			}
		}

		return $wplc_settings;
	}

	private function wplc_cleanup_old_options( $dbSettings, $wplc_settings ) {
		// parameters migration from WPLC_ACBC_SETTINGS to WPLC_SETTINGS
		$wplc_acbc_settings = get_option( 'WPLC_ACBC_SETTINGS' );
		if ( ! empty( $wplc_acbc_settings ) ) {
			if ( isset( $wplc_acbc_settings['wplc_chat_delay'] ) ) {
				$wplc_settings->wplc_chat_delay = intval( $wplc_acbc_settings['wplc_chat_delay'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_chat_icon'] ) ) {
				$wplc_settings->wplc_chat_icon = strval( $wplc_acbc_settings['wplc_chat_icon'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_chat_logo'] ) ) {
				$wplc_settings->wplc_chat_logo = strval( $wplc_acbc_settings['wplc_chat_logo'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_pro_chat_email_address'] ) ) {
				$wplc_settings->wplc_pro_chat_email_address = strval( $wplc_acbc_settings['wplc_pro_chat_email_address'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_pro_chat_notification'] ) ) {
				$wplc_settings->wplc_pro_chat_notification = strval( $wplc_acbc_settings['wplc_pro_chat_notification'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_social_fb'] ) ) {
				$wplc_settings->wplc_social_fb = strval( $wplc_acbc_settings['wplc_social_fb'] );
			}
			if ( isset( $wplc_acbc_settings['wplc_social_tw'] ) ) {
				$wplc_settings->wplc_social_tw = strval( $wplc_acbc_settings['wplc_social_tw'] );
			}
			delete_option( 'WPLC_ACBC_SETTINGS' );
		}

		$wplc_encrypt_data = get_option( "WPLC_ENCRYPT_SETTINGS" );
		if ( ! empty( $wplc_encrypt_data ) ) {
			$wplc_settings->wplc_enable_encryption = boolval( $wplc_encrypt_data['wplc_enable_encryption'] );
			delete_option( 'WPLC_ENCRYPT_SETTINGS' );
		}

		$wplc_inex_data = get_option( "WPLC_INEX_SETTINGS" );
		if ( ! empty( $wplc_inex_data ) ) {
			if ( isset( $wplc_inex_data['wplc_exclude_from_pages'] ) ) {
				$wplc_settings->wplc_exclude_from_pages = strval( $wplc_inex_data['wplc_exclude_from_pages'] );
			}
			if ( isset( $wplc_inex_data['wplc_include_on_pages'] ) ) {
				$wplc_settings->wplc_include_on_pages = strval( $wplc_inex_data['wplc_include_on_pages'] );
			}
			if ( isset( $wplc_inex_data['wplc_exclude_home'] ) ) {
				$wplc_settings->wplc_exclude_home = boolval( $wplc_inex_data['wplc_exclude_home'] );
			}
			if ( isset( $wplc_inex_data['wplc_exclude_archive'] ) ) {
				$wplc_settings->wplc_exclude_archive = strval( $wplc_inex_data['wplc_exclude_archive'] );
			}
			if ( isset( $wplc_inex_data['wplc_exclude_post_types'] ) ) {
				$wplc_settings->wplc_exclude_post_types = strval( $wplc_inex_data['wplc_exclude_post_types'] );
			}
			delete_option( 'WPLC_INEX_SETTINGS' );
		}

		$wplc_choose_data = get_option( "WPLC_CHOOSE_SETTINGS" );
		if ( ! empty( $wplc_choose_data ) ) {
			if ( isset( $wplc_choose_data['wplc_auto_online'] ) ) {
				$wplc_settings->wplc_allow_agents_set_status = boolval( $wplc_choose_data['wplc_auto_online'] );
			}
			delete_option( 'WPLC_CHOOSE_SETTINGS' );
		}

		$wplc_et_data = get_option( "WPLC_ET_SETTINGS" );
		if ( ! empty( $wplc_et_data ) ) {
			if ( isset( $wplc_et_data['wplc_send_transcripts_when_chat_ends'] ) ) {
				$wplc_settings->wplc_send_transcripts_when_chat_ends = boolval( $wplc_et_data['wplc_send_transcripts_when_chat_ends'] );
			}
			if ( isset( $wplc_et_data['wplc_send_transcripts_to'] ) ) {
				$wplc_settings->wplc_send_transcripts_to = strval( $wplc_et_data['wplc_send_transcripts_to'] );
			}
			if ( isset( $wplc_et_data['wplc_et_email_body'] ) ) {
				$wplc_settings->wplc_et_email_body = strval( $wplc_et_data['wplc_et_email_body'] );
			}
			if ( isset( $wplc_et_data['wplc_et_email_header'] ) ) {
				$wplc_settings->wplc_et_email_header = strval( $wplc_et_data['wplc_et_email_header'] );
			}
			if ( isset( $wplc_et_data['wplc_et_email_footer'] ) ) {
				$wplc_settings->wplc_et_email_footer = strval( $wplc_et_data['wplc_et_email_footer'] );
			}
			delete_option( 'WPLC_ET_SETTINGS' );
		}

		$wplc_bh_settings = get_option( "WPLC_BH_SETTINGS" );
		if ( ! empty( $wplc_bh_settings ) ) {
			if ( isset( $wplc_bh_settings['enabled'] ) ) {
				$wplc_settings->wplc_bh_enable = boolval( $wplc_bh_settings['enabled'] );
			}
			delete_option( 'WPLC_BH_SETTINGS' );
		}

		$wplc_advanced_settings = get_option( "WPLC_ADVANCED_SETTINGS" );
		if ( ! empty( $wplc_advanced_settings ) ) {
			if ( isset( $wplc_advanced_settings['wplc_iterations'] ) ) {
				$wplc_settings->wplc_iterations = intval( $wplc_advanced_settings['wplc_iterations'] );
			}
			if ( isset( $wplc_advanced_settings['wplc_delay_between_loops'] ) ) {
				$wplc_settings->wplc_delay_between_loops = intval( $wplc_advanced_settings['wplc_delay_between_loops'] );
			}
			delete_option( 'WPLC_ADVANCED_SETTINGS' );
		}

		if ( empty( $wplc_settings->wplc_encryption_key ) ) {
			$wplc_settings->wplc_encryption_key = TCXUtilsHelper::generate_encryption_key();
		}

		$wplc_powered_by = get_option( "WPLC_POWERED_BY" );
		if ( ! empty( $wplc_powered_by ) ) {
			$wplc_settings->wplc_powered_by = intval( $wplc_powered_by );
			delete_option( "WPLC_POWERED_BY" );
		}

		return $wplc_settings;

	}

	private function wplc_fix_trailing_zeros_business_hours($dbSettings, $wplc_settings){
		// this update function exists just to fix faulty business hours settings created by upgrade from 8.X to 9.X (till 9.0.7)
		// that's why we are doing the job using only $wplc_settings
		//
		$result = array();
		foreach($dbSettings['wplc_bh_schedule'] as $day => $daySchedules)
		{
			$result[$day] = array();
			if(is_array($daySchedules) || is_object($daySchedules)) {
				foreach ( $daySchedules as $index => $schedule ) {
					$new_schedule          = new stdClass();
					$new_schedule->from    = new stdClass();
					$new_schedule->to      = new stdClass();
					$new_schedule->code    = TCXUtilsHelper::generateRandomString( 6, false );
					$new_schedule->from->h = str_pad( $schedule['from']['h'], 2, "0", STR_PAD_LEFT );
					$new_schedule->from->m = str_pad( $schedule['from']['m'], 2, "0", STR_PAD_LEFT );

					$new_schedule->to->h      = str_pad( $schedule['to']['h'], 2, "0", STR_PAD_LEFT );
					$new_schedule->to->m      = str_pad( $schedule['to']['m'], 2, "0", STR_PAD_LEFT );
					$result[ $day ][ $index ] = $new_schedule;
				}
			}
		}

		$wplc_settings->wplc_bh_schedule = $result;
		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );

		return $wplc_settings;
	}


	public function wplc_complete_existing_chats() {
		TCXChatHelper::complete_all_ended_chats();
	}

	public function wplc_set_users_capabilities() {

		$admins = get_role( 'administrator' );
		if ( $admins !== null ) {
			$admins->add_cap( 'wplc_cap_admin' );
			$admins->add_cap( "wplc_cap_show_history" );
			$admins->add_cap( "wplc_cap_show_offline" );
		}

		$agents = TCXAgentsHelper::get_agent_users();

		if ( count( $agents ) == 0 ) {
			$uid = get_current_user_id();
			TCXAgentsHelper::set_user_as_agent( $uid );
			TCXAgentsHelper::set_agent_accepting( $uid, true );
			TCXAgentsHelper::update_agent_time( $uid );
		} else {
			foreach ( $agents as $agent ) {
				TCXAgentsHelper::set_user_as_agent( $agent->ID);
			}
		}


	}

	public function wplc_migrate_settings_9_0_25($dbSettings, $wplc_settings){
		if ( !isset( $dbSettings['wplc_settings_button_color'] ) && isset( $dbSettings['wplc_settings_base_color'] )) {
			$wplc_settings->wplc_settings_button_color = $dbSettings['wplc_settings_base_color'];
		}

		if ( isset( $dbSettings['wplc_gdpr_custom'] ) && !$dbSettings['wplc_gdpr_custom']) {
			$wplc_settings->wplc_gdpr_notice_text = "";
		}

		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		return $wplc_settings;

	}

	public function wplc_migrate_settings_9_1_1($dbSettings, $wplc_settings){
		if ( isset( $dbSettings['wplc_channel'] ) && $dbSettings['wplc_channel'] === "wp") {
			$wplc_settings->wplc_channel = "mcu";
			update_option( "WPLC_SHOW_CHANNEL_MIGRATION", true );
		}

		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		return $wplc_settings;
	}

	public function wplc_migrate_settings_9_2_0($dbSettings, $wplc_settings){
		if ( isset($wplc_settings->wplc_bh_days) ) {
			unset($wplc_settings->wplc_bh_days);
		}
		$wplc_settings->wplc_text_chat_ended  ='Your chat session ended';
		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		return $wplc_settings;
	}

	public function wplc_migrate_settings_9_3_0($dbSettings, $wplc_settings){
		if ( isset($wplc_settings->wplc_popout_enabled) ) {
			unset($wplc_settings->wplc_popout_enabled);
		}
		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		return $wplc_settings;
	}

	public function wplc_migrate_settings_9_4_0($dbSettings, $wplc_settings){
		if ( !isset($dbSettings['wplc_gutenberg_settings']['theme']) ) {
			$wplc_settings->wplc_gutenberg_settings['theme']= "template_default";
		}
		update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $wplc_settings ), JSON_UNESCAPED_UNICODE ) );
		return $wplc_settings;
	}

	public function wplc_init_files_table_data(){
		global $wpdb;
		global $wplc_tblname_msgs;
		$sql = "select * from $wplc_tblname_msgs where other like '%isFile\":true%'";

		$file_messages = $wpdb->get_results( $sql );

		foreach ( $file_messages as $message ) {
			$message_properties = json_decode( $message->other );

			if ( is_object( $message_properties ) && isset( $message_properties->isFile ) && $message_properties->isFile &&
			     isset( $message_properties->file ) && is_object( $message_properties->file ) &&
			     isset( $message_properties->file->FileName ) && isset( $message_properties->file->FileLink ) ) {
					  TCXChatData::add_chat_file( $wpdb, $message->chat_sess_id, $message->id, $message_properties->file->FileName, $message_properties->file->FileLink );
			}
		}


	}

	public function wplc_add_files_table($dbSettings, $wplc_settings){
		global $wplc_tblname_chats;
		global $wplc_tblname_msgs;
		global $wplc_tblname_offline_msgs;
		global $wplc_tblname_chat_ratings;
		global $wplc_tblname_chat_departments;
		global $wplc_tblname_actions_queue;
		global $wplc_custom_fields_table;
		global $wplc_webhooks_table;
		global $wplc_quick_responses_table;
		maybe_convert_table_to_utf8mb4($wplc_tblname_chats);
		maybe_convert_table_to_utf8mb4($wplc_tblname_msgs);
		maybe_convert_table_to_utf8mb4($wplc_tblname_offline_msgs);
		maybe_convert_table_to_utf8mb4($wplc_tblname_chat_ratings);
		maybe_convert_table_to_utf8mb4($wplc_tblname_chat_departments);
		maybe_convert_table_to_utf8mb4($wplc_tblname_actions_queue);
		maybe_convert_table_to_utf8mb4($wplc_custom_fields_table);
		maybe_convert_table_to_utf8mb4($wplc_webhooks_table);
		maybe_convert_table_to_utf8mb4($wplc_quick_responses_table);

		return $wplc_settings;
	}
}