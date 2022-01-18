<?php

require_once( WPLC_PLUGIN_DIR . "/functions.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/gdpr.php" );

require_once( WPLC_PLUGIN_DIR . "/ajax/user.php" );
require_once( WPLC_PLUGIN_DIR . "/ajax/agent.php" );
require_once( WPLC_PLUGIN_DIR . "/ajax/settings.php" );
require_once( WPLC_PLUGIN_DIR . "/ajax/chat_server.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/wplc_updater.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/wplc_activator.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/wplc_base_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/wplc_admin_menu.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/wplc_js_manager.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/wplc_enums.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/wplc_crons.php" );

require_once (WPLC_PLUGIN_DIR . "/modules/activation_wizard/activation_wizard_controller.php");
require_once (WPLC_PLUGIN_DIR . "/modules/activation_wizard/activation_wizard_page.php");

require_once (WPLC_PLUGIN_DIR . "/modules/sessions/sessions_controller.php");
require_once (WPLC_PLUGIN_DIR . "/modules/sessions/sessions_page.php");

require_once( WPLC_PLUGIN_DIR . "/modules/session_details/session_details_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/session_details/session_details_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/offline_messages/offline_messages_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/offline_messages/offline_messages_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/custom_fields/custom_fields_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/custom_fields/custom_fields_page.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/custom_fields/manage_custom_field_controller.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/quick_responses/quick_responses_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/quick_responses/quick_responses_page.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/quick_responses/manage_quick_response_controller.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/webhooks/webhooks_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/webhooks/webhooks_page.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/webhooks/manage_webhook_controller.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/dashboard/dashboard_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/dashboard/dashboard_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/settings/settings_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/settings/settings_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/data_tools/data_tools_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/data_tools/data_tools_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/tools/tools_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/tools/tools_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/chat_client/chat_client_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/chat_client/chat_client_page.php" );


require_once( WPLC_PLUGIN_DIR . "/modules/agent_chat/agent_chat_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/agent_chat/agent_chat_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/user_settings/user_settings_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/user_settings/user_settings_page.php" );

require_once( WPLC_PLUGIN_DIR . "/modules/departments/departments_controller.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/departments/departments_page.php" );
require_once( WPLC_PLUGIN_DIR . "/modules/departments/manage_department_controller.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/data_access/chat_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/chat_rating_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/custom_fields_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/offline_messages_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/action_queue_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/webhooks_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/departments_data.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/data_access/quick_responses_data.php" );


require_once( WPLC_PLUGIN_DIR . "/includes/helpers/agents_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/chat_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/encrypt_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/utils_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/php_session_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/ringtones_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/upload_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/webhooks_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/chat_rating_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/transcripts_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/offline_messages_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/quick_responses_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/custom_fields_helper.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/helpers/theme_helper.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/models/settings.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/page_action.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/error.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/session.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/models/pager.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/message.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/offline_message.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/custom_field.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/webhook.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/department.php" );

require_once( WPLC_PLUGIN_DIR . "/includes/models/ajax_response.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/quick_response.php" );
require_once( WPLC_PLUGIN_DIR . "/includes/models/theme.php" );

// Gutenberg Blocks
require_once( WPLC_PLUGIN_DIR . "/includes/blocks/wplc-chat-box/index.php" );

/*
	 * Added back for backwards compat decrypt
	*/
if ( ! class_exists( "AES" ) ) {
	require_once( WPLC_PLUGIN_DIR . '/includes/aes_fast.php' );
}
if ( ! class_exists( "cryptoHelpers" ) ) {
	require_once( WPLC_PLUGIN_DIR . '/includes/cryptoHelpers.php' );
}

