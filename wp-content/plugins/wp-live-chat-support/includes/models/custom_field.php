<?php
class TCXCustomField
{
    public function __construct()
    {}

    public $id;
    public $name;
    public $type;
    public $status;   
    private $content;

    public function setPlainContent($plain_content){
        $this->content = $plain_content;
    }

    public function getStatusName(){
        if( $this->status ){  			
           return __("Active", 'wp-live-chat-support');  		
        } else {
            return __("Inactive", 'wp-live-chat-support');
        }
    }

    public function getTypeName(){

        $this->type = intval( $this->type );
    
        switch( $this->type ){
            case 0:
                $result = __("Text Field", 'wp-live-chat-support');
                break;
            case 1:
                $result = __("Dropdown", 'wp-live-chat-support');
                break;
            default:
            $result = __("Unknown", 'wp-live-chat-support');
                break;
        }
        return $result;
    }

    public function getViewContent($escape="esc_html"){
        if( intval($this->type) === 1 ) {
            $field_content="";
            $delimiter = ($escape==="esc_html")?'<br/>':"\n";
            $n_field_content = json_decode($this->content);
            if(is_array($n_field_content)) {
                foreach($n_field_content as $line) {
                    $field_content.= stripslashes($escape($line));
                    if($line !== end($n_field_content)) {
                        $field_content.= $delimiter;
                    }
                }
            }
        }else {
            $field_content = esc_html($this->content);
        }
        return $field_content;
    }
    
    public function encodeContent($itemDelimiter="\n"){
        if( $this->content != "" && $this->type == 1){

			$content_values = explode( $itemDelimiter, $this->content );
            $contents_encoded = array();
			if($content_values){
				foreach ($content_values as $key => $value) {
					if( strlen(trim($value))>0) {
						array_push($contents_encoded, stripslashes( sanitize_text_field( $value ) ));
					}
				}
			}

			$content_encoded = json_encode( $contents_encoded );

		} else {

			$content_encoded = stripslashes(sanitize_text_field( $this->content ));
			
		}
        return $content_encoded;
    }
        
    public function getSaveUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-custom-field&wplc_action=save_custom_field&nonce=".wp_create_nonce("save_custom_field")."&cfid=".$this->id);
    }
    
    public function getEditUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-custom-field&nonce=".wp_create_nonce('edit_custom_field')."&cfid=".$this->id);
    }

    public function getRemoveUrl()
    {
	    return admin_url("admin.php?page=wplivechat-menu-tools&wplc_action=prompt_remove_custom_field&cfid=".$this->id."#wplc_custom_fields_tab");

    }

}

?>