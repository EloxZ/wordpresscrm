<?php
if (!defined('ABSPATH')) {
    exit;
}

class ManageCustomFieldController extends BaseController
{

    private $cfid;
    
    
    public function __construct($alias,$cfid=-1)
    {
        parent::__construct( __("Manage custom field", 'wp-live-chat-support'),$alias);
        $this->cfid = $cfid;
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    //Actions
    public function view($return_html = false, $add_wrapper=true)
    {
        if($this->cfid>0)
        {
            $this->view_data["custom_field"] = $this->load_custom_field_data();    
        }
        else
        {
            $this->view_data["custom_field"] = new TCXCustomField(); 
            $this->view_data["custom_field"]->id=-1;
	        $this->view_data["custom_field"]->status=1;
        }
        $this->view_data["save_action_url"] = $this->view_data["custom_field"]->getSaveUrl();
	    return $this->load_view(plugin_dir_path(__FILE__) . "manage_custom_field_view.php",$return_html,$add_wrapper);
    }

    public function load_custom_field_data()
    {
        global $wplc_custom_fields_table;
        $custom_field = new TCXCustomField();
        $db_result = TCXCustomFieldsData::get_custom_field($this->db, $this->cfid);

        if($db_result)
        {
            $db_custom_field = reset($db_result);
            $custom_field->id = $db_result->id;
            $custom_field->name = $db_result->field_name;
            $custom_field->type = $db_result->field_type;
            $custom_field->status = $db_result->status;
            $custom_field->setPlainContent($db_result->field_content);
        }
        else
        {
            //TODO:create box on view for this error
            die(__("Custom Field Not Found", 'wp-live-chat-support'));
        }
        return  $custom_field;
    }

    public function save_custom_field($data)
    {
        $error = $this->validation($data);
        if($error->ErrorFound)
        {
            $this->view_data["error"] = $error;
            return; 
        }
        
        $field_to_save = new TCXCustomField();
        $field_to_save->id = intval(isset($data['wplc_custom_field_id']) ? sanitize_text_field($data['wplc_custom_field_id']): '-1');
        $field_to_save->name = sanitize_text_field( $data['wplc_field_name'] );
		$field_to_save->type = intval(sanitize_text_field($data['wplc_field_type']));
	    $field_to_save->status = intval(sanitize_text_field($data['wplc_field_status']));
        $field_to_save->setPlainContent($field_to_save->type===1?$data['wplc_drop_down_values']:'');

        if($field_to_save->id<0)
        {
            TCXCustomFieldsData::add_custom_field($this->db,$field_to_save);
        }
        else
        {
            TCXCustomFieldsData::update_custom_field($this->db,$field_to_save);
        }

        if ($this->db->last_error) {
            $error = new TCXError();
            $error->ErrorFound = true;
            $error->ErrorHandleType = "Show";  
            $error->ErrorData->message= __("Error: Could not save custom field", 'wp-live-chat-support');
            $this->view_data["error"] = $error;
        }
    }

    //private functions
    private function init_actions()
    {
        $saveParams = [];
        $saveParams[] = isset($_POST) && !empty($_POST) ? $_POST : null;

        $this->available_actions[] = new TCXPageAction("save_custom_field", 9, "save_custom_field", 'save_custom_field', $saveParams);
        $viewAction = array_filter(
            $this->available_actions,
            function ($action) {
                return $action->name == 'view';
            }
        );   

        if(count($viewAction)==1)
        {
            reset($viewAction)->required_nonce_key = 'edit_custom_field';
        }
    }

    private function validation($data){
        $result = new TCXError();
        if($data==null)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Redirect";
            $result->ErrorData->url = admin_url("admin.php?page=wplivechat-manage-custom-field&nonce=".wp_create_nonce('edit_custom_field')."&cfid=".$this->cfid);
        }
        else if(strlen($data["wplc_field_name"])==0)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";  
            $result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Name', 'wp-live-chat-support'));
        }
        else if(!is_numeric($data["wplc_field_type"]))
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";  
            $result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Type', 'wp-live-chat-support'));
        }
        else if(intval($data["wplc_field_type"])==1 && strlen($data["wplc_drop_down_values"])==0 )
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";
            $result->ErrorData->message =  sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Dropdown content', 'wp-live-chat-support'));
        }

        return $result;
    }

    //db access

}
?>