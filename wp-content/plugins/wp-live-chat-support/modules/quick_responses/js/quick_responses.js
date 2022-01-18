jQuery(document).ready(function(e) {

    wplc_handle_errors("#PageError");

    jQuery("#qr_form").validate({
        lang: current_locale,
        rules: quick_responses_validation_rules
    });

});