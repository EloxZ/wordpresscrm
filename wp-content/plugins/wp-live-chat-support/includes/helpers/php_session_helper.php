<?php


class TCXPhpSessionHelper {

	public static function clean_session($sessionHandled = false) {
		if(!$sessionHandled) {
			self::start_session();
		}
		$s = $_SESSION;
		foreach($s as $k=>$v) {
			if (substr($k,0,5)=='wplc_') {
				unset($_SESSION[$k]);
			}
		}
		session_regenerate_id(); // is this really needed?
		if(!$sessionHandled) {
			self::close_session();
		}
	}

	public static function start_session() {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			$currentCookieParams = session_get_cookie_params();
			if (PHP_VERSION_ID >= 70300) {
				session_set_cookie_params([
					'lifetime' =>  $currentCookieParams["lifetime"],
					'path' => '/',
					'secure' => is_ssl()?"1":"0",
					'httponly' => "1",
					'samesite' => 'None',
				]);
			} else {
				session_set_cookie_params(
					$currentCookieParams["lifetime"],
					'/; samesite=None',
					'',
					is_ssl()?"1":"0"
				);
			}
			session_start();
		}
	}

	public static function close_session() {
		if (session_status() == PHP_SESSION_ACTIVE) {
			session_write_close();
		}
	}

	public static function set_session($cid) {
		if (!empty($cid)) {
			self::clean_session();
			self::start_session();
			$_SESSION['wplc_session_chat_session_id'] = intval($cid);
			$_SESSION['wplc_session_chat_session_active'] = 1;
			self::close_session();
			return $cid;
		}
	}

}