<?php
class AgilityFeatCtcConfig {

	private $options;

	public function __construct() {
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'page_init'));
	}

	public function add_plugin_page() {
		add_options_page(
			'Settings Admin',
			"AgilityFeat's Click To Call",
			'manage_options',
			'af_ctc_settings_admin',
			array($this, 'create_admin_page')
		);
	}

	public function get_options() {
		return $this->options;
	}

	public function create_admin_page() {
		$this->options = get_option('af_ctc_provider');
		?>
			<div class="wrap">
			<h1>Change your calls Settings</h1>
			<form method="post" action="options.php">
				<?php
						// This prints out all hidden setting fields
						settings_fields( 'call_settings' );
						do_settings_sections( 'af_ctc_settings_admin' );
						submit_button();
				?>
			</form>
			</div>
		<?php
	}

	/* Register and add settings */
	public function page_init() {
		register_setting(
			'call_settings',
			'af_ctc_provider',
			array($this, 'sanitize')
		);

		add_settings_section(
			'af_ctc_provider_config',
			'Provider for Click to Call',
			array($this, 'print_section_info'),
			'af_ctc_settings_admin'
		);

		add_settings_field(
			'use_tokbox',
			'Use Tokbox',
			array($this, 'use_tokbox_callback'),
			'af_ctc_settings_admin',
			'af_ctc_provider_config'
		);

		add_settings_field(
			'api_key',
			'Tokbox API Key',
			array($this, 'api_key_callback'),
			'af_ctc_settings_admin',
			'af_ctc_provider_config'
		);

		add_settings_field(
			'secret_key',
			'Tokbox Secret Key',
			array($this, 'secret_key_callback'),
			'af_ctc_settings_admin',
			'af_ctc_provider_config'
		);

		add_settings_field(
			'extension_id',
			"Google Chrome's Desktop sharing extension ID",
			array($this, 'extension_id_callback'),
			'af_ctc_settings_admin',
			'af_ctc_provider_config'
		);
	}

	public function sanitize($input) {
		$new_input = array();
		$new_input['use_tokbox'] = isset($input['use_tokbox']);

		if(isset($input['api_key']))
			$new_input['api_key'] = sanitize_text_field($input['api_key']);
		if(isset($input['secret_key']))
			$new_input['secret_key'] = sanitize_text_field($input['secret_key']);
		if(isset($input['extension_id']))
			$new_input['extension_id'] = sanitize_text_field($input['extension_id']);

		return $new_input;
	}

	public function print_section_info() {
		echo "This setting lets you choose between using Tokbox for your calls".
			" or using a Peer to Peer connection (Experimental).";
	}

	public function use_tokbox_callback() {
		printf(
			'<input type="checkbox" id="use_tokbox" name="af_ctc_provider[use_tokbox]" %s/>',
			isset( $this->options['use_tokbox'] ) && $this->options['use_tokbox'] ? 'checked' : ''
		);
	}

	public function api_key_callback() {
		printf(
			'<input type="text" id="api_key" name="af_ctc_provider[api_key]" value="%s"/>',
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key']) : ''
		);
	}

	public function secret_key_callback() {
		printf(
			'<input type="password" id="secret_key" name="af_ctc_provider[secret_key]" value="%s" />',
			isset( $this->options['secret_key'] ) ? esc_attr( $this->options['secret_key']) : ''
		);
	}

	public function extension_id_callback() {
		printf(
			'<input type="text" id="extension_id" name="af_ctc_provider[extension_id]" value="%s" />',
			isset( $this->options['extension_id'] ) ? esc_attr( $this->options['extension_id']) : ''
		);
	}
}
?>
