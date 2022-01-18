<div id="wplc-chat-container">
	<?php if ( $onlyPhone ) { ?>
        <call-us-phone
                style="position: fixed; <?= $position_style ?> justify-content: flex-end;
                        flex-direction: column; display: flex; z-index: 99999;
                        --call-us-form-header-background:<?= $baseColor ?>;
                        --call-us-form-height:40vh;
                        --call-us-main-button-color:<?= $baseColor ?>;"
                id="wp-live-chat-by-3CX-phone"
                channel-url="<?= $channel_url ?>"
                wp-url="<?= $wp_url ?>"
                party="<?= $chatParty ?>"
                animation-style="<?= $animation ?>"
                chat-delay="<?=$chatDelay?>"
                enable="<?= $is_enable ?>"
                enable-onmobile="<?= $enable_mobile ?>"
                <?= $chatLang!=='browser'? "lang=\"".$chatLang."\"":"" ?>
        >
        </call-us-phone>
	<?php } else { ?>
        <call-us
                style="position: fixed;
                        <?= $position_style ?>
                        font-family: Arial;
                        z-index: 99999;
                        --call-us-form-header-background:<?= $baseColor ?>;
                        --call-us-main-button-background:<?= $buttonColor ?>;
                        --call-us-client-text-color:<?= $clientColor ?>;
                        --call-us-agent-text-color:<?= $agentColor ?>;
                        --call-us-form-height:<?= $chat_height ?>;"
                id="wp-live-chat-by-3CX"
                channel-url="<?= $channel_url ?>"
                files-url="<?= wplc_protocol_agnostic_url( $files_url ) ?>"
                wp-url="<?= wplc_protocol_agnostic_url( $wp_url ) ?>"
                minimized="<?= $minimized ?>"
                popup-when-online="<?= $popup_when_online ?>"
                animation-style="<?= $animation ?>"
                party="<?= $chatParty ?>"
                minimized-style="<?= $minimizedStyle ?>"
                allow-call="<?= $allowCalls ?>"
                allow-video="<?= $allowVideo ?>"
                allow-soundnotifications="<?= $enable_msg_sounds ?>"
                enable-mute="<?= $enable_msg_sounds ?>"
                enable-onmobile="<?= $enable_mobile ?>"
                offline-enabled = "<?= $offline_enabled ?>"
                enable="<?= $is_enable ?>"
                soundnotification-url="<?= $message_sound ?>"
                facebook-integration-url="<?= $integrations->facebook ?>"
                twitter-integration-url="<?= $integrations->twitter ?>"
                email-integration-url="<?= property_exists( $integrations, 'mail' ) ? $integrations->mail : '' ?>"
                ignore-queueownership="<?= $ignoreQueueOwnership ?>"
                enable-poweredby="<?= $enable_poweredby ?>"
                authentication="<?= $auth_type ?>"
                operator-name="<?= $agent_name ?>"
                show-operator-actual-name="<?= $showAgentsName ?>"
                show-operator-actual-image="<?= $showAgentsName ?>"
                window-icon="<?= wplc_protocol_agnostic_url( $chat_logo ) ?>"
                operator-icon="<?= wplc_protocol_agnostic_url( $agent_logo ) ?>"
                button-icon="<?= wplc_protocol_agnostic_url( $chat_icon ) ?>"
                button-icon-type="<?= $chat_icon_type ?>"
                channel="<?= $channel ?>"
                channel-secret="<?= $secret ?>"
                aknowledge-received="<?= $acknowledgeReceived ?>"
                gdpr-enabled="<?= $gdpr_enabled ?>"
                gdpr-message="<?= esc_attr( $gdpr_message ) ?>"
                files-enabled="<?= $files_enabled ?>"
                rating-enabled="<?= $rating_enabled ?>"
                departments-enabled="<?= $departments_enabled ?>"
                message-userinfo-format="<?= $messageUserinfoFormat ?>"
                message-dateformat="<?= $messageDateFormat ?>"
                visitor-name="<?= $visitor_name ?>"
                visitor-email="<?= $visitor_email ?>"
                greeting-visibility="<?= $greetingMode ?>"
                greeting-offline-visibility="<?= $offlineGreetingMode ?>"
                chat-delay="<?=$chatDelay?>"
                enable-direct-call="<?=$enable_direct_call?>"
                enable-ga = "<?=$ga_enabled?>"
	            <?= $chatLang!=='browser'? "lang=\"".$chatLang."\"":"" ?>
        ></call-us>
	<?php } ?>
</div>
