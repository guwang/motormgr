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

echo '<h1>本月任务</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


$per = date('Ym',time());
$per_y = substr($per,0,4);
$per_m = substr($per,4,2);

echo "<hr>";
echo "月份:$per<br />";


echo "<form name='form1' method='post'>";
echo "<table>";
echo "<tr><th>序号</th><th>维护周期</th><th>检修周期</th><th>装置名称</th><th>设备位号</th><th>设备名称</th><th>维护单位</th><th>维护后运行总数</th><th>检修后运行总数</th>";
$per_days = fun_get_per_days($per);
for($intT=1;$intT<=$per_days;$intT++){
  if($intT<10){
    echo "<th>$per_y-$per_m-0$intT</th>";
  }else{
    echo "<th>$per_y-$per_m-$intT</th>";
  }
}
echo "</tr>";
echo "</table>";
echo "<input type='submit' name='button1' id='sbutton' value='Submit' />";
echo "</form>";

echo "<hr />";


if(isset($_REQUEST['button1'])){
  //  echo '当月天数:'.fun_get_per_days($per);
  $per_y = substr($per,0,4);
  $per_m = substr($per,4,2);
  $per_days = fun_get_per_days($per);
  $f_head = "序号,维护周期,检修周期,装置名称,设备位号,设备名称,维护单位,维护后运行总数,检修后运行总数";
  for($intT=1;$intT<=$per_days;$intT++){
    if($intT<10){
      $f_head .= ",$per_y-$per_m-0$intT";
    }else{
      $f_head .= ",$per_y-$per_m-$intT";
    }
  }
  $f_head = $f_head.",运行剩余时间,检修剩余时间\r\n";
  $sql = 'select distinct "maintainer" from "motor-list" order by "maintainer"';
  $sth = $dbh->prepare($sql);
  $sth->execute();

  while($row = $sth->fetch()){
    $maintainer = $row[0];
    $sth_m = $dbh->prepare('select "permaintain","percheck","site","siteid","equipname","maintainer" from "motor-list" where "maintainer"=:sm order by "siteid"');
    $sth_m->bindValue(':sm', $maintainer, PDO::PARAM_STR);
    $sth_m->execute();
    $intId = 1;
    $f = $f_head;
    while($row_m = $sth_m->fetch()){
      $f .= "$intId,$row_m[0],$row_m[1],$row_m[2],$row_m[3],$row_m[4],$row_m[5],,\r\n";
      $intId++;
    }
    $f_name = "$per-$maintainer.csv";
    $fp=fopen("tmp/$f_name","w+");    //fopen()的其它开关请参看相关函数
    //    echo $f_head.$f;
    fputs($fp,$f);
    fclose($fp);
    echo "<a href='tmp/$f_name'>$f_name</a><br />";
  }
  
}





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




