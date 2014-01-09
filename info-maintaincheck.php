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

$date = date('Y-m-d',time());

echo '<h1>维护及检修信息</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql = 'select t1."id",t1."site",t1."siteid",t1."equipname",t1."permaintain",t1."percheck",t1."maintainer",t2.date_w,t3.date_j from "motor-list" t1';
$sql1 = ' left join (select "siteid",max("bdate") as date_w from "public"."motor-status" where "status"=\'W\' group by "siteid") t2 on t1."siteid"=t2."siteid" left join (select "siteid",max("bdate") as date_j from "public"."motor-status" where "status"=\'J\' group by "siteid") t3 on t1."siteid"=t3."siteid"';
if($_SESSION['user']['standing'] == 1){
  $sth = $dbh->prepare($sql.$sql1);
}else{
  $sql = $sql.$sql1.' where t1."maintainer"=:st';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':st', $_SESSION['user']['uname'], PDO::PARAM_STR);
}

$sth->execute();
echo '<table>';
echo '<tr><th>序号</th><th>安装位址</th><th>设备位号</th><th>设备名称</th><th>维护周期</th><th>检修周期</th><th>维护人员</th><th>上次维护时间</th><th>维护后运行总数</th><th>下次维护时间</th><th>距离下次维护</th><th>上次检修时间</th><th>检修后运行总数</th><th>下次检修时间</th><th>距离下次检修</th></tr>';
$intId = 1;
while($row = $sth->fetch()){
  $days_w_set = $row[4];
  $days_j_set = $row[5];
  $siteid = $row[1];
  $date_w = $row[7];
  $date_j = $row[8];

  //  echo "<br />days_w_set:$days_w_set";

  if($date_w){
    $days_w = (strtotime($date) - strtotime($date_w)) / 86400; 
    if($days_w_set != 0){
      $date_w_next = date('Y-m-d',strtotime($date_w) + ($days_w_set * 86400));
      $days_w_next = (strtotime($date_w_next) - strtotime($date)) / 86400;
    }else{
      $date_w_next = "";
      $days_w_next = "";
    }
  }else{
    $days_w = "";
    $date_w_next = "";
  }
  if($date_j){
    $days_j = (strtotime($date) - strtotime($date_j)) / 86400; 
    if($days_j_set != 0){
      $date_j_next = date('Y-m-d',strtotime($date_j) + ($days_j_set * 86400));
      $days_j_next = (strtotime($date_j_next) - strtotime($date)) / 86400;
    }else{
      $date_j_next = "";
      $days_j_next = "";
    }
  }else{
    $days_j = "";
    $days_j_next = "";
  }

  echo "<tr><td align=center>$intId</td><td>$siteid</td><td>$row[2]</td><td>$row[3]</td>";
  echo "<td align=right>$row[4]</td><td align=right>$row[5]</td><td>$row[6]</td>";
  echo "<td>$date_w</td><td align=center>$days_w</td><td>$date_w_next</td>";
  if(($days_w_set > 0) & ($days_w == "")){
    echo "<td align=center style={background-color:$alert_color1;}>$days_w_next</td>";
  }else if(($days_w_set > 0) & ($days_w_set - $days_w < 0)){
    echo "<td align=center style={background-color:$alert_color1;}>$days_w_next</td>";
  }else if(($days_w_set > 0) & ($days_w_set - $days_w < 5)){
    echo "<td align=center style={background-color:$alert_color2;}>$days_w_next</td>";
  }else if(($days_w_set > 0) & ($days_w_set - $days_w < 10)){
    echo "<td align=center style={background-color:$alert_color3;}>$days_w_next</td>";
  }else{
    echo "<td align=center>$days_w_next</td>";
  }
  echo "<td>$date_j</td><td align=center>$days_j</td>";
  echo "<td>$date_j_next</td>";
  if(($days_j_set > 0) & ($days_j == "")){
    echo "<td align=center style={background-color:$alert_color1;}>$days_j_next</td></tr>";
  }else if(($days_j_set > 0) & ($days_j_set - $days_j < 0)){
    echo "<td align=center style={background-color:$alert_color1;}>$days_j_next</td></tr>";
  }else if(($days_j_set > 0) & ($days_j_set - $days_j < 15)){
    echo "<td align=center style={background-color:$alert_color2;}>$days_j_next</td></tr>";
  }else if(($days_j_set > 0) & ($days_j_set - $days_j < 30)){
    echo "<td align=center style={background-color:$alert_color3;}>$days_j_next</td></tr>";
  }else{
    echo "<td align=center>$days_j_next</td></tr>";
  }

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




