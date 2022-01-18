<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'wplc_add_user_settings_page_resources',11);

add_action( 'edit_user_profile', 'wplc_admin_user_settings' );
add_action( 'show_user_profile', 'wplc_admin_user_settings' );

add_action('edit_user_profile_update', 'wplc_save_user_settings');
add_action('personal_options_update', 'wplc_save_user_settings');


function wplc_add_user_settings_page_resources($hook)
{
    if($hook != 'profile.php')
        {
            return;
        }
    global $wplc_base_file;

}

function wplc_admin_user_settings($user)
{
	if(current_user_can( 'wplc_cap_admin' )) {
		$user_settings_controller = new UserSettingsController( "UserSettings", $user->ID );
		$user_settings_controller->run();
	}
}

function wplc_save_user_settings($userID)
{
	if(current_user_can( 'wplc_cap_admin' )) {
		$user_settings_controller = new UserSettingsController( "UserSettingsUpdate", $userID );
		$user_settings_controller->save_user_settings();
	}
}


?>