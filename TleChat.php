<?php
/* 
Plugin Name: TleChat
Plugin URI: https://github.com/muzishanshi/TleChatForWordpress
Description:  站长聊天室插件为Wordpress站长提供聊天室功能，让站长之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的站长联盟。
Version: 1.0.1
Author: 二呆
Author URI: http://www.tongleer.com
License: 
*/
if(isset($_GET['t'])){
    if($_GET['t'] == 'config'){
        update_option('tle_chat', array('qqUrl' => $_REQUEST['qqUrl'], 'wechatUrl' => $_REQUEST['wechatUrl'], 'aliUrl' => $_REQUEST['aliUrl'], 'token' => $_REQUEST['token']));
    }
}

add_action('admin_menu', 'tle_chat_menu');
function tle_chat_menu(){
    add_options_page('聊天室', '聊天室', 'manage_options', 'tle-chat', 'tle_chat_options');
}
function tle_chat_options(){
    $chat_configs = get_settings('tle_chat');
	?>
	<div class="wrap">
		<h2>站长聊天室:</h2>
		作者：<a href="http://www.tongleer.com" target="_blank" title="">二呆</a><br />
		<form method="get" action="">
			<p>
				QQ支付二维码url<br /><input type="text" name="qqUrl" value="<?=$chat_configs["qqUrl"]==""?"https://i.qianbao.qq.com/wallet/sqrcode.htm?m=tenpay&f=wallet&u=2293338477&a=1&n=Mr.%E8%B4%B0%E5%91%86&ac=26A9D4109C10A5D5C08964FCFD5634EAC852E009B700ECDA2A064092BCF6C016":$chat_configs["qqUrl"];?>" placeholder="QQ支付二维码url" size="50" />
			</p>
			<p>
				微信支付二维码url<br /><input type="text" name="wechatUrl" value="<?=$chat_configs["wechatUrl"]==""?"wxp://f2f0XXfQeK36aDieMEjmveUENW16IZMdDk_c":$chat_configs["wechatUrl"];?>" placeholder="微信支付二维码url" size="50" />
			</p>
			<p>
				支付宝支付二维码url<br /><input type="text" name="aliUrl" value="<?=$chat_configs["aliUrl"]==""?"HTTPS://QR.ALIPAY.COM/FKX03546YRHSVIW3YUK925":$chat_configs["aliUrl"];?>" placeholder="支付宝支付二维码url" size="50" />
			</p>
			<p>
				token<br /><input type="text" name="token" value="<?=$chat_configs["token"];?>" placeholder="token" size="50" />
			</p>
			<p>
				<input type="hidden" name="t" value="config" />
				<input type="hidden" name="page" value="tle-chat" />
				<input type="submit" value="保存" />
			</p>
		</form>
		<?php
		$json=file_get_contents('http://api.tongleer.com/interface/TleChat.php?action=updateWordpress&version=1&domain='.$_SERVER['SERVER_NAME'].'&token='.$chat_configs["token"]);
		$result=json_decode($json,true);
		?>
		版本检查：<?=$result["content"];?>
		<iframe src="<?=urldecode($result["url"]);?>" width="100%" height="700" scrolling = "no"></iframe>
		<small style="color:#aaaaaa">站长聊天室插件为Typecho站长提供聊天室功能，让站长之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的站长联盟。</small>
	</div>
	<?php
}
?>