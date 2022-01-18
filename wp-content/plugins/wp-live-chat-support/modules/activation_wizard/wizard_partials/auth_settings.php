<div class="wizard_body">
    <h1 class="col-form-label"> <?=__('What information do you want to require from a visitor?','wp-live-chat-support')?></h1>
    <div class="row mx-0">
        <div class="col-md-12 px-0">
            <div class="form-row">
                <div class="col-md-6 form-group" id="wplc_auth_mode">
                    <div class="wplc-auth-selection">
                        <input type="radio" value="name" name="wplc_auth_mode" id="wplc_auth_mode_name">
                        <label id="wplc_auth_mode_name_label" for="wplc_auth_mode_name">
                            <?=__('Name only','wp-live-chat-support')?><br>
                        </label>
                    </div>
                    <div class="wplc-auth-selection">
                        <input type="radio" value="both" name="wplc_auth_mode" id="wplc_auth_mode_both">
                        <label id="wplc_auth_mode_both_label" for="wplc_auth_mode_both">
	                        <?=__('Name and email','wp-live-chat-support')?> </label>
                    </div>
                    <div class="wplc-auth-selection">
                        <input type="radio" value="none" name="wplc_auth_mode" id="wplc_auth_mode_none">
                        <label id="wplc_auth_mode_none_label" for="wplc_auth_mode_none">
	                        <?=__('None','wp-live-chat-support')?> </label> </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 class="col-form-label"><?= __( 'Do you want to allow Video or Voice Calls?', 'wp-live-chat-support' ) ?></h1>
    <div class="row mx-0">
        <div class="col-md-12 px-0">
            <div class="form-row">
                <div class="col-md-6 form-group" id="c2cMode">
                    <div class="wplc-c2cmode-selection">
                        <input type="radio" value="chat" name="wplc_c2c_mode" id="wplc_c2c_mode_chat">
                        <label id="wplc_c2c_mode_chat_label" for="wplc_c2c_mode_chat">
	                        <?= __( 'Chat Only', 'wp-live-chat-support' ) ?>
                        </label>
                    </div>
                    <div class="wplc-c2cmode-selection">
                        <input type="radio" value="phonechat" name="wplc_c2c_mode" id="wplc_c2c_mode_phone">
                        <label id="wplc_c2c_mode_phone_label" for="wplc_c2c_mode_phone">
	                        <?= __( 'Phone and Chat', 'wp-live-chat-support' ) ?> </label>
                    </div>
                    <div class="wplc-c2cmode-selection">
                        <input type="radio" value="all" name="wplc_c2c_mode" id="wplc_c2c_mode_all">
                        <label id="wplc_c2c_mode_all_label" for="wplc_c2c_mode_all">
	                        <?= __( 'Video, Phone and Chat', 'wp-live-chat-support' ) ?> </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>