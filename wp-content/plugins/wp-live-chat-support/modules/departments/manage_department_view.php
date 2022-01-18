<div class='wrap wplc_wrap'>
    <h2><?= $department->id > 0 ? __( "Edit a Department", 'wp-live-chat-support' ) : __( "Add new Department", 'wp-live-chat-support' ) ?></h2>
    <div id="wplc_container">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "save_department" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Department saved succesfully", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <form id="dep_form" class='wplc_department_form' method='POST' action="<?= $save_action_url ?>"
              novalidate="novalidate">
            <table class='wp-list-table wplc_list_table widefat striped'>
                <tr>
                    <td><?= __( "Name", 'wp-live-chat-support' ) ?></td>
                    <td><input id='wplc_department_name' name='wplc_department_name'
                               value='<?= sanitize_text_field( $department->name ) ?>' type='text' style='width:500px'>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id='wh_submit' type='submit' class='button button-primary'
                               value='<?= $department->id > 0 ? __( 'Update Department', 'wp-live-chat-support' ) : __( "Add Department", 'wp-live-chat-support' ) ?>'/>
                        <a href='<?= admin_url( "admin.php?page=wplivechat-menu-tools/#wplc_departments_tab" ) ?>' type='button'
                           class='button button-primary'
                           value='<?= __( 'Cancel', 'wp-live-chat-support' ) ?>'><?= __( 'Cancel', 'wp-live-chat-support' ) ?></a>
                    </td>
                </tr>
            </table>
            <input name='wplc_department_id' type='hidden' value='<?= $department->id ?>'>
        </form>
    </div>
</div>