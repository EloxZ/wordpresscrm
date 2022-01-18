<h3><?= __( "Styling", 'wp-live-chat-support' ) ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
    <tr class='wplc_custom_pall_rows'>
        <td width='300' valign='top'><?= __( "Theme", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <?php require_once( WPLC_PLUGIN_DIR . "/components/theme_picker/theme_picker.php" ); ?>
        </td>
    </tr>
	<?php if ( $wplc_settings->wplc_channel !== 'phone' ) { ?>
        <tr>
            <td width="300" valign="top"><?= __( "Use localization plugin", 'wp-live-chat-support' ) ?></td>
            <td>
                <input type="checkbox" class="wplc_check" name="wplc_using_localization_plugin" id="wplc_using_localization_plugin"
                       value="1"<?= ( $wplc_settings->wplc_using_localization_plugin ? ' checked' : '' ); ?> />
                <br/><small><?= sprintf( __( "Enable this if you are using a localization plugin. Should you wish to change the below strings with this option enabled, please visit %sthe documentation%s", 'wp-live-chat-support' ), "<a href='https://www.3cx.com/wp-live-chat/docs/translation/' target='_BLANK'>", '</a>' ); ?></small>
            </td>
        </tr>
	<?php } ?>
    <tr>
        <td width='300' valign='top'><?= __( "Minimized button", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <select class="wplc_settings_dropdown" id='wplc_settings_minimized_style' name='wplc_settings_minimized_style'>
                <option value="Bubble" <?= $wplc_settings->wplc_settings_minimized_style == 'Bubble' ? 'selected' : '' ?>><?= __( "Bubble", 'wp-live-chat-support' ); ?></option>
            </select>
        </td>
    </tr>
    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "Chat box title", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_chat_title" name="wplc_chat_title" type="text" size="50" maxlength="50" class="regular-text"
                   value="<?= esc_attr( $wplc_settings->wplc_chat_title ) ?>"/> <br/>
        </td>
    </tr>
    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "Chat box intro", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_chat_intro" name="wplc_chat_intro" type="text" size="50" maxlength="250"
                   class="regular-text"
                   value="<?= esc_attr( $wplc_settings->wplc_chat_intro ) ?>"/> <br/>
        </td>
    </tr>
    <?php if(false){ //postponed for later version  ?>
    <tr>
        <td width="300" valign="top"><?=__("Name request message", 'wp-live-chat-support') ?>:</td>
        <td>
            <input id="wplc_auth_name_message" name="wplc_auth_name_message" type="text" size="50" maxlength="250" class="regular-text" value="<?=isset($wplc_settings->wplc_auth_name_message)?  esc_attr($wplc_settings->wplc_auth_name_message):"" ?>" />
        </td>
    </tr>
    <tr>
        <td width="300" valign="top"><?=__("Email request message", 'wp-live-chat-support') ?>:</td>
        <td>
            <input id="wplc_auth_email_message" name="wplc_auth_email_message" type="text" size="50" maxlength="250" class="regular-text" value="<?=isset($wplc_settings->wplc_auth_email_message)?  esc_attr($wplc_settings->wplc_auth_email_message):"" ?>"/>
        </td>
    </tr>
    <?php } ?>

    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "Start chat button label", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_button_start_text" name="wplc_button_start_text" type="text" size="50" maxlength="30"
                   class="regular-text"
                   value="<?= esc_attr( $wplc_settings->wplc_button_start_text ) ?>"/> <br/>
        </td>
    </tr>
    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "Welcome message", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_welcome_msg" name="wplc_welcome_msg" type="text" size="50" maxlength="250"
                   class="regular-text" value="<?= esc_attr(stripslashes( $wplc_settings->wplc_welcome_msg )) ?>"/> <span
                    class='description'><?= sprintf(__( 'Use %s variable to display the Name provided by the website visitor.', 'wp-live-chat-support' ),'%NAME%'); ?></span><br/>
        </td>
    </tr>
    <tr>
        <td width='300' valign='top'><?= __( "Auto-response to first message", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input maxlength="250" type="text" name="wplc_pro_auto_first_response_chat_msg" id="wplc_pro_auto_first_response_chat_msg"
                   value="<?= isset( $wplc_settings->wplc_pro_auto_first_response_chat_msg ) ? esc_attr(stripslashes($wplc_settings->wplc_pro_auto_first_response_chat_msg)) : '' ?>">
            <span class='description'><?= sprintf(__( 'Use %s variable to display the Name provided by the website visitor.', 'wp-live-chat-support' ),'%NAME%'); ?></span><br/>
        </td>
    </tr>
    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "On chat end message", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_text_chat_ended" name="wplc_text_chat_ended" type="text" size="50" maxlength="250"
                   class="regular-text" style="margin-top:5px;"
                   value="<?= ( empty( $wplc_settings->wplc_text_chat_ended ) ) ? stripslashes( __( "The chat has been ended by the agent.", 'wp-live-chat-support' ) ) : esc_attr(stripslashes( $wplc_settings->wplc_text_chat_ended ))?>"/>
            <span
                    class='description'><?= sprintf(__( 'Use %s variable to display the Name provided by the website visitor.', 'wp-live-chat-support' ),'%NAME%'); ?></span><br/>
        </td>
    </tr>
    <tr class="wplc_localization_strings">
        <td width="300" valign="top"><?= __( "Agent no answer message", 'wp-live-chat-support' ) ?>:</td>
        <td>
            <input id="wplc_user_no_answer" name="wplc_user_no_answer" type="text" size="50" maxlength="250"
                   class="regular-text" value="<?= esc_attr(stripslashes( $wplc_settings->wplc_user_no_answer )); ?>"/> <span
                    class='description'><?= __( 'This text is shown to the user when an agent has failed to answer a chat', 'wp-live-chat-support' ); ?></span><br/>
        </td>
    </tr>
    <tr>
        <td><label for=""><?= __( 'Chat box animation', 'wp-live-chat-support' ); ?></label></td>

        <td>
            <div class='wplc_animation_block'>
                <div class='wplc_animation_image <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-1' ) ? 'wplc_animation_active' : "" ?>'
                     id='wplc_animation_1' data-value="animation-1">
                    <i class="fa fa-arrow-circle-up wplc_orange"></i>
                    <p><?= __( 'Slide Up', 'wp-live-chat-support' ); ?></p>
                </div>
                <div class='wplc_animation_image <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-2' ) ? 'wplc_animation_active' : "" ?>'
                     id='wplc_animation_2' data-value="animation-2">
                    <i class="fa fa-arrows-alt-h wplc_red"></i>
                    <p><?= __( 'Slide From The Side', 'wp-live-chat-support' ); ?></p>
                </div>
                <div class='wplc_animation_image <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-3' ) ? 'wplc_animation_active' : "" ?>'
                     id='wplc_animation_3' data-value="animation-3">
                    <i class="fa fa-arrows-alt wplc_orange"></i>
                    <p><?= __( 'Fade In', 'wp-live-chat-support' ); ?></p>
                </div>
                <div class='wplc_animation_image <?= ( ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-4' ) || ! isset( $wplc_settings->wplc_animation ) ) ? 'wplc_animation_active' : "" ?>'
                     id='wplc_animation_4' data-value="animation-4">
                    <i class="fa fa-thumbtack wplc_red"></i>
                    <p><?= __( 'No Animation', 'wp-live-chat-support' ); ?></p>
                </div>
            </div>
            <input type="radio" name="wplc_animation" value="animation-1" class="wplc_animation_rb wplc_hide_input"
                   id="wplc_rb_animation_1" <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-1' ) ? 'checked' : "" ?>/>
            <input type="radio" name="wplc_animation" value="animation-2" class="wplc_animation_rb wplc_hide_input"
                   id="wplc_rb_animation_2" <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-2' ) ? 'checked' : "" ?>/>
            <input type="radio" name="wplc_animation" value="animation-3" class="wplc_animation_rb wplc_hide_input"
                   id="wplc_rb_animation_3" <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-3' ) ? 'checked' : "" ?>/>
            <input type="radio" name="wplc_animation" value="animation-4" class="wplc_animation_rb wplc_hide_input"
                   id="wplc_rb_animation_4" <?= ( isset( $wplc_settings->wplc_animation ) && $wplc_settings->wplc_animation == 'animation-4' ) ? 'checked' : "" ?>/>
        </td>
    </tr>


</table>
