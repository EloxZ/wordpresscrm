<?php
class TCXRingtonesHelper {

	public static function get_tone_name($tone, $path) {
		$tonename = '';
		if (!empty($tone)) {
			$tonename = basename($tone);
		}
		if (!file_exists($path.$tonename)) {
			$tonename='';
		}
		return $tonename;
	}

	public static function get_tone_url($tone, $path, $url, $default) {
		$tonename=self::get_tone_name($tone, $path);
		if (empty($tonename)){
			return $default;
		}
		return $url.$tonename;
	}

	public static function get_ringtone_name($ringtone) {
		return self::get_tone_name($ringtone, WPLC_PLUGIN_DIR."/includes/sounds/");
	}

	public static function get_messagetone_name($messagetone) {
		return self::get_tone_name($messagetone, WPLC_PLUGIN_DIR."/includes/sounds/message/");
	}

	public static function get_ringtone_url($ringtone) {
		return self::get_tone_url($ringtone,  WPLC_PLUGIN_DIR."/includes/sounds/", WPLC_PLUGIN_URL.'includes/sounds/', WPLC_PLUGIN_URL.'includes/sounds/general/Default_chat.mp3');
	}

	public static function get_messagetone_url($messagetone,$default='') {
		return self::get_tone_url($messagetone,  WPLC_PLUGIN_DIR."/includes/sounds/message/", WPLC_PLUGIN_URL.'includes/sounds/message/',$default );
	}


	public static function get_available_sounds($path,$default_key ='') {
		$wplc_ringtones = array($default_key => __("Default",'wp-live-chat-support'));
		$files = scandir($path);
		foreach ($files as $value) {
			if (empty($value) || is_dir($path.$value)) {
				continue;
			}
			$value=basename($value);
			$wplc_ringtones[$value] = str_replace('_', ' ', pathinfo($value, PATHINFO_FILENAME));
		}
		return $wplc_ringtones;
	}

}