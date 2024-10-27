<?php

include_once 'class_af_ctc_tokbox_session.php';


class AfCtcCallInterface {

	public static $options;

	public static function create_call() {
		self::$options = get_option('af_ctc_provider');
		global $wpdb;
		$table_name = $wpdb->prefix . "af_ctc_call";

		if(self::$options['use_tokbox']) {
			$call = new AfCtcOpentokSession(self::$options['api_key'], self::$options['secret_key']);
			$data = array(
				'apiKey' => self::$options['api_key'],
				'sessionId' => $call->get_session_id(),
				'token' => $call->get_token()
			);
			$wpdb->replace(
				$table_name,
				array(
					'session_id' => $call->get_session_id(),
					'token' => $call->get_token(),
					'caller' => wp_get_current_user()->user_login,
					'callee' => $_POST['callee']
				),
				array('%s', '%s', '%s', '%s')
			);
			$wpdb->print_error();
			echo json_encode($data);
			die();
		}
		echo json_encode($data);
		die();
	}

	public static function take_call() {
		self::$options = get_option('af_ctc_provider');
		global $wpdb;
		$table_name = $wpdb->prefix . "af_ctc_call";
		$current_user = wp_get_current_user()->user_login;
		$calls = array();
		$calls = $wpdb->get_results("select session_id, token, callee from ${table_name} where callee='${current_user}'");
		if(count($calls) != 0) {
			if(self::$options['use_tokbox'] == true) {
				$data = array(
					'success' => true,
					'apiKey' => self::$options['api_key'],
					'sessionId' => $calls[0]->session_id,
					'token' => $calls[0]->token
				);
				echo json_encode($data);
				die();
			}
		}

		$data = array(
			'success' => false
		);
		echo json_encode($data);
		die();
	}

	public static function end_call() {
		global $wpdb;
		$current_user = wp_get_current_user()->user_login;
		$table_name = $wpdb->prefix . 'af_ctc_call';
		$wpdb->delete($table_name, array(
			'caller' => $current_user,
		));
		$wpdb->delete($table_name, array(
			'callee' => $current_user,
		));
		error_log('deleting');
		echo json_encode(array(
			'success' => true
		));
		die();
	}

}
?>
