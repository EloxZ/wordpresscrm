<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXQuickResponsesData {
	private static function get_quick_response_column_types() {
		return array(
			'id'         => array( 'type' => 'int', 'format' => '%d' ),
			'title'      => array( 'type' => 'int', 'format' => '%s' ),
			'response'   => array( 'type' => 'varchar', 'format' => '%s' ),
			'sort'       => array( 'type' => 'int', 'format' => '%d' ),
			'status'     => array( 'type' => 'int', 'format' => '%d' ),
			'updated_at' => array( 'type' => 'datetime', 'format' => '%s' ),
		);
	}

	public static function generate_quick_responses_query()
	{
		global $wplc_quick_responses_table;
		return "SELECT * FROM $wplc_quick_responses_table";
	}

	public static function get_quick_responses( $db, $limit = 100000, $offset = 0 ) {

		$db_results = $db->get_results( $db->prepare(self::generate_quick_responses_query(). " ORDER BY `sort` ASC LIMIT %d OFFSET %d", $limit, $offset ) );

		return $db_results;
	}

	public static function get_active_quick_responses( $db ) {
		global $wplc_quick_responses_table;
		$db_results = $db->get_results( "SELECT * FROM $wplc_quick_responses_table where `status`=1 ORDER BY `sort` ASC " );

		return $db_results;
	}

	public static function get_quick_response( $db, $qrid ) {
		global $wplc_quick_responses_table;

		return $db->get_row(
			$db->prepare( "SELECT * FROM $wplc_quick_responses_table WHERE `id` = %d", $qrid )
		);
	}


	public static function get_quick_response_property( $db, $qrid, $property ) {
		$result = self::get_quick_response( $db, $qrid );
		if ( $result != null ) {
			return $result->$property;
		} else {
			return null;
		}
	}


	public static function add_quick_response( $db, $data ) {
		global $wplc_quick_responses_table;

		$dataToSave    = array(
			'title'    => $data->title,
			'response' => $data->response,
			'sort'     => $data->sort,
			'status'   => $data->status
		);
		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_quick_response_column_types(), $dataToSave );

		if ( $db->insert( $wplc_quick_responses_table, $dataToSave, $columnsFormat ) ) {
			return $db->insert_id;
		} else {
			return - 1;
		}

	}

	public static function update_quick_response( $db, $qrid, $data ) {
		global $wplc_quick_responses_table;

		$data_to_update    = array(
			'title'    => $data->title,
			'response' => $data->response,
			'sort'     => $data->sort,
			'status'   => $data->status
		);
		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_quick_response_column_types(), $data_to_update );

		$result = $db->update(
			$wplc_quick_responses_table,
			$data_to_update,
			array( 'id' => $qrid ),
			$columnsFormat,
			array( '%d' )
		);

		return $result;
	}

	public static function update_quick_response_property( $db, $qrid, $property, $data ) {
		global $wplc_quick_responses_table;

		$columnsFormat = TCXUtilsHelper::filter_data_map_array( self::get_quick_response_column_types(), $property );

		$result = $db->update(
			$wplc_quick_responses_table,
			array(
				$property => $data
			),
			array( 'id' => $qrid ),
			$columnsFormat,
			array( '%d' )
		);

		return $result;
	}

	public static function remove_quick_response( $db, $qrid ) {
		global $wplc_quick_responses_table;

		$delete_sql = "DELETE FROM $wplc_quick_responses_table WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare( $delete_sql, $qrid );
		$db->query( $delete_sql );
	}

}

?>