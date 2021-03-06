<?php 
/*!
 * Single company view page
 */
defined( 'ZEROBSCRM_PATH' ) || exit;

global $zbs;

/**
 * Render the page
 */
function jpcrm_render_company_view_page( $id = -1 ){

  if (!empty($id) && $id > 0){

      global $zbs;


          $useQuotes = false; //not yet $useQuotes = zeroBSCRM_getSetting('feat_quotes');
          $useInvoices = zeroBSCRM_getSetting('feat_invs');
          $useTrans = zeroBSCRM_getSetting('feat_transactions');
          $useTasks = false; if ($zbs->isDAL3()) $useTasks = zeroBSCRM_getSetting('feat_calendar'); // v3+

          $args = array(
                      'withCustomFields'  => true,
                      'withQuotes'        => true,
                      'withInvoices'      => true,
                      'withTransactions'  => true,
                      'withLogs'          => true,
                      //'withLastLog'       => false,
                      'withTags'          => true,
                      'withOwner'         => true,
                      'withValues'        => true,
                      'withContacts'      => true,
                  );

          // get tasks if using
          if ($useTasks) $args['withTasks'] = true;

          #} Get screen options for user
          $screenOpts = $zbs->userScreenOptions();

          #} get our single company info
              //DAL3?
              if ($zbs->isDAL3())
                $zbsCompanyObj = $zbs->DAL->companies->getCompany($id,$args);
              else
                $zbsCompanyObj = zeroBS_getCompany($id,true);

          // if customer id provided, but no obj, don't load any further.
          // this matches the ghost-record treatment in the single edit.php class
          if (!is_array($zbsCompanyObj)){

                // brutal hide, then msg #ghostrecord
                ?><style type="text/css">#zbs-edit-save, #zbs-nav-view, #zbs-nav-prev, #zbs-nav-next { display:none; }</style>
                <div id="zbs-edit-warnings-wrap"><?php
                echo zeroBSCRM_UI2_messageHTML('warning','Error Retrieving '.jpcrm_label_company(),'There does not appear to be a '.jpcrm_label_company().' with this ID.','disabled warning sign','zbsCantLoadData');  
                ?></div><?php  
                return false;

          }

          // until DAL2 catches up with co, we need these lines to move ['meta'] into obj
          if ($zbs->isDAL3())
              $zbsCompany = $zbsCompanyObj;
          else {
              $zbsCompany = $zbsCompanyObj['meta'];
              $zbsCompany['id'] = $zbsCompanyObj['id'];
              $zbsCompany['created'] = $zbsCompanyObj['created'];
              $zbsCompany['name'] = $zbsCompanyObj['name'];
              $zbsCompany['transactions'] = array(); if (isset($zbsCompanyObj['transactions']) && is_array($zbsCompanyObj['transactions'])) $zbsCompany['transactions'] = $zbsCompanyObj['transactions'];
              $zbsCompany['invoices'] = array(); if (isset($zbsCompanyObj['invoices']) && is_array($zbsCompanyObj['invoices'])) $zbsCompany['invoices'] = $zbsCompanyObj['invoices'];
          }

          
          #} Get actions
          $companyActions = zeroBS_company_actions($id);

          #} PREP
          $companyEmail = ''; if (isset($zbsCompany['email'])) $companyEmail = $zbsCompany['email'];

          // values - DAL3 we get them passed all nicely :)
          $companyTotalValue = 0; if (isset($zbsCompany['total_value'])) $companyTotalValue = $zbsCompany['total_value'];
          $companyQuotesValue = 0; if (isset($zbsCompany['quotes_total'])) $companyQuotesValue = $zbsCompany['quotes_total'];
          $companyInvoicesValue = 0; if (isset($zbsCompany['invoices_total'])) $companyInvoicesValue = $zbsCompany['invoices_total'];
          $companyTransactionsValue = 0; if (isset($zbsCompany['transactions_total'])) $companyTransactionsValue = $zbsCompany['transactions_total'];

          // pre dal 3 did this way
          if (!$zbs->isDAL3()){

            // calc'd each individually
            // never used (pre dal3) $companyTotalValue = zeroBS_companyTotalValue($id, $zbsCompany['invoices'],$zbsCompany['transactions'])
            // never used (pre dal3) $companyQuotesValue = zeroBS_companyQuotesValue($id, $zbsCompany['quotes']);
            $companyInvoicesValue = zeroBS_companyInvoicesValue($id, $zbsCompany['invoices']);
            $companyTransactionsValue = zeroBS_companyTransactionsValue($id, $zbsCompany['transactions']);

          }

          // put screen options out
          zeroBSCRM_screenOptionsPanel();

        ?>

          <div class="ui divided grid" style="margin-top:-1em;">

            <div class="ten wide column" id="zbs-company-panel">

              <div class="ui segment grid">

              <?php # based on avatar/no avatar, subtle diff design here:
              // No avatars for co's yet (2.72) if ($avatarMode == "3" || empty($avatar)){
                
                  // 1 column, no avatar card
                  ?><div class="sixteen wide column zbs-view-card"><?php
              /*
              } else {

                  // normal, 2 column 'contact card'
                  ?><div class="three wide column" style="text-align:center">
                      <?php echo $avatar; ?>
                        <a class="ui button blue mini" style="margin-top:0.8em" href="<?php echo zbsLink('edit',$id,'zerobs_customer',false);?>">
                            <?php _e("Edit Contact", "zero-bs-crm"); ?>
                        </a>

                    </div>
                    <div class="thirteen wide column zbs-view-card"><?php


              }*/ ?>

                      <h3>
                        <?php echo zeroBS_companyName('',$zbsCompany,false,false); ?>
                        <?php #} When no avatar, show edit button top right
                          // no avatars yet for co - if ($avatarMode == "3" || empty($avatar)){
                            ?><a class="ui button blue mini right floated" style="margin-top:0.8em" href="<?php echo zbsLink('edit',$id,'zerobs_company',false);?>">
                                  <?php _e("Edit ".jpcrm_label_company(), "zero-bs-crm"); ?>
                              </a><?php
                          // no avatars yet for co - } 
                        ?>
                      </h3>
                      <?php /*<p class="zbs-email">
                        <?php zeroBSCRM_html_sendemailto($id,$contactEmail,false); ?>
                      </p> */ ?>
                      <p class="zbs-sentence">
                        <?php echo zeroBSCRM_html_companyIntroSentence($zbsCompany,$zbsCompanyObj); ?>
                      </p>


                      <?php # https://codepen.io/kyleshockey/pen/bdeLrE 
                      /* nope, none here yet if (count($companyActions) > 0) { ?>
                      <div class="action-wrap">
                        <div class="ui green basic dropdown action-button"><?php _e(jpcrm_label_company().' Actions',"zero-bs-crm"); ?><i class="dropdown icon"></i>
                           <div class="menu">
                            <?php foreach ($companyActions as $actKey => $action){ ?>
                               <div class="item zbs-company-action" id="zbs-company-action-<?php echo $actKey; ?>" data-action="<?php if (isset($action['url'])) echo 'url'; ?>" data-url="<?php if (isset($action['url'])) echo $action['url']; ?>">
                                 <?php 

                                    // got ico?
                                    if (isset($action['ico'])) echo '<i class="'.$action['ico'].'"></i>';

                                    // got text?
                                    if (isset($action['label'])) echo $action['label'];

                                ?>
                               </div>
                            <?php } ?>
                            </div>
                       </div>
                     </div>
                     <?php }  */?>


                  </div>
              </div>

              

              <!-- company vitals -->
              <?php

                // prep
                $statusStr = ''; if (isset($zbsCompany) && isset($zbsCompany['status']) && !empty($zbsCompany['status'])) $statusStr = $zbsCompany['status'];
              
                // compiled addr str
                $addrStr = ''; if (isset($zbsCompany)) $addrStr = zeroBS_companyAddr($zbsCompany['id'],$zbsCompany,'full','<br />');
                $addr2Str = ''; if (isset($zbsCompany)) $addr2Str = zeroBS_companySecondAddr($zbsCompany['id'],$zbsCompany,'full','<br />');

                // tels?
                $tels = array(); 
                if (isset($zbsCompany) && isset($zbsCompany['maintel']) && !empty($zbsCompany['maintel'])) $tels['maintel'] = $zbsCompany['maintel'];
                if (isset($zbsCompany) && isset($zbsCompany['sectel']) && !empty($zbsCompany['sectel'])) $tels['sectel'] = $zbsCompany['sectel'];

                /* 
                // socials
                global $zbsSocialAccountTypes;
                $zbsSocials = zeroBS_getCustomerSocialAccounts($id);
                  // empty empties.. hmmm
                  $zbsSocialsProper = array(); if (is_array($zbsSocials) && count($zbsSocials) > 0) foreach ($zbsSocials as $zbsSocialKey => $zbsSocialAcc) if (!empty($zbsSocialAcc)) $zbsSocialsProper[$zbsSocialKey] = $zbsSocialAcc;
                  $zbsSocials = $zbsSocialsProper; unset($zbsSocialsProper);

                */


                // retrieve any additional tabs peeps have prepared
                $zbsCompanyVitalTabs = apply_filters( 'jetpack-crm-company-vital-tabs', array(), $id );

              ?>

              <div id="zbs-vitals-box">
                <div class="ui top attached tabular menu">
                  <div data-tab="vitals" class="<?php if (!isset($activeVitalsTab)) { echo 'active '; $activeVitalsTab = 'vitals'; } ?>item"><?php 

                      echo jpcrm_label_company().' '.__("Vitals","zero-bs-crm"); 

                      ?></div>
                  <?php /* if (count($zbsSocialAccountTypes) > 0 && count($zbsSocials) > 0){ ?>
                    <div data-tab="social" class="<?php if (!isset($activeVitalsTab)) { echo 'active '; $activeVitalsTab = 'social'; } ?>item"><?php _e('Social',"zero-bs-crm"); ?></div>                      
                  <?php } */ ?>
                  <?php #} Any integrated tabs - via filter jetpack-crm-contact-vital-tabs
                  if (is_array($zbsCompanyVitalTabs) && count($zbsCompanyVitalTabs) > 0){
                    $tabIndx = 1;
                    foreach ($zbsCompanyVitalTabs as $tab){

                      $tabName = __('Untitled Tab',"zero-bs-crm");
                      $tabID = 'zbs-company-tab-'.$tabIndx;

                      if (is_array($tab) && isset($tab['name'])) $tabName = $tab['name'];
                      if (is_array($tab) && isset($tab['id'])) $tabID = $tab['id'];
                      
                      ?><div data-tab="<?php echo $tabID; ?>" class="item"><?php echo $tabName; ?></div><?php

                      $tabIndx++;

                    }

                  } ?>
                  <?php if (!empty($statusStr)) { ?>
                  <div class="right menu item">
                    <?php _e("Status","zero-bs-crm");?>: 
                    <span class="ui green label"><?php echo $statusStr; ?></span>
                  </div>
                  <?php } ?>
                </div>

                <div class="ui bottom attached active tab segment" data-tab="vitals" id="zbs-company-view-vitals">
                    <table class="ui fixed single line celled table">
                      <tbody>
                        <?php if ($zbs->isDAL3()){
                          if ( $useInvoices == "1" || $useTrans == "1" ): ?>
                          <tr>
                            <td class="zbs-view-vital-label"><strong><?php _e("Total Value","zero-bs-crm"); ?><i class="circle info icon link" data-content="<?php _e("Total Value is all transaction types and any unpaid invoices","zero-bs-crm");?>" data-position="bottom center"></i></strong></td>
                            <td><strong><?php echo zeroBSCRM_formatCurrency($companyTotalValue); ?></strong></td>
                          </tr>
                          <?php endif; ?>
                          <?php if ($useQuotes == "1"): ?>
                          <tr>
                            <td class="zbs-view-vital-label"><?php _e("Quotes","zero-bs-crm"); ?> <i class="circle info icon link" data-content="<?php _e("Quotes: This shows the total sum of your quotes & count.","zero-bs-crm");?>" data-position="bottom center"></i></td>
                            <td>
                              <?php if (count($zbsCompany['quotes']) > 0)
                                      echo zeroBSCRM_formatCurrency($companyQuotesValue).' ('.count($zbsCompany['quotes']).')';
                                    else
                                      _e('None',"zero-bs-crm"); ?>
                            </td>
                          </tr>
                          <?php endif;
                        } // if dal3
                        if ($useInvoices == "1"): ?>
                        <tr class="zbs-view-vital-invoices">
                          <td class="zbs-view-vital-label"><?php _e("Invoices","zero-bs-crm"); ?> <i class="circle info icon link" data-content="<?php _e("Invoices: This shows the total sum of your invoices & count.","zero-bs-crm");?>" data-position="bottom center"></i></td>
                          <td>
                            <?php if (count($zbsCompany['invoices']) > 0)
                                    echo zeroBSCRM_formatCurrency($companyInvoicesValue).' ('.count($zbsCompany['invoices']).')';
                                  else
                                    _e('None',"zero-bs-crm"); ?>
                          </td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($useTrans == "1"): ?>
                        <tr>
                          <td class="zbs-view-vital-label"><?php _e("Transactions","zero-bs-crm"); ?> <i class="circle info icon link" data-content="<?php _e("Transactions Total & count: This shows the sum of your succeeded transactions (set in settings)","zero-bs-crm");?>" data-position="bottom center"></i></td>
                          <td>
                            <?php if (count($zbsCompany['transactions']) > 0)
                                    echo zeroBSCRM_formatCurrency($companyTransactionsValue).' ('.count($zbsCompany['transactions']).')';
                                  else
                                    _e('None',"zero-bs-crm"); ?>
                          </td>
                        </tr>
                        <?php endif; ?>
                        <tr class="wraplines">
                          <td class="zbs-view-vital-label"><?php _e("Address Details","zero-bs-crm");?></td>
													<td>
														<?php
															if (!empty($addrStr) && empty($addr2Str)) {
																echo $addrStr;
															} elseif (empty($addrStr) && !empty($addr2Str)) {
																echo $addr2Str;
															} elseif (!empty($addrStr) && !empty($addr2Str)) {
																?>
																<div class="ui grid">
																	<div class="eight wide column">
																		<h4 class="ui dividing header" style="margin-bottom: 0.6em;"><?php _e('Main address',"zero-bs-crm"); ?></h4>
																		<?php echo $addrStr; ?>
																	</div>
																	<div class="eight wide column">
																		<h4 class="ui dividing header" style="margin-bottom: 0.6em;"><?php _e('Secondary address',"zero-bs-crm"); ?></h4>
																		<?php echo $addr2Str; ?>
																	</div>
																</div>
																<?php
															} else {
																_e('No Address on File',"zero-bs-crm");
															}
														?>
													</td>
                        </tr>
                        <tr>
                          <td class="zbs-view-vital-label"><?php _e("Telephone Contacts","zero-bs-crm");?></td>
                          <td>
                            <?php 


                                  if (count($tels) > 0){

                                    // Click 2 call?
                                    $click2call = $zbs->settings->get('clicktocall');

                                    ?><div class="ui horizontal list"><?php

                                      foreach ($tels as $telKey => $telNo){ ?>
                                      <div class="item">
                                        <?php switch ($telKey){

                                          case 'sectel': 
                                              echo '<i class="large phone icon"></i>';
                                              break;
                                          case 'maintel':
                                              echo '<i class="large phone square icon"></i>'; 
                                              break;

                                        } ?>
                                        <div class="content">
                                          <?php if ($click2call == "1") { ?>
                                          <a class="ui small button" href="<?php echo zeroBSCRM_clickToCallPrefix().$telNo; ?>" title="<?php _e('Call',"zero-bs-crm").' '.$telNo; ?>"><?php echo $telNo; ?></a>
                                          <?php } else { ?>
                                          <div class="header"><?php echo $telNo; ?></div>
                                          <?php } ?>
                                        </div>
                                      </div>
                                      <?php } ?>

                                    </div><?php


                                  } else _e('No phone number on File',"zero-bs-crm"); ?></td>
                        </tr>
                        <tr class="wraplines">
                          <td class="zbs-view-vital-label"><?php _e("Contacts","zero-bs-crm"); ?> <i class="circle info icon link" data-content="<?php _e("Contacts at this ".jpcrm_label_company(),"zero-bs-crm");?>" data-position="bottom center"></i></td>
                          <td id="zbs-company-view-vitals-contacts">
                            <?php 

                              // contacts at company
                              $contactStr = zeroBSCRM_html_linkedCompanyContacts($id,(isset($zbsCompanyObj['contacts']) ? $zbsCompanyObj['contacts'] : false));

                              if (!empty($contactStr))
                                echo $contactStr;
                              else
                                _e('None',"zero-bs-crm");

                            ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="zbs-view-vital-label"><?php _e("Source","zero-bs-crm");?></td>
                          <td>
                            <?php
                            $zeroBSCRMsource = zeroBS_getExternalSource($id,ZBS_TYPE_COMPANY);
                            //MS? not sure what import meant to be here: if (isset($zeroBSCRMsource) && isset($zeroBSCRMsource['meta']) && isset($zeroBSCRMsource['meta']['import'])) echo $zeroBSCRMsource['meta']['import'];
                            if (isset($zeroBSCRMsource) && isset($zeroBSCRMsource['source']) && isset($zeroBSCRMsource['uid'])){


                                $uid = $zeroBSCRMsource['uid'];

                                // company + CSV means uid will be a useless hash, so replace that with name if we have
                                if (isset($zbsCompany['name'])) 
                                  $uid = $zbsCompany['name'];
                                else
                                  $uid = __('Imported based on name','zero-bs-crm');


                                echo zeroBS_getExternalSourceTitle($zeroBSCRMsource['source'],$uid);
                            } else
                              _e('Manually Added',"zero-bs-crm");

                             ?></td>
                        </tr>
                      </tbody>
                    </table>

                </div>

                  <?php #} Any integrated tabs - via filter jetpack-crm-contact-vital-tabs
                  if (is_array($zbsCompanyVitalTabs) && count($zbsCompanyVitalTabs) > 0){
                    $tabIndx = 1;
                    foreach ($zbsCompanyVitalTabs as $tab){

                      $tabID = 'zbs-company-tab-'.$tabIndx;
                      if (is_array($tab) && isset($tab['id'])) $tabID = $tab['id'];
                      
                      ?><div class="ui bottom attached tab segment" data-tab="<?php echo $tabID; ?>" id="zbs-contact-view-vitals-<?php echo $tabID; ?>">
                      <?php #} Content
                          if (is_array($tab) && isset($tab['contentaction'])){

                              // calls the users function name, if they opted for that instead of content
                              call_user_func($tab['contentaction'],$id);

                          } else if (is_array($tab) && isset($tab['content'])) echo $tab['content']; ?>
                      </div><?php

                      $tabIndx++;

                    }

                  } ?>                      
                <!-- / company vitals -->
              </div>
        







                  <h4 class="ui horizontal header divider">
                    <i class="archive icon"></i>
                    <?php _e('Documents',"zero-bs-crm"); ?>
                  </h4>

                  <div id="zbs-doc-menu">
                    <div class="ui top attached tabular menu">
                      <?php /* never, yet! if ($useQuotes == "1"){ ?><div data-tab="quotes" class="<?php if (!isset($activeTab)) { echo 'active '; $activeTab = 'quotes'; } ?>item"><?php _e('Quotes',"zero-bs-crm"); ?></div><?php } ?>*/ ?>
                      <?php if ($useInvoices == "1"){ ?><div data-tab="invoices" class="<?php if (!isset($activeTab)) { echo 'active '; $activeTab = 'invoices'; } ?>item"><?php _e('Invoices',"zero-bs-crm"); ?></div><?php } ?>                      
                      <?php if ($useTrans == "1"){ ?><div data-tab="transactions" class="<?php if (!isset($activeTab)) { echo 'active '; $activeTab = 'transactions'; } ?>item"><?php _e('Transactions',"zero-bs-crm"); ?></div><?php } ?>
                      <div data-tab="files" class="<?php if (!isset($activeTab)) { echo 'active '; $activeTab = 'files'; } ?>item"><?php _e('Files','zero-bs-crm'); ?></div>                    
                      <?php if ($useTasks == "1"){ ?><div data-tab="tasks" class="<?php if (!isset($activeTab)) { echo 'active '; $activeTab = 'tasks'; } ?>item"><?php _e('Tasks',"zero-bs-crm"); ?></div><?php } ?>
                    </div>

                    <?php if ($useInvoices == "1"){ ?>
                    <div class="ui bottom attached <?php if ($activeTab == 'invoices') echo 'active '; ?>tab segment" data-tab="invoices">
                        <table class="ui celled table unstackable">
                              <thead>
                                <tr>
                                  <th><?= $zbs->settings->get('reflabel') ?></th>
                                  <th><?php _e("Date","zero-bs-crm"); ?></th>
                                  <th><?php _e("Amount","zero-bs-crm"); ?></th>
                                  <th><?php _e("Status","zero-bs-crm"); ?></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                // prep link to create a new invoice
                                $new_invoice_url = zbsLink('create',-1,ZBS_TYPE_INVOICE).'&prefillco='.$zbsCompany['id'];
                                
                                if (count($zbsCompany['invoices']) > 0){

                                  foreach($zbsCompany['invoices'] as $invoice){
                                    // debugecho '<pre>'; print_r($invoice); echo '</pre><hr>';

                                    $idRefStr = ''; 
                                    // DAL3 change of field name
                                    if ($zbs->isDAL3()){

                                        // 3.0
                                        if (isset($invoice['id'])) $idRefStr = '#'.$invoice['id'];
                                        if (isset($invoice['id_override'])) {
                                          if (!empty($idRefStr)) $idRefStr .= ' -';
                                          $idRefStr .= ' '.$invoice['id_override'];
                                        }

                                        $invoiceURL = zbsLink('edit',$invoice['id'],ZBS_TYPE_INVOICE);

                                        $invoiceVal = $invoice['total'];

                                        $invoiceStatus = $invoice['status'];

                                    } else {

                                        // <3.0
                                        if (isset($invoice['zbsid'])) $idRefStr = '#'.$invoice['zbsid'];
                                        if (isset($invoice['meta']) && isset($invoice['meta']['ref'])) {
                                          if (!empty($idRefStr)) $idRefStr .= ' -';
                                          $idRefStr .= ' '.$invoice['meta']['ref'];
                                        }

                                        $invoiceURL = zbsLink('edit',$invoice['id'],ZBS_TYPE_INVOICE);//admin_url('post.php?action=edit&post='.$invoice['id']);

                                        $invoiceVal = $invoice['meta']['val'];

                                        $invoiceStatus = $invoice['meta']['status'];

                                    }
                                    
                                    echo "<tr>";
                                      echo '<td><a href="'.$invoiceURL.'">' . $idRefStr . "</a></td>";
                                      echo "<td>" . zeroBSCRM_html_InvoiceDate($invoice) . "</td>";
                                      echo "<td>" . zeroBSCRM_formatCurrency($invoiceVal) . "</td>";
                                      echo "<td><span class='".zeroBSCRM_html_invoiceStatusLabel($invoice)."'>" . ucfirst($invoiceStatus) . "</span></td>";
                                    echo "</tr>"; 
                                  }
                                } else {

                                  // empty, create?
                                  ?><tr>
                                      <td colspan="4">
                                          <div class="ui info icon message" id="zbsNoInvoiceResults">
                                            <div class="content">
                                              <div class="header"><?php _e('No Invoices',"zero-bs-crm"); ?></div>
                                              <p>
                                                <?php
                                                //prefill doesn't yet exist for companies
                                                echo sprintf(__('This %s does not have any invoices yet. Do you want to <a href="%s">create one</a>?',"zero-bs-crm"),jpcrm_label_company(), $new_invoice_url);
                                                ?>
                                              </p>
                                            </div>
                                          </div>
                                      </td>
                                    </tr><?php

                                }

                                ?>

                              </tbody>
                            </table>
                            <?php if (count($zbsCompany['invoices']) > 0) : ?>
                              <div style="text-align: right;">
                                <a href="<?php echo $new_invoice_url ?>" class="ui basic green button">
                                  <i class="plus square outline icon"></i>
                                  <?php _e('Add Invoice','zero-bs-crm') ?>
                                </a>
                              </div>
                            <?php endif; ?>
                    </div><?php } ?>
                    <div class="ui bottom attached <?php if ($activeTab == 'transactions') echo 'active '; ?>tab segment" data-tab="transactions">
                        <?php

                        // get columns from screen options
                        $activeTransactionColumns = array('date','id','total','status'); // default
                        if (
                            isset($screenOpts) && is_array($screenOpts) 
                              && isset($screenOpts['tablecolumns']) && is_array($screenOpts['tablecolumns']) 
                                && isset($screenOpts['tablecolumns']['transactions']) 
                                && is_array($screenOpts['tablecolumns']['transactions']) 
                                && count($screenOpts['tablecolumns']['transactions']) > 0
                          ) $activeTransactionColumns = $screenOpts['tablecolumns']['transactions'];
                        ?>
                        <table class="ui celled table unstackable">
                              <thead>
                                <tr>
                                <?php 

                                // for now, pick out id so always on left
                                if (in_array('id', $activeTransactionColumns)) echo '<th>'.zeroBS_objDraw_transactionColumnHeader('id').'</th>';

                                foreach ($activeTransactionColumns as $col){ 

                                  // id pulled out above
                                  if ($col != 'id') echo '<th>'.zeroBS_objDraw_transactionColumnHeader($col).'</th>'; 

                                } ?>
                                </tr>
                              </thead>
                              <tbody>
                                <?php

                                // prep link to create a new transaction
                                $new_transaction_url = zbsLink('create',-1,ZBS_TYPE_TRANSACTION).'&prefillco='.$zbsCompany['id'];

                                if (count($zbsCompany['transactions']) > 0){

                                  foreach($zbsCompany['transactions'] as $zbsTransaction){

                                    echo "<tr>";
                                
                                      // ultimately these should be drawn by JS so they can use the same 
                                      // 'generate obj html' funcs as list view
                                      // for now quickly generated in php for this freelance.

                                        // for now, pick out id so always on left
                                        if (in_array('id', $activeTransactionColumns)) echo '<td>'.zeroBS_objDraw_transactionColumnTD('id',$zbsTransaction).'</td>';

                                        foreach ($activeTransactionColumns as $col){ 

                                          // id pulled out above
                                          if ($col != 'id') echo '<td>'.zeroBS_objDraw_transactionColumnTD($col,$zbsTransaction).'</td>';

                                        }

                                    echo "</tr>";
                                  }
                                } else {

                                  // empty, create?
                                  ?><tr>
                                      <td colspan="<?php echo count($activeTransactionColumns); ?>">
                                          <div class="ui info icon message" id="zbsNoTransactionResults">
                                            <div class="content">
                                              <div class="header"><?php _e('No Transactions',"zero-bs-crm"); ?></div>
                                              <p>
                                                <?php
                                                //prefill doesn't yet exist for companies
                                                echo sprintf(__('This %s does not have any transactions yet. Do you want to <a href="%s">create one</a>?',"zero-bs-crm"),jpcrm_label_company(), $new_transaction_url);
                                                ?>
                                              </p>
                                            </div>
                                          </div>
                                      </td>
                                    </tr><?php

                                }

                                ?>

                              </tbody>
                            </table>
                            <?php if (count($zbsCompany['transactions']) > 0) : ?>
                              <div style="text-align: right;">
                                <a href="<?php echo $new_transaction_url ?>" class="ui basic green button">
                                  <i class="plus square outline icon"></i>
                                  <?php _e('Add Transaction','zero-bs-crm') ?>
                                </a>
                              </div>
                            <?php endif; ?>
                    </div>

                    <div class="ui bottom attached tab segment" data-tab="files">
                        <table class="ui celled table unstackable" id="zbsFilesTable" style="margin-bottom:0;">
                          <thead>
                            <tr>
                              <th><?php _e("Info", 'zero-bs-crm');?></th>
                              <th class="center aligned"><?php _e("View File", 'zero-bs-crm');?></th>
                              <th class="center aligned" style="min-width:230px"><?php _e("Actions", 'zero-bs-crm');?></th>
                            </tr>
                          </thead>
                          <tbody>
                        <?php
                          //$zbsFiles = zeroBSCRM_getCustomerFiles($id); $hasFiles = false;
                          $zbsFiles = zeroBSCRM_files_getFiles('company',$id);
                          $hasFiles = false;
                          
                          // prep link to create a new file
                          $new_file_url = zbsLink('edit',$id,'zerobs_company',false).'#zerobs-company-files-head';

                          #} Any files
                          if (is_array($zbsFiles) && count($zbsFiles) > 0){ 

                            $hasFiles = true;

                            $fileLineIndx = 0; foreach($zbsFiles as $zbsFile){                              

                              //$fileFullname = basename($zbsFile['file']);
                              //$file = substr($fileFullname,strpos($fileFullname, '-')+1);
                              $file = zeroBSCRM_files_baseName($zbsFile['file'],isset($zbsFile['priv']));
                              ?><tr>
                                <td>
                                  <h4><?php if (isset($zbsFile['title'])) echo $zbsFile['title']; else echo __('Untitled','zero-bs-crm'); ?></h4>
                                  <p>
                                    <?php if (isset($zbsFile['desc'])) echo $zbsFile['desc']; ?>
                                  </p>
                                  <em>(<?php echo $file; ?>)</em>
                                </td>

                                      <td class="center aligned">
                                          <?php 
                                              echo '<a class="" href="'.$zbsFile['url'].'" target="_blank" class="ui button basic">'.__("View","zero-bs-crm") .'</a>';
                                              
                                              ?>
                                      </td>

                                      <td class="center aligned">
                                        <?php
                                        $zbs_edit = esc_url(admin_url('admin.php?page='.$zbs->slugs['editfile']) . "&company=".$id."&fileid=" . $fileLineIndx  );   
                                        ?>
                                        <a href="<?php echo $zbs_edit;?>" target="_blank" class="ui button basic"><i class="edit icon"></i><?php _e("Edit","zero-bs-crm"); ?></a>&nbsp;&nbsp;
                                        <button class="zbsDelFile ui button basic" data-type="company" data-delurl="<?php echo $zbsFile['url']; ?>"><i class="trash alternate icon"></i><?php _e("Delete","zero-bs-crm"); ?></button>
                                      </td>
                              </tr><?php
           
                              $fileLineIndx++;
                            } //end of the files loop.. 


                          } 

                          // put this out either way, so that if a user deletes all it can be reshown in ui

                              // empty, create?
                              ?><tr id="zbs-no-files-msg" style="display:<?php if (!$hasFiles) echo 'table-row'; else echo 'none'; ?>">
                                  <td colspan="4">
                                      <div class="ui info icon message" id="zbsNoFileResults">
                                        <div class="content">
                                          <div class="header"><?php _e('No Files',"zero-bs-crm"); ?></div>
                                          <p>
                                            <?php
                                            echo sprintf(__('This %s does not have any files yet. Do you want to <a href="%s">upload one</a>?',"zero-bs-crm"),jpcrm_label_company(), $new_file_url);
                                            ?>
                                          </p>
                                        </div>
                                      </div>
                                  </td>
                                </tr><?php

                        ?>
                        </tbody>
                        </table>
                        <div id="zbsFileActionOutput" style="display:none"></div>
                      </div>

                      <?php if ($useTasks == "1"){ ?>
                      <div class="ui bottom attached <?php if ($activeTab == 'tasks') echo 'active '; ?>tab segment" data-tab="tasks">
                          <table class="ui celled table unstackable">
                                <thead>
                                  <tr>
                                    <th><?php _e("Date","zero-bs-crm"); ?></th>
                                    <th><?php _e("Task","zero-bs-crm"); ?></th>
                                    <th><?php _e("Status","zero-bs-crm"); ?></th>
                                    <th><?php _e("View","zero-bs-crm"); ?></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  // prep link to create a new task
                                  $new_task_url = zbsLink('create',-1,ZBS_TYPE_EVENT).'&prefillco='.$zbsCompany['id'];
                                  
                                  if (isset($zbsCompany['tasks']) && is_array($zbsCompany['tasks']) && count($zbsCompany['tasks']) > 0){

                                    $lastTaskStart = -1; $upcomingOutput = false;

                                    foreach ($zbsCompany['tasks'] as $task){

                                      // if the first task is upcoming, add a header
                                      if (!$upcomingOutput && $task['start'] > time()){

                                        // tried to use horizontal divider here, but there's a semantic bug
                                        // ... when using these in tables. https://semantic-ui.com/elements/divider.html
                                        // ... adding display:block to the td fixes, but then colspan doesn't work. Skipping for now
                                        echo '<tr><td colspan="4"><div class="ui horizontal divider">'.__('Upcoming Tasks','zero-bs-crm').'</div></td></tr>';

                                        // shown
                                        $upcomingOutput = true;

                                      }

                                      // if there are tasks in future, and past, draw a line between
                                      if ($lastTaskStart > 0 && $lastTaskStart > time() && $task['end'] < time()){


                                        // tried to use horizontal divider here, but there's a semantic bug
                                        // ... when using these in tables. https://semantic-ui.com/elements/divider.html
                                        // ... adding display:block to the td fixes, but then colspan doesn't work. Skipping for now
                                        echo '<tr><td colspan="4"><div class="ui horizontal divider">'.__('Past Tasks','zero-bs-crm').'</div></td></tr>';

                                      }

                                      $taskURL = zbsLink('edit',$task['id'],ZBS_TYPE_EVENT);
                                      $statusStr = __('Incomplete','zero-bs-crm'); if (isset($task['complete']) && $task['complete'] === 1) $statusStr = __('Completed','zero-bs-crm');
                                      $status = "<span class='".zeroBSCRM_html_taskStatusLabel($task)."'>" . $statusStr . "</span>";
                                      
                                      echo "<tr>";
                                        echo "<td>" . zeroBSCRM_html_taskDate($task) . "</td>";
                                        echo "<td>" . $task['title'] . "</td>";
                                        echo "<td>".$status."</td>";
                                        echo '<td style="text-align:center"><a href="'.$taskURL.'">' . __('View','zero-bs-crm') . "</a></td>";
                                      echo "</tr>"; 

                                      $lastTaskStart = $task['start'];

                                    }
                                  } else {

                                    ?><tr>
                                        <td colspan="4">
                                            <div class="ui info icon message" id="zbsNoTaskResults">
                                              <div class="content">
                                                <div class="header"><?php _e('No Tasks',"zero-bs-crm"); ?></div>
                                                <p>
                                                  <?php
                                                  //prefill doesn't yet exist for companies
                                                  echo sprintf(__('This %s does not have any tasks yet. Do you want to <a href="%s">create one</a>?',"zero-bs-crm"),jpcrm_label_company(), $new_task_url);
                                                  ?>
                                                </p>
                                              </div>
                                            </div>
                                        </td>
                                      </tr><?php

                                  }

                                  ?>

                                </tbody>
                              </table>
                              <?php if (count($zbsCompany['tasks']) > 0) : ?>
                                <div style="text-align: right;">
                                  <a href="<?php echo $new_task_url ?>" class="ui basic green button">
                                    <i class="plus square outline icon"></i>
                                    <?php _e('Add Task','zero-bs-crm') ?>
                                  </a>
                                </div>
                              <?php endif; ?>
                      </div><?php } ?>

                </div><!-- docs -->

              <?php 

                  $companyTags = zeroBSCRM_getCompanyTagsByID($zbsCompany['id']);

                  if (count($companyTags) > 0){

                      ?><!-- TAGGED --><div class="zbs-view-tags">
                      <h4 class="ui horizontal header divider">
                          <i class="tag icon"></i>
                          <?php _e('Tagged',"zero-bs-crm"); ?>
                      </h4>
                      <?php

                        // output as links
                        zeroBSCRM_html_linkedCompanyTags($zbsCompany['id'],$companyTags,'ui medium olive button');

                      ?>
                      </div><!-- / TAGGED --><?php
                  } ?>

            </div>

            <div class="six wide column" id="zbs-custom-quicklinks" style="padding-right: 30px;">

              <?php 
                  #} Metaboxes
                  zeroBSCRM_do_meta_boxes( 'zerobs_view_company', 'side', $zbsCompany );
              ?>
              
            </div>


          </div>
          
              <script type="text/javascript">
                
                // Nonce
                var zbscrmjs_secToken = '<?php echo wp_create_nonce( "zbscrmjs-ajax-nonce" ); ?>';

                // moved to singleview.js
                var zbsViewSettings = {

                    objid: <?php echo $id; ?>,
                    objdbname: 'company' <?php //echo $this->objType; ?>

                };

                </script><?php

                // PRIVATE hook (do not share with dev/docs PLEASE leave off.)
                do_action('zerobscrm_companyview_postscripts');

     } // if ID

}


/*

  Custom Fields View Company Tab

*/
function zeroBSCRM_pages_admin_view_page_company_custom_fields($arr=array(), $id=-1) {

  global $zbs;

  // Here we hide it if:
  // - Non admin
  // - No custom fields
  if ($zbs->DAL->companies->hasCustomFields($id,false) || zeroBSCRM_isZBSAdminOrAdmin()){

      // this is just a check :)
      if (!is_array($arr)) $arr = array();

      // Here we add the new tab
      $arr[] = array(
        'id' => 'company-custom-fields-tab',
        'name' => __('Custom Fields', 'zero-bs-crm'),
        'content' => zeroBSCRM_pages_admin_display_custom_fields_table($id,ZBS_TYPE_COMPANY)
      );

  }

  return $arr;

}
add_filter( 'jetpack-crm-company-vital-tabs', 'zeroBSCRM_pages_admin_view_page_company_custom_fields', 10, 2);