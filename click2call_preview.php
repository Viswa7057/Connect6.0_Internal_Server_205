<?php
	$mobile    = $_REQUEST['phoneNumber'];
			$extension = $_REQUEST['extension'];  //internalPhoneline
			$user    = $_REQUEST['user'];
		        $callback = $_REQUEST['callback'];
			$CON    = $_REQUEST['status'];
				
				$date = date('Y-m-d H:i:s');

			include_once("/home/common/db_connect.php");
                        $uniq = uniqid();
                         error_reporting(E_ALL);
		/*	 $cat = exec("/usr/sbin/asterisk -rx 'sip show channels' | grep -w $agent");
       				if($cat == ''){}else{
        				exit;}*/
 	                 $query = "select callstatus,confnum,channel,wfh_number from user_live where extension='$extension'";
			 $res = mysqli_query($conn,$query);
                         $row = mysqli_fetch_array($res);
                         $callstatus = $row['callstatus'];
                         $agchan = $row['channel'];
                         $confnum = $row['confnum'];
                         $wfh = $row['wfh_number'];
                         if($wfh == ''){}else{
			 $catt = exec("/usr/sbin/asterisk -rx 'sip show channels' | grep -w $wfh");
                         if($catt == ''){}else{exit;}}

			 if(($cat == '')&&($callstatus == 'IDLE' || $callstatus == 'Manual')){}else{
                                        exit;}
                         $uniq=uniqid();
                         $now = date('YMdHis');
        		 $path = "/home/monitor/";
        		 $recording = $path."OUT".$mobile."_".$extension."_".$now;
        		 $recmp3 = "OUT".$mobile."_".$extension."_".$now.".mp3";
			 $mtrand = mt_rand(10000,99999);
			 //$confnum = "87".$mobile.$mtrand;
			 mysqli_query($conn,"SET SESSION sql_mode = ''");
                         $query="insert into call_log(calledid,agent,uniqid,dial_time,confnum,calltype,monitorfile,callerid,agent_leg,CON)values('$mobile','$extension','$uniq',now(),'$confnum','PREVIEW','$recmp3','','$agchan','$CON')";
                         mysqli_query($conn,$query);

                        if(($CON == 'CON')||($CON == 'CB')){$query = "update user_live set callstatus='ONCALL',uniqid='$uniq',phone_number='$mobile' where extension='$extension'";}else{
                        $query = "update user_live set callstatus='DIAL',uniqid='$uniq',phone_number='$mobile' where extension='$extension'";}
                        mysqli_query($conn,$query);

		        $channel = "SIP/".$extension;
                        if($wfh == ''){}else{$channel = "SIP/JIO1/".$wfh;}

			$uniq1 = "/tmp/".$uniq.".call";
        		$myfile =  fopen($uniq1,"w");
                $cat = exec("/usr/sbin/asterisk -rx 'sip show channels' | grep -w $extension");
                                if($cat == ''){
                fwrite($myfile,"channel:$channel\n");
                //fwrite($myfile,"CALLERID:00917969413341\n");
                fwrite($myfile,"context:out-six\n");
                fwrite($myfile,"waittime:45\n");
                fwrite($myfile,"extension:$confnum\n");
                fwrite($myfile,"Setvar:agentobd=$extension\n");
                fwrite($myfile,"Setvar:calledid=$mobile\n");
                fwrite($myfile,"Setvar:uniq=$uniq\n");
                fwrite($myfile,"Setvar:recfile=$recording\n");
                fwrite($myfile,"Setvar:confnum=$confnum\n");
                fwrite($myfile,"Setvar:CON=$CON\n");
                //fwrite($myfile,"Setvar:__SIPADDHEADER1=P-Preferred-Identity:<sip:00917969413333@10.60.72.72>\n");
                fclose($myfile);
                exec("/bin/mv $uniq1 /var/spool/asterisk/outgoing");
                 }else{
                exec("/usr/sbin/asterisk -rx 'channel redirect $agchan chanagentcall,6790,1'");}




?>
