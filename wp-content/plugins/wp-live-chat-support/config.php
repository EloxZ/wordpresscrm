<?php
/*
 * Define important constants
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('WPLC_MIN_WP_VERSION', "5.3");
define('WPLC_MIN_PHP_VERSION', "5.4");
define('WPLC_PLUGIN_VERSION', "9.4.1");
define('WPLC_PLUGIN_DIR', dirname(__FILE__));
define('WPLC_PLUGIN_URL', wplc_plugins_url( '/', __FILE__ ) );
define('WPLC_PLUGIN', plugin_basename( __FILE__ ) );
define('WPLC_ACTIVATION_SERVER', 'https://wplc.3cx.net' );
define('WPLC_CHAT_SERVER','https://wplc.3cx.net/api/chatrouter');
define('WPLC_ENABLE_CHANNELS',  "mcu,phone" );
