<?php
	include 'login.class.php';
	$lCon = get_option("wp_fast_login_options");
	if( empty( $lCon ) ){
		$url = 'http://open.51094.com/user/myscript/0.html';
		$tempConfig = get($url);
		$lCon = json_decode($tempConfig,true);
		update_option("wp_fast_login_options",$lCon);
	}

	if( isset( $_POST['Submit'] ) ){
		$sup = $_POST['loginConfig'];
		if( empty( $sup ) ){
			echo '<script>alert("请最少选择一种登录方式");</script>';
		}else{
			$config = get_option("wp_fast_login_user_options");
			$req['appid'] = $config['appid'];
			$req['support'] = $sup;
			$req['backurl'] = home_url( '/' );
			$req['title'] = home_url( '/' );
			$req['type'] = 1;
			$url = 'http://open.51094.com/user/signapp.html';
			$ret = post( $url, $req );
			$ret = json_decode($ret,true);
			if( !empty( $ret ) && 0==$ret['result'] ){
				delete_option('wp_fast_login_user_options');
				update_option("wp_fast_login_user_options",$ret['data']);
				$info = ' <span style="color:green;">配置成功，可正常使用</span> ';
			}else{
				$info = ' <span style="color:red;">请求失败，请再试一次！</span> ';
			}
		}
	}
	$config = get_option("wp_fast_login_user_options");
?>
<style>
	li.fast_login{float:left;margin:5px;list-style: none;width:40px;height:40px;border-radius: 8px;padding:1px;cursor: pointer;}
	.on{border:1px solid green;}
	.off{border:1px solid #fff;}
</style>
<div class="wrap">
	<h2>第三方登录插件</h2>
	<hr>
	<form action="" method="post" name="wp_fast_login_form">
	<table class="form-table">
		<tbody><tr valign="top">
			<th style="text-align:right;line-height: 50px;width:100px;">
				选择登录方式：
			</th>
			<td>
				<?php
					$support = explode('#',$config['sup']);
					foreach( $lCon as $v ){
						$class = ' off ';
						if( in_array($v['id'],$support) ){
							$class = ' on ';
						}
						$loginConfig[] = $v['id'];
						echo '<li class="fast_login '.$class.'" lid="'.$v['id'].'" onclick="setSelectConf(this);"><img style="width:40px;height:40px;border-radius:8px;" src="http://open.51094.com/Public/img/hezuo/hz_'.$v['id'].'.jpg"></li>';
					}
				?>
			</td>
		</tr>		
	</tbody></table>
	<p class="submit" style="padding-left: 200px;">
		<input type="hidden" name="loginConfig" id="loginConfig" value="<?php echo implode('#', $loginConfig);?>">
		<input type="submit" class="button-primary" name="Submit" value="保存更改">
		<p><?php echo $info;?></p>
	</p>
	</form>
</div>
<script>
document.getElementByClass = function (classname) {
   var elements = [];
   var alltags = document.all ? document.all : document.getElementsByTagName("*")
   for (var i=0; i<alltags.length; i++) {
      if(alltags[i].classList.contains(classname))
          elements[elements.length] = alltags[i];
   }
   return elements;
}
function setSelectConf(obj){
	if( obj.classList.contains('on') ){
		obj.classList.add('off')
		obj.classList.remove('on')	
	}else{
		obj.classList.remove('off')
		obj.classList.add('on')
	}
	var fList = document.getElementByClass('fast_login');
	var length = fList.length;
	var loginConfig = [];

	for( var i=0;i<length;i++ ){
		if( fList[i].classList.contains('on') ){
			var lid = fList[i].attributes.getNamedItem('lid');
			loginConfig.push(lid.value);
		}
	}
	var login = document.getElementById('loginConfig');
	login.value = loginConfig.join('#');
}
</script>
