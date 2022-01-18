<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ManageQuickResponseController extends BaseController {

	private $qrid;

	public function __construct( $alias, $qrid = - 1 ) {
		parent::__construct( __( "Manage quick responses", 'wp-live-chat-support' ), $alias );
		$this->qrid = $qrid;
		$this->init_actions();
		$this->parse_action( $this->available_actions );
	}

	//Actions
	public function view($return_html = false, $add_wrapper=true) {
		if ( $this->qrid > 0 ) {
			$this->view_data["quick_response"] = $this->load_quick_response_data();
		} else {
			$this->view_data["quick_response"]     = new TCXQuickResponse();
			$this->view_data["quick_response"]->id = - 1;
		}
		$this->view_data["save_action_url"] = $this->view_data["quick_response"]->getSaveUrl();
		return $this->load_view( plugin_dir_path( __FILE__ ) . "manage_quick_response_view.php" ,$return_html,$add_wrapper);
	}

	public function save_quick_response( $data ) {
		$error = $this->validation( $data );
		if ( $error->ErrorFound ) {
			$this->view_data["error"] = $error;

			return;
		}

		$quick_response_to_save           = new TCXQuickResponse();
		$quick_response_to_save->id       = intval( isset( $data['wplc_quick_response_id'] ) ? sanitize_text_field( $data['wplc_quick_response_id'] ) : '-1' );
		$quick_response_to_save->title    = sanitize_text_field( $data['wplc_quick_response_title'] );
		$quick_response_to_save->response = sanitize_text_field( $data['wplc_quick_response_response'] );
		$quick_response_to_save->sort     = intval( sanitize_text_field( $data['wplc_quick_response_sort'] ) );
		$quick_response_to_save->status   = intval( sanitize_text_field( $data['wplc_quick_response_status'] ) );

		if ( $quick_response_to_save->id < 0 ) {
			TCXQuickResponsesData::add_quick_response( $this->db, $quick_response_to_save );
		} else {
			TCXQuickResponsesData::update_quick_response( $this->db, $this->qrid, $quick_response_to_save );
		}

		if ( $this->db->last_error ) {
			$error                     = new TCXError();
			$error->ErrorFound         = true;
			$error->ErrorHandleType    = "Show";
			$error->ErrorData->message = __( "Error: Could not save quick response", 'wp-live-chat-support' );
			$this->view_data["error"]  = $error;
		}
	}

	//private functions
	private function init_actions() {
		$saveParams   = [];
		$saveParams[] = isset( $_POST ) && ! empty( $_POST ) ? $_POST : null;

		$this->available_actions[] = new TCXPageAction( "save_quick_response", 9, "save_quick_response", 'save_quick_response', $saveParams );
		$viewAction                = array_filter(
			$this->available_actions,
			function ( $action ) {
				return $action->name == 'view';
			}
		);

		if ( count( $viewAction ) == 1 ) {
			reset( $viewAction )->required_nonce_key = 'edit_quick_response';
		}
	}

	private function validation( $data ) {
		$result = new TCXError();
		if ( $data == null ) {
			$result->ErrorFound      = true;
			$result->ErrorHandleType = "Redirect";
			$result->ErrorData->url  = admin_url( "admin.php?page=wplivechat-manage-quick-response&nonce=" . wp_create_nonce( 'edit_quick_response' ) . "&qrid=" . $this->qrid );
		} else if ( strlen( $data["wplc_quick_response_title"] ) == 0 ) {
			$result->ErrorFound         = true;
			$result->ErrorHandleType    = "Show";
			$result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Title', 'wp-live-chat-support'));
		} else if ( ! is_numeric( $data["wplc_quick_response_sort"] ) ) {
			$result->ErrorFound         = true;
			$result->ErrorHandleType    = "Show";
			$result->ErrorData->message = sprintf(__( "Field '%s' must be numeric and can't be empty.", 'wp-live-chat-support' ), __( 'Sort', 'wp-live-chat-support'));
		} else if ( strlen( $data["wplc_quick_response_response"] ) == 0 ) {
			$result->ErrorFound         = true;
			$result->ErrorHandleType    = "Show";
			$result->ErrorData->message = sprintf(__( "Field '%s' can't be empty.", 'wp-live-chat-support' ), __( 'Response', 'wp-live-chat-support'));
		}

		return $result;
	}

	private function load_quick_response_data() {
		global $wplc_quick_responses_table;
		$quick_response = new TCXQuickResponse();
		$db_result      = TCXQuickResponsesData::get_quick_response( $this->db, $this->qrid );

		if ( $db_result ) {
			$db_quick_response        = reset( $db_result );
			$quick_response->id       = $db_result->id;
			$quick_response->title    = esc_html(stripslashes($db_result->title));
			$quick_response->response = esc_html(stripslashes($db_result->response));
			$quick_response->sort     = $db_result->sort;
			$quick_response->status     = $db_result->status;
		} else {
			//TODO:create box on view for this error
			die( __( "Quick response not Found", 'wp-live-chat-support' ) );
		}

		return $quick_response;
	}


	//db access


}

?>