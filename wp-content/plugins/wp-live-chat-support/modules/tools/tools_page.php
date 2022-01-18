<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('admin_menu', 'wplc_admin_tools_menu', 5);
add_action( 'admin_menu', 'wplc_admin_dashboard_menu', 5 );
add_action( 'admin_enqueue_scripts', 'wplc_add_tools_page_resources', 11 );

function wplc_admin_tools_menu(){
	$wplc_settings = TCXSettings::getSettings();
	$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
	if($channel!=='phone') {
		$tools_listing_hook = wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Tools', 'wp-live-chat-support' ), __( 'Tools', 'wp-live-chat-support' ), 'wplc_cap_admin', 'wplivechat-menu-tools', 'wplc_admin_tools_page', 70 );
	}
}

function wplc_add_tools_page_resources( $hook ) {
	if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-tools' ))
	{
		return;
	}
	global $wplc_base_file;
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tooltip');
	wp_enqueue_script('jquery-ui-tabs');

	wp_register_style( 'wplc-jquery-ui', wplc_plugins_url('/css/vendor/jquery-ui/jquery-ui.css', $wplc_base_file), array(), WPLC_PLUGIN_VERSION);
	wp_enqueue_style( 'wplc-jquery-ui' );

	wp_register_style('wplc-tabs', wplc_plugins_url('/css/wplc_tabs.css', $wplc_base_file), array('wplc-jquery-ui'), WPLC_PLUGIN_VERSION);
	wp_enqueue_style('wplc-tabs');

	wp_register_style( 'wplc-admin-styles', wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION);
	wp_enqueue_style( 'wplc-admin-styles' );

	wp_register_style( 'wplc-tools-style', wplc_plugins_url( '/tools.css', __FILE__ ), array(), WPLC_PLUGIN_VERSION);
	wp_enqueue_style( 'wplc-tools-style' );

	wp_register_script('wplc-tools', wplc_plugins_url('/js/tools.js', __FILE__), array('jquery'), WPLC_PLUGIN_VERSION, true);
	wp_enqueue_script('wplc-tools');

	wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-fa' );
}

function wplc_admin_tools_page() {
	$tools_controller = new ToolsController("Tools");
	$tools_controller->run();
}

?>