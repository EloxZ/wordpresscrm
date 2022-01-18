<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BaseController {
	protected $wplc_settings;
	protected $available_actions;
	protected $selected_action;
	protected $required_nonce_key;
	protected $view_data;
	protected $page_title;
	protected $page_alias;
	protected $db;

	public function __construct( $title, $alias, $custom_view = null ) {
		global $wpdb;
		global $wplc_error;
		if ( $wplc_error != null ) {
			$this->load_error_view( $wplc_error );
			exit;
		}

		$this->wplc_settings       = TCXSettings::getSettings();
		$this->available_actions   = [];
		$this->page_title          = $title;
		$this->page_alias          = $alias;
		$this->selected_action     = new TCXPageAction( "view" );
		$this->available_actions[] = $this->selected_action;
		$this->view_data           = array();
		$this->view_data["error"]  = new TCXError();
		$this->db                  = $wpdb;
		do_action( "wplc_hook_page_header_" . $alias );

		add_action( "wplc_hook_view_page_" . $alias, function () use ( $custom_view ) {
			if ( $custom_view != null && is_callable( array( $this, $custom_view ) ) ) {
				$this->$custom_view();
			} else {
				$this->view();
			}
		}, 10 );

	}

	public function run() {
		TCXUtilsHelper::update_stats( $this->page_alias );
		do_action( "wplc_hook_run_page_" . $this->page_alias );
		do_action( "wplc_hook_view_page_" . $this->page_alias );
	}

	protected function load_error_view( $error ) {
		include_once( plugin_dir_path( __FILE__ ) . "error_view.php" );
	}

	protected function load_view( $filepath, $return_html=false, $add_wrapper=true, $children = array() ) {
		$data               = $this->convert_view_data( $this->view_data );
		$data["page_title"] = $this->page_title;

		$view_data                    = array_merge( $data, $_GET );
		$view_data['wplc_settings']   = $this->wplc_settings;
		$view_data['selected_action'] = $this->selected_action;

		unset( $data );
		$view_html =TCXUtilsHelper::evaluate_php_template( $filepath, $view_data );
		if($add_wrapper) {
			$result_view = '<div id="wplc_wrapper">';
			$result_view .= $view_html;
			$result_view .= '</div>';
		}else
		{
			$result_view = $view_html;
		}


		if ( count( $children ) > 0 ) {
			libxml_use_internal_errors( true );
			$doc               = new DOMDocument();
			$doc->formatOutput = true;

			$doc->loadHTML( $result_view );
			foreach ( $children as $child ) {
				$container_element = $doc->getElementById( $child->id );
				$html              = $child->controller->view(true,false);
				$node              = $this->createElementFromHTML( $doc,$html );
				$container_element->appendChild( $node );
			}
			$result_view = $doc->saveHTML();
		}

		if ( $return_html ) {
			return $result_view;
		} else {
			echo $result_view;
			return true;
		}

	}

	//TODO::go to utils
	protected function createElementFromHTML( $doc, $str ) {
		$d = new DOMDocument();
		$d->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8')  );

		return $doc->importNode( $d->documentElement, true );
	}

	protected function convert_view_data( $data ) {
		if ( ! is_array( $data ) ) {
			$data = is_object( $data )
				? get_object_vars( $data )
				: array();
		}

		return $data;
	}

	protected function parse_action() {
		$requested_action = TCXUtilsHelper::wplc_check_http_data( 'wplc_action' );
		$requested_action = $requested_action == null ? 'view' : $requested_action;

		foreach ( $this->available_actions as $action ) {
			if ( $requested_action == $action->name ) {
				$this->selected_action = $action;
				break;
			}
		}

		if ( $this->selected_action->required_nonce_key !== null ) {
			$nonce = TCXUtilsHelper::wplc_check_http_data( 'nonce' );

			if ( ! wp_verify_nonce( ( $nonce == null ? "" : $nonce ), $this->selected_action->required_nonce_key ) ) {
				wp_die( __( "You do not have permission to perform this action", 'wp-live-chat-support' ) );
			}
		}

		if ( $this->selected_action->name != "view" && $this->selected_action->callback != null ) {
			$actionName = $this->selected_action->hook === null ? "wplc_hook_run_page_" . $this->page_alias : $this->selected_action->hook;
			add_action( $actionName, function () {
				call_user_func_array( array(
					$this,
					$this->selected_action->callback
				), $this->selected_action->callback_params );
			}, $this->selected_action->priority );
		}

	}

}

?>