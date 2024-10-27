<?php

define('af_click_to_call.php', __FILE__);
include_once 'includes/class_af_ctc_tokbox_session.php';
include_once 'includes/class_af_ctc_call_interface.php';

class AgilityFeatCtc {
	private static $instance;
	protected $templates;
	protected $contacts;
	protected $configuration;
	protected static $options;

	/* behave as a singleton class */
	public static function get_instance() {
		if(null == self::$instance) {
			self::$instance = new AgilityFeatCtc();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->templates = array();
		$this->contacts = array();
		self::$options = get_option('af_ctc_provider');
		$this->configuration = new AgilityFeatCtcConfig();


		if(version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {
			add_filter(
				'page_attributes_dropdown_pages_args',
				array($this, 'register_af_ctc_pages')
			);
		} else {
			add_filter('theme_page_templates', array($this, 'add_new_template'));
		}
		add_filter(
			'wp_insert_post_data', 
			array($this, 'register_af_ctc_templates')
		);

		add_filter(
			'template_include',
			array($this, 'view_af_ctc_templates')
		);

		$this->templates = array(
			'public/af_ctc_contacts.php' => 'Contacts'
		);

		/* initialize all actions and filters */
		add_action('wp_enqueue_scripts', array($this,'attach_admin_scripts'));
		add_action('wp_enqueue_scripts', array($this,'add_style_sheets'));
		add_action('wp_ajax_create_call', array('AfCtcCallInterface', 'create_call'));
		add_action('wp_ajax_take_call', array('AfCtcCallInterface', 'take_call'));
		add_action('wp_ajax_end_call', array('AfCtcCallInterface', 'end_call'));
		add_action('admin_bar_menu', array($this, 'bar_icon'), 90);
		add_filter('heartbeat_received', array($this, 'receive_user_beat'), 10, 2);
	}

	public function bar_icon($wp_admin_bar) {
		$message = 'Make a Call';
		$menu_id = 'call_menu';
		$args = array(
			'id' => $menu_id,
			'title' => $message,
			'href' => site_url() . '/contacts/',
			'meta' => array(
				'class' => 'btn',
				'title' => $message,
				'onclick' => 'jQuery("#video_el").toggleClass("video_el")'
			)
		);
		$wp_admin_bar->add_node($args);
	}

	public function receive_user_beat($response, $data) {
		global $wpdb;
		$table_name = $wpdb->prefix."user_status";
		$user_login = wp_get_current_user()->user_login;
		if(empty($data['date'])) {
			return $response;
		}

		/* write to database changes in user status 15 second
		/* time from wordpress */
		$wpdb->replace(
			$table_name,
			array(
				'user_login' => $user_login,
				'last_seen' => $data['date']
			),
			array('%s', '%s')
		);

		$users = $wpdb->get_results("select user_login, last_seen from ${table_name}");
		$online_users = array();
		$offline_users = array();
		foreach ($users as $u) {
			if(time() - strtotime($u->last_seen) < 30) {
				array_push($online_users, $u->user_login);
				error_log($u->user_login);
			} else {
				array_push($offline_users, $u->user_login);
			}
		}
		/* format response before sending it back */
		$response['user_login'] = $user_login;
		$response['online_users'] = $online_users;
		$response['offline_users'] = $offline_users;
		$response['heartbeat_interval'] = 'fast';
		return $response;
	}

	public function attach_admin_scripts() {
		if(is_page('contacts')) {
			wp_enqueue_script(
				'ajax-script',
				plugins_url('/public/js/heartbeat_actions.js', __FILE__),
				array('jquery', 'heartbeat')
			);

			wp_enqueue_script(
				'opentok-script',
				plugins_url('/public/js/opentok.min.js', __FILE__),
				array()
			);
			
			wp_enqueue_script(
				'opentok-session-handler',
				plugins_url('/public/js/opentok_handler.js', __FILE__),
				array('jquery')
			);

			wp_localize_script(
				'opentok-session-handler',
				'php',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'extension_id' => self::$options['extension_id']
				)
			);
		}
	}

	public function add_style_sheets() {
		if(is_page('contacts')) {
			wp_enqueue_style('page_template', plugins_url('/public/css/contacts.css', __FILE__));
		}
	}

	public function add_new_template( $post_templates ) {
		$post_templates = array_merge( $post_templates, $this->templates );
		return $post_templates;
	}


	public function register_af_ctc_templates($atts) {
		$cache_key = 'page-templates-' . md5(get_theme_root().'/'.get_stylesheet());
		$templates = wp_get_theme()->get_page_templates();
		if(empty($templates)) {
			$templates = array();
		}

		wp_cache_delete($cache_key, 'themes');
		$templates = array_merge($templates, $this->templates);
		wp_cache_add($cache_key, $templates, 'themes', 1800);

		return $atts;
	}

	public function view_af_ctc_templates($template) {
		global $post;

		if(!$post) {
			return $template;
		}

		if(!isset($this->templates[get_post_meta(
			$post->ID, '_wp_page_template', true
		)])) {
			return $template;
		}

		$file = plugin_dir_path(__FILE__) . get_post_meta(
			$post->ID, '_wp_page_template', true
		);

		if(file_exists($file)) {
			return $file;
		}
		
		return  $template;
	}
}

?>
