jQuery(document).ready(function () {

    jQuery("#wplc_theme_picker_component").trigger("theme_picker-loaded");

    jQuery('input[type=radio][name=wplc_theme]').change(function () {
        onThemeSelection(jQuery(this));
    });

    jQuery(".wplc_style_colorpicker_input").focusout(function () {
        jQuery("#color_picker").hide();
    });

    jQuery('#wplc_custom_theme .wplc_style_colorpicker').click(function () {
        if (jQuery('input[type=radio][name=wplc_theme]:checked').val() === 'custom') {
            //set the selected color on color picker based
            var selected_color = jQuery(this).data("color");
            jQuery(".wplc_style_colorpicker_input").val(selected_color);

            jQuery("#color_picker").data("color_id", this.id);
            jQuery("#color_picker").show();
            set_selected_colorpicker(this.id);
            setTimeout(function () {
                //if not in timeout click event get executed before the .show() completion and picker appears in wrong position
                //because it's position is relative to  #color_picker element
                jQuery(".wplc_style_colorpicker_input").click();
            }, 50);
        }
    });

    jQuery('.wplc_style_colorpicker_input').change(function () {
        jQuery("#color_picker").hide();
    });

    jQuery('.wplc_style_colorpicker_input').on("input", function () {
        var input_id = jQuery("#color_picker").data("color_id");
        jQuery("#" + input_id).css("background-color", jQuery(this).val());
        jQuery("#" + input_id).data("color", jQuery(this).val());
        jQuery(".wplc_pallet_color[data-color_id=" + input_id + "] input.wplc_style_colorpicker_value").val(jQuery(this).val());
        jQuery('#wplc_theme_picker_component').trigger('theme_picker-color-input', {
            selectedTheme: 'custom',
            color: jQuery(this).val(),
            changedColor:input_id
        });
    });

    jQuery(".wplc_theme").on("click",function(e){
        if (!jQuery(e.target).is('input[type=radio][name=wplc_theme]')) {
            const themeRadioElement = jQuery(this).find('input[type=radio][name=wplc_theme]');
            if (themeRadioElement && !themeRadioElement.is(':checked')) {
                themeRadioElement.click();
                onThemeSelection(themeRadioElement);
                if (jQuery(e.target).is("#wplc_custom_theme .wplc_style_colorpicker")) {
                    jQuery(e.target).click();
                }
            }
        }
    });
});

function onThemeSelection(selectedTheme){
    var selection = selectedTheme.val();

    var themeBaseColorPicker = jQuery(".wplc_pallet[data-pallet_name="+selection+"] .wplc_pallet_color .wplc_pallet_base_color");
    var baseColor = '#000000';
    if(themeBaseColorPicker!==undefined)
    {
        baseColor = themeBaseColorPicker.data("color");
    }

    jQuery('#wplc_theme_picker_component').trigger('theme_picker-color-input', {
        selectedTheme: selection,
        color: baseColor,
        changedColor:'base_color'
    });
    if (selection === 'custom') {
        set_selected_colorpicker("base_color");
    } else {
        set_selected_colorpicker("none");
        jQuery("#wplc_picker_header").html('');
    }
}

function mapPickerAlias(pickerAlias) {
    var result = '';
    switch (pickerAlias) {
        case 'base_color':
            result = 'Base';
            break;
        case 'buttons_color':
            result = 'Buttons';
            break;
        case 'client_color':
            result = 'Visitor Chat';
            break;
        case 'agent_color':
            result = 'Agent Chat';
            break;
        default:
            break;
    }
    return result;
}

function mapPickerHeaderPosition(pickerAlias) {
    var result = '';
    switch (pickerAlias) {
        case 'base_color':
            result = '0px';
            break;
        case 'buttons_color':
            result = '34px';
            break;
        case 'agent_color':
            result = '65px';
            break;
        case 'client_color':
            result = '106px';
            break;
        default:
            break;
    }
    return result;
}

function set_selected_colorpicker(pickerId) {
    var picker_header = mapPickerAlias(pickerId);
    var picker_header_left_shift = mapPickerHeaderPosition(pickerId);
    jQuery("#wplc_picker_header").html(picker_header);
    jQuery("#wplc_picker_header").css("left",picker_header_left_shift);

    var colorpickerBorders = jQuery(".wplc_pallet[data-pallet_name='custom'] .wplc_pallet_color .wplc_colorpicker_border");
    colorpickerBorders.removeClass("wplc_selected_border");
    colorpickerBorders.addClass("wplc_no_border");
    var selectedColorpickerBorder = jQuery(".wplc_pallet[data-pallet_name='custom'] .wplc_pallet_color[data-color_id='" + pickerId + "'] .wplc_colorpicker_border");
    selectedColorpickerBorder.removeClass("wplc_no_border");
    selectedColorpickerBorder.addClass("wplc_selected_border");
    var colorpickers = jQuery(".wplc_pallet[data-pallet_name='custom'] .wplc_style_colorpicker");
    colorpickers.removeClass("wplc_selected_colorpicker");
    colorpickers.addClass("wplc_default_colorpicker");
    var selectedColorpicker = jQuery(".wplc_pallet[data-pallet_name='custom'] .wplc_pallet_color[data-color_id='" + pickerId + "'] .wplc_colorpicker_border .wplc_style_colorpicker");
    selectedColorpicker.addClass("wplc_selected_colorpicker");
    selectedColorpicker.removeClass("wplc_default_colorpicker");
}