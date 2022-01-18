<h3><?= __( "Embed Code", 'wp-live-chat-support' ) ?></h3>

<table class='form-table wp-list-table wplc_list_table widefat fixed striped pages'>

    <tr>
        <td>
            <div id='wplc_embed_code_viewer' style="height:500px"></div>
            <textarea id='wplc_embed_code_viewer_textarea' style='display: none;' data-editor='html' rows='50'>
                <?= trim($chat_client_component->run()); ?>
            </textarea>
        </td>
    </tr>
    <tr>
        <td>
            <?=__("Download from here callus.js and host it within yours site server.") ?> <br/>
            <a class="button button-primary valid" id="download_call_us" download="callus.js" href="<?= $call_us_file; ?>" target="_blank"> Download CallUs.js </a>
        </td>
    </tr>
</table>
