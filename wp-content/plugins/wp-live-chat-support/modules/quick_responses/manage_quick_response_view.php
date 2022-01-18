<div class='wrap wplc_wrap'>
    <h2><?= $quick_response->id > 0 ? __( "Edit a Quick response", 'wp-live-chat-support' ) : __( "Add new Quick response", 'wp-live-chat-support' ) ?></h2>
    <div id="wplc_container">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "save_quick_response" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Quick response saved succesfully", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <form id="qr_form" class='wplc_quick_response_form' method='POST' action="<?= $save_action_url ?>"
              novalidate="novalidate">
            <table class='wp-list-table wplc_list_table widefat striped'>
                <tr>
                    <td><?= __( "Title", 'wp-live-chat-support' ) ?></td>
                    <td><input placeholder='Title' id='wplc_quick_response_title' name='wplc_quick_response_title'
                               value='<?= sanitize_text_field( $quick_response->title ) ?>' type='text'
                               style='width:500px'></td>
                </tr>
                <tr>
                    <td><?= __( "Response", 'wp-live-chat-support' ) ?></td>
                    <td><textarea placeholder='Type here the response message' id='wplc_quick_response_response'
                                  name='wplc_quick_response_response' rows="5"
                                  style='width:500px'><?= sanitize_text_field( $quick_response->response ) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><?= __( "Display order", 'wp-live-chat-support' ) ?></td>
                    <td><input placeholder='Display order' id='wplc_quick_response_sort' name='wplc_quick_response_sort'
                               value='<?= intval( $quick_response->sort ) ?>' type='number' style='width:500px'></td>
                </tr>
                <tr>
                    <td><?= __( 'Status', 'wp-live-chat-support' ) ?></td>
                    <td>
                        <select class="wplc_settings_dropdown" name='wplc_quick_response_status' id='wplc_quick_response_status' style='width: 250px;'>
                            <option value='1' <?= $quick_response->status == 1 ? 'selected' : '' ?>><?= __( "Active", 'wp-live-chat-support' ) ?></option>
                            <option value='0' <?= $quick_response->status == 0 ? 'selected' : '' ?>><?= __( "Inactive", 'wp-live-chat-support' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id='wh_submit' type='submit' class='button button-primary'
                               value='<?= $quick_response->id > 0 ? __( 'Update Quick response', 'wp-live-chat-support' ) : __( "Add Quick response", 'wp-live-chat-support' ) ?>'/>
                        <a href='<?= admin_url( "admin.php?page=wplivechat-menu-tools/#wplc_quick_responses_tab" ) ?>' type='button'
                           class='button button-primary'
                           value='<?= __( 'Cancel', 'wp-live-chat-support' ) ?>'><?= __( 'Cancel', 'wp-live-chat-support' ) ?></a>
                    </td>
                </tr>
            </table>
            <input name='wplc_quick_response_id' type='hidden' value='<?= $quick_response->id ?>'>
        </form>
    </div>
</div>