<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXChatData {
	private static function get_chat_column_types() {
		return array(
			'id'                    => array( 'type' => 'int', 'format' => '%d' ),
			'timestamp'             => array( 'type' => 'datetime', 'format' => '%s' ),
			'name'                  => array( 'type' => 'varchar', 'format' => '%s' ),
			'email'                 => array( 'type' => 'varchar', 'format' => '%s' ),
			'ip'                    => array( 'type' => 'varchar', 'format' => '%s' ),
			'status'                => array( 'type' => 'int', 'format' => '%d' ),
			'session'               => array( 'type' => 'varchar', 'format' => '%s' ),
			'url'                   => array( 'type' => 'varchar', 'format' => '%s' ),
			'last_active_timestamp' => array( 'type' => 'datetime', 'format' => '%s' ),
			'last_action_by'        => array( 'type' => 'int', 'format' => '%d' ),
			'agent_id'              => array( 'type' => 'int', 'format' => '%d' ),
			'other'                 => array( 'type' => 'longtext', 'format' => '%s' ),
			'department_id'         => array( 'type' => 'int', 'format' => '%d' ),
		);
	}

	private static function get_chat_message_column_types() {
		return array(
			'id'           => array( 'type' => 'int', 'format' => '%d' ),
			'chat_sess_id' => array( 'type' => 'int', 'format' => '%d' ),
			'msgfrom'      => array( 'type' => 'varchar', 'format' => '%s' ),
			'msg'          => array( 'type' => 'longtext', 'format' => '%s' ),
			'timestamp'    => array( 'type' => 'datetime', 'format' => '%s' ),
			'status'       => array( 'type' => 'int', 'format' => '%d' ),
			'originates'   => array( 'type' => 'int', 'format' => '%d' ),
			'other'        => array( 'type' => 'longtext', 'format' => '%s' ),
			'afrom'        => array( 'type' => 'int', 'format' => '%d' ),
			'ato'          => array( 'type' => 'int', 'format' => '%d' ),
		);
	}

	public static function get_chat( $db, $cid ) {
		global $wplc_tblname_chats;

		return $db->get_row(
			$db->prepare( "SELECT * FROM $wplc_tblname_chats WHERE `id` = %d", $cid )
		);
	}

	public static function get_chat_by_session( $db, $session ) {
		global $wplc_tblname_chats;

		return $db->get_row(
			$db->prepare( "SELECT * FROM $wplc_tblname_chats WHERE `session` like '%s'", $session )
		);
	}

	public static function get_incomplete_chats( $db, $department_id = - 1 ,$ignored_statuses=array() ) {
		global $wplc_tblname_chats;

		$ignored_statuses = empty($ignored_statuses) ? array( ChatStatus::NOT_STARTED,ChatStatus::BROWSE ): $ignored_statuses;

		$db_results = $db->get_results( $db->prepare( "SELECT * FROM $wplc_tblname_chats WHERE `completed` = 0 and `status` not in (" . implode( ',', $ignored_statuses ) . ") and (`department_id` = %d or %d=-1)  ORDER BY `timestamp` DESC ", $department_id, $department_id ) );

		return $db_results;
	}

	public static function get_visitors_count($db) {
		global $wplc_tblname_chats;

		$ignored_statuses = array( ChatStatus::NOT_STARTED );

		$db_result = $db->get_var( "SELECT count(*) FROM $wplc_tblname_chats WHERE `completed` = 0 and `status` not in (" . implode( ',', $ignored_statuses ) . ")");

		return $db_result;
	}

	public static function generate_missed_chat_query(  ) {
		global $wplc_tblname_chats;
		return "SELECT * FROM $wplc_tblname_chats WHERE `status` = 0";
	}

	public static function get_missed_chats( $db, $limit = 100000, $offset = 0 ) {
		global $wplc_tblname_chats;
		$db_results = $db->get_results( $db->prepare( self::generate_missed_chat_query()."  ORDER BY `timestamp` DESC LIMIT %d OFFSET %d", $limit, $offset ) );

		return $db_results;
	}

	public static function generate_history_query($db) {
		global $wplc_tblname_chats;
		global $wplc_tblname_chat_ratings;
		$completed_statuses = array(
			ChatStatus::ENDED_BY_CLIENT,
			ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
			ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
			ChatStatus::ENDED_BY_AGENT,
			ChatStatus::ENDED_DUE_BLOCK,
			ChatStatus::MISSED,
			ChatStatus::OLD_ENDED
		);

		return  "SELECT {$wplc_tblname_chats}.*,$wplc_tblname_chat_ratings.rating,$wplc_tblname_chat_ratings.comments ,users.display_name as 'agent_name' FROM $wplc_tblname_chats
        left join {$db->users} users on users.id = $wplc_tblname_chats.agent_id
        left join $wplc_tblname_chat_ratings on $wplc_tblname_chat_ratings.cid = $wplc_tblname_chats.id
        WHERE  status in ( " . implode( ',', $completed_statuses ) . " )";
	}

	public static function generate_search_history_query($db , $email, $status,$prepare_query= false ) {
		global $wplc_tblname_chats;
		global $wplc_tblname_chat_ratings;
		$completed_statuses = array(
			ChatStatus::ENDED_BY_CLIENT,
			ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
			ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
			ChatStatus::ENDED_BY_AGENT,
			ChatStatus::MISSED,
			ChatStatus::OLD_ENDED
		);

		$query = "SELECT {$wplc_tblname_chats}.*,$wplc_tblname_chat_ratings.rating,$wplc_tblname_chat_ratings.comments ,users.display_name as 'agent_name' FROM $wplc_tblname_chats
		left join {$db->users} users on users.id = $wplc_tblname_chats.agent_id
        left join $wplc_tblname_chat_ratings on $wplc_tblname_chat_ratings.cid = $wplc_tblname_chats.id
        WHERE (status = %d or (%d = -1 and status in ( " . implode( ',', $completed_statuses ) . " ))) and  (email like '%s' or '' like '%s' )";

		if($prepare_query)
		{
			return $db->prepare($query,$status,$status,'%' . $email . '%',$email);
		}
		else
		{
			return $query;
		}

	}

	public static function get_history( $db, $limit = 100000, $offset = 0 ) {

		$query = self::generate_history_query($db) . " 
        ORDER BY `timestamp` DESC
        LIMIT %d OFFSET %d";

		$db_results = $db->get_results( $db->prepare( $query, $limit, $offset ) );

		return $db_results;
	}

	public static function search_history( $db, $email, $status, $limit = 100000, $offset = 0 ) {

		$query = self::generate_search_history_query($db, $email, $status ) . " 
        ORDER BY `timestamp` DESC
        LIMIT %d OFFSET %d";

		$db_results = $db->get_results( $db->prepare( $query,$status, $status,'%' . $email . '%',$email, $limit, $offset ) );

		return $db_results;
	}

	public static function get_session_details( $db, $cid ) {
		global $wplc_tblname_chats;
		global $wplc_tblname_msgs;
		global $wplc_tblname_chat_ratings;

		$db_results = $db->get_results( $db->prepare( "
        select 
        $wplc_tblname_chats.other as other_data,
        $wplc_tblname_chats.ip as client_data,
        $wplc_tblname_chats.id as session_id,
        $wplc_tblname_chats.timestamp,
        $wplc_tblname_chats.last_active_timestamp,
        $wplc_tblname_chats.name,
        $wplc_tblname_chats.email,
        $wplc_tblname_chats.url,
        $wplc_tblname_msgs.id as message_id,
        $wplc_tblname_msgs.timestamp as message_timestamp,
        $wplc_tblname_msgs.msgfrom ,
        $wplc_tblname_msgs.msg,
        $wplc_tblname_msgs.originates,
        $wplc_tblname_chat_ratings.rating, 
        $wplc_tblname_chat_ratings.comments 
        from $wplc_tblname_chats
        left join $wplc_tblname_msgs on $wplc_tblname_msgs.chat_sess_id = $wplc_tblname_chats.id
        left join $wplc_tblname_chat_ratings on $wplc_tblname_chat_ratings.cid = $wplc_tblname_chats.id
        where $wplc_tblname_chats.id = %d
        order by $wplc_tblname_chats.timestamp 
        ", $cid ) );

		return $db_results;
	}

	public static function get_chat_property( $db, $cid, $property ) {
		$result = self::get_chat( $db, $cid );
		if ( $result != null ) {
			return $result->$property;
		} else {
			return null;
		}
	}

	public static function get_chats_by_status( $db, $statuses ) {
		global $wplc_tblname_chats;

		if ( ! is_array( $statuses ) ) {
			if ( is_numeric( $statuses ) ) {
				$statuses = array( $statuses );
			} else {
				throw new Error( "Not valid status array" );
			}
		}

		return $db->get_results(
			"
        SELECT *
        FROM $wplc_tblname_chats
        WHERE `status` in (" . implode( ',', $statuses ) . ")"
		);
	}

	public static function get_chat_files( $db, $cid ) {
		global $wplc_tblname_files;

		$sql = "SELECT *
            FROM $wplc_tblname_files
            WHERE `cid` = %d ";

		return $db->get_results( $db->prepare( $sql, $cid ) );
	}


	public static function add_chat( $db, $data ) {
		global $wplc_tblname_chats;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_column_types(), $data );
		if ( $db->insert( $wplc_tblname_chats, $data, $columnsFormat ) ) {
			return $db->insert_id;
		} else {
			return - 1;
		}

	}

	public static function update_chat( $db, $cid, $data_to_update ) {
		global $wplc_tblname_chats;

		if(array_key_exists('status', $data_to_update))
		{
			//NEVER update status without flow_check, due to concurrent pollings may occur conflicts!!
			self::update_chat_status_with_flow_check( $db, $cid, $data_to_update['status'] );
			unset($data_to_update['status']);
		}


		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_column_types(), $data_to_update );

		$result = $db->update(
			$wplc_tblname_chats,
			$data_to_update,
			array( 'id' => $cid ),
			$columnsFormat,
			array( '%d' )
		);

		return $result;
	}

	public static function update_chat_property( $db, $cid, $property, $data ) {
		global $wplc_tblname_chats;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_column_types(), $property );

		$result = $db->update(
			$wplc_tblname_chats,
			array(
				$property => $data
			),
			array( 'id' => $cid ),
			$columnsFormat,
			array( '%d' )
		);

		return $result;
	}

	public static function remove_chat( $db, $cid ) {
		global $wplc_tblname_chats;
		global $wplc_tblname_msgs;
		global $wplc_tblname_files;

		$delete_msgs_sql = "DELETE FROM $wplc_tblname_msgs WHERE `chat_sess_id` = '%d'";
		$delete_msgs_sql = $db->prepare( $delete_msgs_sql, $cid );
		$db->query( $delete_msgs_sql );

		$delete_sql = "DELETE FROM $wplc_tblname_files WHERE `cid` = '%d'";
		$delete_sql = $db->prepare( $delete_sql, $cid );
		$db->query( $delete_sql );

		$delete_sql = "DELETE FROM $wplc_tblname_chats WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare( $delete_sql, $cid );
		$db->query( $delete_sql );
	}

	public static function update_chat_status_with_flow_check( $db, $cid, $status ) {
		global $wplc_tblname_chats;

		$update_sql = "update $wplc_tblname_chats set status = %d WHERE `id` = %d ";

		$incompatible_statuses = array();
		switch ( $status ) {
			case ChatStatus::ACTIVE:
				$incompatible_statuses = [
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT,
					ChatStatus::MISSED
				];
				break;
			case ChatStatus::PENDING_AGENT:
				$incompatible_statuses = [
					ChatStatus::ACTIVE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT,
					ChatStatus::MISSED
				];
				break;
			case ChatStatus::BROWSE:
				$incompatible_statuses = [
					ChatStatus::PENDING_AGENT,
					ChatStatus::ACTIVE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT,
					ChatStatus::MISSED
				];
				break;
			case ChatStatus::MISSED:
				$incompatible_statuses = [
					ChatStatus::ACTIVE,
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT
				];
				break;
			case ChatStatus::ENDED_DUE_CLIENT_INACTIVITY:
				$incompatible_statuses = [
					ChatStatus::PENDING_AGENT,
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT
				];
				break;
			case ChatStatus::ENDED_DUE_AGENT_INACTIVITY:
				$incompatible_statuses = [
					ChatStatus::PENDING_AGENT,
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT
				];
				break;
			case ChatStatus::ENDED_BY_CLIENT:
				$incompatible_statuses = [
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_AGENT
				];
				break;
			case ChatStatus::ENDED_BY_AGENT:
				$incompatible_statuses = [
					ChatStatus::PENDING_AGENT,
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT
				];
				break;
			case ChatStatus::ENDED_DUE_BLOCK:
				$incompatible_statuses = [
					ChatStatus::BROWSE,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT
				];
				break;
			case ChatStatus::NOT_STARTED:
				$incompatible_statuses = [
					ChatStatus::ACTIVE,
					ChatStatus::PENDING_AGENT,
					ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
					ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
					ChatStatus::ENDED_BY_CLIENT,
					ChatStatus::ENDED_BY_AGENT
				];
				break;
		}

		if ( ! empty( $incompatible_statuses ) ) {
			$update_sql = $update_sql . " and status not in (" . implode( ',', $incompatible_statuses ) . ")";
		}

		$update_sql = $db->prepare( $update_sql, $status, $cid );

		return $db->query( $update_sql );
	}

	public static function truncate_history( $db ) {
		global $wplc_tblname_chats;
		global $wplc_tblname_msgs;
		$db->query( "TRUNCATE TABLE $wplc_tblname_chats" );
		$db->query( "TRUNCATE TABLE $wplc_tblname_msgs" );
	}

	public static function update_message_property( $db, $mid, $property, $data ) {
		global $wplc_tblname_msgs;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_message_column_types(), $property );

		return $db->update(
			$wplc_tblname_msgs,
			array(
				$property => $data
			),
			array( 'id' => $mid ),
			$columnsFormat,
			array( '%d' )
		);
	}

	public static function update_batch_message_property( $db, $mids, $property, $data ) {
		global $wplc_tblname_msgs;
		$ids_literal = '';
		if ( is_array( $mids ) && ! empty( $mids ) ) {
			$ids_literal = implode( ',', $mids );
		} else {
			throw new Error( "Not valid ids array" );
		}

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_message_column_types(), $property );

		$sql = $db->prepare(
			"UPDATE $wplc_tblname_msgs
				SET $property = $columnsFormat[$property]
				WHERE id in ($ids_literal)
				", $data );

		$result = $db->query( $sql );

		return $result;
	}

	public static function add_chat_message( $db, $cid, $fromname, $msg, $originator, $other_data, $sender_agent_id = 0, $recipient_agent_id = 0 ) {
		global $wplc_tblname_msgs;

		return $db->insert(
			$wplc_tblname_msgs,
			array(
				'chat_sess_id' => $cid,
				'timestamp'    => current_time( 'mysql',true ),
				'msgfrom'      => $fromname,
				'msg'          => maybe_serialize( $msg ),
				'status'       => 0,
				'originates'   => $originator,
				'other'        => json_encode( $other_data ),
				'afrom'        => $sender_agent_id,
				'ato'          => $recipient_agent_id
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d'
			)
		);
	}

	public static function get_chat_messages( $db, $cid, $statuses = array(), $refersTo = 'ANYONE', $limit = 10000000 ) {
		global $wplc_tblname_msgs;
		$statuses_literal = '';
		$receiver_id      = - 1;
		if ( is_array( $statuses ) && ! empty( $statuses ) ) {
			$statuses_literal = implode( ',', $statuses );
		}

		$sql = "SELECT *
            FROM $wplc_tblname_msgs
            WHERE `chat_sess_id` = %d "
		       . ( strlen( $statuses_literal ) > 0 ? " and `status`in (" . $statuses_literal . ")" : "" );

		switch ( $refersTo ) {
			case "ANYONE":
				//this clause is just for integrity of parameters in order to call execution once in the end
				//of the function , it will be always true
				$sql .= " AND (%d = -1) ";
				break;
			case "User":
				$sql .= " AND (`originates` = 1 OR `originates` = 0) AND (%d = -1)";
				break;
			default:
				$sql         .= " AND `ato` = %d";
				$receiver_id = intval( $refersTo );
				break;
		};

		$sql .= " ORDER BY `timestamp` ASC LIMIT %d";

		return $db->get_results( $db->prepare( $sql, $cid, $receiver_id, $limit ) );

	}

	public static function get_chat_message( $db, $mid ) {
		global $wplc_tblname_msgs;
		$sql = "SELECT *
            FROM $wplc_tblname_msgs
            WHERE `id` = %d ";

		return $db->get_row( $db->prepare( $sql, $mid ) );
	}

	public static function add_chat_file( $db, $cid, $message_id, $fileName,$fileUrl ) {
		global $wplc_tblname_files;

		return $db->insert(
			$wplc_tblname_files,
			array(
				'cid'      => $cid,
				'message_id'      => $message_id,
				'filename' => $fileName,
				'fileurl'  => $fileUrl,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
			)
		);
	}

}

?>