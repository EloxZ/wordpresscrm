<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SessionsController extends BaseController {

	private $pager;

	public function __construct( $alias ) {
		parent::__construct( __( "Chat History", 'wp-live-chat-support' ), $alias );
		$this->init_actions();
		$this->parse_action( $this->available_actions );

	}

	public function load_sessions() {

		$db_results = TCXChatData::get_history( $this->db, $this->pager->rows_per_page, $this->pager->offset );

		$results = TCXChatHelper::get_client_chat_list( $db_results );

		return $results;
	}

	public function load_statuses() {
		$statuses = array(
			__("All",'wp-live-chat-support')             => - 1,
			__("Missed",'wp-live-chat-support')          => ChatStatus::MISSED,
			__("Ended by client",'wp-live-chat-support') => ChatStatus::ENDED_BY_CLIENT,
			__("Agent inactive",'wp-live-chat-support')  => ChatStatus::ENDED_DUE_AGENT_INACTIVITY,
			__("Client inactive",'wp-live-chat-support') => ChatStatus::ENDED_DUE_CLIENT_INACTIVITY,
			__("Ended by agent",'wp-live-chat-support')  => ChatStatus::ENDED_BY_AGENT,
			__("Blocked by agent",'wp-live-chat-support')  => ChatStatus::ENDED_DUE_BLOCK
		);

		return $statuses;
	}

	public function delete_session( $cid ) {
		if ( $cid > 0 ) {
			$chat_files = TCXChatData::get_chat_files($this->db,$cid);

			TCXChatData::remove_chat( $this->db, $cid );
			if ( $this->db->last_error ) {
				$this->view_data["delete_success"] = false;
			} else {
				foreach ($chat_files as $file)
				{
					$file_location = TCXUtilsHelper::attachment_url_to_path( $file->fileurl );

					if($file_location!==false && !is_dir($file_location)){
						wp_delete_file($file_location);
					}
				}
				$this->view_data["delete_success"] = true;
			}
		}

	}

	public function search_sessions( $email, $status ) {
		//$this->view_data["chats"] = $this->sea_sessions();
		$this->pager = TCXUtilsHelper::wplc_get_pager( TCXChatData::generate_search_history_query($this->db, $email, $status,true ) );

		$db_chats                 = TCXChatData::search_history( $this->db, $email, $status, $this->pager->rows_per_page, $this->pager->offset );
		$this->view_data["chats"] = TCXChatHelper::get_client_chat_list( $db_chats );

	}

	public function clean_session() {
		TCXChatData::truncate_history( $this->db );
	}

	public function view($return_html = false, $add_wrapper=true) {
		if ( ! isset( $this->pager ) ) {
			$this->pager = TCXUtilsHelper::wplc_get_pager( TCXChatData::generate_history_query($this->db) );
		}

		if ( ! array_key_exists( "chats", $this->view_data ) ) {
			$this->view_data["chats"] = $this->load_sessions();
		}

		$this->view_data["truncate_session_nonce"] = wp_create_nonce( 'truncateSessionsNonce' );
		$this->view_data["delete_session_nonce"]   = wp_create_nonce( 'deleteSessionsNonce' );
		$this->view_data["search_nonce"]           = wp_create_nonce( 'searchSessionsNonce' );
		$this->view_data["current_page"]           = $this->pager->current_page;
		$this->view_data["statuses"]               = $this->load_statuses();

		$this->view_data["page_links"] = paginate_links( array(
			'base'      => add_query_arg( 'pagenum', '%#%' ),
			'format'    => '',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'total'     => $this->pager->pages_counter,
			'current'   => $this->pager->current_page,
		) );

		return $this->load_view( plugin_dir_path( __FILE__ ) . "sessions_view.php",$return_html,$add_wrapper );
	}

	private function init_actions() {
		$this->available_actions   = [];
		$this->available_actions[] = new TCXPageAction( "prompt_remove_session" );
		$this->available_actions[] = new TCXPageAction( "prompt_truncate_session" );

		$this->available_actions[] = new TCXPageAction( "execute_truncate_session", 9, "truncateSessionsNonce", "clean_session" );

		$search_params             = [];
		$search_params[0]          = isset( $_GET['wplc_email_filter'] ) ? sanitize_email( urldecode($_GET['wplc_email_filter']) ) : "";
		$search_params[1]          = isset( $_GET['wplc_status_filter'] ) ? intval( sanitize_text_field( $_GET['wplc_status_filter'] ) ) : - 1;
		$this->available_actions[] = new TCXPageAction( "search_history", 9, "searchSessionsNonce", "search_sessions", $search_params );

		$idParam   = [];
		$idParam[] = isset( $_GET['cid'] ) ? intval( sanitize_text_field( $_GET['cid'] ) ) : - 1;

		$this->available_actions[] = new TCXPageAction( "execute_remove_session", 9, "deleteSessionsNonce", 'delete_session', $idParam );
	}

}

?>