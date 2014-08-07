<?php
	function login_page(){
		$config = get_option("wp_fast_login_user_options");
		echo "
		<script>
			var login = document.getElementById('loginform');
			var div = document.createElement('div');
			div.style['padding'] = '10px 0';
			div.innerHTML = '其它登录：<span id=\"hzy_fast_login\"></span>';
			login.appendChild(div);
			var url = 'http://open.51094.com/user/myscript/".$config['appid'].".html';
			var script = document.createElement('script');
			script.src = url;
			document.body.appendChild(script)
		</script>
		";	
	}

	function login_admin_page(){
		include 'wp-login-admin.php';
	}

	function login_admin_menu(){
		add_options_page('第三方登录插件','第三方登录插件','manage_options',__FILE__,'login_admin_page');
	}

	function get( $url ){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HEADER, false); //  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}

	function post( $url, $data ){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HEADER, false); // 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}

	function getCallback(){
		if ( isset( $_GET['param'] ) ) {
			$param = stripslashes(urldecode($_GET['param']));
			$param = json_decode($param,true);
			if( isset( $param['name'] ) && isset( $param['uniq'] ) ){ 			//插件登录回调
				$user = isSign( $param );
				header('Location:/wp-admin');
				exit;
			}
		} 
	}

	function isSign( $data ){
		$data['uniq'] = hash('adler32', $data['uniq']);
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users.' WHERE user_login = "'.$data['uniq'].'" ';
		$user = $wpdb -> query( $sql );
		if( empty( $user ) ){
			$user_id = wp_create_user( $data['uniq'], 110110, '' );
			update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
			update_user_option( $user_id, 'nickname', $data['name'], true ); //Set up the Password change nag.
			update_user_option( $user_id, 'yt_user_level', 1, true ); //Set up the Password change nag.
			update_user_option( $user_id, 'description', $data['from'], true ); //Set up the Password change nag.
			wp_new_user_notification( $user_id, 110110 );
		}
		$credentials['user_login'] = $data['uniq'];
		$credentials['user_password'] = 110110;
		$user = wp_signon($credentials);
		return $user;
	}
?>
