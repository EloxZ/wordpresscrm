<?php


class TCXEncryptHelper {

	public static function decrypt($input)
	{
		$wplc_settings = TCXSettings::getSettings();
		$key = substr($wplc_settings->wplc_encryption_key.$wplc_settings->wplc_encryption_key,0,64);
		$cipherSplit = explode( " ", $input);
		$originalSize = intval($cipherSplit[0]);
		$iv = cryptoHelpers::toNumbers($cipherSplit[1]);
		$cipherText = $cipherSplit[2];
		$cipherIn = cryptoHelpers::toNumbers($cipherText);
		$keyAsNumbers = cryptoHelpers::toNumbers(bin2hex($key));
		$keyLength = count($keyAsNumbers);

		$decrypted = AES::decrypt(
			$cipherIn,
			$originalSize,
			AES::modeOfOperation_CBC,
			$keyAsNumbers,
			$keyLength,
			$iv
		);

		$hexDecrypted = cryptoHelpers::toHex($decrypted);
		return pack("H*" , $hexDecrypted);
	}

	public static function encrypt($input)
	{
		$wplc_settings = TCXSettings::getSettings();
		$key = substr($wplc_settings->wplc_encryption_key.$wplc_settings->wplc_encryption_key,0,64);
		$inputData = cryptoHelpers::convertStringToByteArray($input);
		$keyAsNumbers = cryptoHelpers::toNumbers(bin2hex($key));
		$keyLength = count($keyAsNumbers);
		$iv = cryptoHelpers::generateSharedKey(16);

		$encrypted = AES::encrypt(
			$inputData,
			AES::modeOfOperation_CBC,
			$keyAsNumbers,
			$keyLength,
			$iv
		);

		$retVal = $encrypted['originalsize']." ".cryptoHelpers::toHex($iv)." ".cryptoHelpers::toHex($encrypted['cipher']);
		return $retVal;

	}


}