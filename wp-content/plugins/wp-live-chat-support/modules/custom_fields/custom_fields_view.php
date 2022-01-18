<div class="wrap wplc_wrap">
    <h3>
		<?= $page_title ?>
        <a href='?page=wplivechat-manage-custom-field&nonce=<?= wp_create_nonce( "edit_custom_field" ) ?>&cfid=-1'
           class='wplc_add_new_btn'><?= __( "Add New", 'wp-livechat' ) ?></a>
    </h3>
    <div id="wplc_container">
		<?php if ( $selected_action->name == "prompt_remove_custom_field" && is_numeric( $cfid ) ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Are you sure you want to delete this custom field?", 'wp-live-chat-support' ) ?>
                <br>
                <a class='button'
                   href='?page=wplivechat-menu-tools&wplc_action=execute_remove_custom_field&cfid=<?= sanitize_text_field( $cfid ) ?>&nonce=<?= $delete_custom_field_nonce ?>#wplc_custom_fields_tab'><?= __( "Yes", 'wp-live-chat-support' ) ?></a>
                <a class='button'
                   href='?page=wplivechat-menu-tools#wplc_custom_fields_tab'><?= __( "No", 'wp-live-chat-support' ) ?></a>
            </div>
		<?php } else if ( $selected_action->name == "prompt_remove_custom_field" ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete custom field", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>


		<?php if ( $selected_action->name == "execute_remove_custom_field" && ! ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete custom field", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_custom_field" && ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Custom field Deleted", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>


        <table class="wp-list-table wplc_list_table widefat fixed striped" cellspacing="0">
            <thead>
            <tr>
                <th scope='col' id='wplc_id_colum' class='manage-column column-id'>
                    <span><?= __( "ID", 'wp-live-chat-support' ) ?></span></th>
                <th scope='col' id='wplc_name_colum'
                    class='manage-column'><?= __( "Name", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_type_colum'
                    class='manage-column'><?= __( "Type", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_status_colum'
                    class='manage-column'><?= __( "Status", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_action_colum'
                    class='manage-column'><?= __( "Actions", 'wp-live-chat-support' ) ?></th>
            </tr>
            </thead>
            <tbody id="the-list" class='list:wp_list_text_link'>
			<?php
			if ( ! $fields ) {
				?>
                <tr>
                    <td colspan='6'><?php __( "Create your first custom field", 'wp-live-chat-support' ) ?></td>
                </tr>
				<?php
			} else {
				foreach ( $fields as $result ) {
					?>
                    <tr id="record_<?= intval( $result->id ) ?>" style="height:30px;" \>
                        <td class='field_id' id='field_id_<?= intval( $result->id ) ?>'><?= $result->id ?></td>
                        <td class='field_name'
                            id='field_name_<?= intval( $result->id ) ?>'><?= stripslashes( esc_html( $result->name ) ) ?></td>
                        <td class='field_type'
                            id='field_type_<?= intval( $result->id ) ?>'><?= esc_html( $result->getTypeName() ) ?></td>
                        <td class='field_status'
                            id='field_status_<?= intval( $result->id ) ?>'><?= $result->getStatusName() ?></td>
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
                <div class="tablenav-pages" style="margin: 1em 0;float:none;text-align:center;"><?= $page_links ?></div>
            </div>
			<?php
		}
		?>
    </div>
</div>