<?php


class TCXCustomFieldHelper {

	public static function module_db_integration()
	{
		global $wplc_custom_fields_table;
		$sql = "
            CREATE TABLE `" . $wplc_custom_fields_table . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `field_name` varchar(700) NOT NULL,
                `field_type` int(11) NOT NULL,
                `field_content` varchar(700) NOT NULL,          
                `status` tinyint(1) NOT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
        ";

		dbDelta($sql);
	}

}