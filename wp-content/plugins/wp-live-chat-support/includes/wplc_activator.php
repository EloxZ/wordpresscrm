<?php
/** Settings page */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wplc_base_file;
// Activation Hook
register_activation_hook( $wplc_base_file, 'wplc_activate' );
register_deactivation_hook( $wplc_base_file, 'wplc_choose_deactivate' );
add_action( 'activated_plugin', 'wplc_redirect_on_activate' );

function wplc_activate( $plugin_file ) {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	wplc_check_guid(true);
	add_option( "wplc_db_version", WPLC_PLUGIN_VERSION );
	update_option( "wplc_db_version", WPLC_PLUGIN_VERSION );

	TCXSettings::initSettings();
	$wplc_settings = TCXSettings::getSettings();
	$wplc_updater = new TCXUpdater();

	do_action( "wplc_version_migration" );
	$wplc_updater->versionMigration($wplc_settings);
	$wplc_updater->wplc_complete_existing_chats();
	$wplc_updater->wplc_set_users_capabilities();

}


/**
 * Deactivate of the plugin - set the accepting chat variable to false
 * @return void
 * @since  1.0.00
 */
function wplc_choose_deactivate() {
	TCXAgentsHelper::set_agent_accepting( get_current_user_id(), false );
	wplc_check_guid( true,true );
	wplc_cron_job_delete();
}

function wplc_redirect_on_activate($plugin){
	if($plugin == 'wp-live-chat-support/wp-live-chat-support.php') {
		wplc_redirect_to_wizard();
	}
}
