jQuery(function(){
    var tgm_media_frame_default;
    var tgm_media_frame_agent_logo;
    var tgm_media_frame_logo;


    jQuery(document.body).on('click.tgmOpenMediaManager', '#wplc_btn_upload_logo', function(e){
        e.preventDefault();

        if ( tgm_media_frame_logo ) {
            tgm_media_frame_logo.open();
            return;
        }

        tgm_media_frame_logo = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Upload your Logo',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Logo'
            }
        });

        tgm_media_frame_logo.on('select', function(){
            var media_attachment = tgm_media_frame_logo.state().get('selection').first().toJSON();
            jQuery('#wplc_chat_logo').val(btoa(media_attachment.url));
           /* jQuery("#wplc_logo_area").html("<img src=\""+media_attachment.url+"\" width='100px'/>");*/
            jQuery("#wplc_logo_preview").prop('src',media_attachment.url);
        });
        tgm_media_frame_logo.open();
    });

    jQuery(document.body).on('click.tgmOpenMediaManager', '#wplc_btn_upload_agent_logo', function(e){
        e.preventDefault();

        if ( tgm_media_frame_agent_logo ) {
            tgm_media_frame_agent_logo.open();
            return;
        }

        tgm_media_frame_agent_logo = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Upload your Logo',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as agent default picture'
            }
        });

        tgm_media_frame_agent_logo.on('select', function(){
            var media_attachment = tgm_media_frame_agent_logo.state().get('selection').first().toJSON();
            jQuery('#wplc_agent_logo').val(btoa(media_attachment.url));
            jQuery("#wplc_agent_logo_preview").prop('src',media_attachment.url);
        });
        tgm_media_frame_agent_logo.open();
    });

    jQuery(document.body).on('click.tgmOpenMediaManager', '#wplc_btn_upload_icon', function(e){
        e.preventDefault();

        if ( tgm_media_frame_default ) {
            tgm_media_frame_default.open();
            return;
        }

        tgm_media_frame_default = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Upload your chat icon',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Chat Icon'
            }
        });

        tgm_media_frame_default.on('select', function(){
            var media_attachment = tgm_media_frame_default.state().get('selection').first().toJSON();
            jQuery('#wplc_chat_icon').val(btoa(media_attachment.url));
            jQuery("#wplc_icon_area #wplc_icon_default .wplc_default_chat_icon_selected").hide();
            jQuery("#wplc_icon_area #wplc_icon_default .wplc_default_chat_icon_selected[data-icontype='url']").show();
            jQuery("#wplc_icon_area #wplc_icon_default .wplc_default_chat_icon_selected[data-icontype='url'] img").attr("src",media_attachment.url);
            jQuery("#wplc_chat_icon_type").val("url");
        });
        tgm_media_frame_default.open();
    });

    jQuery("#wplc_btn_remove_logo").click(function() {
        jQuery("#wplc_logo_area").empty();
        jQuery("#wplc_chat_logo").val("");
    });
    jQuery("#wplc_btn_select_default_icon").click(function() {
        jQuery("#wplc_default_chat_icons").slideToggle();
    });

    jQuery("#wplc_btn_use_default_agent_logo").click(function() {
        jQuery("#wplc_agent_logo_preview").prop('src',settings_localization_data.imagesUrl +"/operatorIcon.png");
        jQuery("#wplc_agent_logo").val("");
    });


    jQuery(".wplc_default_chat_icon_selector").click(function() {
        var icon_selection = jQuery(this).data("icontype");
        if(typeof icon_selection != 'undefined') {
            jQuery(".wplc_default_chat_icon_selected:not([data-icontype='"+icon_selection+"'])").hide();
            jQuery(".wplc_default_chat_icon_selected[data-icontype='"+icon_selection+"']").show();
            jQuery(".wplc_default_chat_icon_selected[data-icontype='Default'] svg rect").css("fill",jQuery(".wplc_default_chat_icon_selected").css("background-color"))

            jQuery("#wplc_chat_icon_type").val(icon_selection);
        }
        jQuery("#wplc_default_chat_icons").slideToggle();
    });

    jQuery(".wplc_default_chat_icon_selected[data-icontype='Default'] svg rect").css("fill",jQuery(".wplc_default_chat_icon_selected").css("background-color"))

});