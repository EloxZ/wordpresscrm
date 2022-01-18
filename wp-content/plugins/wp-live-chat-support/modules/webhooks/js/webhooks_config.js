var webhooks_validation_rules = {
    wplc_webhook_event: {
        required: true
    },
    wplc_webhook_domain: {
        required: true,
        url2 : true
    },
    wplc_webhook_method: {
        required: true
    }
};