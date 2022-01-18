<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wplc_images     = 'jpg|jpeg|png|gif|bmp';
$wplc_multimedia = 'mp4|mp3|mpeg|ogg|ogm|ogv|webm|avi|wav|mov';
$wplc_documents  = 'doc|docx|xls|xlsx|pub|pubx|pdf|csv|txt';
//$wplc_others='zip|rar|7z|gz|tgz';
$wplc_allowed_extensions = $wplc_images . '|' . $wplc_multimedia . '|' . $wplc_documents; //.'|'.$wplc_others;


add_filter( "wplc_filter_mail_body", "wplc_filter_control_mail_body", 10, 2 );
function wplc_filter_control_mail_body( $header, $msg ) {
	$primary_bg_color = apply_filters( "wplc_mailer_bg_color", "#0596d4" ); //Default orange
	$body             = '
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">      
    <html>
    
    <body>



        <table id="" border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: sans-serif;">
        <tbody>
          <tr>
            <td width="100%" style="padding: 30px 20px 100px 20px;">
              <table cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate;">
                <tbody>
                  <tr>
                    <td style="padding-bottom: 20px;">
                      
                      <p>' . $header . '</p>
                      <hr>
                    </td>
                  </tr>
                </tbody>
              </table>

              <table id="" cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; font-size: 14px;">
              <tbody>
                  <tr>
                    <td class="sortable-list ui-sortable">
                        ' . nl2br( $msg ) . '
                    </td>
                  </tr>
                </tbody>
              </table>

              <table cellpadding="0" cellspacing="0" class="" width="100%" style="border-collapse: separate; max-width:100%;">
                <tbody>
                  <tr>
                    <td style="padding-top:20px;">
                      <table border="0" cellpadding="0" cellspacing="0" class="" width="100%">
                        <tbody>
                          <tr>
                            <td id="">
                             <p>' . site_url() . '</p>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>


        
        </div>
    </body>
</html>
            ';

	return $body;
}

add_filter( "wplc_mailer_bg_color", "wplc_fitler_mailer_bg_color", 10, 1 );
function wplc_fitler_mailer_bg_color( $default_color ) {
	$wplc_settings = TCXSettings::getSettings();

	if ( isset( $wplc_settings->wplc_settings_base_color ) ) {
		$default_color = "#" . $wplc_settings->wplc_settings_base_color;
	}

	return $default_color;
}



//node server


function wplc_force_bool( $arr, $key, $default = false ) {
	if ( isset( $arr[ $key ] ) ) {
		return boolval( $arr[ $key ] );
	}
	if ( is_array( $default ) ) {
		return $default[ $key ];
	}

	return $default;
}

if( !function_exists('boolval')) {
	function boolval($var){
		return !! $var;
	}
}

