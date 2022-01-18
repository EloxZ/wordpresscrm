<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wplc_version_migration', 'wplc_custom_fields_activation' );
add_action('admin_enqueue_scripts', 'wplc_add_custom_fields_page_resources',11);
add_action('admin_menu', 'wplc_admin_custom_fields_menu', 5);

function wplc_admin_custom_fields_menu(){
    $customfields_manage_hook = add_submenu_page('wplivechat-menu', __('Manage Custom Field', 'wp-live-chat-support'), __('Manage Custom Field', 'wp-live-chat-support'), 'wplc_cap_admin', 'wplivechat-manage-custom-field', 'wplc_admin_manage_custom_field');
}

function wplc_add_custom_fields_page_resources($hook)
{

    if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-manage-custom-field')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-custom-fields')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-tools' ))
        {
            return;
        }
    global $wplc_base_file;

	wp_register_style("wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-admin-styles" );
    
    wp_register_script("custom_fields", wplc_plugins_url( '/js/custom_fields.js', __FILE__ ),array(), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("custom_fields" );

    wp_register_script("custom_fields_config", wplc_plugins_url( '/js/custom_fields_config.js', __FILE__ ),array('custom_fields'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("custom_fields_config" );

	TCXUtilsHelper::wplc_add_jquery_validation();
}

function wplc_admin_custom_fields()
{

    $custom_fields_controller = new CustomFieldsController("customFields");
    $custom_fields_controller->run();
    
}

function wplc_admin_manage_custom_field()
{
    if(isset($_GET['cfid']))
    {  
        $manage_custom_fields_controller = new ManageCustomFieldController("manageCustomField",$_GET['cfid']);
        $manage_custom_fields_controller->run();
    }
    else
    {
        wp_die( __("Page not found","wp-live-chat-support"));
    }
}

function wplc_custom_fields_activation()
{
	TCXCustomFieldHelper::module_db_integration();
} 


?>