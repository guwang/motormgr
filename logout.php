<?php
session_start();
date_default_timezone_set('Etc/GMT-8');
require_once 'conf/sys_para.php';
$db_type = $conf['server'][0]['type'];
$db_host = $conf['server'][0]['host'];
$db_port = $conf['server'][0]['port'];
$db_name = $conf['server'][0]['defaultdb'];
$db_user = $conf['server'][0]['user'];
$db_pass = $conf['server'][0]['pass'];

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql ='insert into "syslog"("buid","bdate","btime","bclient","baction","bsucc") values('.$_SESSION['user']['uid'].',\''.$_SESSION['user']['logindate'].'\',\''.$_SESSION['user']['logintime'].'\',\'motormgr\',\'logout\',1)';
$dbh -> exec($sql);


$_SESSION = array();
session_destroy;
header("location:index.php");

?>