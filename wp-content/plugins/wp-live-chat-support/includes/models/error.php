<?php
class TCXError
{

    public $ErrorFound;
    public $ErrorHandleType;
    public $ErrorData;

    public function __construct()
    {
        $this->ErrorFound = false;
        $this->ErrorHandleType = "NOACTION";
        $this->ErrorData = new stdClass();
    }

    public static function createShowError($message)
    {
        $result = new self();
        $result->ErrorFound = true;
        $result->ErrorHandleType = "Show";  
        $result->ErrorData->message =$message;
        
        return $result;
    }
}
?>