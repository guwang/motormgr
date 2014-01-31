<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <META http-equiv="content-type" content="text/html; charset=utf-8" />
  <META NAME="Generator" CONTENT="Emacs">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="css/style.css" />
  <script src="class/jquery/jquery-1.10.2.min.js"></script>
  </HEAD>
  <TITLE> 电机台账管理 </TITLE>
 <BODY>
<?php
session_start();
date_default_timezone_set('Etc/GMT-8');
require_once 'conf/sys_para.php';

if($_SESSION['sys']['login_motor'] == '1'){
  echo "<p align='right'><a href=logout.php>注销&nbsp;&nbsp;".$_SESSION['user']['uname']."</a></p>";
}
else{
  echo "<p>请登录!</p><br>";
  echo "<a href=index.php>返回登录页面</a>";
  exit;
}

      

$db_type = $conf['server'][0]['type'];
$db_host = $conf['server'][0]['host'];
$db_port = $conf['server'][0]['port'];
$db_name = $conf['server'][0]['defaultdb'];
$db_user = $conf['server'][0]['user'];
$db_pass = $conf['server'][0]['pass'];

echo '<h1>工作日志</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


if($_SESSION['user']['standing'] == 1){
  $sql = 'select "id","did","flow","bdate","workslot","explan","priority","closed","uid","uptime" from "motor-daily"';
  $sth = $dbh->prepare($sql);
}else{
  $sql = 'select "id","did","flow","bdate","workslot","explan","priority","closed","uid","uptime" from "motor-daily" where "uid"=:i1';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
}

$sth->execute();
echo '<table>';
echo '<tr><th>ID</th><th>Flow ID</th><th>日期</th><th>班次</th><th>记录</th><th>优先级</th><th>是否关闭</th><th>录入</th><th>时间</th></tr>';
while($row = $sth->fetch())
{
  echo "<tr><td align=center>$row[1]</td><td>$row[2]</td><td>$row[3]</td>";
  if($row[4] == 1){
    echo "<td align=center>白</td>";
  }else{
    echo "<td align=center>黑</td>";
  }
  echo "<td align=right>$row[5]</td><td align=right>$row[6]</td><td align=center>$row[7]</td><td align=center>$row[8]</td><td>$row[9]</td>";
  echo "</tr>";
}
echo '</table>';



/*
echo "<b>第一种方法:</b><br />";
$sql = 'select "site" from "motor-list" limit 1 offset 0';
$rst = $dbh -> query($sql);
//$row = $rst -> fetch();
foreach($rst as $row){
  $temp1 = $row[0];
  echo "$temp1<br />";
}

echo '<br />';
echo "<b>第二种方法:</b><br />";
$sth = $dbh->prepare('select * from "motor-list" where "siteid" = :sn');
$sth->bindValue(':sn', '141P204B', PDO::PARAM_STR);
$sth->execute();
while($row = $sth->fetch())
{
     echo $row[3];
}
*/




?>
  
 </BODY>
</HTML>




