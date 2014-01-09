<?php
header('Content-Type: text/html; charset=UTF-8');

$ssqlserver="10.210.136.18";
$ssqluser="phpuser";
$ssqlpass="yinti2009";

//$smysqlserver="127.0.0.1";
//$smysqlserver="localhost";
$smysqlserver="10.210.136.19";
$smysqlport=3306;
$smysqltype="mysql";
$smysqluser="buserstandard";
$smysqlpass="qianli";
$smysqlsystem="msystem";

$smysql_db="mdata_2009";
$smysql_db_ar="mdata_2008";


$conf['server'][0]['desc'] = 'PostgreSQL';
$conf['server'][0]['type'] = 'pgsql';
$conf['server'][0]['host'] = 'localhost';
$conf['server'][0]['port'] = 5432;
$conf['server'][0]['user'] = 'webuser';
$conf['server'][0]['pass'] = 'webuser';
$conf['server'][0]['sysdb'] = 'msystem';
$conf['server'][0]['defaultdb'] = 'prod';

$adminmail="guirong.wang@dhl.com";

//警告颜色, 1,2,3分别为红色,橙色,黄色
$alert_color1 = "#FF2608";
$alert_color2 = "#FFA500";
$alert_color3 = "#FFFF00";

function fun_get_cip(){                    //»ñÈ¡¿Í»§¶ËIPµØÖ·
  if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
    if ($_SERVER["HTTP_CLIENT_IP"]) {
      $proxy = $_SERVER["HTTP_CLIENT_IP"];
    } else {
      $proxy = $_SERVER["REMOTE_ADDR"];
    }
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else {
    if ($_SERVER["HTTP_CLIENT_IP"]) {
      $ip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
      $ip = $_SERVER["REMOTE_ADDR"];
    }
  }
  //echo "Your IP $ip";
  //if (isset($proxy)) {
  //echo "Your proxy IP is $proxy";
  //}
  return($ip);
}

function funBarryStr1($str){       //½«ÐÎÈçGPC3270,GSS3000,BFA5010µÄ×Ö·û´®¼ÓÉÏµ¥ÒýºÅ,±ä³ÉÐÎÈç 'GPC3270','GSS3000','BFA5010'
  $arr=explode(",",$str);
  $strL="";
  for($i=0;$i<sizeof($arr);$i++){
    if($i==sizeof($arr)-1){
      $strL=$strL."'".$arr[$i]."'";
    }
    else{
      $strL=$strL."'".$arr[$i]."',";
    }
  }
  return($strL);
}


function funBarryPerNum($str){    //½«ÐÎÈçJAN08µÄÆÚ¼ä¸ñÊ½×ª»»ÎªÐÎÈç200801¸ñÊ½
  $str=trim($str);
  $str=strtoupper($str);
  $mon=substr($str,0,3);
  $year=substr($str,3,2);
  $year="20".$year;
  switch($mon){
  case "JAN":
    $monnum="01";
    break;
  case "FEB":
    $monnum="02";
    break;
  case "MAR":
    $monnum="03";
    break;
  case "APR":
    $monnum="04";
    break;
  case "MAY":
    $monnum="05";
    break;
  case "JUN":
    $monnum="06";
    break;
  case "JUL":
    $monnum="07";
    break;
  case "AUG":
    $monnum="08";
    break;
  case "SEP":
    $monnum="09";
    break;
  case "OCT":
    $monnum="10";
    break;
  case "NOV":
    $monnum="11";
    break;
  case "DEC":
    $monnum="12";
    break;
  }
  return($year.$monnum);
}




function funPeriodAdd($perin){	       /* 期间增加1 */
  $year=substr($perin,0,4);
  $month=substr($perin,4,2);
  if($month<12){
    return($perin+1);
  }else{
    $year++;
    return($year."01");
  }
}


function funPeriodReduce($perin){
  $year=substr($perin,0,4);
  $month=substr($perin,4,2);
  if($month>1){
    return($perin-1);
  }else{
    $year--;
    return($year."12");
  }
}


function fun_file_wipe_null($file){           /* È¥³ýÎÄ¼þÖÐµÄ\N */
  $body = file_get_contents($file);
  $body2 = str_replace("\N", "", $body);
  unlink($file);
  $handle = fopen ($file,"w");
  fwrite ($handle,$body2);
  fclose ($handle);                           //¹Ø±ÕÖ¸Õë
}

function fun_file_wipe_quota($file){           /* È¥³ýÎÄ¼þÖÐµÄË«ÒýºÅ */
  $body = file_get_contents($file);
  $body2 = str_replace(chr(34), "", $body);    //Ìæ»»Ë«ÒýºÅ
  unlink($file);
  $handle = fopen ($file,"w");
  fwrite ($handle,$body2);
  fclose ($handle);                            //¹Ø±ÕÖ¸Õë
}

function fun_get_per_days($perin){
  $year=substr($perin,0,4);
  $month=substr($perin,4,2);
  $d = $year . "-" . $month;
  return(date('t',strtotime($d)));
}


?>