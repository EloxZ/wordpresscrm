<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wplc_version_migration', 'wplc_chat_activation' );
add_action('wp_enqueue_scripts', 'wplc_add_chat_client_page_resources' );
add_action('wp_footer', 'wplc_chat_client_page');
add_filter('script_loader_tag', 'wplc_defer_callus_js', 10, 2);

function wplc_add_chat_client_page_resources($hook)
{
    if(is_admin())
    {
        return;
    }
    global $wplc_base_file;
	$wplc_settings = TCXSettings::getSettings();
	if ( ! class_exists( 'Mobile_Detect' ) ) {
		require_once( WPLC_PLUGIN_DIR . '/includes/Mobile_Detect.php' );
	}
	$wplc_detect_device = new Mobile_Detect;
	$is_mobile          = $wplc_detect_device->isMobile() ? true : false;


	if(TCXUtilsHelper::wplc_show_chat_client() ) {
		if(!$is_mobile) {
			wp_register_script( "wplc-chat_app", wplc_plugins_url( '/js/callus.js', __FILE__ ), array(), WPLC_PLUGIN_VERSION, true );
			wp_enqueue_script( 'wplc-chat_app' );
		}else{
			wp_register_script( "wplc-chat_loader", wplc_plugins_url( '/js/callus_loader.js', __FILE__ ), array(), WPLC_PLUGIN_VERSION, true );
			wp_enqueue_script( 'wplc-chat_loader' );

			$callusLoader_data = array( "source" => wplc_plugins_url( '/js/callus.js', __FILE__ ) );
			wp_localize_script( 'wplc-chat_loader', 'callusLoader_data', $callusLoader_data );

		}
	}
}

function wplc_chat_client_page()
{
    if(!TCXUtilsHelper::wplc_show_chat_client())
    {
        return;
    }
    $support_chat_client = new ChatClientController("chat_client");
    $support_chat_client->run();
}

function wplc_chat_activation()
{
	TCXChatHelper::module_db_integration();
	TCXChatRatingHelper::module_db_integration();
}

function wplc_defer_callus_js( $url ) {
	if ( strpos( $url, 'callus.js' ) ) {
		return str_replace( ' src', ' defer src', $url );
	}else{
		return $url;
	}
}
