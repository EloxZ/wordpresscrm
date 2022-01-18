<?php
if (!defined('ABSPATH')) {
    exit;
}

class TCXCustomFieldsData
{

	public static function generate_custom_fields_query(){
		global $wplc_custom_fields_table;
		return "SELECT * FROM $wplc_custom_fields_table";
	}

	public static function get_custom_fields($db,$limit=100000,$offset=0)
	{
		$db_results = $db->get_results($db->prepare(self::generate_custom_fields_query()." ORDER BY `field_name` ASC LIMIT %d OFFSET %d", $limit,$offset));
		return $db_results;
	}

	public static function get_active_custom_fields($db)
	{
		global $wplc_custom_fields_table;
		$db_results = $db->get_results("SELECT * FROM $wplc_custom_fields_table where status=1  ORDER BY `field_name`");
		return $db_results;
	}

	public static function get_custom_field($db,$cfid)
	{
		global $wplc_custom_fields_table;
		$db_result = $db->get_row($db->prepare("SELECT * FROM $wplc_custom_fields_table where `id`= %d",$cfid ));
		return  $db_result;
	}

	public static function remove_custom_field($db , $cfid)
	{
		global $wplc_custom_fields_table;

		$delete_sql = "DELETE FROM $wplc_custom_fields_table WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare($delete_sql, $cfid);
		$db->query($delete_sql);
	}

	public static function add_custom_field($db,$field)
	{
		global $wplc_custom_fields_table;
		return $db->insert(
			$wplc_custom_fields_table,
			array(
				'field_name' 	=> $field->name,
				'field_type' 	=> $field->type,
				'field_content'	=> $field->encodeContent(),
				'status'		=> $field->status
			),
			array(
				'%s',
				'%s',
				'%s',
				'%d',
			)
		);
	}

	public static function update_custom_field($db,$field)
	{
		global $wplc_custom_fields_table;
		return $db->update(
			$wplc_custom_fields_table,
			array(
				'field_name' 	=> $field->name,
				'field_type' 	=> $field->type,
				'field_content'	=> $field->encodeContent(),
				'status'		=> $field->status
			),
			array( 'id' => $field->id ),
			array(
				'%s',
				'%s',
				'%s',
				'%d',
			),
			array( '%d' )
		);
	}



}
?>