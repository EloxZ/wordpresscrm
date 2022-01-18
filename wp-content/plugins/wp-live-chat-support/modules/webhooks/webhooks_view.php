<div class="wrap wplc_wrap">
    <h3>
		<?= $page_title ?>
        <a href='?page=wplivechat-manage-webhook&nonce=<?= wp_create_nonce( "edit_webhook" ) ?>&whid=-1'
           class='wplc_add_new_btn'><?= __( "Add New", 'wp-livechat' ) ?></a>
    </h3>
    <div id="wplc_container">
		<?php if ( $selected_action->name == "prompt_remove_webhook" && is_numeric( $whid ) ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Are you sure you want to delete this web hook?", 'wp-live-chat-support' ) ?>
                <br>
                <a class='button'
                   href='?page=wplivechat-menu-tools&wplc_action=execute_remove_webhook&whid=<?= sanitize_text_field( $whid ) ?>&nonce=<?= $delete_webhook_nonce ?>#wplc_webhooks_tab'><?= __( "Yes", 'wp-live-chat-support' ) ?></a>
                <a class='button' href='?page=wplivechat-menu-tools#wplc_webhooks_tab'><?= __( "No", 'wp-live-chat-support' ) ?></a>
            </div>
		<?php } else if ( $selected_action->name == "prompt_remove_webhook" ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete web hook", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_webhook" && ! ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete web hook", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_webhook" && ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Web hook Deleted", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <table class="wp-list-table wplc_list_table widefat fixed striped" cellspacing="0">
            <thead>
            <tr>
                <th scope='col' id='wplc_id_colum' class='manage-column column-id'>
                    <span><?= __( "ID", 'wp-live-chat-support' ) ?></span></th>
                <th scope='col' id='wplc_event_colum'
                    class='manage-column'><?= __( "Event", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_target_colum'
                    class='manage-column'><?= __( "Target URL", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_method_colum'
                    class='manage-column'><?= __( "Method", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_action_colum'
                    class='manage-column'><?= __( "Actions", 'wp-live-chat-support' ) ?></th>
            </tr>
            </thead>
            <tbody id="the-list" class='list:wp_list_text_link'>
			<?php
			if ( ! $webhooks ) {
				?>
                <tr>
                    <td colspan='5'><?php __( "Create your first web hook", 'wp-live-chat-support' ) ?></td>
                </tr>
				<?php
			} else {
				foreach ( $webhooks as $result ) {
					?>
                    <tr id="record_<?= intval( $result->id ) ?>" style="height:30px;" \>
                        <td class='field_id' id='field_id_<?= intval( $result->id ) ?>'><?= $result->id ?></td>
                        <td class='field_event'
                            id='field_event_<?= intval( $result->id ) ?>'><?= $result->getActionName() ?></td>
                        <td class='field_target'
                            id='field_target_<?= intval( $result->id ) ?>'><?= esc_url( $result->url ) ?></td>
                        <td class='field_method'
                            id='field_method_<?= intval( $result->id ) ?>'><?= $result->method ?></td>
                        <td class='field_actions' id='field_actions_<?= intval( $result->id ) ?>'>
                            <a href='<?= $result->getEditUrl() ?>'
                               class='button'><?= __( "Edit", 'wp-live-chat-support' ) ?></a>
                            <a href='<?= $result->getRemoveUrl() ?>'
                               class='button'><?= __( "Delete", 'wp-live-chat-support' ) ?></a>
                        </td>
                    </tr>
					<?php
				}
			}
			?>
            </tbody>
        </table>

		<?php
		if ( $page_links ) {
			?>
            <div class="tablenav">
                <div class="tablenav-pages" style="margin: 1em 0;float:none;text-align:center;"><?= $page_links ?>
                </div>
            </div>
			<?php
		}
		?>
    </div>
</div>