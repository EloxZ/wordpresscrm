jQuery(document).ready(function(e) {

    wplc_handle_errors("#PageError");
    jQuery("#cf_form").validate({
        lang: current_locale,
        rules: custom_fields_validation_rules
    });
    jQuery("body").on("change", "#wplc_field_type", set_view_by_field_type);
    jQuery("#wplc_field_type").trigger("change");



});

function set_view_by_field_type() {
    var selection = jQuery(this).val();

    if (selection == '1') {
        jQuery("#wplc_field_value_dropdown_row").show();
    } else {
        jQuery("#wplc_field_value_dropdown_row").hide();
    }
    addValuesValidationRules(selection);
}

function addValuesValidationRules(selectedType) {
     if(selectedType == '1')
     {
         jQuery("#wplc_drop_down_values").rules("add", {
             required: true
         });
     }
     else
     {
         jQuery("#wplc_drop_down_values").rules("remove" );
     }
}
