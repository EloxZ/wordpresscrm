<?php


class TCXQuickResponseHelper {

	public static function module_db_integration()
	{
		global $wplc_quick_responses_table;
		$sql = "
		        CREATE TABLE `" . $wplc_quick_responses_table . "` ( 
			    `id` INT(11) NOT NULL AUTO_INCREMENT , 
			    `title` VARCHAR(100) NOT NULL , 
			    `response` VARCHAR(2000) NOT NULL , 
			    `sort` INT(70) NOT NULL DEFAULT '1' , 
			    `status` TINYINT(1) NULL DEFAULT '1' , 
			    `updated_at` DATETIME(3) NOT NULL , 
			    PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
      ";

		dbDelta($sql);
	}

	public static function instantiate_quick_responses($db_quick_responses)
	{
		$results = array();

		foreach ($db_quick_responses as $key => $db_result) {
			$quick_response = new TCXQuickResponse();
			$quick_response->id = $db_result->id;
			$quick_response->title = esc_html(stripslashes($db_result->title));
			$quick_response->response = esc_html(stripslashes($db_result->response));
			$quick_response->sort = $db_result->sort;
			$quick_response->status = $db_result->status;
			$results[$key] = $quick_response;
		}

		return $results;
	}

}