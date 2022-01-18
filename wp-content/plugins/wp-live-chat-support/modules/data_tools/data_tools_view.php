<div class="wrap wplc_wrap">
    <h3>
		<?= $page_title ?>
    </h3>
    <div id="wplc_container">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "execute_import_settings" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Import Complete", 'wp-live-chat-support' ) ?>
		<?php } ?>

		<?php if ( $selected_action->name == "prompt_import_settings" ) { ?>
            <table class='wp-list-table widefat fixed striped pages' style="margin-bottom:10px;">
                <form method="POST"
                      action="admin.php?page=wplivechat-menu-tools&wplc_action=execute_import_settings&nonce=<?= $import_nonce ?>"
                      enctype="multipart/form-data">
                    <tr>
                        <td>
                            <strong style="font-size:16px"><?= __( "Import Settings", 'wp-live-chat-support' ); ?></strong>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
							<?= __( "CSV File", 'wp-live-chat-support' ); ?>:
                        </td>
                        <td>
                            <input type="file" name="wplc_at_import_file" id="wplc_at_import_file"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <em><?= __( "Please note: Import CSV must have been exported using the Export tool", 'wp-live-chat-support' ); ?></em>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input class="button-primary" type="submit" name="wplc_at_file_import_submit"
                                   value="<?= __( "Import", 'wp-live-chat-support' ); ?>"/>
                            <strong><em>(<?= __( "This cannot be undone", 'wp-live-chat-support' ); ?>)</em></strong>
                        </td>
                    </tr>
                </form>
            </table>
		<?php } ?>
        <table class='wp-list-table widefat fixed striped pages'>
            <tr>
                <td>
					<?= __( "Chat Settings", 'wp-live-chat-support' ); ?>:
                </td>
                <td>
                    <a href="?page=wplivechat-menu-tools&wplc_action=export_settings&nonce=<?= $wplc_tools_nonce; ?>"
                       class='button-secondary' target="_blank">
						<?= __( "Export Settings", 'wp-live-chat-support' ); ?>
                    </a>
                    <a href="?page=wplivechat-menu-tools&wplc_action=prompt_import_settings&nonce=<?= $wplc_tools_nonce; ?>"
                       class='button-primary'>
						<?= __( "Import Settings", 'wp-live-chat-support' ); ?>
                    </a>
                </td>
            </tr>

            <tr>
                <td>
					<?= __( "Chat History", 'wp-live-chat-support' ); ?>:
                </td>
                <td>
                    <a href="?page=wplivechat-menu-tools&wplc_action=export_history&nonce=<?= $wplc_tools_nonce; ?>"
                       class='button-secondary' target="_blank">
						<?= __( "Export History", 'wp-live-chat-support' ); ?>
                    </a>
                </td>
            </tr>

            <tr>
                <td>
					<?= __( "Chat Ratings", 'wp-live-chat-support' ); ?>:
                </td>
                <td>
                    <a href="?page=wplivechat-menu-tools&wplc_action=export_ratings&nonce=<?= $wplc_tools_nonce; ?>"
                       class='button-secondary' target="_blank">
						<?= __( "Export Ratings", 'wp-live-chat-support' ); ?>
                    </a>
                </td>
            </tr>

            <tr>
                <td>
					<?= __( "Offline Messages", 'wp-live-chat-support' ); ?>:
                </td>
                <td>
                    <a href="?page=wplivechat-menu-tools&wplc_action=export_offline_msg&nonce=<?= $wplc_tools_nonce; ?>"
                       class='button-secondary' target="_blank">
						<?= __( "Export Offline Messages", 'wp-live-chat-support' ); ?>
                    </a>
                </td>
            </tr>
        </table>
    </div>
</div>