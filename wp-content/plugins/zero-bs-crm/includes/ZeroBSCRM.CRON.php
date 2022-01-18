<?php 
/*!
 * Jetpack CRM
 * https://jetpackcrm.com
 * V1.2.3
 *
 * Copyright 2020 Automattic
 *
 * Date: 15/12/16
 */

/*

	To add to cron:

		1) Add to list (#1)
		2) Add func x 2 (#2)


	To see what cron is enabled:
	http://wordpress.stackexchange.com/questions/98032/is-there-a-quick-way-to-view-the-wp-cron-schedule
  <?php 

    $cron_jobs = get_option( 'cron' );
    print_r($cron_jobs);

  ?>
	

*/

/* ======================================================
	Wrapper Arr (lists of cron to add)
   ====================================================== */

	 global 	$zbscrm_CRONList; 
	 
   			$zbscrm_CRONList = array(

   				##WLREMOVE
   				'tele' => 'daily',
   				'ext' => 'daily',
   				##/WLREMOVE

   				# use alpha, will be lower-cased for hook
   				// v3.0+ we do away with this: 'clearAutoDrafts' => 'hourly',
   				'notifyEvents' => 'hourly',
   				//'clearTempHashes' => 'hourly'
   				'clearSecLogs' => 'daily',

   			);



/* ======================================================
	/Wrapper Arr (lists of cron to add)
   ====================================================== */


/* ======================================================
	Add Jetpack CRM Custom schedule (5m)
	// https://wordpress.stackexchange.com/questions/208135/how-to-run-a-function-every-5-minutes
   ====================================================== */
	function zeroBSCRM_cronSchedules($schedules){
	    if(!isset($schedules["5min"])){
	        $schedules["5min"] = array(
	            'interval' => 5*60,
	            'display' => __('Once every 5 minutes', 'zero-bs-crm'));
	    }
	    return $schedules;
	}
	add_filter('cron_schedules','zeroBSCRM_cronSchedules');
/* ======================================================
	/Add Jetpack CRM Custom schedule (5m)
   ====================================================== */


/* ======================================================
	Scheduler Funcs
   ====================================================== */
function zeroBSCRM_activateCrons(){


	global $zbscrm_CRONList; 
	foreach ($zbscrm_CRONList as $cronName => $timingStr)	{
		
		$hook = 'zbs'.strtolower($cronName);
		$funcName = 'zeroBSCRM_cron_'.$cronName;
		
	    if (! wp_next_scheduled ( $hook )) {
				wp_schedule_event(time(), $timingStr, $hook);
	    }

	}

}
register_activation_hook(ZBS_ROOTFILE, 'zeroBSCRM_activateCrons');
function zeroBSCRM_deactivateCrons(){

	global $zbscrm_CRONList; 
	foreach ($zbscrm_CRONList as $cronName)	{
		
		$hook = 'zbs'.strtolower($cronName);
		$funcName = 'zeroBSCRM_cron_'.$cronName;

		wp_clear_scheduled_hook($hook);

	}

}
register_deactivation_hook(ZBS_ROOTFILE, 'zeroBSCRM_deactivateCrons');
/* ======================================================
	/ Scheduler Funcs
   ====================================================== */





/* ======================================================
	Actual Action Funcs #2
   ====================================================== */

   # ======= Clear Auto-drafts
	function zeroBSCRM_cron_clearAutoDrafts() {

		#} Simple
		// only show these for legacy users using DAL<3
		// #backward-compatibility
		global $zbs;
		if (!$zbs->isDAL3()) {
			zeroBSCRM_clearCPTAutoDrafts();
		}

	}

	add_action('zbsclearautodrafts', 'zeroBSCRM_cron_clearAutoDrafts');


	function zeroBSCRM_cron_notifyEvents() {

		#} Simple
		zeroBSCRM_notifyEvents();

	}

	add_action('zbsnotifyevents', 'zeroBSCRM_cron_notifyEvents');


   # ======= Clear temporary hashes
	/* function zeroBSCRM_cron_clearTempHashes() {

		#} Simple
		zeroBSCRM_clearTemporaryHashes();

	}

	add_action('zbscleartemphashes', 'zeroBSCRM_cron_clearTempHashes'); */

   # ======= Clear security logs (from easy-pay hash requests) *after 72h
	function zeroBSCRM_cron_clearSecLogs() {

		#} Simple
		zeroBSCRM_clearSecurityLogs();

	}

	add_action('zbsclearseclogs', 'zeroBSCRM_cron_clearSecLogs'); 

/* ======================================================
	/ Actual Action Funcs
   ====================================================== */

/* ======================================================
	CRONNABLE FUNCTION (should house these somewhere)
   ====================================================== */

// Notify user of upcoming event (task)
function zeroBSCRM_notifyEvents(){

	// is the email notification active? (if not, nothing to do)
	if (!zeroBSCRM_get_email_status(ZBSEMAIL_EVENTNOTIFICATION)) return;

	global $zbs;

	// Backward compatibility replaced with DAL2+ support in 4.0.7
	if (!$zbs->isDAL2()) return;

	// retrieve upcoming event reminders
	$dueEventReminders = $zbs->DAL->eventreminders->getEventreminders(array(

		'dueBefore' => time()+3600, // anytime within next hour
		'dueAfter' => time()-3600, // anytime from -1h
		'sent' => false, // reminders which hasn't been sent

	));

	// cycle through them, if any
	foreach ($dueEventReminders as $eventReminder){

		$event = $zbs->DAL->events->getEvent($eventReminder['event']);

		// check if event
		// check event not complete (if so, no need to send)
		// check if event has owner
		if (is_array($event) && $event['complete'] !== 1 && $event['owner'] > 0){

			// retrieve target (event owner)
			$owner_info = get_userdata($event['owner']);
			if ($owner_info > 0){

				// email
				$owner_email = $owner_info->user_email;

				// send notification email (tracking dealt with by zeroBSCRM_mailDelivery_sendMessage)

				// ==========================================================================================
				// =================================== MAIL SENDING =========================================

				// generate html
				$emailHTML = zeroBSCRM_Event_generateNotificationHTML(true, $owner_email, $event['id'], $event);

	              // build send array
	              $mailArray = array(
	                'toEmail' => $owner_email,
	                'toName' => '',
	                'subject' => zeroBSCRM_mailTemplate_getSubject(ZBSEMAIL_EVENTNOTIFICATION),
	                'headers' => zeroBSCRM_mailTemplate_getHeaders(ZBSEMAIL_EVENTNOTIFICATION),
	                'body' => $emailHTML,
	                'textbody' => '',
	                'options' => array(
	                  'html' => 1
	                ),
	                'tracking' => array( 
	                  // tracking :D (auto-inserted pixel + saved in history db)
	                  'emailTypeID' => ZBSEMAIL_EVENTNOTIFICATION,
	                  'targetObjID' => $event['owner'],
	                  'senderWPID' => -13,
	                  'associatedObjID' => $event['id']
	                )
	              );

	              // Sends email, including tracking, via setting stored route out, (or default if none)
	              // and logs tracking :)

					// discern delivery method
					$mailDeliveryMethod = zeroBSCRM_mailTemplate_getMailDelMethod(ZBSEMAIL_EVENTNOTIFICATION);
					if (!isset($mailDeliveryMethod) || empty($mailDeliveryMethod)) $mailDeliveryMethod = -1;

					// send
					$sent = zeroBSCRM_mailDelivery_sendMessage($mailDeliveryMethod,$mailArray);

					// mark as sent
					$zbs->DAL->eventreminders->setSentStatus($eventReminder['id'],1);


				// =================================== / MAIL SENDING =======================================
				// ==========================================================================================


			} // / if owner exists as wp user

		} // / if event, if event not complete, if event has owner

	}

}