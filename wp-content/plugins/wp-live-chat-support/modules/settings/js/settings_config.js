var settings_validation_rules = {
    wplc_settings_enabled: {
        required: true
    },
    wplc_settings_align: {
        required: true
    },
    wplc_settings_fill: {
        required: true
    },
    wplc_settings_font: {
        required: true
    },
    wplc_settings_base_color: {
        required: true
    },
    wplc_settings_agent_color: {
        required: true
    },
    wplc_settings_client_color: {
        required: true
    },
    wplc_require_user_info: {
        required: true
    },
    wplc_pro_na: {
        required: true
    },
    wplc_offline_finish_message: {
        required: true
    },
    wplc_chat_title: {
        required: true,
        maxlength:50
    },
    wplc_pro_fst3: {
        required: true
    },
    wplc_button_start_text: {
        required: true,
        maxlength:50
    },
    wplc_chat_intro: {
        required: true,
        maxlength:250
    },
    wplc_agent_default_name:{
        maxlength:250
    },
    wplc_rate_message:{
        maxlength:250
    },
    wplc_rate_comments_message:{
        maxlength:250
    },
    wplc_rate_feedback_request_message:{
        maxlength:250
    },
    wplc_new_chat_ringer_count:{
      required:true,
      min:0,
      max:20
    },
    wplc_text_chat_ended: {
        required: true,
        maxlength:250
    },
    wplc_close_btn_text: {
        required: true
    },
    wplc_user_welcome_chat: {
        required: true
    },
    wplc_welcome_msg: {
        required: true,
        maxlength:350
    },
    wplc_ringtone: {
        required: true
    },
    wplc_messagetone: {
        required: true
    },
    wplc_animation: {
        required: true
        /*
         function() {
                    var result = false;
                    jQuery.each(jQuery(".wplc_animation_rb"), function (index, element) {
                        if (jQuery(element).is(':checked')) {
                            result = true;
                            return false; //this breaks the loop
                        }
                    });
                    return result;
                }
         */
    },

    wplc_social_fb:{
        checkFacebookUrl:true
    },
    wplc_social_tw:{
        checkTwitterUrl:true
    },
    wplc_theme: {
        required: true
    },
    wplc_user_no_answer: {
        required: true,
        maxlength:250
    },
    wplc_gdpr_notice_company: {
        required: true
    },
    wplc_gdpr_notice_retention_purpose: {
        required: true,
        maxlength: 80
    },
    wplc_default_department: {
        required: true
    },
    wplc_pro_chat_email_address: {
        required: true,
        checkEmailList:true
    },
    wplc_send_transcripts_to: {
        required: true
    },
    wplc_et_email_header: {
        required: true
    },
    wplc_et_email_footer: {
        required: true
    },
    wplc_et_email_body: {
        required: true
    },

    wplc_gdpr_notice_text:{
        maxlength: 1000
    },
    wplc_bh_schedule: {
        checkTimeOverlaps: true,
        normalizer: function (value) {
            if(jQuery("#wplc_bh_enable").is(':checked')) {
                return bh_schedules;
            }else
            {
                return [];
            }
        }
    },
    wplc_pro_auto_first_response_chat_msg:{
        maxlength: 250
    }

}



function wplc_addCheckEmailListValidation() {
    jQuery.validator.addMethod('checkEmailList', function (value, element, params) {
        var result = true;
        var emails = [value];
        if(value.indexOf(';')>=0)
        {
            emails = value.split(';');
        }else if (value.indexOf(',')>=0)
        {
            emails = value.split(',');
        }

        emails.forEach(function(email){
            if (!/(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/.test(email))
            {
                result =false;
            }
        })
        return result;
    },'Invalid email addresses.');
}


function wplc_dateRangeOverlaps(first_start, first_end, second_start, second_end) {
    var result = false;
    if ( first_start < second_start ) {
        if ( first_end > second_start ) {
            result = true;
        }
    } else {
        if ( second_end > first_start ) {
            result = true;
        }
    }

    return result;
}

function wplc_addBusinessHoursValidationRules() {
    jQuery.validator.addMethod('checkTimeOverlaps', function (value, element, params) {
        var result = true;
        if (value !== null && Array.isArray(value)) {
            value.forEach(function (daySchedules, index) {
                    //javascript foreach can't break, so we have to check the current result state to avoid override its value when error found.
                    if (result && daySchedules !== null && Array.isArray(daySchedules)) {
                        for (var i = 0; i < daySchedules.length; i++) {

                            var firstStartTimeString = new Date('1970-01-01T' + daySchedules[i].from.h + ':' + daySchedules[i].from.m + ':00Z');
                            var firstEndTimeString = new Date('1970-01-01T' + daySchedules[i].to.h + ':' + daySchedules[i].to.m + ':00Z');

                             if (firstStartTimeString >= firstEndTimeString) {
                                 result = false;
                                 break;
                             }
                            for (var j = i + 1; j < daySchedules.length; j++) {

                                var secondStartTimeString = new Date('1970-01-01T' + daySchedules[j].from.h + ':' + daySchedules[j].from.m + ':00Z');
                                var secondEndTimeString = new Date('1970-01-01T' + daySchedules[j].to.h + ':' + daySchedules[j].to.m + ':00Z');

                                if (secondStartTimeString >= secondEndTimeString) {
                                    result = false;
                                    break;
                                }

                                result = !wplc_dateRangeOverlaps(firstStartTimeString, firstEndTimeString, secondStartTimeString, secondEndTimeString);
                                if (!result) {
                                    break;
                                }
                            }
                            if (!result) {
                                break;
                            }
                        }
                    }

            })
        }
        return result;
    }, "There are schedule overlaps please check your configuration");
}

function wplc_addSocialValidationRules(){
    jQuery.validator.addMethod('checkFacebookUrl', function (value, element, params) {
        var result = true;
        if (value !== null && value.length>0) {
            const facebookUrlPattern = /^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i;
            return  !!value && facebookUrlPattern.test(value);
        }
        return result;
    }, "This does not appear to be a valid FaceBook URL");

    jQuery.validator.addMethod('checkTwitterUrl', function (value, element, params) {
        var result = true;
        if (value !== null && value.length>0) {
            const twitterUrlPattern = /^(https?:\/\/)?((w{3}\.)?)twitter.com\/.*/i;
            return  !!value && twitterUrlPattern.test(value);
        }
        return result;
    }, "This does not appear to be a valid Twitter URL");
}

function wplc_addPbxUrlValidator() {
    jQuery.validator.addMethod('checkPbxValidator', function(value, element, params) {
        const urlPattern = /^(http:\/\/|https:\/\/){1}(([\-\.]?)[a-zA-Z0-9.-])+(:[0-9]{1,5})?(\/[a-zA-Z0-9-._~:\/?#@!$&*=;+%()']*)?\/callus\/#([a-zA-Z0-9.-])*$/;
        const result = urlPattern.test(value);
        return result;
    }, "Please fill the field 3CX Click2Talk URL with a valid url.");
}
