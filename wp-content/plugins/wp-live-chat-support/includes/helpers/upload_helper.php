<?php

class TCXUploadHelper {
	/**
	 * Checks if the file contains an extension which is allowed safe
	 *
	 * @param $filename
	 *
	 * @return false|int
	 */
	public static function check_file_name_for_safe_extension($filename) {
		global $wplc_allowed_extensions;
		return preg_match('/^.*\\.('.$wplc_allowed_extensions.')$/i', $filename);
	}

	/**
	 * Check the mime type if possible on this server
	 *
	 * @param $filepath
	 *
	 * @return bool
	 */
	public static function check_file_mime_type($filepath) {

		$mime = false;
		if (file_exists($filepath)){
			if (class_exists('finfo')) { // best option is using fileinfo class
				$result = new finfo();
				if (is_resource($result) === true) {
					$mime = $result->file(realpath($filepath), FILEINFO_MIME_TYPE);
				}
			}
			if ($mime === false)  { // fallback on mime_content_type()
				if (function_exists('mime_content_type')) {
					$mime = mime_content_type($filepath);
				}
			}
		}

		if ($mime !== false) {
			//We have managed to pull the mime type
			$allowed_mime_types = array(
				'image/gif', 'image/jpeg', 'image/tiff', 'image/bmp','image/png',
				'audio/aac', 'video/x-msvideo', 'video/mpeg','audio/wav', 'audio/mpeg', 'video/webm', 'audio/ogg', 'video/ogg',
				'application/pdf', 'text/csv', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/plain',
				'application/zip', 'application/gzip', 'application/x-rar-compressed', 'application/x-7z-compressed'
			);
			return in_array($mime, $allowed_mime_types);
		}
		return false;
	}
}
