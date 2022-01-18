<h3><?=__("Chat Encryption", 'wp-live-chat-support') ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='700'>
    <tr>
        <td width='300' valign='top'>
            <?=__("Enable Encryption", 'wp-live-chat-support') ?>: <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?=__('All messages will be encrypted when being sent to and from the user and agent.', 'wp-live-chat-support'); ?>"></i></td> 
        <td>
            <input type="checkbox" class="wplc_check" name="wplc_enable_encryption" id="wplc_enable_encryption" value="1"<?= ($wplc_settings->wplc_enable_encryption ? ' checked' : ''); ?>/>
            <p class='notice notice-error' style="margin-top:24px">
                <?=__('Once enabled, all messages sent will be encrypted. This cannot be undone.', 'wp-live-chat-support'); ?>
            </p>
        </td>
    </tr>
    <tr>
    <td width="300" valign="top">
        <label for="wplc_encryption_key"><?=__("Encryption key",'wp-live-chat-support'); ?></label>
    </td>
    <td valign="top">
        <input type="text" value="<?= esc_html($wplc_settings->wplc_encryption_key); ?>" id="wplc_encryption_key" name="wplc_encryption_key" disabled>
        <input type="hidden" name="wplc_encryption_key_nonce" id="wplc_encryption_key_nonce" value="<?= $nonces->encryption_key; ?>">
        <div style="margin-top:5px;" class="button button-secondary" id="wplc_new_encryption_key_btn"><?=__("Generate New", 'wp-live-chat-support'); ?></div>
        <p class="wplc_error_message" id="wplc_new_encryption_key_error"></p>
        <p class='notice notice-warning' style="margin-top:24px">
                <?=__('If you change encryption key, all previously encrypted messages will be lost. This cannot be undone.', 'wp-live-chat-support'); ?>
            </p>
    </td>
    </tr>          
</table>