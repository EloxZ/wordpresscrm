<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_action( 'admin_init', 'wplc_schedule_get_mcu_data' );
add_action( 'admin_init', 'wplc_schedule_update_chats' );
add_action( 'wplc_cron_update_chats_hook', 'wplc_cron_update_chats' );
add_action( 'wplc_cron_get_mcu_data_hook', 'wplc_cron_get_mcu_data' );
add_filter( 'cron_schedules', 'wplc_five_minutes_schedule' );
add_filter( 'cron_schedules', 'wplc_one_and_half_hour_schedule' );


function wplc_cron_job_delete() {
	if ( wp_next_scheduled( 'wplc_cron_get_mcu_data_hook' ) ) {
		wp_clear_scheduled_hook( 'wplc_cron_get_mcu_data_hook' );
	}

	if ( wp_next_scheduled( 'wplc_cron_update_chats_hook' ) ) {
		wp_clear_scheduled_hook( 'wplc_cron_update_chats_hook' );
	}
}


function wplc_five_minutes_schedule( $schedules ) {
	$schedules['five_minutes'] = array(
		'interval' => 300,
		'display'  => esc_html__( 'Every Five Minutes' ),
	);

	return $schedules;
}

function wplc_one_and_half_hour_schedule( $schedules ) {
	$schedules['one_and_half_hour'] = array(
		'interval' => 5400,
		'display'  => esc_html__( 'Every one and a half hour' ),
	);

	return $schedules;
}

function wplc_schedule_update_chats() {
	if ( ! wp_next_scheduled( 'wplc_cron_update_chats_hook' ) ) {
		wp_schedule_event( time() + 60, 'five_minutes', 'wplc_cron_update_chats_hook' );
	}
}

function wplc_schedule_get_mcu_data() {
	if ( ! wp_next_scheduled( 'wplc_cron_get_mcu_data_hook' ) ) {
		wp_schedule_event( time() + 60, 'one_and_half_hour', 'wplc_cron_get_mcu_data_hook' );
	}
}

function wplc_cron_update_chats() {
	global $wpdb;
	$chats = TCXChatData::get_incomplete_chats( $wpdb, - 1, array(
		ChatStatus::OLD_ENDED,
		ChatStatus::PENDING_AGENT,
		ChatStatus::ACTIVE,
		ChatStatus::NOT_STARTED,
	) );
	TCXChatHelper::update_chat_statuses( $chats, false );
}

function wplc_cron_get_mcu_data() {
	$wplc_settings = TCXSettings::getSettings();
	if ( $wplc_settings->wplc_channel === 'mcu' ) {

		$guid = get_option( 'WPLC_GUID' );
		wplc_check_guid( empty($guid) );
		$guid = get_option( 'WPLC_GUID' );

		$cm_session_lastcheck = intval( get_option( 'WPLC_CM_SESSION_CHECK' ) );
		if ( empty( $wplc_settings->wplc_socket_url ) || empty( $wplc_settings->wplc_chat_server_session )
		     || empty( $cm_session_lastcheck )
		     || time() - $cm_session_lastcheck > 5400
		) {
			TCXUtilsHelper::wplc_get_mcu_data_from_cm( $guid );
		}
	}
}