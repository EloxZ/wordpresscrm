<?php
if (!defined('ABSPATH')) {
    exit;
}

class AgentChatController extends BaseController {
   
    public function __construct($alias)
    {
		parent::__construct( __( "Agent Chat", 'wp-live-chat-support' ), $alias );
    }

	public function load_quick_responses()
	{
		$db_results = TCXQuickResponsesData::get_active_quick_responses($this->db);

		return TCXQuickResponseHelper::instantiate_quick_responses($db_results);
	}


    public function view($return_html = false, $add_wrapper=true)
    {
    	$this->view_data["show_migration_message"] = get_option("WPLC_SHOW_CHANNEL_MIGRATION",false);
	    $this->view_data["quick_responses"] = $this->load_quick_responses();
	    $this->view_data["online_users"] = TCXAgentsHelper::get_online_agent_users_count();
	    $this->view_data["online_visitors"] =TCXChatHelper::get_visitors_count();
		return $this->load_view( plugin_dir_path( __FILE__ ) . "agent_chat_view.php",$return_html,$add_wrapper );
    }

}
?>