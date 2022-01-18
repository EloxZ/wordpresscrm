<?php
if (!defined('ABSPATH')) {
    exit;
}

class OfflineMessagesController extends BaseController
{

    private $pager;
    
    
    public function __construct($alias)
    {
        parent::__construct( __("Offline Messages", 'wp-live-chat-support'),$alias);
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    public function load_offline_messages()
    {
        $results = array();
        $db_results = TCXOfflineMessagesData::get_offline_messages($this->db,$this->pager->rows_per_page, $this->pager->offset);

        foreach ($db_results as $key => $db_result) {
            $offline_message = new TCXOfflineMessage();
            $offline_message->id = $db_result->id;
            $offline_message->name = esc_html($db_result->name);
            $offline_message->email = esc_html($db_result->email);
	        $offline_message->phone = esc_html($db_result->phone);
            $offline_message->message = esc_html($db_result->message);
            $offline_message->timestamp =  date('Y-m-d H:i:s',strtotime($db_result->timestamp)+( get_option( 'gmt_offset' ) * 3600 )) ;
            $results[$key] = $offline_message;
        }

        return $results;
    }

    public function delete_offline_message($omid)
    {
        if ($omid > 0) {
                TCXOfflineMessagesData::remove_offline_message($this->db,$omid);
                if ($this->db->last_error) {
                  $this->view_data["delete_success"] = false;
                } else {
                  $this->view_data["delete_success"] = true;
                }
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
	    $this->pager = TCXUtilsHelper::wplc_get_pager(TCXOfflineMessagesData::generate_offline_messages_query());
        $this->view_data["chats"] = $this->load_offline_messages();
        $this->view_data["delete_offline_message_nonce"] = wp_create_nonce('delete_offline_message');
        $this->view_data["current_page"] = $this->pager->current_page;
        
        $this->view_data["page_links"] = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $this->pager->pages_counter,
            'current' => $this->pager->current_page,
        ));

	    return $this->load_view(plugin_dir_path(__FILE__) . "offline_messages_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_remove_offline_message");

        $removeParams = [];
        $removeParams[] = isset($_GET['omid']) ? intval(sanitize_text_field($_GET['omid'])) : -1;
        
        $this->available_actions[] = new TCXPageAction("execute_remove_offline_message", 9, "delete_offline_message", 'delete_offline_message', $removeParams);

    }

    //db access


}
?>