<?php
/**
 * Single Invoice Template
 *
 * The single invoice template
 *
 * @author 		ZeroBSCRM
 * @package 	Templates/Portal/Invoice
 * @see			https://kb.jetpackcrm.com/
 * @version     3.0
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access

do_action( 'zbs_enqueue_scripts_and_styles' );
//zeroBS_portal_enqueue_stuff();

	//moved into func
    if(function_exists('zeroBSCRM_clientPortalgetEndpoint')) {
        $invoice_endpoint = zeroBSCRM_clientPortalgetEndpoint('invoices');
		}
    else {
      $invoice_endpoint = 'invoices';
		}
    

	// v3.0 Hashes (or ID)
		$invIDOrHash = sanitize_text_field( get_query_var( $invoice_endpoint ) );
		// if not allowed to view invoice, die
		if ( !jpcrm_can_current_user_view_invoice($invIDOrHash) ) {
			$err = '<center>';
			$err .= '<h3>'.__('Error loading invoice','zero-bs-crm').'</h3>';
			$err .= __('Either this invoice does not exist or you do not have permission to view it.', 'zero-bs-crm');
			$err .= '</center>';
			echo $err;
			die();
		}
	$invHash = ''; $invID = -1;
	// discern if hash or id
	if (substr($invIDOrHash,0,3) == 'zh-'){
		
		// definitely hash
		$invHash 	= substr($invIDOrHash,3);
		$hashOK 	= zeroBSCRM_invoicing_getFromHash($invHash,-1);
		$invID 		= $hashOK['data']['ID'];
		$showNav 	= false;
		$fullWidth 	= true;

	} else {

		// probably ID
		$invID 		= (int)$invIDOrHash;
		$showNav 	= true;
		$fullWidth	= false;

	}

	// mikes perma check
	if(isset($_GET['zbsid'])){
			$zbsClientID 	= (int)$_GET['zbsid'];
			$zbsWarn = __("You are using PLAIN permalinks. Please switch to %postname% for the proper Client Portal experience. Some features may not work in plain permalink mode","zero-bs-crm"); 
	}


?>
<style>
.stripe-button-el{
    background: none !important;
    border: 0px !important;
    box-shadow: none !important;
}
.zbs-back-to-invoices a:hover{
	text-decoration:none;
}
</style>
<div id="zbs-main" class="zbs-site-main">
	<div class="zbs-client-portal-wrap main site-main zbs-post zbs-hentry">
		<div class='zbs-portal-wrapper zbs-portal-inv-single <?php if($fullWidth){ echo "fullW"; } ?>'>
			<?php
			if ($showNav){
				zeroBS_portalnav($invoice_endpoint);
			}

					?><div class='zbs-portal-wrapper-sin zbs-single-invoice-portal'><?php
							//now this function checks for the access rights (eventually in zeroBSCRM_invoice_generatePortalInvoiceHTML)
							zeroBSCRM_portal_single_invoice($invID, true); 
						?>
					</div>


			<div style="clear:both"></div>
			<?php zeroBSCRM_portalFooter(); ?>
		</div>
	</div>
</div>