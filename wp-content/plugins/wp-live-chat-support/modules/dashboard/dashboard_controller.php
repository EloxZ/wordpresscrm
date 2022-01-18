<?php
if (!defined('ABSPATH')) {
    exit;
}

class DashboardController extends BaseController
{
   
    public function __construct($alias)
    {
        parent::__construct( __("Dashboard", 'wp-live-chat-support'),$alias);
    }

    private function load_stats(){

        $resultsDb = self::get_stats($this->db);
        
        $stats=array();
        if ($resultsDb) {
            $stats['0'] = array();
            $stats['0']['count'] = $resultsDb->today;
            $stats['0']['missed']= $resultsDb->missedtoday;
            $stats['0']['total']= $resultsDb->missedtoday+$resultsDb->today;

            $stats['30'] = array();
            $stats['30']['count']=  $resultsDb->day30;
            $stats['30']['missed']= $resultsDb->missed30;
            $stats['30']['total']= $resultsDb->missed30+$resultsDb->day30;

            $stats['60'] = array();
            $stats['60']['count']= $resultsDb->day60; 
            $stats['60']['missed']= $resultsDb->missed60;
            $stats['60']['total']= $resultsDb->missed60+$resultsDb->day60;

            $stats['90'] = array();
            $stats['90']['count']= $resultsDb->day90;
            $stats['90']['missed']= $resultsDb->missed90;
            $stats['90']['total']= $resultsDb->missed90+$resultsDb->day90;
        } 
        return $stats;
    }

    /*
    * Fetches news feed from the WPLC website
    */
    private function fetch_news_feed(){
        $response = wp_remote_get('https://feed.wp-livechat.com',	array( 'timeout' => 2, 'httpversion' => '1.1', 'user-agent'  => 'WordPress'));
        $response_code = wp_remote_retrieve_response_code($response);
        
        if ( is_wp_error( $response ) || !is_array( $response ) || $response_code !== 200 ) {
            return "ERROR";
        } else {
            return $response['body']; 
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
        $news =$this->fetch_news_feed();
        $this->view_data["online_users"] = TCXAgentsHelper::get_online_agent_users_count();
	    $this->view_data["online_visitors"] =TCXChatHelper::get_visitors_count();
        $this->view_data["news"] = $news=="ERROR"?__("An error has occured while fetching the news feed.",'wp-live-chat-support'):$news;
        $this->view_data["user"] = wp_get_current_user();
        $this->view_data["stats"] = $this->load_stats();
	    return $this->load_view(plugin_dir_path(__FILE__) . "dashboard_view.php",$return_html,$add_wrapper);
    }
    
    //db access

    public static function get_stats($db){
        global $wplc_tblname_chats;
        $sql = "SELECT  COUNT(IF(`timestamp` > CURDATE() AND `agent_id` <> 0 AND `status` != 0, 1, null)) AS today,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 30 DAY) AND `agent_id` <> 0 AND `status` != 0, 1, null)) AS day30,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 60 DAY) AND `agent_id` <> 0 AND `status` != 0, 1, null)) AS day60,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 90 DAY) AND `agent_id` <> 0 AND `status` != 0, 1, null)) AS day90,
                        COUNT(IF(`timestamp` > CURDATE() AND `status` = 0, 1, null)) AS missedtoday,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 30 DAY) AND `status` = 0, 1, null)) AS missed30,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 60 DAY) AND `status` = 0, 1, null)) AS missed60,
                        COUNT(IF(`timestamp` > DATE_SUB(NOW(), INTERVAL 90 DAY) AND `status` = 0, 1, null)) AS missed90 
                FROM `$wplc_tblname_chats`";
        return $db->get_row( $sql );
    }
}


?>