<?php
class TCXQuickResponse
{
    public function __construct()
    {}

    public $id;
    public $title;
    public $response;
    public $status;   
    public $sort;

    public function getStatusName(){
        if( $this->status ){  			
           return __("Active", 'wp-live-chat-support');  		
        } else {  				
            return __("Inactive", 'wp-live-chat-support');
        }
    }
        
    public function getSaveUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-quick-response&wplc_action=save_quick_response&nonce=".wp_create_nonce("save_quick_response")."&qrid=".$this->id);
    }
    
    public function getEditUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-quick-response&nonce=".wp_create_nonce('edit_quick_response')."&qrid=".$this->id);
    }

    public function getRemoveUrl()
    {
	    return admin_url("admin.php?page=wplivechat-menu-tools&wplc_action=prompt_remove_quick_response&qrid=".$this->id."#wplc_quick_responses_tab");
    }

}

?>