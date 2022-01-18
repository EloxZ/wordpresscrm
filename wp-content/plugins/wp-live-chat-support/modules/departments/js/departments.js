jQuery(document).ready(function(e) {
        wplc_handle_errors("#PageError");
        jQuery("#dep_form").validate({
            lang: current_locale,
            rules: departments_validation_rules
        });

});