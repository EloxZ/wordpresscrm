<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action( 'admin_menu', 'wplc_admin_menu', 4 );
add_action( 'admin_menu', 'wplc_support_menu',5 );
add_action( 'admin_menu', 'wplc_order_menu_items' );
add_action( 'admin_menu', 'wplc_setup_hidden_menu_pages', 15 );
add_action( 'admin_bar_menu', 'wplc_maa_online_agents', 100 );

function wplc_admin_menu()
{
	//adding submenu with same menu_slug in order to be are able to manage the position of the submenu item.
	add_menu_page('3CX Live Chat', __('Live Chat', 'wp-live-chat-support'), 'wplc_ma_agent', 'wplivechat-menu', 'wplc_admin_agent_chat', 'dashicons-format-chat');
	$wplc_settings = TCXSettings::getSettings();
	$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
	if($channel!=='phone') {
		wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Live Chat', 'wp-live-chat-support' ), __( 'Live Chat', 'wp-live-chat-support' ), 'wplc_ma_agent', 'wplivechat-menu', 'wplc_admin_agent_chat', - 10 );
	}
}

function wplc_support_menu() {
	wplc_add_ordered_submenu_page( 'wplivechat-menu', __( 'Support', 'wp-live-chat-support' ), __( 'Support', 'wp-live-chat-support' ), 'wplc_ma_agent', 'wplivechat-menu-support-page', null, 100 );
	global $submenu;
	if( array_key_exists('wplivechat-menu',$submenu) && is_array($submenu['wplivechat-menu'])) {
        foreach ($submenu['wplivechat-menu'] as $index => $item) {
            if (is_array($item) && count($item) > 2 && $item[0] == __('Support', 'wp-live-chat-support')) {
                $submenu['wplivechat-menu'][ $index ][2] = "https://wordpress.org/plugins/wp-live-chat-support/#installation";
                break;
            }
        }
    }
}

function wplc_setup_hidden_menu_pages(){
	add_filter( 'submenu_file', function($submenu_file){
		$screen = get_current_screen();
		switch($screen->id){
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-manage-custom-field"):
				$submenu_file = "wplivechat-menu-custom-fields";
				break;
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-manage-webhook"):
				$submenu_file = "wplivechat-menu-webhooks";
				break;
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-manage-department"):
				$submenu_file = "wplivechat-menu-departments";
				break;
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-manage-trigger"):
				$submenu_file = "wplivechat-menu-triggers";
				break;
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-manage-quick-response"):
				$submenu_file = "wplivechat-menu-quick-responses";
				break;
			case TCXUtilsHelper::wplc_get_page_hook("wplivechat-session-details"):
				$submenu_file = "wplivechat-menu-history";
				break;
			default:
				break;
		}
		return $submenu_file;
	});

	add_action('admin_head', function () {
		remove_submenu_page('wplivechat-menu','wplivechat-manage-custom-field');
		remove_submenu_page('wplivechat-menu','wplivechat-manage-webhook');
		remove_submenu_page('wplivechat-menu','wplivechat-manage-department');
		remove_submenu_page('wplivechat-menu','wplivechat-manage-trigger');
		remove_submenu_page('wplivechat-menu','wplivechat-chatbox');
		remove_submenu_page('wplivechat-menu','wplivechat-session-details');
		remove_submenu_page('wplivechat-menu','wplivechat-manage-quick-response');
		remove_submenu_page('wplivechat-menu','wplivechat-error');

		$wplc_settings = TCXSettings::getSettings();
		$channel = TCXUtilsHelper::wplc_check_channel_change_on_save($wplc_settings->wplc_channel);
		if($channel==='phone') {
			//this menu item will be created automatically by wordpress when we are adding the main menu,
			// so we need to remove it from submenu even if we are not adding it.
			remove_submenu_page('wplivechat-menu','wplivechat-menu');
		}

		if ( get_option( "WPLC_SETUP_WIZARD_RUN", 'NOTEXIST' ) === "1" ) {
			remove_submenu_page( 'wplivechat-menu', 'wplc-getting-started' );
		}else
		{
			global $submenu;
			foreach($submenu['wplivechat-menu'] as $wplc_menu)
			{
				if($wplc_menu[2] !== 'wplc-getting-started') {
					remove_submenu_page( 'wplivechat-menu', $wplc_menu[2] );
				}
			}
		}


	});
}

function wplc_order_menu_items()
{
    global $menu;
    global $submenu;
    global $menu_order;

    if(is_array($menu_order)) {
	    foreach ( $submenu as $menu_alias => $value ) {
		    if ( key_exists( $menu_alias, $menu_order ) ) {
			    usort( $value, function ( $menu_item_a, $menu_item_b ) use ( $menu_alias, $menu_order ) {
				    if ( key_exists( $menu_item_a[2], $menu_order[ $menu_alias ] ) ) {
					    if ( key_exists( $menu_item_b[2], $menu_order[ $menu_alias ] ) ) {
						    return $menu_order[ $menu_alias ][ $menu_item_a[2] ] - $menu_order[ $menu_alias ][ $menu_item_b[2] ];

					    } else {
						    return $menu_order[ $menu_alias ][ $menu_item_a[2] ] - 10000000;
					    }
				    } else if ( key_exists( $menu_item_b[2], $menu_order[ $menu_alias ] ) ) {
					    return 10000000 - $menu_order[ $menu_alias ][ $menu_item_b[2] ];
				    } else {
					    return 0;
				    }
			    } );
			    $submenu[ $menu_alias ] = $value;
		    }
	    }
    }
}

function wplc_add_ordered_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, callable $function = null, $position = null)
{
    //The add_submenu_page wordpress's function is a little bit strange on the way of sorting items. It expects items position to be in a row and smaller than the count of menu items.
    //So we can't let empty positions in order to be flexy on the case of a future expansion of menu!!
    global $menu_order;
    $hook = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, ($function == null ? '' : $function), $position);

    if (!is_array($menu_order)) {
        $menu_order = array();
    }

    if (!key_exists($parent_slug, $menu_order) || !is_array($menu_order[$parent_slug])) {
        $menu_order[$parent_slug] = array();
    }

    $menu_order[$parent_slug][$menu_slug] = $position;

    return $hook;
}

function wplc_maa_online_agents() {
	if (!current_user_can('wplc_ma_agent', array(null))) {
		return; //if user doesn't have permissions for chat agent, do not show admin bar.
	}
	$wplc_settings = TCXSettings::getSettings();
	$user_array = TCXAgentsHelper::get_online_agent_users();
	$agent_count = count($user_array);
	if ($agent_count>0) {
		$circle_class = "wplc_green_circle";
		if ($agent_count==1) {
			$chat_agents = __('Chat Agent Online', 'wp-live-chat-support');
		} else {
			$chat_agents = __('Chat Agents Online', 'wp-live-chat-support');
		}
	} else {
		$circle_class = "wplc_red_circle";
		$chat_agents = __('Chat Agents Online', 'wp-live-chat-support');
	}

	global $wp_admin_bar;
	if (is_admin() && $wplc_settings->wplc_channel !== 'phone') {
		$wp_admin_bar->add_menu( array(
			'id'    => 'wplc_ma_online_agents',
			'title' => '<span class="wplc_circle ' . $circle_class . '" id="wplc_ma_online_agents_circle"></span><span id="wplc_ma_online_agents_count">' . $agent_count . '</span> <span id="wplc_ma_online_agents_label">' . $chat_agents . '</span>',
			'href'  => false
		) );
	}

	foreach($user_array as $user) {
		$wp_admin_bar->add_menu(array(
			'id' => 'wplc_user_online_'.$user->ID,
			'parent' => 'wplc_ma_online_agents',
			'title' => $user->display_name,
			'href' => false,
		));
	}

	if (is_admin() && $wplc_settings->wplc_channel !== 'phone') {
		$wp_admin_bar->add_node( array(
			'id' => 'wplc_ma_online_switch',
			'meta' => array('class' => 'wplc_online_switch_'.(TCXAgentsHelper::is_agent_accepting() ? 'online' : 'offline')),
			'title' => '<input  '.(!$wplc_settings->wplc_allow_agents_set_status ?"disabled":"").'  type="checkbox" id="wplc_online_topbar_switch" '.(TCXAgentsHelper::is_agent_accepting() ? 'checked="checked"' : '') . ' class="wplc_check wplc_online_topbar_switch wplc_online_topbar_switch_' . (TCXAgentsHelper::is_agent_accepting() ? 'online' : 'offline') . '"  />
      <span id="wplc_ma_online_agent_text">'.(TCXAgentsHelper::is_agent_accepting() ? __('Online', 'wp-live-chat-support') : __('Offline','wp-live-chat-support')) . '
      </span>',
			'href' => false
		));
	}
}
