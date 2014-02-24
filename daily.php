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
   function fun_trim(str){
     if(str == ""){
       return "";
     }else{
       var reg=/^\s*(.*?)\s*$/;
       return str.replace(reg,"$1");
     }
   }


   function fun_check_worklog(){
   //     var require_priori = document.getElementById('txt_priori').value;
     var radios = document.getElementsByName('txt_priori');
     //     var radios = document.getElementsByName(radioName);
     var priori_checked = false;
     for (var i = 0; i < radios.length; i++){
       if (radios[i].checked) 
	 priori_checked = true;
     }
     if(priori_checked == false){
       alert("请选择优先级!");
       return false;
     }
     //     alert(priori_checked);

     var require_worklog = fun_trim(document.getElementById('txt_worklog').value);
     if(require_worklog == ""){
       alert("请输入日志内容!");
       return false;
     }


     return true; 
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

//$time = date('Y-m-d H:i:s',time());
$time_work = date('H',time());

//echo "时间:$time_work";

echo '<h1>工作日志</h1>';
//echo '电机运行信息';

include_once("menu.php");

$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


$sth = $dbh->prepare('select "authtype" from "sysdataauth" where "uid"='.$_SESSION['user']['uid'].' and "authsort"=\'view_log_days\'');
$sth->execute();
$row = $sth->fetch();
$sys_view_log_days = $row[0];

if(isset($_GET['flowid'])) $get_flowid = $_GET['flowid']; else $get_flowid = "";

$date_now = date('Y-m-d',time());
$date_begin = date('Y-m-d',strtotime($date_now) - $sys_view_log_days*24*60*60);


if(isset($_POST['submit'])){
  $insert_date = date('Y-m-d',time());
  $insert_time = date('Y-m-d H:i:s',time());
  $post_flowid = $_POST['txt_flowid'];
  $post_workslot = $_POST['txt_workslot'];
  $post_priori = $_POST['txt_priori'];
  if(isset($_POST['txt_close'])) $post_close = $_POST['txt_close']; else $post_close = "";
  $post_worklog = $_POST['txt_worklog'];
  if($post_flowid == "") $insert_flowid = 0; else $insert_flowid = $post_flowid;
  if($post_workslot == "day") $insert_workslot = 1; else $insert_workslot = 2;
  $insert_priori = substr($post_priori,1,1);
  if($post_close == "") $insert_close = 0; else $insert_close = 1;
  $insert_worklog = trim($post_worklog);

  $sql = 'select max("did")+1 as newid from "motor-daily"';
  $rst = $dbh -> query($sql);
  $row = $rst -> fetch();
  $did_new = $row[0];
  if(!$did_new) $did_new = 1;

  $sql = 'insert into "motor-daily"("did","flow","bdate","workslot","explan","priority","closed","uid","uptime") values(:i1,:i2,:s1,:i3,:s2,:i4,:i5,:i6,:s3)';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':i1', $did_new, PDO::PARAM_INT);
  $sth->bindValue(':i2', $insert_flowid, PDO::PARAM_INT);
  $sth->bindValue(':s1', $insert_date, PDO::PARAM_STR);
  $sth->bindValue(':i3', $insert_workslot, PDO::PARAM_INT);
  $sth->bindValue(':s2', $insert_worklog, PDO::PARAM_STR);
  $sth->bindValue(':i4', $insert_priori, PDO::PARAM_INT);
  $sth->bindValue(':i5', $insert_close, PDO::PARAM_INT);
  $sth->bindValue(':i6', $_SESSION['user']['uid'], PDO::PARAM_INT);
  $sth->bindValue(':s3', $insert_time, PDO::PARAM_INT);
  $sth->execute();
}



if($_SESSION['user']['standing'] == 1){
  $sql = 'select t1."id",t1."did",t1."flow",t1."bdate",t1."workslot",t1."explan",t1."priority",t1."closed",t2."uname",t1."uptime" from "motor-daily" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" where ("bdate">\''.$date_begin.'\' or ("priority">0 and "closed"=0)) order by "bdate","did"';
  $sth = $dbh->prepare($sql);
}else{
  $sql = 'select t1."id",t1."did",t1."flow",t1."bdate",t1."workslot",t1."explan",t1."priority",t1."closed",t2."uname",t1."uptime" from "motor-daily" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" where ("bdate">\''.$date_begin.'\' or ("priority">0 and "closed"=0)) and t1."uid"=:i1 order by "bdate","did"';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
}
$sth->execute();
echo '<table>';
echo '<tr><th>ID</th><th style="width:80;">日期</th><th>Flow ID</th><th>班次</th><th style="width:700;">记录</th><th>优先级</th><th>关闭</th><th>录入</th><th>时间</th><th>Action</th></tr>';
while($row = $sth->fetch())
{
  $priori = $row['priority'];
  if($priori == 1){
    echo "<tr style='background-color:$alert_color2;'>";
  }else if($priori == 2){
    echo "<tr style='background-color:$alert_color1;'>";
  }
  echo "<td align=center>$row[1]</td><td>$row[3]</td><td align=center>$row[2]</td>";
  if($row[4] == 1){
    echo "<td align=center>白</td>";
  }else{
    echo "<td align=center>夜</td>";
  }
  echo "<td style='width:700;'>$row[5]</td><td align=center>$row[6]</td><td align=center>$row[7]</td><td align=center>$row[8]</td><td>$row[9]</td>";
  echo "<td align=center><a href='?flowid=".$row[1]."'>Flow</a></td>";
  echo "</tr>";
}
echo '</table>';

echo "<br />";
if($_SESSION['user']['standing'] == 1){
  $sql = 'select t1."id",t1."did",t1."flow",t1."bdate",t1."workslot",t1."explan",t1."priority",t1."closed",t2."uname",t1."uptime" from "motor-daily" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" where ("bdate">\''.$date_begin.'\' or ("priority">0 and "closed"=0)) and "flow"=0 order by "bdate","did"';
  $sth = $dbh->prepare($sql);
}else{
  $sql = 'select t1."id",t1."did",t1."flow",t1."bdate",t1."workslot",t1."explan",t1."priority",t1."closed",t2."uname",t1."uptime" from "motor-daily" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" where ("bdate">\''.$date_begin.'\' or ("priority">0 and "closed"=0)) and t1."uid"=:i1 and "flow"=0 order by "bdate","did"';
  $sth = $dbh->prepare($sql);
  $sth->bindValue(':i1', $_SESSION['user']['uid'], PDO::PARAM_INT);
}
$sth->execute();
echo "<div class='div-daily'>";
echo "<div class='div-id'>ID</div>";
echo "<div class='div-date'>日期</div>";
echo "<div class='div-flowid'>Flow ID</div>";
echo "<div class='div-workslot'>班次</div>";
echo "<div class='div-explan'>记录</div>";
echo "<div class='div-priority'>优先级</div>";
echo "<div class='div-close'>关闭</div>";
echo "<div class='div-user'>录入</div>";
echo "<div class='div-time'>时间</div>";
echo "<div class='div-action'>Action</div>";
echo "</div>";

while($row = $sth->fetch())
{
  $priori = $row['priority'];
  if($priori == 1){
    echo "<div class='div-daily' style='background-color:$alert_color2;'>";
  }else if($priori == 2){
    echo "<div class='div-daily' style='background-color:$alert_color1;'>";
  }
  echo "<div class='div-id'>$row[1]</div>";
  echo "<div class='div-date'>$row[3]</div>";
  echo "<div class='div-flowid'>$row[2]</div>";
  if($row[4] == 1){
    echo "<div class='div-workslot'>白</div>";
  }else{
    echo "<div class='div-workslot'>夜</div>";
  }
  echo "<div class='div-explan'>$row[5]</div>";
  echo "<div class='div-priority'>$row[6]</div>";
  echo "<div class='div-close'>$row[7]</div>";
  echo "<div class='div-user'>$row[8]</div>";
  echo "<div class='div-time'>$row[9]</div>";
  echo "<div class='div-action'><a href='?flowid=".$row[1]."'>Flow</a></div>";
  echo "</div>";
}



echo "<div class='div-daily'>";
echo "<div class='div-id'>ID</div>";
echo "<div class='div-date'>日期</div>";
echo "<div class='div-flowid'>Flow ID</div>";
echo "<div class='div-workslot'>班次</div>";
echo "<div class='div-explan'>记录</div>";
echo "<div class='div-priority'>优先级</div>";
echo "<div class='div-close'>关闭</div>";
echo "<div class='div-user'>录入</div>";
echo "<div class='div-time'>时间</div>";
echo "<div class='div-action'>Action</div>";
echo "</div>";



function test($n){
  echo $n." ";
  if($n>0){
    test($n-1);
  }else{
    echo "<-->";
  }
  echo $n." ";
}

test(10);


echo "<hr />";
echo "<div style='width:700px;height:200px'>";
echo "<h3>录入日志:</h3><br />";
echo '<form name="frm_worklog" id="frm_worklog" enctype="multipart/form-data" method="post" onsubmit="return fun_check_worklog();">';
echo "Flow ID:<b>$get_flowid</b>";
echo "<input name='txt_flowid' type=hidden value='$get_flowid'>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "班次:";
if($time_work >= 8 && $time_work < 17){
  echo "<label><input type='radio' name='txt_workslot' value='day' style='border:none;' checked>白班</label>";
  echo "<label><input type='radio' name='txt_workslot' value='night' style='border:none;'>夜班</label>";
}else{
  echo "<label><input type='radio' name='txt_workslot' value='day' style='border:none;'>白班</label>";
  echo "<label><input type='radio' name='txt_workslot' value='night' style='border:none;' checked>夜班</label>";
}
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "优先级:";
echo "<label><input type='radio' id='txt_priori' name='txt_priori' value='p0' style='border:none;'>记录</label>";
echo "<label><input type='radio' id='txt_priori' name='txt_priori' value='p1' style='border:none;'>提示</label>";
echo "<label><input type='radio' id='txt_priori' name='txt_priori' value='p2' style='border:none;'>警告</label>";

echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
if($get_flowid) echo "<label>关闭<input name='txt_close' type='checkbox' value='yes'></label>";

echo "<textarea id='txt_worklog' name='txt_worklog' type=textarea style='width:700px;height:100px;'></textarea><br />";
echo '<input type=submit class=btn name=submit value="确&nbsp;&nbsp;&nbsp;定" style="width:80px;">';
echo '</form>';

echo "</div>";


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




