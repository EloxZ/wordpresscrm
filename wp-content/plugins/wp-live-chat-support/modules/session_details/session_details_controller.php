<?php
if (!defined('ABSPATH')) {
    exit;
}

class SessionDetailsController extends BaseController
{
    private $cid;

    public function __construct($alias,$cid)
    {
        parent::__construct(__("Session Details", 'wp-live-chat-support'), $alias);
        $this->cid = $cid;
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    public function load_session_details()
    {
	    return TCXChatHelper::get_chat_including_messages($this->cid);
    }

    public function view($return_html = false, $add_wrapper=true)
    {
        $sessionDetails = $this->load_session_details();
        if( !array_key_exists('session' ,$sessionDetails))
        {
        	wp_redirect(admin_url( 'admin.php?page=wplivechat-menu-history' ));
	        exit;
        }
        $this->view_data["session"] = $sessionDetails['session'];
        $this->view_data["session_messages"] = $sessionDetails['messages'];
        $this->view_data["browser"] = isset( $sessionDetails['session']->client_data['user_agent']) ? TCXUtilsHelper::get_browser_string($sessionDetails['session']->client_data['user_agent']) : "Unknown";
        $this->view_data["browser_image"] = WPLC_PLUGIN_URL . 'images/browsers/' . TCXUtilsHelper::get_browser_image($this->view_data["browser"], "16");
        $this->view_data["rating_class"] = $sessionDetails['session']->rating ===1 ?"rating-good":($sessionDetails['session']->rating ===0 ?"rating-bad":"");
	    $this->view_data["rating_comments"] = $sessionDetails['session']->rating_comments;
	    return $this->load_view(plugin_dir_path(__FILE__) . "session_details_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
    }

}
?>