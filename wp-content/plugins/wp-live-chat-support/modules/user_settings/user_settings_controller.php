<?php
if (!defined('ABSPATH')) {
    exit;
}

class UserSettingsController extends BaseController
{

    private $userId;
    
    public function __construct($alias,$userId=-1)
    {
        parent::__construct( __("User Settings", 'wp-live-chat-support'),$alias);
        $this->userId = $userId;
    }

    //Actions
    public function view($return_html = false, $add_wrapper=true)
    {
		$this->view_data["departments"] =  $this->load_departments();
	    $this->view_data["userTag"] = $this->load_user_tagline();
	    $this->view_data["selected_department"] = intval(get_user_meta($this->userId, "wplc_user_department", true));
	    $this->view_data["is_user_agent"] = sanitize_text_field( get_the_author_meta( 'wplc_ma_agent', $this->userId ) ) == "1";

	    return $this->load_view(plugin_dir_path(__FILE__) . "user_settings_view.php",$return_html,$add_wrapper);
    }

    public function save_user_settings()
    {
	    if (isset($_POST['wplc_ma_agent']) && $_POST['wplc_ma_agent'] =='1') {
		    TCXAgentsHelper::set_user_as_agent($this->userId );
		    if( isset( $_POST['wplc_user_tagline'] ) ){
			    TCXAgentsHelper::set_agent_tagline($this->userId );
		    }
		    if(isset($_POST['wplc_user_department'])){
			    TCXAgentsHelper::set_agent_department($this->userId);
		    }
	    } else {
		    TCXAgentsHelper::revoke_agent_from_user($this->userId );
	    }
    }


    private function load_user_tagline(){
	    $result =  "";
	    if ( get_the_author_meta( 'wplc_user_tagline',  $this->userId ) != "" ) {
		    $result = sanitize_text_field( get_the_author_meta( 'wplc_user_tagline',  $this->userId ) );
	    }
	    return $result;
    }

    private function load_departments(){
    	global $wpdb;
	    $result  = TCXDepartmentsData::get_departments($wpdb);
	    if($result == null)
	    {
	    	$result = array();
	    }
	    return $result;
    }

}
?>