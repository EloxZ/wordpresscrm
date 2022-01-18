<?php
class TCXChatMessage
{

    public function __construct()
    {}

    public $id;
    public $originates;
    public $from;
    public $timestamp;
    public $session_id;
	public $messageText;
    private $message;
    private $is_encrypted;

    public function get_message(){
        if($this->is_encrypted)
        {
            return TCXEncryptHelper::decrypt($this->message);
        }
        else
        {
            return $this->message;
        }
    }

    public function set_message($message_serialized)
    {
        $message = maybe_unserialize($message_serialized);
        if(is_array($message))
        {
            $this->message = $message['m'];
            $this->is_encrypted = $message['e']==1;
        }
        else
        {
            $this->message = $message_serialized;
            $this->is_encrypted =false;
        }
	    $this->messageText = $this->get_message();
    }

    public function getOriginator()
    {
        switch ($this->originates) {
            case 2:
                return  __('user', 'wp-live-chat-support');
                break;
            default:
                return  __('agent', 'wp-live-chat-support');
                break;
        }
    }

}

?>