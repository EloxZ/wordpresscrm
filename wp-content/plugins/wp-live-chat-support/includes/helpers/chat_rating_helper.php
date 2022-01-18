<?php


class TCXChatRatingHelper {

	public static function update_chat_statuses( $chats ) {
		foreach ( $chats as $chat ) {
			self::update_chat_status( $chat );
		}
	}

	public static function set_chat_rating( $cid, $rating_score, $rating_comments ) {
		global $wpdb;
		$chat   = TCXChatData::get_chat( $wpdb, $cid );
		$rating = TCXChatRatingData::get_chat_rating_by_chat( $wpdb, $cid );

		if ( $rating != null && ! empty( $rating ) ) {
			return TCXChatRatingData::update_chat_rating( $wpdb, $rating->id, array(
				'rating'    => $rating_score,
				'comments'  => $rating_comments,
				'timestamp' => current_time( 'mysql', true ),
			) );
		} else {
			return TCXChatRatingData::add_chat_rating( $wpdb, array(
				'cid'       => $chat->id,
				'aid'       => $chat->agent_id,
				'rating'    => $rating_score,
				'comments'  => $rating_comments,
				'timestamp' => current_time( 'mysql', true ),
			) );
		}
	}

	public static function module_db_integration() {
		global $wplc_tblname_chat_ratings;
		$sql = "
        CREATE TABLE `" . $wplc_tblname_chat_ratings . "` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `timestamp` datetime NOT NULL,
          `cid` int(11) NOT NULL,
          `aid` int(11) NOT NULL,
          `rating` int(11) NOT NULL,
          `comments` VARCHAR(2000),
          PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
      ";

		dbDelta( $sql );
	}


}