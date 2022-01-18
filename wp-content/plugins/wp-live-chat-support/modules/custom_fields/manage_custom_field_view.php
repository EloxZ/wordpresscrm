<div class='wrap wplc_wrap'>
    <h2><?= $custom_field->id > 0 ? __( "Edit a Custom Field", 'wp-live-chat-support' ) : __( "Add new Custom Field", 'wp-live-chat-support' ) ?></h2>
    <div id="wplc_container">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "save_custom_field" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Custom field saved succesfully", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <form id="cf_form" class='wplc_custom_field_form' method='POST' action="<?= $save_action_url ?>"
              novalidate="novalidate">
            <table class='wp-list-table wplc_list_table widefat fixed wpgmza-listing'>
                <tbody>
                <tr>
                    <td><?= __( 'Field Name', 'wp-live-chat-support' ) ?></td>
                    <td><input type='text' name='wplc_field_name' id='wplc_field_name' style='width: 250px;'
                               value='<?= stripslashes( esc_html( $custom_field->name ) ) ?>'/></td>
                </tr>
                <tr>
                    <td><?= __( 'Field Type', 'wp-live-chat-support' ) ?></td>
                    <td>
                        <select class="wplc_settings_dropdown" name='wplc_field_type' id='wplc_field_type' style='width: 250px;'>
                            <option value='0' <?= $custom_field->type == 0 ? 'selected' : '' ?>><?= __( "Text", 'wp-live-chat-support' ) ?></option>
                            <option value='1' <?= $custom_field->type == 1 ? 'selected' : '' ?>><?= __( "Drop Down", 'wp-live-chat-support' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr id='wplc_field_value_dropdown_row'>
                    <td><?= __( 'Drop Down Contents', 'wp-live-chat-support' ) ?></td>
                    <td><textarea name='wplc_drop_down_values' id='wplc_drop_down_values' rows='6'
                                  style='width: 250px;'><?= $custom_field->getViewContent( 'esc_textarea' ) ?></textarea><br/><small><?= __( "Enter each option on a new line", 'wp-live-chat-support' ) ?></small>
                    </td>
                </tr>
                <tr>
                    <td><?= __( 'Status', 'wp-live-chat-support' ) ?></td>
                    <td>
                        <select class="wplc_settings_dropdown" name='wplc_field_status' id='wplc_field_status' style='width: 250px;'>
                            <option value='1' <?= $custom_field->status == 1 ? 'selected' : '' ?>><?= __( "Active", 'wp-live-chat-support' ) ?></option>
                            <option value='0' <?= $custom_field->status == 0 ? 'selected' : '' ?>><?= __( "Inactive", 'wp-live-chat-support' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id='cf_submit' type='submit' class='button button-primary'
                               value='<?= $custom_field->id > 0 ? __( 'Update Custom Field', 'wp-live-chat-support' ) : __( "Add Custom Field", 'wp-live-chat-support' ) ?>'/>
                        <a href='<?= admin_url( "admin.php?page=wplivechat-menu-tools/#wplc_custom_fields_tab" ) ?>' type='button'
                           class='button button-primary'
                           value='<?= __( 'Cancel', 'wp-live-chat-support' ) ?>'><?= __( 'Cancel', 'wp-live-chat-support' ) ?></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <input name='wplc_custom_field_id' type='hidden' value='<?= $custom_field->id ?>'>
        </form>
    </div>
</div>