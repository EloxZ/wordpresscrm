<h3><?= __( "Chat Box Settings", 'wp-live-chat-support' ) ?></h3>
<table class='wp-list-table wplc_list_table widefat fixed striped pages'>
    <tr>
        <td width='300' valign='top'><?= __( "Alignment", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <select class="wplc_settings_dropdown" id='wplc_settings_align' name='wplc_settings_align'>
                <option value="1" <?= $wplc_settings->wplc_settings_align == 1 ? 'selected' : '' ?> ><?= __( "Bottom left", 'wp-live-chat-support' ); ?></option>
                <option value="2" <?= $wplc_settings->wplc_settings_align == 2 ? 'selected' : '' ?>><?= __( "Bottom right", 'wp-live-chat-support' ); ?></option>
                <option value="3" <?= $wplc_settings->wplc_settings_align == 3 ? 'selected' : '' ?>><?= __( "Left", 'wp-live-chat-support' ); ?></option>
                <option value="4" <?= $wplc_settings->wplc_settings_align == 4 ? 'selected' : '' ?>><?= __( "Right", 'wp-live-chat-support' ); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
			<?= __( 'Chat box height (percent of the page)', 'wp-live-chat-support' ); ?>
        </td>
        <td>
            <select class="wplc_settings_dropdown" id='wplc_chatbox_height' name='wplc_chatbox_height'>
                <option value="0"><?= __( 'Use absolute height', 'wp-live-chat-support' ); ?></option>
				<?php
				for ( $i = 30; $i < 90; $i = $i + 10 ) {
					echo '<option value="' . $i . '" ' . ( $wplc_settings->wplc_chatbox_height == $i ? 'selected' : '' ) . '>' . $i . '%</option>';
				}
				?>
            </select>
            <span
				<?= ( $wplc_settings->wplc_chatbox_height > 0 ) ? 'style="display:none" ' : '' ?>id="wplc_chatbox_absolute_height_span"><input
                        type="number" class="wplc-input-number" id="wplc_chatbox_absolute_height" style="width:70px"
                        name="wplc_chatbox_absolute_height" min="100" max="1000" step="1"
                        value="<?= $wplc_settings->wplc_chatbox_absolute_height; ?>"/>px</span>
    </tr>
    <tr>
        <td width='300'>
			<?= __( "Automatic Chatbox Pop-Up", 'wp-live-chat-support' ) ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Expand the chat box automatically (prompts the user to enter their name and email address).", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td>
            <select class="wplc_settings_dropdown" id='wplc_auto_pop_up' name='wplc_auto_pop_up'>
                <option value="0" <?= $wplc_settings->wplc_auto_pop_up == 0 ? 'selected' : '' ?>><?= __( "Disabled", 'wp-live-chat-support' ); ?></option>
                <option value="1" <?= $wplc_settings->wplc_auto_pop_up == 1 ? 'selected' : '' ?>><?= __( "Only on desktop", 'wp-live-chat-support' ); ?></option>
                <option value="2" <?= $wplc_settings->wplc_auto_pop_up == 2 ? 'selected' : '' ?>><?= __( "Only on mobile", 'wp-live-chat-support' ); ?></option>
                <option value="3" <?= $wplc_settings->wplc_auto_pop_up == 3 ? 'selected' : '' ?>><?= __( "Both on desktop and mobile", 'wp-live-chat-support' ); ?></option>
            </select>
            <br/>
            <input type="checkbox" class="wplc_check" name="wplc_auto_pop_up_online" id="wplc_auto_pop_up_online_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_auto_pop_up_online ? ' checked' : '' ); ?>/>
            <label><?= __( "Pop-up only when agents are online", 'wp-live-chat-support' ); ?></label>
        </td>
    </tr>
    <tr>
        <td width='300'>
			<?= __( "System Language", 'wp-live-chat-support' ) ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Configure the language that will be used for texts in client's chat except the texts explicitly configured in settings section.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td>
            <select class="wplc_settings_dropdown" name='wplc_language' id='wplc_language'>
                <option value="browser" <?= ( ( 'browser' == $wplc_settings->wplc_language ) ? 'selected' : '' ) ?>><?= __( "Browser's Language", 'wp-live-chat-support' ) ?></option>
		        <?php
		        foreach ( $wplc_languages as $lang ) { ?>
                    <option value="<?= $lang->alias ?>" <?= ( ( $lang->alias == $wplc_settings->wplc_language ) ? 'selected' : '' ) ?>><?= $lang->name ?></option>
		        <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
			<?= __( "Display for chat message:", 'wp-live-chat-support' ) ?>
        </td>
        <td>
            <input type="checkbox" class="wplc_check" name="wplc_show_name" id="wplc_show_name_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_show_name ? ' checked' : '' ); ?>/>
            <label><?= __( "Name", 'wp-live-chat-support' ); ?></label><br/>
            <input type="checkbox" class="wplc_check" name="wplc_show_avatar" id="wplc_show_avatar_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_show_avatar ? ' checked' : '' ); ?>/>
            <label><?= __( "Avatar", 'wp-live-chat-support' ); ?></label><br/>
        </td>
    </tr>
    <tr>
        <td>
			<?= __( "Chat box for logged in users only:", 'wp-live-chat-support' ) ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "By checking this, only users that are logged in will be able to chat with you.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td>
            <input type="checkbox" class="wplc_check" name="wplc_display_to_loggedin_only" id="wplc_display_to_loggedin_only_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_display_to_loggedin_only ? ' checked' : '' ); ?>/>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Show agent's name", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Agent's name will be shown to client's chat box as it is specified on user profile information.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" class="wplc_check" value="1"
                   id="wplc_show_agent_name"
                   name="wplc_show_agent_name" <?= ( $wplc_settings->wplc_show_agent_name ? ' checked' : '' ); ?> />


        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Default agent's name", 'wp-live-chat-support' ) ?>:
        </td>
        <td valign='top'>
            <input id="wplc_agent_default_name" name="wplc_agent_default_name" maxlength="250"
                   type="text" value="<?= esc_attr( $wplc_settings->wplc_agent_default_name ) ?>"/>
        </td>
    </tr>
    <tr>
        <td>
			<?= __( "Display a timestamp in the chat window:", 'wp-live-chat-support' ) ?>
        </td>
        <td>
            <input type="checkbox" class="wplc_check" name="wplc_show_date" id="wplc_show_date_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_show_date ? ' checked' : '' ); ?>/>
            <label><?= __( "Date", 'wp-live-chat-support' ); ?></label><br/>
            <input type="checkbox" class="wplc_check" name="wplc_show_time" id="wplc_show_time_checkbox"
                   value="1"<?= ( $wplc_settings->wplc_show_time ? ' checked' : '' ); ?>/>
            <label><?= __( "Time", 'wp-live-chat-support' ); ?></label>
        </td>
    </tr>
</table>

<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
	<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Incoming chat ring tone", 'wp-live-chat-support' ) ?>:
            </td>
            <td>
                <select class="wplc_settings_dropdown" name='wplc_ringtone' id='wplc_ringtone'>
					<?php
					foreach ( $wplc_ringtones->ringtones as $k => $v ) { ?>
                        <option playurl="<?= TCXRingtonesHelper::get_ringtone_url( $k ) ?>"
                                value="<?= $k ?>" <?= ( ( $k == $wplc_ringtones->ringtone_selected ) ? 'selected' : '' ) ?>><?= $v ?></option>
					<?php } ?>
                </select>
                <button type='button' id='wplc_sample_ring_tone'><i class='fa fa-play wplc-fa'></i></button>
            </td>
        </tr>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Incoming message tone", 'wp-live-chat-support' ) ?>:
            </td>
            <td>
                <select class="wplc_settings_dropdown" name='wplc_messagetone' id='wplc_messagetone'>
					<?php
					foreach ( $wplc_ringtones->messagetones as $k => $v ) { ?>
                        <option playurl="<?= TCXRingtonesHelper::get_messagetone_url( $k, WPLC_PLUGIN_URL . 'includes/sounds/general/Default_message.mp3' ) ?>"
                                value="<?= $k ?>" <?= ( ( $k == $wplc_ringtones->messagetone_selected ) ? 'selected' : '' ) ?>><?= $v ?></option>
					<?php } ?>
                </select>
                <button type='button' id='wplc_sample_message_tone'><i class='fa fa-play'></i></button>
            </td>
        </tr>
	<?php } ?>
    <!-- Chat Icon-->
    <tr class='wplc-icon-area'>
        <td width='300' valign='top'>
			<?= __( "Icon", 'wp-live-chat-support' ) ?>:
        </td>
        <td>
            <div class="wplc_default_chat_icon_selector"
                 style="display:block;max-height:50px;"
                 id="wplc_icon_area">
                <div id="wplc_icon_default">
                    <div class="wplc_default_chat_icon_selected" data-icontype="url"
                         style="background-color:<?= $wplc_settings->wplc_settings_base_color; ?>;<?= $wplc_settings->wplc_chat_icon_type !== "url" ? "display:none" : "" ?>">
                        <img src="<?= trim( urldecode( $wplc_settings->wplc_chat_icon ) ); ?>"
                             style="margin-top: 13px;width:25px;height:25px;"/>
                    </div>
                    <div class="wplc_default_chat_icon_selected" data-icontype="Default"
                         style="background-color:<?= $wplc_settings->wplc_settings_base_color; ?>;<?= $wplc_settings->wplc_chat_icon_type !== "Default" ? "display:none" : "" ?>">
						<?= $icons["default_icon"] ?>
                    </div>
                    <div class="wplc_default_chat_icon_selected" data-icontype="Bubble"
                         style="background-color:<?= $wplc_settings->wplc_settings_base_color; ?>;<?= $wplc_settings->wplc_chat_icon_type !== "Bubble" ? "display:none" : "" ?>">
						<?= $icons["bubble_icon"] ?>
                    </div>
                    <div class="wplc_default_chat_icon_selected" data-icontype="DoubleBubble"
                         style="background-color:<?= $wplc_settings->wplc_settings_base_color; ?>;<?= $wplc_settings->wplc_chat_icon_type !== "DoubleBubble" ? "display:none" : "" ?>">
						<?= $icons["double_bubble_icon"] ?>
                    </div>
                </div>
            </div>
            <input id="wplc_chat_icon" name="wplc_chat_icon" type="hidden" size="35" class="regular-text"
                   maxlength="700"
                   value="<?= base64_encode( trim( urldecode( $wplc_settings->wplc_chat_icon ) ) ); ?>"/>
            <br/>
            <input id="wplc_btn_upload_icon" name="wplc_btn_upload_icon" type="button"
                   class="button button-primary valid" value="<?= __( "Upload", 'wp-live-chat-support' ) ?>"/>
            <input id="wplc_btn_select_default_icon" name="wplc_btn_select_default_icon" type="button"
                   class="button button-default valid"
                   value="<?= __( "Default set", 'wp-live-chat-support' ) ?>"/>
            <br/>
			<?= __( "Recommended Size 50x50 (px)", 'wp-live-chat-support' ) ?>

            <div id="wplc_default_chat_icons" style="display: none">
                <strong><?= __( "Select Default Icon", 'wp-live-chat-support' ); ?></strong>
                <div id="wplc_icon_selection">
                    <div class="wplc_default_chat_icon_selector" data-icontype="Default">
						<?= $icons["default_icon"] ?>
                    </div>
                    <div class="wplc_default_chat_icon_selector" data-icontype="Bubble">
						<?= $icons["bubble_icon"] ?>
                    </div>
                    <div class="wplc_default_chat_icon_selector" data-icontype="DoubleBubble">
						<?= $icons["double_bubble_icon"] ?>
                    </div>
                </div>
            </div>
            <input id="wplc_chat_icon_type" name="wplc_chat_icon_type" type="hidden"
                   value="<?= $wplc_settings->wplc_chat_icon_type ?>"/>
        </td>
    </tr>

    <!-- Chat Logo-->
    <tr class='wplc-logo-area'>
        <td width='300' valign='top'>
			<?= __( "Logo", 'wp-live-chat-support' ) ?>:
        </td>
        <td>
            <div style="display:block" id="wplc_logo_area">
                <img id="wplc_logo_preview" src="<?= trim( urldecode( $wplc_settings->wplc_chat_logo ) ); ?>" width="100px"/>
            </div>
            <input id="wplc_chat_logo" name="wplc_chat_logo" type="hidden" size="35" class="regular-text"
                   maxlength="700"
                   value="<?= base64_encode( trim( urldecode( $wplc_settings->wplc_chat_logo ) ) ); ?>"/>
            <input id="wplc_btn_upload_logo" name="wplc_btn_upload_logo" type="button"
                   class="button button-primary valid" value="<?= __( "Upload", 'wp-live-chat-support' ) ?>"/>
            <input id="wplc_btn_remove_logo" name="wplc_btn_remove_logo" type="button"
                   class="button button-default valid" value="<?= __( "Remove", 'wp-live-chat-support' ) ?>"/><br/>
			<?= __( "Recommended Size 250x40 (px)", 'wp-live-chat-support' ) ?>
        </td>
    </tr>

    <tr class='wplc-agent-logo-area'>
        <td width='300' valign='top'>
			<?= __( "Agent default picture", 'wp-live-chat-support' ) ?>:
        </td>
        <td>
            <div style="display:block" id="wplc_agent_logo_area">
                <img id="wplc_agent_logo_preview" src="<?= $agent_logo ?>" width="100px"/>
            </div>
            <input id="wplc_agent_logo" name="wplc_agent_logo" type="hidden" size="35" class="regular-text"
                   maxlength="700"
                   value="<?= base64_encode( $agent_logo_value ); ?>"/>
            <input id="wplc_btn_upload_agent_logo" name="wplc_btn_upload_agent_logo" type="button"
                   class="button button-primary valid" value="<?= __( "Upload", 'wp-live-chat-support' ) ?>"/>
            <input id="wplc_btn_use_default_agent_logo" name="wplc_btn_use_default_agent_logo" type="button"
                   class="button button-default valid" value="<?= __( "Reset to default", 'wp-live-chat-support' ) ?>"/><br/>
			<?= __( "Recommended Size 30x30 (px)", 'wp-live-chat-support' ) ?>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'>
			<?= __( "Chat button delayed startup (seconds)", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "How long to delay showing the Live Chat button on a page", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td>
            <input id="wplc_chat_delay" name="wplc_chat_delay" type="text" size="6" maxlength="4"
                   value="<?= intval( $wplc_settings->wplc_chat_delay ); ?>"/>
        </td>
    </tr>

</table>
<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
    <h3><?= __( "User Experience", 'wp-live-chat-support' ) ?></h3>
    <table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
        <tbody>
        <tr>
            <td width='300' valign='top'><?= __( "Share files", 'wp-live-chat-support' ) ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Adds file sharing to your chat box!", 'wp-live-chat-support' ) ?>"></i></td>
            <td><input id='wplc_ux_file_share' class="wplc_check" name='wplc_ux_file_share'
                       type='checkbox'<?= ( $wplc_settings->wplc_ux_file_share ? ' checked' : '' ) ?> /></td>
        </tr>
        <tr>
            <td width='300' valign='top'><?= __( "Visitor experience ratings", 'wp-live-chat-support' ) ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Allows users to rate the chat experience with an agent.", 'wp-live-chat-support' ) ?>"></i>
            </td>
            <td><input id='wplc_ux_exp_rating' name='wplc_ux_exp_rating' class="wplc_check"
                       type='checkbox'<?= ( $wplc_settings->wplc_ux_exp_rating ? ' checked' : '' ) ?> /></td>
        </tr>

        <tr class="wplc_rating_message_row">
            <td width="300" valign="top"><?=__("Rating request message", 'wp-live-chat-support') ?>:</td>
            <td>
                <input id="wplc_rate_message" name="wplc_rate_message" type="text" size="50" maxlength="250" class="regular-text" value="<?=isset($wplc_settings->wplc_rate_message)?  esc_attr($wplc_settings->wplc_rate_message):"" ?>" />
            </td>
        </tr>
        <tr class="wplc_rating_message_row">
            <td width="300" valign="top"><?=__("Feedback option message", 'wp-live-chat-support') ?>:</td>
            <td>
                <input id="wplc_rate_feedback_request_message" name="wplc_rate_feedback_request_message" type="text" size="50" maxlength="250" class="regular-text" value="<?=isset($wplc_settings->wplc_rate_feedback_request_message)?  esc_attr($wplc_settings->wplc_rate_feedback_request_message):"" ?>" />
            </td>
        </tr>
        <tr class="wplc_rating_message_row">
            <td width="300" valign="top"><?=__("Feedback request message", 'wp-live-chat-support') ?>:</td>
            <td>
                <input id="wplc_rate_comments_message" name="wplc_rate_comments_message" type="text" size="50" maxlength="250" class="regular-text" value="<?=isset($wplc_settings->wplc_rate_comments_message)?  esc_attr($wplc_settings->wplc_rate_comments_message):"" ?>" />
            </td>
        </tr>

        </tbody>
    </table>
<?php } ?>

<h3><?= __( "Greeting", 'wp-live-chat-support' ) ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
    <tbody>
    <tr>
        <td width="300" valign="top">
            <label for="wplc_channel"><?= __( "Visibility", 'wp-live-chat-support' ); ?>:</label>
        </td>
        <td valign="top">

            <select class="wplc_settings_dropdown" id="wplc_greeting_mode" name="wplc_greeting_mode">
                <option <?= $wplc_settings->wplc_greeting_mode == 'none' ? 'selected' : ''; ?> value="none">Disabled</option>
                <option <?= $wplc_settings->wplc_greeting_mode == 'desktop' ? 'selected' : ''; ?> value="desktop">Only on desktop</option>
                <option <?= $wplc_settings->wplc_greeting_mode == 'mobile' ? 'selected' : ''; ?> value="mobile">Only on mobile</option>
                <option <?= $wplc_settings->wplc_greeting_mode == 'both' ? 'selected' : ''; ?> value="both">Both on desktop and mobile</option>
            </select>
        </td>
    </tr>
    <tr id="wplc_greeting_message_row">
        <td width='300' valign='top'>
			<?= __( "Greeting Text", 'wp-live-chat-support' ) ?>:
        </td>
        <td valign='top'>
            <input id="wplc_greeting_message" name="wplc_greeting_message"
                   type="text" value="<?= esc_attr( $wplc_settings->wplc_greeting_message ) ?>"/>
        </td>
    </tr>
    </tbody>
</table>

<h3><?= __( "Social", 'wp-live-chat-support' ) ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>
    <tbody>
    <tr>
        <td width='300' valign='top'><?= __( "Facebook URL", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Link your Facebook page here. Leave blank to hide", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td><input id='wplc_social_fb' class='wplc_check_url' name='wplc_social_fb'
                   placeholder="<?= __( "Facebook URL", 'wp-live-chat-support' ) ?>..." type='text'
                   value="<?= urldecode( $wplc_settings->wplc_social_fb ); ?>"/>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'><?= __( "Twitter URL", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Link your Twitter page here. Leave blank to hide", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td><input id='wplc_social_tw' class='wplc_check_url' name='wplc_social_tw'
                   placeholder="<?= __( "Twitter URL", 'wp-live-chat-support' ) ?>..." type='text'
                   value="<?= urldecode( $wplc_settings->wplc_social_tw ); ?>"/>
        </td>

    </tr>
    </tbody>
</table>


<?php do_action( 'wplc_hook_admin_settings_chat_box_settings_after' ); ?>
