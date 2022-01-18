<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('wplc_version_migration', 'wplc_quick_responses_activation' );
add_action('admin_enqueue_scripts', 'wplc_add_quick_responses_page_resources',11);
add_action('admin_menu', 'wplc_admin_quick_responses_menu', 5);


function wplc_admin_quick_responses_menu(){
    $quick_responses_manage_hook = add_submenu_page('wplivechat-menu', __('Manage Quick Response', 'wp-live-chat-support'), __('Manage Quick Response', 'wp-live-chat-support'), 'wplc_cap_admin', 'wplivechat-manage-quick-response', 'wplc_admin_manage_quick_response');
}

function wplc_add_quick_responses_page_resources($hook)
{
    if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-manage-quick-response')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-quick-responses')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-tools' ))
        {
            return;
        }
    global $wplc_base_file;

	wp_register_style("wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-admin-styles" );
    
    wp_register_script("quick_responses", wplc_plugins_url( '/js/quick_responses.js', __FILE__ ),array(), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("quick_responses" );

    wp_register_script("quick_responses_config", wplc_plugins_url( '/js/quick_responses_config.js', __FILE__ ),array('quick_responses'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("quick_responses_config" );

	TCXUtilsHelper::wplc_add_jquery_validation();
}

function wplc_admin_quick_responses()
{

    $quick_responses_controller = new QuickResponsesController("Quick Responses");
    $quick_responses_controller->run();
    
}

function wplc_admin_manage_quick_response()
{
    if(isset($_GET['qrid']))
    {  
        $manage_quick_responses_controller = new ManageQuickResponseController("manageQuickResponse",$_GET['qrid']);
        $manage_quick_responses_controller->run();
    }
    else
    {
        wp_die( __("Page not found","wp-live-chat-support"));
    }
}

function wplc_quick_responses_activation()
{
    TCXQuickResponseHelper::module_db_integration();
} 


?>