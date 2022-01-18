<?php

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_action( 'wp_ajax_wplc_get_chat_info', 'wplc_get_chat_info' );
add_action( 'wp_ajax_wplc_set_agent_chat', 'wplc_set_agent_chat' );
add_action( 'wp_ajax_wplc_get_chat_messages', 'wplc_get_chat_messages' );
add_action( 'wp_ajax_wplc_admin_send_msg', 'wplc_admin_send_msg' );
add_action( 'wp_ajax_wplc_admin_upload_file', 'wplc_admin_upload_file' );
add_action( 'wp_ajax_wplc_admin_close_chat', 'wplc_admin_close_chat' );
add_action( 'wp_ajax_wplc_choose_accepting', 'wplc_set_agent_accepting' );
add_action( 'wp_ajax_wplc_keep_alive', 'wplc_keep_alive' );
add_action( 'wp_ajax_wplc_block_visitor', 'wplc_block_visitor' );


function wplc_get_chat_info() {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	$current_user_id  = wplc_validate_agent_call();
	$chat             = TCXChatData::get_chat( $wpdb, $cid );
	if($chat->agent_id!=0) {
        $agent = get_user_by( 'id', $chat->agent_id );
        $chat->agent_name = $agent!=null? $agent->display_name:"Uknown";
	}
	$chat->other      = maybe_unserialize( $chat->other );
	$chat_client_info = json_decode( $chat->ip );

	$chat->blocked = false;
	if ( property_exists( $chat_client_info, 'ip' ) ) {
		$wplc_settings = TCXSettings::getSettings();
		$banned_ips    = $wplc_settings->wplc_banned_ips;
		$chat->blocked = in_array( $chat_client_info->ip, $banned_ips );
	}

	if ( array_key_exists( 'custom_fields', $chat->other ) && $chat->other['custom_fields'] != null ) {
		foreach ( $chat->other['custom_fields'] as $key => $custom_field ) {
			$chat->other['custom_fields'][ $key ]['name']  = esc_html( $custom_field['name'] );
			$chat->other['custom_fields'][ $key ]['value'] = esc_html( $custom_field['value'] );
		}
	}

	die( TCXChatAjaxResponse::success_ajax_respose( $chat, $chat->status ) );
}

function wplc_set_agent_chat( $is_transfer = false ) {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	$current_user_id = wplc_validate_agent_call();
	$chat            = TCXChatData::get_chat( $wpdb, $cid );
	if ( $chat->agent_id != $current_user_id && ( $is_transfer || $chat->agent_id <= 0 ) ) {

		if ( TCXChatHelper::set_agent_id( $cid, $current_user_id ) !== false ) {
			if ( TCXChatHelper::set_chat_status( $cid, ChatStatus::ACTIVE ) !== false ) {
				TCXChatHelper::set_messages_agent_id( $cid, $current_user_id );
				TCXWebhookHelper::send_webhook( WebHookTypes::AGENT_ACCEPT, array( "chat_id" => $cid ) );
				die( TCXChatAjaxResponse::success_ajax_respose( true, ChatStatus::ACTIVE ) );
			}
		}
	} else if ( $chat->agent_id > 0 && $chat->agent_id != $current_user_id && ! $is_transfer ) {
		die( TCXChatAjaxResponse::error_ajax_respose( __( "Another agent already joined this chat session." ) ) );
	} else {
		die( TCXChatAjaxResponse::success_ajax_respose( true, ChatStatus::ACTIVE ) );
	}
	die( TCXChatAjaxResponse::error_ajax_respose( false ) );
}

function wplc_get_chat_messages() {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	$chat     = TCXChatData::get_chat( $wpdb, $cid );
	$agent_id = wplc_validate_agent_call( $chat->agent_id );
	$messages = wplc_load_all_messages( $chat );
	die( TCXChatAjaxResponse::success_ajax_respose( $messages, $chat->status ) );
}

function wplc_admin_send_msg() {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}
	$message = '';
	if ( strlen( $_POST['msg'] ) > 0 ) {
		$message = stripslashes( $_POST['msg'] );
	}

	$chat     = TCXChatData::get_chat( $wpdb, $cid );
	$agent_id = wplc_validate_agent_call( $chat->agent_id );

	$new_msg_id = TCXChatHelper::add_chat_message( UserTypes::AGENT, $cid, $message, null, $agent_id );
	$result     = new stdClass();
	$result->id = - 1;
	if ( $new_msg_id >= 0 ) {
		$added_message      = TCXChatData::get_chat_message( $wpdb, $new_msg_id );
		$result->id         = $added_message->id;
		$result->added_at   = $added_message->timestamp;
		$result->msg        = TCXChatHelper::decrypt_msg( $added_message->msg );
		$result->originates = $added_message->originates;
	}
	die( TCXChatAjaxResponse::success_ajax_respose( $result, $chat->status ) );
}

function wplc_admin_upload_file() {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	$chat     = TCXChatData::get_chat( $wpdb, $cid );
	$agent_id = wplc_validate_agent_call( $chat->agent_id );

	$response = new stdClass();
	add_filter( 'upload_dir', 'wplc_set_wplc_upload_dir_filter' );
	foreach ( $_FILES as $file ) {
		$upload_overrides = array( 'test_form' => false );
		$file_info        = wp_handle_upload( $file, $upload_overrides );
		$chat_msg         = $file_info['url'];

		$fileData           = new stdClass();
		$fileData->FileName = $file['name'];
		$fileData->FileLink = $file_info['url'];
		$fileData->FileSize = $file['size'];

		$wplc_rec_msg = TCXChatHelper::add_chat_message( UserTypes::AGENT, $cid, $chat_msg, $fileData, $agent_id );

		$added_message        = TCXChatData::get_chat_message( $wpdb, $wplc_rec_msg );
		$response->fileLink   = $fileData->FileLink;
		$response->fileName   = $fileData->FileName;
		$response->fileSize   = $fileData->FileSize;
		$response->id         = $wplc_rec_msg;
		$response->added_at   = $added_message->timestamp;
		$response->originates = $added_message->originates;
	}
	remove_filter( 'upload_dir', 'wplc_set_wplc_upload_dir_filter' );
	die( TCXChatAjaxResponse::success_ajax_respose( $response ) );
}

function wplc_load_all_messages( $chat ) {
	$result   = array();
	$messages = TCXChatHelper::get_chat_messages( $chat->id );
	if ( $messages && is_array( $messages ) ) {
		$result = wplc_convert_to_client_messages( $messages );
	}

	return $result;
}

function wplc_convert_to_client_messages( $messages ) {
	$result = array_map( function ( $message ) {
		return wplc_convert_to_client_message( $message );
	}, $messages );

	return $result;
}

function wplc_convert_to_client_message( $message ) {
	$message_properties = json_decode( $message->other );
	$message_response   = array(
		"id"         => $message->id,
		"msg"        => TCXChatHelper::decrypt_msg( $message->msg ),
		"code"       => "NONE",
		"added_at"   => $message->timestamp,
		"originates" => $message->originates,
		"is_file"    => is_object( $message_properties ) && isset( $message_properties->isFile ) ? $message_properties->isFile : false
	);

	return $message_response;
}

function wplc_admin_close_chat() {
	global $wpdb;
	$cid        = 0;
	$end_status = ChatStatus::ENDED_BY_AGENT;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	if ( ! empty( $_POST['status'] ) ) {
		$end_status = sanitize_text_field( $_POST['status'] );
	}

	$chat     = TCXChatData::get_chat( $wpdb, $cid );
	$agent_id = wplc_validate_agent_call( $chat->agent_id );

	if ( TCXChatHelper::end_chat( $cid, $end_status ) ) {
		die( TCXChatAjaxResponse::success_ajax_respose( "CHAT ENDED", $end_status ) );
	} else {
		die( TCXChatAjaxResponse::error_ajax_respose( "Unable to end Chat" ) );
	}

}

function wplc_validate_agent_call( $chat_agent_id = - 1 ) {
	if ( ! TCXAgentsHelper::is_agent() ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Not an agent." ) );
	}

	if ( ! check_ajax_referer( 'wplc', 'security', false ) ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Invalid nonce." ) );
	}

	$agent_id = get_current_user_id();
	if ( $chat_agent_id > 0 && $agent_id != $chat_agent_id ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Current agent doesn't match with the agent who is responsible for that chat" ) );
	}

	return $agent_id;
}

function wplc_set_agent_accepting() {
	$is_online = false;
	if ( ! empty( $_POST['is_online'] ) ) {
		$is_online = $_POST['is_online'] == "true";
	}
	$current_user_id = wplc_validate_agent_call();
	if ( TCXAgentsHelper::is_agent( $current_user_id ) ) {
		TCXAgentsHelper::set_agent_accepting( $current_user_id, $is_online );
		if ( ! $is_online ) {
			delete_user_meta( $current_user_id, "wplc_chat_agent_online" );
		}
	}

	$online_agents = TCXAgentsHelper::get_online_agent_users();

	$result = array_map( function ( $value ) {
		return $value->data->user_login;
	}, $online_agents );

	die( TCXChatAjaxResponse::success_ajax_respose( $result ) );
}

function wplc_keep_alive() {
	$agent_id = wplc_validate_agent_call();
	if ( $agent_id >= 0 ) {
		TCXAgentsHelper::update_agent_time( $agent_id );
		die( TCXChatAjaxResponse::success_ajax_respose( $agent_id ) );
	} else {
		die( TCXChatAjaxResponse::error_ajax_respose( "Not an agent." ) );
	}
}

function wplc_block_visitor() {
	global $wpdb;
	$cid = 0;
	if ( ! empty( $_POST['cid'] ) ) {
		$cid = sanitize_text_field( $_POST['cid'] );
	}

	$chat       = TCXChatData::get_chat( $wpdb, $cid );
	$agent_id   = wplc_validate_agent_call( $chat->agent_id );
	$end_status = ChatStatus::ENDED_DUE_BLOCK;

	if ( TCXChatHelper::end_chat( $cid, $end_status ) ) {
		$wplc_settings = TCXSettings::getSettings();

		$banned_ips       = $wplc_settings->wplc_banned_ips;
		$chat_client_info = json_decode( $chat->ip );
		if ( property_exists( $chat_client_info, 'ip' ) ) {
			if ( ! in_array( $chat_client_info->ip, $banned_ips ) ) {
				array_push( $banned_ips, $chat_client_info->ip );
				TCXSettings::setSettingValue( 'wplc_banned_ips', $banned_ips );
			}
			die( TCXChatAjaxResponse::success_ajax_respose( "IP Blocked", $end_status ) );
		}
	}
	die( TCXChatAjaxResponse::error_ajax_respose( "Unable to block visitor" ) );
}

