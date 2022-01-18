<div class="wizard_body">
    <div class="row mx-0">
        <div class="col-md-12 px-0">
            <div class="form-row">
                <ol>
                    <li><?=__('Specify the URL of your website by clicking on Messaging > Add Live Chat in the 3CX Management Console.','wp-live-chat-support')?>
                        <a href="https://www.3cx.com/docs/manual/live-chat/" target="_blank"><?=__('Read more.','wp-live-chat-support')?></a></li>
                    <li><?=__('Copy the Click2Talk URL obtained from Extension or Queue and paste it here.','wp-live-chat-support')?>
                        <div class="form-group col-md-12 mx-0 px-0" id="existing_pbx_settings">
                            <input placeholder="https://my-pbx.3cx.eu:5001/callus/#support" id="clickToTalkUrl" name="clickToTalkUrl" class="form-control" type="text"
                                   pattern="^(http:\/\/|https:\/\/){1}(([\-\.]?)[a-zA-Z0-9.-])+(:[0-9]{1,5})?(\/[a-zA-Z0-9-._~:\/?#@!$&*=;+%()']*)?\/callus\/#([a-zA-Z0-9.-])*$">
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>