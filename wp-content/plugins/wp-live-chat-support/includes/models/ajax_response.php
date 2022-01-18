<?php
class TCXChatAjaxResponse extends TCXAjaxResponse
{
	public $Status;

	public function __construct()
	{
		parent::__construct();
	}

	public static function success_ajax_respose($data ,$chatStatus = null)
	{
		$result = parent::create_ajax_response($data);
		if($chatStatus !== null)
		{
			$result->Status = $chatStatus;
		}
		$json_data = json_encode($result);
		if(json_last_error()===JSON_ERROR_UTF8)
		{
			$json_data = TCXUtilsHelper::wplc_json_encode($result);
		}

		if(!empty($json_data) && $json_data!=='false')
		{
			return $json_data;
		}
		else
		{
			return "{'JsonError':'". json_last_error()."'}";
		}
	}

}

class TCXAjaxResponse
{

	public $Data;

    public $ErrorFound;
    public $ErrorMessage;


    public function __construct()
    {
        $this->ErrorFound = false;
        $this->Data =  array();
        $this->ErrorMessage = "";
    }

    protected static function create_ajax_response($data){
	    header( "Content-Type: application/json" );
	    $result = new self();
	    $result->Data = $data;
	    return $result;
    }

	public static function error_ajax_respose($error_message)
	{
		$result = new self();
		$result->ErrorFound = true;
		$result->ErrorMessage = $error_message;
		header( "Content-Type: application/json" );
		return json_encode($result);
	}

	public static function success_ajax_respose($data)
	{
		$result = self::create_ajax_response($data);
		$json_data = json_encode($result);
		if(json_last_error()===JSON_ERROR_UTF8)
		{
			$json_data = TCXUtilsHelper::wplc_json_encode($result);
		}

		if(!empty($json_data) && $json_data!=='false')
		{
			return $json_data;
		}
		else
		{
			return "{'JsonError':'". json_last_error()."'}";
		}
	}

}
?>