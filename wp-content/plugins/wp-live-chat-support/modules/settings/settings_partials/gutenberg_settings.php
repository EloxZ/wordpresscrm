<h3><?=__('Gutenberg Blocks', 'wp-live-chat-support') ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>

    <tr>
        <td width='300' valign='top'><?=__('Enable Gutenberg Blocks', 'wp-live-chat-support') ?>:</td> 

        <td>
            <input type="checkbox" class="wplc_check" id="activate_block" name="wplc_gutenberg_settings[enable]" <?= $gutenberg['enable'] == 1 ? 'checked' : '' ?>/>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Block size', 'wp-live-chat-support') ?>:</td> 
        <td>
            <select class="wplc_settings_dropdown" id="wplc_gutenberg_size" name="wplc_gutenberg_settings[size]" value="1">
                <option <?= ($gutenberg['size'] == 1) ? 'selected' : ''; ?> value="1">Small</option>
                <option <?= ($gutenberg['size'] == 2) ? 'selected' : ''; ?> value="2">Medium</option>
                <option <?= ($gutenberg['size'] == 3) ? 'selected' : ''; ?> value="3">Large</option>
            </select>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Set block logo', 'wp-live-chat-support') ?>:</td>

        <td>
            <input type="button" id="wplc_gutenberg_upload_logo" class="button button-primary" value="Upload Logo"/>
            <input type="button" id="wplc_gutenberg_remove_logo" class="button button-default" value="Reset Logo"/>
            <input type="hidden" id="wplc_gutenberg_default_logo" value="<?= $gutenberg['default_logo']; ?>" />
            <input type="hidden" id="wplc_gutenberg_logo" name="wplc_gutenberg_settings[logo]" value="<?= $gutenberg['logo']; ?>"/>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Text in block', 'wp-live-chat-support') ?>:</td>

        <td>
            <input type="text" id="wplc_gutenberg_text" name="wplc_gutenberg_settings[text]" placeholder="Block text" value="<?= $gutenberg['text'] ?>"/>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Use icon', 'wp-live-chat-support') ?>:<td>
            <input type="checkbox" id="wplc_gutenberg_enable_icon" class="wplc_check" name="wplc_gutenberg_settings[enable_icon]" <?= $gutenberg['enable_icon'] == 1 ? 'checked' : '' ?>/>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Icon in block', 'wp-live-chat-support') ?>:</td>

        <td>
            <input type="text" id="wplc_gutenberg_icon" name=" wplc_gutenberg_settings[icon]" value="<?= $gutenberg['icon'] ?>"/>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__("Preview block", 'wp-live-chat-support') ?>:</td>

        <td>
            <div id="wplc-chat-box" class="wplc_gutenberg_preview"></div>
        </td>
    </tr>

    <tr>
        <td width='300' valign='top'><?=__('Custom HTML Template', 'wp-live-chat-support') ?>:
            <small><p><i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip"></i> You can use the following placeholders to add content dynamically:</p>
            <p><code class="wplc_code" title="Click to copy text">{wplc_logo}</code> - <?=__('Displays the chosen logo', 'wp-live-chat-support'); ?></p>
            <p><code class="wplc_code" title="Click to copy text">{wplc_text}</code> - <?=__('Displays the chosen custom text', 'wp-live-chat-support'); ?></p>
            <p><code class="wplc_code" title="Click to copy text">{wplc_icon}</code> - <?=__('Displays the chosen icon', 'wp-live-chat-support'); ?></p></small>
        </td>

        <td>
            <div id='wplc_custom_html_editor'></div>
            <textarea name='wplc_gutenberg_settings[custom_html]' id='wplc_custom_html' style='display: none;' data-editor='css' rows='12'>
                <?= trim($gutenberg['custom_html']); ?>
            </textarea>
            
            
            <input type="button" id="wplc_gutenberg_reset_html" class="button button-default" value="Reset Default"/>
            <select name="wplc_gutenberg_settings[theme]" class="wplc_settings_dropdown" id="wplc_custom_templates">
                <option value="template_default" <?= $gutenberg['theme'] == 'template_default' ? 'selected' : '' ?>>Default - Dark</option>
                <option value="template_default_light" <?= $gutenberg['theme'] == 'template_default_light' ? 'selected' : '' ?>>Default - Light</option>
                <option value="template_default_tooltip" <?= $gutenberg['theme'] == 'template_default_tooltip' ? 'selected' : '' ?>>Default - Tooltip</option>
                <option value="template_circle" <?= $gutenberg['theme'] == 'template_circle' ? 'selected' : '' ?>>Circle - Default</option>
                <option value="template_tooltip" <?= $gutenberg['theme'] == 'template_tooltip' ? 'selected' : '' ?>>Circle - Tooltip</option>
                <option value="template_circle_rotate" <?= $gutenberg['theme'] == 'template_circle_rotate' ? 'selected' : '' ?>>Circle - Rotating</option>
                <option value="template_chat_bubble" <?= $gutenberg['theme'] == 'template_chat_bubble' ? 'selected' : '' ?>>Chat Bubble</option>
                
            </select>
        </td>
    </tr>
</table>