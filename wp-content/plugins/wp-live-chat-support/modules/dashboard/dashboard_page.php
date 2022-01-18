<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'admin_menu', 'wplc_admin_dashboard_menu', 5 );
add_action( 'admin_enqueue_scripts', 'wplc_add_dashboard_page_resources', 11 );

function wplc_admin_dashboard_menu() {
	$wplc_settings = TCXSettings::getSettings();
	$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
	if($channel!=='phone') {
		$dashboard_hook = wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Dashboard', 'wp-live-chat-support' ), __( 'Dashboard', 'wp-live-chat-support' ), 'wplc_cap_admin', 'wplivechat-menu-dashboard', 'wplc_admin_dashboard_page', 10 );
	}
}

function wplc_add_dashboard_page_resources( $hook ) {
	if ( $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-dashboard' )) {
		return;
	}
	global $wplc_base_file;
	$wplc_settings = TCXSettings::getSettings();

	wp_register_style( 'wplc-dashboard-styles', wplc_plugins_url( '/dashboard_style.css', __FILE__ ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( 'wplc-dashboard-styles' );

	wp_register_script( 'wplc-dashboard', wplc_plugins_url( '/js/wplc_dashboard.js',__FILE__), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_script( 'wplc-dashboard' );

	/*	wp_register_script( 'font-awesome-js-svg', wplc_plugins_url( '/js/vendor/font-awesome/all.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'font-awesome-js-svg' );*/

	wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-fa' );

}


function wplc_admin_dashboard_page() {
	$support_dashboard = new DashboardController( "dashboard" );
	$support_dashboard->run();
}

?>