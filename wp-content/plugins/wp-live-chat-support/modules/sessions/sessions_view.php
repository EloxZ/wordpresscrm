<div class="wrap wplc_wrap"><h2> <?= $page_title ?></h2>
    <div id="wplc_container">
		<?php if ( $selected_action->name == "prompt_remove_session" && is_numeric( $cid ) ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Are you sure you would like to delete this chat?", 'wp-live-chat-support' ) ?>
                <br>
                <a class='button'
                   href='?page=wplivechat-menu-session&wplc_action=execute_remove_session&cid=<?= sanitize_text_field( $cid ) ?>&wplc_confirm=1&nonce=<?= $delete_session_nonce ?>'><?= __( "Yes", 'wp-live-chat-support' ) ?></a>
                <a class='button' href='?page=wplivechat-menu-session'><?= __( "No", 'wp-live-chat-support' ) ?></a>
            </div>
		<?php } else if ( $selected_action->name == "prompt_remove_session" ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete chat", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "prompt_truncate_session" ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Caution: It's an irreversible action! Are you sure you would like to delete full session?", 'wp-live-chat-support' ) ?>
                <br>
                <a class='button'
                   href='?page=wplivechat-menu-session&wplc_action=execute_truncate_session&wplc_confirm=1&nonce=<?= $truncate_session_nonce ?>'><?= __( "Yes", 'wp-live-chat-support' ) ?></a>
                <a class='button' href='?page=wplivechat-menu-session'><?= __( "No", 'wp-live-chat-support' ) ?></a>
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_session" && ! ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;'><?= __( "Error: Could not delete chat", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_remove_session" && ! ! $delete_success ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Chat Deleted", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <div class="bootstrap-wplc-content">

            <form action="?page=wplivechat-menu-session" method="get">
                <input type="hidden" name="wplc_action" value="search_history"/>
                <input type="hidden" name="nonce" value="<?= $search_nonce ?>"/>
                <input type="hidden" name="page" value="wplivechat-menu-session"/>

                <div id="accordionFilters">
                    <div class="col-md-12">
                       <div id="wplc_filters" style="">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="wplc_email_filter">Email address</label>
                                            <input type="email" class="form-control" id="wplc_email_filter"
                                                   name="wplc_email_filter">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="wplc_status_filter">Status</label>
                                            <select class="form-control" id="wplc_status_filter"
                                                    name="wplc_status_filter">
												<?php foreach ( $statuses as $key => $status ) { ?>
                                                    <option value="<?= $status ?>"><?= $key ?></option>
												<?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 wplc_filter_buttons">

                                            <button type="submit" class="button">Search</button>
                                            <a class="button" href="?page=wplivechat-menu-session&wplc_action=prompt_truncate_session<?= ( $current_page != null ? "&pagenum=" . $current_page : "" ) ?>"><?= __( "Delete all", 'wp-live-chat-support' ) ?></a>

                                    </div>
                                </div>
                        </div>
                    </div>
                </div>

            </form>


        </div>

        <table class="wp-list-table wplc_list_table widefat fixed " cellspacing="0">
            <thead>
            <tr>
                <th class='manage-column column-id'><span><?= __( "Date", 'wp-live-chat-support' ) ?></span></th>
                <th scope='col' id='wplc_name_column' class='manage-column column-id'>
                    <span><?= __( "Name", 'wp-live-chat-support' ) ?> </span></th>
                <th scope='col' id='wplc_email_column'
                    class='manage-column column-id'><?= __( "Email", 'wp-live-chat-support' ) ?> </th>
                <th scope='col' id='wplc_url_column' class='manage-column column-url'
                    style=""><?= __( "URL", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_status_column' class='manage-column column-status'
                    style=""><?= __( "Status", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_rate_column' class='manage-column column-rate'
                    style=""><?= __( "Rate", 'wp-live-chat-support' ) ?></th>
                <th scope='col' id='wplc_action_column' class='manage-column column-action sortable desc' style="">
                    <span><? __( "Action", 'wp-live-chat-support' ) ?></span></th>

            </tr>
            </thead>
            <tbody id="the-list" class='list:wp_list_text_link'>
			<?php
			if ( ! $chats ) {
				?>
                <tr>
                    <td></td>
                    <td><?php __( "No chats available at the moment.", 'wp-live-chat-support' ) ?></td>
                </tr>
				<?php
			} else {
				foreach ( $chats as $result ) {
					?>
                    <tr id="record_<?= intval( $result->id ) ?>" style="height:30px;" \>
                        <td class='chat_id column-chat_d'><?= sanitize_text_field( $result->timestamp ) ?></td>
                        <td class='chat_name column_chat_name' id='chat_name_<?= intval( $result->id ) ?>'><img
                                    src="//www.gravatar.com/avatar/<?= md5( $result->email ) ?>?s=30&d=<?=( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https' : 'http'?>://ui-avatars.com/api//<?=$result->avatar_name_alias?>/32/<?=TCXUtilsHelper::wplc_color_by_string($result->name)?>/fff"
                                    class='wplc-user-message-avatar'
                                    align="absmiddle"/> <?= sanitize_text_field( $result->name ) ?></td>
                        <td class='chat_email column_chat_email' id='chat_email_<?= intval( $result->id ) ?>'><a
                                    href='mailto:<?= sanitize_text_field( $result->email ) ?>'
                                    title='Email."<?= $result->email ?>."'><?= sanitize_text_field( $result->email ) ?></a>
                        </td>
                        <td class='chat_url column_chat_url'
                            id='chat_url_<?= intval( $result->id ) ?>'><?= esc_url( $result->url ) ?></td>
                        <td class='chat_status column_chat_status' id='chat_status_<?= intval( $result->id ) ?>'>
                            <strong><?= $result->getStatusName() ?></strong></td>
                        <td class='chat_rate column_chat_rate' id='chat_rate_<?= intval( $result->id ) ?>'>
                                <div class="history_chat_rate"><?= $result->getRatingHtml() ?></div>
                        </td>
                        <td class='chat_action column_chat_action'>
                            <a href='<?= $result->getSessionHistoryUrl() ?>' class='button'
                               title='<?= __( 'View Chat Session', 'wp-live-chat-support' ) ?>' target='_BLANK'
                               id=''><?= __( 'View', 'wp-live-chat-support' ) ?></a>
                            <a href='<?= $result->getDownloadSessionHistoryUrl() ?>' class='button'
                               title='<?= __( 'Download Chat Session', 'wp-live-chat-support' ) ?>' target='_BLANK'
                               id=''><?= __( 'Download', 'wp-live-chat-support' ) ?></a>
                            <a href='<?= $result->getRemoveSessionUrl( 'wplivechat-menu-session', $current_page ) ?>'
                               class='button'><?= __( 'Delete', 'wp-live-chat-support' ) ?></a>
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