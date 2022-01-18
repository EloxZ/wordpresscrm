<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_action( 'wp_ajax_nopriv_wplc_set_session_status', 'wplc_set_session_status' );


function wplc_set_session_status(){
	if(!isset($_POST["sessionId"]) || empty($_POST["sessionId"]) || !isset($_POST["lastAccess"]) || empty($_POST["lastAccess"])  )
	{
		die( TCXAjaxResponse::error_ajax_respose( "Invalid Request" ) );
	}
	global $wpdb;
	$chat = TCXChatData::get_chat_by_session($wpdb,sanitize_text_field($_POST["sessionId"]));
	TCXChatHelper::update_chat_status($chat,sanitize_text_field($_POST["lastAccess"]));

	die(TCXAjaxResponse::success_ajax_respose($_POST["sessionId"]));
}