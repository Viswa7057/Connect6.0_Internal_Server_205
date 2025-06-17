#!/usr/bin/php
<?php
include_once("phpagi-2.20/phpagi.php");
$AGI = new AGI();

$lead_id = $argv[1];
$uniq = $argv[2];
$chan = $argv[3];
include_once("/home/common/db_connect.php");

mysqli_query($conn,"update call_log set client_time=now(),customer_leg='$chan' where uniqid='$uniq'");
$query = mysqli_query($conn,"select audio,ivr from haloo_list where lead_id = '$lead_id'");
//$query = mysqli_query($conn,"select audio from haloo_list where lead_id = (select lead_id from dialable_lead where id = '$lead_id' limit 1));
$res = mysqli_fetch_array($query);
//$audio = explode("/".substr($res['audio'],0,-4);

$aa = $res['audio'];
$audio = substr($aa,0,-4);
$ivr = $res['ivr'];
$AGI->set_variable("audio",$audio);
$AGI->set_variable("ivrname",$ivr);
?>



