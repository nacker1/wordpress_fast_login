<?php
/*
 *	Plugin Name: 第三方登录插件
 *	Plugin URI: http://open.51094.com
 *	Description: 使用前请务必先设置，无需要申请即可使用QQ、微博、人人等帐号实现快速登录。
 *	Version: 1.0.0
 *	Author: huangZY
 *	Email:	hzy@51094.com
 *	Author URI: http://www.51094.com/?p=640
 *	License: GPL
**/
	include 'login.class.php';
	wp_login_url();
	add_action('init','getCallback',1);
	add_action( 'login_form',  'login_page' );
	add_action('admin_menu', 'login_admin_menu');
	
	function fast_login_settings_link($links, $file) {
		if ($file == plugin_basename(__FILE__)){
			$settings_link = '<a href="options-general.php?page=' . dirname(plugin_basename(__FILE__)) . '/login.class.php">' . __("Settings") . '</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}

	add_filter('plugin_action_links', 'fast_login_settings_link', 10, 2 );

	function qqconnect_register_extra_fields($user_id, $password = "", $meta = array()) {
		$userdata = array();
		$userdata['ID'] = $user_id;
		$userdata['user_pass'] = $_POST['user_pass'];
		wp_new_user_notification($user_id, $_POST['user_pass'], 1);
		wp_update_user($userdata);

		delete_user_setting('default_password_nag', $user_id);
		delete_user_meta($user_id, 'default_password_nag');

		$openid = $_POST['openid'];
		$access_token = $_POST['access_token'];
		if ($openid && $access_token) {
			add_user_meta($user_id, 'qqconnect_openid', $openid, true);
			add_user_meta($user_id, 'qqconnect_access_token', $access_token, true);
			unset($_SESSION["openid"]);
			unset($_SESSION["access_token"]);
			unset($_SESSION['state']);
		} 
		wp_set_auth_cookie($user_id, true, false);
		wp_set_current_user($user_id);
		wp_redirect(home_url('/'));
		exit();
	} 
?>
