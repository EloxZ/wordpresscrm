<?php
/**
 * Jetpack CRM Core
 *
 * @author   Woody Hayday, Mike Stott
 * @package  ZeroBSCRM
 * @since    2.27
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Main ZeroBSCRM Class.
 *
 * @class ZeroBSCRM
 * @version	2.27
 */
final class ZeroBSCRM {

	/**
	 * ZeroBSCRM version.
	 *
	 * @var string
	 */
	public $version = '4.7.0';

	/**
	 * WordPress version tested with.
	 *
	 * @var string
	 */
	public $wp_tested = '5.8';

	/**
	 * WordPress update API version.
	 *
	 * @var string
	 */
	public $api_ver = '1.0';

	/**
	 * Jetpack CRM update API version.
	 *
	 * @var string
	 */
	public $update_api_version = '1.0';

	/**
	 * ZeroBSCRM DB version.
	 *
	 * @var string
	 */
	public $db_version = '3.0';

	/**
	 * ZeroBSCRM DAL version.
	 *
	 * @var string
	 */
	public $dal_version = '3.0';

	/**
	 * ZeroBSCRM Extension Compatible versions
	 * Stores which extensions are viable to use with newly-migrated v3.0
	 *
	 * @var string
	 */
	public $compat_versions = array(

		// v3.0 Migration needed a 'minimum version' for any extensions which might not work with v3.0 but were active premigration
		// 15th Nov - as numbers it does not like the 1.4.1 type format so added as strings.
		'v3extminimums' => array(

			'advancedsegments' => '1.3',
			'apiconnector' => '1.6',
			'automations' => '1.4.1',
			'aweber' => '1.2',
			'awesomesupport' => '2.5',
			'batchtag' => '2.3',
			'passwordmanager' => '1.4.1',
			'clientportalpro' => '1.7',
			'contactform' => '2.5',
			'convertkit' => '2.5',
			'csvpro' => '2.0',
			'envato' => '2.4.2',
			'exitbee' => '1.1',
			'funnels' => '1.2',
			'googlecontact' => '2.6',
			'gravity' => '2.6',
			'groove' => '2.6',
			'invpro' => '2.6',
			'livestorm' => '1.1',
			'mailcamp' => '2.0.4',
			'mailchimp' => '2.6',
			'membermouse' => '1.5',
			'optinmonster' => '1.1',
			'paypal' => '2.6.1',
			'registrationmagic' => '1.1',
			'salesdash' => '2.6',
			'stripe' => '2.6.2',
			'systememail' => '1.1',
			'twilio' => '1.5',
			'woosync' => '2.9',
			'wordpressutilities' => '1.2',
			'worldpay' => '2.4'

		)

	);

	/**
	 * ZeroBSCRM DAL .
	 *
	 * @var object (DAL Class init) ;)
	 */
	public $DAL = false;

	/**
	 * Dependency checker
	 *
	 * @var object JPCRM_DependencyChecker init
	 */
	public $dependency_checker = false;

	/**
	 * Feature sniffer
	 *
	 * @var object JPCRM_FeatureSniffer init
	 */
	public $feature_sniffer = false;

	/**
	 * DB1 compatability support
	 *
	 * @var Bool - if true, basically $obj['meta'] is a clone of $obj itself (To be disabled once safely in DAL2 + updated extensions)
	 */
	public $db1CompatabilitySupport = false;

	/**
	 * DB2 compatability support
	 *
	 * @var Bool - if true, basically $obj['meta'] is a clone of $obj itself (To be disabled once safely in DAL3 + updated extensions)
	 * This variant accounts for stray objs in quotes, trans, invs, etc.
	 */
	public $db2CompatabilitySupport = false;

	/**
	 * ZeroBSCRM DB Version Switch.
	 *
	 * @var string
	 */
	public $DBVER = 1;

	/**
	 * The single instance of the class.
	 *
	 * @var ZeroBSCRM
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * ZeroBSCRM Page Loaded (KEY - used for screenoptions) (equivilent of pagenow)
	 *
	 * @var string
	 */
	public $pageKey = 'root';


	/**
	 * Template path, this is where we look in the theme directory for templates
	 *
	 * @var string
	 */
	public $template_path = 'jetpack-crm';

	/**
	 * Extensions instances
	 *
	 * @var Jetpack CRM Extensions
	 */
	public $extensions = null;

	/**
	 * External Sources
	 *
	 * @var Jetpack CRM External Sources
	 */
	public $external_sources = null;


	/**
	 * Settings Object
	 *
	 * @var Jetpack CRM Settings Object
	 */

	public $settings = null;

	/**
	 * Internal Automator Block
	 *
	 * @var Bool - if true, IA will not fire anything :)
	 */
	public $internalAutomatorBlock = false;

	/**
	 * Metaboxes Object
	 *
	 * @var Jetpack CRM Metaboxes Object
	 */

	public $metaboxes = null;

	/**
	 * Menus Object
	 *
	 * @var Jetpack CRM Menus Array
	 * This ultimately adds any WP menus that need injecting
	 */
	private $menu = null;


	/**
	 * URLS Array
	 *
	 * @var Jetpack CRM URLS list
	 */
	public $urls;

	/**
	 * Slugs Array
	 *
	 * @var Jetpack CRM Slugs list
	 */
	public $slugs;

	/**
	 * Transient Array
	 *
	 * @var Jetpack CRM Transients list
	 */
	public $transients; 

	/**
	 * Included Array (means we can not 'reinclude' stripe etc.)
	 */
	public $included = array(

		'stripe' => false

	);

	/**
	 * Libraries included (3.0.12+)
	 * Note: All paths need to be prepended by ZEROBSCRM_PATH before use
	 */
	private $libs = array(
		'dompdf' => array(
			'version' 	=> '0.8.3',
			'path'		=> 'includes/lib/dompdf-0-8-3/',
			'include' 	=> 'includes/lib/dompdf-0-8-3/autoload.inc.php'
		),
        'dompdf-1' => array(
            'version' 	=> '1.0.2',
            'path'		=> 'includes/lib/dompdf-1/',
            'include' 	=> 'includes/lib/dompdf-1/autoload.inc.php'
        )
	);

	/**
	 * Page Messages Array
	 * Experimental: stores msgs such as "Contact Updated"
	 *
	 * @var msg arr
	 */
	public $pageMessages; 

	/**
	 * Templating: placeholders
	 *
	 * @var object Placeholder Class
	 */
	public $templating_placeholders = false;

	/**
	 * Acceptable mime types Array
	 *
	 * @var Jetpack CRM Acceptable mime types list
	 */
	public $acceptable_mime_types; 

	/**
	 * Acceptable html array
	 *
	 * @var Jetpack CRM Acceptable html types list
	 * Was previously: $zeroBSCRM_allowedHTML 
	 */
	public $acceptable_html = array(
		    'a' => array(
		        'href' => array(),
		        'title' => array()
		    ),
		    'br' => array(),
		    'em' => array(),
		    'strong' => array(),
		    'ul' => array(),
		    'li'  => array(),
		    'p' =>array(),
		    'div' => array(
		    	'class' => array(),
		    	'style' => array(),
		    	'id'	=> array(),
		    ),
		    'img' => array(
		    	'class' => array(),
		    	'style' => array(),
		    	'src'	=> array()
		    ),
		    'table' => array(
		    	'tr' => array(
		    		'th' => array(
		    			'label' => array()
		    		),
		   			'class' => array(),
		   			'label' => array(),
		   			'th'	=>array()
		   		),
		    	'style' => array(),
		    	'label' => array()
		    ),
		    'td' => array(),
		    'tr' => array()
		  );
	

	/**
	 * Error Codes Array
	 * Experimental: loads + stores error codes, (only when needed/requested)
	 *
	 * @var error code arr
	 */
	private $errorCodes; 

	/**
	 * Main ZeroBSCRM Instance.
	 *
	 * Ensures only one instance of ZeroBSCRM is loaded or can be loaded.
	 *
	 * @since 2.27
	 * @static
	 * @return ZeroBSCRM - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		zerobscrm_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zero-bs-crm' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		zerobscrm_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'zero-bs-crm' ), '2.1' );
	}

	/**
	 * Return private $menu
	 *
	 * @since 3.0
	 */
	public function getMenu(){ return $this->menu; }

	/**
	 * Auto-load in-accessible properties on demand - What is this wizadrey?
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	*/
	/*
	See: http://php.net/manual/en/language.oop5.overloading.php#object.get
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'shipping', 'mailer', 'checkout' ), true ) ) {
			return $this->$key();
		}
	}

	*/

	/**
	 * Jetpack CRM Constructor.
	 */
	public function __construct() {

		#} define constants & globals
		$this->define_constants();
		// WH stopped this global usage 25/1/18 - use $zbs->slugs etc. now $this->define_globals();
		// see setupUrlsSlugsEtc below

		$this->debugMode();

		#} DB MODE check (influences includes etc. - ultimately assists catching those who need to migrate data from 1.2 db)
		$this->DBModeCheck();

		#} Load includes
		$this->includes();

		// urls, slugs, (post inc.)
		$this->setupUrlsSlugsEtc();

		#} Initialisation
		$this->init_hooks();

		#} Post Init hook
		do_action( 'zerobscrm_loaded' );

	}

	/**
	 *   Maintain a list of Jetpack CRM extension slugs here.
	 * 		(This was an MS initiative for updates/licensing, WH removed 27/11/18, doing via Keys = rebrandr friendly)
	 */
	/*

	 public $zeroBSCRM_extensionSlugs = array(

		'ZeroBSCRM_BulkTagger.php',


	 );
	 */
 

	/**
	 * Define ZeroBSCRM Constants.
	 */
	private function define_constants() {

		// Main paths etc.
		$this->define( 'ZBS_ABSPATH', dirname( ZBS_ROOTFILE ) . '/' );
		$this->define( 'ZEROBSCRM_PATH', plugin_dir_path(ZBS_ROOTFILE) );
		$this->define( 'ZEROBSCRM_URL', plugin_dir_url(ZBS_ROOTFILE) );

		// THIS COULD DO WITH RETHINKING/RECHECKING! WH
		$this->define( 'ZEROBSCRM_WILDPATH', ZEROBSCRM_PATH . 'wild/' ); 
		$this->define( 'ZEROBSCRM_WILDURL', ZEROBSCRM_URL . 'wild/' ); #} THIS is for PHP which is called seperate to main WP FLOW!!

		// Template paths
		$this->define( 'ZEROBSCRM_TEMPLATEPATH', ZEROBSCRM_PATH . 'templates/' ); 
		$this->define( 'ZEROBSCRM_TEMPLATEURL', ZEROBSCRM_URL . 'templates/' );
		
		// include path
		$this->define( 'ZEROBSCRM_INCLUDE_PATH', ZEROBSCRM_PATH . 'includes/' ); 

		#} define that the CORE has been loaded - for backwards compatibility with other extensions
		$this->define('ZBSCRMCORELOADED',true);

		#} Menu types
		$this->define('ZBS_MENU_FULL', 1);
		$this->define('ZBS_MENU_SLIM', 2);
		$this->define('ZBS_MENU_CRMONLY', 3);

		#} Debug
		$this->define('ZBS_CRM_DEBUG', true);

	}

	private function debugMode(){

		if( defined( 'ZBS_CRM_DEBUG' ) ){
			/*
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
			*/
		}
	}

	#} DB MODE check (influences includes etc. - ultimately assists catching those who need to migrate data from 1.2 db)
	private function DBModeCheck(){

		// This checks if certain migrations have been completed + maintains flags to include the right DAL legacy support
		// THIS one sets to DAL2 if not DAL2
		$migration299Fini = get_option('zbs_db_migration_300',false);
		if (!is_array($migration299Fini)){

			// un-migrated DAL2 (2.53) database
			$this->db_version = '2.53';
			$this->dal_version = '2.53';
			$this->db2CompatabilitySupport = true;		

			#} Load database migration file if not loaded, as needed for migration
			require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Migrations.Database.php');

		} 

		//.. which then cascades here, if not DAL2 + DAL3, then DAL1:

		// This checks if certain migrations have been completed + maintains flags to include the right DAL legacy support
		$migration253Fini = get_option('zbs_db_migration_253',false);
		if (!is_array($migration253Fini)){

			// un-migrated <2.53 database
			$this->db_version = '1.2';
			$this->dal_version = '1.0';
			$this->db1CompatabilitySupport = true;

			#} Load database migration file if not loaded, as needed for migration
			#} Not sure why this isn't loaded by the above catch, but appears to not be on some new installs.
			require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Migrations.Database.php');

		}


	}

	#} Use this for shorthand checking old DAL
	public function isDAL1(){

		// is DAL = 1.0
		return (version_compare($this->dal_version,  "2.53") < 0);

	}

	#} Use this for shorthand checking new DAL additions
	// this says "is At least DAL2"
	public function isDAL2(){

		// is DAL > 1.0
		return (version_compare($this->dal_version,  "1.0") > 0);

	}

	#} Use this for shorthand checking new DAL additions
	// this says "is At least DAL3"
	public function isDAL3(){

		// is DAL > 1.0
		return (version_compare($this->dal_version,  "2.53") > 0);

	}

    #} Use this to output the number of plugins with "Jetpack CRM" in the name
	public function extensionCount($activatedOnly=false){

		/* Following func: zeroBSCRM_extensionsInstalledCount
		... will get all active rebrandr extensions, 
		... and all active/inactive branded extensions

		... and returns a count here */
		return zeroBSCRM_extensionsInstalledCount($activatedOnly); 
	}
	

	private function setupUrlsSlugsEtc(){

		// array check
		if (!is_array($this->urls)) $this->urls = array();
		if (!is_array($this->slugs)) $this->slugs = array();
		if (!is_array($this->transients)) $this->transients = array();
		if (!is_array($this->acceptable_mime_types)) $this->acceptable_mime_types = array();
		if (!is_array($this->pageMessages)) $this->pageMessages = array();

		// mime types just use this func () - needs rethinking - includes/ZeroBSCRM.FileUploads.php
		$this->acceptable_mime_types = zeroBSCRM_returnMimeTypes();

		#} Urls
		// not from 2.5+ global 	$zbs->urls;
				$this->urls['home']								= 'https://jetpackcrm.com';
				// makes more sense to me? $this->urls['support']				= 'https://kb.jetpackcrm.com/';
				$this->urls['support']						= 'https://kb.jetpackcrm.com/submit-a-ticket/';
				$this->urls['feedback']						= 'https://kb.jetpackcrm.com/submit-a-ticket/';
				$this->urls['pricing']						= 'https://jetpackcrm.com/pricing/';

				##WLREMOVE
				$this->urls['betafeedbackemail']	= 'hello@jetpackcrm.com'; // SPECIFICALLY ONLY USED FOR FEEDBACK ON BETA RELEASES, DO NOT USE ELSEWHERE
				##/WLREMOVE
								
				$this->urls['docs']								= 'https://kb.jetpackcrm.com/';
				$this->urls['productsdatatools']	= 'https://jetpackcrm.com/data-tools/'; 
				$this->urls['extimgrepo']					= 'https://jetpackcrm.com/_plugin_dependent_assets/_i/';
				$this->urls['rateuswporg']				= 'https://wordpress.org/support/view/plugin-reviews/zero-bs-crm?filter=5#postform';
				$this->urls['extdlrepo']					= 'https://jetpack-crm-cdn.s3.amazonaws.com/ext/';//'http://demo.zbscrm.com/_ext/';
				$this->urls['apidocs']						= 'https://automattic.github.io/jetpack-crm-api-docs/';

				// used for ext manager:
				$this->urls['checkoutapi']				= 'https://jetpackcrm.com/wp-json/zbsextensions/v1/extensions/0';		
				$this->urls['howtoinstall']				= 'https://kb.jetpackcrm.com/knowledge-base/how-do-i-install-a-zero-bs-extension/';
				$this->urls['apiconnectorsales']	= 'https://jetpackcrm.com/product/api-connector/';
				$this->urls['autonumberhelp']			= 'https://kb.jetpackcrm.com/knowledge-base/custom-field-type-autonumber/';
				$this->urls['akamode']						= 'https://jetpackcrm.com/feature/aka-mode/';
				$this->urls['licensinginfo']			= 'https://kb.jetpackcrm.com/knowledge-base/yearly-subscriptions-refunds/';
				$this->urls['easyaccessguide'] 		= 'https://kb.jetpackcrm.com/knowledge-base/easy-access-links-for-client-portal/';

				// API v3.0 - licensing - 5/12/18
				$this->urls['api']					= 'https://app.jetpackcrm.com/api/updates/updates';
				$this->urls['apilocalcheck']	    = 'https://app.jetpackcrm.com/api/updates/localcheck';
				$this->urls['smm']					= 'https://app.jetpackcrm.com/api/welcome-wizard';
				
				// account
				$this->urls['account']						= 'https://app.jetpackcrm.com/';		
				$this->urls['licensekeys']				= 'https://app.jetpackcrm.com/license-keys';
				$this->urls['community']					= 'https://jetpackcrm.com/community/';

				#} sales urls			 
				$this->urls['products'] 					= 'https://jetpackcrm.com/extensions/'; 
				$this->urls['extcsvimporterpro']	= 'https://jetpackcrm.com/product/csv-importer-pro/';
				$this->urls['invpro'] 						= 'https://jetpackcrm.com/product/invoicing-pro/';
				$this->urls['upgrade'] 						= 'https://jetpackcrm.com/checkout/?plan=entrepreneur&utm_source=plugin&utm_medium=plugin&utm_campaign=welcome_upgrade'; 
				$this->urls['extcpp']							= 'https://jetpackcrm.com/product/client-portal-pro/';
				$this->urls['extcal']							= 'https://jetpackcrm.com/product/calendar-pro/';	
				$this->urls['roadtov3']						= 'https://jetpackcrm.com/road-to-v3/';	
				$this->urls['advancedsegments']		= 'https://jetpackcrm.com/product/advanced-segments/';
				$this->urls['bulktagger']					= 'https://jetpackcrm.com/product/bulk-tagger/';
				$this->urls['salesdash']					= 'https://jetpackcrm.com/product/sales-dashboard/';

				$this->urls['feedbackform']				= 'https://forms.gle/eBTFC3MybfaikWPu8';

				// social
				$this->urls['twitter']						= 'https://twitter.com/jetpackcrm';

				// assets
				$this->urls['crm-logo']						= plugins_url( 'i/jpcrm-logo.png', ZBS_ROOTFILE );

				// temp/updates
				$this->urls['db2migrate']					= 'https://kb.jetpackcrm.com/knowledge-base/updating-contact-database-dbv2-migration/';
				$this->urls['db3migrate']					= 'https://kb.jetpackcrm.com/knowledge-base/upgrading-database-v3-0-migration/';
				$this->urls['migrationhelpemail']	= 'hello@jetpackcrm.com'; 
				$this->urls['db3migrateexts']			= 'https://kb.jetpackcrm.com/knowledge-base/upgrading-database-v3-0-migration/#extension-compatibility';

				// kb
				$this->urls['kbdevmode']					= 'https://kb.jetpackcrm.com/knowledge-base/developer-mode/';
				$this->urls['kbquoteplaceholders']	= 'https://kb.jetpackcrm.com/knowledge-base/placeholders-in-emails-quote-templates-invoices-etc/#quote-template-placeholders';
				$this->urls['kblicensefaq']				= 'https://kb.jetpackcrm.com/knowledge-base/license-keys-faq/';
				$this->urls['kbcronlimitations']	= 'https://kb.jetpackcrm.com/knowledge-base/wordpress-cron-limitations/';
				$this->urls['kbfirstcontact']			= 'https://kb.jetpackcrm.com/knowledge-base/adding-your-first-customer/';
				$this->urls['kbactivatecoreext']	= 'https://kb.jetpackcrm.com/knowledge-base/how-to-activate-deactivate-core-modules/';
				$this->urls['kbinvoicebuilder']		= 'https://kb.jetpackcrm.com/knowledge-base/how-to-use-the-invoice-builder/';
				$this->urls['kbteam']				= 'https://kb.jetpackcrm.com/knowledge-base/setting-up-your-team/';
				$this->urls['kbupdateext']			= 'https://kb.jetpackcrm.com/knowledge-base/how-do-i-update-an-extension/';
				$this->urls['kbclientportal']		= 'https://kb.jetpackcrm.com/knowledge-base/how-does-the-client-portal-work/';
				$this->urls['kbtemplatefiles']		= 'https://kb.jetpackcrm.com/knowledge-base/templating-how-to-change-templates-pdfs-portal-emails/';
				$this->urls['kbeasyaccess']				= 'https://kb.jetpackcrm.com/knowledge-base/easy-access-links-for-client-portal/';
				$this->urls['kbdisablewelcome']		= 'https://kb.jetpackcrm.com/knowledge-base/automatically-create-wordpress-users-but-not-send-them-a-welcome-email/';
				$this->urls['kbapi']							= 'https://kb.jetpackcrm.com/knowledge-base/using-the-api-connector/';
				$this->urls['kbshowwpmenus']			= 'https://kb.jetpackcrm.com/knowledge-base/how-to-get-wordpress-menu-items-back/';
				$this->urls['kbsmtpsetup']				= 'https://kb.jetpackcrm.com/knowledge-base/mail-delivery-method-setup-smtp/';
				$this->urls['kbgmailsetup']				= 'https://kb.jetpackcrm.com/knowledge-base/using-gmail-with-zbs-crm-mail-delivery-system/';
				$this->urls['kbcrmdashboard']			= 'https://kb.jetpackcrm.com/knowledge-base/zero-bs-crm-dashboard/';
				$this->urls['kbrevoverview']			= 'https://kb.jetpackcrm.com/knowledge-base/revenue-overview-chart/';
				$this->urls['kbcsvformat']				= 'https://kb.jetpackcrm.com/knowledge-base/what-should-my-csv-be-formatted-like/';
				$this->urls['kbcat_cal']					= 'https://kb.jetpackcrm.com/article-categories/calendar/';


				// coming soon
				$this->urls['soon']								= 'https://jetpackcrm.com/coming-soon/';

				// v4 rebrand announcement
				$this->urls['v4announce']					= 'https://jetpackcrm.com/rebrand-announcement';

				#} Usage Tracking
				$this->urls['usage']				= 'https://app.jetpackcrm.com/api/usage';
				$this->urls['usageinfo'] 			= 'https://jetpackcrm.com/usage-tracking';

		#} Page slugs
		// not from 2.5+ global	$this->slugs;

				$this->slugs['home'] 			= "zerobscrm-settings";

				##WLREMOVE
				$this->slugs['home'] 			= "zerobscrm-plugin";
				##/WLREMOVE
				$this->slugs['dash'] 			= "zerobscrm-dash";
				$this->slugs['settings'] 		= "zerobscrm-plugin-settings";
				$this->slugs['logout']			= "zerobscrm-logout";
				$this->slugs['datatools']		= "zerobscrm-datatools";
				$this->slugs['welcome']			= "zerobscrm-welcome";
				$this->slugs['feedback']		= "zerobscrm-feedback";
				$this->slugs['extensions']		= "zerobscrm-extensions";
				$this->slugs['bulktools']		= "zerobscrm-bulktools";
				$this->slugs['export']			= "zerobscrm-export";
				$this->slugs['systemstatus']	= "zerobscrm-systemstatus";
				$this->slugs['sync']			= "zerobscrm-sync";
				// These don't seem to be used anymore?
				//$this->slugs['connect']			= "zerobscrm-connect";
				//$this->slugs['app'] 			= "zerobscrm-app";
				//$this->slugs['whlang']			= "zerobscrm-whlang";
				//$this->slugs['customfields']	= "zerobscrm-customfields";
				//$this->slugs['import']			= "zerobscrm-import";

				//CSV importer Lite
				$this->slugs['csvlite']			= "zerobscrm-csvimporterlite-app";

				#} FOR NOW wl needs these:
				$this->slugs['bulktagger'] 		= "zerobscrm-batch-tagger";
				$this->slugs['salesdash'] 		= "sales-dash";
				$this->slugs['stripesync'] 		= "zerobscrm-stripesync-app";
				$this->slugs['woosync'] 		= "woo-importer";
				$this->slugs['paypalsync'] 		= "zerobscrm-paypal-app";

				#} OTHER UI PAGES WHICH WEREN'T IN SLUG - MS CLASS ADDITION
				#} WH: Not sure which we're using here, think first set cleaner:
				// NOTE: DAL3 + these are referenced in DAL2.php so be aware :)
				// (This helps for generically linking back to list obj etc.)
				// USE zbsLink!
				$this->slugs['managecontacts']		= "manage-customers";
				$this->slugs['managequotes']		= "manage-quotes";
				$this->slugs['manageinvoices']		= "manage-invoices";
				$this->slugs['managetransactions']	= "manage-transactions";
				$this->slugs['managecompanies'] 	= "manage-companies";
				$this->slugs['manageformscrm']		= "manage-forms";
				$this->slugs['segments']			= "manage-segments";
				$this->slugs['quote-templates'] 	= "manage-quote-templates";
				$this->slugs['manage-events']			= "manage-events";
				$this->slugs['manage-events-completed'] = "manage-events-completed";
				$this->slugs['manage-events-list'] 		= "manage-events-list";
				$this->slugs['managecontactsprev']		= "manage-customers-crm";
				$this->slugs['managequotesprev']		= "manage-quotes-crm";
				$this->slugs['managetransactionsprev']	= "manage-transactions-crm";
				$this->slugs['manageinvoicesprev']		= "manage-invoices-crm";
				$this->slugs['managecompaniesprev'] 	= "manage-companies-crm";
				$this->slugs['manageformscrmprev']		= "manage-forms-crm";

				#} NEW UI - ADD or EDIT, SEND EMAIL, NOTIFICATIONS
				$this->slugs['addedit'] 			= "zbs-add-edit";
				$this->slugs['sendmail'] 			= "zerobscrm-send-email";

				$this->slugs['emails'] 				= "zerobscrm-emails";

				$this->slugs['notifications'] 		= "zerobscrm-notifications";

				#} TEAM - Manage the CRM team permissions
				$this->slugs['team'] 				= "zerobscrm-team";

				#} CUSTOMER SEARCHING
				$this->slugs['customer-search'] 	= "customer-searching";

				#} Export tools
				$this->slugs['zbs-export-tools'] 	= "zbs-export-tools";
				$this->slugs['legacy-zbs-export-tools'] 	= "zbs-legacy-export-tools";

				#} Your Profile (for Calendar Sync and Personalised Stuff (like your own task history))
				$this->slugs['your-profile']		= "your-crm-profile";
				$this->slugs['reminders']			= "zbs-reminders";

				#} Adds a USER (i.e. puts our menu on user-new.php through ?page =)
				$this->slugs['zbs-new-user']		= "zbs-add-user";
				$this->slugs['zbs-edit-user']		= "zbs-edit-user"; #WH Added this, not sure what you're using for

				// MIGRATION Pages:
				$this->slugs['migratedb2contacts']	= "migrate-customers-db2";
				$this->slugs['migratedal3']			= "migrate-dal3";


				// emails
				$this->slugs['email-templates'] = 'zbs-email-templates';

				// tag manager
				$this->slugs['tagmanager']	= "tag-manager";

				#} Deletion and no access
				$this->slugs['zbs-deletion']		= 'zbs-deletion';
				$this->slugs['zbs-noaccess']		= 'zbs-noaccess';

				#} Install helper
				$this->slugs['zerobscrm-install-helper'] = "zerobscrm-install-helper";

				#} File Editor
				$this->slugs['editfile'] = "zerobscrm-edit-file";

				#} Extensions Deactivated error
				$this->slugs['extensions-active'] = "zbs-extensions-active";

				


			// Transients
			// These are transients which CRM owns which can be set via jpcrm_set_jpcrm_transient() etc.

				// Licensing prompts
				$this->transients['jpcrm-license-modal'] = false;
				
	}



	/**
	 * Include required core files used in admin and on the frontend. Note. In the main
	 * file it was included everything on front end too. Can move the relevant ones 
	 * to if ( $this->is_request( 'admin' ) ) { } once initial tests complete.
	 */
	public function includes() {


		// Admin messages (for any promos etc)
		require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.PluginAdminNotices.php');


		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('includes');
		// =================== / General Perf Testing =========================
		// ====================================================================


		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.GeneralFuncs.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.DateTime.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.AdminPages.Checks.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.ScriptsStyles.php');

		#} Settings
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Config.Init.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'wh.config.lib.php');
		
		#} WP REST API SUPPORT (better performant AJAX)
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.REST.php');

		#} General store of Error Codes
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.ErrorCodes.php');

		// Language modifiers (e.g. Company -> Organisation)
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-language.php');

		// Segment conditions; hereafter we can extend the zeroBSCRM_segmentCondition class
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-segment-conditions.php');


		#} DATA

			#} DAL VERSION SWITCH 
			if ($this->isDAL3()){

				// DAL3
				// Here we include: 
				// - DAL 3 (base class)
				// - DAL 3 Objects
				// - DAL3.Helpers.php (our helper funcs)

				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.php');

				// 3.0 DAL objs:
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.ObjectLayer.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Contacts.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Companies.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Segments.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Quotes.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.QuoteTemplates.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Invoices.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Transactions.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Forms.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Events.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.EventReminders.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.Logs.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Obj.LineItems.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Export.php');

				// helper funcs
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Helpers.php');

				// drop-in-replacement for previous global fields (uses models from objs now.)
				// NOTE: Rather than initially hard-typed, this now needs to WAIT until DAL3 initialised
				// ... so field Globals available LATER in build queue in DAL3+
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL3.Fields.php'); 
				

				#} Metaboxes v3.0
					
					// Root classes
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBox.php');
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Logs.php');
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Tags.php');
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.ExternalSources.php');

				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Contacts.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Companies.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.TagManager.php');

				#} 3.0 + ALL are in our metaboxes
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Quotes.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.QuoteTemplates.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Invoices.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Ownership.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Events.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Transactions.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Forms.php');

				// NO CPTs! YAY!
				
			} elseif ($this->isDAL2()){

				// DAL2 - v2.53 :)
				// Here we include: 
				// - DAL 2 (modified to contain DAL, Obj layer class, and contacts, segments + logs as classes)
				// - DAL 2 legacy support
					// - Original DAL (Until we've migrated ALL funcs from DAL1 -> DAL2 (other objs left to do)) (This is included in DAL2.LegacySupport.php)
				// NOPE. // - DAL 3 Objects, because DAL2 calls them, and loading them will assist MIGRATION 2->2.5 :)

				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.php');

				// legacy supp (DAL1+DAL2 funcs)
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.LegacySupport.php');

				// Root classes
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBox.php'); 
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Tags.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Logs.php');

				#} Metaboxes v3.0 (these are already migrated by v2.53)
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.ExternalSources.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Contacts.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Companies.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.TagManager.php');

				#} Metaboxes (v2)
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Quotes.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Invoices.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.ExternalSources.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Ownership.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Events.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Transactions.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Forms.php');

				// still using CPT's + basic fields
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CPTs.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL.Fields.php');

				// LOCK out edit page :)
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes3.Locked.php');
				
			} else {

				// sub dal 2?
				// if ($this->dal_version == '1.0')

				// v1.0 DAL (as yet un-migrated to 2.53+ )
				// Here we include: 
				// - Original DAL
				// - DAL 1 legacy support
				// - DAL 2 (as used by migration routine)

					// AS AT DAL3, This gets fully moved into DAL.Legacy Support.php 
					//require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL.php');

				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL1.LegacySupport.php');
				// DAL2 fractured now, need a few:
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.php');
					/* Nope, undid this // from 2.99+ DAL2 got subdivided (these are needed for 1->2 migrations)
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.ObjectLayer.php');
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.Obj.Contacts.php');
					require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.Obj.Logs.php'); */

				// LOCK out edit page :)
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Locked.php');

				// still using CPT's + basic fields
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CPTs.php');
				require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL.Fields.php');

			}

		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.ExternalSources.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DataIOValidation.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Database.php');

		#} Split out DAL2:
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DAL2.Mail.php');

		#} Admin Pages
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.AdminStyling.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.AdminPages.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.FormatHelpers.php');

		#} Dashboard Boxes - WH Q why do we also need to define VARS for these, require once only requires once, right?
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.DashboardBoxes.php');

		#} The kitchen sink
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Migrations.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Compatibility.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Localisation.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Extensions.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Actions.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Menus.WP.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Menus.Top.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.License.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Menus.Learn.php');
		

		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Permissions.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.ScreenOptions.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Inventory.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.ReWriteRules.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-mail-templating.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-templating.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-templating-placeholders.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MailTracking.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.InternalAutomator.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CRON.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Social.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CustomerSearch.php');

		#} Secondary
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.AJAX.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.WYSIWYGButtons.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CustomerFilters.php');	   	
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.InternalAutomatorRecipes.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.FileUploads.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Forms.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.InvoiceBuilder.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.QuoteBuilder.php');
    
		// Ajax for /admin/*
		require_once(ZEROBSCRM_PATH . 'admin/email/ajax.php');

		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Export.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.SystemChecks.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.IntegrationFuncs.php');
    
		// Temporarily removed until MC2 catches up + finishes Mail Delivery: 
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Mail.php');

		#} OBJ List Class (ZeroBSCRM.List.php) & List render funcs (ZeroBSCRM.List.Views.php) & List Column data
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.List.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.List.Views.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.List.Columns.php');


		#} OBJ Edit & Delete Classes (ZeroBSCRM.Edit.php) 
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Edit.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Delete.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.TagManager.php');


		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Core.Page.Controller.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Edit.Segment.php');

		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.List.Events.php');
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.List.CompletedEvents.php');

		#} Semantic UI Helper + columns list
		require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.SemanticUIHelpers.php');

		#} Put Plugin update message (notifications into the transient /wp-admin/plugins.php) page.. that way the nag message is not needed at the top of pages (and will always show, not need to be dismissed)
		require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.PluginUpdates.php');

		// v3.0 update coming, warning
		require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.PluginUpdates.ImminentRelease.php');

		#} FROM PLUGIN HUNT THEME - LOT OF USEFUL CODE IN HERE. 
		require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.NotifyMe.php');

		// load dependency checker (since 4.5.0)
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-dependency-checker.php');

		// load feature sniffer (since 4.5.0)
		require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-feature-sniffer.php');

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// if we need CLI stuff
		}

		if ( $this->is_request( 'admin' ) ) {			//admin includes..

		}

		if ( $this->is_request( 'frontend' ) ) {		//ones for front end stuff

		}

		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {

		}

		

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('includes');
		// =================== / General Perf Testing =========================
		// ====================================================================


	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks() {

	    #} General activation hook: DB check, role creation
	    register_activation_hook(ZBS_ROOTFILE, array($this, 'install') );

	    ##WLREMOVE 
	    #} WIZARD hook
	    // moved into 'install' activation hook (centralised = better) 
	    // v2.96.2 register_activation_hook(ZBS_ROOTFILE, array($this, 'wizard') );
	    ##/WLREMOVE

	    #} Pre-init Hook
		do_action('before_zerobscrm_init');

	    #} After all the plugins have loaded (THESE FIRE BEFORE INIT)
	    add_action('plugins_loaded', array($this, 'load_textdomain' ) ); #} Translations
	   	// this moved to post_init_plugins_loaded below, needs to be post init: add_action('plugins_loaded', array($this, 'after_active_plugins_loaded') );
	    
	    #} Initialise
		
		// our 'pre-init', this is the last step before init
	    // ... and loads settings :)
		//add_action('admin_init', array($this, 'preInit'), 1);
		add_action('init', array($this, 'preInit'), 1);

		// our formal init
		add_action('init', array($this, 'init'), 10);

		// post init (init 99)
		add_action('init', array($this, 'postInit'), 99);
		
		#} Admin init - should condition this per page.. 
		add_action('admin_init', array($this, 'admin_init') );

		#} Add thumbnail support?
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );

	    #} Extension links
		add_filter( 'plugin_action_links_' . plugin_basename(ZBS_ROOTFILE), array($this, 'add_action_links' ));



	    #} Install/uninstall - use uninstall.php here
	    register_deactivation_hook(ZBS_ROOTFILE, array($this ,'uninstall') );


	}

	public function filterExtensions($extensions_array = false){
		
		$extensions_array =  apply_filters('zbs_extensions_array', $extensions_array);

		//remove dupes - even this doesn't seem to remove the dupes!
		return array_unique($extensions_array);


		/* WH wrote this in 2.97.7, but probs not neceessary, not adding to not break anything
		// only apply filter if legit passed
		if (is_array($extensions_array) && count($extensions_array) > 0) 
			$extensions_array = apply_filters('zbs_extensions_array', $extensions_array);
		else // else pass it with empty:
			$extensions_array = apply_filters('zbs_extensions_array', array());

		return $extensions_array; */

	}

	// load initial external sources
	private function loadBaseExternalSources(){

		// simply loads our initial set from array, for now.
		$this->external_sources = zeroBS_baseExternalSources();

	}

	// load any extra hooked-in external sources
	private function loadExtraExternalSources(){

		// load initials if not loaded/borked
		if (!is_array($this->external_sources) || count($this->external_sources) < 1){

			// reload initial
			$this->loadBaseExternalSources();

		}

		// should be guaranteed that this->external_sources is an array now, but if for god-knows what reason, it's not, error.
		if (!is_array($this->external_sources)){

			// error out? (hard error not useful as err500's peeps)
			// ... rude text? (no translation, this way if someone EVER sees, they'll hopefully tell us)
			echo 'CRM ERROR #399: No external sources!<br>';

			// should NEVER happen:
			$this->external_sources = array();

		}

		// NOW we apply any filters to a blank array, then merge that with our HARD typed array to insure we never LOOSE originals
		$newExtSources 	= $this->filterExternalSources(array());
		// ^^ this is kind of miss-use of filters, but it'll work well here.

		// if anything to add, manually parse here (for complete control)
		if (is_array($newExtSources) && count($newExtSources) > 0) foreach ($newExtSources as $extKey => $extDeets){

			// will look like this:
			//$external_sources['woo'] = array('WooCommerce', 'ico' => 'fa-shopping-cart');

			// override/add to main (if checks out):
			if (is_string($extKey) && !empty($extKey) && is_array($extDeets) && count($extDeets) > 0){

				// seems in right format
				$this->external_sources[$extKey] = $extDeets;

			}


		} // / if any new to add

		// at this point $this->external_sources should be fully inline with apply_filter's added, 
		// but will NEVER lack the original 'pack'
		// and 100% will be an array.

	}

	// simply applies filters to anny passed array
	// NOTE: From 2.97.7 this is only taken as a 'second' layer, as per loadExtraExternalSources() above.
	// ... so it can stay super simple.
	public function filterExternalSources($approved_sources=false){

		return apply_filters('zbs_approved_sources', $approved_sources);

	}


	#} Build-out Object Models
	public function buildOutObjectModels(){


		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('customfields');
		// =================== / General Perf Testing =========================
		// ====================================================================


		#} Unpack Custom Fields + Apply sorts
		zeroBSCRM_unpackCustomFields();
		zeroBSCRM_unpackCustomisationsToFields(); 
		if (1 == 1){ #} switch off for perf?
			zeroBSCRM_applyFieldSorts();
		}

		#} Unpacks any settings logged against listview setups
		zeroBSCRM_unpackListViewSettings();

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('customfields');
		// =================== / General Perf Testing =========================
		// ====================================================================

	}


	/* Don't think we need this 
	#} thumbnail support - :) 
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'product', 'thumbnail' );
	} */

	public function setup_environment(){
		// Don't think we need this $this->add_thumbnail_support();  //add thumbnail support
	}


	public function add_action_links ( $links ) {
		$mylinks = array(
		'<a href="https://jetpackcrm.com/extensions/">'. __("Extensions","zero-bs-crm") . '</a>',
		);
		return array_merge( $links, $mylinks );
	}


	public function admin_menu(){

		// v3 we moved to custom menu build (uses $this->menu)
		if ($this->isDAL3())
			$this->applyMenu();
		else
			// < v3 (old) menus: (CPTs etc.)
			zeroBSCRM_admin_menu(); 

		// hook for extensions to add menus :)
		do_action('zerobs_admin_menu');

	}

	
	// v3.0 + this replaces zeroBSCRM_admin_menu
	// ... takes $zbs->menu and adds any wp menus based on the array
	private function applyMenu(){

		// build init, if not there
		if ( ! isset( $this->menu ) && ! is_array( $this->menu ) ) {
			$this->menu = zeroBSCRM_menu_buildMenu();
		}
		// ready?
		if ( isset( $this->menu ) && is_array( $this->menu ) ) {

		  	// Here we apply filters, this allows other ext etc. to modify menu items before we priotise + build
			$menu = apply_filters( 'zbs_menu_wpmenu', $this->menu );

			// remove non-permissioned
			$menu = zeroBSCRM_menu_securityGuard($menu);

			// resort based on 'order'
			$menu = zeroBSCRM_menu_order($menu);

			// output (adds menus)
			zeroBSCRM_menu_applyWPMenu($menu);

		}

	}



	public function post_init_plugins_loaded(){

		#} renamed to postSettingsIncludes and moved into that flow, (made more logical sense)

		#} Forms - only initiate if installed :)
		if (zeroBSCRM_isExtensionInstalled('forms')) zeroBSCRM_forms_includeEndpoint();

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('postsettingsincludes');
		// =================== / General Perf Testing =========================
		// ====================================================================
	}

	public function preInit(){

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('preinit');
		// =================== / General Perf Testing =========================
		// ====================================================================

		global $zeroBSCRM_Conf_Setup, $zbscrmApprovedExternalSources;
		
		#} Init DAL (DAL2, now always enabled)
		$this->DAL = new zbsDAL;

		#} ASAP after DAL is initialised, need to run this, which DEFINES all DAL3.Obj.Models into old-style $globalFieldVars
		#} #FIELDLOADING
		if ($this->isDAL3()) zeroBSCRM_fields_initialise();

		#} Setup Config (centralises version numbers temp)
		global $zeroBSCRM_Conf_Setup; 
		$zeroBSCRM_Conf_Setup['conf_pluginver'] = $this->version;
		$zeroBSCRM_Conf_Setup['conf_plugindbver'] = $this->db_version;		

		// Not needed yet :) do_action( 'before_zerobscrm_settings_init' );

		#} Init settings + sources
		$this->settings   			= new WHWPConfigLib($zeroBSCRM_Conf_Setup); 

		// register any modules with core
		$this->jpcrm_register_modules();

		// external sources, load, then initially filter
		$this->loadBaseExternalSources();
		$this->loadExtraExternalSources();

		// This just sets up metaboxes (empty array for now) - see zeroBSCRM_add_meta_box in Edit.php
		if (!is_array($this->metaboxes)) $this->metaboxes = array();

		#} This houses includes which need to fire post settings model load
		// NOTE: BECAUSE this has some things which add_action to init
		// ... this MUST fire on init with a priority of 1, so that these still "have effect"
		$this->postSettingsIncludes();

		// TEMP (ext update for 2.5 notice):
		if (defined('ZBSTEMPLEGACYNOTICE')) zeroBS_temp_ext_legacy_notice();

		// Legacy support for pre v2.50 settings in extensions
		zeroBSCRM_legacySupport();

		// load dependency checker for any modules/extensions
		$this->dependency_checker = new JPCRM_DependencyChecker;

		// load feature sniffer to alert user to available integrations
		$this->feature_sniffer = new JPCRM_FeatureSniffer;
		$this->jpcrm_sniff_features();

		// load included modules
		// this needs to be fired early so modules can hook into third-party plugin actions/filters
		do_action( 'jpcrm_load_modules' );

		// fire an action
		do_action( 'after_zerobscrm_settings_preinit' );

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('preinit');
		// =================== / General Perf Testing =========================
		// ====================================================================

	}

	public function postSettingsIncludes(){

	// ====================================================================
	// ==================== General Perf Testing ==========================
	if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('postsettingsincludes');
	// =================== / General Perf Testing =========================
	// ====================================================================

		#} extensions :D - here are files that don't need including if they're switched off...
		#} ^^ can probably include this via free extension manager class (longer term tidier?)
		// WH addition: this was firing PRE init (you weren't seeing because no PHP warnings...needs to fire after)
   	
		#Retrieve settings
		#$zbsCRMTempSettings = $zbs->settings->getAll(); use zeroBSCRM_isExtensionInstalled

		#} free extensions setup (needs to be post settings)
		zeroBSCRM_freeExtensionsInit();

		#} CSV Importer LITE
   		#} only run all this is no PRO installed :)
		if (!zeroBSCRM_isExtensionInstalled('csvpro') && zeroBSCRM_isExtensionInstalled('csvimporterlite') && !defined('ZBSCRM_INC_CSVIMPORTERLITE')) require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.CSVImporter.php');

		#} Customer Portal
		#} This removes functions which are used (Quotes and Invoices are both "previewed" on the Portal)
		if(zeroBSCRM_isExtensionInstalled('portal') && !defined('ZBSCRM_INC_PORTAL')) require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Portal.php');

		#} API
		if(zeroBSCRM_isExtensionInstalled('api') && !defined('ZBSCRM_INC_API')) require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.API.php');

		#} If zbs admin: Tour
		if(zeroBSCRM_isZBSAdminOrAdmin() && !defined('ZBSCRM_INC_ONBOARD_ME')) require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.OnboardMe.php');

		#} If usage tracking is active - include the tracking code.
		$tracking_active = zeroBSCRM_getSetting('shareessentials');
		if($tracking_active){
			require_once( ZEROBSCRM_INCLUDE_PATH . 'jpcrm-usage-tracking.php');
			$this->tracking = new jpcrm_usage_tracking;
		}

		#} Ownership
        $usingOwnership = $this->settings->get('perusercustomers');
		if ($usingOwnership && !$this->isDAL3()) {
			if (!class_exists('zeroBS__Metabox')) require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBox.php'); 
			require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.MetaBoxes.Ownership.php');
		}

		if ( $this->isDAL3() && zeroBSCRM_isExtensionInstalled( 'jetpackforms' ) ) {
			#} Jetpack - can condition this include on detection of Jetpack - BUT the code in Jetpack.php only fires on actions so will be OK to just include
			require_once( ZEROBSCRM_INCLUDE_PATH . 'ZeroBSCRM.Jetpack.php');
		}

	}

	#} Initialisation - enqueueing scripts/styles
	public function init(){

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('init');
		// =================== / General Perf Testing =========================
		// ====================================================================

		// this catches zbs_customers who may be accessing backend (shouldn't)
		$this->checkBackendAccess();

		global $zeroBSCRM_Conf_Setup, $zeroBSCRM_extensionsInstalledList, $zbscrmApprovedExternalSources;

		// unpack custom fieldsbuildOutObjectModels
		// #} #FIELDLOADING
		$this->buildOutObjectModels();
		
		#} Unpacks any settings logged against listview setups
		zeroBSCRM_unpackListViewSettings();

		#} Post settings hook - all meta views load in this hook :)
		// this has to fire for public + admin (things like mail campaigns rely on this for link tracking)
		do_action( 'after_zerobscrm_settings_init' );

		#} this needs to be post settings
		$this->extensions 			= $this->filterExtensions($zeroBSCRM_extensionsInstalledList); 

		#} Post extensions loaded hook
		do_action( 'after_zerobscrm_ext_init' );

		#} Load the admin menu. Can consider in here the 'ORDER' of the menu
		#} As well where extensions put their settings too 
	    add_action('admin_menu', array($this,'admin_menu') ); 

	    #} WH MOVED these from being added on init_hooks, to just calling them here, was legacy mess.
		// no longer used (now notifyme) add_action('init', array($this,'admin_noticies') ); #} load the admin noticies etc..
		//add_action('init', array($this,'include_updater') ); #} load the auto-updater class
		$this->include_updater();
		//add_action('init', 'zeroBSCRM_wooCommerceRemoveBlock'); #}  Admin unlock for ZBS users if WooCommerce installed 
		zeroBSCRM_wooCommerceRemoveBlock();
		//add_action('init', array($this, 'post_init_plugins_loaded')); #} Registers stuff that needs settings etc.
		$this->post_init_plugins_loaded();

		#} Setting Enabled List Inc:
		$useQuoteBuilder = $this->settings->get('usequotebuilder');
		if($useQuoteBuilder == "1"){

			// <DAL3 needed this old class, V3.0+ uses our list view class :)
			if (!$this->isDAL3() && !class_exists('zeroBSCRM_QuoteTemplate_List') && !function_exists('zeroBSCRM_render_quotetemplateslist_page')) 
				require_once( ZEROBSCRM_INCLUDE_PATH  . 'ZeroBSCRM.List.QuoteTemplate.php');
		}

	    #} Run any migrations
	    zeroBSCRM_migrations_run($this->settings->get('migrations'));

		#} Brutal override for feeding in json data to typeahead
		// WH: should these be removed now we're using REST?
		if (isset($_GET['zbscjson']) && is_user_logged_in() && zeroBSCRM_permsCustomers()){ exit(zeroBSCRM_cjson()); }
		if (isset($_GET['zbscojson']) && is_user_logged_in() && zeroBSCRM_permsCustomers()){ exit(zeroBSCRM_cojson()); }

		#} Brutal override for inv previews
		// No longer req. v3.0 + this is delivered via HASH URL
		// if (isset($_GET['zbs_invid']) && wp_verify_nonce($_GET['_wpnonce'], 'zbsinvpreview') && is_user_logged_in() && zeroBSCRM_permsInvoices()){ exit(zeroBSCRM_invoice_generateInvoiceHTML((int)sanitize_text_field($_GET['zbs_invid']),false)); }


		#} Catch Dashboard + redir (if override mode)
		#} but not for wp admin (wptakeovermodeforall)
		if ($this->settings->get('wptakeovermode') == 1) {
		
			// Not if API or Client Portal...
			// ... moved them inside this func..
			zeroBSCRM_catchDashboard();
		
		}

		#} JUST before cpt, we do any install/uninstall of extensions, so that cpt's can adjust instantly:
		zeroBSCRM_extensions_init_install();

		// stuff pre DAL3 needs CPTs etc.
		if (!$this->isDAL3()){
			
			#COMMENT} setup post types
			zeroBSCRM_setupPostTypes();

		}

		#} Here we do any 'default content' installs (quote templates) (In CPT <DAL3, In DAL3.Helpers DAL3+)
		zeroBSCRM_installDefaultContent();
				
		#} Admin & Public req
		wp_enqueue_script("jquery");

		#} Post Init hook
		do_action('zerobscrm_post_init');

		#} Public & non wp-cli only	
		if (!is_admin() && !defined( 'WP_CLI' )){

			#} Catch front end loads :)
			if ($this->settings->get('killfrontend') == 1){

				#http://wordpress.stackexchange.com/questions/12863/check-if-were-on-the-wp-login-page
				#} 2.0.2 also allow /wild stuff (e.g. welcome wiz)
				if (!zeroBSCRM_isLoginPage() && !zeroBSCRM_isWelcomeWizPage() && !zeroBSCRM_isAPIRequest()){

					 zeroBSCRM_stopFrontEnd();

				}
			}

		} 

		#} Finally, if it's an edit page for a (obj) which is hard owned by another, redir away
		// if admin, ignore :)
	    if ($this->settings->get('perusercustomers') && !zeroBSCRM_isZBSAdminOrAdmin()){

	    	#} Using ownership
		    if (!$this->settings->get('usercangiveownership')){

		    	// DAL3/pre switch
		    	if ($this->isDAL3()){

				    	#} is one of our dal3 edit pages
				    	if (zeroBSCRM_is_zbs_edit_page()){

				    		// in this specific case we pre-call globalise_vars
				    		// ... which later gets recalled if on an admin page (but it's safe to do so here too)
							// this moves any _GET into $zbsPage
							$this->globalise_vars();


							// this allows us to use these:
							$objID = $this->zbsvar('zbsid'); // -1 or 123 ID
							$objTypeStr = $this->zbsvar('zbstype'); // -1 or 'contact'

							// if objtypestr is -1, assume contact (default)
							if ($objTypeStr == -1)
								$objType = ZBS_TYPE_CONTACT;
							else
								$objType = $this->DAL->objTypeID($objTypeStr);

							// if is edit page + has obj id, (e.g. is not "new")
							// then check ownership
				    		if (isset($objID) && $objID > 0 && $objType > 0){

								$ownershipValid = $this->DAL->checkObjectOwner(array(

								            'objID'         => $objID,
								            'objTypeID'       => $objType,
								            'potentialOwnerID'       => get_current_user_id(),
								            'allowNoOwnerAccess' => true //?


								        ));

			    				#} If user ! has rights, redir
			    				if (!$ownershipValid){

							        #} Redirect to our "no rights" page
							        // OLD WAY header("Location: edit.php?post_type=".$postType."&page=".$this->slugs['zbs-noaccess']."&id=".$postID);
							        header("Location: admin.php?page=".$this->slugs['zbs-noaccess']."&zbsid=".$objID.'&zbstype='.$objTypeStr);
							        exit();


			    				} // / no rights.

				    		} // / obj ID
				    		
				    	} // / is edit page


		    	} else {

		    		// BEFORE DAL3 this could only ever be co/company edit page

				    	#} Not allowed to side-assign (if are, then allow to view others too..)
				    	if (zeroBSCRM_is_existingcustomer_edit_page() || zeroBSCRM_is_existingcompany_edit_page()){


				    		#} Get post id
				    		if (isset($_GET['post']) && !empty($_GET['post'])) $postID = (int)$_GET['post'];

				    		if (isset($postID) && $postID > 0){

				    			#} Admin sees all (dealt with above anyhow)
				    			if (!current_user_can('administrator')){

				    				#} If user ! has rights, redir
				    				if (!zeroBS_checkOwner($postID,get_current_user_id(),true)){

				    					//echo 'Checked owner: '.$postID.' against '.get_current_user_id().' result: '.zeroBS_checkOwner($postID,get_current_user_id()).'!'; 
				    					//exit();

				    					$postType = 'zbs_customer'; if (isset( $_GET['post'] )) $postType = get_post_type( $_GET['post'] );

								        #} Redirect to our "no rights" page
								        header("Location: admin.php?post_type=".$postType."&page=".$this->slugs['zbs-noaccess']."&id=".$postID);
								        exit();


				    				} // / no rights.

				    			} // / not admin

				    		} // / post id

				    	} // / is edit page

				} // is ! DAL 3
		    
		    } // / is setting usercangiveownership
			
	    } // / !is admin
	
		// debug
		// print_r($GLOBALS['wp_post_types']['zerobs_quo_template']); exit();

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('init');
		// =================== / General Perf Testing =========================
		// ====================================================================
	}

	public function postInit(){

		// pre 2.97.7:
		// WH note: filterExternalSources() above didn't seem to be adding them all (stripesync e.g. was being added on init:10)
		// ... so this gets called a second time (should be harmless) at init:99 (here)
		//$this->external_sources = $this->filterExternalSources($this->external_sources);

		// 2.97.7, switched to this, a more ROBUST check which only 'adds' and won't remove.
		$this->loadExtraExternalSources();

		// this allows various extensions to add users AFTER external sources def loaded
		do_action('after_zerobscrm_extsources_init');

		// This action should replace after_zerobscrm_extsources_init when we refactor load order in this class.
		// initially used by advanced segments to add custom field segment condition classes after the class is declared in jpcrm-segment-conditions.php
		do_action( 'jpcrm_post_init' );

	}

	public function admin_init(){

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('admin_init');
		// =================== / General Perf Testing =========================
		// ====================================================================


		// catch wiz + redirect (after activate)
		$this->wizardInitCheck();

		//only load if we are a ZBS admin page? Will this break the world?!?
		if(zeroBSCRM_isAdminPage()){

				#} apply any filters req. to the exclude-from-settings arr
				global $zbsExtensionsExcludeFromSettings; $zbsExtensionsExcludeFromSettings = apply_filters( 'zbs_exclude_from_settings', $zbsExtensionsExcludeFromSettings );

				// this moves any _GET into $zbsPage
				$this->globalise_vars();

				// this sets page titles where it can ($this->setPageTitle();)				
				add_filter( 'zbs_admin_title_modifier' , array($this, 'setPageTitle' ),10,2);

				#} This is a pre-loader for edit page classes, allows us to save data before loading the page :)
				zeroBSCRM_prehtml_pages_admin_addedit();

				// Again, necessary? do_action('before_zerobscrm_admin_init');

				// All style registering moved into ZeroBSCRM.ScriptsStyles.php for v3.0, was getting messy
				zeroBSCRM_scriptStyles_initStyleRegister();

				// JS Root obj (zbs_root)
				zeroBSCRM_scriptStyles_enqueueJSRoot();

				// Check for stored messages in case we were redirected.
				$this->maybe_retrieve_page_messages();

				#} Custom msgs (LEGACY < 3.0)
				if (!$this->isDAL3()){

					if (
							(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_customer') || 
							(!empty($postTypeStr) && $postTypeStr == 'zerobs_customer')
							) {
									add_filter('post_updated_messages', 'zeroBSCRM_improvedPostMsgsCustomers');

							}
					if (
							(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_company') || 
							(!empty($postTypeStr) && $postTypeStr == 'zerobs_company')
							) {
									add_filter('post_updated_messages', 'zeroBSCRM_improvedPostMsgsCompanies');

							}
					if (
							(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_invoice') || 
							(!empty($postTypeStr) && $postTypeStr == 'zerobs_invoice')
							) {
									add_filter('post_updated_messages', 'zeroBSCRM_improvedPostMsgsInvoices');

							}
					if (
							(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_quote') || 
							(!empty($postTypeStr) && $postTypeStr == 'zerobs_quote')
							) {
									add_filter('post_updated_messages', 'zeroBSCRM_improvedPostMsgsQuotes');

							}
					if (
							(isset($_GET['post_type']) && $_GET['post_type'] == 'zerobs_transaction') || 
							(!empty($postTypeStr) && $postTypeStr == 'zerobs_transaction')
							) {
									add_filter('post_updated_messages', 'zeroBSCRM_improvedPostMsgsTransactions');

							}
				}


		}else{

			#} Hook the root to here - although this doesn't show on edit post page.. weirdly
			wp_enqueue_script('zerobsnonzbsglobal', plugins_url('/js/ZeroBSCRM.nonadmin.global'.wp_scripts_get_suffix().'.js',ZBS_ROOTFILE), array( 'jquery' ), $this->version);
 			

 			// THIS is PUBLIC zbs_root (not admin side, which is now made in /includes/ZeroBSCRM.ScriptsStyles.php)
		    $zbs_root = array(
                'root' => ZEROBSCRM_URL,
                'localeOptions' => zeroBSCRM_date_localeForDaterangePicker()
            );
			wp_localize_script( 'zerobsnonzbsglobal', 'zbs_root', $zbs_root );

		}


		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('admin_init');
		// =================== / General Perf Testing =========================
		// ====================================================================



		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('after-zerobscrm-admin-init');
		// =================== / General Perf Testing =========================
		// ====================================================================

		// Action hook
		do_action('after-zerobscrm-admin-init');

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('after-zerobscrm-admin-init');
		// =================== / General Perf Testing =========================
		// ====================================================================

	}

	// this checks whether any extensions are active which might bring down an install to 500 error
	// backstop in case extensions used which don't deactivate for whatver reason (being doubly sure in core)
	// as part of UX work also make sure all extensions are classified and only load when core hook triggers
	// some users were still hitting 500 errors so worth having this in place to protect us / help retain / PICNIC
	// wh renamed: check_active_zbs_extensions -> pre_deactivation_check_exts_deactivated
	function pre_deactivation_check_exts_deactivated(){
			global $zbs;
			#} from telemetry however what if someone has extensions installed this should show up
			#} this is the full count (not whether they are active)
			$extensions_installed = zeroBSCRM_extensionsInstalledCount(true);
			if($extensions_installed > 0){				
				//tried to add an error above the plugins.php list BUT it did not seem to show
				//instead re-direct to one of our pages which tells them about making sure extensions are
				//decativated before deactivating core
				wp_safe_redirect(admin_url('admin.php?page=' . $zbs->slugs['extensions-active']));
				die(); //will killing it here stop deactivation?

				// failsafe?
				return false;
			}
			return true;
	}

	public function uninstall(){

		
			//this will just abort the wizard wont it, not the actual deactivation so redirect to our own error page
			if($this->pre_deactivation_check_exts_deactivated()){
				
				##WLREMOVE
		
				// Remove roles :) 
				zeroBSCRM_clearUserRoles();

				#Debug delete_option('zbsfeedback');exit();
				$feedbackAlready = get_option('zbsfeedback');

				// if php notice, (e.g. php ver to low, skip this)				
				if (!defined('ZBSDEACTIVATINGINPROG') && $feedbackAlready == false && !defined('ZBSPHPVERDEACTIVATE')){

					#} Show stuff + Deactivate
					#} Define is to stop an infinite loop :)
					#} (Won't get here the second time)
					define('ZBSDEACTIVATINGINPROG',true);

					#} Before you go...
					if (function_exists('file_get_contents')){
						
						#} telemetry
						// V3.0 No more telemetry if (!zeroBSCRM_isWL()) zeroBSCRM_teleLogAct(3);

						try {

							#} Also manually deactivate before exit
							deactivate_plugins( plugin_basename( ZBS_ROOTFILE ) );

							#} require template
							require_once( ZEROBSCRM_PATH . 'admin/activation/before-you-go.php' ); exit();


						} catch (Exception $e){

							#} Nada 

						}

					}	

				}

				##/WLREMOVE


		} //end of check if there are extensions active

		
	}

	public function install(){

		// php ver check
		if ($this->phpCheck()){

			// has valid php ver

      		// this transient says 'we installed in last 30s'
      		set_transient( '_zbs_just_installed', true, 30 );

	      	// dir build
	      	zeroBSCRM_privatisedDirCheck();
	      	zeroBSCRM_privatisedDirCheckWorks(); // /_wip

		    #} Additional DB tables hook on activation (such as api keys table) - requires ZeroBSCRM.Database.php
		    zeroBSCRM_database_check();

		    // roles
			zeroBSCRM_clearUserRoles();

			// roles + 
			zeroBSCRM_addUserRoles();

		}

	}

	// this func runs on admin_init and xxxx
	public function wizardInitCheck(){

		  // somehow this is firing pre activation, but no matter, just check php before...
		  if ($this->phpCheck()){

		  		// php ver ok

		      // Bail if no activation redirect
		      if ( ! get_transient( '_zbs_just_installed' ) ) {
		        return;
		      }

		      // Bail if activating from network, or bulk
		      if ( is_network_admin()){ # WH removed this, if bulk, still do it :D || isset( $_GET['activate-multi'] ) ) {
		        return;
		      }

		      // Delete the redirect transient
		      delete_transient( '_zbs_just_installed' );

				
			##WLREMOVE
		      // show only if not WL
		      // Bail if already completed wizard:
		      // Redirect to bbPress about page
		      $runCount = get_option('zbs_wizard_run',0);
		      if($runCount <= 0){

				#} require template
				require_once( ZEROBSCRM_PATH . 'admin/activation/welcome-to-jpcrm.php' ); exit();

		      }
			##/WLREMOVE

		  }
	}

	/**
	 * Loads the Plugin Updater Class
	 *
	 * @since 2.97.x
	 */
	public function include_updater(){

		#} Initialise ZBS Updater Class 
		global $zeroBSCRM_Updater;
		if (!isset($zeroBSCRM_Updater)) 
			$zeroBSCRM_Updater = new zeroBSCRM_Plugin_Updater( 
				$this->urls['api'], 
				$this->update_api_version, 
				ZBS_ROOTFILE, 
				array(
				'version' 	=> $this->version,
				'license' 	=> false,      				//license initiated to false..
			)
		);

	}

	/**
	 * 
	 * Loads the modules (core extensions) included with core
	 * 
	 * Ideally this would read the modules in dynamically from /includes/modules and they would load
	 * into the extensions page without hard-coding, but this is a first iteration.
	 * 
	 * Ultimately we should probably include Jetpack Forms and maybe CSV Importer.
	 * 
	 * Modularisation of this manner may allow easier drop-in integrations by third-party devs as well.
	 *
	 */
	public function jpcrm_register_modules() {
		require_once( ZEROBSCRM_INCLUDE_PATH . 'modules/givewp/jpcrm-givewp-init.php' );
	}

	/**
	 * 
	 * Check to see if there's an integration our users could be using
	 * At some point we should modularize this as well
	 * 
	 */
	public function jpcrm_sniff_features() {
		$this->feature_sniffer->sniff_for_plugin(
			array(
				'feature_slug'		=> 'jetpackforms',
				'plugin_slug'			=> 'jetpack/jetpack.php',
				'more_info_link'	=> 'https://kb.jetpackcrm.com/knowledge-base/jetpack-contact-forms/'
			)
		);
		do_action('jpcrm_sniff_features');
		$this->feature_sniffer->show_feature_alerts();
	}

	/**
	 * Ensures fatal errors are logged so they can be picked up in the status report.
	 *
	 * @since 3.2.0
	 */
	public function log_errors() {
		$error = error_get_last();
		if ( E_ERROR === $error['type'] ) {
			$this->write_log($error['message'] . PHP_EOL);    //check this method.. should be OK
		}
	}

	public function write_log( $log ){
		    if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Check the active theme.
	 *
	 * @since  2.6.9
	 * @param  string $theme Theme slug to check.
	 * @return bool
	 */
	private function is_active_theme( $theme ) {
		return get_template() === $theme;
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
	}




	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/woocommerce/woocommerce-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/woocommerce-LOCALE.mo
	 */


	public function load_textdomain() {

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_startTimer('loadtextdomain');
		// =================== / General Perf Testing =========================
		// ====================================================================

		load_plugin_textdomain( 'zero-bs-crm', FALSE, ZBS_LANG_DIR); //basename( dirname( ZBS_ROOTFILE ) ) . '/languages' ); //plugin_dir_path( ZBS_ROOTFILE ) .'/languages'

		// ====================================================================
		// ==================== General Perf Testing ==========================
		if (defined('ZBSPERFTEST')) zeroBSCRM_performanceTest_closeGlobalTest('loadtextdomain');
		// =================== / General Perf Testing =========================
		// ====================================================================

	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', ZBS_ROOTFILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( ZBS_ROOTFILE ) );
	}



	/**
	 * Check if Settings exists, load default if not
	 *
	 * @return string
	 */
	public function checkSettingsSetup() {
		global $zeroBSCRM_Conf_Setup;
		if (!isset($this->settings)) {
			$this->settings = NULL; //https://stackoverflow.com/questions/8900701/creating-default-object-from-empty-value-in-php
			$this->settings = new WHWPConfigLib($zeroBSCRM_Conf_Setup);
		}
	}

	/**
	 * Check if user has capabilities to view backend :)
	 *
	 * @return string
	 */
	public function checkBackendAccess(){
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// if zbs_customer in admin area, kick em out :)
		if (zeroBSCRM_isRole('zerobs_customer')) {

			if (is_admin()){
				$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url( '/' );
				if (current_user_can( 'zerobs_customer' )) exit( wp_redirect( $redirect ) );
			}
			
			// and remove wp bar from front end
			add_filter('show_admin_bar', '__return_false');
		}

	}


	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Globalise ZBS Vars
	 *
	 * @return nout
	 */
	public function globalise_vars() {

		// here, where things are consistently used through a page
		// e.g. admin.php?page=zbs-add-edit&action=edit&zbsid=3
		// we globally set them to save time later :)
		global $zbsPage; $zbsPage = array();
		
		// zbsid
		if(isset($_GET['zbsid']) && !empty($_GET['zbsid'])){

		   $zbsid = (int)sanitize_text_field($_GET['zbsid']);

		  // if $zbsid is set, make it a GLOBAL (save keep re-getting)
		  // this is used by metaboxes, insights + hypothesis, titles below etc. DO NOT REMOVE
		  if ($zbsid > 0){ 
		    $zbsPage['zbsid'] = $zbsid;
		  }

		}
		
		// page
		if(isset($_GET['page']) && !empty($_GET['page'])){

		   $page = sanitize_text_field( $_GET['page'] );

		  // if $page is set, make it a GLOBAL (save keep re-getting)
		  // this is used by metaboxes, insights + hypothesis, titles below etc. DO NOT REMOVE
		  if (!empty($page)){ 
		    $zbsPage['page'] = $page;
		  }

		}
		
		// action
		if(isset($_GET['action']) && !empty($_GET['action'])){

		   $action = sanitize_text_field( $_GET['action'] );

		  // if $action is set, make it a GLOBAL (save keep re-getting)
		  // this is used by metaboxes, insights + hypothesis, titles below etc. DO NOT REMOVE
		  if (!empty($action)){ 
		    $zbsPage['action'] = $action;
		  }

		}
		
		// type
		if(isset($_GET['type']) && !empty($_GET['type'])){

		   $type = sanitize_text_field( $_GET['type'] );

		  // if $type is set, make it a GLOBAL (save keep re-getting)
		  // this is used by metaboxes, insights + hypothesis, titles below etc. DO NOT REMOVE
		  if (!empty($type)){ 
		    $zbsPage['type'] = $type;
		  }

		}
		
		// zbstype
		if(isset($_GET['zbstype']) && !empty($_GET['zbstype'])){

		   $zbstype = sanitize_text_field( $_GET['zbstype'] );

		  // if $zbstype is set, make it a GLOBAL (save keep re-getting)
		  // this is used by metaboxes, insights + hypothesis, titles below etc. DO NOT REMOVE
		  if (!empty($zbstype)){ 
		    $zbsPage['zbstype'] = $zbstype;
		  }

		}

		// if action = 'edit' + no 'zbsid' = NEW EDIT (e.g. new contact)
		if (isset($zbsPage['action']) && $zbsPage['action'] == 'edit' && (!isset($zbsPage['zbsid']) || $zbsPage['zbsid'] < 1)){

			$zbsPage['new_edit'] = true;
			
		}


	}

	/**
	 * Get Globalised ZBS Vars
	 *
	 * @return str/int/bool
	 */
	public function zbsvar($key='') {

		// globalise_vars returned
		global $zbsPage; 
		
		// zbsid
		if (is_array($zbsPage) && !empty($key) && isset($zbsPage[$key]) && !empty($zbsPage[$key])){

		   return $zbsPage[$key];

		}

		return -1;

	}

	/**
	 * Tries to set page title (where it can)
	 *
	 * @return nout
	 */
	public function setPageTitle($title='',$adminTitle='') {

		// default
		$pageTitle = ( ( $adminTitle == '' ) ? __( 'Jetpack CRM','zero-bs-crm' ) : $adminTitle );

		// useful? global $post, $title, $action, $current_screen;
		// global $zbsPage; print_r($zbsPage); exit();

		// we only need to do this for pages where we're using custom setups (not added via wp_add_menu whatever)
		if ($this->zbsvar('page') != -1){

			switch ($this->zbsvar('page')){

				case 'zbs-add-edit':

					// default
					$pageTitle = __('View | Jetpack CRM'.$this->zbsvar('action'),'zero-bs-crm');

					// default/no type passed
					$objType = __('Contact','zero-bs-crm');

					switch ($this->zbsvar('zbstype')){

						case 'contact':

							$objType = __('Contact','zero-bs-crm');
							break;

						case 'company':

							$objType = jpcrm_label_company();
							break;

						case 'segment':

							$objType = __('Segment','zero-bs-crm');
							break;

						case 'quote':

							$objType = __('Quote','zero-bs-crm');
							break;

						case 'invoice':

							$objType = __('Invoice','zero-bs-crm');
							break;

						case 'transaction':

							$objType = __('Transaction','zero-bs-crm');
							break;

						case 'event':

							$objType = __('Event','zero-bs-crm');
							break;

						case 'form':

							$objType = __('Form','zero-bs-crm');
							break;

						case 'quotetemplate':

							$objType = __('Quote Template','zero-bs-crm');
							break;

						case 'log':

							$objType = __('Log','zero-bs-crm');
							break;


					}

					// just formatting:
					if (!empty($objType)) $objType = ' '.$objType;

					switch ($this->zbsvar('action')){

						case 'edit':

							$pageTitle = __('Edit '.$objType.' | Jetpack CRM','zero-bs-crm');
							break;

						case 'delete':

							$pageTitle = __('Delete '.$objType.' | Jetpack CRM','zero-bs-crm');
							break;

						case 'view':

							$pageTitle = __('View '.$objType.' | Jetpack CRM','zero-bs-crm');
							break;


					}

					break;

				case 'zerobscrm-emails':

					// default
					$pageTitle = __('Email Manager | Jetpack CRM','zero-bs-crm');

					break;
					
				case 'tag-manager':

					$pageTitle = __('Tag Manager | Jetpack CRM','zero-bs-crm');
					break;
					
				case 'zerobscrm-notifications':

					$pageTitle = __('Notifications | Jetpack CRM','zero-bs-crm');
					break;
					
				case 'zbs-email-templates':

					$pageTitle = __('Email Templates | Jetpack CRM','zero-bs-crm');
					break;
					
				case 'zbs-export-tools':

					$pageTitle = __('Export Tools | Jetpack CRM','zero-bs-crm');
					break;
					
				case 'zerobscrm-csvimporterlite-app':

					$pageTitle = __('Import Tools | Jetpack CRM','zero-bs-crm');
					break;

			}

		}


		return $pageTitle;

	}


	

	/**
	 * Get Current User's screen options for current page
	 * This requires add_filter on page to work :)
	 * // actually just use a global for now :) - so just set global $zbs->pageKey on page :)
	 * // 2.94.2+ can pass pagekey to get opts for page other than current (used for list view perpage)
	 *
	 * @return array() screen options
	 */
	public function userScreenOptions($pageKey=false) {

		// TO ADD LATER: (optionally) allow admins to create a 'forced' screen options set (e.g. metabox layout etc.)
		// ... forced or default :)

		$currentUserID = get_current_user_id();

		if (!$pageKey || empty($pageKey)){

			// actually just use a global for now :) - so just set global $zbs->pageKey on page :)
			$pageKeyCheck = apply_filters('zbs_pagekey', $this->pageKey); 

		} else $pageKeyCheck = $pageKey;

		if ($currentUserID > 0 && !empty($pageKeyCheck)){

			/* 
			Array
			(
			    [tabs_1] => zerobs-customer-logs,zerobs-customer-edit
			    [zerobs-customer-files] => self
			) 
			*/

			// retrieve via dal
			//print_r($this->DAL->userSetting($currentUserID,'screenopts_'.$currentPageKey,false));

			return $this->DAL->userSetting($currentUserID,'screenopts_'.$pageKeyCheck,false);

		}

		return array();

	}

	/**
	 * Shorthand for get_Current_user_id
	 *
	 * @return int user id
	 */
	public function user() {

		return get_current_user_id();

	}


	/**
	 * Shorthand for zeroBSCRM_getSetting('license_key')
	 *
	 * @return array() license settings (trimmed down)
	 */
	public function license() {

		// default
		$ret = array(
			'validity' 	=> false,
			'access'	=> 'none',
			'expires'	=> -1
			);

		// retrieve
  		$licenseKeyArr = zeroBSCRM_getSetting('license_key');

  		// return only these (not key, for simple semi-security? lol. not even)
  		if (is_array($licenseKeyArr)){
  			if (isset($licenseKeyArr['validity'])) $ret['validity'] = $licenseKeyArr['validity'];
  			if (isset($licenseKeyArr['access'])) $ret['access'] = $licenseKeyArr['access'];
  			if (isset($licenseKeyArr['expires'])) $ret['expires'] = $licenseKeyArr['expires'];
  		}

  		return $ret;

	}


	/**
	 * Returns true/false if has AT LEAST entrepreneur Bundle
	 *	... suspect this is an easy way to HACK out show promotional material. So rethink around that at some point.
	 * 
	 * @return bool 
	 */
	public function hasEntrepreneurBundleMin() {

		$license = $this->license();
		$valid = array('entrepreneur','reseller');
		$license['validity'] = ($license['validity'] === 'true'? true: false);
		if ($license['validity'] && (in_array($license['access'],$valid))) return true;

		return false;

	}


	/**
	 * Returns true/false if has AT LEAST entrepreneur Bundle
	 *	... suspect this is an easy way to HACK out show promotional material. So rethink around that at some point.
	 * 
	 * @return bool 
	 */
	public function hasFreelancerBundleMin() {

		$license = $this->license();
		$valid = array('freelancer','entrepreneur','reseller');
		$license['validity'] = ($license['validity'] === 'true'? true: false);
		if ($license['validity'] && (in_array($license['access'],$valid))) return true;

		return false;

	}


	/**
	 * Returns pretty label for subscription
	 *	... suspect this is an easy way to HACK out show promotional material. So rethink around that at some point.
	 * 
	 * @return string 
	 */
	public function getSubscriptionLabel($str='') {

		if (empty($str)) {
			// get from license
			$license = $this->license();
			if (isset($license['validity'])){
				$license['validity'] = ($license['validity'] === 'true'? true: false);
				if ($license['validity'] && isset($license['access']) && !empty($license['access'])) $str = $license['access'];
			}

		}

		switch ($str){

			case 'freelancer':
				return 'Freelancer Bundle';
				break;

			case 'entrepreneur':
				return 'Entrepreneur Bundle';
				break;

			case 'reseller':
				return 'Branded Bundle';
				break;

			// for all others, use this:
			default:
				return 'Extension: '.ucwords($str);
				break;

		}

		return false;

	}	


	// ========== Basic Library Management =========


	/**
	 * Retrieve array of details for a library
	 * Returns: array() or false
	 */
	public function lib( $libKey = '' ) {

        $libKey = $this->checkDompdfVersion( $libKey );

		if ( isset( $this->libs[ $libKey ] ) && is_array( $this->libs[$libKey] ) ) {

			// update path to use ZEROBSCRM_PATH
			$ret = $this->libs[ $libKey ];
			$ret['path'] = ZEROBSCRM_PATH.$this->libs[ $libKey ]['path'];
			$ret['include'] = ZEROBSCRM_PATH.$this->libs[ $libKey ]['include'];

			return $ret;
		}

		return false;
	}

	/**
	 * Retrieve root path for a library
	 * Returns: str or false
	 */
	public function libPath( $libKey = '' ) {

        $libKey = $this->checkDompdfVersion( $libKey );

		if ( isset( $this->libs[ $libKey ] ) && isset( $this->libs[ $libKey ]['path'] ) ) {
		    return ZEROBSCRM_PATH.$this->libs[ $libKey ]['path'];
        }

		return false;
	}

	/**
	 * Retrieve full include path for a library
	 * Returns: str or false
	 */
	public function libInclude( $libKey = '' ) {

        $libKey = $this->checkDompdfVersion( $libKey );

		if ( isset( $this->libs[$libKey] ) && isset( $this->libs[$libKey]['include'] ) ) {
		    return ZEROBSCRM_PATH.$this->libs[ $libKey ]['include'];
        }

		return false;
	}

	private function checkDompdfVersion( $libKey ) {

        if( $libKey === 'dompdf' && version_compare( '7.1.0', PHP_VERSION, '<=') ) {
            $libKey = 'dompdf-1';
        }

        return $libKey;
    }

	/**
	 * Retrieve version of a library
	 * Returns: str or false
	 */
	public function libVer($libKey=''){

		if (isset($this->libs[$libKey]) && isset($this->libs[$libKey]['version'])) return $this->libs[$libKey]['version'];

		return false;
	}

	/**
	 * Check if library already loaded
	 * Returns: bool
	 */
	public function libIsLoaded($libKey=''){

		if (isset($this->libs[$libKey]) && isset($this->libs[$libKey]['include']) && !isset($this->libs[$libKey]['loaded'])) return false;

		return true;
	}

	/**
	 * Load a library via include
	 * Returns: str or false
	 */
	public function libLoad( $libKey='' ) {

	    $libKey = $this->checkDompdfVersion( $libKey );

		if (
			isset($this->libs[$libKey]) && 
			isset($this->libs[$libKey]['include']) && 
			!isset($this->libs[$libKey]['loaded']) && 
			file_exists(ZEROBSCRM_PATH.$this->libs[$libKey]['include'])
		) {
			require_once(ZEROBSCRM_PATH.$this->libs[$libKey]['include']);
			$this->libs[$libKey]['loaded'] = true;
		}

		return false;
	}

	// ======= / Basic Library Management =========

	// =========== Templating Class ===============
	/**
	 * Loads templating placeholder class into this->templating_placeholders
	 * and returns it
	 * (Can be loaded on-demand)
	 */
	public function get_templating() {

		// load if not loaded
		if ( $this->templating_placeholders === false ){

			$this->templating_placeholders = new jpcrm_templating_placeholders();

		}
		
		return $this->templating_placeholders;
	}

	// ============ / Templating Class ============

	// =========== Error Coding ===================

	public function getErrorCode($errorCodeKey=-1){

		if ($errorCodeKey > 0){

			// load err codes if not loaded
			if (!isset($this->errorCodes)) $this->errorCodes = zeroBSCRM_errorCodes();

			// if set, return
			if (isset($this->errorCodes[$errorCodeKey])) return $this->errorCodes[$errorCodeKey];

		}

		return false;
	}

	// =========== / Error Coding ===================


	// =========== PHP VERSION CHECK ==============
	// runs on activation hook
	public function phpCheck() {

	    if (is_admin() && current_user_can( 'activate_plugins' )) {

			/* 
			http://php.net/eol.php
			https://en-gb.wordpress.org/about/requirements/
			https://kb.jetpackcrm.com/knowledge-base/php-version-zero-bs-crm/
			*/

			#} Check which parent plugin is req.
			$requirementsMet = true;
			if (version_compare(phpversion(), '5.6', '<')){ $requirementsMet = false; add_action( 'admin_notices',array($this,'oldManPHPNotice')); }

	        if (!$requirementsMet){

	        	// this stops 'before you go' hijacking deactivate
	        	define('ZBSPHPVERDEACTIVATE',1);

	        	#} Nope.
	        	deactivate_plugins( plugin_basename( ZBS_ROOTFILE ) ); 
		        if ( isset( $_GET['activate'] ) ) {
		            unset( $_GET['activate'] );
		        }

		        return false;

		    }
	    }

	    return true;
	}

	public function oldManPHPNotice(){
	    ?><div class="error"><p><?php _e('Jetpack CRM requires PHP version 5.6+, versions of PHP older than 5.6 are no longer supported.','zero-bs-crm'); ?> <a href="https://kb.jetpackcrm.com/knowledge-base/php-version-zero-bs-crm/" target="_blank"><?php _e('Please click here to resolve this','zero-bs-crm'); ?></a></p></div><?php 
	}
	// =========== / PHP VERSION CHECK ============
  
	private function get_page_messages_transient_key( $obj_type, $inserted_id ) {
		return sprintf( "pageMessages_%d_%d_%d", $this->DAL->objTypeID( $obj_type ), get_current_user_id(), $inserted_id );
	}

	private function maybe_retrieve_page_messages() {
		if ( zeroBS_hasGETParamsWithValues(array( 'admin.php' ),array('page'=>'zbs-add-edit','action'=>'edit')) 
			&& zeroBS_hasGETParams( [ 'admin.php' ], [ 'zbstype', 'zbsid' ] ) ) {
			$transient_key = $this->get_page_messages_transient_key( $_GET['zbstype'], $_GET['zbsid' ] );
			$page_messages = get_transient( $transient_key );
			if ( ! empty( $page_messages ) ) {
				$this->pageMessages = $page_messages;
				delete_transient( $transient_key );
			}
		}
	}

	public function new_record_edit_redirect( $obj_type, $inserted_id  ) {
		if ( ! empty( $this->pageMessages ) ) {
			$transient_key = $this->get_page_messages_transient_key( $obj_type, $inserted_id );
			set_transient( $transient_key, $this->pageMessages, MINUTE_IN_SECONDS );
		}
		wp_redirect( zbsLink( 'edit', $inserted_id, $obj_type ) );
		exit;
	}

}
