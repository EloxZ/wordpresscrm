<?php
if (!defined('ABSPATH')) {
    exit;
}

class TCXOfflineMessagesData
{


	public static function generate_offline_messages_query()
	{
		global $wplc_tblname_offline_msgs;
		return "SELECT * FROM $wplc_tblname_offline_msgs ";

	}

	public static function get_offline_messages($db,$limit=100000,$offset=0)
	{
		global $wplc_tblname_offline_msgs;
		$db_results = $db->get_results($db->prepare(self::generate_offline_messages_query()." ORDER BY `timestamp` DESC LIMIT %d OFFSET %d", $limit,$offset));
		return $db_results;
	}

	public static function remove_offline_message($db,$omid)
	{
		global $wplc_tblname_offline_msgs;
		$delete_sql = "DELETE FROM $wplc_tblname_offline_msgs WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare($delete_sql, $omid);
		$db->query($delete_sql);
	}

	/**
	 * Saves offline messages to the database
	 * @param  string $name    User name
	 * @param  string $email   User email
	 * @param  string $message Message being saved
	 * @return Void
	 * @since  5.1.00
	 */
	public static function add_offline_message($db,$name, $email, $message, $phone){
		global $wplc_tblname_offline_msgs;

		$ins_array = array(
			'timestamp' => current_time('mysql',true),
			'name' => sanitize_text_field($name),
			'email' => sanitize_email($email),
			'phone' => sanitize_text_field($phone),
			'message' => implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $message ) ) ),
			'ip' => TCXUtilsHelper::get_user_ip(),
			'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'])
		);

		return $db->insert( $wplc_tblname_offline_msgs, $ins_array );
	}

}
?>