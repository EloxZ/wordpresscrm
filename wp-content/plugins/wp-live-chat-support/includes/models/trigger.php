<?php
class TCXTrigger
{
    public function __construct()
    {}

    public $id;
    public $name;
    public $type;
    public $content;
    public $show_content;
    public $status;

    public function setContent($pages,$secondsDelay,$scrollPercentage,$html)
    {
        $data = array("pages"=>sanitize_text_field($pages),"secs"=>intval(sanitize_text_field($secondsDelay)),"perc"=>intval(sanitize_text_field($scrollPercentage)),"html"=>wp_kses(stripslashes($html),self::get_allowed_tags()));
        $this->content = maybe_serialize( $data );
    }

    public function getTypeName()
    {

        $this->type = intval($this->type);

        switch ($this->type) {
            case 0:
                $result = __("Page Trigger", 'wp-live-chat-support');
                break;
            case 1:
                $result = __("Time Trigger", 'wp-live-chat-support');
                break;
            case 2:
                $result = __("Scroll Trigger", 'wp-live-chat-support');
                break;
            case 3:
                $result = __("Page Leave Trigger", 'wp-live-chat-support');
                break;
            default:
                $result = __("Unknown", 'wp-live-chat-support');
                break;
        }
        return $result;
    }

    public function getStatusName()
    {

        $this->status = intval($this->status);

        switch ($this->status) {
            case 0:
                $result = __("Disabled", 'wp-live-chat-support');
                break;
            case 1:
                $result = __("Enabled", 'wp-live-chat-support');
                break;
            default:
                $result = __("Unknown", 'wp-live-chat-support');
                break;
        }
        return $result;
    }

    public function getPage()
    {
        $trigger_content = maybe_unserialize($this->content);
        if (isset($trigger_content['pages'])) {
            return $trigger_content['pages'] == '' ? __("All", 'wp-live-chat-support') : $trigger_content['pages'];
        } else {
            return '';
        }
    }

    public function getContent()
    {
        $trigger_content = maybe_unserialize($this->content);
        if (isset($trigger_content['html'])) {
            return $trigger_content['html'];
        } else {
            return '';
        }
    }

    public function getSecondsDelay()
    {
        $trigger_content = maybe_unserialize($this->content);
        if (isset($trigger_content['secs']) && is_numeric($trigger_content['secs'])) {
            return intval($trigger_content['secs']);
        } else {
            return 0;
        }
    }

    public function getScrollPercentage()
    {
        $trigger_content = maybe_unserialize($this->content);
        if (isset($trigger_content['perc']) && is_numeric($trigger_content['perc'])) {
            return intval($trigger_content['perc']);
        } else {
            return 0;
        }
    }

    public function getSaveUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-trigger&wplc_action=save_trigger&nonce=" . wp_create_nonce("SaveTrigger") . "&trid=" . $this->id);
    }

    public function getEditUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-trigger&nonce=" . wp_create_nonce('edit_trigger') . "&trid=" . $this->id);
    }

    public function getRemoveUrl()
    {
        return admin_url("admin.php?page=wplivechat-menu-triggers&wplc_action=prompt_remove_trigger&trid=" . $this->id);
    }

    public function getChangeStatusURL()
    {
        return admin_url("admin.php?page=wplivechat-menu-triggers&wplc_action=change_trigger_status&trid=" . $this->id . "&trstatus=" . ($this->status == 1 ? "0" : "1") . "&nonce=" . wp_create_nonce("ChangeTriggerStatus"));
    }

    public static function get_allowed_tags(){
        $tags = wp_kses_allowed_html("post");
        $tags['iframe'] = array(
                'src'    		  => true,
                'width'  		  => true,
                'height' 		  => true,
                'align'  		  => true,
                'class'  		  => true,
                'style'    		  => true,
                'name'   		  => true,
                'id'     		  => true,
                'frameborder' 	  => true,
                'seamless'    	  => true,
                'srcdoc'      	  => true,
                'sandbox'     	  => true,
                'allowfullscreen' => true
            );
        $tags['input'] = array(
                'type'    		  => true,
                'value'  		  => true,
                'placeholder' 	  => true,
                'class'  		  => true,
                'style'    		  => true,
                'name'   		  => true,
                'id'     		  => true,
                'checked' 	      => true,
                'readonly'    	  => true,
                'disabled'        => true,
                'enabled'     	  => true
            );
        $tags['select'] = array(
                'value'    		  => true,
                'class'  		  => true,
                'style'    		  => true,
                'name'   		  => true,
                'id'     		  => true
            );
        $tags['option'] = array(
                'value'    		  => true,
                'class'  		  => true,
                'style'    		  => true,
                'name'   		  => true,
                'id'     		  => true,
                'selected' 	      => true
            );
        return $tags;
    }

}
