<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <META http-equiv="content-type" content="text/html; charset=utf-8" />
  <META NAME="Generator" CONTENT="Emacs">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="css/style.css" /> 
  </HEAD>
  <TITLE> Motor Manager </TITLE>
 <BODY onload="document.frmlogin.user.focus()">
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

if(isset($_POST['submit'])){
    $name = $_POST['user'];
    $pass = $_POST['pass'];
}else{
    $name = "";
    $pass = "";
}


echo "<center><h2>电机运行管理系统</h2></center>";
echo '<form name=frmlogin id=frmlogin enctype="multipart/form-data" method="post">';
echo '<table>';
echo '<tr><td class=grid align=right>User ID:</td>
          <td class=grid><input name=user type=text style="width:150px;" class=text value="'.$name.'"></td>
          <td class=grid></td></tr>';
echo '<tr><td class=grid align=right>Passowrd:</td>
          <td class=grid><input name=pass type=password style="width:150px;" class=text></td>
          <td class=grid></td></tr>';
echo '<tr><td class=grid></td>
          <td class=grid align=right><input type=submit class=btn name=submit value="确&nbsp;&nbsp;&nbsp;定" style="width:80px;"></td>
          <td class=grid></td></tr>';
echo '</table>';
echo '</form>';

if(isset($_POST['submit']) && $name && $pass){
  $dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
  //$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
  //$dbh -> query("SET NAMES UTF8");
  $dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  $pass1 = sha1($pass);
  $sth = $dbh->prepare('select "uid","uname","ename","cname","branch","dep","tel","mb","email","standing","regtime","del" from "sysuser" where "uname" = :su and "pass"=:sp');
  $sth->bindValue(':su', $name, PDO::PARAM_STR);
  $sth->bindValue(':sp', $pass1, PDO::PARAM_STR);
  $sth->execute();
  //while($row = $sth->fetch()){
  //     echo $row[3];
  //}
  $rows = $sth -> rowCount();
  if($rows != 1){
    $sql ='insert into "syslog"("buser","bdate","btime","bclient","baction","bsucc") values(\''.$name.'\',\''.date('Y-m-d',time()).'\',\''.date('Y-m-d H:i:s',time()).'\',\'motormgr\',\'login\',0)';
    $dbh -> exec($sql);
    echo "用户名或密码不正确,请重新输入!";
    die();
  }else{
    $row = $sth->fetch();
    if($row['del'] == 1){
      echo "该用户名已被注销!";
    }else{
      if($name == $pass){
	echo "<script>window.location='changepass.php?act=renew&uname=$name'</script>";
	die();
      }
      $_SESSION['user']['uid']        = $row['uid'];
      $_SESSION['user']['mail']       = $row['email'];
      $_SESSION['user']['uname']      = $row['uname'];
      $_SESSION['user']['ename']      = $row['ename'];
      $_SESSION['user']['cname']      = $row['cname'];
      $_SESSION['user']['branch']     = $row['branch'];
      $_SESSION['user']['dep']        = $row['dep'];
      $_SESSION['user']['tel']        = $row['tel'];
      $_SESSION['user']['mb']         = $row['mb'];
      $_SESSION['user']['standing']   = $row['standing'];
      $_SESSION['user']['regtime']    = $row['regtime'];
      $_SESSION['user']['logintime']  = date('Y-m-d H:i:s',time());
      $_SESSION['user']['logindate']  = date('Y-m-d',time());
      $_SESSION['sys']['login_motor'] = '1';
      $sql ='insert into "syslog"("buid","bdate","btime","bclient","baction","bsucc") values('.$_SESSION['user']['uid'].',\''.$_SESSION['user']['logindate'].'\',\''.$_SESSION['user']['logintime'].'\',\'motormgr\',\'login\',1)';
      $dbh -> exec($sql);
      echo "<script>window.location='main.php'</script>";
    }
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




