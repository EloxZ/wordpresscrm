/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V2.4
 *
 * Copyright 2020 Automattic
 * CRM dash date range picker
 *
 * Date: 15th August 2018
 */

jQuery(function($){

    window.dash_security = jQuery('#zbs_dash_count_security').val();

    var ctx = document.getElementById("growth-chart");
    window.contactChart = new Chart( ctx, {
        type: 'bar',
        data: {
            labels: "",
            datasets: [
            {
                label: "",
                backgroundColor: "#00a0d2",
                data: ""
            }
            ]
        },
        options: {
            responsive: true, 
            maintainAspectRatio: false,
            legend: { display: false },
            title: {
            display: false,
            text: ''
            },
            scales: {
            yAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true   // minimum value will be 0.
                }
            }]
        }


        }
    });

    funnel_height = jQuery('#bar-chart').height();
    jQuery('.zbs-funnel').height(funnel_height);

    jQuery( window ).on( 'resize', function() {

        jQuery('#funnel-container').html("");

        funnel_height = jQuery('#bar-chart').height();
        jQuery('.zbs-funnel').height(funnel_height);

        jQuery('#funnel-container').drawFunnel(window.funnelData, {
            width: jQuery('.zbs-funnel').width() - 50, 
            height: jQuery('.zbs-funnel').height() - 50,  
        
            // Padding between segments, in pixels
            padding: 1, 
        
            // Render only a half funnel
            half: false,  
        
            // Width of a segment can't be smaller than this, in pixels
            minSegmentSize: 30,  
        
            // label: function () { return "Label!"; } 
        
        
            label: function (obj) {
                return obj;
            }
          });
      });

    jQuery('.dashboard-customiser').on("click",function(e){
        jQuery('.dashboard-custom-choices').toggle();
    });

    jQuery('.contact-display-chooser .day-or-month .button').on("click", function(e){
        
        jQuery('.contact-display-chooser .day-or-month .button').removeClass('selected');
        jQuery(this).addClass('selected');

        range = jQuery(this).attr('data-range');

        if(range == 'yearly'){
            jetpackcrm_draw_contact_chart(window.yearly);
        }
        if(range == 'monthly'){
            jetpackcrm_draw_contact_chart(window.monthly);
        }
        if(range == 'weekly'){
            jetpackcrm_draw_contact_chart(window.weekly);
        }
        if(range == 'daily'){
            jetpackcrm_draw_contact_chart(window.daily);
        }

    });

    jQuery('.dashboard-custom-choices input').on("click", function(e){
        var zbs_dash_setting_id = jQuery(this).attr('id');
        jQuery('#' + zbs_dash_setting_id + '_display').toggle();

        var is_checked = -1; if (jQuery('#' + zbs_dash_setting_id).is(":checked")) is_checked = 1;
        var the_setting = zbs_dash_setting_id;
        var security = jQuery('#zbs_dash_setting_security').val();

        var data = {
        'action': 'zbs_dash_setting',
        'is_checked': is_checked,
        'the_setting': the_setting,
        'security': security
        };

        jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        "data": data,
        dataType: 'json',
        timeout: 20000,
        success: function(response) {


        },
        error: function(response){

    
        }

        });
    });


    jQuery(function() {

        function cb(start, end) {
            zbsStrokeColor = zbsJS_admcolours.colors[0];
            jQuery('#reportrange span').html(start.format('MMM,D,Y') + ' - ' + end.format('MMM,D,Y'));

            var zbs_start_date = start.format('Y-MM-DD');
            var zbs_end_date = end.format('Y-MM-DD');

            jQuery('.loading').css('color', zbsStrokeColor).show();
    
            var t = {
                    action: 'jetpackcrm_dash_refresh',
                    start_date: zbs_start_date,
                    end_date: zbs_end_date,
                    security: window.dash_security
                    }

                o = jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: t,
                    dataType: "json"
                });
                o.done(function(res) {
                    //can re-call the AJAX and re-draw to be fair.. for now do it with window vars
                    window.yearly   = res.chart.yearly;
                    window.monthly  = res.chart.monthly;
                    window.weekly   = res.chart.weekly;
                    window.daily    = res.chart.daily;

                    jQuery("#crm_summary_numbers").addClass(res.boxes);
                    summary_html = '';
                    for (var i=0; i<res.summary.length; i++) {
                        item = res.summary[i];
                        summary_html += '\
                            <div class="card center aligned">\
                                <div class="content">\
                                    <h3 class="ui header">'+ item.label  +'</h3>\
                                    <div class="range_total">+' + item.range_total + '</div>\
                                    <div class="text-muted alltime_total">' + item.alltime_total_str + '</div>\
                                </div>\
                                <div class="ui bottom attached button">\
                                    <a href="' + item.link + '">\
                                        <i class="unhide icon"></i>\
                                        ' + zeroBSCRMJS_globViewLang('viewall') + '\
                                    </a>\
                                </div>\
                            </div>\
                      ';
                    }
                    jQuery("#crm_summary_numbers").html(summary_html);

                    jetpackcrm_draw_contact_chart(res.chart.monthly);
                    
                })
                o.fail(function(res){
    
                });
    
    
        }
    
    
        cb(moment().subtract(1, 'year'), moment());
    
        jQuery('#reportrange').daterangepicker({
            ranges: {
               'Last 30 Days': [moment().subtract(30, 'days'), moment()],
               'Latest Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'Prior Month' : [moment().subtract(2, 'month').startOf('month'), moment().subtract(2, 'month').endOf('month')],
               'Last 3 Months': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'Last 6 Months': [moment().subtract(6, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'Last Year': [moment().subtract(1, 'year').startOf('month'), moment().subtract(1, 'month').endOf('month')],
             //  'All Time': [ window.zbs_min_trans_date , moment()],
            },
            locale: {
              firstDay: (window.zbs_root.localeOptions && window.zbs_root.localeOptions.firstDay) ? window.zbs_root.localeOptions.firstDay : 0
            },
            maxDate: moment()
        }, cb);
    });

    jQuery('#daterange').on('apply.daterangepicker', function(ev, picker) {
      jQuery("#zbs_from").val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
      jQuery("#zbs_to").val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
    });

});


function jetpackcrm_draw_contact_chart(data){
    window.contactChart.data.labels = data.labels;
    window.contactChart.data.datasets[0].data = data.data;
    window.contactChart.update();
}