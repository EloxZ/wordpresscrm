<?php
class TCXDepartment
{
    public function __construct()
    {}

    public $id;
    public $name;

    public function getSaveUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-department&wplc_action=save_department&nonce=" . wp_create_nonce("save_department") . "&depid=" . $this->id);
    }

    public function getEditUrl()
    {
        return admin_url("admin.php?page=wplivechat-manage-department&nonce=" . wp_create_nonce('edit_department') . "&depid=" . $this->id);
    }

    public function getRemoveUrl()
    {
	    return admin_url("admin.php?page=wplivechat-menu-tools&wplc_action=prompt_remove_department&depid=" . $this->id."#wplc_departments_tab");
    }

}
