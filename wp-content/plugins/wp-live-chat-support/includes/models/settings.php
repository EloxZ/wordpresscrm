<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_filter( 'pre_update_option_siteurl', array( 'TCXSettings', 'wplc_check_siteurl_changes' ), 1, 2 );

class TCXSettings {
	private static $instance = null;

	public $wplc_allow_department_selection;
	public $wplc_allow_agents_set_status;
	public $wplc_animation;
	public $wplc_auto_pop_up;
	public $wplc_auto_pop_up_online;
	public $wplc_bh_enable;
	public $wplc_bh_schedule;
	public $wplc_chat_delay;
	public $wplc_chat_icon;
	public $wplc_chat_icon_type;
	public $wplc_chat_logo;
	public $wplc_agent_logo;
	public $wplc_chatbox_height;
	public $wplc_chatbox_absolute_height;
	public $wplc_close_btn_text;
	public $wplc_default_department;
	public $wplc_delay_between_loops;
	public $wplc_delete_db_on_uninstall;
	public $wplc_display_to_loggedin_only;
	public $wplc_enable_encryption;
	public $wplc_enable_initiate_chat;
	public $wplc_enable_msg_sound;
	public $wplc_enable_visitor_sound;
	public $wplc_enable_voice_notes_on_admin;
	public $wplc_enable_voice_notes_on_visitor;
	public $wplc_enabled_on_mobile;
	public $wplc_encryption_key;
	public $wplc_environment;
	public $wplc_et_email_body;
	public $wplc_et_email_footer;
	public $wplc_et_email_header;
	public $wplc_exclude_from_pages;
	public $wplc_exclude_home;
	public $wplc_exclude_archive;
	public $wplc_exclude_post_types;
	public $wplc_gdpr_enabled;
	public $wplc_gdpr_notice_company;
	public $wplc_gdpr_notice_retention_period;
	public $wplc_gdpr_notice_retention_purpose;
	public $wplc_gdpr_notice_text;
	public $wplc_hide_when_offline;
	public $wplc_include_on_pages;
	public $wplc_iterations;
	public $wplc_loggedin_user_info;
	public $wplc_agent_default_name;
	public $wplc_show_agent_name;
	public $wplc_messagetone;
	public $wplc_new_chat_ringer_count;
	public $wplc_theme;
	public $wplc_pro_auto_first_response_chat_msg;
	public $wplc_pro_chat_email_address;
	public $wplc_pro_chat_email_offline_subject;
	public $wplc_pro_chat_notification;
	public $wplc_chat_title;
	public $wplc_chat_intro;
	public $wplc_pro_na;
	public $wplc_offline_finish_message;
	public $wplc_button_start_text;
	public $wplc_require_user_info;
	public $wplc_ringtone;
	public $wplc_send_transcripts_to;
	public $wplc_send_transcripts_when_chat_ends;
	public $wplc_settings_align;
	public $wplc_settings_base_color;
	public $wplc_settings_button_color;
	public $wplc_settings_agent_color;
	public $wplc_settings_client_color;
	public $wplc_settings_enabled;
	public $wplc_show_avatar;
	public $wplc_show_date;
	public $wplc_show_name;
	public $wplc_show_time;
	public $wplc_social_fb;
	public $wplc_social_tw;
	public $wplc_text_chat_ended;
	public $wplc_use_geolocalization;
	public $wplc_enable_ga;
	public $wplc_user_alternative_text;
	public $wplc_user_default_visitor_name;
	public $wplc_user_no_answer;
	public $wplc_using_localization_plugin;
	public $wplc_ux_exp_rating;
	public $wplc_ux_file_share;
	public $wplc_welcome_msg;
	public $wplc_channel;
	public $wplc_channel_url;
	public $wplc_files_url;
	public $wplc_socket_url;
	public $wplc_chat_server_session;
	public $wplc_chat_party;
	public $wplc_powered_by;
	public $wplc_gutenberg_settings;
	public $wplc_banned_ips;
	public $wplc_settings_minimized_style;
	public $wplc_allow_call;
	public $wplc_allow_video;
	public $wplc_direct_call_enabled;
	public $wplc_allow_chat;
	public $wplc_cluster_manager_route_server;
	public $wplc_greeting_mode;
	public $wplc_greeting_message;
	public $wplc_offline_greeting_mode;
	public $wplc_offline_greeting_message;
	public $wplc_ignore_queue_ownership;
	public $wplc_offline_name_message;
	public $wplc_offline_email_message;
	public $wplc_offline_length_error;
	public $wplc_offline_email_invalid;
	public $wplc_offline_name_invalid;
	public $wplc_rate_message;
	public $wplc_rate_comments_message;
	public $wplc_rate_feedback_request_message;
	public $wplc_call_title;
	public $wplc_language;
	public $wplc_auth_name_message;
	public $wplc_auth_email_message;

	private function __construct() {

	}

	public static function getSettingsTypes() {
		return array(
			"wplc_allow_agents_set_status"                 => "boolean",
			"wplc_allow_department_selection"              => "boolean",
			"wplc_animation"                               => "string",
			"wplc_auto_pop_up"                             => "integer",
			"wplc_auto_pop_up_online"                      => "boolean",
			"wplc_bh_enable"                               => "boolean",
			"wplc_chat_delay"                              => "integer",
			"wplc_chat_icon"                               => "base64-url",
			"wplc_chat_logo"                               => "base64-url",
			"wplc_agent_logo"                              => "base64-url",
			"wplc_chat_icon_type"                          => "string",
			"wplc_chatbox_absolute_height"                 => "integer",
			"wplc_chatbox_height"                          => "integer",
			"wplc_close_btn_text"                          => "string",
			"wplc_default_department"                      => "integer",
			"wplc_delay_between_loops"                     => "integer",
			"wplc_delete_db_on_uninstall"                  => "boolean",
			"wplc_display_to_loggedin_only"                => "boolean",
			"wplc_enable_encryption"                       => "boolean",
			"wplc_enable_initiate_chat"                    => "boolean",
			"wplc_enable_msg_sound"                        => "boolean",
			"wplc_enable_visitor_sound"                    => "boolean",
			"wplc_enable_voice_notes_on_admin"             => "boolean",
			"wplc_enable_voice_notes_on_visitor"           => "boolean",
			"wplc_enabled_on_mobile"                       => "boolean",
			"wplc_encryption_key"                          => "string",
			"wplc_environment"                             => "integer",
			"wplc_et_email_body"                           => "html",
			"wplc_et_email_footer"                         => "html",
			"wplc_et_email_header"                         => "html",
			"wplc_exclude_archive"                         => "boolean",
			"wplc_exclude_from_pages"                      => "string",
			"wplc_exclude_home"                            => "boolean",
			"wplc_gdpr_enabled"                            => "boolean",
			"wplc_gdpr_notice_company"                     => "string",
			"wplc_gdpr_notice_retention_period"            => "integer",
			"wplc_gdpr_notice_retention_purpose"           => "string",
			"wplc_gdpr_notice_text"                        => "string",
			"wplc_hide_when_offline"                       => "boolean",
			"wplc_include_on_pages"                        => "string",
			"wplc_iterations"                              => "integer",
			"wplc_loggedin_user_info"                      => "boolean",
			"wplc_show_agent_name"                         => "boolean",
			"wplc_agent_default_name"                      => "string",
			"wplc_messagetone"                             => "string",
			"wplc_new_chat_ringer_count"                   => "integer",
			"wplc_theme"                                   => "string",
			"wplc_powered_by"                              => "boolean",
			"wplc_pro_auto_first_response_chat_msg"        => "string",
			"wplc_pro_chat_email_address"                  => "string",
			"wplc_pro_chat_email_offline_subject"          => "string",
			"wplc_pro_chat_notification"                   => "boolean",
			"wplc_chat_title"                              => "string",
			"wplc_chat_intro"                              => "string",
			"wplc_pro_na"                                  => "string",
			"wplc_offline_finish_message"                  => "string",
			"wplc_button_start_text"                       => "string",
			"wplc_require_user_info"                       => "string",
			"wplc_ringtone"                                => "string",
			"wplc_send_transcripts_to"                     => "string",
			"wplc_send_transcripts_when_chat_ends"         => "boolean",
			"wplc_settings_align"                          => "integer",
			"wplc_settings_base_color"                     => "string",
			"wplc_settings_button_color"                   => "string",
			"wplc_settings_agent_color"                    => "string",
			"wplc_settings_client_color"                   => "string",
			"wplc_settings_enabled"                        => "integer",
			"wplc_show_avatar"                             => "boolean",
			"wplc_show_date"                               => "boolean",
			"wplc_show_name"                               => "boolean",
			"wplc_show_time"                               => "boolean",
			"wplc_social_fb"                               => "url",
			"wplc_social_tw"                               => "url",
			"wplc_text_chat_ended"                         => "string",
			"wplc_use_geolocalization"                     => "boolean",
			"wplc_enable_ga"                               => "boolean",
			"wplc_user_alternative_text"                   => "string",
			"wplc_auth_name_message"                       => "string",
			"wplc_auth_email_message"                      => "string",
			"wplc_user_default_visitor_name"               => "string",
			"wplc_user_no_answer"                          => "string",
			"wplc_using_localization_plugin"               => "boolean",
			"wplc_ux_exp_rating"                           => "boolean",
			"wplc_ux_file_share"                           => "boolean",
			"wplc_welcome_msg"                             => "string",
			"wplc_bh_schedule"                             => "json",
			"wplc_exclude_post_types"                      => "array-string",
			"wplc_banned_ips"                              => "array-string",
			"wplc_channel"                                 => "string",
			"wplc_channel_url"                             => "url",
			"wplc_files_url"                               => "url",
			"wplc_socket_url"                              => "socket-url",
			"wplc_chat_server_session"                     => "string",
			"wplc_chat_party"                              => "string",
			"wplc_gutenberg_settings"                      => "array-settings",
			"wplc_gutenberg_settings>enable"               => "boolean",
			"wplc_gutenberg_settings>size"                 => "integer",
			"wplc_gutenberg_settings>logo"                 => "url",
			"wplc_gutenberg_settings>text"                 => "string",
			"wplc_gutenberg_settings>icon"                 => "string",
			"wplc_gutenberg_settings>enable_icon"          => "boolean",
			"wplc_gutenberg_settings>custom_html"          => "html",
			"wplc_gutenberg_settings>theme"                => "string",
			"wplc_autorespond_settings"                    => "array-settings",
			"wplc_autorespond_settings>wplc_ar_enable"     => "boolean",
			"wplc_autorespond_settings>wplc_ar_from_name"  => "string",
			"wplc_autorespond_settings>wplc_ar_from_email" => "string",
			"wplc_autorespond_settings>wplc_ar_subject"    => "string",
			"wplc_autorespond_settings>wplc_ar_body"       => "html",
			"wplc_settings_minimized_style"                => "string",
			"wplc_allow_call"                              => "boolean",
			"wplc_allow_chat"                              => "boolean",
			"wplc_allow_video"                             => "boolean",
			"wplc_direct_call_enabled"                     => "boolean",
			"wplc_cluster_manager_route_server"            => "string",
			"wplc_greeting_mode"                           => "string",
			"wplc_greeting_message"                        => "string",
			"wplc_offline_greeting_mode"                   => "string",
			"wplc_offline_name_message"                    => "string",
			"wplc_offline_email_message"                   => "string",
			"wplc_offline_greeting_message"                => "string",
			"wplc_offline_length_error"                    => "string",
			"wplc_offline_email_invalid"                   => "string",
			"wplc_offline_name_invalid"                    => "string",
			"wplc_ignore_queue_ownership"                  => "boolean",
			"wplc_rate_message"                            => "string",
			"wplc_rate_comments_message"                   => "string",
			"wplc_rate_feedback_request_message"           => "string",
			"wplc_call_title"                              => "string",
			"wplc_language"                                => "string",
		);
	}

	public static function getDefaultSettings() {
		global $wplc_base_file;
		$result = self::getSettings();

		$result->wplc_allow_department_selection       = false;
		$result->wplc_allow_agents_set_status          = true;
		$result->wplc_animation                        = 'animation-4';
		$result->wplc_auto_pop_up                      = 0;
		$result->wplc_auto_pop_up_online               = false;
		$result->wplc_bh_enable                        = false;
		$result->wplc_bh_schedule                      = array();
		$result->wplc_chat_delay                       = 2;
		$result->wplc_chat_icon                        = wplc_plugins_url( '/images/wplc_icon.png', $wplc_base_file );
		$result->wplc_chat_icon_type                   = "Default";
		$result->wplc_chat_logo                        = '';
		$result->wplc_agent_logo                       = '';
		$result->wplc_chatbox_height                   = 0;
		$result->wplc_chatbox_absolute_height          = 330;
		$result->wplc_close_btn_text                   = __( "close", 'wp-live-chat-support' );
		$result->wplc_default_department               = - 1;
		$result->wplc_delay_between_loops              = 500;
		$result->wplc_delete_db_on_uninstall           = true;
		$result->wplc_display_to_loggedin_only         = false;
		$result->wplc_enable_encryption                = false;
		$result->wplc_enable_initiate_chat             = false;
		$result->wplc_enable_msg_sound                 = true;
		$result->wplc_enable_visitor_sound             = true;
		$result->wplc_enable_voice_notes_on_admin      = false;
		$result->wplc_enable_voice_notes_on_visitor    = false;
		$result->wplc_enabled_on_mobile                = true;
		$result->wplc_encryption_key                   = '';
		$result->wplc_environment                      = 2;
		$result->wplc_et_email_body                    = self::wplc_get_default_transcript_body();
		$result->wplc_et_email_footer                  = "<span style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: normal;'>" . __( 'Thank you for chatting with us.' ) . "</span>";
		$result->wplc_et_email_header                  = '<a title="' . get_bloginfo( 'name' ) . '" href="' . get_bloginfo( 'url' ) . '" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; text-decoration: underline;">' . get_bloginfo( 'name' ) . '</a>';
		$result->wplc_exclude_from_pages               = '';
		$result->wplc_exclude_home                     = false;
		$result->wplc_exclude_archive                  = false;
		$result->wplc_exclude_post_types               = array();
		$result->wplc_banned_ips                       = array();
		$result->wplc_gdpr_enabled                     = false;
		$result->wplc_gdpr_notice_company              = get_bloginfo( 'name' );
		$result->wplc_gdpr_notice_retention_period     = 30;
		$result->wplc_gdpr_notice_retention_purpose    = __( 'Chat/Support', 'wp-live-chat-support' );
		$result->wplc_gdpr_notice_text                 = '';
		$result->wplc_hide_when_offline                = false;
		$result->wplc_include_on_pages                 = '';
		$result->wplc_iterations                       = 60;
		$result->wplc_loggedin_user_info               = true;
		$result->wplc_show_agent_name                  = true;
		$result->wplc_agent_default_name               = 'Support';
		$result->wplc_messagetone                      = '';
		$result->wplc_new_chat_ringer_count            = 4;
		$result->wplc_theme                            = '3CX';
		$result->wplc_powered_by                       = true;
		$result->wplc_pro_auto_first_response_chat_msg = '';
		$result->wplc_pro_chat_email_address           = get_option( 'admin_email' );
		$result->wplc_pro_chat_email_offline_subject   = '';
		$result->wplc_pro_chat_notification            = false;
		$result->wplc_chat_title                       = __( "Live Chat & Talk", 'wp-live-chat-support' );
		$result->wplc_chat_intro                       = __( "Could we have your name and email?", 'wp-live-chat-support' );
		$result->wplc_pro_na                           = __( "We are away, leave us a message!", 'wp-live-chat-support' );
		$result->wplc_offline_finish_message           = __( "We received your message and we'll contact you soon.", 'wp-live-chat-support' );
		$result->wplc_button_start_text                = __( "Chat", 'wp-live-chat-support' );
		$result->wplc_require_user_info                = 'both';
		$result->wplc_ringtone                         = '';
		$result->wplc_send_transcripts_to              = 'user';
		$result->wplc_send_transcripts_when_chat_ends  = false;
		$result->wplc_settings_align                   = 2;
		$result->wplc_settings_base_color              = '#373737';
		$result->wplc_settings_button_color            = '#0596d4';
		$result->wplc_settings_agent_color             = '#eeeeee';
		$result->wplc_settings_client_color            = '#d4d4d4';
		$result->wplc_settings_enabled                 = 0;
		$result->wplc_show_avatar                      = true;
		$result->wplc_show_date                        = true;
		$result->wplc_show_name                        = true;
		$result->wplc_show_time                        = true;
		$result->wplc_social_fb                        = '';
		$result->wplc_social_tw                        = '';
		$result->wplc_text_chat_ended                  = __( "Your session is over. Please feel free to contact us again!", 'wp-live-chat-support' );
		$result->wplc_use_geolocalization              = false;
		$result->wplc_enable_ga                        = false;
		$result->wplc_user_alternative_text            = __( "Please click 'Chat' to initiate a chat with an agent", 'wp-live-chat-support' );
		$result->wplc_user_default_visitor_name        = __( "Guest", 'wp-live-chat-support' );
		$result->wplc_user_no_answer                   = __( "No answer. Try again later.", 'wp-live-chat-support' );
		$result->wplc_using_localization_plugin        = false;
		$result->wplc_ux_exp_rating                    = true;
		$result->wplc_ux_file_share                    = true;
		$result->wplc_welcome_msg                      = __( "Hello! How can we help you today?", 'wp-live-chat-support' );
		$result->wplc_channel                          = "mcu";
		$result->wplc_channel_url                      = admin_url( 'admin-ajax.php' );
		$result->wplc_socket_url                       = "";
		$result->wplc_chat_server_session              = "";
		$result->wplc_chat_party                       = "";
		$result->wplc_settings_minimized_style         = "Bubble";
		$result->wplc_allow_call                       = false;
		$result->wplc_allow_chat                       = true;
		$result->wplc_allow_video                      = false;
		$result->wplc_direct_call_enabled              = true;
		$result->wplc_cluster_manager_route_server     = '';
		$result->wplc_greeting_mode                    = 'none';
		$result->wplc_greeting_message                 = __( "Hey, we're here to help!", 'wp-live-chat-support' );
		$result->wplc_offline_greeting_mode            = 'none';
		$result->wplc_offline_name_message             = __( "Could we have your name?", 'wp-live-chat-support' );
		$result->wplc_offline_email_message            = __( "Could we have your email?", 'wp-live-chat-support' );
		$result->wplc_offline_greeting_message         = __( "Hey, we're here to help!", 'wp-live-chat-support' );
		$result->wplc_offline_length_error             = __( "Maximum characters reached", 'wp-live-chat-support' );
		$result->wplc_offline_email_invalid            = __( "I'm sorry, that doesn't look like an email address. Can you try again?", 'wp-live-chat-support' );
		$result->wplc_offline_name_invalid             = __( "I'm sorry, the provided name is not valid.", 'wp-live-chat-support' );
		$result->wplc_rate_message                     = __( "Rate your conversation", 'wp-live-chat-support' );
		$result->wplc_rate_comments_message            = __( "Tell us your feedback", 'wp-live-chat-support' );
		$result->wplc_rate_feedback_request_message    = __( 'Do you want to give us more detailed feedback?', 'wp-live-chat-support' );
		$result->wplc_call_title                       = __( "Call Us", 'wp-live-chat-support' );
		$result->wplc_auth_name_message                = __("Could we have your name?","wp-live-chat-support");
		$result->wplc_auth_email_message               = __("Could we have your email?","wp-live-chat-support");
		$result->wplc_ignore_queue_ownership           = false;
		$result->wplc_language                         = "browser";

		$result->wplc_gutenberg_settings   = array(
			"enable"      => true,
			"size"        => 2,
			"logo"        => "",
			"text"        => "Live Chat",
			"enable_icon" => true,
			"icon"        => "fas fa-comment-dots",
			"custom_html" => "<!-- Default HTML -->
<div class=\"wplc_block\">
    <span class=\"wplc_block_logo\">{wplc_logo}</span>
    <span class=\"wplc_block_text\">{wplc_text}</span>
    <span class=\"wplc_block_icon\">{wplc_icon}</span>
</div>",
			"theme"=>"template_default"
		);
		$result->wplc_autorespond_settings = array(
			"wplc_ar_enable"     => false,
			"wplc_ar_from_name"  => "3CX Live Chat",
			"wplc_ar_from_email" => get_option( 'admin_email' ),
			"wplc_ar_subject"    => "Thank you",
			"wplc_ar_body"       => ""
		);

		return $result;
	}

	public static function getFromArray( $arraySettings, $mergeWithDefaults = true ) {
		if ( $mergeWithDefaults ) {
			$result = self::getDefaultSettings();
		} else {
			$result = self::getSettings();
		}

		foreach ( $arraySettings as $key => $value ) {
			if ( property_exists( $result, $key ) ) {
				$result->$key = $value;
			}
		}

		return $result;
	}

	public static function getSettings() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function getDbSettings() {
		$json_settings = get_option( "WPLC_JSON_SETTINGS" );
		if ( ! $json_settings || empty( $json_settings ) ) {
			$DbSettings = get_option( "WPLC_SETTINGS" );
		} else {
			$DbSettings = TCXUtilsHelper::wplc_json_decode( $json_settings, true );
		}

		return $DbSettings;
	}

	public static function initSettings() {
		$current = self::getDbSettings();
		if ( empty( $current ) || ! is_array( $current ) ) {
			self::getDefaultSettings();
		} else {
			self::getFromArray( $current );
		}
	}

	public static function getSettingValue( $settingName, $defaultValue = '' ) {
		$result   = $defaultValue;
		$settings = self::getSettings();
		if ( isset( $settings->$settingName ) ) {
			$result = $settings->$settingName;
		}

		return $result;
	}

	public static function setSettingValue( $setting_name, $value ) {
		$result   = true;
		$settings = self::getSettings();
		if ( property_exists( $settings, $setting_name ) ) {
			$settings->$setting_name = $value;
			update_option( "WPLC_JSON_SETTINGS", TCXUtilsHelper::wplc_json_encode( TCXUtilsHelper::convertToArray( $settings ), JSON_UNESCAPED_UNICODE ) );
		} else if ( get_option( $setting_name, null ) !== null ) {
			update_option( $setting_name, $value );
		} else {
			$result = false;
		}

		return $result;
	}

	public static function setTheme( $theme_alias ) {
		$result = false;
		$theme  = TCXTheme::get_theme( $theme_alias );
		if ( $theme_alias === 'custom' || $theme !== null ) {
			self::setSettingValue( 'wplc_theme', $theme_alias );
			if ( $theme_alias !== 'custom' ) {

				self::setSettingValue( 'wplc_settings_base_color', $theme->base_color );
				self::setSettingValue( 'wplc_settings_agent_color', $theme->agent_color );
				self::setSettingValue( 'wplc_settings_client_color', $theme->client_color );
				self::setSettingValue( 'wplc_settings_button_color', $theme->button_color );
				$result = true;

			} else {
				$result = true;
			}
		}

		return $result;
	}


	public function getSaveUrl() {
		return admin_url( "admin.php?page=wplivechat-menu-settings&wplc_action=save_settings&nonce=" . wp_create_nonce( "saveSettings" ) );
	}

	public static function wplc_check_siteurl_changes( $new_value, $old_value ) {
		if ( $new_value != $old_value ) {
			$settings          = self::getSettings();
			$new_site_url_data = wp_parse_url( $new_value );
			$old_site_url_data = wp_parse_url( $old_value );
			if ( $settings->wplc_channel == "wp" ) {
				$new_channel_url = self::build_new_url( $new_site_url_data, $old_site_url_data, $settings->wplc_channel_url );
				if ( $new_channel_url !== false ) {
					self::setSettingValue( "wplc_channel_url", $new_channel_url );
				}
				$new_files_url = self::build_new_url( $new_site_url_data, $old_site_url_data, $settings->wplc_files_url );
				if ( $new_files_url !== false ) {
					self::setSettingValue( "wplc_files_url", $new_files_url );
				}
			}
			$new_logo_url = self::build_new_url( $new_site_url_data, $old_site_url_data, $settings->wplc_chat_logo );
			if ( $new_logo_url !== false ) {
				self::setSettingValue( "wplc_chat_logo", $new_logo_url );
			}

			$new_agent_logo_url = self::build_new_url( $new_site_url_data, $old_site_url_data, $settings->wplc_agent_logo );
			if ( $new_agent_logo_url !== false ) {
				self::setSettingValue( "wplc_agent_logo", $new_agent_logo_url );
			}

			$new_icon_url = self::build_new_url( $new_site_url_data, $old_site_url_data, $settings->wplc_chat_icon );
			if ( $new_icon_url !== false ) {
				self::setSettingValue( "wplc_chat_icon", $new_icon_url );
			}

		}

		return $new_value;
	}

	private static function build_new_url( $new_url_parsed, $old_url_parsed, $url_to_change ) {
		$new_url    = $new_url_parsed['scheme'] . "://" . $new_url_parsed['host'];
		$url_parsed = wp_parse_url( $url_to_change );

		if ( isset( $url_parsed['host'] ) && isset( $old_url_parsed['host'] ) && $url_parsed['host'] === $old_url_parsed['host'] ) {
			if ( isset( $new_url_parsed['port'] ) ) {
				$new_url = $new_url . ":" . $new_url_parsed['port'];
			}

			if ( isset( $url_parsed['path'] ) ) {
				$new_url = $new_url . $url_parsed['path'];
			}

			return $new_url;
		} else {
			return false;
		}
	}

	private static function wplc_get_default_transcript_body() {
		return '
		<table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: Georgia, serif;">
	    <tbody>
	      <tr>
	        <td width="100%" style="padding: 30px 20px 100px 20px;">
	          <table cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate;">
	            <tbody>
	              <tr>
	                <td style="padding-bottom: 20px;">
	                  
	                  <p>[wplc_et_transcript_header_text]</p>
	                </td>
	              </tr>
	            </tbody>
	          </table>

	          <table id="" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; font-size: 12px; color: rgb(51, 62, 72);">
	          <tbody>
	              <tr>
	                <td class="sortable-list ui-sortable" >
	                    [wplc_et_transcript]
	                </td>
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
	                        <td id="">
	                         <p>[wplc_et_transcript_footer_text]</p>
	                        </td>
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
	  </table>';
	}
}

/* restore default settings snippet */
/*$settings = TCXSettings::getDefaultSettings();
update_option('WPLC_SETTINGS',TCXUtilsHelper::convertToArray($settings));
die();*/

