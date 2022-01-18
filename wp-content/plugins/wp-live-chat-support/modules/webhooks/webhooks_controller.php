<?php
if (!defined('ABSPATH')) {
    exit;
}

class WebHooksController extends BaseController
{

    private $pager;
        
    public function __construct($alias)
    {
        parent::__construct( __("Web Hooks", 'wp-live-chat-support'),$alias);
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    public function load_webhooks()
    {
        $results = array();
        $db_results = TCXWebhooksData::get_webhooks($this->db,$this->pager->rows_per_page, $this->pager->offset);

        foreach ($db_results as $key => $db_result) {
            $webhook = new TCXWebHook();
            $webhook->id = $db_result->id;
            $webhook->url =  esc_url($db_result->url);
            $webhook->action = $db_result->action;
            $webhook->method = $db_result->method;
            $results[$key] = $webhook;
        }

        return $results;
    }

    public function delete_webhook($whid)
    {
        if ($whid > 0) {
                TCXWebhooksData::remove_webhook($this->db,$whid);
                if ($this->db->last_error) {
                  $this->view_data["delete_success"] = false;
                } else {
                  $this->view_data["delete_success"] = true;
                }
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
	    $this->pager = TCXUtilsHelper::wplc_get_pager(TCXWebhooksData::generate_webhooks_query());
        $this->view_data["webhooks"] = $this->load_webhooks();
        $this->view_data["delete_webhook_nonce"] = wp_create_nonce('delete_webhook');
        
        $this->view_data["current_page"] = $this->pager->current_page;
        
        $this->view_data["page_links"] = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $this->pager->pages_counter,
            'current' => $this->pager->current_page,
        ));


	    return $this->load_view(plugin_dir_path(__FILE__) . "webhooks_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_remove_webhook");

        $removeParams = [];
        $removeParams[] = isset($_GET['whid']) ? intval(sanitize_text_field($_GET['whid'])) : -1;
        
        $this->available_actions[] = new TCXPageAction("execute_remove_webhook", 9, "delete_webhook", 'delete_webhook', $removeParams);

    }



    //db access



}

?>