<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TCXDepartmentsData {



	public static function generate_departments_query()
	{
		global $wplc_tblname_chat_departments;
		return "SELECT * FROM $wplc_tblname_chat_departments ";
	}

	public static function get_departments($db,$limit=1000000,$offset=0)
	{
		$db_results = $db->get_results($db->prepare(self::generate_departments_query()." ORDER BY `id` ASC LIMIT %d OFFSET %d", $limit, $offset));
		return $db_results;
	}

	public static function remove_department($db,$depid)
	{
		global $wplc_tblname_chat_departments;
		$delete_sql = "DELETE FROM $wplc_tblname_chat_departments WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare($delete_sql, $depid);
		$db->query($delete_sql);

	}

	public static function add_department($db,$department)
	{
		global $wplc_tblname_chat_departments;
		return $db->insert(
			$wplc_tblname_chat_departments,
			array(
				'name' 	=> $department->name
			),
			array(
				'%s'
			)
		);
	}

	public static function update_department($db,$department)
	{
		global $wplc_tblname_chat_departments;
		return $db->update(
			$wplc_tblname_chat_departments,
			array(
				'name' 	=> $department->name
			),
			array( 'id' => $department->id ),
			array(
				'%s'
			),
			array( '%d' )
		);
	}

	public static function get_department($db,$depid)
	{
		global $wplc_tblname_chat_departments;
		$db_result = $db->get_row($db->prepare("SELECT * FROM $wplc_tblname_chat_departments where `id`= %d",$depid ));
		return  $db_result;
	}

	public static function module_db_integration()
	{
		global $wpdb;
		global $wplc_tblname_chat_departments;
		global $wplc_tblname_chats;
		$sql = "
        CREATE TABLE `" . $wplc_tblname_chat_departments . "` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(700) NOT NULL,
            PRIMARY KEY  (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
      ";

		dbDelta($sql);

		$department_field_sql = " SHOW COLUMNS FROM $wplc_tblname_chats WHERE `Field` = 'department_id'";
		$results = $wpdb->get_results($department_field_sql);
		if (!$results) {
			$department_field_sql = "
                ALTER TABLE `$wplc_tblname_chats` ADD `department_id` INT(11) NOT NULL ;
            ";
			$wpdb->query($department_field_sql);
		}
	}

}

?>