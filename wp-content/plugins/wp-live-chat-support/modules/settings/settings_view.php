<div class='wrap wplc_wrap' style="display:none;">
    <h2><?= __( "Settings", 'wp-live-chat-support' ) ?></h2>
    <div id="wplc_container" data-loaderOnStart="true">
		<?php if ( is_object( $error ) && $error->ErrorFound ) { ?>
            <div style="display:none;"
                 id="PageError"
                 data-error_handle_type="<?= $error->ErrorHandleType ?>"
                 data-error_data="<?= esc_html( json_encode( $error->ErrorData ) ) ?>"
            >
            </div>
		<?php }
		if ( $show_config_warning ) { ?>
            <div class='error'>
                <p><?= $function_time_limit_missing ? __( "WPLC: set_time_limit() is not enabled on this server. You may experience issues while using 3CX Live Chat as a result of this. Please get in contact your host to get this function enabled.", 'wp-live-chat-support' ) : "" ?></p>
                <p><?= $config_safe_mode_enabled ? __( "WPLC: Safe mode is enabled on this server. You may experience issues while using 3CX Live Chat as a result of this. Please contact your host to get safe mode disabled.", 'wp-live-chat-support' ) : "" ?></p>
            </div>
		<?php } ?>

		<?php if ( $selected_action->name == "save_settings" && isset( $error ) && ! $error->ErrorFound ) { ?>
            <div class='update-nag'
                 style='margin-top: 0px;margin-bottom: 5px;border-color:#67d552;'><?= __( "Settings saved succesfully", 'wp-live-chat-support' ) ?>
                <br></div>
		<?php } ?>

        <form id="sets_form" class='ignoreBaseLoader wplc_settings_form' method='POST' action="<?= $save_action_url ?>"
              novalidate="novalidate">
            <div id="wplc_tabs" class="wplc_tabs">
                <ul>
					<?php foreach ( $tabs as $tab ) { ?>
                        <li><a href="#<?= $tab->id ?>"><i class="<?= $tab->icon ?>"></i> <?= $tab->label ?></a></li>
					<?php } ?>
                </ul>
				<?php foreach ( $tabs as $tab ) { ?>
                    <div id="<?= $tab->id ?>">
						<?php include_once( plugin_dir_path( __FILE__ ) . $tab->view ); ?>
                    </div>
				<?php } ?>
            </div>
            <p class='submit'><input type='submit' name='wplc_save_settings' class='button-primary'
                                     value='<?= __( "Save Settings", 'wp-live-chat-support' ) ?>'/></p>
        </form>
    </div>
</div>