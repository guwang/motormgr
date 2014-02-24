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

echo '<h1>本周任务A</h1>';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sth = $dbh->prepare('select "authtype" from "sysdataauth" where "uid"='.$_SESSION['user']['uid'].' and "authsort"=\'view_days\'');
$sth->execute();
$row = $sth->fetch();
$sys_view_days = $row[0];
$sth = $dbh->prepare('select "authtype" from "sysdataauth" where "uid"='.$_SESSION['user']['uid'].' and "authsort"=\'edit_days\'');
$sth->execute();
$row = $sth->fetch();
$sys_edit_days = $row[0];

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
//$date_diff = $week - 1;
//$date_begin = date('Y-m-d',strtotime($date) - $date_diff*24*60*60);
$date_begin = date('Y-m-d',strtotime($date) - $sys_view_days*24*60*60);
//echo "date_begin:".$date_begin;


//  echo "<pre>";
//  print_r($_POST);
//  echo "</pre>";


if(isset($_POST['btn_week'])){
  $now = date('Y-m-d H:i:s',time());
  foreach($_POST as $key=>$update_value){  
    //    echo '<br>'.$key.":".$update_value;  
    //    $date_update = substr($key,0,5);
    //    $date_update = strpos($key,'_');
    if(substr($key,0,2) == "st"){
      $update_value = strtoupper($update_value);
      $date_int1 = substr($key,2,1000);
      $date_int = substr($date_int1,0,strpos($date_int1,'_'));
      $update_date = date('Y-m-d',$date_int);
      $update_siteid = substr($key,strpos($key,'_')+1,1000);
      $update_temp1 = $_POST['tq'.$date_int.'_'.$update_siteid];
      $update_temp2 = $_POST['th'.$date_int.'_'.$update_siteid];
      $update_shake1 = $_POST['sq'.$date_int.'_'.$update_siteid];
      $update_shake2 = $_POST['sh'.$date_int.'_'.$update_siteid];
      //      echo '<br>'.$update_date.' '.$update_siteid.' '.$update_value.' '.$update_temp1.' '.$update_temp2.' '.$update_shake1.' '.$update_shake2;
      $sth = $dbh->prepare('update "motor-status" set "status"=:i1, "uid"=:i2, "uptime"=:s1, "temp1"=:i3, "temp2"=:i4, "shake1"=:d1, "shake2"=:d2 where "bdate"=:s2 and "siteid"=:s3');
      $sth->bindValue(':i1', $update_value, PDO::PARAM_INT);
      $sth->bindValue(':i2', $_SESSION['user']['uid'], PDO::PARAM_INT);
      $sth->bindValue(':i3', $update_temp1, PDO::PARAM_INT);
      $sth->bindValue(':i4', $update_temp2, PDO::PARAM_INT);
      $sth->bindValue(':d1', $update_shake1, PDO::PARAM_STR);
      $sth->bindValue(':d2', $update_shake2, PDO::PARAM_STR);
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
echo "<tr><th rowspan=2>序号</th><th rowspan=2 style='width:30px;'>维护周期</th><th rowspan=2 style='width:30px;'>检修周期</th><th rowspan=2>设备位号</th><th rowspan=2>设备名称</th><th rowspan=2>额定电流</th><th rowspan=2>维护单位</th><th rowspan=2 style='width:55px;'>维护后运行总数</th><th rowspan=2 style='width:50px;'>检修后运行总数</th>";
$per_days = fun_get_per_days($per);
for($intT=1;$intT<=$sys_view_days;$intT++){
  $date_print = date('Y-m-d',strtotime($date_begin) + $intT*24*60*60);
  $diff=(strtotime($date)-strtotime($date_print))/86400;
  //  echo "日期差:$diff | date:$date | date_print:$date_print <br />";
  $date_print_id = strtotime($date_print);
  if(($diff < $sys_edit_days) & ($diff >= 0)){
    if($date_print == $date){
      echo '<th colspan=6 style="background-color:#BF75E3;" ondblclick="fun_setvalue(\''.$date_print_id.'\')">'.$date_print.'</th>';
    }else{
      echo '<th colspan=6 ondblclick="fun_setvalue(\''.$date_print_id.'\')">'.$date_print.'</th>';
    }
  }else{
    echo "<th colspan=6>".$date_print."</th>";
  }
}
echo "</tr>";
echo "<tr>";
for($intT=0;$intT<$sys_view_days;$intT++){
  echo "<th style='width:30px;'>是否运行</th><th style='width:30px;'>前轴温度</th><th style='width:30px;'>后轴温度</th><th style='width:30px;'>上下振动</th><th style='width:30px;'>水平振动</th><th style='width:30px;'>电流</th>";
}
echo "</tr>";

$sql = 'select t1."permaintain",t1."percheck",t1."site",t1."siteid",t1."equipname",t1."maintainer",t2.date_w,t3.date_j,t1."power",t1."voltage" from "motor-list" t1';
$sql .= ' left join (select "siteid",max("bdate") as date_w from "public"."motor-status" where "status"=\'W\' group by "siteid") t2 on t1."siteid"=t2."siteid" left join (select "siteid",max("bdate") as date_j from "public"."motor-status" where "status"=\'J\' group by "siteid") t3 on t1."siteid"=t3."siteid"';
if($_SESSION['user']['standing'] == 1){
  $sth = $dbh->prepare($sql);
}else{
  $sql .= ' where t1."maintainer"=:st';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':st', $_SESSION['user']['uname'], PDO::PARAM_STR);
}
$sth->execute();

//echo "<br />$sql";

$intId = 1;
while($row = $sth->fetch()){
  $days_w_set = $row[0];
  $days_j_set = $row[1];
  $siteid = $row[3];
  $date_w = $row[6];
  $date_j = $row[7];
  $power = $row['power'];
  $voltage = $row['voltage'];
  if($date_w) $days_w = (strtotime($date) - strtotime($date_w)) / 86400; else $days_w = "";
  if($date_j) $days_j = (strtotime($date) - strtotime($date_j)) / 86400; else $days_j = "";
  echo "<tr><td align=center>$intId</td>";
  echo "<td align=right>$row[0]</td>";
  echo "<td align=right>$row[1]</td>";
  echo "<td>$row[3]</td><td>$row[4]</td><td align='right'>$row[9]</td><td>$row[5]</td>";
  
  //  echo "<br />$siteid: $days_w_set - $days_w";
  if(($days_w_set > 0) && ($days_w == 0) && ($date_w != $date)){
    echo "<td align=center style='background-color:$alert_color1;'>$days_w</td>";
  }else if(($days_w_set > 0) && ($days_w_set - $days_w < 0)){
    echo "<td align=center style='background-color:$alert_color1;'>$days_w</td>";
  }else if(($days_w_set > 0) && ($days_w_set - $days_w < 5)){
    echo "<td align=center style='background-color:$alert_color2;'>$days_w</td>";
  }else if(($days_w_set > 0) && ($days_w_set - $days_w < 10)){
    echo "<td align=center style='background-color:$alert_color3;'>$days_w</td>";
  }else{
    echo "<td align=center>$days_w</td>";
  }

  if(($days_j_set > 0) && ($days_j == 0) && ($date_j != $date)){
    echo "<td align=center style='background-color:$alert_color1;'>$days_j</td>";
  }else if(($days_j_set > 0) && ($days_j_set - $days_j < 0)){
    echo "<td align=center style='background-color:$alert_color1;'>$days_j</td>";
  }else if(($days_j_set > 0) && ($days_j_set - $days_j < 15)){
    echo "<td align=center style='background-color:$alert_color2;'>$days_j</td>";
  }else if(($days_j_set > 0) && ($days_j_set - $days_j < 30)){
    echo "<td align=center style='background-color:$alert_color3;'>$days_j</td>";
  }else{
    echo "<td align=center>$days_j</td>";
  }
  for($intT=1;$intT<=$sys_view_days;$intT++){
    $date_submit = strtotime($date_begin) + $intT*24*60*60;
    $date_print = date('Y-m-d',$date_submit);
    $diff=(strtotime($date)-strtotime($date_print))/86400;
    //    echo $diff."<br />";
    $sth_getvalue = $dbh->prepare('select "status","temp1","temp2","shake1","shake2","voltage" from "motor-status" where "siteid"=:s1 and "bdate"=:d1');
    $sth_getvalue -> bindValue(':s1', $siteid, PDO::PARAM_STR);
    $sth_getvalue -> bindValue(':d1', $date_print, PDO::PARAM_STR);
    $sth_getvalue -> execute();
    if($sth_getvalue -> rowCount() == 0){
      $sth_getpre = $dbh->prepare('select "status" from "motor-status" where "siteid"=:s and "bdate" in(SELECT max("bdate") FROM "motor-status" where "siteid"=:s)');
      $sth_getpre -> bindValue(':s', $siteid, PDO::PARAM_STR);
      $sth_getpre -> execute();
      $row_getpre = $sth_getpre -> fetch();
      $status_pre = $row_getpre[0];
      if($status_pre != "0"){
	$status_pre = "1"; 
	$status_getvalue = "1";
      }else{
	$status_pre = "0";
	$status_getvalue = "0";
      }
      $sth_adddefault = $dbh->prepare('insert into "motor-status"("bdate","siteid","uid","uptime","status") values(:d1,:s1,:i1,:t1,:s2)');
      $sth_adddefault -> bindValue(':d1', $date_print, PDO::PARAM_STR);
      $sth_adddefault -> bindValue(':s1', $siteid, PDO::PARAM_STR);
      $sth_adddefault -> bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
      $sth_adddefault -> bindValue(':t1', $now, PDO::PARAM_STR);
      $sth_adddefault -> bindValue(':s2', $status_pre, PDO::PARAM_STR);
      $sth_adddefault -> execute();
      $temp1_getvalue = 0;
      $temp2_getvalue = 0;
      $shake1_getvalue = 0;
      $shake2_getvalue = 0;
      $voltage_getvalue = 0;
    }else{
      $row_getvalue = $sth_getvalue->fetch();
      $status_getvalue = $row_getvalue[0];
      $temp1_getvalue = $row_getvalue[1];
      $temp2_getvalue = $row_getvalue[2];
      $shake1_getvalue = $row_getvalue[3];
      $shake2_getvalue = $row_getvalue[4];
      $voltage_getvalue = $row_getvalue[5];
    }

    /*
    echo "<br>siteid:".$siteid;
    echo "<br>sys_edit_days:".$sys_edit_days;
    echo "<br>diff:".$diff;
    echo "<br>temp_getvalue:".$temp_getvalue;
    echo "<br>shake_getvalue:".$shake_getvalue;
    echo "<hr>";
    */

    if(($diff < $sys_edit_days) & ($diff >= 0)){
      echo "<td><input type='text' name='st".$date_submit."_".$row[3]."' id='".$date_submit."' maxlength='1' value='".$status_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>";  //运行状态 运行为1 为运行为2 维护为w
      if($power > 160){
	if($temp1_getvalue > 75 || ($temp1_getvalue == 0 && $status_getvalue != 0)){
	  echo "<td><input type='text' name='tq".$date_submit."_".$row[3]."' id='tq".$date_submit."' maxlength='3' value='".$temp1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //前轴温度
	}else{
	  echo "<td><input type='text' name='tq".$date_submit."_".$row[3]."' id='tq".$date_submit."' maxlength='3' value='".$temp1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //前轴温度
	}
	if($temp2_getvalue > 75 || ($temp2_getvalue == 0 && $status_getvalue != 0)){
	  echo "<td><input type='text' name='th".$date_submit."_".$row[3]."' id='th".$date_submit."' maxlength='3' value='".$temp2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //后轴温度
	}else{
	  echo "<td><input type='text' name='th".$date_submit."_".$row[3]."' id='th".$date_submit."' maxlength='3' value='".$temp2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //后轴温度
	}
	if($shake1_getvalue > 2.8 || ($shake1_getvalue == 0 && $status_getvalue != 0)){
	  echo "<td><input type='text' name='sq".$date_submit."_".$row[3]."' id='sq".$date_submit."' maxlength='3' value='".$shake1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //前轴振动
	}else{
	  echo "<td><input type='text' name='sq".$date_submit."_".$row[3]."' id='sq".$date_submit."' maxlength='3' value='".$shake1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //前轴振动
	}
	if($shake2_getvalue > 2.8 || ($shake2_getvalue == 0 && $status_getvalue != 0)){
	  echo "<td><input type='text' name='sh".$date_submit."_".$row[3]."' id='sh".$date_submit."' maxlength='3' value='".$shake2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //后轴振动
	}else{
	  echo "<td><input type='text' name='sh".$date_submit."_".$row[3]."' id='sh".$date_submit."' maxlength='3' value='".$shake2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //后轴振动
	}
	if(($voltage_getvalue / $voltage) >= 0.9 || ($voltage_getvalue == 0 && $status_getvalue != 0)){
	  echo "<td><input type='text' name='vq".$date_submit."_".$row[3]."' id='vq".$date_submit."' maxlength='5' value='".$voltage_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //后轴振动
	}else{
	  echo "<td><input type='text' name='vq".$date_submit."_".$row[3]."' id='vq".$date_submit."' maxlength='5' value='".$voltage_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //后轴振动
	}
      }else{
	if($temp1_getvalue > 75){
	  echo "<td><input type='text' name='tq".$date_submit."_".$row[3]."' id='tq".$date_submit."' maxlength='3' value='".$temp1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //前轴温度
	}else{
	  echo "<td><input type='text' name='tq".$date_submit."_".$row[3]."' id='tq".$date_submit."' maxlength='3' value='".$temp1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //前轴温度
	}
	if($temp2_getvalue > 75){
	  echo "<td><input type='text' name='th".$date_submit."_".$row[3]."' id='th".$date_submit."' maxlength='3' value='".$temp2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //后轴温度
	}else{
	  echo "<td><input type='text' name='th".$date_submit."_".$row[3]."' id='th".$date_submit."' maxlength='3' value='".$temp2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //后轴温度
	}
	if($shake1_getvalue > 2.8){
	  echo "<td><input type='text' name='sq".$date_submit."_".$row[3]."' id='sq".$date_submit."' maxlength='3' value='".$shake1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //前轴振动
	}else{
	  echo "<td><input type='text' name='sq".$date_submit."_".$row[3]."' id='sq".$date_submit."' maxlength='3' value='".$shake1_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //前轴振动
	}
	if($shake2_getvalue > 2.8){
	  echo "<td><input type='text' name='sh".$date_submit."_".$row[3]."' id='sh".$date_submit."' maxlength='3' value='".$shake2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;color:#FF2608;' /></td>"; //后轴振动
	}else{
	  echo "<td><input type='text' name='sh".$date_submit."_".$row[3]."' id='sh".$date_submit."' maxlength='3' value='".$shake2_getvalue."' style='width:30px;text-align:center;text-transform:uppercase;' /></td>"; //后轴振动
	}
      }
    }else{
      echo "<td style='text-align:center;'>$status_getvalue</td>";
      if(($temp1_getvalue == 0 && $power > 160 && $status_getvalue != "0") || $temp1_getvalue > 75){
	echo "<td style='text-align:center;color:#FF2608;'>$temp1_getvalue</td>";
      }else{
	echo "<td style='text-align:center;'>$temp1_getvalue</td>";
      }
      if(($temp2_getvalue == 0 && $power > 160 && $status_getvalue != "0") || $temp2_getvalue > 75){
	echo "<td style='text-align:center;color:#FF2608;'>$temp2_getvalue</td>";
      }else{
	echo "<td style='text-align:center;'>$temp2_getvalue</td>";
      }
      if(($shake1_getvalue == 0 && $power > 160 && $status_getvalue != "0") || $shake1_getvalue > 2.8){
	echo "<td style='text-align:center;color:#FF2608;'>$shake1_getvalue</td>";
      }else{
	echo "<td style='text-align:center;'>$shake1_getvalue</td>";
      }
      if(($shake2_getvalue == 0 && $power > 160 && $status_getvalue != "0") || $shake2_getvalue > 2.8){
	echo "<td style='text-align:center;color:#FF2608;'>$shake2_getvalue</td>";
      }else{
	echo "<td style='text-align:center;'>$shake2_getvalue</td>";
      }
      if(($voltage_getvalue == 0 && $power > 30 && $status_getvalue != "0") || $voltage_getvalue > 5){
	echo "<td style='text-align:center;color:#FF2608;'>$voltage_getvalue</td>";
      }else{
	echo "<td style='text-align:center;'>$voltage_getvalue</td>";
      }
    }
  }
  echo "</tr>\n";
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




