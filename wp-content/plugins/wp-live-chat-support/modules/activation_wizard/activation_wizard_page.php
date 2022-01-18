<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action( 'admin_enqueue_scripts', 'wplc_add_activation_wizard_page_resources' );
add_action( 'admin_menu', 'wplc_admin_activation_wizard_menu', 5 );
/*update_option( "WPLC_SETUP_WIZARD_RUN", false );*/
function wplc_admin_activation_wizard_menu() {
	$activationwizard_hook = wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Getting Started', 'wp-live-chat-support' ), __( 'Getting Started', 'wp-live-chat-support' ), 'wplc_cap_admin', 'wplc-getting-started', 'wplc_admin_activation_wizard', 0 );
}

function wplc_add_activation_wizard_page_resources( $hook ) {
	if ( $hook != TCXUtilsHelper::wplc_get_page_hook('wplc-getting-started')) {
		return;
	}
	global $wplc_base_file;
	$settings = TCXSettings::getSettings();

	wp_register_style( "wplc-bootstrap", admin_url( '/admin.php?wplc_action=loadbootstrap', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-bootstrap" );

	wp_register_style( "wplc-activation-wizard-css", wplc_plugins_url( '/activation_wizard_style.css', __FILE__ ), array( 'wplc-bootstrap' ), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-activation-wizard-css" );

	wp_register_style("wplc-component-theme-picker-style", wplc_plugins_url( '/components/theme_picker/theme_picker.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-component-theme-picker-style" );

	wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-fa' );

	wp_register_script( "wplc-activation-wizard", wplc_plugins_url( '/js/activation_wizard.js', __FILE__ ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( "wplc-activation-wizard" );

	wp_register_script("wplc-component-theme-picker", wplc_plugins_url( '/components/theme_picker/js/theme_picker.js', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION,true );
	wp_enqueue_script("wplc-component-theme-picker" );

	$script_data = array(
		'chat_list_url' => $settings->wplc_channel == 'phone' ? admin_url( 'admin.php?page=wplivechat-menu-settings' ) : admin_url( 'admin.php?page=wplivechat-menu' ),
	);

	wp_localize_script( 'wplc-activation-wizard', 'localization_data', $script_data );

	wp_register_script( "wplc-chat_app", wplc_plugins_url( '/modules/chat_client/js/callus.js', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'wplc-chat_app' );

}

function wplc_admin_activation_wizard() {
	$activation_wizard_controller = new ActivationWizardController( "activationWizard" );
	$activation_wizard_controller->run();
}

