<?php
/**
 * Single Quote Template
 *
 * The Single Quote Portal Page 
 *
 * @author 		ZeroBSCRM
 * @package 	Templates/Portal/Quote
 * @see			https://kb.jetpackcrm.com/
 * @version     3.0
 * 
 */

// Don't allow direct access
if ( ! defined( 'ABSPATH' ) ) exit; 

// Enqueuement
do_action( 'zbs_enqueue_scripts_and_styles' );


// retrieve endpoint
$quote_endpoint = 'quotes';
if ( function_exists( 'zeroBSCRM_clientPortalgetEndpoint' ) ){
	$quote_endpoint = zeroBSCRM_clientPortalgetEndpoint('quotes');
}

// retrieve hash or ID
$quote_id_or_hash = sanitize_text_field( get_query_var( $quote_endpoint ) );

// if not allowed to view quote, die
if ( !jpcrm_can_current_user_view_quote( $quote_id_or_hash ) ) {
	
	$err = '<center>';
	$err .= '<h3>'.__('Error loading quote','zero-bs-crm').'</h3>';
	$err .= __('Either this quote does not exist or you do not have permission to view it.', 'zero-bs-crm');
	$err .= '</center>';
	echo $err;
	
	die();

}

$quote_hash = ''; 
$quote_id = -1;

// discern if hash or id
if ( substr( $quote_id_or_hash, 0, 3 ) == 'zh-'){
	
	// definitely hash
	$quote_hash 		= substr( $quote_id_or_hash, 3 );
	$hashOK 			= zeroBSCRM_quotes_getFromHash( $quote_hash, -1 );
	$quote_id 			= $hashOK['data']['ID'];
	$show_navigation 	= false;
	$full_width 		= true;

} else {

	// probably ID
	$quote_id 			= (int)$quote_id_or_hash;
	$show_navigation 	= true;
	$full_width			= false;

}

// permalink check
if ( isset( $_GET['zbsid'] ) ){

	$zbsClientID 	= (int)$_GET['zbsid'];
	$portal_warning = __("You are using PLAIN permalinks. Please switch to %postname% for the proper Client Portal experience. Some features may not work in plain permalink mode","zero-bs-crm"); 

}


?>
<style>
.zerobs-proposal-body{
    font-size: 16px;
    background: #FFFFFF;
    box-shadow: 0px 1px 2px 0 rgba(34,36,38,0.15);
    margin: 1rem 0em;
    padding: 20px;
    border-radius: 0.28571429rem;
    border: 1px solid rgba(34,36,38,0.15);
    margin-top: -32px;
}
.zerobs-proposal-body li, .zerobs-proposal-body li span{
	padding:5px;
	line-height: 18px;
}
.zerobs-proposal-body table td, table tbody th {
    border: 1px solid #ddd;
    padding: 8px;
    font-size: 16px;
}
.zerobs-proposal-body ul{
	padding-left:20px;
}
</style>


<div id="zbs-main" class="zbs-site-main">
	<div class="zbs-client-portal-wrap main site-main zbs-post zbs-hentry">
		<?php

		// Show navigation?
		if ( $show_navigation ){
			zeroBS_portalnav($quote_endpoint);
		}

		?><div class="zbs-portal-wrapper zbs-portal-quote-single<?php if ( $full_width ){ echo " fullW"; } ?>"><?php

			// if viewing as admin
		 	if ( zeroBSCRM_isZBSAdminOrAdmin() ){  
		 		if ( isset( $portal_warning ) ) {		 		
		 			jpcrm_portal_viewing_as_admin( $portal_warning );
		 		}
			} 

			// output quote html
			echo jpcrm_portal_single_quote( $quote_id, $quote_hash ); 

		?></div>

		<div style="clear:both"></div>
		<?php zeroBSCRM_portalFooter(); ?>
	</div>
</div>