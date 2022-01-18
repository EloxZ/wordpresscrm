<div class='wrap wplc_wrap'>
    <h2><?= $webhook->id > 0 ? __( "Edit a Web hook", 'wp-live-chat-support' ) : __( "Add new Web hook", 'wp-live-chat-support' ) ?></h2>
    <div id="wplc_container">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "save_webhook" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Web hook saved succesfully", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <form id="wh_form" class='wplc_webhook_form' method='POST' action="<?= $save_action_url ?>"
              novalidate="novalidate">
            <table class='wp-list-table wplc_list_table widefat striped'>
                <tr>
                    <td><?= __( "Event", 'wp-live-chat-support' ) ?></td>
                    <td>
                        <select class="wplc_settings_dropdown" id='wplc_webhook_event' name='wplc_webhook_event' style='width:200px'>
							<?php foreach ( $webhook_events as $key => $event_name ) { ?>
                                <option value='<?= $key ?>' <?= $webhook->action == $key ? 'selected' : '' ?>><?= $event_name ?></option>
							<?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= __( "Target URL", 'wp-live-chat-support' ) ?></td>
                    <td><input placeholder='http://example.com/webhook_handler' id='wplc_webhook_domain'
                               name='wplc_webhook_domain' value='<?= esc_url( $webhook->url ) ?>' type='text'
                               style='width:500px'></td>
                </tr>
                <tr>
                    <td><?= __( "Method", 'wp-live-chat-support' ) ?></td>
                    <td>
                        <select class="wplc_settings_dropdown" id='wplc_webhook_method' name='wplc_webhook_method' style='width:200px'
                                value='<?= $webhook->method ?>'>
                            <option value='GET' <?= ( $webhook->method === "GET" ? "selected" : "" ) ?>><?= __( "GET", 'wp-live-chat-support' ) ?></option>
                            <option value='POST' <?= ( $webhook->method === "POST" ? "selected" : "" ) ?>><?= __( "POST", 'wp-live-chat-support' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input id='wh_submit' type='submit' class='button button-primary'
                               value='<?= $webhook->id > 0 ? __( 'Update Web hook', 'wp-live-chat-support' ) : __( "Add Web hook", 'wp-live-chat-support' ) ?>'/>
                        <a href='<?= admin_url( "admin.php?page=wplivechat-menu-tools/#wplc_webhooks_tab" ) ?>' type='button'
                           class='button button-primary'
                           value='<?= __( 'Cancel', 'wp-live-chat-support' ) ?>'><?= __( 'Cancel', 'wp-live-chat-support' ) ?></a>
                    </td>
                </tr>
            </table>
            <input name='wplc_webhook_id' type='hidden' value='<?= $webhook->id ?>'>
        </form>
    </div>
</div>