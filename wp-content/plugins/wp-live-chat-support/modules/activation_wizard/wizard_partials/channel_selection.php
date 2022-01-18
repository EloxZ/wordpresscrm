<div class="wizard_body">
    <div class="row mx-0">
        <div class="col-12 px-0">
            <div class="row">
                <div class="wplc-channel-selection">
                    <input type="radio" value="phone" name="wplc_pbx_exist" id="wplc_pbx_exist_yes">
                    <label id="wplc_pbx_exist_yes_label" for="wplc_pbx_exist_yes">
						<?=__('Yes, I have 3CX and would like to have','wp-live-chat-support')?><br>
                    </label>
                    <ul>
                        <li> <i class="fa fa-check"></i><?=__('iOS and Android Apps','wp-live-chat-support')?></li>
                        <li><i class="fa fa-check"></i><?=__('Ability to elevate chats to voice or video call','wp-live-chat-support')?></li>
                        <li><i class="fa fa-check"></i><?=__('Have groups of agents answer chats','wp-live-chat-support')?></li>
                        <li><i class="fa fa-check"></i><?=__('Get 3CX free hosted or on premise at www.3cx.com','wp-live-chat-support')?></li>
                    </ul>

                </div>
                <div class="wplc-channel-selection">
                    <input type="radio" value="mcu" name="wplc_pbx_exist" id="wplc_pbx_exist_no">
                    <label id="wplc_pbx_exist_no_label" for="wplc_pbx_exist_no">
						<?=__('No. I will login to WordPress to answer chats.','wp-live-chat-support')?> </label>
                </div>
            </div>
        </div>
    </div>
</div>
