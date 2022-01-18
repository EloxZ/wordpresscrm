var current_locale = admin_localization_data.locale;
document.addEventListener('DOMContentLoaded', ()=>{
    wplc_showLoader('windowload');
});
jQuery(document).ready(function () {

    wplc_init_heartbeat();
    wplc_init_keep_alive();
    wplc_setup_checkboxes();
    wplc_setup_agent_online_switch();

    jQuery("input[type=text],textarea,input[type=number],input[type=password]").on("mousedown",function(event){
        var selectionLength = this.value.substr(this.selectionStart, (this.selectionEnd -this.selectionStart)).length;
        if(selectionLength==this.value.length && this.value.length>0)
        {
            event.preventDefault();
            this.setSelectionRange(0,0);
        }
    })



    if (typeof TCXfa === "object") {
        if (typeof TCXfa.tcxFaInit === "function") {
            TCXfa.tcxFaInit();
        }
    }
    //if we ignore this command settings page which uses jquery-tabs can't be validated as one form and we have to 
    //create one form for each tab.Because of these command validator stops ignoring hidden fields as it does by design. 
    if (jQuery.validator != undefined) {
        jQuery.validator.setDefaults({
            ignore: [],
            showErrors: wplc_validation_complete_function
        });
    }

    jQuery("form").submit(function () {
        wplc_showLoader();
    })

    //configure support link to open in new tab.
    jQuery( "ul#adminmenu a[href='https://wordpress.org/plugins/wp-live-chat-support/#installation']" ).attr( 'target', '_blank' );
});

jQuery(window).on("load", function () {
    wplc_hideLoader('windowload');
});


function wplc_setup_checkboxes() {
    //When a form gets submitted unchecked checkbox inputs don't get submitted , so this function add a hidden input to each checkbox and set up a form submit callback in order to 
    //disable checkboxes before submission and prevent double submission of checked checkboxes!
    jQuery.each(jQuery(":checkbox.wplc_check"), function (key, checkbox) {
        var checkboxName = jQuery(checkbox).prop("name");
        var hiddenInput = jQuery("<input type=\"hidden\" class=\"fieldname\" id=\"" + checkboxName + "_post_value\"   name=\"" + checkboxName + "\" value=\"" + (jQuery(checkbox).prop("checked") ? "1" : "0") + "\" />");
        jQuery(checkbox).attr("data-original-name", checkboxName);
        jQuery(checkbox).attr("data-exclude-on-submit", "true");
        jQuery(checkbox).prop("name", checkboxName + "_view_checkbox");
        jQuery(checkbox).parent().append(hiddenInput);
    });


    jQuery(":checkbox.wplc_check").change(function () {
        cb = jQuery(this);
        jQuery("input[id='" + cb.data("original-name") + "_post_value']").val(cb.prop('checked') ? "1" : "0");
    });

    jQuery.each(jQuery("form"), function (key, form) {
        jQuery(form).on("submit", function () {
            jQuery.each(jQuery("[data-exclude-on-submit='true']"), function (key, checkbox) {
                jQuery(checkbox).prop("disabled", true);
            });
        });
    });
}

function wplc_handle_errors(error_holder_selector) {
    if (jQuery(error_holder_selector).length > 0) {
        var handleType = jQuery(error_holder_selector).data("error_handle_type");
        var error_data = jQuery(error_holder_selector).data("error_data");

        switch (handleType) {
            case "Show":
                jQuery(error_holder_selector).show();
                jQuery(error_holder_selector).append("<div class='update-nag' style='margin-top: 0px;margin-bottom: 5px;'>" + error_data.message + "<br></div>");
                break;
            case "Redirect":
                window.location.href = error_data.url;
                break;
            default:
                console.log("Page Error", error_data);
                break;
        }

    }
}

function wplc_validation_complete_function(errorMap, errorList, target = null) {
    if (target == null) {
        target = this;
    }
    target.defaultShowErrors();
    if (errorList.length > 0) {
        jQuery.each(jQuery("[data-exclude-on-submit='true']"), function (key, checkbox) {
            jQuery(checkbox).prop("disabled", false);
        });
        wplc_hideLoader();
    }
};

function wplc_init_heartbeat() {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['client'] = 'wplc_heartbeat';
    });

    jQuery(document).on('heartbeat-tick', function (e, data) {
        if (data.hasOwnProperty('online_agents') && data.online_agents !== undefined) {
            wplc_update_topbar_agent_list(data.online_agents);
        }
    });
}

function wplc_init_keep_alive(){
    setInterval(()=>{
        wplc_keep_alive_call();
    },5*60*1000)//
}

function wplc_desktop_notification() {
    if (typeof Notification !== 'undefined') {
        if (!Notification) {
            return;
        }
        if (Notification.permission !== "granted")
            Notification.requestPermission();

        var wplc_desktop_notification = new Notification(admin_localization_data.tcx_new_chat_notification_title, {
            icon: admin_localization_data.tcx_new_chat_notification_icon,
            body: admin_localization_data.tcx_new_chat_notification_text
        });
    }
}

function wplc_setup_agent_online_switch() {

    jQuery(document).ready(function () {
        var top_bar_switch_element = jQuery('#wplc_online_topbar_switch');
        top_bar_switch_element.on('click', function (e) {
            top_bar_switch_element.attr('disabled', true);
            if (top_bar_switch_element[0].checked) {
                wplc_set_agent_accepting_call(true).then(function (agents) {
                    wplc_update_agent_status(true);
                    wplc_update_topbar_agent_list(agents.Data);
                }, function (error) {
                    top_bar_switch_element.removeAttr('checked');
                })
                    .always(function () {
                        top_bar_switch_element.removeAttr('disabled');
                    });
            } else {
                wplc_set_agent_accepting_call(false).then(function (agents) {
                    wplc_update_agent_status(false);
                    wplc_update_topbar_agent_list(agents.Data);
                }, function (error) {
                    top_bar_switch_element.attr('checked', 'true');
                }).always(function () {
                    top_bar_switch_element.removeAttr('disabled');
                });
            }
        });
    });
}

function wplc_update_agent_status(isOnline) {
    var top_bar_switch_element = jQuery('#wplc_online_topbar_switch');

    if (isOnline) {
        jQuery('#wplc_ma_online_agents_circle').removeClass('wplc_red_circle')
        jQuery('#wplc_ma_online_agents_circle').addClass('wplc_green_circle')
        top_bar_switch_element.removeClass('wplc_online_topbar_switch_offline');
        top_bar_switch_element.addClass('wplc_online_topbar_switch_online');
        jQuery('#wplc_ma_online_agent_text').text(admin_localization_data.accepting_chats);
        top_bar_switch_element.attr('checked', 'true');
        jQuery("body").trigger("changeAgentOnline", isOnline);

    } else {
        jQuery('#wplc_ma_online_agents_circle').removeClass('wplc_green_circle')
        jQuery('#wplc_ma_online_agents_circle').addClass('wplc_red_circle')
        top_bar_switch_element.removeClass('wplc_online_topbar_switch_online');
        top_bar_switch_element.addClass('wplc_online_topbar_switch_offline');
        jQuery('#wplc_ma_online_agent_text').text(admin_localization_data.not_accepting_chats);
        top_bar_switch_element.removeAttr('checked');
        jQuery("body").trigger("changeAgentOnline", isOnline);
    }
}

function wplc_set_agent_accepting_call(is_online) {
    var data = {
        action: 'wplc_choose_accepting',
        security: admin_localization_data.nonce,
        is_online: is_online
    };
    return jQuery.ajax({
        url: admin_localization_data.wplc_ajaxurl,
        data: data,
        type: "POST"
    });
}

function wplc_keep_alive_call() {
    var data = {
        action: 'wplc_keep_alive',
        security: admin_localization_data.nonce
    };
    return jQuery.ajax({
        url: admin_localization_data.wplc_ajaxurl,
        data: data,
        type: "POST"
    });
}

function wplc_update_topbar_agent_list(agents) {
    jQuery("#wplc_ma_online_agents_count").text(agents.length);
    var agents_drop_down_element = jQuery('#wp-admin-bar-wplc_ma_online_agents-default');
    if (agents_drop_down_element.length > 0) {
        jQuery('#wp-admin-bar-wplc_ma_online_agents-default').empty();
    } else {
        agents_drop_down_element = jQuery("<ul id=\"wp-admin-bar-wplc_ma_online_agents-default\" class=\"ab-submenu\"></ul>");
        var drop_down_div = jQuery("<div class=\"ab-sub-wrapper\"></div>").append(agents_drop_down_element);
        jQuery("#wp-admin-bar-wplc_ma_online_agents").append(drop_down_div);
        jQuery("#wp-admin-bar-wplc_ma_online_agents").addClass("menupop");

        jQuery("#wp-admin-bar-wplc_ma_online_agents").hoverIntent({
            over: function () {
                jQuery("#wp-admin-bar-wplc_ma_online_agents").addClass("hover");
            },
            out: function () {
                jQuery("#wp-admin-bar-wplc_ma_online_agents").removeClass("hover");
            },
        });

    }

    jQuery.each(agents, function (key, agent) {
        agents_drop_down_element.append("<li id='wp-admin-bar-wplc_user_online_" + key + "'><div class='ab-item ab-empty-item'>" + agent + "</div></li>");
    })
}

function wplc_setupActiveUsersLabels() {
    jQuery(document).on('heartbeat-tick', function (e, data) {
        if (data.hasOwnProperty('online_agents') && data.online_agents !== undefined) {
            jQuery("#wplc_online_agents").html(data.online_agents.length);
        }

        if (data.hasOwnProperty('online_visitors') && data.online_visitors !== undefined) {
            jQuery("#wplc_online_visitors").html(data.online_visitors);
        }
    });
}

function wplc_showLoader(type='', wrapperId = 'wplc_container') {
    var container = jQuery("#" + wrapperId);
    if (container.length && (type =='' || (type=='windowload' &&  container.data("loaderonstart")))) {
        container.hide();
        var imgLoader = jQuery("#wplc_admin_loader");
        if (imgLoader.length) {
            imgLoader.show();
        } else {
            var img = jQuery('<img>');
            img.attr('src', admin_localization_data.wplc_baseurl + 'images/ajax-loader.gif');
            img.attr('id', 'wplc_admin_loader');
            img.css('display', "block");
            img.css('margin', "20px auto");
            img.insertBefore(container);
            /*
            if(type ==''){
                img.appendTo('.wplc_wrap');
            }
            else{
                img.appendTo('.wplc_wrap');
            }*/
        }
    }
}

function wplc_hideLoader(type='',wrapperId = 'wplc_container') {
    var container = jQuery("#" + wrapperId);
    if (container.length && (type =='' || (type=='windowload' &&  container.data("loaderonstart")))) {
        var imgLoader = jQuery("#wplc_admin_loader");
        if (imgLoader.length) {
            imgLoader.hide();
        }
        container.fadeIn();
    }

}



