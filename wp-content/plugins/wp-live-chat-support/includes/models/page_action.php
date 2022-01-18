<?php

class TCXPageAction {
	/**
	 * Constructor
	 *
	 * @param string $name Action Name
	 * @param int $action_priority Execution Priority
	 * @param string $action_required_nonce_key Key to generate the nonce if needed for the action
	 * @param string $action_callback action function which will be executed by call_user_func_array
	 * @param array $action_callback_params callback function params as array
	 *
	 * @param string $action_hook
	 *
	 * @author 3cx
	 */
	public function __construct( $name, $action_priority = 10, $action_required_nonce_key = null, $action_callback = null, $action_callback_params = [], $action_hook = null ) {
		$this->name               = $name;
		$this->priority           = $action_priority;
		$this->callback           = $action_callback;
		$this->callback_params    = $action_callback_params;
		$this->required_nonce_key = $action_required_nonce_key;
		$this->hook               = $action_hook;

	}

	public $required_nonce_key;
	public $name;
	public $callback;
	public $callback_params;
	public $priority;
	public $hook;

}

?>