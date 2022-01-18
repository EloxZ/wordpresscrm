<?php
class TCXWebhook
{
    public function __construct()
    {}

    public $id;
    public $url;
    public $action;
    public $method;

    public function getActionName()
    {
        $this->action = intval($this->action);
        return TCXWebhookHelper::getWebhookActionsDictionary()[$this->action];
    }

    public function getSaveUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-webhook&wplc_action=save_webhook&nonce=" . wp_create_nonce("save_webhook") . "&whid=" . $this->id);
    }

    public function getEditUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-webhook&nonce=" . wp_create_nonce('edit_webhook') . "&whid=" . $this->id);
    }

    public function getRemoveUrl()
    {
        return admin_url("admin.php?page=wplivechat-menu-tools&wplc_action=prompt_remove_webhook&whid=" . $this->id."#wplc_webhooks_tab");
    }



}
