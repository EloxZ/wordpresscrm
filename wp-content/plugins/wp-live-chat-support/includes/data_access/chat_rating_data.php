<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXChatRatingData {
	private static function get_chat_rating_column_types() {
		return array(
			'id'        => array( 'type' => 'int', 'format' => '%d' ),
			'timestamp' => array( 'type' => 'datetime', 'format' => '%s' ),
			'cid'       => array( 'type' => 'int', 'format' => '%d' ),
			'aid'       => array( 'type' => 'int', 'format' => '%d' ),
			'rating'    => array( 'type' => 'int', 'format' => '%d' ),
			'comments'    => array( 'type' => 'varchar', 'format' => '%s' ),
		);
	}

	public static function get_chat_rating( $db, $crid ) {
		global $wplc_tblname_chat_ratings;
		$db_result = $db->get_row( $db->prepare( "SELECT * FROM $wplc_tblname_chat_ratings where `id`= %d", $crid ) );

		return $db_result;
	}

	public static function get_chat_rating_by_chat( $db, $cid ) {
		global $wplc_tblname_chat_ratings;
		$db_result = $db->get_row( $db->prepare( "SELECT * FROM $wplc_tblname_chat_ratings where `cid`= %d", $cid ) );

		return $db_result;
	}

	public static function remove_chat_rating( $db, $crid ) {
		global $wplc_tblname_chat_ratings;

		$delete_sql = "DELETE FROM $wplc_tblname_chat_ratings WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare( $delete_sql, $crid );
		$db->query( $delete_sql );
	}

	public static function add_chat_rating( $db, $data ) {
		global $wplc_tblname_chat_ratings;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array(self::get_chat_rating_column_types(),$data);

		if ( $db->insert( $wplc_tblname_chat_ratings, $data, $columnsFormat ) ) {
			return $db->insert_id;
		} else {
			return - 1;
		}
	}

	public static function update_chat_rating( $db, $rateid, $data_to_update ) {
		global $wplc_tblname_chat_ratings;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_chat_rating_column_types(), $data_to_update );

		$result = $db->update(
			$wplc_tblname_chat_ratings,
			$data_to_update,
			array( 'id' => $rateid ),
			$columnsFormat,
			array( '%d' )
		);

		return $result;
	}


}

?>