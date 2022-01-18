<div class="wizard_body">
    <div class="row d-flex flex-row wplc_theme_chooser_container">
        <div class="my-2 wplc_colorpickers">
	        <?php require_once( WPLC_PLUGIN_DIR . "/components/theme_picker/theme_picker.php" ); ?>
        </div>
        <div class="my-2 wplc_chat_preview d-flex flex-column align-items-center">
            <div id="chat_preview_container" style="
                --call-us-form-header-background:#373737;
                --call-us-main-button-background:#0596d4;
                --call-us-client-text-color:#d4d4d4;
                --call-us-agent-text-color:#eeeeee;
                --call-us-form-height:330px;">
                <div class="panel">
                    <div class="panel_content chat-form">
                        <div class="panel_head">
                            <div class="root">
                                <div class="d-flex">
                                    <div class="user_info_container">
                                        <div class="img_cont">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46.9 46.9"
                                                 class="">
                                                <path d="M23.4 46.9C10.5 46.9 0 36.4 0 23.4c0-6.2 2.5-12.1 6.8-16.5C11.2 2.5 17.2 0 23.4 0h.1c12.9 0 23.4 10.5 23.4 23.4 0 13-10.5 23.4-23.5 23.5zm0-45.3c-12.1 0-21.9 9.8-21.8 21.9 0 5.8 2.3 11.3 6.4 15.4 4.1 4.1 9.6 6.4 15.4 6.4 12.1 0 21.8-9.8 21.8-21.9 0-12.1-9.7-21.8-21.8-21.8z"
                                                      fill="#0596d4"></path>
                                                <circle cx="23.4" cy="23.4" r="18.6" fill="#eaeaea"></circle>
                                                <path d="M27 27.6c3.1-2 4-6.1 2-9.1s-6.1-4-9.1-2-4 6.1-2 9.1c.5.8 1.2 1.5 2 2-4.4.4-7.7 4-7.7 8.4v2.2c6.6 5.1 15.9 5.1 22.5 0V36c0-4.4-3.3-8-7.7-8.4z"
                                                      fill="#fff"></path>
                                            </svg>
                                            <span class="online_icon"></span>
                                        </div>
                                        <div class="user_info">
                                            <div title="Agent" class="user_name">Agent</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action_menu">
                            <span class="action_menu_btn button-closed_8MHR-">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="-5 -5 35 35" class="">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                                    <path d="M0 0h24v24H0z"
                                          fill="none"></path>
                                </svg>
                            </span>
                            </div>
                        </div>
                        <div class="panel_body">
                            <div class="chatroot chatbody">
                                <div class="card-body card-body_chat msg_card_body">
                                    <div class="messageroot">
                                        <div class="d-flex justify-content-end msg_bubble">
                                            <div class="msg_container_send" style="color: black;">
                                                <span>Hello! How can we help you today?</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="messageroot">
                                        <div class="d-flex justify-content-start msg_bubble">
                                            <div class="img_cont_msg">
                                                 <svg class="rounded-circle user_img_msg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="64px" height="64px" viewBox="0 0 64 64" version="1.1"><circle fill="#d4d4d4" cx="32" width="64" height="64" cy="32" r="32"/><text x="50%" y="50%" style="color: #fff; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="28" font-weight="600" dy=".1em" dominant-baseline="middle">VI</text></svg>
                                            </div>
                                            <div class="msg_container" style="color: black;">
                                                <span>I want to know more about your products</span>
                                                <div class="msg_sub">
                                                <span class="msg_time">
                                                    <div class="msg_sender_name">Visitor </div>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="messageroot">
                                        <div class="d-flex justify-content-end msg_bubble">
                                            <div class="msg_container_send" style="color: black;">
                                                <span>Yes, of course! We are here to help.</span>
                                                <div class="msg_sub">
                                                <span class="msg_time_send">
                                                    <div class="msg_sender_name">Agent</div>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="img_cont_msg">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 46.9 46.9"
                                                     class="">
                                                    <path d="M23.4 46.9C10.5 46.9 0 36.4 0 23.4c0-6.2 2.5-12.1 6.8-16.5C11.2 2.5 17.2 0 23.4 0h.1c12.9 0 23.4 10.5 23.4 23.4 0 13-10.5 23.4-23.5 23.5zm0-45.3c-12.1 0-21.9 9.8-21.8 21.9 0 5.8 2.3 11.3 6.4 15.4 4.1 4.1 9.6 6.4 15.4 6.4 12.1 0 21.8-9.8 21.8-21.9 0-12.1-9.7-21.8-21.8-21.8z"
                                                          fill="#0596d4"></path>
                                                    <circle cx="23.4" cy="23.4" r="18.6" fill="#eaeaea"></circle>
                                                    <path d="M27 27.6c3.1-2 4-6.1 2-9.1s-6.1-4-9.1-2-4 6.1-2 9.1c.5.8 1.2 1.5 2 2-4.4.4-7.7 4-7.7 8.4v2.2c6.6 5.1 15.9 5.1 22.5 0V36c0-4.4-3.3-8-7.7-8.4z"
                                                          fill="#fff"></path>
                                                </svg>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="card-footer card-footer">
                                    <div class="chat-message-input-form">
                                        <div class="materialInput"><input type="text"
                                                                          readonly
                                                                          placeholder="Type your message..."
                                                                          maxlength="20479" name="chatInput"
                                                                          autocomplete="off"
                                                                          value='Type your message...'
                                                                          class="chat-message-input"></div>
                                        <div class="chat-action-buttons send-trigger">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                 data-icon="paper-plane" role="img"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 512 512"
                                                 class="svg-inline--fa fa-paper-plane fa-w-16 fa-2x"
                                                 style="width: 20px; height: 20px;">
                                                <path fill="currentColor"
                                                      d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="banner">
                                        <div class="chat-action-buttons">
                                        </div>
                                        <span class="powered-by"><a href="https://www.3cx.com" target="_blank">Powered By 3CX </a></span>
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
