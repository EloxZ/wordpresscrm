<h3><?php _e("Privacy", 'wp-live-chat-support') ?></h3>
<table class="wp-list-table wplc_list_table widefat fixed striped pages">
    <tbody>
      <tr>
        <td width="300" valign="top">
          <label for="wplc_gdpr_enabled"><?=__("Enable privacy controls", 'wp-live-chat-support'); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?=__('Disabling will disable all GDPR related options, this is not advised.', 'wp-live-chat-support'); ?>"></i></label>
        </td>
        <td>
          <input type="checkbox" class="wplc_check" name="wplc_gdpr_enabled" id="wplc_gdpr_enabled_checkbox" value="1" <?= (isset($wplc_settings->wplc_gdpr_enabled) && $wplc_settings->wplc_gdpr_enabled == '1' ? 'checked' : ''); ?>>
          <a href="https://gdpr.eu/" target="_blank"><?=__("Importance of GDPR Compliance", 'wp-live-chat-support'); ?></a>
        </td>
      </tr>

      <tr>
        <td width="300" valign="top">
            <label for="wplc_gdpr_notice_company"><?=__("Organization name", 'wp-live-chat-support'); ?> <span style="font-size: 10px">(%%COMPANY%%)</span></label>
        </td>
        <td>
          <input type="text" name="wplc_gdpr_notice_company" id="wplc_gdpr_notice_company" value="<?= (isset($wplc_settings->wplc_gdpr_notice_company) ? esc_attr(stripslashes($wplc_settings->wplc_gdpr_notice_company)) : get_bloginfo('name')); ?>">
        </td>
      </tr>

      <tr>
        <td width="300" valign="top">
            <label for="wplc_gdpr_notice_retention_purpose"><?=__("Data retention purpose", 'wp-live-chat-support'); ?> <span style="font-size: 10px">(%%PURPOSE%%)</span></label>
        </td>
        <td>
          <input maxlength="80" type="text" name="wplc_gdpr_notice_retention_purpose" id="wplc_gdpr_notice_retention_purpose" value="<?= (isset($wplc_settings->wplc_gdpr_notice_retention_purpose) ? esc_attr($wplc_settings->wplc_gdpr_notice_retention_purpose) : __('Chat/Support', 'wp-live-chat-support')); ?>">
        </td>
      </tr>

      <tr>
        <td width="300" valign="top">
            <label for="wplc_gdpr_notice_retention_period"><?=__("Data retention period", 'wp-live-chat-support'); ?> <span style="font-size: 10px">(%%PERIOD%%)</span></label>
        </td>
        <td>
          <input type="number" class="wplc-input-number" name="wplc_gdpr_notice_retention_period" id="wplc_gdpr_notice_retention_period" min="1" max="730" step="1" value="<?= (isset($wplc_settings->wplc_gdpr_notice_retention_period) ? intval($wplc_settings->wplc_gdpr_notice_retention_period) : 30); ?>"> <?=__('days', 'wp-live-chat-support'); ?>
        </td>
      </tr>

      <tr>
        <td width="300" valign="top">
          <label><?=__("GDPR notice to visitors", 'wp-live-chat-support'); ?>
            <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?=__('Users will be asked to accept the notice shown here, in the form of a check box.', 'wp-live-chat-support'); ?>"></i>
          </label>
        </td>
        <td>
            <textarea cols="45" rows="5" maxlength="1000" name="wplc_gdpr_notice_text" id="wplc_gdpr_notice_text"><?= wplc_gdpr_generate_retention_agreement_notice($wplc_settings,false);?></textarea>
        </td>
      </tr>
    </tbody>
  </table>
