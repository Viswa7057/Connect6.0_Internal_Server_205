#!/usr/bin/php
<?php
include_once("phpagi-2.20/phpagi.php");
$AGI = new AGI();


$uniq = $argv[1];
$hcause = $argv[2];
$disposition = $argv[3];
$recordfile = $argv[4].".wav";
$rec = str_replace("monitor","MP3",$argv[4]).".mp3";
//$rec = $argv[4].".mp3";
$agent = $argv[5];
$cleg = $argv[6];


include_once("/home/common/db_connect.php");

$query = "update user_live set callstatus = 'IDLE' where extension='$agent'";
mysqli_query($conn,$query);


$query = "select id,agent_leg,customer_leg,otheragent_leg,btransfer,calledid,agent,pd_dial,hang_status,calledid from call_log where uniqid='$uniq'";
$query1 = mysqli_query($conn,$query);

                $rows = mysqli_fetch_array($query1);
                $aleg = $rows['agent_leg'];
                $cleg = $rows['customer_leg'];$oleg = $rows['otheragent_leg'];$btransfer = $rows['btransfer'];$calledid=$rows['calledid'];$aagent = $rows['agent'];
                $pddial = $rows['pd_dial'];$isstat=$rows['hang_status'];$calledid = $rows['calledid'];$vid = $rows['id'];

system("/usr/sbin/asterisk -rx 'channel request hangup $cleg'");

$query ="update call_log set end_time=now(),duration=time_to_sec(timediff(end_time,dial_time)),client_duration=time_to_sec(timediff(end_time,client_time)),dispositionother='$disposition',generaldisposition='$disposition',causecodeother='$hcause',generalcausecode = '$hcause' where uniqid='$uniq'";
mysqli_query($conn,$query);

/*$ten = mysqli_query($conn,"select * from calling_redial where phone_number='$calledid' order by id desc limit 1");
if(mysqli_num_rows($ten)==1){
$rt = mysqli_fetch_array($ten);
$attempt = $rt['attempts'];$last=$rt['att'];
if($attempt == $last){
mysqli_query($conn,"delete from calling_redial where phone_number='$calledid'");
goto label20;}
}*/

$resj = mysqli_query($conn,"select * from calling_redial where phone_number='$calledid' and status = '1' limit 1");
if(mysqli_num_rows($resj)==1){
//$er = mysqli_fetch_array($resj);
//$uniq = $er['uniqid'];
//$id = $er['id'];
//mysqli_query($conn,"update calling_redial set status='0' where id='$id'");
}else{
if($cleg == ""){
$res = mysqli_query($conn,"select no_attempts,time_gap,list_id,lead_id from haloo_list where lead_id = (select lead_id from call_log where calledid = '$calledid' and calltype='VB' order by id desc limit 1)");
$rd = mysqli_fetch_array($res);
$att = $rd['no_attempts'];$gap = $rd['time_gap'];$list_id = $rd['list_id'];$lead_id=$rd['lead_id'];
if($att == 0){}else{
        date_default_timezone_set('Asia/Kolkata');
	 $datenow = date('Y-m-d H:i:s');
         $gap = "1";
	for($d=1;$d<=$att;$d++){
		$redial = $gap*$d;
                $endTime = strtotime("+$redial minutes", strtotime($datenow));
		$ret= date('Y-m-d H:i:s', $endTime);
		mysqli_query($conn,"SET SESSION sql_mode = ''");
                mysqli_query($conn,"insert into calling_redial(phone_number,entry_date,list_id,status,attempts,gap,dial_time,lead_id,uniqid,agent,vid)values('$calledid','$ret','$list_id','1','$att','$gap',now(),'$lead_id','$uniq','$aagent','$vid')");
             }
	}	


	}
}
//label20:
exec("/usr/bin/curl -k '$incoming_url/API/backAPI/voice_blast.php?extension=$agent&next=1'");

?>
        


