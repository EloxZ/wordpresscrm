<h3><?= __( "General Settings", 'wp-live-chat-support' ) ?></h3>
<table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Chat enabled", 'wp-live-chat-support' ) ?>:
        </td>
        <td>
            <select class="wplc_settings_dropdown" id='wplc_settings_enabled' name='wplc_settings_enabled'>
                <option value="1" <?= selected( $wplc_settings->wplc_settings_enabled, 1 ) ?>>
					<?= __( "Yes", 'wp-live-chat-support' ); ?>
                </option>
                <option value="2" <?= selected( $wplc_settings->wplc_settings_enabled, 2 ) ?>>
					<?= __( "No", 'wp-live-chat-support' ); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Required Chat Box Fields", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Set default fields that will be displayed when users starting a chat", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <div class="wplc-require-user-info_item">
                <input type="radio" value="name" name="wplc_require_user_info"
                       id="wplc_require_user_info_name" <?= ( $wplc_settings->wplc_require_user_info == 'name' ? ' checked' : '' ); ?> />
                <label for="wplc_require_user_info_name">
			        <?= __( 'Name only', 'wp-live-chat-support' ); ?>
                </label>
            </div>
            <div class="wplc-require-user-info_item">
                <input type="radio" value="email" name="wplc_require_user_info"
                       id="wplc_require_user_info_email" <?= ( $wplc_settings->wplc_require_user_info == 'email' ? ' checked' : '' ); ?> />
                <label for="wplc_require_user_info_email">
			        <?= __( 'Email only', 'wp-live-chat-support' ); ?>
                </label>
            </div>
            <div class="wplc-require-user-info_item">
                <input type="radio" value="both" name="wplc_require_user_info"
                       id="wplc_require_user_info_both" <?= ( $wplc_settings->wplc_require_user_info == 'both' ? ' checked' : '' ); ?> />
                <label for="wplc_require_user_info_both">
					<?= __( 'Name and email', 'wp-live-chat-support' ); ?>
                </label>
            </div>
            <div class="wplc-require-user-info_item">
                <input type="radio" value="none" name="wplc_require_user_info"
                       id="wplc_require_user_info_none" <?= ( $wplc_settings->wplc_require_user_info == 'none' ? ' checked' : '' ); ?> />
                <label for="wplc_require_user_info_none">
					<?= __( 'None', 'wp-live-chat-support' ); ?>
                </label>
            </div>
        </td>
    </tr>
    <tr class="wplc-loggedin-user-info-row" style="<?=( $wplc_settings->wplc_require_user_info == 'none' ?'display:none':'')?>">
        <td width='300' valign='top'>
			<?= __( "Use Logged In User Details", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "A user's Name and Email Address will be used by default if they are logged in.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" class="wplc_check" value="1" id="wplc_logged_in_user_info_checkbox"
                   name="wplc_loggedin_user_info"<?= ( $wplc_settings->wplc_loggedin_user_info ? ' checked' : '' ); ?> />
        </td>
    </tr>
    <tr class="wplc-user-default-visitor-name__row"
        style="<?= $wplc_settings->wplc_require_user_info == 'name' || $wplc_settings->wplc_require_user_info == 'both' ? 'display:none' : '' ?>">
        <td width='300' valign='top'>
			<?= __( "Default visitor name", 'wp-live-chat-support' ); ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "This name will be displayed for all not logged in visitors", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="text" name="wplc_user_default_visitor_name" maxlength="25" id="wplc_user_default_visitor_name"
                   value="<?= esc_attr( $wplc_settings->wplc_user_default_visitor_name ); ?>"/>
        </td>
    </tr>
    <tr class="wplc-no-auth-text-row"
        style="display:<?= $wplc_settings->wplc_require_user_info == 'none' ? 'contents' : 'none' ?>">
        <td width='300' valign='top'>
			<?= __( "Input Field Replacement Text", 'wp-live-chat-support' ) ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "This is the text that will show in place of the Name And Email fields", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <textarea cols="45" rows="5" id="wplc_user_alternative_text_textarea"
                      name="wplc_user_alternative_text"><?= esc_textarea( $wplc_settings->wplc_user_alternative_text ); ?></textarea>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Enable On Mobile Devices", "wplivechat" ); ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Disabling this will mean that the Chat Box will not be displayed on mobile devices. (Smartphones and Tablets)", "wplivechat " ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" value="1" class="wplc_check" id="wplc_enabled_on_mobile_checkbox"
                   name="wplc_enabled_on_mobile" <?= ( $wplc_settings->wplc_enabled_on_mobile ? ' checked' : '' ); ?> />
        </td>
    </tr>

	<?php if ( $wplc_settings->wplc_channel==='mcu' ) { ?>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Play a sound when there is a new visitor", 'wp-live-chat-support' ); ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Disable this to mute the sound that is played when a new visitor arrives", 'wp-live-chat-support' ) ?>"></i>
            </td>
            <td valign='top'>
                <input type="checkbox" value="1" class="wplc_check"
                       name="wplc_enable_visitor_sound" <?= ( $wplc_settings->wplc_enable_visitor_sound ? ' checked' : '' ); ?> />
            </td>
        </tr>
	<?php } ?>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Play a sound on new message", 'wp-live-chat-support' ); ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Disable this to mute the sound that is played when a new chat message is received", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" value="1" class="wplc_check" id="wplc_enable_msg_sound_checkbox"
                   name="wplc_enable_msg_sound" <?= ( $wplc_settings->wplc_enable_msg_sound ? ' checked' : '' ); ?> />
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Delete database entries on uninstall", 'wp-live-chat-support' ); ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "This will delete all 3CX Live Chat related database entries such as options and chats on uninstall.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" value="1" class="wplc_check" id="wplc_delete_db_on_uninstall_checkbox"
                   name="wplc_delete_db_on_uninstall" <?= ( $wplc_settings->wplc_delete_db_on_uninstall ? ' checked' : '' ); ?>/>
        </td>
    </tr>
	<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Chat email notifications", 'wp-live-chat-support' ) ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Alert me via email as soon as someone wants to chat (while online only)", 'wp-live-chat-support' ); ?>"></i>
            </td>
            <td>
                <input id="wplc_pro_chat_notification" name="wplc_pro_chat_notification" type="checkbox" class="wplc_check"
                       value="1"<?= ( $wplc_settings->wplc_pro_chat_notification ? ' checked' : '' ) ?> />
            </td>
        </tr>

        <tr>
            <td width="300" valign="top">
                <label for="wplc_new_chat_ringer_count"><?= __( "Number of chat rings", 'wp-live-chat-support' ); ?>
                    <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                       title="<?= __( 'Limit the amount of time the new chat ringer will play', 'wp-live-chat-support' ); ?>"></i>
                </label>
            </td>
            <td valign="top">
                <input type="number"  min="0" step="1" max="20" class="wplc-input-number" value="<?= intval( $wplc_settings->wplc_new_chat_ringer_count ); ?>"
                       id="wplc_new_chat_ringer_count" name="wplc_new_chat_ringer_count">
            </td>
        </tr>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Agents can set their online status", 'wp-live-chat-support' ) ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( 'Checking this will allow you to change your status to Online or Offline on the Live Chat page.', 'wp-live-chat-support' ) . ' ' . __( 'If this option is disabled, agents will be always automatically online.', 'wp-live-chat-support' ); ?>"></i>
            </td>
            <td valign="top">
                <input type="checkbox" value="1" class="wplc_check"
                       name="wplc_allow_agents_set_status" <?= ( $wplc_settings->wplc_allow_agents_set_status ? ' checked' : '' ); ?> />
            </td>
        </tr>
	<?php } ?>
</table>

<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Exclude chat from 'Home' page:", 'wp-live-chat-support' ); ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Leaving this unchecked will allow the chat window to display on your home page.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" class="wplc_check" id="wplc_exclude_home_checkbox"
                   name="wplc_exclude_home" <?= ( $wplc_settings->wplc_exclude_home ? ' checked' : '' ); ?> value='1'/>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Exclude chat from 'Archive' pages:", 'wp-live-chat-support' ); ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Leaving this unchecked will allow the chat window to display on your archive pages.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
            <input type="checkbox" class="wplc_check" id="wplc_exclude_archive_checkbox"
                   name="wplc_exclude_archive" <?= ( $wplc_settings->wplc_exclude_archive ? ' checked' : '' ); ?>
                   value='1'/>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Include chat window on the following pages:", 'wp-live-chat-support' ); ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Show the chat window on the following pages. Leave blank to show on all. (Use comma-separated Page ID 's)", 'wp-live-chat-support ' ) ?>"></i>
        </td>
        <td valign='top '>
			<?php
			if ( ! empty( $wp_pages ) ) { ?>
                <select  id='wplc-multi-included-pages' name="wplc_include_on_pages[]" multiple='multiple'>
					<?php foreach ( $wp_pages as $page ) { ?>
                        <option value='<?= $page->id ?>' <?= $page->included ? 'selected' : '' ?>><?= $page->name ?></option>
						<?php
					} ?>
                </select>
			<?php } else { ?>
				<?= __( 'No pages found.', 'wp-live-chat-support' ) ?>
			<?php } ?>
        </td>
    </tr>
    <tr>
        <td width='300 ' valign='top '>
			<?= __( "Exclude chat window on the following pages:", 'wp-live-chat-support ' ); ?> <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( "Do not show the chat window on the following pages. Leave blank to show on all. (Use comma-separated Page ID's)", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
			<?php
			if ( ! empty( $wp_pages ) ) { ?>
                <select id='wplc-multi-excluded-pages' name="wplc_exclude_from_pages[]" multiple='multiple'>
					<?php foreach ( $wp_pages as $page ) { ?>
                        <option value='<?= $page->id ?>' <?= $page->excluded ? 'selected' : '' ?>><?= $page->name ?></option>
						<?php
					} ?>
                </select>
			<?php } else { ?>
				<?= __( 'No pages found.', 'wp-live-chat-support' ) ?>
			<?php } ?>

        </td>
    </tr>
    <tr class="wplc-exclude-post-types__row">
        <td width='300' valign='top'>
			<?= __( "Exclude chat window on selected post types", 'wp-live-chat-support' ); ?>: <i
                    class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                    title="<?= __( " Do not show the chat window on the following post types pages.", 'wp-live-chat-support' ) ?>"></i>
        </td>
        <td valign='top'>
			<?php
			if ( ! empty( $wp_posts_types ) ) { ?>
                <select id='wplc-multi-excluded-post-types' name="wplc_exclude_post_types[]" multiple='multiple'>
					<?php foreach ( $wp_posts_types as $post_type ) { ?>
                        <option value='<?= $post_type->name ?>' <?= $post_type->excluded ? 'selected' : '' ?>><?= $post_type->name ?></option>
						<?php
					} ?>
                </select>
			<?php } else { ?>
				<?= __( 'No post types found.', 'wp-live-chat-support' ) ?>
			<?php } ?>
        </td>
    </tr>
</table>

<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
    <h4><?= __( "Geolocalization", 'wp-live-chat-support' ) ?></h4>
    <table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Detect Visitors Country", 'wp-live-chat-support' ); ?>:
            </td>
            <td valign='top'>
                <input type="checkbox" value="1" class="wplc_check"
                       name="wplc_use_geolocalization" <?= ( $wplc_settings->wplc_use_geolocalization ? ' checked' : '' ) ?> />
                &nbsp;&nbsp;(
				<?= sprintf( __( "This feature requires the use of the GeoIP Detection plugin. Install it by going %s", 'wp-live-chat-support' ), '<a style="text-decoration: underline" href="https://wordpress.org/plugins/geoip-detect/" target="_blank">' . __( 'here', 'wp-live-chat-support' ) . '</a>' ); ?>
                )
            </td>
        </tr>
    </table>
<?php } ?>
<h4><?= __( "Google Analytics", 'wp-live-chat-support' ) ?></h4>
<table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Enable Google Analytics events", 'wp-live-chat-support' ); ?>:
        </td>
        <td valign='top'>
            <input type="checkbox" value="1" class="wplc_check"
                   name="wplc_enable_ga" <?= ( $wplc_settings->wplc_enable_ga ? ' checked' : '' ) ?> />
            (<?= __( "Prerequisite is to have configured Google Analytics on your site.", 'wp-live-chat-support' ); ?>)
        </td>
    </tr>
</table>
<div style="display:none">
    <h4><?= __( "Voice Notes", 'wp-live-chat-support' ); ?></h4>
    <table class='wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Enable Voice Notes on admin side", 'wp-live-chat-support' ); ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Enabling this will allow you to record the voice during the chat and send it to visitor once you hold on CTRL + SPACEBAR in main chat window", 'wp-live-chat-support' ) ?>"></i>
            </td>
            <td valign='top'>
                <input type="checkbox" value="1" class="wplc_check"
                       name="wplc_enable_voice_notes_on_admin" <?= ( $wplc_settings->wplc_enable_voice_notes_on_admin ? ' checked' : '' ) ?> />
            </td>
        </tr>
        <tr>
            <td width='300' valign='top'>
				<?= __( "Enable Voice Notes on visitor side", 'wp-live-chat-support' ); ?>: <i
                        class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"
                        title="<?= __( "Enabling this will allow the visitors to record the voice during the chat and send it to agent once they hold on CTRL + SPACEBAR", 'wp-live-chat-support' ) ?>"></i>
            </td>
            <td valign='top'>
                <input type="checkbox" value="1" class="wplc_check"
                       name="wplc_enable_voice_notes_on_visitor" <?= ( $wplc_settings->wplc_enable_voice_notes_on_visitor ? ' checked' : '' ) ?> />
            </td>
        </tr>
    </table>
</div>

<h4><?= __( "Advanced settings", 'wp-live-chat-support' ) ?></h4>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'>
			<?= __( "Show 'Powered by 3CX' in chat box", 'wp-live-chat-support' ) ?>:
        </td>
        <td>
            <input type="checkbox" value="1" class="wplc_check" id="wplc_powered_by_checkbox"
                   name="wplc_powered_by" <?= ( $wplc_settings->wplc_powered_by && $wplc_settings->wplc_powered_by == 1 ) ? "checked" : "" ?> />
        </td>
    </tr>
</table>

<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
<h4><?= __( "Chat Transcript Settings", 'wp-live-chat-support' ) ?></h4>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'><?= __( "Send transcripts when chat ends:", 'wp-live-chat-support' ) ?></td>
        <td>
            <input type="checkbox" value="1" class="wplc_check"
                   name="wplc_send_transcripts_when_chat_ends" <?= ( $wplc_settings->wplc_send_transcripts_when_chat_ends ? ' checked' : '' ) ?> />
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'><?= __( "Send transcripts to:", 'wp-live-chat-support' ) ?></td>
        <td>
            <select class="wplc_settings_dropdown" name="wplc_send_transcripts_to">
                <option value="user" <?= $wplc_settings->wplc_send_transcripts_to == 'user' ? 'selected' : '' ?>><?= __( "Web Visitor", 'wp-live-chat-support' ) ?></option>
                <option value="admin" <?= $wplc_settings->wplc_send_transcripts_to == 'admin' ? 'selected' : '' ?>><?= __( "Agent", 'wp-live-chat-support' ) ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'><?= __( "Email body", 'wp-live-chat-support' ) ?></td>
        <td>
			<textarea cols='85' rows='15' name="wplc_et_email_body">
                <?= trim( html_entity_decode( stripslashes( $wplc_settings->wplc_et_email_body ) ) ); ?>
            </textarea>
        </td>
    </tr>


    <tr>
        <td width='300' valign='top'><?= __( "Email header", 'wp-live-chat-support' ) ?></td>
        <td>
			<textarea cols='85' rows='5' name="wplc_et_email_header">
                <?= trim( stripslashes( $wplc_settings->wplc_et_email_header ) ); ?>
            </textarea>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'><?= __( "Email footer", 'wp-live-chat-support' ) ?></td>
        <td>
			<textarea cols='85' rows='5' name="wplc_et_email_footer">
                <?= trim( stripslashes( $wplc_settings->wplc_et_email_footer ) ); ?>
            </textarea>
        </td>
    </tr>
</table>
<?php } ?>
