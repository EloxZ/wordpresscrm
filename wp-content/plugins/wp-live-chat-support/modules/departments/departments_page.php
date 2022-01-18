<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('wplc_version_migration', 'wplc_departments_activation' );
add_action('admin_enqueue_scripts', 'wplc_add_departments_page_resources',11);
add_action('admin_menu', 'wplc_admin_departments_menu', 5);

function wplc_admin_departments_menu(){
    $departments_manage_hook = add_submenu_page('wplivechat-menu', __('Manage Web Hook', 'wp-live-chat-support'), __('Manage Web Hook', 'wp-live-chat-support'), 'wplc_cap_admin', 'wplivechat-manage-department', 'wplc_admin_manage_department');
}

function wplc_add_departments_page_resources($hook)
{
    if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-departments')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-manage-department')
        && $hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-tools' ))
        {
            return;
        }
    global $wplc_base_file;

	wp_register_style("wplc-admin-styles", wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-admin-styles" );

    wp_register_script("departments", wplc_plugins_url( '/js/departments.js', __FILE__ ),array(), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("departments" );

    wp_register_script("departments_config", wplc_plugins_url( '/js/departments_config.js', __FILE__ ),array('departments'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("departments_config" );

	TCXUtilsHelper::wplc_add_jquery_validation();
}

function wplc_admin_departments()
{

    $departments_controller = new DepartmentsController("Departments");
    $departments_controller->run();
    
}

function wplc_admin_manage_department()
{
    if(isset($_GET['depid']))
    {  
        $manage_departments_controller = new ManageDepartmentController("manageDepartment",$_GET['depid']);
        $manage_departments_controller->run();
    }
    else
    {
        wp_die( __("Page not found","wp-live-chat-support"));
    }
}

function wplc_departments_activation()
{
    TCXDepartmentsData::module_db_integration();
} 


?>