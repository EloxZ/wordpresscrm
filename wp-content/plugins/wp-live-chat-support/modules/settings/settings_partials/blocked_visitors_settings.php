<h3><?=__("Blocked Visitors / IP Addresses", 'wp-live-chat-support') ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages' width='100%'>                       
    <tr>
    <td>
        <textarea name="wplc_banned_ips" style="width: 50%; min-height: 200px;" placeholder="<?=__('Enter each IP Address you would like to block on a new line', 'wp-live-chat-support'); ?>" autocomplete="false"><?= $blocked_ips ?></textarea>
        <p class="description"><?=__('Blocking a user\'s IP Address here will hide the chat window from them, preventing them from chatting with you. Each IP Address must be on a new line', 'wp-live-chat-support'); ?></p>
    </td>
    </tr>
</table>