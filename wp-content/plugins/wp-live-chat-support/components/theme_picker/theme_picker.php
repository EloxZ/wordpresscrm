<div id="wplc_theme_picker_component">
    <div class="d-flex flex-column align-items-center">
		<?php foreach ( $themes as $theme ) { ?>
            <div class="form-group wplc_theme">
                <input type="radio" value="<?= $theme->alias ?>" name="wplc_theme" id="<?= $theme->alias ?>"
					<?= ( $wplc_settings->wplc_theme == $theme->alias ? ' checked' : '' ); ?> />
                <label class="col-form-label wplc_colorpicker_label"
                       for="<?= $theme->alias ?>"><?= $theme->name ?></label>
                <div class="wplc_pallet" data-pallet_name="<?= $theme->alias ?>">
                    <div class="wplc_pallet_color">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_base_color"
                             data-color="<?= $theme->base_color ?>"
                             style="background-color: <?= $theme->base_color ?>"></div>
                    </div>
                    <div class="wplc_pallet_color">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_button_color"
                             data-color="<?= $theme->button_color ?>"
                             style="background-color: <?= $theme->button_color ?>"></div>
                    </div>
                    <div class="wplc_pallet_color">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_agent_color"
                             data-color="<?= $theme->agent_color ?>"
                             style="background-color: <?= $theme->agent_color ?>"></div>
                    </div>
                    <div class="wplc_pallet_color">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_client_color"
                             data-color="<?= $theme->client_color ?>"
                             style="background-color: <?= $theme->client_color ?>"></div>
                    </div>
                </div>
            </div>
		<?php } ?>

        <div class="form-group wplc_theme" id="wplc_custom_theme">
            <input type="radio" value="custom" name="wplc_theme" id='CustomTheme'
				<?= ( $wplc_settings->wplc_theme == 'custom' ? ' checked' : '' ); ?> />
            <label class="col-form-label wplc_colorpicker_label"
                   for="CustomTheme"><?= __( "Customize", "wp-live-chat-support" ) ?></label>
            <div class="wplc_pallet" data-pallet_name="custom">
                <div class="wplc_pallet_color" data-color_id="base_color">
                    <input class="wplc_style_colorpicker_value" type="hidden"
                           name="wplc_settings_base_color" value="<?= $wplc_settings->wplc_settings_base_color ?>"/>
                    <div class="wplc_colorpicker_border">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_base_color"
                             data-color="<?= $wplc_settings->wplc_settings_base_color ?>"
                             id="base_color"
                             style="background-color: <?= $wplc_settings->wplc_settings_base_color ?>"></div>
                    </div>
                </div>
                <div class="wplc_pallet_color" data-color_id="buttons_color">
                    <input class="wplc_style_colorpicker_value" type="hidden"
                           name="wplc_settings_button_color" value="<?= $wplc_settings->wplc_settings_button_color ?>"/>
                    <div class="wplc_colorpicker_border">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_button_color"
                             data-color="<?= $wplc_settings->wplc_settings_button_color ?>"
                             id="buttons_color"
                             style="background-color: <?= $wplc_settings->wplc_settings_button_color ?>"></div>
                    </div>
                </div>
                <div class="wplc_pallet_color" data-color_id="agent_color">
                    <input class="wplc_style_colorpicker_value" type="hidden"
                           name="wplc_settings_agent_color" value="<?= $wplc_settings->wplc_settings_agent_color ?>"/>
                    <div class="wplc_colorpicker_border">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_agent_color"
                             data-color="<?= $wplc_settings->wplc_settings_agent_color ?>"
                             id="agent_color"
                             style="background-color: <?= $wplc_settings->wplc_settings_agent_color ?>"></div>
                    </div>
                </div>
                <div class="wplc_pallet_color" data-color_id="client_color">
                    <input class="wplc_style_colorpicker_value" type="hidden"
                           name="wplc_settings_client_color" value="<?= $wplc_settings->wplc_settings_client_color ?>"/>
                    <div class="wplc_colorpicker_border">
                        <div class="wplc_style_colorpicker wplc_default_colorpicker wplc_pallet_client_color"
                             data-color="<?= $wplc_settings->wplc_settings_client_color ?>"
                             id="client_color"
                             style="background-color: <?= $wplc_settings->wplc_settings_client_color ?>"></div>
                    </div>
                </div>
            </div>
            <div id="color_picker">
                <label id="wplc_picker_header"></label>
                <input class="wplc_style_colorpicker_input" type="color" value="#0596d4">
            </div>
        </div>
    </div>
</div>
