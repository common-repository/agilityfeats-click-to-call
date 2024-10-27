<?php

include_once 'opentok.phar';

use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\Role;

class AfCtcOpentokSession {

	private $session;
	private $session_id;

	/* create and store all necessary information
	 * to stablish a connection and a call with
	 * tokbox */
	public function __construct($apiKey, $secretKey) {
		$opentok = new OpenTok($apiKey, $secretKey);
		$this->session = $opentok->createSession(array('mediaMode' => MediaMode::ROUTED));
		$this->session_id = $this->session->getSessionId();
	}

	public function get_session_id() {
		return $this->session_id;
	}

	public function get_token() {
		return $this->session->generateToken();
	}
}
?>
