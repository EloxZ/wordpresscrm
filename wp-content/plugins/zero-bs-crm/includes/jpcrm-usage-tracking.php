<?php
/**
 * 
 * Usage Tracking for CRM insights
 * is only included if the setting
 * is enabled
 * 
 * @since 4.3.0
 * 
 */

class jpcrm_usage_tracking{

    public function __construct() {

        $this->init_hooks();

    }

    public function init_hooks(){

        #} Usage tracking (pageviews) sent via AJAX
		add_action('admin_footer', array($this, 'track_pageviews'));
        add_action('wp_ajax_jpcrm_send_page_view', array($this, 'send_page_view'));

    }


    /**
     * AJAX function to send the pageview
     */
    public function send_page_view(){

        global $zbs;

        $res = array();

        // check nonce
        check_ajax_referer( 'jpcrm_usage_tracking_nonce', 'security' );

        // retrieve page_name
        $page_name = sanitize_text_field($_POST['page_name']);

        // pool data
        $data = array(
            'action'        => 'jpcrm_track_usage',
            'event_name'    => $page_name,
            'event_type' 	=> 'page_view',
            'site_url' 		=> home_url()
        );

        // call
        $response = wp_remote_post( $zbs->urls['usage'], array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => $data,
            'cookies'     => array()
            )
        );

        $res['pageview'] = 'sent';

        // send summary snapshot based on a transient
        if(!get_transient('jpcrm_crm_snapshot')){

            $this->send_snapshot();
            $res['snapshot'] = 'sent';
            set_transient( 'jpcrm_crm_snapshot', true, DAY_IN_SECONDS );

        }

        wp_send_json($res);
    
    }

    /**
     * send summary snapshot
     */
    public function send_snapshot(){

        global $zbs;

        // build data
		$contacts_count			    = $zbs->DAL->contacts->getFullCount();
		$companies_count 			= $zbs->DAL->companies->getFullCount();
		$transactions_count 		= $zbs->DAL->transactions->getFullCount();
		$quotes_count 			    = $zbs->DAL->quotes->getFullCount();
		$invoices_count 			= $zbs->DAL->invoices->getFullCount();
		$forms_count 				= $zbs->DAL->forms->getFullCount();
		$events_count 			    = $zbs->DAL->events->getFullCount();
		$crm_users 			        = count(zeroBSCRM_crm_users_list());
		$extensions_count 	        = zeroBSCRM_extensionsInstalledCount();

		$data = array(
			'action'					=> 'jpcrm_track_stats',
			'site_url' 					=> home_url(),
			'contacts_count' 			=> $contacts_count,
			'companies_count'			=> $companies_count,
			'transactions_count' 		=> $transactions_count,
			'quotes_count'				=> $quotes_count,
			'invoices_count'			=> $invoices_count,
			'forms_count'				=> $forms_count,
			'events_count'				=> $events_count,
			'users_count'				=> $crm_users,
			'extensions_count'			=> $extensions_count,
		);

        // call
		return wp_remote_post( $zbs->urls['usage'], array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $data,
			'cookies'     => array()
			)
		);

    }

    /**
     * Package admin page as string which can be passed to usage tracking API
     */
    public function get_jpcrm_admin_page(){

        // retrieve uri
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
        
        // if somehow failed, return
        if ( ! $uri ) {
            return '';
        }

        // hard remove any unwanted get parameters here
        $uri = remove_query_arg( array( '_wpnonce' ), admin_url( $uri ) );        

        // get _GET Parameters
        $parameters = jpcrm_url_get_params( $uri );

        // cycle through some key parameters and tokenise for anonymity / cleanliness
        if ( is_array( $parameters ) ) {

            $return_string = '';

            // required: page
            if ( isset( $parameters['page'] ) && !empty ( $parameters['page'] ) ){

                $return_string = $parameters['page'];

            } else {

                // missing $page, skip
                return '';

            }

            // Overrides:

            // action: by default it's add, unless zbsid is set, then it's edit, make so:
            if ( isset( $parameters['action'] ) && !empty( $parameters['action'] ) ){

                // if action is set and no zbsid is present, it's an add
                if ( 
                    $parameters['action'] == 'edit' && (
                    !isset( $parameters['zbsid'] ) || empty( $parameters['zbsid'] )
                ) ){

                    $parameters['action'] = 'add';
                }

                // note if there's an action present and zbsid, we can go ahead and remove zbsid from the return :)
                if ( isset( $parameters['zbsid'] ) ){
                 
                    unset( $parameters['zbsid'] );
                
                }


            }

            // generic out any non-helpful data
            $parameters_to_blank = array( 'zbsid', 'zbsprefillcust', 'zbsprefill', 'zbs_tag', 'quickfilters', 'sort', 'zbsowner', 'zbs_template_id' );

            foreach ( $parameters_to_blank as $parameter_key){

                if ( isset( $parameters[ $parameter_key ] ) ){

                    // set generic value
                    $parameters[ $parameter_key ] = 'present';

                }

            }

            // finally rebuild into a useful string
            foreach ( $parameters as $parameter_key => $parameter_value){

                // skip page as is always added above
                if ( $parameter_key == 'page' ){

                    continue;

                }

                if ( !empty( $return_string ) ){

                    $return_string .= '|';

                }

                // here we check if $parameter_value might be an email
                // ... designed as future-proofing and to catch any potential leaks of user data to our system
                if ( zeroBSCRM_validateEmail( $parameter_value ) ) $parameter_value = '{email}';

                // append return string
                $return_string .= $parameter_key . ':' . $parameter_value;

            }

            return $return_string;

        }


        // fallback: retrieve page via explode
        $split = explode("?page=",$uri);
        $page = $split[0];
        if(count($split) == 2){
            $page = $split[1];
        }

        // returns everything after the ?page= part of the URL
        return $page;

    }



    /**
     * JS to track usage
     */
	public function track_pageviews(){

        global $zbs;

        if (zeroBSCRM_isAdminPage()){

            $page = $this->get_jpcrm_admin_page();

            if ( !empty( $page ) ){

                ?>
                <script>
                    data = {
                        action: 'jpcrm_send_page_view',
                        page_name: '<?php echo $page; ?>',
                        security: '<?php echo wp_create_nonce('jpcrm_usage_tracking_nonce'); ?>'
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        //nothing to see here.
                    });
                </script>
                <?php

            }


        }	

	}

}