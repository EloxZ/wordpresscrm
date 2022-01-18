<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ToolsController extends BaseController {

	private $children ;
	public function __construct( $alias ) {
		parent::__construct( __( "Tools", 'wp-live-chat-support' ), $alias );
		$this->init_children( $alias );
	}

	public function view($return_html = false, $add_wrapper=true) {
		return $this->load_view( plugin_dir_path( __FILE__ ) . "tools_view.php",$return_html,$add_wrapper,$this->children );
	}

	private function init_children( $alias ) {
		$this->children    = array();
		$child             = new stdClass();
		$child->id         = "wplc_data_tools_tab";
		$child->controller = new DataToolsController( 'dataTools' );
		$this->children[]  = $child;
		add_action( "wplc_hook_run_page_" . $alias, function () {
			do_action( "wplc_hook_run_page_dataTools" );
		}, 9 );

		$child             = new stdClass();
		$child->id         = "wplc_departments_tab";
		$child->controller = new DepartmentsController( 'departments' );
		$this->children[]  = $child;
		add_action( "wplc_hook_run_page_" . $alias, function () {
			do_action( "wplc_hook_run_page_departments" );
		}, 9 );

		$child             = new stdClass();
		$child->id         = "wplc_custom_fields_tab";
		$child->controller = new CustomFieldsController( 'customFields' );
		$this->children[]  = $child;
		add_action( "wplc_hook_run_page_" . $alias, function () {
			do_action( "wplc_hook_run_page_customFields" );
		}, 9 );

		$child             = new stdClass();
		$child->id         = "wplc_quick_responses_tab";
		$child->controller = new QuickResponsesController( 'quickResponses' );
		$this->children[]  = $child;
		add_action( "wplc_hook_run_page_" . $alias, function () {
			do_action( "wplc_hook_run_page_quickResponses" );
		}, 9 );

		$child             = new stdClass();
		$child->id         = "wplc_webhooks_tab";
		$child->controller = new WebHooksController( 'webHooks' );
		$this->children[]  = $child;
		add_action( "wplc_hook_run_page_" . $alias, function () {
			do_action( "wplc_hook_run_page_webHooks" );
		}, 9 );
	}

}
?>