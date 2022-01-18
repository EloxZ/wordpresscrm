<?php
if (!defined('ABSPATH')) {
    exit;
}

class QuickResponsesController extends BaseController
{

    private $pager;
        
    public function __construct($alias)
    {
        parent::__construct( __("Quick Responses", 'wp-live-chat-support'),$alias);
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    public function load_quick_responses()
    {
        $db_results = TCXQuickResponsesData::get_quick_responses($this->db,$this->pager->rows_per_page, $this->pager->offset);

        return TCXQuickResponseHelper::instantiate_quick_responses($db_results);
    }

    public function delete_quick_response($qrid)
    {
        if ($qrid > 0) {
                TCXQuickResponsesData::remove_quick_response($this->db,$qrid);
                if ($this->db->last_error) {
                  $this->view_data["delete_success"] = false;
                } else {
                  $this->view_data["delete_success"] = true;
                }
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
	    $this->pager = TCXUtilsHelper::wplc_get_pager(TCXQuickResponsesData::generate_quick_responses_query());
        $this->view_data["quick_responses"] = $this->load_quick_responses();
        $this->view_data["delete_quick_response_nonce"] = wp_create_nonce('delete_quick_response');
        
        $this->view_data["current_page"] = $this->pager->current_page;
        
        $this->view_data["page_links"] = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $this->pager->pages_counter,
            'current' => $this->pager->current_page,
        ));
       
        
        return $this->load_view(plugin_dir_path(__FILE__) . "quick_responses_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_remove_quick_response");

        $removeParams = [];
        $removeParams[] = isset($_GET['qrid']) ? intval(sanitize_text_field($_GET['qrid'])) : -1;
        
        $this->available_actions[] = new TCXPageAction("execute_remove_quick_response", 9, "delete_quick_response", 'delete_quick_response', $removeParams);

    }


    //db access



}

?>