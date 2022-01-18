<?php
if (!defined('ABSPATH')) {
    exit;
}

class DepartmentsController extends BaseController
{

    private $pager;
        
    public function __construct($alias)
    {
        parent::__construct( __("Departments", 'wp-live-chat-support'),$alias);
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    public function load_departments()
    {
        $results = array();
        $db_results = TCXDepartmentsData::get_departments($this->db,$this->pager->rows_per_page, $this->pager->offset);

        foreach ($db_results as $key => $db_result) {
            $department = new TCXDepartment();
            $department->id = $db_result->id;
            $department->name = esc_html(stripslashes($db_result->name));
            $results[$key] = $department;
        }

        return $results;
    }

    public function delete_department($depid)
    {
        if ($depid > 0) {
	        TCXDepartmentsData::remove_department($this->db,$depid);
                if ($this->db->last_error) {
                  $this->view_data["delete_success"] = false;
                } else {
                  $this->view_data["delete_success"] = true;
                }
        }
    }

    public function view($return_html = false, $add_wrapper=true)
    {
	    $this->pager =TCXUtilsHelper::wplc_get_pager(TCXDepartmentsData::generate_departments_query());
        $this->view_data["departments"] = $this->load_departments();
        $this->view_data["delete_department_nonce"] = wp_create_nonce('delete_department');
        
        $this->view_data["current_page"] = $this->pager->current_page;
        
        $this->view_data["page_links"] = paginate_links(array(
            'base' => add_query_arg('pagenum', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $this->pager->pages_counter,
            'current' => $this->pager->current_page,
        ));


	    return $this->load_view(plugin_dir_path(__FILE__) . "departments_view.php",$return_html,$add_wrapper);
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_remove_department");

        $removeParams = [];
        $removeParams[] = isset($_GET['depid']) ? intval(sanitize_text_field($_GET['depid'])) : -1;
        
        $this->available_actions[] = new TCXPageAction("execute_remove_department", 9, "delete_department", 'delete_department', $removeParams);

    }
}

?>