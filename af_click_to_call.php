<?php 
/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       AgilityFeat's Click to Call
 * Plugin URI:        http://wordpress.com/plugins/agilityfeats-click-to-call
 * Description:       A plugin that uses Tokbox to achieve one click calls to other users
 * Version:           0.5.0
 * Author:            WebRTC.ventures
 * Author URI:        https://webrtc.ventures/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       af_click_to_call
 */

include_once 'settings.php';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_af_click_to_call() {
	require_once plugin_dir_path( __FILE__ ) . '/includes/class_af_ctc_call_activator.php';
	AfCtcActivator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_af_click_to_call() {
	require_once plugin_dir_path( __FILE__ ) . '/includes/class_af_ctc_call_deactivator.php';
	AfCtcDeactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_af_click_to_call' );
register_deactivation_hook( __FILE__, 'deactivate_af_click_to_call' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'class_af_click_to_call.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_af_click_to_call() {
	$plugin = AgilityFeatCtc::get_instance();
}
run_af_click_to_call();
?>
