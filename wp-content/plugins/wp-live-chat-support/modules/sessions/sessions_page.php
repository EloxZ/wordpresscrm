<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'wplc_admin_sessions_menu', 5 );
add_action( 'admin_enqueue_scripts', 'wplc_add_sessions_page_resources', 11 );
add_action( 'admin_init', 'wplc_download_session' );


function wplc_admin_sessions_menu() {

	$wplc_settings = TCXSettings::getSettings();
	$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
	if($channel!=='phone') {
		$session_listing_hook = wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Chat History', 'wp-live-chat-support' ), __( 'Chat History', 'wp-live-chat-support' ), 'wplc_cap_show_history', 'wplivechat-menu-session', 'wplc_admin_session', 30 );
	}
}

function wplc_add_sessions_page_resources( $hook ) {
	if ( $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-session' )) {
		return;
	}
	global $wplc_base_file;


	wp_register_style("wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-admin-styles" );

	wp_register_style( 'wplc-sessions-style', wplc_plugins_url('/sessions_style.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
	wp_enqueue_style( 'wplc-sessions-style' );

	wp_register_style( "wplc-bootstrap", admin_url( '/admin.php?wplc_action=loadbootstrap', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-bootstrap" );
}

function wplc_admin_session() {
	$session_controller = new SessionsController( "session" );
	$session_controller->run();
}

function wplc_download_session() {
	global $wpdb;
	global $wplc_tblname_msgs;
	if ( ! TCXUtilsHelper::check_page_action( "wplivechat-menu-session", "download_session" ) ||
	     ! is_user_logged_in() || ! TCXAgentsHelper::is_agent() ||
	     ! isset( $_GET['cid'] ) ) {
		return;
	}

	if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'downloadSessionsNonce' ) ) {
		wp_die( __( "You do not have permission do perform this action", 'wp-live-chat-support' ) );
	}

	$cid = intval( $_GET['cid'] );
	$fileName = 'live_chat_session_' . md5( $cid ) . '.csv';

	header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
	header( 'Content-Description: File Transfer' );
	header( "Content-type: text/csv" );
	header( "Content-Disposition: attachment; filename={$fileName}" );
	header( "Expires: 0" );
	header( "Pragma: public" );

	$fh = @fopen( 'php://output', 'w' );

	$results = $wpdb->get_results( $wpdb->prepare( "
                SELECT *
                FROM $wplc_tblname_msgs
                WHERE `chat_sess_id` = %s
                ORDER BY `timestamp` ASC
                LIMIT 0,1000
                ", $cid )
	);

	$fields[] = array(
		'id'      => __( 'Chat ID', 'wp-live-chat-support' ),
		'msgfrom' => __( 'From', 'wp-live-chat-support' ),
		'msg'     => __( 'Message', 'wp-live-chat-support' ),
		'time'    => __( 'Timestamp', 'wp-live-chat-support' ),
		'orig'    => __( 'Origin', 'wp-live-chat-support' ),
	);

	foreach ( $results as $result => $key ) {
		if ( $key->originates == 2 ) {
			$user = __( 'user', 'wp-live-chat-support' );
		} else {
			$user = __( 'agent', 'wp-live-chat-support' );
		}

		$fields[] = array(
			'id'      => $key->chat_sess_id,
			'msgfrom' => $key->msgfrom,
			'msg'     => TCXChatHelper::decrypt_msg( $key->msg ),
			'time'    => date('Y-m-d H:i:s',strtotime($key->timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ,
			'orig'    => $user,
		);
	}

	foreach ( $fields as $field ) {
		fputcsv( $fh, $field, ",", '"' );
	}
	// Close the file
	fclose( $fh );
	// Make sure nothing else is sent, our file is done
	exit;
	//die();
}