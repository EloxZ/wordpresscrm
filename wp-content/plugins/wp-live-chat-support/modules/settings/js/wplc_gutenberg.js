var sizes = ['sm', 'md', 'lg'];
// Gutenberg functions
jQuery(function ($) {
    // Initate settings
    // initiate_gutenberg_settings();

    jQuery('#wplc_gutenberg_size').on('change', function () {
        jQuery('.wplc_block').removeClass('sm').removeClass('md').removeClass('lg');
        jQuery('.wplc_block').addClass(sizes[jQuery(this).val() - 1]);
        jQuery('#wplc_custom_templates').trigger('change');
    });

    jQuery('#wplc_gutenberg_icon').on('change', function () {
        var fontawesome_class = jQuery(this).val().replace(/\./g, "");
        jQuery('.wplc_block_icon i').removeClass().addClass(fontawesome_class);
    });

    jQuery('.iconpicker-item').on('click', function () {
        var fontawesome_class = jQuery(this).attr('title').replace(/\./g, "");
        jQuery('.wplc_block_icon i').removeClass().addClass(fontawesome_class);
    });

    jQuery('#wplc_gutenberg_text').on('change', function () {
        var text = jQuery(this).val();
        jQuery('.wplc_block_text').html(text);
    });

    jQuery('#activate_block').on('change', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('.wplc_block').removeClass('disabled');
        } else {
            jQuery('.wplc_block').addClass('disabled');
        }
    });

    jQuery('#wplc_gutenberg_enable_icon').on('change', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('.wplc_block_icon').show();
            jQuery('#wplc_gutenberg_icon').removeAttr('disabled');
            jQuery('#wplc_gutenberg_icon').val("fas fa-comment-dots");
        } else {
            jQuery('.wplc_block_icon').hide();
            jQuery('#wplc_gutenberg_icon').attr('disabled', 'disabled');
        }
    });

    jQuery('.wplc_code').on('click', function () {
        var $temp = jQuery('<input>');
        jQuery('body').append($temp);
        $temp.val(jQuery(this).text()).select();
        document.execCommand('copy');
        $temp.remove();
        jQuery(this).closest('p').append('<p class="wplc_copied_message">' + jQuery(this).text() + ' copied to clipboard.</p>').find('.wplc_copied_message').fadeOut('slow');
    });

    // Allow users to upload a logo image
    var media_uploader;
    jQuery('#wplc_gutenberg_upload_logo').on('click', function (e) {

        e.preventDefault();

        if (media_uploader) {
            media_uploader.open();
            return;
        }

        media_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select a Logo',
            button: {
                text: 'Select Logo'
            },
            multiple: false
        });

        media_uploader.on('select', function () {
            attachment = media_uploader.state().get('selection').first().toJSON();
            jQuery('#wplc_gutenberg_logo').val(attachment.url);
            jQuery('.wplc_block_logo').css('background-image', 'url(' + attachment.url + ')');
        });

        media_uploader.open();

    });

    jQuery('#wplc_gutenberg_remove_logo').on('click', function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to reset the logo to the default logo?")) {
            jQuery('#wplc_gutenberg_logo').val(jQuery('#wplc_gutenberg_default_logo').val());
            jQuery('.wplc_block_logo').css('background-image', 'url("' + jQuery('#wplc_gutenberg_default_logo').val() + '")');
        }
        return;
    });

});

function initiate_gutenberg_settings() {
    var editor_html = ace.edit('wplc_custom_html_editor');
    editor_html.$blockScrolling = Infinity;
    editor_html.setTheme('ace/theme/monokai');
    editor_html.getSession().setMode('ace/mode/html');
    editor_html.getSession().setUseWorker(false);
    editor_html.setValue(jQuery.trim(jQuery('#wplc_custom_html').val()), 1);
    editor_html.getSession().on("change", function () {
        jQuery('#wplc_custom_html').val(editor_html.getSession().getValue());
    });

    if (jQuery.trim(editor_html.getSession().getValue()) == '') {
        editor_html.setValue(jQuery.trim(gutenbergData.default_html), 1);
    }

    jQuery('#wplc_gutenberg_reset_html').on('click', function () {
        editor_html.setValue(jQuery.trim(gutenbergData.default_html), 1);
        jQuery("#wplc_custom_templates").val("template_default");
    });

    jQuery('#wplc_settings').on('submit', function () {
        if (jQuery.trim(editor_html.getSession().getValue()) == '') {
            editor_html.setValue(gutenbergData.default_html, 1);
        }
    });

    // Predefined Templates
    wplc_gutenberg_templates(editor_html);

    // Initiate FontAwesome Icon Picker
    jQuery('#wplc_gutenberg_icon').iconpicker({
        placement: 'bottomRight'
    });

    jQuery('.wplc_block').removeClass('sm').removeClass('md').removeClass('lg');
    jQuery('.wplc_block').addClass(sizes[jQuery('#wplc_gutenberg_size').val() - 1]);

    if (jQuery('#wplc_gutenberg_enable_icon').is(':checked')) {
        jQuery('.wplc_block_icon').show();
        jQuery('#wplc_gutenberg_icon').removeAttr('disabled');
    } else {
        jQuery('.wplc_block_icon').hide();
        jQuery('#wplc_gutenberg_icon').attr('disabled', 'disabled');
    }

    if (jQuery('#activate_block').is(':checked')) {
        jQuery('.wplc_block').removeClass('disabled');
    } else {
        jQuery('.wplc_block').addClass('disabled');
    }
}

function wplc_gutenberg_templates(editor) {

    var templates = new Array();
    templates['template_default'] = '<!-- Default Template - Dark -->\n<div class="wplc_block">\n\t{wplc_logo}\n\t<span class="wplc_block_text">{wplc_text}</span>\n\t<span class="wplc_block_icon">{wplc_icon}</span>\n</div>';
    templates['template_default_tooltip'] = '<!-- Default - Tooltip -->\n<!-- Hover over the block to see the tooltip -->\n<div class="wplc_block">\n\t<span class="wplc_block_tooltip">Want to chat?</span>\n\t{wplc_logo}\n\t<span class="wplc_block_text">{wplc_text}</span>\n\t<span class="wplc_block_icon">{wplc_icon}</span>\n</div>';
    templates['template_default_light'] = '<!-- Default - Light Template -->\n<div class="wplc_block light">\n\t{wplc_logo}\n\t<span class="wplc_block_text">{wplc_text}</span>\n\t<span class="wplc_block_icon">{wplc_icon}</span>\n</div>';
    templates['template_tooltip'] = '<!-- Circle - Tooltip Template -->\n<!-- Hover over the block to see the tooltip -->\n<div class="wplc_block circle">\n\t<span class="wplc_block_tooltip">Chat with us!</span>\n\t{wplc_logo}\n</div>';
    templates['template_circle'] = '<!-- Circle Template -->\n<div class="wplc_block circle">\n\t{wplc_logo}\n</div>';
    templates['template_chat_bubble'] = '<!-- Chat Bubble Template -->\n<div class="wplc_block chat_bubble">\n\t{wplc_logo}\n\t<span class="wplc_block_text">{wplc_text}</span>\n</div>';
    templates['template_circle_rotate'] = '<!-- Circle - Rotating Template -->\n<!-- Hover over the icon to see it rotate -->\n<div class="wplc_block circle rotate">\n\t{wplc_logo}\n\t<span class="wplc_block_icon">{wplc_icon}</span>\n</div>';

    var current_template = wplc_gutenberg_filter_template(editor.getSession().getValue());

    jQuery(document).find('.wplc_gutenberg_preview').html(current_template);

    jQuery('#wplc_custom_templates').on('change', function () {
        if (jQuery(this).val() !== '0') {
            var template = jQuery.trim(templates[jQuery(this).val()]);
            template = template.replace('wplc_block', 'wplc_block ' + sizes[jQuery(document).find('#wplc_gutenberg_size').val() - 1]);
            editor.setValue(template, 1);
            jQuery(document).find('.wplc_gutenberg_preview').html(wplc_gutenberg_filter_template(template));
        }
    });
}

function wplc_gutenberg_filter_template(template) {
    var logo = jQuery(document).find('#wplc_gutenberg_logo').val();
    if(logo==='')
    {
        logo = jQuery(document).find('#wplc_gutenberg_default_logo').val();
    }
    var text = jQuery(document).find('#wplc_gutenberg_text').val();
    var icon = jQuery(document).find('#wplc_gutenberg_icon').val();
    var placeholder_codes = ['wplc_logo', 'wplc_text', 'wplc_icon'];
    var placeholder_values = ['<span class="wplc_block_logo" style="background-image: url(\'' + logo + '\');"></span>',
            text,
            icon !== '' ? '<i class="fa ' + icon + '"></i>' : '<i class="fas fa-comment-dots"></i>'
        ];

    for (var i = 0; i < placeholder_codes.length; i++) {
        template = template.replace(new RegExp('{' + placeholder_codes[i] + '}', 'gi'), placeholder_values[i]);
    }

    return template;
}