<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div>
    <span><strong><?= __( 'Starting Time:', 'wp-live-chat-support' ) ?></strong>
            <?= date( 'Y-m-d H:i:s', current_time( strtotime( $session->timestamp ) ) ) ?>
    </span>
    <span><strong><?= __( 'Ending Time:', 'wp-live-chat-support' ) ?> </strong>
            <?= date( 'Y-m-d H:i:s', current_time( strtotime( $session->end_timestamp ) ) ) ?>
        </span>
    <div>
        <div>
            <div><?= sanitize_text_field( $session->name ) ?>
                ,(<?= sanitize_text_field( $session->email ) ?>)
            </div>
        </div>
    </div>
    <div>
        <div>
			<?php foreach ( $messages as $message ) {
				if ( $message->from == "System notification" ) {
					?>
                    <span>
                        <?= $message->get_message() ?>
                    </span>
					<?php
				} else {
					?>
                    <strong><?= $message->from ?>
                        ( <?= date( 'H:i:s', current_time( strtotime( $message->timestamp ) ) ) ?> ):</strong>
                    <span><?= $message->get_message() ?></span>
				<?php }
				?>
                <br/>
			<?php }
			?>
        </div>
    </div>
</div>
