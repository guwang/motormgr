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

echo '<h1>系统参数设置</h1>';
//echo '电机运行信息';

include_once("menu.php");


if($_SESSION['user']['standing'] != 1){
  die("没有权限查看系统参数!");
}



$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


if(isset($_POST['btn_up'])){
  //  print_r($_POST);
  $now = date('Y-m-d H:i:s',time());
  foreach($_POST as $field=>$value){  
    //    echo '<br>'.$field.":".$value;  
    if($field != "btn_up"){
      $sth = $dbh->prepare('update "sysconf" set "sysvalue"=\''.$value.'\',"uid"=:i1,"uptime"=:s3 where "sysname"=:s2 and "sysvalue"<>\''.$value.'\'');
      //      $sth->bindValue(':s1', $value, PDO::PARAM_STR);
      $sth->bindValue(':s2', $field, PDO::PARAM_STR);
      $sth->bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
      $sth->bindValue(':s3', $now, PDO::PARAM_STR);
      //      print_r($sth);
      $sth->execute();
    }
  }

}



$sql = 'select t1."sysname",t1."sysvalue",t2."uname",t1."uptime" "regtime" from "sysconf" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" order by t1."sysname"';
$sth = $dbh->prepare($sql);
//$sth->bindValue(':sn', '141P204B', PDO::PARAM_STR);
$sth->execute();
echo "<br /><br />";
echo '<table>';
echo '<tr><th>参数名</th><th>设置值</th><th>用户</th><th>更新时间</th></tr>';
$intId = 1;
echo "<form name='form1' method='post'>";
while($row = $sth->fetch())
{
  echo "<tr><td>$row[0]</td><td>";
  echo "<input type='text' name='".$row[0]."' id='".$row[0]."' maxlength='2' value='".$row[1]."' style={width:90px;text-align:center;} />";
  echo "</td><td>$row[2]</td><td>$row[3]</td>";
  echo "</tr>";
  $intId++;
}
echo '</table>';
echo "<input type='submit' name='btn_up' id='btn_up' value='Submit' />";
echo '</form>';



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




