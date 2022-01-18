<?php
if (!defined('ABSPATH')) {
    exit;
}

class CustomFieldsController extends BaseController
{

    private $pager;
        
    public function __construct($alias)
    {
        parent::__construct( __("Custom Fields", 'wp-live-chat-support'),$alias);
        $this->init_actions();
        $this->parse_action();
    }

    public function load_custom_fields()
    {
        global $wplc_custom_fields_table;
        $results = array();
        $db_results = TCXCustomFieldsData::get_custom_fields($this->db,$this->pager->rows_per_page, $this->pager->offset);

        foreach ($db_results as $key => $db_result) {
            $custom_field = new TCXCustomField();
            $custom_field->id = $db_result->id;
            $custom_field->name = $db_result->field_name;
            $custom_field->type = $db_result->field_type;
            $custom_field->status = $db_result->status;
            $custom_field->setPlainContent($db_result->field_content);
            $results[$key] = $custom_field;
        }

        return $results;
    }

    public function delete_custom_field($cfid)
    {
        if ($cfid > 0) {
                TCXCustomFieldsData::remove_custom_field($this->db,$cfid);
                if ($this->db->last_error) {
                  $this->view_data["delete_success"] = false;
                } else {
                  $this->view_data["delete_success"] = true;
                }
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
	    $this->pager = TCXUtilsHelper::wplc_get_pager( TCXCustomFieldsData::generate_custom_fields_query() );
        $this->view_data["fields"] = $this->load_custom_fields();
        $this->view_data["delete_custom_field_nonce"] = wp_create_nonce('delete_custom_field');
        
        $this->view_data["current_page"] = $this->pager->current_page;
        
        $this->view_data["page_links"] = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $this->pager->pages_counter,
            'current' => $this->pager->current_page,
        ));
       
        
        return $this->load_view(plugin_dir_path(__FILE__) . "custom_fields_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_remove_custom_field");

        $removeParams = [];
        $removeParams[] = isset($_GET['cfid']) ? intval(sanitize_text_field($_GET['cfid'])) : -1;
        
        $this->available_actions[] = new TCXPageAction("execute_remove_custom_field", 9, "delete_custom_field", 'delete_custom_field', $removeParams);

    }

    //db access



}

?>