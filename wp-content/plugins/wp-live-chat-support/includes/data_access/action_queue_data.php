<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXActionQueueData {
	private static function get_action_queue_column_types() {
		return array(
			'chat_session_id'    => array( 'type' => 'int', 'format' => '%d' ),
			'message_id'         => array( 'type' => 'int', 'format' => '%d' ),
			'sender'             => array( 'type' => 'int', 'format' => '%d' ),
			'recipient'          => array( 'type' => 'varchar', 'format' => '%s' ),
			'action_type'        => array( 'type' => 'varchar', 'format' => '%s' ),
			'data'               => array( 'type' => 'varchar', 'format' => '%s' ),
			'timestamp_added_at' => array( 'type' => 'datetime', 'format' => '%s' ),
			'status'             => array( 'type' => 'int', 'format' => '%d' ),
			'code'               => array( 'type' => 'varchar', 'format' => '%s' ),
			'message_properties' => array( 'type' => 'varchar', 'format' => '%s' ),
		);
	}

	public static function add_action_in_queue( $db, $data ) {
		global $wplc_tblname_actions_queue;

		$data["code"] = TCXUtilsHelper::generateRandomString( 14 );

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_action_queue_column_types(), $data );

		if ( $db->insert( $wplc_tblname_actions_queue, $data, $columnsFormat ) ) {
			return true;
		} else {
			return false;
		}

	}

	public static function remove_actions_from_queue( $db, $session_id, $recipient, $change_code ) {
		global $wplc_tblname_actions_queue;

		$get_last_reported_timestamp_sql = "select timestamp_added_at from $wplc_tblname_actions_queue as dataToDelete where code like '%s'";
		$get_last_reported_timestamp_sql = $db->prepare( $get_last_reported_timestamp_sql, $change_code );
		$last_reported_timestamp         = $db->get_var( $get_last_reported_timestamp_sql );
		$delete_sql                      = "DELETE FROM $wplc_tblname_actions_queue  
						WHERE chat_session_id = %d  
							and recipient like '%s'
							and timestamp_added_at <  STR_TO_DATE('%s', '%%Y-%%m-%%d %%H:%%i:%%s.%%f') ;";
		$delete_sql                      = $db->prepare( $delete_sql, $session_id, $recipient, $last_reported_timestamp );
		$db->query( $delete_sql );
	}

	public static function clean_actions_from_queue( $db, $session_id, $recipient ) {
		global $wplc_tblname_actions_queue;

		$delete_sql = "DELETE FROM $wplc_tblname_actions_queue 
 						WHERE `chat_session_id` = %d 
							and  recipient like '%s';";
		$delete_sql = $db->prepare( $delete_sql, $session_id, $recipient );
		$db->query( $delete_sql );
	}

	public static function get_actions_from_queue( $db, $session_id, $recipient, $change_code ) {
		global $wplc_tblname_actions_queue;

		$select_sql = "select * 
						FROM $wplc_tblname_actions_queue 
						WHERE `chat_session_id` = %d 
						and (COALESCE(recipient,'') = '' or recipient like '%s')
						and (('%s' like 'NONE' and action_type<> %d ) or timestamp_added_at> (select timestamp_added_at from $wplc_tblname_actions_queue where code like '%s'));";
		$select_sql = $db->prepare( $select_sql, $session_id, $recipient, $change_code, ActionTypes::CHANGE_STATUS, $change_code );

		return $db->get_results( $select_sql );
	}

}

?>