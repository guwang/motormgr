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

$act   = $_GET['act'];
$uname = $_GET['uname'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
$pass3 = $_POST['pass3'];

if(!$uname) $uname = $_SESSION['user']['uname'];

echo "<center><h2>更改密码</h2></center>";
if($act == "renew") echo "为保障密码安全,您必须更改新密码才能登录系统!<br /><br />";
echo '<form name=frmpass id=frmpass enctype="multipart/form-data" method="post">';
echo '<table>';
echo '<tr><td class=grid align=right>用户名:</td><td class=grid>'.$uname.'</td></tr>';
echo '<tr><td class=grid align=right>当前密码:</td>
          <td class=grid><input name=pass1 type=password style="width:150px;" class=text></td>
          <td class=grid></td></tr>';
echo '<tr><td class=grid align=right>新密码:</td>
          <td class=grid><input name=pass2 type=password style="width:150px;" class=text></td>
          <td class=grid></td></tr>';
echo '<tr><td class=grid align=right>重复新密码:</td>
          <td class=grid><input name=pass3 type=password style="width:150px;" class=text></td>
          <td class=grid></td></tr>';
echo '<tr><td class=grid></td>
          <td class=grid align=right><input type=submit class=btn value="确&nbsp;&nbsp;&nbsp;定" style="width:80px;"></td>
          <td class=grid></td></tr>';
echo '</table>';
echo '</form>';

echo "$pass1<br>$pass2<br>$pass3";

//if($pass1 & $pass2 & ($pass2 == $pass3)){
if($pass1 & $pass2 & $pass3){
  if($pass2 != $pass3) die("两次输入的两密码必须一样!");
  $dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user;password=$db_pass");
  //$dbh = new PDO("$db_type:host=$db_host;port=$db_port;dbname=$db_name;user=$db_user");
  //$dbh -> query("SET NAMES UTF8");
  $dbh -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

  //  echo "<br />执行";
  //  die();

  $pass1h = sha1($pass1);
  $sth = $dbh->prepare('select "uid","uname","ename","cname","branch","dep","tel","mb","email","standing","regtime","del" from "sysuser" where "uname" = :su and "pass"=:sp');
  $sth->bindValue(':su', $uname, PDO::PARAM_STR);
  $sth->bindValue(':sp', $pass1h, PDO::PARAM_STR);
  $sth->execute();
  //while($row = $sth->fetch()){
  //     echo $row[3];
  //}
  $rows = $sth -> rowCount();
  if($rows != 1){
    $sql ='insert into "syslog"("buser","bdate","btime","bclient","baction","bsucc") values(\''.$name.'\',\''.date('Y-m-d',time()).'\',\''.date('Y-m-d H:i:s',time()).'\',\'motormgr\',\'login\',0)';
    $dbh -> exec($sql);
    echo "当前密码不正确,请重新输入!";
    die();
  }else{
    $row = $sth->fetch();
    if($row['del'] == 1){
      echo "该用户名已被注销!";
    }else{
      $pass2h = sha1($pass2);
      $sth2 = $dbh->prepare('update "sysuser" set "pass"=:sp where "uname" = :su');
      $sth2->bindValue(':su', $uname, PDO::PARAM_STR);
      $sth2->bindValue(':sp', $pass2h, PDO::PARAM_STR);
      $sth2->execute();

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


?>
</BODY>
</HTML>
