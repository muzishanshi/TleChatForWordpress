<?php
/* 
Plugin Name: TleChat
Plugin URI: https://github.com/muzishanshi/TleChatForWordpress
Description:  站长聊天室插件为站长和用户提供聊天室功能，让站长与用户之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的联盟。
Version: 1.0.6
Author: 二呆
Author URI: http://www.tongleer.com
License: 
*/
if(isset($_GET['t'])){
    if($_GET['t'] == 'config'){
        update_option('tle_chat', array('isEnableJQuery' => $_REQUEST['isEnableJQuery'], 'appId' => $_REQUEST['appId'], 'appKey' => $_REQUEST['appKey'], 'notice' => $_REQUEST['notice']));
    }
}

add_action('admin_menu', 'tle_chat_menu');
function tle_chat_menu(){
    add_options_page('聊天室', '聊天室', 'manage_options', 'tle-chat', 'tle_chat_options');
}

add_action('wp_head', 'tle_chat_wp_head');
function tle_chat_wp_head(){
	$cssUrl = plugins_url() . '/TleChat/chat/ui/css/layui.css';
	echo '<link rel="stylesheet" href="'.$cssUrl.'"  media="all">';
}

add_action('wp_footer', 'tle_chat_wp_footer');
function tle_chat_wp_footer(){
	global $current_user;
	$chat_configs = get_settings('tle_chat');
	if(@$chat_configs['isEnableJQuery']=="y"){
		$jquerysrc='<script src=https://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js></script>';
	}else{
		$jquerysrc='';
	}
	echo '
		<div style="position:fixed;bottom:0;right:0;">
			<button id="btnChatroom" class="layui-btn layui-btn-normal">聊天室</button>
		</div>
		'.$jquerysrc.'
		<script src="https://www.tongleer.com/api/web/include/layui/layui.js"></script>
		<script>
		layui.use("layer", function(){
			var $ = layui.jquery, layer = layui.layer;
			$("#btnChatroom").click(function(){
				layer.open({
					type: 2
					,title: "聊天室"
					,id: "chatroom"
					,area: ["95%", "95%"]
					,shade: 0
					,maxmin: true
					,offset: "auto"
					,content: "'.plugins_url().'/TleChat/chat/chat.php?uid='.$current_user->ID.'"
					,btn: ["关闭"]
					,yes: function(){
					  layer.closeAll();
					}
					,zIndex: layer.zIndex
					,success: function(layero){
					  layer.setTop(layero);
					}
				});
			});
		});
		</script>
	';
}

function tle_chat_options(){
    $chat_configs = get_settings('tle_chat');
	$config_room=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../plugins/TleChat/config/config_room.php'),'<?php die; ?>'));
	global $current_user; 
	?>
	<div class="wrap">
		<h2>站长聊天室:</h2>
		作者：<a href="http://www.tongleer.com" target="_blank" title="">二呆</a><br />
		<form method="get" action="">
			<p>
				前台是否加载jquery：
				<input type="radio" name="isEnableJQuery" value="n" <?=isset($chat_configs['isEnableJQuery'])?($chat_configs['isEnableJQuery']=="n"?"checked":""):"";?> />否
				<input type="radio" name="isEnableJQuery" value="y" <?=isset($chat_configs['isEnableJQuery'])?($chat_configs['isEnableJQuery']!="n"?"checked":""):"checked";?> />是
			</p>
			<p>
				前台聊天室配置<a href="https://leancloud.cn/" target="_blank">leancloud</a>的appId<br /><input type="text" name="appId" value="<?=$chat_configs["appId"]==""?"":$chat_configs["appId"];?>" placeholder="leancloud的appId" size="50" />
			</p>
			<p>
				前台聊天室配置<a href="https://leancloud.cn/" target="_blank">leancloud</a>的appKey<br /><input type="text" name="appKey" value="<?=$chat_configs["appKey"]==""?"":$chat_configs["appKey"];?>" placeholder="leancloud的appKey" size="50" />
			</p>
			<p>
				前台显示的公告<br /><input type="text" name="notice" value="<?=$chat_configs["notice"]==""?"":$chat_configs["notice"];?>" placeholder="输入前台显示的公告" size="50" />
			</p>
			<p>
				<input type="hidden" name="t" value="config" />
				<input type="hidden" name="page" value="tle-chat" />
				<input type="submit" value="保存" />
			</p>
		</form>
		版本检查：<span id="versionCodeChat"></span>
		<p>
			<script src="https://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
			<input type="hidden" id="objectId" value="<?=$config_room["objectId"];?>" />
			<input type="button" id="clearAudio" value="清空所有录音" />
			<input type="button" id="delRoom" value="删除当前聊天室" />
			<input type="button" id="createRoom" value="创建新聊天室" />
			<script>
				$.post("<?=plugins_url();?>/TleChat/update.php",{version:5},function(data){
					var data=JSON.parse(data);
					$("#versionCodeChat").html(data.content);
					$("#chatUrl").html('<a href="https://www.tongleer.com" target = "_blank">站长聊天室</a>&nbsp;|&nbsp;<a href="'+decodeURIComponent(data.url)+'" target = "_blank">站长直播间</a>');
				});
				$("#clearAudio").click(function(){
					$.post("<?=plugins_url();?>/TleChat/chat/clearAudio.php",{action:"clearAudio"},function(data){
						alert("清空录音成功");
					});
				});
				$("#delRoom").click(function(){
					$.post("<?=plugins_url();?>/TleChat/chat/delRoom.php",{action:"delRoom"},function(data){
						alert(data);
					});
				});
				$("#createRoom").click(function(){
					var flag=false;
					if($("#objectId").val()!=""){
						if(confirm("确认当前聊天室已经销毁后可创建新聊天室，还要继续吗？")){
							flag=true;
						}
					}else{
						flag=true;
					}
					if(flag){
						$.post("<?=plugins_url();?>/TleChat/chat/createRoom.php",{action:"createRoom",uid:"<?=$current_user->ID;?>"},function(data){
							alert(data);
						});
					}
				});
			</script>
		</p>
		<div id="chatUrl"></div>
		<small style="color:#aaaaaa">站长聊天室插件为站长和用户提供聊天室功能，让站长与用户之间的联系更加友爱，支持文本、长文本、语音聊天、图片传输及站长之间的QQ、微信、支付宝打赏，共同建立一个友爱的联盟。</small>
	</div>
	<?php
}
?>