<h3><?= __( "Chat Operating Hours", 'wp-live-chat-support' ) ?></h3>
<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>
    <tr>
        <td>
            <div style="width:100%">
                <input type='checkbox' class="wplc_check" name='wplc_bh_enable' id='wplc_bh_enable'
                       value='1' <?= ( $wplc_settings->wplc_bh_enable ? ' checked' : '' ) ?> />
				<?= __( "Configure the times that chat should be available", 'wp-live-chat-support' ) ?>
            </div>
        </td>
    </tr>
    <tr id="wplc_bh_table" <?= ( ! $wplc_settings->wplc_bh_enable ? 'style="display:none"' : '' ) ?>>
        <td>
            <table class='form-table wp-list-table wplc_business_list_table striped'>
                <tr>
                    <th style="width:200px; min-width: 200px;"><?= __( "Week Day", 'wp-live-chat-support' ) ?></th>
                    <th style="width:100%;"><?= __( "Available", 'wp-live-chat-support' ) ?></th>
                    <th style="width:100px; min-width: 100px;"></th>
                </tr>
				<?php for ( $day = 0; $day < 7; $day ++ ) { ?>
                    <tr>
                        <td style="text-align:left; width:200px; min-width: 200px;">
                            <label>
								<?= ucfirst( date_i18n( 'l', gmmktime( 12, 0, 0, 1, 2 + $day, 2000 ) ) ) ?>
                            </label>
                        </td>
                        <td style="width:100%;">
                            <div id="wplc_not_available_bh_<?= $day ?>"><?= __( "Not available", 'wp-live-chat-support' ) ?></div>
                            <ol style="margin-left: 1em;" id="bh_schedules_<?= $day ?>">

                            </ol>
                        </td>
                        <td style="width:100px; min-width: 100px;">
                            <span class="bootstrap-wplc-content">
                                <button type="button" class="btn-sm btn-primary wplc_settings_button wplc_add_schedule"
                                        data-day="<?= $day ?>">
                                    Add
                                </button>
                            </span>
                        </td>
                    </tr>
				<?php } ?>
				<?php if ( $business_hours_overlap_found ) { ?>
                    <tr>
                        <td colspan="3">
                            <p class="notice notice-warning">
								<?= __( 'Time intervals are incorrect or overlapping. Please fix your settings or you might get unexpected behavior.', 'wp--live-chat-support' ) ?>
                            </p>
                        </td>
                    </tr>
				<?php } ?>

            </table>
        </td>
    </tr>
    <tr>
        <td width='300'><?= __( "Current Site Time", 'wp-live-chat-support' ) ?> <?= current_time( 'mysql' ); ?></td>
    </tr>
</table>

<input type="hidden" id="wplc_bh_schedule" name="wplc_bh_schedule"/>

<div class="bootstrap-wplc-content" id="bhModal" tabindex="-1"
     title="<?= __( "Add Chat Operating Hours Schedule", 'wp-live-chat-support' ) ?>">
    <input type="hidden" id="schedule_id" value="-1">
    <input type="hidden" id="weekday">
    <div class="form-group">
        <row>
            <label for="from-time"
                   class="col-md-2 col-form-label"><?= __( "From", 'wp-live-chat-support' ) ?>:</label>
            <select class="col-md-4" name="wplc_bh_schedule_from_hours" id='wplc_bh_schedule_from_hours'>
				<?php foreach ( $times['hours'] as $hour ) { ?>
                    <option <?= $hour == '09' ? 'selected' : '' ?>
                            value='<?= $hour ?>'><?= $hour ?></option>
				<?php } ?>
            </select>
            <select class="col-md-4" name="wplc_bh_schedule_from_minutes"
                    id='wplc_bh_schedule_from_minutes'>
				<?php foreach ( $times['minutes'] as $minute ) { ?>
                    <option <?= $minute == '00' ? 'selected' : '' ?>
                            value='<?= $minute ?>'><?= $minute ?></option>
				<?php } ?>
            </select>
        </row>
    </div>
    <div class="form-group">
        <row>
            <label for="to-time"
                   class="col-md-2 col-form-label"><?= __( "To", 'wp-live-chat-support' ) ?> :</label>

            <select class="col-md-4" name="wplc_bh_schedule_to_hours" id='wplc_bh_schedule_to_hours'>
				<?php foreach ( $times['hours'] as $hour ) { ?>
                    <option <?= $hour == '17' ? 'selected' : '' ?>
                            value='<?= $hour ?>'><?= $hour ?></option>
				<?php } ?>
            </select>
            <select class="col-md-4" name="wplc_bh_schedule_to_minutes" id='wplc_bh_schedule_to_minutes'>
				<?php foreach ( $times['minutes'] as $minute ) { ?>
                    <option <?= $minute == '00' ? 'selected' : '' ?>
                            value='<?= $minute ?>'><?= $minute ?></option>
				<?php } ?>
            </select>
        </row>
    </div>
    <div id="bhModal_buttons">
        <button type="button" class="btn btn-secondary" id="bhClose">Close</button>
        <button type="button" class="btn btn-primary wplc_settings_button" id="bhSave">Add</button>
    </div>
</div>
