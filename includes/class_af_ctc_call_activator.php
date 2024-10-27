<?php
class AfCtcActivator {
	/* create database on activate if table
	 * does not exist */
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . "user_status";
		$call_table_name = $wpdb->prefix . "af_ctc_call";
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_login varchar(130) NOT NULL UNIQUE,
			last_seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			online boolean DEFAULT FALSE NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta($sql);

		$sql = "CREATE TABLE IF NOT EXISTS $call_table_name (
			id mediumint(0) NOT NULL AUTO_INCREMENT,
			session_id varchar(512) NOT NULL UNIQUE,
			caller varchar(130) NOT NULL UNIQUE,
			callee varchar(130) NOT NULL UNIQUE,
			token  varchar(512) NOT NULL UNIQUE,
			PRIMARY KEY (id)
		) $charset_collate;";
		dbDelta($sql);
	}
}
?>
