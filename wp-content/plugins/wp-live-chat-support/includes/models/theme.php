<?php
class TCXTheme
{
    public function __construct()
    {}

    public static function create_theme($alias,$name,$base_color,$button_color,$agent_color,$client_color){
    	$result = new TCXTheme();
	    $result->alias = $alias;
	    $result->name = $name;
	    $result->base_color = $base_color;
	    $result->button_color = $button_color;
	    $result->agent_color = $agent_color;
	    $result->client_color = $client_color;

	    return $result;

    }

    public static function available_themes(){
	    $themes = [];
	    array_push($themes,self::create_theme('3CX','3CX','#373737','#0596d4','#eeeeee','#d4d4d4'));
	    array_push($themes,self::create_theme('SaltyWater','Salty Water','#186c77','#05d6d6','#eeeeee','#d4d4d4'));
	    array_push($themes,self::create_theme('SummerVibes','Summer Vibes','#d97e17','#d63005','#eeeeee','#d4d4d4'));
	    return $themes;
    }

	public static function get_theme($alias){

			$result = array_filter(
				self::available_themes(),
				function ($theme) use (&$alias) {
					return $theme->alias == $alias;
				}
			);
			if(count($result)===1)
			{
				return $result[array_keys($result)[0]];
			}
			else
			{
				return null;
			}


	}

	public $name;
	public $alias;
	public $base_color;
	public $button_color;
	public $agent_color;
    public $client_color;

}

?>
