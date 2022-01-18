
jQuery(document).ready(function () {

    if (typeof TCXfa === "object") {
        if (typeof TCXfa.tcxFaInit === "function") {
            TCXfa.tcxFaInit();
        }
    }

    jQuery('.wp-block-wp-live-chat-support-wplc-chat-box').on('click', function () {
        document.querySelector('call-us').shadowRoot.getElementById('wplc-chat-button').click()
    });
});