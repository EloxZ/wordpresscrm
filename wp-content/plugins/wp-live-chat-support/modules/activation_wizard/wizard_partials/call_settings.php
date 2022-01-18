<div class="wizard_body">
    <div class="row mx-0">
        <div class="col-md-12 px-0">
            <div class="form-row">
                <div class="col-md-6 form-group" id="wplc_direct_call">
                    <h1 class="col-form-label"> <?=__('Do you want to show the call us button under the chat button? This allows calls to be made without initiating a chat.','wp-live-chat-support')?></h1>
                    <div class="wplc-direct-call-selection">
                        <input type="radio" value="1" name="wplc_direct_call_mode" id="wplc_direct_call_enable" checked>
                        <label id="wplc_direct_call_enable_label" for="wplc_direct_call_enable">
                            <?=__('Yes','wp-live-chat-support')?><br>
                        </label>
                    </div>
                    <div class="wplc-direct-call-selection">
                        <input type="radio" value="0" name="wplc_direct_call_mode" id="wplc_direct_call_disable">
                        <label id="wplc_direct_call_disable_label" for="wplc_direct_call_disable">
	                        <?=__('No','wp-live-chat-support')?> </label>
                    </div>
                </div>
                <div class="col-md-6" >
                    <img src="<?= wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . '/images/direct_call_preview.png'); ?>">
                </div>
            </div>
        </div>
    </div>
</div>