<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'wplc_add_offline_messages_page_resources',11);
add_action('wplc_version_migration', 'wplc_offline_messages_activation' );
add_action('admin_menu', 'wplc_admin_offline_messages_menu', 5);

function wplc_admin_offline_messages_menu(){
	$wplc_settings = TCXSettings::getSettings();
	$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
	if($channel!=='phone') {
		$offline_messages_hook = wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Offline Messages', 'wp-live-chat-support' ), __( 'Offline Messages', 'wp-live-chat-support' ), 'wplc_cap_show_offline', 'wplivechat-menu-offline-messages', 'wplc_admin_offline_messages', 50 );
	}
}

function wplc_add_offline_messages_page_resources( $hook ) {
	if ( $hook != TCXUtilsHelper::wplc_get_page_hook( 'wplivechat-menu-offline-messages' ) ) {
		return;
	}
	global $wplc_base_file;


	wp_register_style( "wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-admin-styles" );
}

function wplc_admin_offline_messages()
{

    $offline_messages_controller = new OfflineMessagesController("offlineMessages");
    $offline_messages_controller->run();
    
}



function wplc_offline_messages_activation()
{
	TCXOfflineMessagesHelper::module_db_integration();
}

?>