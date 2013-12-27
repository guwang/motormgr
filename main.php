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

echo '<h1>电机基本信息</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


if($_SESSION['user']['standing'] == 1){
  $sql = 'select * from "motor-list"';
  $sth = $dbh->prepare($sql);
}else{
  $sql = 'select * from "motor-list" where "maintainer"=:st';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':st', $_SESSION['user']['uname'], PDO::PARAM_STR);
}

$sth->execute();
echo '<table width=150%>';
echo '<tr><th>序号</th><th>安装位址</th><th>设备位号</th><th>设备名称</th><th>规格型号</th><th>额定功率</th><th>额定电流(A)</th><th>绝缘等级</th><th>防护等级</th><th>防爆等级</th><th>转速(r/min)</th><th>重量(kg)</th><th>安装方式</th><th>前轴承型号</th><th>后轴承型号</th><th>出厂编号</th><th>生产厂家</th><th>装置</th><th>是否加脂</th><th>维护周期</th><th>检修周期</th><th>维护人员</th></tr>';
$intId = 1;
while($row = $sth->fetch())
{
  echo "<tr><td align=center>$intId</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td align=right>$row[5]</td><td align=right>$row[6]</td><td align=center>$row[7]</td><td align=center>$row[8]</td><td>$row[9]</td><td align=right>$row[10]</td><td align=right>$row[11]</td><td align=center>$row[12]</td><td>$row[13]</td><td>$row[14]</td><td>$row[15]</td><td>$row[16]</td><td>$row[17]</td>";
  if($row[18] == 1){
    echo "<td align=center>是</td>";
  }else{
    echo "<td align=center>否</td>";
  }
  echo "<td align=right>$row[19]</td><td align=right>$row[20]</td><td>$row[21]</td></tr>";
  $intId++;
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




