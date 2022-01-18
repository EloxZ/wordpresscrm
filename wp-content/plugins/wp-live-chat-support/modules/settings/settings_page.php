<?php

if (!defined('ABSPATH')) {
    exit;
}


add_action('admin_menu', 'wplc_admin_settings_menu', 5);
add_action('admin_enqueue_scripts', 'wplc_add_settings_page_resources',11);
add_action('admin_notices', 'wplc_chat_server_warning');

function wplc_admin_settings_menu(){
    $settings_hook = wplc_add_ordered_submenu_page('wplivechat-menu', __('Settings', 'wp-live-chat-support'), __('Settings', 'wp-live-chat-support'), 'wplc_cap_admin', 'wplivechat-menu-settings', 'wplc_admin_settings_page',20);
}

function wplc_add_settings_page_resources($hook)
{
    if($hook != TCXUtilsHelper::wplc_get_page_hook('wplivechat-menu-settings' ))
        {
            return;
        }
    $wplc_settings = TCXSettings::getSettings();
    global $wplc_base_file;

    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tooltip');
    wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-dialog');

    wp_register_style( 'wplc-jquery-ui', wplc_plugins_url('/css/vendor/jquery-ui/jquery-ui.css', $wplc_base_file), array(), WPLC_PLUGIN_VERSION);
    wp_enqueue_style( 'wplc-jquery-ui' );

	wp_register_style( 'wplc-jquery-multiselect', wplc_plugins_url('/js/vendor/lou-multiselect/css/multi-select.dist.css', $wplc_base_file), array(), WPLC_PLUGIN_VERSION);
	wp_enqueue_style( 'wplc-jquery-multiselect' );

	wp_register_style('wplc-tabs', wplc_plugins_url('/css/wplc_tabs.css', $wplc_base_file), array('wplc-jquery-ui'), WPLC_PLUGIN_VERSION);
	wp_enqueue_style('wplc-tabs');

    wp_register_style( 'wplc-admin-styles', wplc_plugins_url( '/css/admin_styles.css', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION);
    wp_enqueue_style( 'wplc-admin-styles' );

    wp_register_style( 'wplc-settings-style', wplc_plugins_url('/settings_style.css', __FILE__), array(), WPLC_PLUGIN_VERSION);
    wp_enqueue_style( 'wplc-settings-style' );

    wp_register_style( 'wplc-fontawesome-iconpicker', wplc_plugins_url( '/css/fontawesome-iconpicker.min.css', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION);
    wp_enqueue_style( 'wplc-fontawesome-iconpicker' );

	wp_register_style("wplc-component-theme-picker-style", wplc_plugins_url( '/components/theme_picker/theme_picker.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-component-theme-picker-style" );

	wp_register_style("wplc-gutenberg-style", wplc_plugins_url( '/includes/blocks/wplc-chat-box/wplc_gutenberg_trigger_styles.css', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style("wplc-gutenberg-style" );

	wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'tcx-fa' );

	wp_register_script( 'wplc-md5', wplc_plugins_url( '/js/vendor/md5/md5.min.js', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'wplc-md5' );

    wp_register_script("wplc-settings", wplc_plugins_url( '/js/settings.js', __FILE__ ),array(), WPLC_PLUGIN_VERSION,true );
	wp_enqueue_script("wplc-settings" );

	wp_register_script("wplc-component-theme-picker", wplc_plugins_url( '/components/theme_picker/js/theme_picker.js', $wplc_base_file ),array(), WPLC_PLUGIN_VERSION,true );
	wp_enqueue_script("wplc-component-theme-picker" );

	$script_data          = array(
		'edit_user_url'         => admin_url( 'user-edit.php' ),
		'pagesHeader' => __( "All pages", 'wp-live-chat-support' ),
		'pagesWithChatHeader' => __( "Pages with chat", 'wp-live-chat-support' ),
		'pagesWithoutChatHeader' => __( "Pages without chat", 'wp-live-chat-support' ),
		'postsHeader' => __( "All post types", 'wp-live-chat-support' ),
		'postsWithChatHeader' => __( "Post types with chat", 'wp-live-chat-support' ),
		'imagesUrl' => wplc_plugins_url( '/images', $wplc_base_file )

	);
	wp_localize_script( 'wplc-settings', 'settings_localization_data', $script_data );


	wp_register_script("wplc-settings_config", wplc_plugins_url( '/js/settings_config.js', __FILE__ ),array('wplc-settings'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("wplc-settings_config" );

    wp_register_script( 'wplc-ace', wplc_plugins_url( '/js/vendor/ace/ace.js', $wplc_base_file ), array('jquery'), WPLC_PLUGIN_VERSION,true);
    wp_enqueue_script( "wplc-ace");

    wp_register_script( 'wplc-admin-js-settings', wplc_plugins_url('/js/wplc_u_admin_settings.js', __FILE__), array(), WPLC_PLUGIN_VERSION,true);
    wp_enqueue_script('wplc-admin-js-settings');
	wp_localize_script('wplc-admin-js-settings', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

    wp_register_script("wplc-gutenberg", wplc_plugins_url( '/js/wplc_gutenberg.js', __FILE__ ),array('wplc-ace'), WPLC_PLUGIN_VERSION,true );
    wp_enqueue_script("wplc-gutenberg" );

    $gutenberg_data = array( "default_html"=> '<!-- Default HTML -->
<div class="wplc_block">
    <span class="wplc_block_logo">{wplc_logo}</span>
    <span class="wplc_block_text">{wplc_text}</span>
    <span class="wplc_block_icon">{wplc_icon}</span>
</div>');
    wp_localize_script( 'wplc-gutenberg', 'gutenbergData', $gutenberg_data );

    wp_register_script('wplc-fontawesome-iconpicker', wplc_plugins_url('/js/fontawesome-iconpicker.js', $wplc_base_file), array('jquery'), WPLC_PLUGIN_VERSION, true);
    wp_enqueue_script('wplc-fontawesome-iconpicker');

	wp_register_script('wplc-multiselect-js', wplc_plugins_url('/js/vendor/lou-multiselect/js/jquery.multi-select.js', $wplc_base_file), array('jquery'), WPLC_PLUGIN_VERSION, true);
	wp_enqueue_script('wplc-multiselect-js');

	TCXUtilsHelper::wplc_add_jquery_validation();
    wp_enqueue_media();

    wp_register_script('wplc-upload', wplc_plugins_url('/js/settings_uploads.js', __FILE__), array('jquery','wplc-settings'), WPLC_PLUGIN_VERSION, true);
    wp_enqueue_script('wplc-upload');


	wp_register_style( "wplc-bootstrap", admin_url( '/admin.php?wplc_action=loadbootstrap', $wplc_base_file ), array(), WPLC_PLUGIN_VERSION );
	wp_enqueue_style( "wplc-bootstrap" );


}


function wplc_admin_settings_page()
{
    $support_settings = new SettingsController("settings");
    $support_settings->run();
}


function wplc_chat_server_warning()
{
	if (isset($_GET['page'])) {
		if ($_GET['page'] === 'wplivechat-menu-settings') {
				$chat_server_error = get_option('WPLC_NO_SERVER_MATCH', false);
				if ($chat_server_error === true || $chat_server_error === 'true') {
					$output = "<div class='update-nag' style='margin-bottom: 5px; border-color:#dd0000'>";
					$output .=     "<strong>" . __("Warning - 3CX Chat Server disabled", 'wp-live-chat-support') . "</strong><br>";
					$output .=     "<p>" . sprintf(__('Your WP-Live Chat installed version ( %s ) is incompatible with current running chat servers please upgrade your plugin or contact support.', 'wp-live-chat-support'),WPLC_PLUGIN_VERSION) . "</a></p>";
					$output .= "</div>";
					echo $output;
				}
		}
	}
}

?>