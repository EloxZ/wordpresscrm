jQuery(function () {
    jQuery(window).scroll(function() {
        var script = document.createElement('script');
        script.src = callusLoader_data.source;
        script.crossOrigin = 'anonymous';
        script.id = 'tcx-callus-js';
        document.head.appendChild(script);
        jQuery(window).off('scroll');
    });
});