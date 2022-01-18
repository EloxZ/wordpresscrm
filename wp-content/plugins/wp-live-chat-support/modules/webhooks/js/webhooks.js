jQuery(document).ready(function(e) {

    wplc_handle_errors("#PageError");

    jQuery("#wh_form").validate({
        lang: current_locale,
        rules: webhooks_validation_rules
    });

});