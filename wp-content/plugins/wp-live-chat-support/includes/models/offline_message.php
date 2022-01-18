<?php
class TCXOfflineMessage
{

    public function __construct()
    {}

    public $id;
    public $name;
    public $email;
	public $phone;
    public $message;
    public $timestamp;
    

    public function getRemoveOfflineMessageUrl($pageNum = null)
    {
        return admin_url('admin.php?page=wplivechat-menu-offline-messages&wplc_action=prompt_remove_offline_message&omid=' . $this->id . ($pageNum != null ? "&pagenum=" . $pageNum : ""));
    }

}
?>