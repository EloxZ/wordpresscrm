<a name='wplc-user-fields'></a><h2><?=__( '3CX Live Chat - User Fields', 'wp-live-chat-support')?></h2>
<table class='form-table'>
    <tr>
        <th><label for='wplc_user_tagline'><?=__('User tagline', 'wp-live-chat-support')?></label></th>
        <td>
            <label for='wplc_user_tagline'>
                <textarea name='wplc_user_tagline' id='wplc_user_tagline' rows='6'>
                    <?= $userTag ?>
                </textarea>
                <br/>
                <small><?=__( 'This will show up at the top of the chatbox - Leave blank to disable.', 'wp-live-chat-support')?></small>
            </label>
        </td>
    </tr>
</table>
<table class="form-table">
    <tr>
        <th>
            <label for="wplc_ma_agent"> <?=__('Chat Agent', 'wp-live-chat-support')?></label>
        </th>
        <td>
            <label for="wplc_ma_agent">
                <input name="wplc_ma_agent" type="checkbox" class="wplc_check" id="wplc_ma_agent" value="1" <?=$is_user_agent ? "checked='checked'" : "" ?>>
                <?=__("Make this user a chat agent",'wp-live-chat-support')?>
            </label>
        </td>
    </tr>
</table>
<table class="form-table">
    <tr>
        <th>
            <label for="wplc_user_department"><?=__('Chat Department', 'wp-live-chat-support')?></label>
        </th>
        <td>
            <select class="wplc_settings_dropdown" id="wplc_user_department" class="wplc_settings_dropdown" name="wplc_user_department">
                <option value="-1"><?= __("No Department", 'wp-live-chat-support'); ?></option>
	            <?php
		            foreach($departments as $dep) {
			            ?>
                        <option value="<?=$dep->id?>" <?=($selected_department === intval($dep->id) ? "selected" : "" )?> ><?= sanitize_text_field($dep->name); ?></option>
			            <?php
		            }
	            ?>
            </select>
        </td>
    </tr>
</table>