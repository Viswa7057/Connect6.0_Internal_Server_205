<?php
$campaign = $_REQUEST['campaign'];

require_once("db_connect.php");
	
	
$query_select = "SELECT lead_id,label_first_name,label_middle_name,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_gender,label_phone_number,label_phone_code,label_alt_phone1,label_alt_phone2,label_alt_phone3,label_alt_phone4,label_alt_phone5,label_alt_phone6,label_alt_phone7,label_alt_phone8,label_comments,label_field1,label_field2,label_field3,label_field4,label_field5,label_field6,label_field7,label_field8,label_field9,label_field10,label_field11,label_field12,label_field13,label_field14,label_field15,label_field16,label_field17,label_field18,label_field19,label_field20,label_field21,label_field22,label_field23,label_field24,label_field25,label_field26,label_field27,label_field28,label_dropdown1,label_dropdown2,label_dropdown3,label_dropdown4,label_dropdown5,label_dropdown6,label_dropdown7,agent,priority FROM haloo_list WHERE campaign='$campaign' AND status='PRE_VIEW' AND (modify_date IS NULL OR modify_date = '') AND (hp_status IS NULL OR hp_status = '') ORDER BY priority ASC, RAND() LIMIT 1;";
			 $res_select = mysqli_query($conn,$query_select);
                         $row = mysqli_fetch_array($res_select);
						 
$lead_id = $row['lead_id'];
$first_name = $row['label_first_name'];
$middle_name = $row['label_middle_name'];
$last_name = $row['label_last_name'];
$address1 = $row['label_address1'];
$address2 = $row['label_address2'];
$address3 = $row['label_address3'];
$label_city = $row['label_city'];
$label_state = $row['label_state'];
$label_province = $row['label_province'];
$label_gender = $row['label_gender'];
$label_phone_number = $row['label_phone_number'];
$label_phone_code = $row['label_phone_code'];
$alt_phone1 = $row['label_alt_phone1'];
$alt_phone2 = $row['label_alt_phone2'];
$alt_phone3 = $row['label_alt_phone3'];
$alt_phone4 = $row['label_alt_phone4'];
$alt_phone5 = $row['label_alt_phone5'];
$alt_phone6 = $row['label_alt_phone6'];
$alt_phone7 = $row['label_alt_phone7'];
$alt_phone8 = $row['label_alt_phone8'];
$comments = $row['label_comments'];
$label_field1 = $row['label_field1'];
$label_field2 = $row['label_field2'];
$label_field3 = $row['label_field3'];
$label_field4 = $row['label_field4'];
$label_field5 = $row['label_field5'];
$label_field6 = $row['label_field6'];
$label_field7 = $row['label_field7'];
$label_field8 = $row['label_field8'];
$label_field9 = $row['label_field9'];
$label_field10 = $row['label_field10'];
$label_field11 = $row['label_field11'];
$label_field12 = $row['label_field12'];
$label_field13 = $row['label_field13'];
$label_field14 = $row['label_field14'];
$label_field15 = $row['label_field15'];
$label_field16 = $row['label_field16'];
$label_field17 = $row['label_field17'];
$label_field18 = $row['label_field18'];
$label_field19 = $row['label_field19'];
$label_field20 = $row['label_field20'];
$label_field21 = $row['label_field21'];
$label_field22 = $row['label_field22'];
$label_field23 = $row['label_field23'];
$label_field24 = $row['label_field24'];
$label_field25 = $row['label_field25'];
$label_field26 = $row['label_field26'];
$label_field27 = $row['label_field27'];
$label_field28 = $row['label_field28'];
$dropdown1_list = $row['label_dropdown1'];
$dropdown2_list = $row['label_dropdown2'];
$dropdown3_list = $row['label_dropdown3'];
$dropdown4_list = $row['label_dropdown4'];
$dropdown5_list = $row['label_dropdown5'];
$dropdown6_list = $row['label_dropdown6'];
$dropdown7_list = $row['label_dropdown7'];
$agent = $row['agent'];

echo $first_name."||".$middle_name."||".$last_name."||".$address1."||".$address2."||".$address3."||".$label_city."||".$label_state."||".$label_province."||".$label_gender."||".$label_phone_number."||".$label_phone_code."||".$alt_phone1."||".$alt_phone2."||".$alt_phone3."||".$alt_phone4."||".$alt_phone5."||".$alt_phone6."||".$alt_phone7."||".$alt_phone8."||".$comments."||".$label_field1."||".$label_field2."||".$label_field3."||".$label_field4."||".$label_field5."||".$label_field6."||".$label_field7."||".$label_field8."||".$label_field9."||".$label_field10."||".$label_field11."||".$label_field12."||".$label_field13."||".$label_field14."||".$label_field15."||".$label_field16."||".$label_field17."||".$label_field18."||".$label_field19."||".$label_field20."||".$label_field21."||".$label_field22."||".$label_field23."||".$label_field24."||".$label_field25."||".$label_field26."||".$label_field27."||".$label_field28."||".$dropdown1_list."||".$dropdown2_list."||".$dropdown3_list."||".$dropdown4_list."||".$dropdown5_list."||".$dropdown6_list."||".$dropdown7_list."||".$agent;

$query_update = "UPDATE haloo_list SET status='Preview',modify_date=now(),hp_status=3 WHERE lead_id='$lead_id'";
			 $res_select = mysqli_query($conn,$query_update);
?>
