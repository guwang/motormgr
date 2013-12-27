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
<script language='JavaScript'>
function fun_setvalue(sdate){
   //   var sdate1 = String(sdate);
   //   alert(sdate);
   var arr_x=document.getElementsByName(sdate);
   //   alert(x.length);
   for(var i = 0; i < arr_x.length; i++){  
     arr_x[i].value = "1";
   }
}

</script>
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

echo '<h1>本周任务</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$now = date('Y-m-d H:i:s',time());
$per = date('Ym',time());
$per_y = substr($per,0,4);
$per_m = substr($per,4,2);

$week = date('w');
$weekid = date('W');
$date = date('Y-m-d',time());
echo "<hr>";
echo "今天是:$date<br />";
echo "本年第".$weekid."周<br />";
//echo "今天周".$week."<br />";
$date_diff = $week - 1;
$date_begin = date('Y-m-d',strtotime($date) - $date_diff*24*60*60);
//echo "date_begin:".$date_begin;



if(isset($_POST['btn_week'])){
  $now = date('Y-m-d H:i:s',time());
  //print_r($_POST);
  foreach($_POST as $key=>$update_value){  
    //        echo '<br>'.$key.":".$update_value;  
    //    $date_update = substr($key,0,5);
    //    $date_update = strpos($key,'_');
    if($update_value != "Submit"){
      $update_date = date('Y-m-d',substr($key,0,strpos($key,'_')));
      $update_siteid = substr($key,strpos($key,'_')+1,100);
      //      echo '<br>'.$update_date.' '.$update_siteid.' '.$update_value;
      $sth = $dbh->prepare('update "motor-status" set "status"=:i1, "uid"=:i2, "uptime"=:s1 where "bdate"=:s2 and "siteid"=:s3');
      $sth->bindValue(':i1', $update_value, PDO::PARAM_INT);
      $sth->bindValue(':i2', $_SESSION['user']['uid'], PDO::PARAM_INT);
      $sth->bindValue(':s1', $now, PDO::PARAM_STR);
      $sth->bindValue(':s2', $update_date, PDO::PARAM_STR);
      $sth->bindValue(':s3', $update_siteid, PDO::PARAM_STR);
      $sth->execute();
    }
  }  
}


echo "<hr>";
echo "<form name='form1' method='post'>";
echo "<table>";
echo "<tr><th rowspan='2'>序号</th><th rowspan='2'>维护周期</th><th rowspan='2'>检修周期</th><th rowspan='2'>装置名称</th><th rowspan='2'>设备位号</th><th rowspan='2'>设备名称</th><th rowspan='2'>维护单位</th><th rowspan='2'>维护后运行总数</th><th rowspan='2'>检修后运行总数</th>";
$per_days = fun_get_per_days($per);
for($intT=0;$intT<=6;$intT++){
  $date_print = date('Y-m-d',strtotime($date_begin) + $intT*24*60*60);
  $diff=(strtotime($date)-strtotime($date_print))/86400;
  $date_print_id = strtotime($date_print);
  if(($diff < 3) & ($diff >= 0)){
    if($date_print == $date){
      echo '<th style={background:#BF75E3;} ondblclick="fun_setvalue(\''.$date_print_id.'\')">'.$date_print.'</th>';
    }else{
      echo '<th ondblclick="fun_setvalue(\''.$date_print_id.'\')">'.$date_print.'</th>';
    }
  }else{
    echo "<th>".$date_print."</th>";
  }
}
echo "</tr>";
echo "<tr><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th><th>日</th></tr>";

$sql = 'select "permaintain","percheck","site","siteid","equipname","maintainer" from "motor-list"';
if($_SESSION['user']['standing'] == 1){
  $sth = $dbh->prepare($sql);
}else{
  $sql .= ' where "maintainer"=:st';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':st', $_SESSION['user']['uname'], PDO::PARAM_STR);
}
$sth->execute();

$intId = 1;
while($row = $sth->fetch()){
  $siteid = $row[3];
  echo "<tr><td align=center>$intId</td><td align=right>$row[0]</td><td align=right>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td></td><td></td>";
  for($intT=0;$intT<=6;$intT++){
    $date_submit = strtotime($date_begin) + $intT*24*60*60;
    $date_print = date('Y-m-d',$date_submit);
    $diff=(strtotime($date)-strtotime($date_print))/86400;
    //    echo $diff."<br />";
    $sth_getvalue = $dbh->prepare('select "status" from "motor-status" where "siteid"=:s1 and "bdate"=:d1');
    $sth_getvalue -> bindValue(':s1', $siteid, PDO::PARAM_STR);
    $sth_getvalue -> bindValue(':d1', $date_print, PDO::PARAM_STR);
    $sth_getvalue -> execute();
    if($sth_getvalue -> rowCount() == 0){
      $sth_adddefault = $dbh->prepare('insert into "motor-status"("bdate","siteid","uid","uptime") values(:d1,:s1,:i1,:t1)');
      $sth_adddefault -> bindValue(':d1', $date_print, PDO::PARAM_STR);
      $sth_adddefault -> bindValue(':s1', $siteid, PDO::PARAM_STR);
      $sth_adddefault -> bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
      $sth_adddefault -> bindValue(':t1', $now, PDO::PARAM_STR);
      $sth_adddefault -> execute();
      $status_getvalue = 0;
    }else{
      $row_getvalue = $sth_getvalue->fetch();
      $status_getvalue = $row_getvalue[0];
    }

    if(($diff < 3) & ($diff >= 0)){
      if($status_getvalue == 1){
	echo "<td><input type='text' name='".$date_submit."_".$row[3]."' id='".$date_submit."' maxlength='1' value='".$status_getvalue."' style={width:90px;text-align:center;} /></td>";
      }else{
	echo "<td><input type='text' name='".$date_submit."_".$row[3]."' id='".$date_submit."' maxlength='1' value='".$status_getvalue."' style={width:90px;text-align:center;color:#FF2608;} /></td>";
      }
    }else{
      if($status_getvalue == 1){
	echo "<td style={width:90px;text-align:center;}>$status_getvalue</td>";
      }else{
	echo "<td style={width:90px;text-align:center;color:#FF2608;}>$status_getvalue</td>";
      }
    }
  }
  echo "</tr>";
  $intId++;
}


echo "</table>";
echo "<input type='submit' name='btn_week' id='btn_week' value='Submit' />";
echo "</form>";

echo "<hr />";








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




