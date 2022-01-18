<?php
class TCXChatSession
{

    public function __construct()
    {}

    public $id;
    public $name;
    public $email;
    public $status;
    public $timestamp;
    public $end_timestamp;
    public $url;
    public $client_data;
    public $rating;
    public $custom_fields;
	public $seconds_duration;
	public $agent_counter;
	public $client_counter;
	public $avatar_name_alias;


	public function getRatingHtml(){
		switch ($this->rating) {
			case 0:
				return '<img src="'.wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . 'images/svgs/angry.svg' ).'" alt="😭" > <span>'. __( "Very Bad", 'wp-live-chat-support' ) .'</span>';
				break;
			case 1:
				return '<img src="'.wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . 'images/svgs/frown.svg' ).'" alt="☹" ><span>'. __( "Bad", 'wp-live-chat-support' ) .'</span>';
				break;
			case 2:
				return '<img src="'.wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . 'images/svgs/meh.svg' ).'" alt="😐" ><span>'. __( "Neutral", 'wp-live-chat-support' ) .'</span>';
				break;
			case 3:
				return '<img src="'.wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . 'images/svgs/smile.svg' ).'" alt="🙂" ><span>'. __( "Good", 'wp-live-chat-support' ) .'</span>';
				break;
			case 4:
				return '<img src="'.wplc_protocol_agnostic_url( WPLC_PLUGIN_URL . 'images/svgs/grin.svg' ).'" alt="😀" ><span>'. __( "Very Good", 'wp-live-chat-support' ) .'</span>';
				break;
			default:
				return __("No Rating",'wp-live-chat-support');
				break;
		}
	}

    public function getStatusName()
    {
        switch ($this->status) {
            case ChatStatus::OLD_ENDED :
                return __("complete", 'wp-live-chat-support');
                break;
            case ChatStatus::PENDING_AGENT:
                return __("pending", 'wp-live-chat-support');
                break;
            case ChatStatus::ACTIVE:
                return __("active", 'wp-live-chat-support');
                break;
            case ChatStatus::BROWSE:
                return __("browsing", 'wp-live-chat-support');
                break;
            case ChatStatus::MISSED:
                return __("Missed", 'wp-live-chat-support');
                break;
            case ChatStatus::ENDED_BY_AGENT:
                return __("Ended by agent", 'wp-live-chat-support');
                break;
	        case ChatStatus::ENDED_BY_CLIENT:
		        return __("Ended by client", 'wp-live-chat-support');
		        break;
	        case ChatStatus::ENDED_DUE_CLIENT_INACTIVITY:
		        return __("Ended due client inactivity", 'wp-live-chat-support');
		        break;
	        case ChatStatus::ENDED_DUE_AGENT_INACTIVITY:
		        return __("Ended due agent inactivity", 'wp-live-chat-support');
		        break;
	        case ChatStatus::ENDED_DUE_BLOCK:
		        return __("Blocked by agent", 'wp-live-chat-support');
		        break;
            default:
                return __("Unknown",'wp-live-chat-support');
                break;
        }
    }



    public function getSessionHistoryUrl()
    {
        return admin_url('admin.php?page=wplivechat-session-details&cid=' . $this->id . "&nonce=" . wp_create_nonce("sessionDetailsNonce"));
    }

    public function getDownloadSessionHistoryUrl()
    {
        return admin_url('admin.php?page=wplivechat-menu-session&wplc_action=download_session&type=csv&cid=' . $this->id . "&nonce=" . wp_create_nonce("downloadSessionsNonce"));
    }

    public function getRemoveSessionUrl($page, $pageNum = null)
    {
        return admin_url('admin.php?page=' . $page . '&wplc_action=prompt_remove_session&cid=' . $this->id . ($pageNum != null ? "&pagenum=" . $pageNum : ""));
    }

}
?>