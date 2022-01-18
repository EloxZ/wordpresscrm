<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_action( 'wp_ajax_wplc_generate_new_node_token', 'wplc_generate_new_node_token' );
add_action( 'wp_ajax_wplc_generate_new_encryption_key', 'wplc_generate_new_encryption_key' );
add_action( 'wp_ajax_wplc_dismiss_migration_notice', 'wplc_dismiss_migration_notice' );


function wplc_generate_new_node_token() {
	if ( ! is_admin() ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Not an administrator." ) );
	}

	if ( ! check_ajax_referer( 'generate_new_token', 'security' ) ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Invalid nonce." ) );
	}
	$res = TCXUtilsHelper::node_server_token_get( true );
	die( TCXChatAjaxResponse::success_ajax_respose( array(
		'key'   => $res,
		'error' => ''
	) ) );
}

function wplc_generate_new_encryption_key() {
	if ( ! is_admin() ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Not an administrator." ) );
	}

	if ( ! check_ajax_referer( 'generate_new_encryption_key', 'security' ) ) {
		die( TCXChatAjaxResponse::error_ajax_respose( "Invalid nonce." ) );
	}

	$key = wplc_generate_encryption_key();
	if ( TCXSettings::setSettingValue( 'wplc_encryption_key', $key ) ) {

		die( TCXChatAjaxResponse::success_ajax_respose( array(
			'key' => $key
		) ) );
	} else {
		die( TCXChatAjaxResponse::error_ajax_respose( "Unable to set a new key." ) );
	}
}

function wplc_generate_encryption_key() {
	return md5( mt_rand() ) . md5( mt_rand() );
}

function wplc_dismiss_migration_notice() {
	delete_option("WPLC_SHOW_CHANNEL_MIGRATION");
}