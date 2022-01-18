<?php
if (!defined('ABSPATH')) {
    exit;
}

class TCXWebhooksData
{

	public static function generate_webhooks_query()
	{
		global $wplc_webhooks_table;
		return "SELECT * FROM $wplc_webhooks_table ";
	}

	public static function get_webhooks($db,$limit,$offset)
	{

		$db_results = $db->get_results($db->prepare(self::generate_webhooks_query()." ORDER BY `id` ASC LIMIT %d OFFSET %d", $limit, $offset));
		return $db_results;
	}

	public static function get_webhook_by_event($db,$event_code) {
		global $wplc_webhooks_table;
		$db_results = $db->get_results($db->prepare("SELECT * FROM $wplc_webhooks_table WHERE `action` = %d  ORDER BY `id` ASC", $event_code));
		return $db_results;
	}

	public static function remove_webhook($db,$whid)
	{
		global $wplc_webhooks_table;
		$delete_sql = "DELETE FROM $wplc_webhooks_table WHERE `id` = '%d' LIMIT 1";
		$delete_sql = $db->prepare($delete_sql, $whid);
		$db->query($delete_sql);

	}

	public static function add_webhook($db,$webhook)
	{
		global $wplc_webhooks_table;
		return $db->insert(
			$wplc_webhooks_table,
			array(
				'url' 	=> $webhook->url,
				'action' 	=> $webhook->action,
				'method'	=> $webhook->method
			),
			array(
				'%s',
				'%d',
				'%s'
			)
		);
	}

	public static function update_webhook($db,$webhook)
	{
		global $wplc_webhooks_table;
		return $db->update(
			$wplc_webhooks_table,
			array(
				'url' 	=> $webhook->url,
				'action' 	=> $webhook->action,
				'method'	=> $webhook->method
			),
			array( 'id' => $webhook->id ),
			array(
				'%s',
				'%d',
				'%s'
			),
			array( '%d' )
		);
	}

	public static function get_webhook($db,$whid)
	{
		global $wplc_webhooks_table;
		$db_result = $db->get_row($db->prepare("SELECT * FROM $wplc_webhooks_table where `id`= %d",$whid ));
		return  $db_result;
	}
}
?>