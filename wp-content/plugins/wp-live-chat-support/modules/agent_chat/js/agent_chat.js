var wplc_pending_refresh = null;
var my_chats = [];
var active_chat = -1;
var wplc_poll_list_run = true;
var ringer_count = 0;
var orig_title = document.getElementsByTagName("title")[0].innerHTML;


jQuery(function () {
    jQuery("#wplc_connecting_loader").show();
    jQuery("#wplc_dashboard_department_selector").on("change", function () {
        window.location = localization_data.chat_list_url + (jQuery(this).val() !== "0" ? "&wplc_department_view=" + jQuery(this).val() : "");
    });

    jQuery("body").on("click", "#wplc_admin_join_chat", function (event) {

        jQuery("#wplc_join_chat").hide();
        jQuery("#wplc_chat_messages").show();
        jQuery("#wplc_chat_actions").show();
        jQuery("body").trigger("joinChat", active_chat);
    });

    jQuery("body").on("click", "#chat_list_body:not(.chat_loading) .chat_list", function (event) {

        if (event.preventDefault) {
            event.preventDefault();
        } else {
            event.returnValue = false;
        }
        var chatID = jQuery(this).data("cid");
        if (chatID != active_chat) {
            jQuery("#chat_list_body").addClass("chat_loading");

            if (jQuery(this).data("status") === 3 && !my_chats.includes(parseInt(chatID))) {
                wplc_open_joined_chat(chatID, jQuery(this))
            } else {
                wplc_open_chat(chatID, jQuery(this));
            }
            jQuery(this).find(".wplc_message_count").hide();
        }
    });

    jQuery("body").on("changeAgentOnline", function (e, isOnline) {
        wplc_set_chat_status(isOnline);
    });

    jQuery("body").on("chatOpened", function (e, cid) {
        wplc_chat_opened(cid);
    });


    wplc_set_dismiss_migration_notice();
    wplc_set_chat_status(localization_data.agent_accepts_data);
    setInterval(wplc_update_time_elapsed, 1000 * 60);

    //generate_date_diff_string( start_time )

});

function wplc_open_chat(chatID, element) {

    jQuery(".wplc_p_cul.active_chat").removeClass("active_chat");
    element.addClass("active_chat");

    jQuery("body").trigger("openChat", chatID);
}

function wplc_chat_opened(chatID) {
    jQuery("#wplc_chat_messages").html("");
    var completedStatuses = [0, 1, 13, 14, 15, 16, 18];
    var chat_element = jQuery("#chat_list_body .chat_list[data-cid='" + chatID + "']");
    if (!my_chats.includes(chatID) && !completedStatuses.includes(chat_element.data("status"))) {
        jQuery("#wplc_chat_messages").show();
        jQuery("#wplc_chat_actions").hide();
        jQuery("#wplc_join_chat").show();
        jQuery("#wplc_admin_close_chat").hide();
        jQuery("body").trigger("attendChat", chatID);
        jQuery("#chat_list_body").removeClass("chat_loading");
    } else {
        jQuery("#wplc_join_chat").hide();
        jQuery("#wplc_chat_messages").show();

        if (completedStatuses.includes(chat_element.data("status"))) {
            jQuery("#wplc_admin_close_chat").hide();
            jQuery("#wplc_chat_actions").hide();
            jQuery("#wplc_join_chat").hide();
            jQuery("body").trigger("openCompletedChat", chatID);
        } else {
            jQuery("#wplc_chat_actions").show();
            jQuery("body").trigger("joinChat", chatID);
        }
    }
}

function wplc_open_joined_chat(chatID, element) {
    active_chat = chatID;
    jQuery(".wplc_p_cul.active_chat").removeClass("active_chat");
    element.addClass("active_chat");

    jQuery("#wplc_no_chat").hide();
    jQuery("#wplc_bh_offline").hide();
    jQuery("#wplc_agent_offline").hide();
    jQuery("#wplc_chat_joined").show();
    wplc_change_chat_status(false, chatID);
    jQuery("#chat_list_body").removeClass("chat_loading");
}


function wplc_set_dismiss_migration_notice() {
    jQuery("#wplc_migration_notice .notice-dismiss").on('click', function () {
        var data = {
            action: 'wplc_dismiss_migration_notice',
            security: localization_data.nonce
        };
        jQuery.ajax({
            url: localization_data.ajaxurl,
            data: data,
            type: "POST"
        });
    });
}

function apply_chat_list_filters() {
    jQuery(".chat_list.wplc_p_cul").each(function (index, chatElement) {
        // (active_filters.only_assigned && v_agent != localization_data.user_id)||(active_filters.hide_browsing && v_status==5)
        if (active_filters.hide_browsing) {
            if (jQuery(chatElement).data("status") == 5) {
                jQuery(chatElement).hide();
            }
        } else if (active_filters.only_assigned) {
            if (jQuery(chatElement).data("aid") != localization_data.user_id) {
                jQuery(chatElement).hide();
            }
        } else {
            if (jQuery(chatElement).is(":hidden")) {
                jQuery(chatElement).show();
            }
        }
    });
}

function wplc_set_chat_status(isOnline) {
    localization_data.agent_accepts_data = isOnline;
    if (localization_data.wplc_is_chat_page) {
        if (localization_data.wplc_not_business_hours) {
            jQuery("#chat_list_body").empty();
            jQuery("#wplc_no_chat").hide();
            jQuery("#wplc_chat_joined").hide();
            jQuery("#wplc_agent_offline").hide();
            jQuery("#wplc_bh_offline").show();
        } else if (isOnline) {
            if (jQuery('[id^=wplc_chat_cont]').length == 0) {
                jQuery("#wplc_no_chat").show();
                jQuery("#wplc_bh_offline").hide();
                jQuery("#wplc_agent_offline").hide();
                jQuery("#wplc_chat_joined").hide();
            }
        } else {
            jQuery("#chat_list_body").empty();
            jQuery("#wplc_bh_offline").hide();
            jQuery("#wplc_chat_joined").hide();
            jQuery("#wplc_no_chat").hide();
            jQuery("#wplc_agent_offline").show();
        }
    }

    if (isOnline && !localization_data.wplc_not_business_hours) {
        wplc_setup_chat_listing();
    } else if (localization_data.wplc_not_business_hours || !isOnline) {
        if (localization_data.channel === 'mcu') {
            jQuery("body").trigger('mcu-close-socket');
        }
        jQuery("#wplc_connecting_loader").hide();
        if (jQuery("#wplc_chat_panel").is(":hidden")) {
            jQuery("#wplc_chat_panel").show();
        }
    }
}

function wplc_setup_chat_listing() {
    if (localization_data.channel === 'mcu') {
        var pendingStatuses = [2, 6];
        jQuery("body").unbind('mcu-chat-list-add');
        jQuery("body").unbind('mcu-chat-list-update');
        jQuery("body").unbind('mcu-chat-list-remove');
        jQuery("body").on('mcu-chat-list-add', function (e, data) {
            var chatData = {};
            chatData[data.chatID] = {
                agent_id: data.agentID,
                name: data.name,
                email: data.email,
                browser: data.browser,
                state: 0,
                status: data.status,
                id: data.chatID,
                session: data.sessionID,
                country: data.country,
                timestamp: data.createdAt,
                department_id: data.departmentID,
                hash: md5(data.name + data.email + data.status + data.chatID + data.sessionID)
            };
            if (localization_data.wplc_is_chat_page) {
                wplc_add_chat_list_item(chatData);
            }
            if (pendingStatuses.includes(data.status)) {
                wplc_notify_agent();
            }
        });
        jQuery("body").on('mcu-chat-list-update', function (e, data) {
            var chatData = {};
            chatData[data.chatID] = {
                agent_id: data.agentID,
                name: data.name,
                email: data.email,
                browser: data.browser,
                state: 0,
                status: data.status,
                id: data.chatID,
                session: data.sessionID,
                country: data.country,
                timestamp: data.createdAt,
                department_id: data.departmentID,
                hash: md5(data.name + data.email + data.status + data.chatID + data.sessionID)
            };
            if (localization_data.wplc_is_chat_page) {
                wplc_update_chat_list_item(chatData);
            }
            if (pendingStatuses.includes(data.status)) {
                wplc_notify_agent();
            }
        });
        jQuery("body").on('mcu-chat-list-remove', function (e, data) {
            var chatData = {};
            chatData[data.chatID] = data.chatID;
            if (localization_data.wplc_is_chat_page) {
                wplc_remove_chat_list_item(chatData);
            }
        });
        jQuery("body").trigger('mcu-setup-socket');
    }
}

function wplc_update_time_elapsed() {
    jQuery(".time_elapsed_label").each(function (index, chat) {
        var start_time = jQuery(chat).data("start");
        jQuery(chat).html(wplc_generate_date_diff_string(start_time));
    });
}

function wplc_notify_agent() {

    var limit = 4; //Default
    if (typeof localization_data.ringer_count != "undefined") {
        limit = parseInt(localization_data.ringer_count.value);
    }

    if (typeof localization_data.ringtone !== 'undefined') {

        if (wplc_pending_refresh === null) {
            wplc_pending_refresh = setInterval(function () {
                if (!!localization_data.enable_new_visitor_ring) {
                    new Audio(localization_data.ringtone).play();
                }
                ringer_count++
                if (ringer_count == 1) {
                    wplc_desktop_notification();

                }
                if (ringer_count >= limit) {

                    clearInterval(wplc_pending_refresh);
                    wplc_pending_refresh = null;
                    ringer_count = 0;
                    document.title = "** CHAT REQUEST **";
                    setTimeout(function () {
                        document.title = orig_title;
                    }, 4000);
                }
            }, 3000);
        }
    }

}

function wplc_display_error(error, dismiss) {
    if (window.console) {
        console.log(error);
    }
    var network_issue_element = jQuery(".wplc_network_issue");
    network_issue_element.html("<span>" + error + "</span>");
    network_issue_element.fadeIn();
    if (dismiss) {
        setTimeout(function () {
            network_issue_element.fadeOut();
        }, 5000);
    }
}

function wplc_is_chat_active(status) {
    var result = false;
    var activeStatuses = [2, 3, 6];
    if (activeStatuses.indexOf(parseInt(status)) >= 0) {
        result = true;
    }
    return result;
}

function wplc_get_chat_status_element(cid, status, state) {

    let result;
    switch (state) {
        case 0:
            switch (status) {
                case 0:
                case 1:
                case 13:
                case 14:
                case 15:
                case 16:
                case 4:
                case 8:
                case 9:
                case 12:
                case 18:
                    result = "<span class='status_dot ended' > </span>";
                    break;
                case 2:
                case 6:
                    result = "<span class='status_dot pending' ></span>";
                    break;
                case 3:
                    if (my_chats.includes(parseInt(cid))) {
                        result = "<span class='status_dot chatting' ></span>";
                    } else {
                        result = "<span  class='status_dot active'></span>";
                    }
                    break;

                case 5:
                    result = "<span  class='status_dot default'></span>";
                    break;
                default:
                    result = "";
                    break;
            }
            break;
        case 1:
            result = "<span  class='status_dot minimized'></span><span> Minimized </span>";
            break;
        default:
            result = "";
            break;

    }
    return result;
}

function wplc_create_chat_list_element(chat, addContainer) {

    var v_agent = chat['agent_id'];

    var v_name = chat['name'];
    var v_email = chat['email'];
    var v_country = '';
    var v_country_image = '';

    if (typeof chat['country'] != 'undefined' && chat['country'] !== null && typeof chat['country']['name'] !== 'undefined' && chat['country']['image'] !== 'undefined') {
        v_country = chat['country']['name'];
        v_country_image = chat['country']['image'];
    }

    var v_status = chat['status'];
    var v_time = wplc_generate_date_diff_string(chat['timestamp']);
    var v_start = chat['timestamp'];
    var v_available_for_chat = wplc_is_chat_active(parseInt(v_status));


    var v_department = '';
    if (chat['department_id'] !== undefined) {
        var department = localization_data.wplc_departments.find(function (dep) {
            return parseInt(dep.id) === chat['department_id'];
        });

        v_department = department !== undefined ?
            "<span class='badge-pill badge-primary chat_visitor_department'>" + department.name + "</span>" : '';
    }

    var avatarName = wplc_isDoubleByte(v_name) ? 'Visitor' : v_name;
    var gravatarSource = "//www.gravatar.com/avatar/" + md5(v_email) + "?s=64&d=" + encodeURIComponent(localization_data.wplc_protocol + "://ui-avatars.com/api/" + avatarName + "/64/" + wplc_stringToColor(v_name) + "/fff")
    var v_country_image_html = "";
    if (v_country_image !== '') {
        v_country_image_html = "<span class='flag-tag'> <img src='" + v_country_image + "' alt='" + v_country + "' title='" + v_country + "' /> </span>";
    }
    var new_chat_badge = parseInt(v_status) === 2 ? '<div class="wplc_new_chat_badge" ><span class="badge badge-pill badge-danger wplc-badge-new">New</span></div>' : '';

    var test_list_html = addContainer ? '<div id="wplc_chat_cont' + chat['id'] + '" data-browser="' + chat['browser'] + '" data-hash="' + chat['hash'] + '" >' : '';
    test_list_html += '<div class="chat_list wplc_p_cul ' + ' ' + (active_chat == chat['id'] ? 'active_chat' : '') + '" id="wplc_p_ul_' + chat['id'] + '" data-cid="' + chat['id'] + '" data-sid="' + chat['session'] + '" data-enable="' + v_available_for_chat + '" data-aid="' + v_agent + '" data-status ="' + v_status + '">';
    test_list_html += '  <div class="chat_img">';
    test_list_html += wplc_get_chat_status_element(chat['id'], parseInt(v_status), parseInt(chat['state']));
    test_list_html += '     <img src="' + gravatarSource + '" alt="sunil">';
    test_list_html += '  </div>';

    test_list_html += '      <div class="chat_ib">';
    test_list_html += '         <div class="chat_visitor_info">';
    test_list_html += '             <div class="chat_visitor_info_first_line">';
    test_list_html += '                 <h5 class="chat_visitor_name">' + v_country_image_html + v_name + ' </h5>';
    test_list_html += v_department;
    test_list_html += '             </div>';
    test_list_html += '         </div>';
    test_list_html += '         <div class="chat_right_info">';
    test_list_html += '              <div class="chat_right_info_main">';
    test_list_html += new_chat_badge;
    test_list_html += '             <div class="wplc_message_count"><span class="badge badge-danger" ></span></div>';
    test_list_html += '             <span data-start="' + v_start + '" class="time_elapsed_label chat_date">' + v_time + '</span>';
    test_list_html += '         </div>';
    test_list_html += '         <div id="wplc_guest_typing" style="display: none;">typing...</div>';
    test_list_html += '      </div>';
    test_list_html += '    </div>';
    test_list_html += '</div>';

    return test_list_html;
}

function wplc_notify_mcu_status_changes(chat) {
    var statusToNotify = [0, 16, 14, 13];
    if (localization_data.channel === 'mcu'
        && typeof chat.session !== 'undefined'
        && statusToNotify.indexOf(parseInt(chat.status)) >= 0) {
        jQuery("body").trigger('mcu-chat-ended', chat.session);
    }
}

function wplc_update_chat_list_item(items) {
    Object.keys(items).forEach(function (chatID) {
        var current_id = parseInt(chatID);
        if (items[current_id].agent_id === localization_data.user_id && !my_chats.includes(current_id)) {
            my_chats.push(current_id);
        }
        ;
        var chatContainer = jQuery("#wplc_chat_cont" + current_id);
        if (items[current_id].hash !== chatContainer.data("hash")) {
            var wplc_v_html = wplc_create_chat_list_element(items[current_id], chatContainer.length === 0);
            wplc_notify_mcu_status_changes(items[current_id]);
            var endedStatuses = [0, 1, 13, 14, 15, 16, 4, 8, 9, 12, 3, 18];
            if (endedStatuses.includes(items[current_id].status) && current_id == active_chat && !my_chats.includes(current_id)) {
                wplc_change_chat_status(false, current_id);
            }
            if (chatContainer.length > 0) {
                jQuery("#wplc_p_ul_" + current_id).remove();
                chatContainer.append(wplc_v_html);
            } else {
                jQuery("#chat_list_body").append(wplc_v_html);
            }
        }
        jQuery(".chat_list.wplc_p_cul[data-sid='" + items[current_id].session + "'] .wplc_message_count").hide();

    });
}

function wplc_add_chat_list_item(items) {
    Object.keys(items).forEach(function (chatID) {
        var current_id = parseInt(chatID);
        if (items[current_id].agent_id === localization_data.user_id && !my_chats.includes(current_id)) {
            my_chats.push(current_id);
        }
        ;

        var chatContainer = jQuery("#wplc_chat_cont" + current_id);
        var wplc_v_html = wplc_create_chat_list_element(items[current_id], chatContainer.length === 0);
        if (chatContainer.length > 0) {
            chatContainer.append(wplc_v_html);
        } else {
            jQuery("#chat_list_body").append(wplc_v_html);
        }

        if (active_chat == current_id) {
            var chat_element = jQuery("#chat_list_body .chat_list[data-cid='" + active_chat + "']");
            wplc_open_chat(active_chat, chat_element);
        }
    });
}

function wplc_remove_chat_list_item(items) {
    Object.keys(items).forEach(function (chatID) {
        var current_id = parseInt(chatID);
        jQuery("#wplc_chat_cont" + current_id).fadeOut(2000).delay(2000).remove();
        jQuery("body").trigger("wplc-chat-removed", current_id);
    });

}

function wplc_generate_date_diff_string(start_time) {
    start_time = start_time.indexOf('+') < 0 && start_time.indexOf('Z') < 0 ? start_time + ' +0000' : start_time;
    var date_start = new Date(start_time);
    var now = new Date();
    var cur_time = now.getTime();
    var time_start = date_start.getTime();

    var time_elapsed = (cur_time - time_start) / 1000;
    var seconds = time_elapsed;
    var minutes = Math.round(time_elapsed / 60);
    var hours = Math.round(time_elapsed / 3600);
    var days = Math.round(time_elapsed / 86400);
    var weeks = Math.round(time_elapsed / 604800);
    var months = Math.round(time_elapsed / 2600640);
    var years = Math.round(time_elapsed / 31207680);
    // Seconds
    if (seconds <= 60) {
        return "0 min";
    } //Minutes
    else if (minutes <= 60) {
        if (minutes == 1) {
            return "1 min";
        } else {
            return minutes + " min";
        }
    } //Hours
    else if (hours <= 24) {
        if (hours == 1) {
            return "1 hr";
        } else {
            return hours + " hrs";
        }
    } //Days
    else if (days <= 7) {
        if (days == 1) {
            return "1 day";
        } else {
            return days + " days";
        }
    } //Weeks
    else if (weeks <= 4.3) {
        if (weeks == 1) {
            return "1 week";
        } else {
            return weeks + " weeks";
        }
    } //Months
    else if (months <= 12) {
        if (months == 1) {
            return "1 month";
        } else {
            return months + " months";
        }
    } //Years
    else {
        if (years == 1) {
            return "1 year";
        } else {
            return years + " years";
        }
    }
}

function wplc_get_browser_image_name(browser_image, browser) {
    if (typeof browser_image === 'undefined') {
        switch (browser) {
            case 'Chrome':
                browser_image = "chrome_16x16.png";
                break;
            case 'Edge':
                browser_image = "internet-explorer_16x16.png";
                break;
            case 'Firefox':
                browser_image = "firefox_16x16.png";
                break;
            case 'Opera':
                browser_image = "opera_16x16.png";
                break;
            default:
                browser_image = "web_16x16.png";
                break;
        }

    }
    return browser_image;
}
