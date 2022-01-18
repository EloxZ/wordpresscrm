<div class="wrap wplc_wrap"><h2> <?= $page_title ?></h2>
    <div id="wplc_container">
		<?php if ( $selected_action->name == "prompt_remove_offline_message" && is_numeric( $omid ) ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Are you sure you would like to delete this message?", 'wp-live-chat-support' ) ?>
                <br>
                <a class='button'
                   href='?page=wplivechat-menu-offline-messages&wplc_action=execute_remove_offline_message&omid=<?= sanitize_text_field( $omid ) ?>&nonce=<?= $delete_offline_message_nonce ?>'><?= __( "Yes", 'wp-live-chat-support' ) ?></a>
                <a class='button'
                   href='?page=wplivechat-menu-offline-messages'><?= __( "No", 'wp-live-chat-support' ) ?></a>
            </div>
		<?php } else if ( $selected_action->name == "prompt_remove_offline_message" ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete offline message", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>


		<?php if ( $selected_action->name == "execute_remove_offline_message" && ! ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete offline message", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_offline_message" && ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Message Deleted", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <table class="wp-list-table widefat fixed " cellspacing="0">
            <thead>
            <tr>
                <th class='manage-column column-date' style='width: 15%'>
                    <span><?= __( "Date", 'wp-live-chat-support' ) ?></span></th>
                <th scope='col' id='wplc_name_colum' class='manage-column column-id' style='width: 10%'>
                    <span><?= __( "Name", 'wp-live-chat-support' ) ?></span></th>
                <th scope='col' id='wplc_email_colum' class='manage-column column-id'
                    style='width: 15%'><?= __( "Email", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_phone_colum' class='manage-column column-id'
                    style='width: 15%'><?= __( "Phone", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_message_colum' class='manage-column column-id'
                    style='width: 40%'><?= __( "Message", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_actions_colum' class='manage-column column-id'
                    style='width: 10%'><?= __( "Actions", 'wp-live-chat-support' ) ?></th>
            </tr>
            </thead>
            <tbody id="the-list" class='list:wp_list_text_link'>
			<?php
			if ( ! $chats ) {
				?>
                <tr>
                    <td></td>
                    <td><?php __( "You have not received any offline messages.", 'wp-live-chat-support' ) ?></td>
                </tr>
				<?php
			} else {
				foreach ( $chats as $result ) {
					?>
                    <tr id="record_<?= intval( $result->id ) ?>" style="height:30px;" \>

                        <td class='chat_id column-chat_d'><?= sanitize_text_field( $result->timestamp ) ?></td>
                        <td class='chat_name column_chat_name' id='chat_name_<?= intval( $result->id ) ?>'><img
                                    src="//www.gravatar.com/avatar/<?= md5( $result->email ) ?>?s=30&d=<?=( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https' : 'http'?>://ui-avatars.com/api//<?=$result->name?>/32/<?=TCXUtilsHelper::wplc_color_by_string($result->name)?>/fff"
                                    align="absmiddle"/> <?= sanitize_text_field( $result->name ) ?></td>
                        <td class='chat_email column_chat_email' id='chat_email_<?= intval( $result->id ) ?>'><a
                                    href='mailto:<?= sanitize_email( $result->email ) ?>'
                                    title='Email <?= $result->email ?>'><?= sanitize_email( $result->email ) ?></a></td>
                        <td class='chat_name column_chat_phone'
                            id='chat_url_<?= intval( $result->id ) ?>'><?= nl2br( sanitize_text_field( $result->phone ) ) ?></td>
                        <td class='chat_name column_chat_url'
                            id='chat_url_<?= intval( $result->id ) ?>'><?= nl2br( sanitize_text_field( $result->message ) ) ?></td>
                        <td class='chat_name column_chat_delete'>
                            <a href='<?= $result->getRemoveOfflineMessageUrl( $current_page ) ?>' class='button'
                               title='<?= __( 'Delete Message', 'wp-live-chat-support' ) ?>'
                               id=''><?= __( 'Delete', 'wp-live-chat-support' ) ?></a>
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