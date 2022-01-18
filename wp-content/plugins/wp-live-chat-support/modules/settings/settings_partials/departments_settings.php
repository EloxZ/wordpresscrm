<h3><?=__("Departments", 'wp-live-chat-support') ?></h3>
<table class="wp-list-table wplc_list_table widefat fixed striped pages">
    <tbody>
        <tr>
            <td width='300'>
                <?=__("Default Department", 'wp-live-chat-support') ?> 
                <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?=__("Default department a new chat is assigned to", 'wp-live-chat-support'); ?>"></i>
            </td>
            <td>
                <select class="wplc_settings_dropdown" id="wplc_default_department" name="wplc_default_department">
                    <option value="-1"><?=__("No Department", 'wp-live-chat-support'); ?></option>
                    <?php foreach($departments as $dep){ ?>
                        <option value="<?= $dep->id;?>" <?= (intval($wplc_settings->wplc_default_department) == intval($dep->id) ? "SELECTED" : "" ); ?> ><?= sanitize_text_field($dep->name); ?></option>
                    <?php } ?>
                </select> <a href="<?= admin_url('admin.php?page=wplivechat-menu-tools#wplc_departments_tab'); ?>" class="button button-secondary" title="<?=__('Create or Edit Departments')?>"><i class="fas fa-pencil-alt wplc_light_grey"></i></a>
            </td>
        </tr>

        <tr>
            <td width='300'>
                <?=__("User Department Selection", 'wp-live-chat-support') ?> 
                <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?=__("Allow user to select a department before starting a chat?", 'wp-live-chat-support'); ?>"></i>
            </td>
            <td>
                <input type="checkbox" class="wplc_check" name="wplc_allow_department_selection" id="wplc_allow_department_selection" value="1" <?=(boolval($wplc_settings->wplc_allow_department_selection) ? "CHECKED" : "") ?> />
            </td>
        </tr>

        <tr>
            <td width='300'>
                <?=__("Note: Chats will be transferred in the event that agents are not available within the selected department", 'wp-live-chat-support') ?> 
            </td>
            <td>
                
            </td>
        </tr>
    </tbody>
</table>
<p><?= sprintf(__("Create departments %s.",'wp-live-chat-support'),'<a href="'.admin_url('admin.php?page=wplivechat-menu-tools#wplc_departments_tab').'">'.__('here','wp-live-chat-support').'</a>'); ?></p>
<br>
