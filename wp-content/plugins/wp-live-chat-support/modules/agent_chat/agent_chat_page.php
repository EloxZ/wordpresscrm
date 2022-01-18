<?php
if (!defined('ABSPATH')) {
    exit;
}
add_action( 'admin_enqueue_scripts', 'wplc_add_agent_chat_page_resources', 0 );

function wplc_add_agent_chat_page_resources( $hook ) {
	$wplc_settings = TCXSettings::getSettings();
	if ( $hook != 'toplevel_page_wplivechat-menu' ) {
		return;
	}

	if ( $wplc_settings->wplc_channel === 'phone' ) {
		exit( wp_redirect( admin_url( 'admin.php?page=wplivechat-menu-settings' ) ) );
	}
    global $wplc_base_file;

	wp_enqueue_script( 'underscore' );
	wp_enqueue_script('jquery-ui-menu');

	wp_register_style( "wplc-bootstrap", admin_url( '/admin.php?wplc_action=loadbootstrap', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-bootstrap" );

	wp_register_style( 'wplc-agent-chat-style', wplc_plugins_url( '/agent_chat_style.css', __FILE__ ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( 'wplc-agent-chat-style' );

	wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-fa' );

	wp_register_script( 'tcx-emojione', wplc_plugins_url( '/js/emojione-light.min.js', __FILE__ ), array(), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-emojione' );

	TCXUtilsHelper::wplc_load_chat_js(true);
	wplc_load_agent_chat_box_js();
}

function wplc_admin_agent_chat() {
	$agent_local_chatbox_controller = new AgentChatController( "agentChat" );
	$agent_local_chatbox_controller->run();
}

function wplc_load_agent_chat_box_js() {
	global $wplc_base_file;

	wp_register_script( 'wplc-anchorme', wplc_plugins_url( '/js/anchorme/anchorme.js', __FILE__ ), array(), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'wplc-anchorme' );

	wp_register_script( 'wplc-agent-chat-chatbox', wplc_plugins_url( '/js/agent_chat_chatbox.js', __FILE__ ), array( 'wplc-utils-js' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'wplc-agent-chat-chatbox' );
}

