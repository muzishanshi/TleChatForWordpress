<?php
header("Content-type: text/html; charset=utf-8");
define('PATH', dirname(dirname(__FILE__)).'/');
require_once(PATH . '../../../wp-config.php');  
global $wpdb;
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");

$chat_configs = get_settings('tle_chat');

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="delRoom"){
	$config_room=@unserialize(ltrim(file_get_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php'),'<?php die; ?>'));
	if(!isset($chat_configs["appId"])||!isset($chat_configs["appKey"])){echo('有未填写参数');exit;}
	if($config_room["objectId"]==""){echo('聊天室为空，不必删除。');exit;}
	//删除聊天室
	$result=delRoom($config_room["objectId"], $chat_configs["appId"], $chat_configs["appKey"]);

	file_put_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php','<?php die; ?>'.serialize(array(
		'objectId'=>"",
		'createdAt'=>""
	)));
	echo('删除完成');exit;
}
?>