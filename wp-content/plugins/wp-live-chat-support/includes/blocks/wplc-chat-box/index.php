<?php
/**
 * BLOCK: WP Live Chat Support Chat box
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'enqueue_block_editor_assets', 'wplc_chat_box_block_editor_assets' );

function wplc_chat_box_block_editor_assets() {
	global $wplc_base_file;
	$wplc_settings = TCXSettings::getSettings();
	if ( $wplc_settings->wplc_gutenberg_settings ) {
		// Scripts
		wp_enqueue_script(
			'wplc_chat_box',
			wplc_plugins_url( 'block.js', __FILE__ ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
		);

		$settings['wplc_typing']  = __( "Type here", 'wp-live-chat-support' );
		$settings['wplc_enabled'] = $wplc_settings->wplc_gutenberg_settings['enable'];
		$settings['wplc_size']    = $wplc_settings->wplc_gutenberg_settings['size'];
		$settings['wplc_logo']    = $wplc_settings->wplc_gutenberg_settings['logo'];
		$settings['wplc_text']    = $wplc_settings->wplc_gutenberg_settings['text'];
		$settings['wplc_icon']    = $wplc_settings->wplc_gutenberg_settings['icon'];
		$settings['wplc_icon_enabled'] = $wplc_settings->wplc_gutenberg_settings['enable_icon'];;
		$settings['wplc_custom_html'] = $wplc_settings->wplc_gutenberg_settings['custom_html'];;

		wp_localize_script( 'wplc_chat_box', 'wplc_settings', $settings );
		wp_localize_script('wplc_chat_box','wplc_baseurl',array("url"=>WPLC_PLUGIN_URL));
		// Styles
		wp_enqueue_style(
			'wplc_gutenberg_trigger',
			wplc_plugins_url( 'wplc_gutenberg_trigger_styles.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'wplc_gutenberg_trigger_styles.css' )
		);
		/*	wp_register_script( 'font-awesome-js-svg', wplc_plugins_url( '/js/vendor/font-awesome/all.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
	wp_enqueue_script( 'font-awesome-js-svg' );*/

		wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'wplc_chat_box','jquery' ), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'tcx-fa' );
	}
}




add_action( 'enqueue_block_assets', 'wplc_chat_box_block_assets' );

function wplc_chat_box_block_assets() {
	global $wplc_base_file;
	$wplc_settings = TCXSettings::getSettings();
	if ( $wplc_settings->wplc_gutenberg_settings['enable'] && TCXUtilsHelper::wplc_show_chat_client()) {
		// Styles for front-end
		wp_enqueue_style(
			'wplc_chat_box-front-end-template', wplc_plugins_url( '/wplc_gutenberg_trigger_styles.css', __FILE__ ), array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'wplc_gutenberg_trigger_styles.css' )
		);

		wp_register_script( 'tcx-fa', wplc_plugins_url( '/js/tcx-fa.min.js', $wplc_base_file ), array( 'jquery' ), WPLC_PLUGIN_VERSION, true );
		wp_enqueue_script( 'tcx-fa' );
	}
}

add_filter( 'render_block', 'wplc_render_gutenberg_chat_trigger' , 10, 2 );

function wplc_render_gutenberg_chat_trigger( $block_content, $block ) {
	if ( 'wp-live-chat-support/wplc-chat-box' === $block['blockName'] ) {
		if(!TCXUtilsHelper::wplc_show_chat_client()) {
			$block_content = '';
		}
	}
	return $block_content;
}
