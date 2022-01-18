<?php
if (!defined('ABSPATH')) {
    exit;
}

class DataToolsController extends BaseController
{

    private $pager;

    public function __construct($alias)
    {
        parent::__construct(__("Data Management", 'wp-live-chat-support'), $alias);
        $this->init_actions();
        $this->parse_action($this->available_actions);

    }

    public function view($return_html = false, $add_wrapper=true)
    {
        $this->view_data["wplc_tools_nonce"] = wp_create_nonce('toolsNonce');
        $this->view_data["import_nonce"] = wp_create_nonce('importSettingsNonce');
	    return $this->load_view(plugin_dir_path(__FILE__) . "data_tools_view.php",$return_html,$add_wrapper);

    }

    public function import_settings($data)
    {
        if (isset($data['Files']['wplc_at_import_file']['tmp_name'])) {
            try {
                $row = 1;
                $file_ref = realpath($data['Files']['wplc_at_import_file']['tmp_name']);
                $handle = fopen($file_ref, "r");

                $import_key_check = array(
                    "WPLC_JSON_SETTINGS"=>"JSON",
                    "WPLC_SETTINGS"=>"OBJECT",
                    "WPLC_GA_SETTINGS"=>"OBJECT",
                    "WPLC_BANNED_IP_ADDRESSES"=>"OBJECT",
                    "wplc_advanced_settings"=>"OBJECT",
                    "WPLC_POWERED_BY"=>"OBJECT",
                    "WPLC_DOC_SUGG_SETTINGS"=>"OBJECT",
                    "WPLC_ACBC_SETTINGS"=>"OBJECT",
                    "WPLC_INEX_SETTINGS"=>"OBJECT",
                    "WPLC_AUTO_RESPONDER_SETTINGS"=>"OBJECT",
                    "WPLC_ET_SETTINGS"=>"OBJECT",
                    "WPLC_SN_SETTINGS"=>"OBJECT",
                    "WPLC_ZENDESK_SETTINGS"=>"OBJECT",
                    "WPLC_CCTT_SETTINGS"=>"OBJECT",
                );

                if ($handle !== false) {
                    $values_to_update = array();
                    while (($csv_settings = fgetcsv($handle, 0, ",")) !== false) {
                        $num = count($csv_settings);
                        if ($num == 2 && $row > 1) { 
                            
                            $key = $csv_settings[0];
                            $value = self::handle_import_data($csv_settings[1]);
                            if (array_key_exists($key, $import_key_check)) {
                                if($value!==null)
                                {
                                    $values_to_update[$key] =  $value;
                                }
                                else
                                {
                                    throw new Exception($key.": Unable to deserialize."); 
                                }
                            }
                        }
                        $row++;
                    }

                    foreach( $values_to_update as $key=>$value)
                    {
                        //import file exported from old version! 
                        $key = $key=="WPLC_SETTINGS"? "WPLC_JSON_SETTINGS" :$key;

                        if($key==="WPLC_JSON_SETTINGS" && is_object($value))
                        {
	                        $settings_types = TCXSettings::getSettingsTypes();
                        	foreach($value as $setting_key=>$setting_value)
	                        {
			                        $setting_value = TCXUtilsHelper::get_setting_value_sanitized( $setting_value, $setting_key, $settings_types );
			                        if ( $setting_value !== null ) {
				                        $value->$setting_key = $setting_value;
			                        }
	                        }
                        }

                        update_option( $key,$import_key_check[$key]=="JSON" ? TCXUtilsHelper::wplc_json_encode($value,JSON_UNESCAPED_UNICODE):$value );
                    }                    

                    fclose($handle);
                    wplc_activate(null);
                }
            } catch (Exception $e) {
                if($handle)
                {
                    fclose($handle);
                }
                $error = new TCXError();
                $error->ErrorFound = true;
                $error->ErrorHandleType = "Show";
                $error->ErrorData->message = __("Import Failed - Could Not Process File",'wp-live-chat-support')." [ ".$e->getMessage()." ]";
                $this->view_data["error"] = $error;
            }
        } else {
            $error = new TCXError();
            $error->ErrorFound = true;
            $error->ErrorHandleType = "Show";
            $error->ErrorData->message = __("Import Failed - Could Not Process File", 'wp-live-chat-support');
            $this->view_data["error"] = $error;
        }
    }

    private function init_actions()
    {
        $this->available_actions = [];
        $this->available_actions[] = new TCXPageAction("prompt_import_settings");

        $saveParams = [];
        $saveParams[] = array('Data' => isset($_POST) && !empty($_POST) ? $_POST : null,
            'Files' => isset($_FILES) && !empty($_FILES) ? $_FILES : null,
        );

        $this->available_actions[] = new TCXPageAction("execute_import_settings", 9, "importSettingsNonce", 'import_settings', $saveParams);
    }

    private static function handle_import_data($data){
	    $result = null;
        if(!TCXUtilsHelper::try_base64_decode($data,$serialized_data))
        {
            $result = maybe_unserialize( $data );
        }
        else
        {
            if(!TCXUtilsHelper::try_json_decode($serialized_data,$result))
            {
                $result = maybe_unserialize( $serialized_data );
            }
        }
        return $result;
    }

}
