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
ob_start();
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

echo '<h1>用户管理</h1>';
//echo '电机运行信息';

include_once("menu.php");


if($_SESSION['user']['standing'] != 1){
  die("没有权限查看用户信息!");
}



$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
//$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
//$dbh -> query("SET NAMES UTF8");
$dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


if(isset($_GET['act']) && $_GET['act'] == "resetpass"){
  $uid = $_GET['uid'];
  $uname = $_GET['uname'];
  $pass = sha1($uname);
  $sth = $dbh->prepare('update "sysuser" set "pass"=:s1 where "uid"=:i1');
  $sth->bindValue(':s1', $pass, PDO::PARAM_STR);
  $sth->bindValue(':i1', $uid, PDO::PARAM_INT);
  $sth->execute();
  echo ("用户$uname的密码已被重置为$uname,他需要在下次登录时立即更改密码.");
}


$sql = 'select "uid","uname","cname","branch","dep","tel","mb","email","regtime" from "sysuser" where "del"=0 order by "uid"';
$sth = $dbh->prepare($sql);
//$sth->bindValue(':sn', '141P204B', PDO::PARAM_STR);
$sth->execute();
echo "<br /><br />";
echo '<table>';
echo '<tr><th>UID</th><th>用户名</th><th>姓名</th><th>站点</th><th>部门</th><th>电话</th><th>手机</th><th>邮箱</th><th>注册时间</th><th>Action</th></tr>';
$intId = 1;
while($row = $sth->fetch())
{
  echo "<tr><td>$row[0]</td><td><a href='?viewid=$row[0]#bottom'>$row[1]</a></td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td align=right>$row[5]</td><td align=right>$row[6]</td><td align=center>$row[7]</td><td align=center>$row[8]</td>";
  echo "<td><a href='usermgr.php?act=resetpass&uid=$row[0]&uname=$row[1]'>重置密码</a></td>";
  echo "</tr>";
  $intId++;
}
echo '</table>';



if(isset($_GET['viewid'])){
  $viewid = $_GET['viewid'];
  echo "<hr />";
  $sql = 'insert into "sysdataauth"("uid","authsort","authtype","upuid","uptime") select \''.$viewid.'\',"authsort","authtype",'.$_SESSION['user']['uid'].',\''.date('Y-m-d H:i:s',time()).'\' from "sysdataauthlist" where "authsort" not in(select "authsort" from "sysdataauth" where "uid"='.$viewid.')';
  $dbh -> exec($sql);
  $sql = 'select t1."uid",t1."authsort",t1."authtype",t1."upuid",t1."uptime",t2."uname",t2."cname",t2."ename",t3."authdes" from "sysdataauth" t1 inner join "sysuser" t2 on t1."uid"=t2."uid" inner join "sysdataauthlist" t3 on t1."authsort"=t3."authsort" where t1."uid"='.$viewid.' order by t1."authsort"';
  //  echo "<br />$sql";
  echo "<form name='frm_upuser' method=post>";
  echo "<table>";
  echo "<tr><th>UID</th><th>Name</th><th>Auth Sort</th><th>Auth Des</th><th>Set Value</th><th>Update UID</th><th>Last Update</th></tr>";
  foreach($dbh -> query($sql) as $row){
    echo "<tr><td>$row[uid]</td><td>$row[uname]</td><td>$row[authsort]</td><td>$row[authdes]</td><td><input name='$row[authsort]' type=text style='width:200px;' class=text value='$row[authtype]'></td><td>$row[upuid]</td><td>$row[uptime]</td></tr>";

  }
  echo "</table>";
  if($_SESSION['user']['standing'] == 1){
    echo "<input type='submit' class=btn name='update_user' value='Update' style='height:20px;'>";
  }
  echo "</form>";
}


if(isset($_POST['update_user']) && $_SESSION['user']['standing'] == 1){
  $sql = 'select * from "sysdataauth" where "uid"='.$viewid.' order by "authsort"';
  //  $rst_sort = $db -> query($sql);
  //  $rows_sort = $rst_sort -> rowCount();
  foreach($dbh -> query($sql) as $row_sort){
    //    echo "<br />".$_REQUEST[$row_sort['authsort']]."    ".$row_sort['autht'];
    if($row_sort['authtype'] != $_REQUEST[$row_sort['authsort']]){
      $sql = 'update "sysdataauth" set "authtype"=\''.$_REQUEST[$row_sort['authsort']].'\',"upuid"='.$_SESSION['user']['uid'].',"uptime"=\''.date('Y-m-d H:i:s',time()).'\' where "uid"='.$viewid.' and "authsort"=\''.$row_sort['authsort'].'\'';
      //      echo "<br />".$sql;
      $dbh -> exec($sql);
      //      mysql_query("insert into `$dbsys`.`blog`(`buid`,`bdate`,`btime`,`bip`,`bclient`,`baction`,`bsucc`) values(".$_SESSION['uid'].",'".date('Y-m-d',time())."','".date('Y-m-d H:i:s',time())."','".$_SESSION['clientip']."','budget','bud2004',1)");
    }
  }

  Header("Location: usermgr.php?viewid=$viewid#bottom");
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




