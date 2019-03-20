<?php
header("Content-type: text/html; charset=utf-8");
define('PATH', dirname(dirname(__FILE__)).'/');
require_once(PATH . '../../../wp-config.php');  
global $wpdb;
include_once "../include/function.php";
date_default_timezone_set("Etc/GMT-8");

$action = isset($_POST['action']) ? addslashes($_POST['action']) : '';
if($action=="clearAudio"){
	delDir(dirname(__FILE__)."/upload/audio/");
	mkdir(dirname(__FILE__)."/upload/audio/");
}
?>