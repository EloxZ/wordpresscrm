<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'wplc_initiate_js', 11 );
add_action( 'wp_enqueue_scripts', 'wplc_initiate_js', 11 );
add_action( 'admin_enqueue_scripts', 'wplc_initiate_admin_js', 11 );
add_action( 'wp_enqueue_scripts', 'wplc_initiate_front_js', 11 );


function wplc_initiate_js() {
	global $wplc_base_file;
	if ( is_admin() || TCXUtilsHelper::wplc_show_chat_client() ) {
		$emoji_data = array( "wplc_chaturl" => WPLC_PLUGIN_URL );
		wp_register_script( 'wplc-utils-js', wplc_plugins_url( '/js/wplc_utils.js', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc-utils-js' );
		wp_localize_script( 'wplc-utils-js', 'emoji_localization_data', $emoji_data );
	}
}

function wplc_initiate_admin_js() {
	global $wplc_base_file;
	wp_register_script( "wplc-initiate-admin", wplc_plugins_url( '/js/wplc_initiate.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	$wplc_notification_icon = plugin_dir_url( dirname( __FILE__ ) ) . 'images/wplc_notification_icon.png';
	$data                   = array(
		"locale"                          => get_locale(),
		"tcx_new_chat_notification_title" => __( 'New chat received', 'wp-live-chat-support' ),
		"tcx_new_chat_notification_text"  => __( "A new chat has been received. Please go the 'Live Chat' page to accept the chat", 'wp-live-chat-support' ),
		"tcx_new_chat_notification_icon"  => $wplc_notification_icon,
		"nonce"                           => wp_create_nonce( "wplc" ),
		"user_id"                         => get_current_user_id(),
		"wplc_ajaxurl"                    => admin_url( 'admin-ajax.php' ),
		"accepting_chats"                 => __( 'Online', 'wp-live-chat-support' ),
		"not_accepting_chats"             => __( 'Offline', 'wp-live-chat-support' ),
		"wplc_baseurl"                    => WPLC_PLUGIN_URL
	);

	wp_localize_script( 'wplc-initiate-admin', 'admin_localization_data', $data );
	wp_enqueue_script( 'wplc-initiate-admin' );

	wp_register_style( 'wplc-topbar-styles', wplc_plugins_url( '/css/wplc_topbar_styles.css', $wplc_base_file ), false, WPLC_PLUGIN_VERSION );
	wp_enqueue_style( 'wplc-topbar-styles' );
}

function wplc_initiate_front_js() {
	global $wplc_base_file;
	if ( TCXUtilsHelper::wplc_show_chat_client() ) {
		wp_register_script( "wplc-initiate", wplc_plugins_url( '/js/wplc_front_initiate.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'wplc-initiate' );
	}

}
