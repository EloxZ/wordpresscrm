<?php
if (!defined('ABSPATH')) {
    exit;
}

class ManageDepartmentController extends BaseController
{

    private $depid;
    
    public function __construct($alias,$depid=-1)
    {
        parent::__construct( __("Manage departments", 'wp-live-chat-support'),$alias);
        $this->depid = $depid;
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    //Actions
    public function view($return_html = false, $add_wrapper=true)
    {
        if($this->depid>0)
        {
            $this->view_data["department"] = $this->load_department_data();    
        }
        else
        {
            $this->view_data["department"] = new TCXDepartment(); 
            $this->view_data["department"]->id=-1;
        }
        $this->view_data["save_action_url"] = $this->view_data["department"]->getSaveUrl();
	    return $this->load_view(plugin_dir_path(__FILE__) . "manage_department_view.php",$return_html,$add_wrapper);
    }

    public function save_department($data)
    {
        $error = $this->validation($data);
        if($error->ErrorFound)
        {
            $this->view_data["error"] = $error;
            return; 
        }

        $department_to_save = new TCXDepartment();
        $department_to_save->id = intval(isset($data['wplc_department_id']) ? sanitize_text_field($data['wplc_department_id']): '-1');
        $department_to_save->name = sanitize_text_field( $data['wplc_department_name'] );

        if($department_to_save->id<0)
        {
	        TCXDepartmentsData::add_department($this->db,$department_to_save);
        }
        else
        {
	        TCXDepartmentsData::update_department($this->db,$department_to_save);
        }

        if ($this->db->last_error) {
            $error = new TCXError();
            $error->ErrorFound = true;
            $error->ErrorHandleType = "Show";  
            $error->ErrorData->message= __("Error: Could not save department", 'wp-live-chat-support');
            $this->view_data["error"] = $error;
        }
    }

    //private functions
    private function init_actions()
    {
        $saveParams = [];
        $saveParams[] = isset($_POST) && !empty($_POST) ? $_POST : null;

        $this->available_actions[] = new TCXPageAction("save_department", 9, "save_department", 'save_department', $saveParams);
        $viewAction = array_filter(
            $this->available_actions,
            function ($action) {
                return $action->name == 'view';
            }
        );   

        if(count($viewAction)==1)
        {
            reset($viewAction)->required_nonce_key = 'edit_department';
        }
    }

    private function validation($data){
        $result = new TCXError();
        if($data==null)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Redirect";
            $result->ErrorData->url = admin_url("admin.php?page=wplivechat-manage-department&nonce=".wp_create_nonce('edit_department')."&depid=".$this->depid);
        }
        else if(strlen($data["wplc_department_name"])==0)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";  
            $result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Name', 'wp-live-chat-support'));
        }

        return $result;
    }

    private function load_department_data()
    {
        global $wplc_tblname_chat_departments;
        $department = new TCXDepartment();
        $db_result = TCXDepartmentsData::get_department($this->db,$this->depid);

        if($db_result)
        {
            $db_department = reset($db_result);
            $department->id = $db_result->id;
            $department->name = esc_html(stripslashes( $db_result->name));
        }
        else
        {
            //TODO:create box on view for this error
            die(__("Department Not Found", 'wp-live-chat-support'));
        }
        return  $department;
    }


    //db access


    
  
}
?>