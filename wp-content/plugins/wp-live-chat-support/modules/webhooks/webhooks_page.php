<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('wplc_version_migration', 'wplc_webhooks_activation' );
add_action('admin_enqueue_scripts', 'wplc_add_webhooks_page_resources',11);
add_action('admin_menu', 'wplc_admin_webhooks_menu', 5);

function wplc_admin_webhooks_menu(){
    $webhooks_manage_hook = add_submenu_page('wplivechat-menu', __('Manage Web Hook', 'wp-live-chat-support'), __('Manage Web Hook', 'wp-live-chat-support'), 'wplc_cap_admin', 'wplivechat-manage-webhook', 'wplc_admin_manage_webhook');
}

function wplc_add_webhooks_page_resources($hook)
{
    if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-manage-webhook')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-webhooks')
       && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-tools' ))
        {
            return;
        }
    global $wplc_base_file;

	wp_register_style("wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-admin-styles" );
    
    wp_register_script("webhooks", wplc_plugins_url( '/js/webhooks.js', __FILE__ ),array(), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("webhooks" );

    wp_register_script("webhooks_config", wplc_plugins_url( '/js/webhooks_config.js', __FILE__ ),array('webhooks'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("webhooks_config" );

	TCXUtilsHelper::wplc_add_jquery_validation();
}

function wplc_admin_webhooks()
{

    $webhooks_controller = new WebHooksController("Webhooks");
    $webhooks_controller->run();
    
}

function wplc_admin_manage_webhook()
{
    if(isset($_GET['whid']))
    {  
        $manage_webhooks_controller = new ManageWebHookController("manageWebhook",$_GET['whid']);
        $manage_webhooks_controller->run();
    }
    else
    {
        wp_die( __("Page not found","wp-live-chat-support"));
    }
}

function wplc_webhooks_activation()
{
    TCXWebhookHelper::module_db_integration();
} 


?>