<?php
/*
  Plugin Name: 3CX Live Chat
  Plugin URI: https://www.3cx.com/wp-live-chat/
  Description: The easiest to use website live chat plugin. Let your visitors chat with you and increase sales conversion rates with 3CX Live Chat.
  Version: 9.4.1
  Author: 3CX
  Author URI: https://www.3cx.com/wp-live-chat/
  Domain Path: /languages
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

use Leafo\ScssPhp\Compiler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wplc_base_file;
global $wplc_p_version;
global $wplc_tblname;
global $wpdb;
global $current_chat_id;

global $wplc_tblname_chats;
global $wplc_tblname_msgs;
global $wplc_tblname_files;
global $wplc_tblname_offline_msgs;
global $wplc_tblname_chat_ratings;
global $wplc_tblname_chat_departments;
global $wplc_tblname_actions_queue;
global $wplc_custom_fields_table;
global $wplc_webhooks_table;
global $wplc_quick_responses_table;


/**
 * This stores the admin chat data once so that we do not need to keep sourcing it via the WP DB or Cloud DB
 */

global $admin_chat_data;
$admin_chat_data = false;

$wplc_tblname_offline_msgs     = $wpdb->prefix . "wplc_offline_messages";
$wplc_tblname_chats            = $wpdb->prefix . "wplc_chat_sessions";
$wplc_tblname_actions_queue    = $wpdb->prefix . "wplc_actions_queue";
$wplc_tblname_msgs             = $wpdb->prefix . "wplc_chat_msgs";
$wplc_tblname_chat_ratings     = $wpdb->prefix . "wplc_chat_ratings";
$wplc_tblname_chat_departments = $wpdb->prefix . "wplc_departments";

$wplc_custom_fields_table   = $wpdb->prefix . "wplc_custom_fields";
$wplc_webhooks_table        = $wpdb->prefix . "wplc_webhooks";
$wplc_quick_responses_table = $wpdb->prefix . "wplc_quick_responses";
$wplc_tblname_files = $wpdb->prefix . "wplc_chat_files";

$wplc_base_file = __FILE__;


// Load Config
require_once( plugin_dir_path( __FILE__ ) . "config.php" );
require_once( plugin_dir_path( __FILE__ ) . "includes/wplc_loader.php" );

add_action( 'init', 'wplc_init' );
add_action( 'admin_init', 'wplc_load_bootstrap' );
add_action( 'admin_init', 'wplc_force_update_chat_server' );
add_action( 'admin_init', 'wplc_check_compatibility' );
add_action( 'admin_init', 'wplc_check_wizard_completed' );
add_action( "wp_login", 'wplc_check_guid' );
add_action( "wp_login", "wplc_agent_login_monitor", 10, 2 );

TCXSettings::initSettings();

register_uninstall_hook( __FILE__, 'wplc_uninstall' );
function wplc_check_compatibility() {
	global $wplc_error;
	$compatible = wplc_check_version_compatibility();
	if ( ! $compatible->php || ! $compatible->wp || ! $compatible->ie ) {
		wp_register_style( "wplc-bootstrap", admin_url( '/admin.php?wplc_action=loadbootstrap', __FILE__ ), array(), WPLC_PLUGIN_VERSION );
		wp_enqueue_style( "wplc-bootstrap" );
		$wplc_error              = new stdClass();
		$wplc_error->Title       = __( "Incompatible Environment", "wp-live-chat-support" );
		$wplc_error->Message     = ! $compatible->php ? __( "Your PHP version is lower than required.", "wp-live-chat-support" ) . "<br/>" : "";
		$wplc_error->Message     = $wplc_error->Message . ( ! $compatible->wp ? __( "Your Wordpress version is lower than required.", "wp-live-chat-support" ) . "<br/>" : "" );
		$wplc_error->Message     = $wplc_error->Message . ( ! $compatible->ie ? __( "Internet Explorer is not compatible with 3CX Live Chat plugin.", "wp-live-chat-support" ) : "" );
		$wplc_error->HtmlContent = "";
	}
}

function wplc_uninstall() {
	global $wpdb;
	global $wplc_tblname_offline_msgs;
	global $wplc_tblname_chats;
	global $wplc_tblname_msgs;
	global $wplc_tblname_chat_ratings;
	global $wplc_tblname_chat_departments;

	global $wplc_webhooks_table;
	global $wplc_custom_fields_table;
	global $wplc_tblname_actions_queue;
	global $wplc_quick_responses_table;

	$wplc_settings = TCXSettings::getSettings();
	wplc_cron_job_delete();
	wplc_check_guid( true, true );
	if ( $wplc_settings->wplc_delete_db_on_uninstall ) {
		$options = array(
			'WPLC_ACBC_SETTINGS',
			'wplc_advanced_settings',
			'wplc_api_key_valid',
			'wplc_api_secret_token',
			'WPLC_AUTO_RESPONDER_SETTINGS',
			'WPLC_BANNED_IP_ADDRESSES',
			'wplc_bh_settings',
			'WPLC_CHOOSE_ACCEPTING',
			'WPLC_CHOOSE_FIRST_RUN',
			'WPLC_CHOOSE_SETTINGS',
			'wplc_current_version',
			'WPLC_CUSTOM_CSS',
			'WPLC_CUSTOM_JS',
			'wplc_db_version',
			'wplc_dismiss_notice_bn',
			'WPLC_DOC_SUGG_SETTINGS',
			'WPLC_ENCRYPT_DEPREC_NOTICE_DISMISSED',
			'WPLC_ENCRYPT_FIRST_RUN',
			'WPLC_ENCRYPT_SETTINGS',
			'wplc_end_point_override',
			'WPLC_ET_FIRST_RUN',
			'WPLC_ET_SETTINGS',
			'WPLC_FIRST_TIME_TUTORIAL',
			'WPLC_GA_SETTINGS',
			'WPLC_GDPR_DISABLED_WARNING_DISMISSED',
			'WPLC_GDPR_ENABLED_AT_LEAST_ONCE',
			'WPLC_GUID',
			'WPLC_GUID_CHECK',
			'WPLC_GUID_URL',
			'wplc_gutenberg_settings',
			'WPLC_HIDE_CHAT',
			'WPLC_IC_FIRST_RUN',
			'WPLC_IC_SETTINGS',
			'WPLC_INEX_FIRST_RUN',
			'WPLC_INEX_SETTINGS',
			'WPLC_ma_FIRST_RUN',
			'WPLC_ma_SETTINGS',
			'wplc_mail_host',
			'wplc_mail_password',
			'wplc_mail_port',
			'wplc_mail_type',
			'wplc_mail_username',
			'WPLC_MOBILE_FIRST_RUN',
			'WPLC_MOBILE_SETTINGS',
			'wplc_node_server_secret_token',
			'wplc_node_v8_plus_notice_dismissed',
			'WPLC_POWERED_BY',
			'wplc_previous_is_typing',
			'wplc_pro_current_version',
			'WPLC_PRO_SETTINGS',
			'WPLC_SETTINGS',
			'wplc_stats',
			'WPLC_V8_FIRST_TIME',
			'WPLC_JSON_SETTINGS',
			'WPLC_SETUP_WIZARD_RUN',
			'WPLC_CM_SESSION_CHECK',
			'WPLC_NO_SERVER_MATCH',
			'WPLC_SHOW_CHANNEL_MIGRATION'
		);

		if ( !is_multisite() )
		{
			wplc_clean_database( $options );
		}
		else
		{
			$original_blog_id = get_current_blog_id();

			foreach ( get_sites() as $site )
			{
				switch_to_blog( $site->blog_id );
				wplc_clean_database( $options );
			}

			switch_to_blog( $original_blog_id );
		}

        $upload_dir   = wp_upload_dir();

        if ( ! empty( $upload_dir['basedir'] ) ) {
            $attachments_dir = $upload_dir['basedir'].'/wp_live_chat';
            if ( is_dir( $attachments_dir ) ) {
                TCXUtilsHelper::delete_dir($attachments_dir);
            }
        }
	}
}

/**
 * @param array $options
 * @param wpdb $wpdb
 */
function wplc_clean_database( $options ) {
	global $wpdb;
	foreach ( $options as $option ) {
		delete_option( $option );
	}
	if ( post_type_exists( 'wplc_quick_response' ) ) {
		unregister_post_type( 'wplc_quick_response' );
	}

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_offline_messages" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_chat_sessions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_chat_msgs" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_chat_files" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_chat_ratings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_webhooks" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_custom_fields" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_departments" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_actions_queue" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_quick_responses" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_roi_goals" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_roi_conversions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wplc_devices" );

	$admins = get_role( 'administrator' );
	if ( $admins !== null ) {
		$admins->remove_cap( 'wplc_ma_agent' );
		$admins->remove_cap( 'wplc_cap_admin' );
		$admins->remove_cap( "wplc_cap_show_history" );
		$admins->remove_cap( "wplc_cap_show_offline" );
	}

	$users = TCXAgentsHelper::get_agent_users();
	foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'wplc_user_department' );
		delete_user_meta( $user->ID, 'wplc_ma_agent' );
		delete_user_meta( $user->ID, 'wplc_user_tagline' );
		$user->remove_cap( 'wplc_ma_agent' );
		if ( ! in_array( 'administrator', (array) $user->roles ) ) {
			$user->remove_cap( 'wplc_cap_show_history' );
			$user->remove_cap( 'wplc_cap_show_offline' );
		}
	}
}

// Plugin initialisation
function wplc_init() {
	if ( ob_get_length() ) {
		ob_clean();
	}
	ob_start();

	//TCXSettings::initSettings();
	$wplc_settings = TCXSettings::getSettings();
	// Load Languages
	$plugin_dir = basename( dirname( __FILE__ ) ) . "/languages/";
	load_plugin_textdomain( 'wp-live-chat-support', false, $plugin_dir );

	$current_version = get_option( "wplc_current_version" );
	if ( ! isset( $current_version ) || $current_version != WPLC_PLUGIN_VERSION ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		do_action( "wplc_version_migration" );
		$wplc_updater = new TCXUpdater();
		$wplc_updater->versionMigration( $wplc_settings );
		$wplc_updater->wplc_set_users_capabilities();
		if ( $wplc_settings->wplc_channel === 'mcu' ) {
			TCXUtilsHelper::get_mcu_data( $wplc_settings->wplc_socket_url, $wplc_settings->wplc_chat_server_session, true );
		} else {
			wplc_check_guid( true );
		}
	}

	if ( is_admin() && ( ! isset( $_GET['page'] ) || $_GET['page'] != 'wplivechat-menu' ) && $wplc_settings->wplc_channel == "mcu" ) {
		TCXUtilsHelper::wplc_load_chat_js( false );
	}

}

function wplc_parameter_bool( $settings, $name ) {
	$param = 0;
	if ( ! empty( $settings ) && isset( $settings->$name ) ) {
		$param = intval( $settings->$name );
		if ( $param != 0 ) {
			$param = 1;
		}
	}

	return $param;
}

function wplc_check_guid( $force_update = false, $uninstall = false ) {
	$guid           = get_option( 'WPLC_GUID' );
	$guid_fqdn      = get_option( 'WPLC_GUID_URL' );
	$guid_lastcheck = intval( get_option( 'WPLC_GUID_CHECK' ) );
	if ( empty( $guid_lastcheck ) || time() - $guid_lastcheck > 86400 ) { // check at least once per day to ensure guid is updated properly
		$guid = '';
	}
	if ( empty( $guid ) || $guid_fqdn != get_option( 'siteurl' ) || $force_update ) { // guid not assigned or fqdn is changed since last assignment
		$wplc_settings = TCXSettings::getSettings();
		$server        = 0;
		if ( get_option( "WPLC_SETUP_WIZARD_RUN", 'NOTEXIST' ) === true || get_option( "WPLC_SETUP_WIZARD_RUN", 'NOTEXIST' ) === "1" ) {
			switch ( $wplc_settings->wplc_channel ) {
				case 'wp':
					$server = 1;
					break;
				case 'phone':
					$server = 2;
					break;
				case 'mcu':
					$server = 3;
					break;
			}
		}

		$gdpr = wplc_parameter_bool( $wplc_settings, 'wplc_gdpr_enabled' );

		$pbx_url_string = '';
		if ( $wplc_settings->wplc_channel === 'phone' ) {
			$pbx_url        = parse_url( esc_url_raw( $wplc_settings->wplc_channel_url ) );
			$pbx_url_string = ( array_key_exists( 'scheme', $pbx_url ) ? $pbx_url['scheme'] : '' ) . "://" . $pbx_url['host'] . ( array_key_exists( 'port', $pbx_url ) ? ":" . $pbx_url['port'] : '' );
		}
		$data_array = array(
			'method' => 'POST',
			'body'   => array(
				'method'    => 'get_guid',
				'url'       => get_option( 'siteurl' ),
				'server'    => $server,
				'gdpr'      => $gdpr,
				'version'   => WPLC_PLUGIN_VERSION,
				'pbxurl'    => $pbx_url_string,
				'uninstall' => $uninstall ? 1 : 0
			)
		);

		$response = wp_remote_post( WPLC_ACTIVATION_SERVER . '/api/v1', $data_array );
		update_option( 'WPLC_GUID', '' );
		if ( is_array( $response ) ) {
			if ( $response['response']['code'] == "200" ) {
				$data = json_decode( $response['body'], true );
				if ( $data && isset( $data['guid'] ) ) {
					update_option( 'WPLC_GUID', sanitize_text_field( $data["guid"] ) );
					update_option( 'WPLC_GUID_URL', get_option( 'siteurl' ) );
					update_option( 'WPLC_GUID_CHECK', time() );
				}
			}
		}
	}
}

function wplc_force_update_chat_server() {
	if ( ! empty( $_GET['wplc_action'] ) && $_GET['wplc_action'] == 'invalid_login' ) {
		$wplc_settings = TCXSettings::getSettings();
		TCXUtilsHelper::get_mcu_data( $wplc_settings->wplc_socket_url, $wplc_settings->wplc_chat_server_session, true,true );
		if(!empty($_GET['page'])&& $_GET['page']==='wplivechat-menu') {
			exit( wp_redirect( admin_url( 'admin.php?page=wplivechat-menu' ) ) );
		}
	}
}

function wplc_agent_login_monitor( $user_login, $user ) {
	if ( $user->has_cap( 'wplc_ma_agent' ) ) {
		TCXWebhookHelper::send_webhook( WebHookTypes::AGENT_LOGIN, array( "agent_id" => $user->ID ) );
	}
}


function wplc_protocol_agnostic_url( $url ) {
	return str_replace( "http://", "//", str_replace( "https://", "//", $url ) );
}

function wplc_plugins_url( $path, $plugin ) {

	$url = plugins_url( $path, $plugin );

	return wplc_protocol_agnostic_url( $url );
}

function wplc_check_version_compatibility() {
	global $wp_version;

	$result      = new stdClass();
	$result->php = true;
	$result->wp  = true;
	$result->ie  = true;


	if ( version_compare( $wp_version, WPLC_MIN_WP_VERSION ) < 0 ) {
		$result->wp = false;
	}

	if ( version_compare( phpversion(), WPLC_MIN_PHP_VERSION ) < 0 ) {
		$result->php = false;
	}

	if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) ) {
		$ua = htmlentities( $_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8' );
		if ( preg_match( '~MSIE|Internet Explorer~i', $ua ) || ( strpos( $ua, 'Trident/7.0' ) !== false && strpos( $ua, 'rv:11.0' ) !== false ) ) {
			$result->ie = false;
		}
	}

	return $result;
}

function wplc_load_bootstrap() {
	if ( ! empty( $_GET['wplc_action'] ) && $_GET['wplc_action'] == 'loadbootstrap' ) {
		require_once( WPLC_PLUGIN_DIR . "/includes/vendor/scssphp/scss.inc.php" );
		$bootstrap_css = WPLC_PLUGIN_DIR . '/css/vendor/bootstrap/wplc_bootstrap_' . str_replace( '.', '_', WPLC_PLUGIN_VERSION ) . '.css';
		$result        = '';
		ob_start();
		header( 'Content-Type: text/css' );
		if ( file_exists( $bootstrap_css ) ) {
			include( $bootstrap_css );
			$result = ob_get_contents();
		} else {
			include( WPLC_PLUGIN_DIR . "/css/vendor/bootstrap/bootstrap.min.css" );
			$result = '.bootstrap-wplc-content { ' . ob_get_contents() . '}';

			$scss   = new Compiler();
			$result = $scss->compile( $result );
			$open   = fopen( $bootstrap_css, "a" );
			$write  = fputs( $open, $result );
			fclose( $open );
		}
		ob_end_clean();
		die( $result );
	}
}

/**
 * Heartbeat integrations
 *
 */
add_filter( 'heartbeat_received', 'wplc_heartbeat_receive', 10, 2 );
add_filter( 'heartbeat_nopriv_received', 'wplc_heartbeat_receive', 10, 2 );
function wplc_heartbeat_receive( $response, $data ) {
	if ( array_key_exists( 'client', $data ) && $data['client'] == 'wplc_heartbeat' ) {
		if ( TCXAgentsHelper::is_agent() ) {
			TCXAgentsHelper::update_agent_time();
		}
		if ( is_admin() ) {
			$wplc_settings = TCXSettings::getSettings();
			if ( $wplc_settings->wplc_channel !== 'phone' ) {
				$online_agents = TCXAgentsHelper::get_online_agent_users();

				$response["online_agents"] = array_map( function ( $value ) {
					return $value->data->user_login;
				}, $online_agents );
			}

			$response["online_visitors"] = TCXChatHelper::get_visitors_count();

		}
	}

	return $response;
}


add_filter( 'allowed_http_origin', 'wplc_add_localhost_origin', 10, 2 );
function wplc_add_localhost_origin( $origin, $origin_arg ) {
	if ( array_key_exists( "HTTP_ORIGIN", $_SERVER ) && $_SERVER["HTTP_ORIGIN"] == get_home_url() ) {
		$origin = $_SERVER["HTTP_ORIGIN"];
	}

	if ( $origin === '' && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		if ( array_key_exists( "HTTP_ORIGIN", $_SERVER ) && ( $_SERVER["HTTP_ORIGIN"] == "http://localhost:8080"
		                                                      || $_SERVER["HTTP_ORIGIN"] == "http://localhost:8081" ) ) {
			$origin = "http://localhost:8080";
		}
	}

	return $origin;
}

function wplc_set_wplc_upload_dir_filter( $dirs ) {
	$dirs['subdir'] = '/wp_live_chat';
	$dirs['path']   = $dirs['basedir'] . '/wp_live_chat';
	$dirs['url']    = $dirs['baseurl'] . '/wp_live_chat';

	return $dirs;
}

add_filter( 'admin_footer_text', 'wplc_remove_footer', 99, 1 );
function wplc_remove_footer( $footer_text ) {
	return '';
}

function wplc_check_wizard_completed(){
	if(get_admin_page_parent() === 'wplivechat-menu' && $_GET['page'] !== 'wplc-getting-started'){
		wplc_redirect_to_wizard();
	}
}


function wplc_redirect_to_wizard() {
	if ( get_option( "WPLC_SETUP_WIZARD_RUN", 'NOTEXIST' ) !== "1" ) {
		exit( wp_redirect( admin_url( 'admin.php?page=wplc-getting-started' ) ) );
	}
}

add_filter('script_loader_tag', 'wplc_add_font_awesome_attribute', 10, 2);
function wplc_add_font_awesome_attribute( $tag, $handle ) {
	//This adding special attributes to script for font awesome in order to keep html <i> element during replacement with svg.
	if ( 'tcx-fa' !== $handle ) {
		return $tag;
	}

	//Fix for conflict with plugins which registers the fontawesome.
	//The conflict occurs only if in their tag for fontawesome have already declare attribute data-auto-replace-svg with different value than nest.
	if (!wp_script_is('fontawesome', 'registered') && !TCXUtilsHelper::check_registered_script_by_url('/(.*)(use\.fontawesome\.com)(.*)/') ) {
		return str_replace( ' src', ' defer data-auto-replace-svg="nest" src', $tag );
	}else{
		return "";
	}
}


