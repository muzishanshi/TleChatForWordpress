<?php
header("Content-type: text/html; charset=utf-8");
define('PATH', dirname(dirname(__FILE__)).'/');
require_once(PATH . '../../../wp-config.php');  
global $wpdb;
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");

$chat_configs = get_settings('tle_chat');

if(empty($chat_configs["appId"])||empty($chat_configs["MasterKey"])){echo('有未填写参数');exit;}
$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="createRoom"){
	$uid = isset($_POST['uid']) ? addslashes($_POST['uid']) : '';
	
	$rowUser = $wpdb->get_row( "SELECT * FROM `" . $wpdb->prefix . "users` where ID='".$uid."'");
	//$rowOption = $wpdb->get_row( "SELECT option_value FROM `" . $wpdb->prefix . "options` where option_name='blogname'");
	//创建聊天室
	$nickname=$rowUser->display_name;
	$result=createRoom(get_option('blogname'),array($nickname), $chat_configs["appId"], $chat_configs["MasterKey"]);

	file_put_contents(dirname(__FILE__).'/../../../plugins/TleChat/config/config_room.php','<?php die; ?>'.serialize(array(
		'objectId'=>$result["objectId"],
		'createdAt'=>$result["createdAt"]
	)));

	echo('创建成功');exit;
}
?>