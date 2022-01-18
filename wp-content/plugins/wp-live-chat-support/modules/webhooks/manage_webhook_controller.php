<?php
if (!defined('ABSPATH')) {
    exit;
}

class ManageWebHookController extends BaseController
{

    private $whid;
    
    public function __construct($alias,$whid=-1)
    {
        parent::__construct( __("Manage webhooks", 'wp-live-chat-support'),$alias);
        $this->whid = $whid;
        $this->init_actions();
        $this->parse_action($this->available_actions);
    }

    //Actions
    public function view($return_html = false, $add_wrapper=true)
    {
        if($this->whid>0)
        {
            $this->view_data["webhook"] = $this->load_webhook_data();    
        }
        else
        {
            $this->view_data["webhook"] = new TCXWebhook(); 
            $this->view_data["webhook"]->id=-1;
        }
        $this->view_data["webhook_events"]=TCXWebhookHelper::getWebhookActionsDictionary();
        $this->view_data["save_action_url"] = $this->view_data["webhook"]->getSaveUrl();
	    return $this->load_view(plugin_dir_path(__FILE__) . "manage_webhook_view.php",$return_html,$add_wrapper);
    }

    public function save_webhook($data)
    {
        $error = $this->validation($data);
        if($error->ErrorFound)
        {
            $this->view_data["error"] = $error;
            return; 
        }

        $webhook_to_save = new TCXWebhook();
        $webhook_to_save->id = intval(isset($data['wplc_webhook_id']) ? sanitize_text_field($data['wplc_webhook_id']): '-1');
        $webhook_to_save->url = esc_url_raw( $data['wplc_webhook_domain'] );
		$webhook_to_save->action = intval(sanitize_text_field($data['wplc_webhook_event']));
        $webhook_to_save->method= sanitize_text_field($data['wplc_webhook_method']);

        if($webhook_to_save->id<0)
        {
	        TCXWebhooksData::add_webhook($this->db,$webhook_to_save);
        }
        else
        {
	        TCXWebhooksData::update_webhook($this->db,$webhook_to_save);
        }

        if ($this->db->last_error) {
            $error = new TCXError();
            $error->ErrorFound = true;
            $error->ErrorHandleType = "Show";  
            $error->ErrorData->message= __("Error: Could not save webhook", 'wp-live-chat-support');
            $this->view_data["error"] = $error;
        }
    }

    //private functions
    private function init_actions()
    {
        $saveParams = [];
        $saveParams[] = isset($_POST) && !empty($_POST) ? $_POST : null;

        $this->available_actions[] = new TCXPageAction("save_webhook", 9, "save_webhook", 'save_webhook', $saveParams);
        $viewAction = array_filter(
            $this->available_actions,
            function ($action) {
                return $action->name == 'view';
            }
        );   

        if(count($viewAction)==1)
        {
            reset($viewAction)->required_nonce_key = 'edit_webhook';
        }
    }

    private function validation($data){
        $result = new TCXError();
        if($data==null)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Redirect";
            $result->ErrorData->url = admin_url("admin.php?page=wplivechat-manage-webhook&nonce=".wp_create_nonce('edit_webhook')."&whid=".$this->whid);
        }
        else if(strlen($data["wplc_webhook_domain"])==0)
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";  
            $result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Target URL', 'wp-live-chat-support'));
        }
        else if(!is_numeric($data["wplc_webhook_event"]))
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";  
            $result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Event', 'wp-live-chat-support'));
        }
        else if(strlen($data["wplc_webhook_method"])==0  )
        {
            $result->ErrorFound = true;
            $result->ErrorHandleType = "Show";
            $result->ErrorData->message =  sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Method', 'wp-live-chat-support'));
        }

        return $result;
    }

    private function load_webhook_data()
    {
        global $wplc_webhooks_table;
        $webhook = new TCXWebhook();
        $db_result = TCXWebhooksData::get_webhook($this->db,$this->whid);

        if($db_result)
        {
            $db_webhook = reset($db_result);
            $webhook->id = $db_result->id;
            $webhook->url = esc_url($db_result->url);
            $webhook->action = $db_result->action;
            $webhook->method = $db_result->method;
        }
        else
        {
            //TODO:create box on view for this error
            die(__("Web hook Not Found", 'wp-live-chat-support'));
        }
        return  $webhook;
    }


    //db access


    
  
}
?>