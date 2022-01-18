<?php if ( is_admin() && $show_migration_message ) { ?>
    <div id="wplc_migration_notice" class='notice notice-success is-dismissible'
         style='margin-top: 30px;margin-bottom: 5px;'>
        <span style='font-size: large;text-decoration: underline'> Migration Successful </span><br/>
        You have successfully migrated from on-premise to hosted mode.<br/>
        <strong>Tip:</strong>Switch to "3CX mode" and get free video calls, SMS, Facebook integration as well as
        reporting.<br/>
        Just fill in the registration form and <a href="https://www.3cx.com/phone-system/download-phone-system/"
                                                  target="_blank">get 3CX for free for one year</a> for unlimited users!
    </div>
<?php } ?>
<div id="wplc_sound"></div>
<div id="wplc_admin_chat_holder">
    <div class="wplc_admin_chat_on_premise_header">
        <div id="wplc_admin_chat_info_new" class="wplc_admin_chat_on_premise_header_right">
            <img src="data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzE0MiA3OS4xNjA5MjQsIDIwMTcvMDcvMTMtMDE6MDY6MzkgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE4IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpERkVFRTcwRUFENDkxMUU5QjUzM0I0QThEMzhGNzc5MyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpERkVFRTcwRkFENDkxMUU5QjUzM0I0QThEMzhGNzc5MyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkRGRUVFNzBDQUQ0OTExRTlCNTMzQjRBOEQzOEY3NzkzIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkRGRUVFNzBEQUQ0OTExRTlCNTMzQjRBOEQzOEY3NzkzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4AJkFkb2JlAGTAAAAAAQMAFQQDBgoNAAAH7QAADocAABIG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"
                 align="middle">
            <h2>3CX Live Chat</h2>
        </div>
    </div>

    <div class="bootstrap-wplc-content" id="wplc_agent_chat">
        <div class="container-fluid  h-100">
            <div class="row justify-content-center h-100">
                <div class="col-12">
                    <div id="wplc_connecting_loader" style="display: none">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <div class="row">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </div>
                            <div class="row">
                                <strong>Connecting to chat server...</strong>
                            </div>
                        </div>
                    </div>
                    <div style="display:none" id="wplc_chat_panel" class="h-100">
                        <div id="wplc_chat_list">
                            <div id="chat_list_head">
                                <div id="chat_list_head_row">
                                    <p class="chat_list_head_row_element"> <?= __( "Active Chats", "wp-live-chat-support" ); ?> </p>
                                </div>
                            </div>
                            <div id="chat_list_body">


                            </div>
                        </div>
                        <div id="inactive_chat_box">
                            <div id="wplc_chat_disable">
                                <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/offline_ic.svg' ); ?>">
                                <div id="inactive_message">
                                    <div id="wplc_chat_joined"><?= __( "Another agent is already in chat." ) ?></div>
                                    <div id="wplc_no_chat"><?= __( "There are no active chats." ) ?></div>
                                    <div id="wplc_agent_offline"><?= __( "You have set your status to offline.", 'wp-live-chat-support' ) ?>
                                        <br/>
										<?= __( "To view visitors and accept chats please set your status to online using the switch on the top admin bar.", 'wp-live-chat-support' ) ?>
                                    </div>
                                    <div id="wplc_bh_offline"><?= __( 'The Live Chat box is currently disabled on your website due to:', 'wp-livechat' ) ?>
                                        <a href="<?= admin_url( 'admin.php?page=wplivechat-menu-settings#wplc-business-hours' ) ?>"><?= __( 'Chat Operating Hours Settings', 'wp-livechat' ) ?></a>
                                    </div>
                                </div>
                            </div>
                            <div id="wplc_chat_enable">

                            </div>
                        </div>
                        <div id="active_chat_box">
                            <div id="active_chat_box_wrapper">
                                <div id="chat_box_head">
                                    <div id="wplc_avatar_header">
                                        <img id="wplc_avatar_user" alt="user avatar"
                                             src="">
                                    </div>
                                    <div id="wplc_chat_header" class="recent_heading">
                                        <h4><span id="wplc_chat_name"></span></h4>
                                        <p style="margin:0px;"><span id="wplc_chat_email"></span></p>
                                    </div>

                                    <div class="end_chat_div">
                                        <a href="javascript:void(0);" class="wplc_admin_close_chat"
                                           id="wplc_admin_close_chat"><?= __( "End chat", 'wp-live-chat-support' ) ?> [
                                            <i
                                                    class="fa fa-times"
                                                    aria-hidden="true"></i> ]
                                        </a>

                                        <a href="javascript:void(0);" class="wplc_chat_info_menu"
                                           id="wplc_chat_info_menu">
                                            <i class="fas fa-bars"
                                               aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="chat_box_body">
                                    <div class="msg_history h-100" id="wplc_chat_messages">

                                    </div>
                                    <div id="wplc_chat_footer" class="type_msg no-gutters">
                                        <div style="display:none" id="wplc_join_chat">
                                            <a href="javascript:void(0);"
                                               class="button"
                                               id="wplc_admin_join_chat"><?= __( "Join chat", 'wp-live-chat-support' ) ?>
                                            </a>
                                        </div>
                                        <div style="display:none" id="wplc_chat_actions">
											<?php if ( count( $quick_responses ) > 0 ) { ?>
                                                <div class="actions_msg">
                                                    <button type="button"
                                                            class="wplc_chat_action_button action ui-dropper"
                                                            data-drop="quick_responses_menu"
                                                            id="quick_resp_btn">
                                                        <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                                    </button>
                                                    <div id="quick_responses_menu"
                                                         class="ui-dropdown action-dropdown quick_responses_menu">
														<?php foreach ( $quick_responses as $key => $quick_response ) { ?>
                                                            <button class="dropdown-item" type="button"
                                                                    onclick="wplc_add_quick_response('<?= $quick_response->response ?>')"><?= $quick_response->title ?></button>
														<?php } ?>
                                                    </div>
                                                </div>
											<?php } ?>
											<?php if ( $wplc_settings->wplc_ux_file_share ) { ?>
                                                <input type="file" style="display:none"
                                                       id="file_input" name="file-picker"
                                                       accept="image/jpeg,image/pjpeg,image/png,image/gif,image/bmp,image/x-windows-bmp,image/tiff,image/x-tiff,application/msword,application/pdf,text/plain,application/rtf,application/x-rtf,application/mspowerpoint,application/powerpoint,application/vnd.ms-powerpoint,application/x-mspowerpoint,application/excel,application/vnd.ms-excel,application/x-excel,application/x-msexcel">
                                                <button id="file_picker" class="wplc_chat_action_button"
                                                        style="right:40px;"
                                                        type="button"><i
                                                            class="fas fa-paperclip"
                                                            aria-hidden="true"></i></button>
											<?php } ?>
                                            <div class="input_msg_write w-100">
                                                <input type="text" id="wplc_agent_chat_input" class="write_msg w-100"
                                                       placeholder="Type a message"/>
                                            </div>

                                            <button id="wplc_admin_send_msg" class="wplc_chat_action_button"
                                                    type="button">
                                                <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/svgs/send_ic.svg' ); ?>"> <?= __( "Send", "wp-live-chat-support" ) ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="wplc_sidebar">
                                <div id="wplc_sidebar_box_head">
                                    <div id="wplc_sidebar_box_head_row">
                                        <p class="wplc_sidebar_box_head_row_element"> <?= __( "Chat Information", "wp-live-chat-support" ) ?> </p>
                                    </div>

                                </div>
                                <div id="wplc_sidebar_box_body">
                                    <div class="chat_sidebar_wrapper">
                                        <a href="#" id="wplc_admin_block_ip" class="wplc_chat_action_sidebar"
                                           type="button">
                                            <i class="fa fa-ban"></i>
											<?= __( "Block ip", "wp-live-chat-support" ) ?>
                                        </a>
                                    </div>
                                    <div class="chat_sidebar_wrapper">
                                        <p class="wplc_sidebar_title"><?= __( "Visitor Information", "wp-live-chat-support" ) ?></p>
                                        <hr/>
                                        <div id="wplc_chat_visitor_info">
                                            <div id='wplc_info_visitor_name' class="wplc_sidebar_info_row">
                                                <div id='wplc_info_visitor_name_label'
                                                     class="wplc_sidebar_element_label"><?= __( "Name", "wp-live-chat-support" ) ?></div>
                                                <div id='wplc_info_visitor_name_value'
                                                     class='wplc_sidebar_element_value'></div>
                                            </div>
                                            <div id="wplc_info_visitor_email" class="wplc_sidebar_info_row">
                                                <div id='wplc_info_visitor_email_label'
                                                     class="wplc_sidebar_element_label"><?= __( "Email", "wp-live-chat-support" ) ?></div>
                                                <div id='wplc_info_visitor_email_value'
                                                     class='wplc_sidebar_element_value'></div>
                                            </div>
                                            <div id="wplc_info_visitor_department" class="wplc_sidebar_info_row">
                                                <div id='wplc_info_visitor_department_label'
                                                     class="wplc_sidebar_element_label"><?= __( "Department", "wp-live-chat-support" ) ?></div>
                                                <div id='wplc_info_visitor_department_value'
                                                     class='wplc_sidebar_element_value'></div>
                                            </div>
                                            <div id="wplc_info_visitor_browser" class="wplc_sidebar_info_row">
                                                <div id='wplc_info_visitor_browser_label'
                                                     class="wplc_sidebar_element_label"><?= __( "Browser", "wp-live-chat-support" ) ?></div>
                                                <div id='wplc_info_visitor_browser_value'
                                                     class='wplc_sidebar_element_value'></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chat_sidebar_wrapper">
                                        <p class="wplc_sidebar_title"><?= __( "Custom Fields", "wp-live-chat-support" ) ?></p>
                                        <hr/>
                                        <div id="chat_custom_fields_info">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>