<?php

session_start();
require_once("db_connect.php");
//$link = mysqli_connect("localhost","root","Hal0o(0m@72427242","halootel_bkup");

$loggedInuserName  = $_SESSION['username'];
$loggedInPassword  = $_SESSION['pass'];
$loggedInUserLevel = $_SESSION['user_level'];

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */


if($loggedInuserName == '' && $loggedInPassword == '' ){
	header('Location:/haloocomConnect5/pages/Agent/connect5Login/index.php');

}
$stmt_group="SELECT user_group from users where user_id='$loggedInuserName'";
//echo $stmt_group;
			  $rslt_group= mysqli_query($conn,$stmt_group);
	                           $row_g= mysqli_fetch_row($rslt_group);
				   $user_group = $row_g[0];

$stmt_select="SELECT * from features_settings";
	      $rslt_rs= mysqli_query($conn,$stmt_select);


			   while($row = mysqli_fetch_assoc($rslt_rs)) {
				  $incoming_did = $row["incoming_did"];
				  $incoming_ivr = $row["incoming_ivr"];
				  $predictive_dialing = $row["predictive_dialing"];
				  $userwise_dialing = $row["userwise_dialing"];
				  $voice_blast = $row["voice_blast"];
				  $announcement = $row["announcement"];
				  $star_month = $row["star_month"];
				  $dependant_dropdown = $row["dependant_dropdown"];
				  $feedback_ivr = $row["feedback_ivr"];
				  $phone_book = $row["phone_book"];
			   }



if ($_POST['addlist']) {

  $list_name = $_POST['list_name'];
  $list_id = $_POST['list_id'];
  $campaign = $_POST['campaign'];
  $list_assign = $_POST['list_assign'];
  
if($list_assign == "Preview"){
	$status_n = "PRE_VIEW";
	$campaign = $_POST['preview_campaign'];
}else{
	$status_n = "NEW";
}
  $stmt_rs = "SELECT list_id from haloo_list where list_id='$list_id';";
  $rslt_rs = mysqli_query($conn, $stmt_rs);
  $row_rs = mysqli_fetch_row($rslt_rs);

  if ($row_rs == "") {

  

      // Allowed mime types
      $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

      // Validate whether selected file is a CSV file
      if(!empty($_FILES['uploadfile']['name'])){
      if(in_array($_FILES['uploadfile']['type'], $csvMimes)) {

        // If the file is uploaded
        if (is_uploaded_file($_FILES['uploadfile']['tmp_name'])) {

          // Open uploaded CSV file with read-only mode
          $csvFile = fopen($_FILES['uploadfile']['tmp_name'], 'r');

          // Skip the first line
          fgetcsv($csvFile);

          // Parse data from CSV file line by line
          while (($line = fgetcsv($csvFile)) !== FALSE) {
            // Get row data

            $label_first_name = $line[0];
            $label_middle_name = $line[1];
            $label_last_name = $line[2];
            $label_address1 = $line[3];
            $label_address2 = $line[4];
            $label_address3 = $line[5];
            $label_city = $line[6];
            $label_state = $line[7];
            $label_province = $line[8];
            $label_gender = $line[9];
            $label_phone_number = $line[10];
            $label_phone_code = $line[11];
            $label_alt_phone1 = $line[12];
            $label_alt_phone2 = $line[13];
            $label_alt_phone3 = $line[14];
            $label_alt_phone4 = $line[15];
            $label_alt_phone5 = $line[16];
            $label_alt_phone6 = $line[17];
            $label_alt_phone7 = $line[18];
            $label_alt_phone8 = $line[19];
            $label_comments = $line[20];
            $label_field1 = $line[21];
            $label_field2 = $line[22];
            $label_field3 = $line[23];
            $label_field4 = $line[24];
            $label_field5 = $line[25];
            $label_field6 = $line[26];
            $label_field7 = $line[27];
            $label_field8 = $line[28];
            $label_field9 = $line[29];
            $label_field10 = $line[30];
            $label_field11 = $line[31];
            $label_field12 = $line[32];
            $label_field13 = $line[33];
            $label_field14 = $line[34];
            $label_field15 = $line[35];
            $label_field16 = $line[36];
            $label_field17 = $line[37];
            $label_field18 = $line[38];
            $label_field19 = $line[39];
            $label_field20 = $line[40];
            $label_field21 = $line[41];
            $label_field22 = $line[42];
            $label_field23 = $line[43];
            $label_field24 = $line[44];
            $label_field25 = $line[45];
            $label_field26 = $line[46];
            $label_field27 = $line[47];
            $label_field28 = $line[48];
            $users = $line[49];
			$priority = $line[50];


            $date = date("Y-m-d H:i:s");
$list_id = mysqli_real_escape_string($conn, $list_id);
$list_name = mysqli_real_escape_string($conn, $list_name);
$campaign = mysqli_real_escape_string($conn, $campaign);
$label_first_name = mysqli_real_escape_string($conn, $label_first_name);
$label_middle_name = mysqli_real_escape_string($conn, $label_middle_name);
$label_last_name = mysqli_real_escape_string($conn, $label_last_name);
$label_address1 = mysqli_real_escape_string($conn, $label_address1);
$label_address2 = mysqli_real_escape_string($conn, $label_address2);
$label_address3 = mysqli_real_escape_string($conn, $label_address3);
$label_city = mysqli_real_escape_string($conn, $label_city);
$label_state = mysqli_real_escape_string($conn, $label_state);
$label_province = mysqli_real_escape_string($conn, $label_province);
$label_gender = mysqli_real_escape_string($conn, $label_gender);
$label_phone_number = mysqli_real_escape_string($conn, $label_phone_number);
$label_phone_code = mysqli_real_escape_string($conn, $label_phone_code);
$label_alt_phone1 = mysqli_real_escape_string($conn, $label_alt_phone1);
$label_alt_phone2 = mysqli_real_escape_string($conn, $label_alt_phone2);
$label_alt_phone3 = mysqli_real_escape_string($conn, $label_alt_phone3);
$label_alt_phone4 = mysqli_real_escape_string($conn, $label_alt_phone4);
$label_alt_phone5 = mysqli_real_escape_string($conn, $label_alt_phone5);
$label_alt_phone6 = mysqli_real_escape_string($conn, $label_alt_phone6);
$label_alt_phone7 = mysqli_real_escape_string($conn, $label_alt_phone7);
$label_alt_phone8 = mysqli_real_escape_string($conn, $label_alt_phone8);
$label_comments = mysqli_real_escape_string($conn, $label_comments);
$label_field1 = mysqli_real_escape_string($conn, $label_field1);
$label_field2 = mysqli_real_escape_string($conn, $label_field2);
$label_field3 = mysqli_real_escape_string($conn, $label_field3);
$label_field4 = mysqli_real_escape_string($conn, $label_field4);
$label_field5 = mysqli_real_escape_string($conn, $label_field5);
$label_field6 = mysqli_real_escape_string($conn, $label_field6);
$label_field7 = mysqli_real_escape_string($conn, $label_field7);
$label_field8 = mysqli_real_escape_string($conn, $label_field8);
$label_field9 = mysqli_real_escape_string($conn, $label_field9);
$label_field10 = mysqli_real_escape_string($conn, $label_field10);
$label_field11 = mysqli_real_escape_string($conn, $label_field11);
$label_field12 = mysqli_real_escape_string($conn, $label_field12);
$label_field13 = mysqli_real_escape_string($conn, $label_field13);
$label_field14 = mysqli_real_escape_string($conn, $label_field14);
$label_field15 = mysqli_real_escape_string($conn, $label_field15);
$label_field16 = mysqli_real_escape_string($conn, $label_field16);
$label_field17 = mysqli_real_escape_string($conn, $label_field17);
$label_field18 = mysqli_real_escape_string($conn, $label_field18);
$label_field19 = mysqli_real_escape_string($conn, $label_field19);
$label_field20 = mysqli_real_escape_string($conn, $label_field20);
$label_field21 = mysqli_real_escape_string($conn, $label_field21);
$label_field22 = mysqli_real_escape_string($conn, $label_field22);
$label_field23 = mysqli_real_escape_string($conn, $label_field23);
$label_field24 = mysqli_real_escape_string($conn, $label_field24);
$label_field25 = mysqli_real_escape_string($conn, $label_field25);
$label_field26 = mysqli_real_escape_string($conn, $label_field26);
$label_field27 = mysqli_real_escape_string($conn, $label_field27);
$label_field28 = mysqli_real_escape_string($conn, $label_field28);
$users = mysqli_real_escape_string($conn, $users);
$priority = mysqli_real_escape_string($conn, $priority);

$stmt_insert = "INSERT INTO haloo_list(created_date,list_id,list_name,status,campaign,label_first_name,label_middle_name,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_gender,label_phone_number,label_phone_code,label_alt_phone1,label_alt_phone2,label_alt_phone3,label_alt_phone4,label_alt_phone5,label_alt_phone6,label_alt_phone7,label_alt_phone8,label_comments,label_field1,label_field2,label_field3,label_field4,label_field5,label_field6,label_field7,label_field8,label_field9,label_field10,label_field11,label_field12,label_field13,label_field14,label_field15,label_field16,label_field17,label_field18,label_field19,label_field20,label_field21,label_field22,label_field23,label_field24,label_field25,label_field26,label_field27,label_field28,list_agent,hp_status,user_group,list_status,priority) values('$date','$list_id','$list_name','$status_n','$campaign','$label_first_name','$label_middle_name','$label_last_name','$label_address1','$label_address2','$label_address3','$label_city','$label_state','$label_province','$label_gender','$label_phone_number','$label_phone_code','$label_alt_phone1','$label_alt_phone2','$label_alt_phone3','$label_alt_phone4','$label_alt_phone5','$label_alt_phone6','$label_alt_phone7','$label_alt_phone8','$label_comments','$label_field1','$label_field2','$label_field3','$label_field4','$label_field5','$label_field6','$label_field7','$label_field8','$label_field9','$label_field10','$label_field11','$label_field12','$label_field13','$label_field14','$label_field15','$label_field16','$label_field17','$label_field18','$label_field19','$label_field20','$label_field21','$label_field22','$label_field23','$label_field24','$label_field25','$label_field26','$label_field27','$label_field28','$users','','$user_group','Active','$priority')";
//echo $stmt_insert;

$rslt_insert = mysqli_query($conn, $stmt_insert);

			 }


          // Close opened CSV file
          fclose($csvFile);

          $qstring = '?status=succ';
        } else {
          $qstring = '?status=err';
        }
      } else {
        //$qstring = '?status=invalid_file';
        echo '<script type="text/javascript">alert("Sorry,Only CSV files are allowed.");</script>';
      }
    } else {
      $datetime = date('Y-m-d H:i:s');
          $stmt_select1 = "SELECT list_status from haloo_list where list_id ='$list_id' order by lead_id limit 1";
                          $rslt_rs1 = mysqli_query($conn, $stmt_select1);
                          $row_list = mysqli_fetch_assoc($rslt_rs1);
                          $list_status = $row_list["list_status"];
      $list_status = $row_rs['list_status'];
      $stmt_insert = "INSERT INTO haloo_list (created_date,list_id,list_name,list_status,campaign,user_group) VALUES ('$datetime','$list_id','$list_name','Active','$campaign','$user_group')";
      $rslt_insert_blank = mysqli_query($conn, $stmt_insert);
	}


  } else {
    echo "<script>alert('List id already exist.');</script>";
  }
}



if ($_POST['updatelist_existing']) {

      // Allowed mime types
      $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

      // Validate whether selected file is a CSV file
      if (!empty($_FILES['uploadfile1']['name']) && in_array($_FILES['uploadfile1']['type'], $csvMimes)) {

        // If the file is uploaded
        if (is_uploaded_file($_FILES['uploadfile1']['tmp_name'])) {

          // Open uploaded CSV file with read-only mode
          $csvFile1 = fopen($_FILES['uploadfile1']['tmp_name'], 'r');

          // Skip the first line
          fgetcsv($csvFile1);

          // Parse data from CSV file line by line
          while (($line = fgetcsv($csvFile1)) !== FALSE) {
            // Get row data

            $label_first_name = $line[0];
            $label_middle_name = $line[1];
            $label_last_name = $line[2];
            $label_address1 = $line[3];
            $label_address2 = $line[4];
            $label_address3 = $line[5];
            $label_city = $line[6];
            $label_state = $line[7];
            $label_province = $line[8];
            $label_gender = $line[9];
            $label_phone_number = $line[10];
            $label_phone_code = $line[11];
            $label_alt_phone1 = $line[12];
            $label_alt_phone2 = $line[13];
            $label_alt_phone3 = $line[14];
            $label_alt_phone4 = $line[15];
            $label_alt_phone5 = $line[16];
            $label_alt_phone6 = $line[17];
            $label_alt_phone7 = $line[18];
            $label_alt_phone8 = $line[19];
            $label_comments = $line[20];
            $label_field1 = $line[21];
            $label_field2 = $line[22];
            $label_field3 = $line[23];
            $label_field4 = $line[24];
            $label_field5 = $line[25];
            $label_field6 = $line[26];
            $label_field7 = $line[27];
            $label_field8 = $line[28];
            $label_field9 = $line[29];
            $label_field10 = $line[30];
            $label_field11 = $line[31];
            $label_field12 = $line[32];
            $label_field13 = $line[33];
            $label_field14 = $line[34];
            $label_field15 = $line[35];
            $label_field16 = $line[36];
            $label_field17 = $line[37];
            $label_field18 = $line[38];
            $label_field19 = $line[39];
            $label_field20 = $line[40];
            $label_field21 = $line[41];
            $label_field22 = $line[42];
            $label_field23 = $line[43];
            $label_field24 = $line[44];
            $label_field25 = $line[45];
            $label_field26 = $line[46];
            $label_field27 = $line[47];
            $label_field28 = $line[48];
            $users = $line[49];
			$list_id = $line[50];

 $stmt_rs = "SELECT list_id,list_name,campaign,status,list_status from haloo_list where list_id='$list_id' limit 1;";
  $rslt_rs = mysqli_query($conn, $stmt_rs);
  $row_count = mysqli_num_rows($rslt_rs);

  if ($row_count>0) {

    $row_rs = mysqli_fetch_array($rslt_rs);
    $list_name = $row_rs['list_name'];
    $campaign = $row_rs['campaign'];
    $status = $row_rs['status'];
    $list_status = $row_rs['list_status'];
  }

$datetime = date("Y-m-d H:i:s");
$list_id = mysqli_real_escape_string($conn, $list_id);
$list_name = mysqli_real_escape_string($conn, $list_name);
$campaign = mysqli_real_escape_string($conn, $campaign);
$status = mysqli_real_escape_string($conn, $status);
$list_status = mysqli_real_escape_string($conn, $list_status);
$label_first_name = mysqli_real_escape_string($conn, $label_first_name);
$label_middle_name = mysqli_real_escape_string($conn, $label_middle_name);
$label_last_name = mysqli_real_escape_string($conn, $label_last_name);
$label_address1 = mysqli_real_escape_string($conn, $label_address1);
$label_address2 = mysqli_real_escape_string($conn, $label_address2);
$label_address3 = mysqli_real_escape_string($conn, $label_address3);
$label_city = mysqli_real_escape_string($conn, $label_city);
$label_state = mysqli_real_escape_string($conn, $label_state);
$label_province = mysqli_real_escape_string($conn, $label_province);
$label_gender = mysqli_real_escape_string($conn, $label_gender);
$label_phone_number = mysqli_real_escape_string($conn, $label_phone_number);
$label_phone_code = mysqli_real_escape_string($conn, $label_phone_code);
$label_alt_phone1 = mysqli_real_escape_string($conn, $label_alt_phone1);
$label_alt_phone2 = mysqli_real_escape_string($conn, $label_alt_phone2);
$label_alt_phone3 = mysqli_real_escape_string($conn, $label_alt_phone3);
$label_alt_phone4 = mysqli_real_escape_string($conn, $label_alt_phone4);
$label_alt_phone5 = mysqli_real_escape_string($conn, $label_alt_phone5);
$label_alt_phone6 = mysqli_real_escape_string($conn, $label_alt_phone6);
$label_alt_phone7 = mysqli_real_escape_string($conn, $label_alt_phone7);
$label_alt_phone8 = mysqli_real_escape_string($conn, $label_alt_phone8);
$label_comments = mysqli_real_escape_string($conn, $label_comments);
$label_field1 = mysqli_real_escape_string($conn, $label_field1);
$label_field2 = mysqli_real_escape_string($conn, $label_field2);
$label_field3 = mysqli_real_escape_string($conn, $label_field3);
$label_field4 = mysqli_real_escape_string($conn, $label_field4);
$label_field5 = mysqli_real_escape_string($conn, $label_field5);
$label_field6 = mysqli_real_escape_string($conn, $label_field6);
$label_field7 = mysqli_real_escape_string($conn, $label_field7);
$label_field8 = mysqli_real_escape_string($conn, $label_field8);
$label_field9 = mysqli_real_escape_string($conn, $label_field9);
$label_field10 = mysqli_real_escape_string($conn, $label_field10);
$label_field11 = mysqli_real_escape_string($conn, $label_field11);
$label_field12 = mysqli_real_escape_string($conn, $label_field12);
$label_field13 = mysqli_real_escape_string($conn, $label_field13);
$label_field14 = mysqli_real_escape_string($conn, $label_field14);
$label_field15 = mysqli_real_escape_string($conn, $label_field15);
$label_field16 = mysqli_real_escape_string($conn, $label_field16);
$label_field17 = mysqli_real_escape_string($conn, $label_field17);
$label_field18 = mysqli_real_escape_string($conn, $label_field18);
$label_field19 = mysqli_real_escape_string($conn, $label_field19);
$label_field20 = mysqli_real_escape_string($conn, $label_field20);
$label_field21 = mysqli_real_escape_string($conn, $label_field21);
$label_field22 = mysqli_real_escape_string($conn, $label_field22);
$label_field23 = mysqli_real_escape_string($conn, $label_field23);
$label_field24 = mysqli_real_escape_string($conn, $label_field24);
$label_field25 = mysqli_real_escape_string($conn, $label_field25);
$label_field26 = mysqli_real_escape_string($conn, $label_field26);
$label_field27 = mysqli_real_escape_string($conn, $label_field27);
$label_field28 = mysqli_real_escape_string($conn, $label_field28);
$users = mysqli_real_escape_string($conn, $users);

$stmt_insert1 = "insert into haloo_list (created_date,list_id,list_name,status,list_status,campaign,label_first_name,label_middle_name,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_gender,label_phone_number,label_phone_code,label_alt_phone1,label_alt_phone2,label_alt_phone3,label_alt_phone4,label_alt_phone5,label_alt_phone6,label_alt_phone7,label_alt_phone8,label_comments,label_field1,label_field2,label_field3,label_field4,label_field5,label_field6,label_field7,label_field8,label_field9,label_field10,label_field11,label_field12,label_field13,label_field14,label_field15,label_field16,label_field17,label_field18,label_field19,label_field20,label_field21,label_field22,label_field23,label_field24,label_field25,label_field26,label_field27,label_field28,list_agent,audio,remote_agent,no_calls,ivr,no_attempts,time_gap,hp_status,user_group) values ('$datetime','$list_id','$list_name','NEW','$list_status','$campaign','$label_first_name','$label_middle_name','$label_last_name','$label_address1','$label_address2','$label_address3','$label_city','$label_state','$label_province','$label_gender','$label_phone_number','$label_phone_code','$label_alt_phone1','$label_alt_phone2','$label_alt_phone3','$label_alt_phone4','$label_alt_phone5','$label_alt_phone6','$label_alt_phone7','$label_alt_phone8','$label_comments','$label_field1','$label_field2','$label_field3','$label_field4','$label_field5','$label_field6','$label_field7','$label_field8','$label_field9','$label_field10','$label_field11','$label_field12','$label_field13','$label_field14','$label_field15','$label_field16','$label_field17','$label_field18','$label_field19','$label_field20','$label_field21','$label_field22','$label_field23','$label_field24','$label_field25','$label_field26','$label_field27','$label_field28','$users','$newFilePath_audio','$remote_agent','$no_calls','$ivr_assign','$no_attempts','$time_gap','','$user_group') ";
//echo $stmt_insert;exit;
$rslt_insert = mysqli_query($conn, $stmt_insert1);


			 }

          // Close opened CSV file
          fclose($csvFile1);

          $qstring = '?status=succ';

        } else {
          $qstring = '?status=err';
        }
      } else {
        //$qstring = '?status=invalid_file';
        echo '<script type="text/javascript">alert("Sorry,Only CSV files are allowed.");</script>';
      }

	  header('Location:/haloocomConnect5/pages/forms/List.php');
//exit;

}


if ($_POST['dnc_upload'] == 'dnc_upload') {

    // Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

    // Validate whether selected file is a CSV file
    if(!empty($_FILES['upload_dnc']['name']) && in_array($_FILES['upload_dnc']['type'], $csvMimes)){

        // If the file is uploaded
        if(is_uploaded_file($_FILES['upload_dnc']['tmp_name'])){

            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['upload_dnc']['tmp_name'], 'r');

            // Skip the first line
            fgetcsv($csvFile);

$rows = [];
while(($line = fgetcsv($csvFile)) !== FALSE) {
    $phone_number = mysqli_real_escape_string($conn, trim($line[0]));
    if (!empty($phone_number)) {
        $rows[] = "('$phone_number', 'DNC', NOW(), '$user_group')";
    }
}

if (!empty($rows)) {
    $values = implode(',', $rows);
    $query = "
        INSERT INTO haloo_list (label_phone_number, status, created_date, user_group)
        VALUES $values
        ON DUPLICATE KEY UPDATE status='DNC';
    ";
    mysqli_query($conn, $query);
}

            fclose($csvFile);
            echo "<script>alert('File Uploaded Successfully');</script>";
	          header('Location:/haloocomConnect5/pages/forms/List.php');
        }else{
          echo "<script>alert('File Upload Failed');</script>";
        }
    }else{
      echo '<script type="text/javascript">alert("Sorry, Only CSV files are allowed.");</script>';
    }
}


if ($_POST['dnc_remove'] == 'dnc_remove') {
  $remove_campaign = $_POST['remove_campaign'];

  // Allowed mime types
  $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

  // Validate whether selected file is a CSV file
  if (!empty($_FILES['remove_dnc']['name']) && in_array($_FILES['remove_dnc']['type'], $csvMimes)) {

    // If the file is uploaded
    if (is_uploaded_file($_FILES['remove_dnc']['tmp_name'])) {

      // Open uploaded CSV file with read-only mode
      $csvFile = fopen($_FILES['remove_dnc']['tmp_name'], 'r');

      // Skip the first line
      fgetcsv($csvFile);

      // Parse data from CSV file line by line
      while (($line = fgetcsv($csvFile)) !== FALSE) {
        // Get row data

        $phone_number = $line[0];


        $date = date("Y-m-d H:i:s");
        $stmt_update = "update haloo_list set status='' where label_phone_number='$phone_number'";
        $rslt_update = mysqli_query($conn, $stmt_update);
      }

      // Close opened CSV file
      fclose($csvFile);

      $qstring = '?status=succ';
    } else {
      $qstring = '?status=err';
    }
    echo '<script type="text/javascript">alert("DNC list removed.");</script>';
  } else {
    //$qstring = '?status=invalid_file';
    echo '<script type="text/javascript">alert("Sorry,Only CSV files are allowed.");</script>';
  }

}


if ($_POST['download_dnc'] == 'download_dnc') {

  // Define the filterData function
  function filterData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    $str = trim($str);
  }

  $dnc_dow_campaign = $_POST['dnc_dow_campaign'];
  $fileName = "$dnc_dow_campaign" . "_DNC.xls";
  $fields = array("Phone Number");
  $excelData = implode("\t", array_values($fields)) . "\n";

  $stmt_select = "SELECT label_phone_number from haloo_list where status='DNC'";
  $rslt_rs = mysqli_query($conn, $stmt_select);

  while ($row_list = mysqli_fetch_assoc($rslt_rs)) {
    $list = array($row_list["label_phone_number"]);
    array_walk($list, 'filterData');
    $excelData .= implode("\t", array_values($list)) . "\n";
  }

  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"$fileName\"");

  echo "$excelData";
  exit;
}

if ($_POST['editVoiceblast'] == 'edit_Voiceblast') {

  $txt_campaign = $_POST['txt_campaign'];


  function filterData(&$str)
  {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  $fileName = "$txt_campaign" . ".xls";

  $stmt_campaign = "SELECT screen_label from campaign where campaign_name='$txt_campaign';";
  //echo $stmt_campaign; exit;
  $rslt_campaign = mysqli_query($conn, $stmt_campaign);
  $row_campaign = mysqli_fetch_row($rslt_campaign);
  $label_id = $row_campaign[0];

  $stmt_select = "SELECT * from screen_labels where label_id='$label_id';";
  $rslt_rs = mysqli_query($conn, $stmt_select);

  $x = 1;
  while ($row = mysqli_fetch_assoc($rslt_rs)) {
    if ($row["label_first_name"] != "") {
      $label_first_name = $row["label_first_name"];
    } else {
      $label_first_name = "HIDE";
    }
    if ($row["label_middle_name"] != "") {
      $label_middle_name = $row["label_middle_name"];
    } else {
      $label_middle_name = "HIDE";
    }
    if ($row["label_last_name"] != "") {
      $label_last_name = $row["label_last_name"];
    } else {
      $label_last_name = "HIDE";
    }
    if ($row["label_address1"] != "") {
      $label_address1 = $row["label_address1"];
    } else {
      $label_address1 = "HIDE";
    }
    if ($row["label_address2"] != "") {
      $label_address2 = $row["label_address2"];
    } else {
      $label_address2 = "HIDE";
    }
    if ($row["label_address3"] != "") {
      $label_address3 = $row["label_address3"];
    } else {
      $label_address3 = "HIDE";
    }
    if ($row["label_city"] != "") {
      $label_city = $row["label_city"];
    } else {
      $label_city = "HIDE";
    }
    if ($row["label_state"] != "") {
      $label_state = $row["label_state"];
    } else {
      $label_state = "HIDE";
    }
    if ($row["label_province"] != "") {
      $label_province = $row["label_province"];
    } else {
      $label_province = "HIDE";
    }
    if ($row["label_gender"] != "") {
      $label_gender = $row["label_gender"];
    } else {
      $label_gender = "HIDE";
    }
    if ($row["label_phone_number"] != "") {
      $label_phone_number = $row["label_phone_number"];
    } else {
      $label_phone_number = "HIDE";
    }
    if ($row["label_phone_code"] != "") {
      $label_phone_code = $row["label_phone_code"];
    } else {
      $label_phone_code = "HIDE";
    }
    if ($row["label_alt_phone1"] != "") {
      $label_alt_phone1 = $row["label_alt_phone1"];
    } else {
      $label_alt_phone1 = "HIDE";
    }
    if ($row["label_alt_phone2"] != "") {
      $label_alt_phone2 = $row["label_alt_phone2"];
    } else {
      $label_alt_phone2 = "HIDE";
    }
    if ($row["label_alt_phone3"] != "") {
      $label_alt_phone3 = $row["label_alt_phone3"];
    } else {
      $label_alt_phone3 = "HIDE";
    }
    if ($row["label_alt_phone4"] != "") {
      $label_alt_phone4 = $row["label_alt_phone4"];
    } else {
      $label_alt_phone4 = "HIDE";
    }
    if ($row["label_alt_phone5"] != "") {
      $label_alt_phone5 = $row["label_alt_phone5"];
    } else {
      $label_alt_phone5 = "HIDE";
    }
    if ($row["label_alt_phone6"] != "") {
      $label_alt_phone6 = $row["label_alt_phone6"];
    } else {
      $label_alt_phone6 = "HIDE";
    }
    if ($row["label_alt_phone7"] != "") {
      $label_alt_phone7 = $row["label_alt_phone7"];
    } else {
      $label_alt_phone7 = "HIDE";
    }
    if ($row["label_alt_phone8"] != "") {
      $label_alt_phone8 = $row["label_alt_phone8"];
    } else {
      $label_alt_phone8 = "HIDE";
    }
    if ($row["label_comments"] != "") {
      $label_comments = $row["label_comments"];
    } else {
      $label_comments = "HIDE";
    }
    if ($row["label_field1"] != "") {
      $label_field1 = $row["label_field1"];
    } else {
      $label_field1 = "HIDE";
    }
    if ($row["label_field2"] != "") {
      $label_field2 = $row["label_field2"];
    } else {
      $label_field2 = "HIDE";
    }
    if ($row["label_field3"] != "") {
      $label_field3 = $row["label_field3"];
    } else {
      $label_field3 = "HIDE";
    }
    if ($row["label_field4"] != "") {
      $label_field4 = $row["label_field4"];
    } else {
      $label_field4 = "HIDE";
    }
    if ($row["label_field5"] != "") {
      $label_field5 = $row["label_field5"];
    } else {
      $label_field5 = "HIDE";
    }
    if ($row["label_field6"] != "") {
      $label_field6 = $row["label_field6"];
    } else {
      $label_field6 = "HIDE";
    }
    if ($row["label_field7"] != "") {
      $label_field7 = $row["label_field7"];
    } else {
      $label_field7 = "HIDE";
    }
    if ($row["label_field8"] != "") {
      $label_field8 = $row["label_field8"];
    } else {
      $label_field8 = "HIDE";
    }
    if ($row["label_field9"] != "") {
      $label_field9 = $row["label_field9"];
    } else {
      $label_field9 = "HIDE";
    }
    if ($row["label_field10"] != "") {
      $label_field10 = $row["label_field10"];
    } else {
      $label_field10 = "HIDE";
    }
    if ($row["label_field11"] != "") {
      $label_field11 = $row["label_field11"];
    } else {
      $label_field11 = "HIDE";
    }
    if ($row["label_field12"] != "") {
      $label_field12 = $row["label_field12"];
    } else {
      $label_field12 = "HIDE";
    }
    if ($row["label_field13"] != "") {
      $label_field13 = $row["label_field13"];
    } else {
      $label_field13 = "HIDE";
    }
    if ($row["label_field14"] != "") {
      $label_field14 = $row["label_field14"];
    } else {
      $label_field14 = "HIDE";
    }
    if ($row["label_field15"] != "") {
      $label_field15 = $row["label_field15"];
    } else {
      $label_field15 = "HIDE";
    }
    if ($row["label_field16"] != "") {
      $label_field16 = $row["label_field16"];
    } else {
      $label_field16 = "HIDE";
    }
    if ($row["label_field17"] != "") {
      $label_field17 = $row["label_field17"];
    } else {
      $label_field17 = "HIDE";
    }
    if ($row["label_field18"] != "") {
      $label_field18 = $row["label_field18"];
    } else {
      $label_field18 = "HIDE";
    }
    if ($row["label_field19"] != "") {
      $label_field19 = $row["label_field19"];
    } else {
      $label_field19 = "HIDE";
    }
    if ($row["label_field20"] != "") {
      $label_field20 = $row["label_field20"];
    } else {
      $label_field20 = "HIDE";
    }
    if ($row["label_field21"] != "") {
      $label_field21 = $row["label_field21"];
    } else {
      $label_field21 = "HIDE";
    }
    if ($row["label_field22"] != "") {
      $label_field22 = $row["label_field22"];
    } else {
      $label_field22 = "HIDE";
    }
    if ($row["label_field23"] != "") {
      $label_field23 = $row["label_field23"];
    } else {
      $label_field23 = "HIDE";
    }
    if ($row["label_field24"] != "") {
      $label_field24 = $row["label_field24"];
    } else {
      $label_field24 = "HIDE";
    }
    if ($row["label_field25"] != "") {
      $label_field25 = $row["label_field25"];
    } else {
      $label_field25 = "HIDE";
    }
    if ($row["label_field26"] != "") {
      $label_field26 = $row["label_field26"];
    } else {
      $label_field26 = "HIDE";
    }
    if ($row["label_field27"] != "") {
      $label_field27 = $row["label_field27"];
    } else {
      $label_field27 = "HIDE";
    }
    if ($row["label_field28"] != "") {
      $label_field28 = $row["label_field28"];
    } else {
      $label_field28 = "HIDE";
    }
  }
  $fields = array("$label_first_name", "$label_middle_name", "$label_last_name", "$label_address1", "$label_address2", "$label_address3", "$label_city", "$label_state", "$label_province", "$label_gender", "$label_phone_number", "$label_phone_code", "$label_alt_phone1", "$label_alt_phone2", "$label_alt_phone3", "$label_alt_phone4", "$label_alt_phone5", "$label_alt_phone6", "$label_alt_phone7", "$label_alt_phone8", "$label_comments", "$label_field1", "$label_field2", "$label_field3", "$label_field4", "$label_field5", "$label_field6", "$label_field7", "$label_field8", "$label_field9", "$label_field10", "$label_field11", "$label_field12", "$label_field13", "$label_field14", "$label_field15", "$label_field16", "$label_field17", "$label_field18", "$label_field19", "$label_field20", "$label_field21", "$label_field22", "$label_field23", "$label_field24", "$label_field25", "$label_field26", "$label_field27", "$label_field28", "Agent", "Priority");

  $excelData = implode("\t", array_values($fields)) . "\n";

  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"$fileName\"");

  echo "$excelData";
  exit;
}




if ($_POST['deleteVoiceblasting'] == 'delete_Voiceblast') {
  $deleteList = $_POST['deleteVoiceblast'];
  $stmt_delete = "delete from haloo_list where list_id='$deleteList'";
  $rslt_delete = mysqli_query($conn, $stmt_delete);

 
}



if ($_POST['download']) {

  $id = $_POST['download_list'];
  $listname = $_POST['list_name'];
  $campaign = $_POST['download_campaign'];



  $fileName = "$listname" . ".xls";

  $stmt_campaign = "SELECT screen_label from campaign where campaign_name='$campaign';";
  //echo $stmt_campaign; exit;
  $rslt_campaign = mysqli_query($conn, $stmt_campaign);
  $row_campaign = mysqli_fetch_row($rslt_campaign);
  $label_id = $row_campaign[0];

  $stmt_select = "SELECT * from screen_labels where label_id='$label_id';";
  $rslt_rs = mysqli_query($conn, $stmt_select);
//echo $stmt_select; exit;
  $x = 1;
  while ($row = mysqli_fetch_assoc($rslt_rs)) {
    if ($row["label_first_name"] != "") {
      $label_first_name = $row["label_first_name"];
    } else {
      $label_first_name = "HIDE";
    }
    if ($row["label_middle_name"] != "") {
      $label_middle_name = $row["label_middle_name"];
    } else {
      $label_middle_name = "HIDE";
    }
    if ($row["label_last_name"] != "") {
      $label_last_name = $row["label_last_name"];
    } else {
      $label_last_name = "HIDE";
    }
    if ($row["label_address1"] != "") {
      $label_address1 = $row["label_address1"];
    } else {
      $label_address1 = "HIDE";
    }
    if ($row["label_address2"] != "") {
      $label_address2 = $row["label_address2"];
    } else {
      $label_address2 = "HIDE";
    }
    if ($row["label_address3"] != "") {
      $label_address3 = $row["label_address3"];
    } else {
      $label_address3 = "HIDE";
    }
    if ($row["label_city"] != "") {
      $label_city = $row["label_city"];
    } else {
      $label_city = "HIDE";
    }
    if ($row["label_state"] != "") {
      $label_state = $row["label_state"];
    } else {
      $label_state = "HIDE";
    }
    if ($row["label_province"] != "") {
      $label_province = $row["label_province"];
    } else {
      $label_province = "HIDE";
    }
    if ($row["label_gender"] != "") {
      $label_gender = $row["label_gender"];
    } else {
      $label_gender = "HIDE";
    }
    if ($row["label_phone_number"] != "") {
      $label_phone_number = $row["label_phone_number"];
    } else {
      $label_phone_number = "HIDE";
    }
    if ($row["label_phone_code"] != "") {
      $label_phone_code = $row["label_phone_code"];
    } else {
      $label_phone_code = "HIDE";
    }
    if ($row["label_alt_phone1"] != "") {
      $label_alt_phone1 = $row["label_alt_phone1"];
    } else {
      $label_alt_phone1 = "HIDE";
    }
    if ($row["label_alt_phone2"] != "") {
      $label_alt_phone2 = $row["label_alt_phone2"];
    } else {
      $label_alt_phone2 = "HIDE";
    }
    if ($row["label_alt_phone3"] != "") {
      $label_alt_phone3 = $row["label_alt_phone3"];
    } else {
      $label_alt_phone3 = "HIDE";
    }
    if ($row["label_alt_phone4"] != "") {
      $label_alt_phone4 = $row["label_alt_phone4"];
    } else {
      $label_alt_phone4 = "HIDE";
    }
    if ($row["label_alt_phone5"] != "") {
      $label_alt_phone5 = $row["label_alt_phone5"];
    } else {
      $label_alt_phone5 = "HIDE";
    }
    if ($row["label_alt_phone6"] != "") {
      $label_alt_phone6 = $row["label_alt_phone6"];
    } else {
      $label_alt_phone6 = "HIDE";
    }
    if ($row["label_alt_phone7"] != "") {
      $label_alt_phone7 = $row["label_alt_phone7"];
    } else {
      $label_alt_phone7 = "HIDE";
    }
    if ($row["label_alt_phone8"] != "") {
      $label_alt_phone8 = $row["label_alt_phone8"];
    } else {
      $label_alt_phone8 = "HIDE";
    }
    if ($row["label_comments"] != "") {
      $label_comments = $row["label_comments"];
    } else {
      $label_comments = "HIDE";
    }
    if ($row["label_field1"] != "") {
      $label_field1 = $row["label_field1"];
    } else {
      $label_field1 = "HIDE";
    }
    if ($row["label_field2"] != "") {
      $label_field2 = $row["label_field2"];
    } else {
      $label_field2 = "HIDE";
    }
    if ($row["label_field3"] != "") {
      $label_field3 = $row["label_field3"];
    } else {
      $label_field3 = "HIDE";
    }
    if ($row["label_field4"] != "") {
      $label_field4 = $row["label_field4"];
    } else {
      $label_field4 = "HIDE";
    }
    if ($row["label_field5"] != "") {
      $label_field5 = $row["label_field5"];
    } else {
      $label_field5 = "HIDE";
    }
    if ($row["label_field6"] != "") {
      $label_field6 = $row["label_field6"];
    } else {
      $label_field6 = "HIDE";
    }
    if ($row["label_field7"] != "") {
      $label_field7 = $row["label_field7"];
    } else {
      $label_field7 = "HIDE";
    }
    if ($row["label_field8"] != "") {
      $label_field8 = $row["label_field8"];
    } else {
      $label_field8 = "HIDE";
    }
    if ($row["label_field9"] != "") {
      $label_field9 = $row["label_field9"];
    } else {
      $label_field9 = "HIDE";
    }
    if ($row["label_field10"] != "") {
      $label_field10 = $row["label_field10"];
    } else {
      $label_field10 = "HIDE";
    }
    if ($row["label_field11"] != "") {
      $label_field11 = $row["label_field11"];
    } else {
      $label_field11 = "HIDE";
    }
    if ($row["label_field12"] != "") {
      $label_field12 = $row["label_field12"];
    } else {
      $label_field12 = "HIDE";
    }
    if ($row["label_field13"] != "") {
      $label_field13 = $row["label_field13"];
    } else {
      $label_field13 = "HIDE";
    }
    if ($row["label_field14"] != "") {
      $label_field14 = $row["label_field14"];
    } else {
      $label_field14 = "HIDE";
    }
    if ($row["label_field15"] != "") {
      $label_field15 = $row["label_field15"];
    } else {
      $label_field15 = "HIDE";
    }
    if ($row["label_field16"] != "") {
      $label_field16 = $row["label_field16"];
    } else {
      $label_field16 = "HIDE";
    }
    if ($row["label_field17"] != "") {
      $label_field17 = $row["label_field17"];
    } else {
      $label_field17 = "HIDE";
    }
    if ($row["label_field18"] != "") {
      $label_field18 = $row["label_field18"];
    } else {
      $label_field18 = "HIDE";
    }
    if ($row["label_field19"] != "") {
      $label_field19 = $row["label_field19"];
    } else {
      $label_field19 = "HIDE";
    }
    if ($row["label_field20"] != "") {
      $label_field20 = $row["label_field20"];
    } else {
      $label_field20 = "HIDE";
    }
    if ($row["label_field21"] != "") {
      $label_field21 = $row["label_field21"];
    } else {
      $label_field21 = "HIDE";
    }
    if ($row["label_field22"] != "") {
      $label_field22 = $row["label_field22"];
    } else {
      $label_field22 = "HIDE";
    }
    if ($row["label_field23"] != "") {
      $label_field23 = $row["label_field23"];
    } else {
      $label_field23 = "HIDE";
    }
    if ($row["label_field24"] != "") {
      $label_field24 = $row["label_field24"];
    } else {
      $label_field24 = "HIDE";
    }
    if ($row["label_field25"] != "") {
      $label_field25 = $row["label_field25"];
    } else {
      $label_field25 = "HIDE";
    }
    if ($row["label_field26"] != "") {
      $label_field26 = $row["label_field26"];
    } else {
      $label_field26 = "HIDE";
    }
    if ($row["label_field27"] != "") {
      $label_field27 = $row["label_field27"];
    } else {
      $label_field27 = "HIDE";
    }
    if ($row["label_field28"] != "") {
      $label_field28 = $row["label_field28"];
    } else {
      $label_field28 = "HIDE";
    }
  }
  $fields = array("$label_first_name", "$label_middle_name", "$label_last_name", "$label_address1", "$label_address2", "$label_address3", "$label_city", "$label_state", "$label_province", "$label_gender", "$label_phone_number", "$label_phone_code", "$label_alt_phone1", "$label_alt_phone2", "$label_alt_phone3", "$label_alt_phone4", "$label_alt_phone5", "$label_alt_phone6", "$label_alt_phone7", "$label_alt_phone8", "$label_comments", "$label_field1", "$label_field2", "$label_field3", "$label_field4", "$label_field5", "$label_field6", "$label_field7", "$label_field8", "$label_field9", "$label_field10", "$label_field11", "$label_field12", "$label_field13", "$label_field14", "$label_field15", "$label_field16", "$label_field17", "$label_field18", "$label_field19", "$label_field20", "$label_field21", "$label_field22", "$label_field23", "$label_field24", "$label_field25", "$label_field26", "$label_field27", "$label_field28", "Agent","Haloo_List");

  $excelData = implode("\t", array_values($fields)) . "\n";


  $stmt_select = "SELECT * from haloo_list where list_id ='$id' and campaign='$campaign' ";
  $rslt_rs = mysqli_query($conn, $stmt_select);
//echo $stmt_select; exit;
  $x = 1;
  while ($row_list = mysqli_fetch_assoc($rslt_rs)) {
    $list = array(
      $row_list["label_first_name"], $row_list["label_middle_name"], $row_list["label_last_name"], $row_list["label_address1"], $row_list["label_address2"], $row_list["label_address3"], $row_list["label_city"], $row_list["label_state"], $row_list["label_province"], $row_list["label_gender"], $row_list["label_phone_number"], $row_list["label_phone_code"], $row_list["label_alt_phone1"], $row_list["label_alt_phone2"], $row_list["label_alt_phone3"], $row_list["label_alt_phone4"], $row_list["label_alt_phone5"], $row_list["label_alt_phone6"], $row_list["label_alt_phone7"], $row_list["label_alt_phone8"], $row_list["label_comments"], $row_list["label_field1"], $row_list["label_field2"], $row_list["label_field3"], $row_list["label_field4"], $row_list["label_field5"], $row_list["label_field6"], $row_list["label_field7"], $row_list["label_field8"], $row_list["label_field9"], $row_list["label_field10"], $row_list["label_field11"], $row_list["label_field12"], $row_list["label_field13"], $row_list["label_field14"], $row_list["label_field15"], $row_list["label_field16"], $row_list["label_field17"], $row_list["label_field18"], $row_list["label_field19"], $row_list["label_field20"], $row_list["label_field21"], $row_list["label_field22"], $row_list["label_field23"], $row_list["label_field24"], $row_list["label_field25"], $row_list["label_field26"], $row_list["label_field27"], $row_list["label_field28"], $row_list["list_agent"],$row_list["list_id"]
    );
    //array_walk($list, 'filterData');

    $excelData .= implode("\t", array_values($list)) . "\n";
  }




  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"$fileName\"");

  echo "$excelData";
  exit;
}

if ($_POST['value_stop']) {

  $stop_list = $_POST['stop_list'];

  $stmt_update = "update haloo_list set status='stop' where list_id ='$stop_list' ";
  $rslt_update = mysqli_query($conn, $stmt_update);

  $stmt_update1 = "update haloo_list set list_status='Inactive' where list_id ='$stop_list'";
  $rslt_update1 = mysqli_query($conn, $stmt_update1);

    $stmt_dial = "delete from dialable_lead  where list_id ='$stop_list'";
  $rslt_dial = mysqli_query($conn, $stmt_dial);

 /* $stmt_dial = "update dialable_lead set status='7' where list_id ='$stop_list'";
  $rslt_dial = mysqli_query($conn, $stmt_dial);*/
}

if ($_POST['value_start']) {

  $start_list = $_POST['start_list'];

  $stmt_update_new = "update haloo_list set status='NEW' where list_id ='$start_list' and status='stop' ";
  $rslt_update_new = mysqli_query($conn, $stmt_update_new);

  $stmt_update1 = "update haloo_list set list_status='Active' where list_id ='$start_list'";
  $rslt_update1 = mysqli_query($conn, $stmt_update1);

  $stmt_dial = "update dialable_lead set status='1' where list_id ='$start_list'";
  $rslt_dial = mysqli_query($conn, $stmt_dial);
/*
  $stmt_select = "SELECT * from haloo_list where list_id ='$start_list'";
  $rslt_rs = mysqli_query($conn, $stmt_select);
  $x = 1;
  while ($row_start = mysqli_fetch_assoc($rslt_rs)) {
    $remote_agent = $row_start["remote_agent"];

    exec("/usr/bin/curl -k '$incoming_api/API/backAPI/voice_blast.php?extension=$remote_agent'");

    $query1 = "select extension from user_live where status='READY' AND callstatus='IDLE'";
    $rslt_rs = mysqli_query($conn, $query1);

   if(mysqli_num_rows($rslt_rs)>0) {
      $agent = $row['extension'];

      exec("/usr/bin/curl -k '$incoming_api/API/backAPI/checklistAPI.php?extension=$agent&param=7'");
    }
  } */
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">

  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="../../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="../../plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="../../plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">


  <script src="offline_js/jquery.min.js"></script>
  <script src="offline_js/popper.min.js"></script>
  <script src="offline_js/bootstrap.min.js"></script>

  <style>
    thead th,
    table.dataTable thead td {
      padding: 15px 18px !important;
      border-bottom: 1px solid #dee2e6 !important;
    }

    table.dataTable.no-footer {
      border-bottom: 1px solid #dee2e6 !important;
    }

    .dataTables_wrapper .dataTables_info {
      clear: both;
      float: left;
      padding: 10px !important;
      font-size: smaller !important;
    }

    .dataTables_wrapper .dataTables_paginate {
      float: right;
      text-align: right;
      padding: 10px !important;
      font-size: smaller !important;
    }

    .dataTables_wrapper .dataTables_length {
      float: left;
      padding: 10px !important;
      font-size: smaller !important;
    }

    .dataTables_wrapper .dataTables_filter {
      float: right;
      text-align: right;
      padding: 10px !important;
      font-size: smaller !important;
    }

    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid #dee2e6 !important;
    }

    .dt-button-collection {
      margin-top: 2.5px !important;
      margin-bottom: 5px !important;
    }

    .custom-dropdown {
      width: 100%;
      border: 1px solid #d8bebe;
      position: relative;
      margin-bottom: 43px;
      border-radius: 13px;
    }

    .dropdown-header {
      background-color: #f1f1f1;
      padding: 5px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .choose-audio-type {
      font-weight: bold;
    }

    .play-button {
      background-color: #007BFF;
      color: #fff;
      border: none;
      padding: 5px 10px;
      cursor: pointer;
      width: 26%;
      margin-bottom: 3px;
    }

    .play-button:hover {
      background-color: #0056b3;
    }


    .dropdown-item {
      display: flex;
      justify-content: space-around;
      align-items: center;
      padding: 5px;
      border-bottom: 1px solid #ccc;
      cursor: pointer;
    }

    .dropdown-list {
      max-height: 200px;
      overflow-y: auto;
      display: none;
    }

    .dropdown-list.open {
      display: block;
    }

    .resizable-textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
      resize: vertical;
      min-height: 100px;
    }

    #main_div_convertButton {
      background-color: #007bff;
      color: white;
      margin: auto;
      width: 32%;
      border-style: none;
      border-radius: 19px;
      padding: 7px;
    }
  </style>
  <script>
    $(document).ready(function() {
      sampleDiv_zoom.style.zoom = '80%';
      var scale = 'scale(1)';
      document.body.style.webkitTransform = scale;
      document.body.style.msTransform = scale;
      document.body.style.transform = scale;
    });
  </script>
</head>

<body class="hold-transition sidebar-mini" id="sampleDiv_zoom">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="../../index.php" class="nav-link">Home</a>
        </li>

      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item" style="display:none;">
          <a class="nav-link" data-widget="navbar-search" href="#" role="button">
            <i class="fas fa-search"></i>
          </a>
          <div class="navbar-search-block">
            <form class="form-inline">
              <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                  <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>

        <!-- Messages Dropdown Menu -->

        <!-- Notifications Dropdown Menu -->
<li>
	  <div style="color:white;margin-left:10px;"><img src="img/hexa.png" alt="Admin Logo" class="brand-image" width ="220px" height="65px"></div>
	  </li>

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../logout.php" title="Signout" role="button"><i class="fas fa-sign-out-alt" style="color:red;"></i></a>
        </li>
        <li class="nav-item" style="display:none;">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #1A2B6D; color:white;">
      <!-- Brand Logo -->
      <a href="../../index.php" class="brand-link" style="background-color: #1A2B6D; color:white;">
        <img src="../../dist/img/logo.png" alt="Admin Logo" class="brand-image" style="opacity: .8">
        <span class="brand-text font-weight-light" style="color:#1A2B6D;">Upload List</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="../../dist/img/voip.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block" style="color:white;"><?php echo $loggedInuserName; ?></a>
          </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline" style="display:none;">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  Dashboard
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="../../index.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Overview</p>
                  </a>
                </li>


              </ul>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-edit"></i>
                <p>
                  Administration
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
 <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="Campaign.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Campaign</p>
                </a>
              </li>
			   <li class="nav-item">
                <a href="Ingroup.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ingroup</p>
                </a>
              </li>

			  <?php if($incoming_did == 1){ ?>
			   <li class="nav-item">
                <a href="did.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>DID</p>
                </a>
              </li>
			  <?php } ?>
			  <?php if($incoming_ivr == 1){ ?>
			  <li class="nav-item">
                <a href="ivr_list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>IVR</p>
                </a>
              </li>
			  <?php } ?>
			   <li class="nav-item">
                <a href="User.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>User</p>
                </a>
              </li>

			  <li class="nav-item">
                <a href="voice_logger.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Voice Recordings</p>
                </a>
              </li>
			  <li class="nav-item">
                <a href="show_screen_label.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Screen Labels</p>
                </a>
              </li>
			 <li class="nav-item">
                <a href="List.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Upload List</p>
                </a>
              </li>

			    <li class="nav-item">
                <a href="report.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Report</p>
                </a>
              </li>
			  <?php if($announcement == 1){ ?>
			  <li class="nav-item">
                <a href="addBanner.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Announcement</p>
                </a>
              </li>
			  <?php } ?>
			  <?php if($star_month == 1){ ?>
			  <li class="nav-item">
                <a href="Add_Star_Month.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Star Of the Month</p>
                </a>
              </li>
			   <?php } ?>
			  <li class="nav-item">
                <a href="pause_code.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pause Codes</p>
                </a>
              </li>
			  <li class="nav-item">
                <a href="UserStats.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>User Stats</p>
                </a>
              </li>
			   <?php if($dependant_dropdown == 1){ ?>
      <li class="nav-item">
                <a href="add_dependent_dropdowns.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Dependent Dropdowns</p>
                </a>
              </li>
			  <?php } ?>
			  <?php if($phone_book == 1){ ?>
      <li class="nav-item">
                <a href="phone_book.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Phone Book</p>
                </a>
              </li>
			  <?php } ?>
        </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>UPLOAD LIST</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                <li class="breadcrumb-item active">UPLOAD LIST</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->

		<div class="col-3 d-flex justify-content-end">
				<button id="something_reload" class="btn btn-primary px-3" style="margin-right: -1071px;">Refresh</button>
			</div>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="card-footer">
            <?php if ($loggedInUserLevel == "SuperAdmin") { ?>
              <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Upload New List</button>&nbsp;&nbsp;
			  <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#myModal_exist">Upload Existing List</button>&nbsp;&nbsp;
            <?php } ?>

            <span class="btn btn-success" style="color:white;" onclick="document.getElementById('myModal_edit').style.display='block'; listuploadedit()">
              Download Format
            </span>&nbsp;&nbsp;
	<span class="btn btn-danger" style="color:white;" onclick="document.getElementById('myModal_DNC').style.display='block'; listuploaddnc()">
              Upload DNC
            </span>&nbsp;&nbsp;
<span class="btn btn-warning" style="color:white;" onclick="document.getElementById('myModal_DDNC').style.display='block'; listdownloaddnc()">
              Download DNC
            </span>&nbsp;&nbsp;
<span class="btn btn-dark" style="color:white;" onclick="document.getElementById('remove_DNC').style.display='block'; listremovednc()">
              Remove DNC
            </span>
			&nbsp;&nbsp;
<!--<span class="btn btn-success"><a href="all_list_data.php" style="color:white;">All List Data</a></span>-->
			</div>

			 <div class="card-header">
			<form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
			 <div class="form-group" style="display: inline-block;width: 15%;vertical-align: top;margin-right:7px;">
					<label for="UserID">From Date:</label><input type="date" class="form-control" id="fromdate" name="fromdate" >
					</div>
					<div class="form-group" style="display: inline-block;width: 15%;vertical-align: top;margin-right:7px;">
				    <label for="UserID">To Date:</label><input type="date" class="form-control" id="todate" name="todate" >
					</div>


					<div class="form-group" style="display: inline-block;width: 10%;vertical-align: top;margin-right:7px;margin-top:30px;">
					<input type="submit" class="form-control" name="btn_campaign" value="Filter" style="background-color: #3CB371;color: #ffff;">
					</div>


			</form>
					</div>


            <?php if ($loggedInUserLevel == "SuperAdmin") { ?>
              <!--<span class="btn btn-danger" style="color:white;" onclick="bulkdelete()">Bulk Delete</span>-->
            <?php } ?>
          </div>

          <!-- SELECT2 EXAMPLE -->
          <div class="card card-default">
            <div class="card-header">
              <h3 class="card-title">UPLOADED LIST LISTINGS</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
                <button type="button" style="display:none;" class="btn btn-tool" data-card-widget="remove">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <div class="row">
                <div class="col-md-12">
                  <div class="card-body p-0">
                    <table class="table table-striped projects table-bordered" id="table_camp">
                      <thead>
                        <tr>
                          <!--   <th>
                        Check
                      </th>-->
                          <th>
                            Sr.No.
                          </th>
                          <th>
                            List Id
                          </th>
                          <th>
                            List Name
                          </th>
                          <th>
                            List Uploaded Date
                          </th>
                          <th>
                            Campaign
                          </th>
						  <th>
                            List Status
                          </th>
                          <th>
                            Status
                          </th>
						  <!--<th>
                            Count
                          </th>-->
                          <?php if ($loggedInUserLevel == "SuperAdmin") { ?>
                            <th>
                              Action
                            </th>
                          <?php } ?>
                          <th>
                            Stop/Resume
                          </th>
                          <th>List info</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php


						if($_POST['btn_campaign']){
							//echo "in side filter ";

	                     $start_date = $_POST['fromdate'];
				         $end_date = $_POST['todate'];

						 $stmt_select = "SELECT list_id,list_name,created_date,campaign FROM ( SELECT list_id,list_name,created_date,campaign,ROW_NUMBER() OVER (PARTITION BY list_id ORDER BY created_date DESC) as rn FROM haloo_list WHERE user_group='$user_group' AND list_id != ''  and `created_date` >='$start_date 00:00:00' and `created_date` <='$end_date 23:59:59') subquery WHERE rn = 1
                           ORDER BY created_date DESC;";
					    //echo $stmt_select;
						//die;
						}else{
                      //  $stmt_select = "SELECT DISTINCT list_id,list_name,created_date,campaign from haloo_list where list_id !='' order by created_date DESC";
							  $date= date('Y-m-d');
							$stmt_select = "SELECT list_id,list_name,created_date,campaign FROM ( SELECT list_id,list_name,created_date,campaign,ROW_NUMBER() OVER (PARTITION BY list_id ORDER BY created_date DESC) as rn FROM haloo_list WHERE user_group='$user_group' AND list_id != ''  and `created_date` >='$date 00:00:00' and `created_date` <='$date 23:59:59') subquery WHERE rn = 1
							 ORDER BY created_date DESC;";
							 
							// echo $stmt_select;exit();
						}
							//echo $stmt_select;exit();
                        $rslt_rs = mysqli_query($conn, $stmt_select);
                        $x = 1;
                        while ($row = mysqli_fetch_assoc($rslt_rs)) {
                          $list_id = $row["list_id"];
                          $campaign = $row["campaign"];

                          $stmt_select1 = "SELECT list_status from haloo_list where list_id ='$list_id' order by lead_id limit 1";
                          $rslt_rs1 = mysqli_query($conn, $stmt_select1);
                          $row_list = mysqli_fetch_assoc($rslt_rs1);
                          $list_status = $row_list["list_status"];


						  $stmt_status="SELECT count(*) as list_count from haloo_list where status='NEW' AND list_id ='$list_id' AND campaign='$campaign'";
	                           $rslt_status= mysqli_query($conn,$stmt_status);
	                           $row_status= mysqli_fetch_row($rslt_status);
							   $Total_status = $row_status[0];
							   if($Total_status == "0"){
								   $listStatus = "DIALED";
							   }else{
								   $listStatus = "NEW";
							   }


							   // Query to get the total number of leads in the list
							$stmt_status = "SELECT count(*) as list_count
							FROM haloo_list
							WHERE list_id ='$list_id'";
							$rslt_status = mysqli_query($conn, $stmt_status);
							$row_status = mysqli_fetch_row($rslt_status);
							$Total_status = $row_status[0];

							// Query to get the total dialed lead count
							$stmt_dialed_count = "SELECT count(*) as list_count
								  FROM haloo_list
								  WHERE status IN('DIALED','ANSWERED','NA') AND list_id ='$list_id'";
							$rslt_dialed_count = mysqli_query($conn, $stmt_dialed_count);
							$row_dialed = mysqli_fetch_row($rslt_dialed_count);
							$Total_status_dialled = $row_dialed[0];


							// Calculate the remaining leads (Total - Dialed)
							$remaining_leads = $Total_status - $Total_status_dialled;

							// Ensure remaining leads are not negative
							$remaining_leads = max(0, $remaining_leads);



							?>

                          <tr>
                            <!--  <td><input type="checkbox" name="bulkList[]" value="<?php echo $row["list_id"]; ?>"></td>-->
                            <td>
                              <?php echo $x; ?>
                            </td>
                            <td>
                              <?php echo $row["list_id"]; ?>
                            </td>
                            <td>
                              <?php echo $row["list_name"]; ?>
                            </td>
                            <td>
                              <?php echo $row["created_date"]; ?>
                            </td>
                            <td>
                              <?php echo $row["campaign"]; ?>
                            </td>
							<td>
                             <?php echo $listStatus; ?>
                            </td>

                            <td>
							<?php
							if($list_status == "Active"){
							?>
       <span style="background-color:#00b33c;border:10px solid #00b33c;border-radius:20px;color:white;"><?php echo $list_status; ?></span>
	   <?php
							}else{
								?>
	<span style="background-color:#e60000;border:10px solid #e60000;border-radius:20px;color:white;"><?php echo $list_status; ?></span>
								<?php
							}
							?>
                            </td>
							<!--<td>
                             <?php //echo $Total_status; ?>
                            </td>-->
                            <?php if ($loggedInUserLevel == "SuperAdmin") { ?>
                              <td class="project-actions text-right">

                                <span class="btn btn-dark" onclick="document.getElementById('myModal_delete').style.display='block'; voiceBlastDeleteFun('<?php echo $row["list_id"]; ?>','<?php echo $row["list_name"]; ?>')">
                                  <i class="fas fa-trash">
                                  </i>
                                  Delete
                                </span><br><br>
                                <span>
                                  <form action="" method="POST">
                                    <input type="hidden" value="<?php echo $row["list_name"]; ?>" id="list_name" name="list_name">
                                    <input type="hidden" value="<?php echo $row["list_id"]; ?>" id="download_list" name="download_list">
                                    <input type="hidden" value="<?php echo $row["campaign"]; ?>" id="download_campaign" name="download_campaign">
                                    <input type="submit" class="btn btn-info" name="download" value="Download">

                                  </form>
                                </span>

                              </td>
                              <td class="project-actions text-right">
							  <?php if($list_status == "Active"){  ?>
                                <span id="stoplistBtn">
                                  <!--<form action="" method="POST">-->
                                    <input type="hidden" value="<?php echo $row["list_id"]; ?>" id="stop_list" name="stop_list">
                                    <input type="submit" class="btn btn-warning" onclick = "stopListByajax('<?php echo $row["list_id"]; ?>')" name="value_stop" value="Stop">
                                  <!--</form>-->
                                </span>
							  <?php } else{ ?>
                                <span id="startlistBtn">
                                  <!--<form action="" method="POST">-->
                                    <input type="hidden" value="<?php echo $row["list_id"]; ?>" id="start_list" name="start_list">
                                    <input type="submit" class="btn btn-secondary" onclick="startListByajax('<?php echo $row["list_id"]; ?>')" name="value_start" value="Resume">
                                  <!--</form>-->
                                </span>
                              <?php } ?>
                              </td>
                             <td>
							<?php echo "Total Lead Count: <b>" . $Total_status . "</b><br>";
							echo "Dialed Lead Count: <b>" . $Total_status_dialled . "</b><br>";
							echo "Dialable Lead Count: <b>" . $remaining_leads . "</b><br>";?>
							</td>
                            <?php } ?>

                          </tr>
                        <?php $x++;
                        } ?>


                      </tbody>
                    </table>
                  </div>
                  <!-- /.form-group -->
                </div>
                <!-- /.col -->

                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- /.row -->
            </div>
            <!-- /.card-body -->

          </div>
          <!-- /.card -->

          <!-- The Modal -->
          <div class="modal fade" id="myModal_edit" style="opacity: 3;top : 104px !important">
            <div class="modal-dialog">
              <div class="modal-content" style="top:40px;">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">DOWNLOAD UPLOAD LIST FORMAT</h4>
                  <button type="button" class="close1" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                      <div class="card-body">
                        <div class="form-group">
                          <label>Campaign</label>
                          <select class="form-control select2" style="width: 100%;" id="txt_campaign" name="txt_campaign">
                            <option value="">Select</option>
                            <?php
                            $stmt_select = "SELECT * from campaign where dial_method IN ('Predictive','Preview') and user_group='$user_group'";
                            $rslt_rs = mysqli_query($conn, $stmt_select);

                            $x = 1;
                            while ($row = mysqli_fetch_assoc($rslt_rs)) {
                            ?>

                              <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                            <?php } ?>
                          </select>
                        </div>


                      </div>



                    </div>

                    <!-- /.card-body -->


                    <div class="card-footer">
                      <input type="hidden" value="edit_Voiceblast" name="editVoiceblast">
                      <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>


<div class="modal fade" id="myModal_DNC" style="opacity: 3;top : 104px !important">
            <div class="modal-dialog">
              <div class="modal-content" style="top:40px;">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">UPLOAD DNC LIST</h4>
                  <button type="button" class="close1_dnc" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                      <!--  <div class="form-group">
                          <label>Campaign</label>
                          <select class="form-control select2" style="width: 100%;" id="dnc_campaign" name="dnc_campaign" required>
                            <option value="">Select</option>
                            <?php
                            $stmt_select = "SELECT * from campaign WHERE user_group='$user_group'";
                            $rslt_rs = mysqli_query($conn, $stmt_select);

                            $x = 1;
                            while ($row = mysqli_fetch_assoc($rslt_rs)) {
                            ?>

                              <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                            <?php } ?>
                          </select>
                        </div>-->

                    <div class="form-group">
                        <label for="CampaignName">Upload DNC List ( csv format )</label>
                        <input type="file" name="upload_dnc" id="upload_dnc" class="form-control" required />
                      </div>


                    </div>

                    <!-- /.card-body -->


                    <div class="card-footer">
                      <input type="hidden" value="dnc_upload" name="dnc_upload">
                      <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>


		  <div class="modal fade" id="myModal_DDNC" style="opacity: 3;top : 104px !important">
            <div class="modal-dialog">
              <div class="modal-content" style="top:40px;">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">DOWNLOAD DNC LIST</h4>
                  <button type="button" class="close1_ddnc" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                      <div class="card-body">
                        <div class="form-group">
                          <label>Campaign</label>
                          <select class="form-control select2" style="width: 100%;" id="dnc_dow_campaign" name="dnc_dow_campaign">
                            <option value="">Select</option>
                            <?php
                            $stmt_select = "SELECT * from campaign WHERE user_group='$user_group'";
                            $rslt_rs = mysqli_query($conn, $stmt_select);

                            $x = 1;
                            while ($row = mysqli_fetch_assoc($rslt_rs)) {
                            ?>

                              <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                            <?php } ?>
                          </select>
                        </div>


                      </div>



                    </div>

                    <!-- /.card-body -->


                    <div class="card-footer">
                      <input type="hidden" value="download_dnc" name="download_dnc">
                      <button type="submit" class="btn btn-primary">Download</button>
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>


<div class="modal fade" id="remove_DNC" style="opacity: 3;top : 104px !important">
            <div class="modal-dialog">
              <div class="modal-content" style="top:40px;">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">REMOVE DNC LIST</h4>
                  <button type="button" class="close1_remove" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="form-group">
                          <label>Campaign</label>
                      <select class="form-control select2" style="width: 100%;" id="remove_campaign" name="remove_campaign" required>
                            <option value="">Select</option>
                            <?php
                            $stmt_select = "SELECT * from campaign WHERE user_group='$user_group'";
                            $rslt_rs = mysqli_query($conn, $stmt_select);

                            $x = 1;
                            while ($row = mysqli_fetch_assoc($rslt_rs)) {
                            ?>

                              <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                            <?php } ?>
                          </select>
                        </div>

                    <div class="form-group">
                        <label for="CampaignName">Upload DNC List ( csv format )</label>
                        <input type="file" name="remove_dnc" id="remove_dnc" class="form-control" required />
                      </div>


                    </div>

                    <!-- /.card-body -->


                    <div class="card-footer">
                      <input type="hidden" value="dnc_remove" name="dnc_remove">
                      <button type="submit" class="btn btn-primary">Remove</button>
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- The Modal Edit -->
          <div class="modal fade" id="myModal">
            <div class="modal-dialog" style="overflow: scroll; height:600px;">
              <div class="modal-content scrollit">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">ADD A LIST</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                      <div class="form-group">
                        <label for="CampaignID">List ID<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" pattern="^[a-zA-Z0-9\s]*$" title="Please enter only numbers and characters" id="list_id" name="list_id" placeholder="Enter List Id" required>
                      </div>
                      <div class="form-group">
                        <label for="CampaignID">List Name<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" pattern="^[a-zA-Z0-9\s]*$" title="Please enter only numbers and characters" id="list_name" name="list_name" placeholder="Enter List Name" required>
                      </div>
                      <div class="form-group">
                        <label for="CampaignID">List Assign To<span style="color:red;">*</span></label>
                        <select class="form-control select2" style="width: 100%;" id="list_assign" name="list_assign" onchange="listAssign()" required>
                          <option value="">Select</option>
						   <?php
				  if($predictive_dialing == 1){
				  ?>
                          <option value="Predictive">Predictive Dialing</option>
						  <?php } ?>
						   <?php
				  if($userwise_dialing == 1){
				  ?>
                          <option value="Userwise">Userwise Dialing</option>
						    <?php } ?>
						   <?php
				  if($voice_blast == 1){
				  ?>
                          <option value="VB">Voice Blast</option>
						  <?php } ?>
						  <option value="Preview">Preview Dialing</option>
                        </select>
                      </div>
                      <div class="form-group" id="campaign_display" style="display:none;">
                        <label>Campaign</label>
                        <select class="form-control select2" style="width: 100%;" id="campaign" name="campaign">
                          <option value="">Select</option>
                          <?php
                          $stmt_select = "SELECT * from campaign where dial_method IN('Predictive','Blended Predictive','Preview') and user_group='$user_group'";
                          $rslt_rs = mysqli_query($conn, $stmt_select);

                          $x = 1;
                          while ($row = mysqli_fetch_assoc($rslt_rs)) {
                          ?>

                            <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                          <?php } ?>
                        </select>
                      </div>
					  
					   <div class="form-group" id="preview_display" style="display:none;">
                        <label>Campaign</label>
                        <select class="form-control select2" style="width: 100%;" id="preview_campaign" name="preview_campaign">
                          <option value="">Select</option>
                          <?php
                          $stmt_select = "SELECT * from campaign where dial_method IN('Preview') and user_group='$user_group'";
                          $rslt_rs = mysqli_query($conn, $stmt_select);

                          $x = 1;
                          while ($row = mysqli_fetch_assoc($rslt_rs)) {
                          ?>

                            <option value="<?php echo $row["campaign_id"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                          <?php } ?>
                        </select>
                      </div>
					  
                      <div class="form-group" id="user_display" style="display:none;">
                        <label>Voice Blast Campaign</label>
                        <select class="form-control select2" style="width: 100%;" id="voiceBlast" name="voiceBlast" onchange="campaignChange()">
                          <option value="">Select</option>
                          <?php
                          $stmt_select = "SELECT * from campaign where dial_method='VoiceBlast' and user_group='$user_group'";
                          $rslt_rs = mysqli_query($conn, $stmt_select);

                          $x = 1;
                          while ($row = mysqli_fetch_assoc($rslt_rs)) {
                          ?>

                            <option value="<?php echo $row["campaign_name"]; ?>"><?php echo $row["campaign_name"]; ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="form-group" id="remote_display" style="display:none;">
                        <label>Remote Agents</label>
                        <select class="form-control select2" style="width: 100%;" id="remote_agent" name="remote_agent">
                          <option value="">Select</option>

                        </select>
                      </div>

                      <div class="form-group" id="nocall_display" style="display:none;">
                        <label>Maximum Channels</label>
                        <select class="form-control select2" style="width: 100%;" id="no_dial" name="no_dial">
                          <option value="">Select</option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                          <option value="4">4</option>
                          <option value="5">5</option>
                          <option value="6">6</option>
                          <option value="7">7</option>
                          <option value="8">8</option>
                          <option value="9">9</option>
                          <option value="10">10</option>
                          <option value="11">11</option>
                          <option value="12">12</option>
                          <option value="13">13</option>
                          <option value="14">14</option>
                          <option value="15">15</option>
                          <option value="16">16</option>
                          <option value="17">17</option>
                          <option value="18">18</option>
                          <option value="19">19</option>
                          <option value="20">20</option>
                          <option value="21">21</option>
                          <option value="22">22</option>
                          <option value="23">23</option>
                          <option value="24">24</option>
                          <option value="25">25</option>
                          <option value="26">26</option>
                          <option value="27">27</option>
                          <option value="28">28</option>
                          <option value="29">29</option>
                          <option value="30">30</option>
                          <option value="31">31</option>
                          <option value="32">32</option>
                          <option value="33">33</option>
                          <option value="34">34</option>
                          <option value="35">35</option>
                          <option value="36">36</option>
                          <option value="37">37</option>
                          <option value="38">38</option>
                          <option value="39">39</option>
                          <option value="40">40</option>
                          <option value="41">41</option>
                          <option value="42">42</option>
                          <option value="43">43</option>
                          <option value="44">44</option>
                          <option value="45">45</option>
                          <option value="46">46</option>
                          <option value="47">47</option>
                          <option value="48">48</option>
                          <option value="49">49</option>
                          <option value="50">50</option>
                          <option value="51">51</option>
                          <option value="52">52</option>
                          <option value="53">53</option>
                          <option value="54">54</option>
                          <option value="55">55</option>
                          <option value="56">56</option>
                          <option value="57">57</option>
                          <option value="58">58</option>
                          <option value="59">59</option>
                          <option value="60">60</option>
                          <option value="61">61</option>
                          <option value="62">62</option>
                          <option value="63">63</option>
                          <option value="64">64</option>
                          <option value="65">65</option>
                          <option value="66">66</option>
                          <option value="67">67</option>
                          <option value="68">68</option>
                          <option value="69">69</option>
                          <option value="70">70</option>
                          <option value="71">71</option>
                          <option value="72">72</option>
                          <option value="73">73</option>
                          <option value="74">74</option>
                          <option value="75">75</option>
                          <option value="76">76</option>
                          <option value="77">77</option>
                          <option value="78">78</option>
                          <option value="79">79</option>
                          <option value="80">80</option>
                          <option value="81">81</option>
                          <option value="82">82</option>
                          <option value="83">83</option>
                          <option value="84">84</option>
                          <option value="85">85</option>
                          <option value="86">86</option>
                          <option value="87">87</option>
                          <option value="88">88</option>
                          <option value="89">89</option>
                          <option value="90">90</option>
                          <option value="91">91</option>
                          <option value="92">92</option>
                          <option value="93">93</option>
                          <option value="94">94</option>
                          <option value="95">95</option>
                          <option value="96">96</option>
                          <option value="97">97</option>
                          <option value="98">98</option>
                          <option value="99">99</option>
                          <option value="100">100</option>

                        </select>
                      </div>

                      <div class="form-group" id="attempt_display" style="display:none;">
                        <label for="CampaignID">No Of Attempts</label>
                        <select class="form-control select2" style="width: 100%;" id="no_attempts" name="no_attempts">
                          <option value="">Select</option>
                          <option value="1">1</option>
                          <option value="2">2</option>
                          <option value="3">3</option>
                        </select>
                      </div>

                      <div class="form-group" id="timeGap_display" style="display:none;">
                        <label for="CampaignID">Time Gap(min)</label>
                        <select class="form-control select2" style="width: 100%;" id="time_gap" name="time_gap">
                          <option value="">Select</option>
                          <option value="10">10</option>
                          <option value="15">15</option>
                          <option value="30">30</option>
                        </select>
                      </div>

                      <div class="form-group" id="blast_display" style="display:none;">
                        <label for="CampaignID">Voice Blast To</label>
                        <select class="form-control select2" style="width: 100%;" id="blast_assign" name="blast_assign" onchange="blastAssign()">
                          <option value="">Select</option>
                          <option value="Audio">Audio</option>
                          <option value="IVR">IVR</option>
                          <option value="tts">Text to Speech</option>
                        </select>
                      </div>

                      <div class="form-group" id="ivr_diaplay" style="display:none;">
                        <label for="CampaignName">Assign IVR</label>
                        <select class="form-control select2" style="width: 100%;" id="ivr_assign" name="ivr_assign">
                          <option value="">Select</option>
                          <?php
                          $stmt_select = "SELECT DISTINCT ivr_name from ivr_flow WHERE user_group='$user_group'";
                          $rslt_rs = mysqli_query($conn, $stmt_select);

                          $x = 1;
                          while ($row = mysqli_fetch_assoc($rslt_rs)) {

                          ?>
                            <option value="<?php echo $row["ivr_name"]; ?>"><?php echo $row["ivr_name"]; ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <div class="form-group" id="tts-display" style="display: none;">
                        <label for="CampaignName">Choose audio type for Text to Speech</label>
                        <div class="custom-dropdown">
                          <div class="dropdown-header" id="dropdownHeader">
                            <span style="    margin: auto;color: black;font-weight: 600;">Choose Audio Type</span>
                            <div class="dropdown-icon">▼</div>
                          </div>
                          <div class="dropdown-list" id="voiceDropdown">
                          </div>
                        </div>
                        <audio controls id="audioPlayer" style="margin-left: 11%;">
              Your browser does not support the audio element.
            </audio>
            <input type="hidden" name="newFilePath_audio" id="newFilePath_audio" value="">

                        <textarea id="main_tts_inputtext" class="resizable-textarea" placeholder="Enter text to convert to audio..." rows="4" cols="40" style="display: block; margin-top: 3%; margin-bottom: 5%;"></textarea>
                        <button id="main_div_convertButton" aria-label="Convert Text to Audio" style="display: block;">Convert</button>
                      </div>

                      <div class="form-group" id="voice_diaplay" style="display:none;">
                        <label for="CampaignName">Voice File Upload ( mp3 format )</label>
                        <input type="file" name="uploadvoice" id="uploadvoice" class="form-control" />
                      </div>

                      <div class="form-group">
                        <label for="CampaignName">Upload List ( csv format )<span style="color:red;">*</span></label>
                        <input type="file" name="uploadfile" id="uploadfile" class="form-control"  />
                      </div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <input type="submit" name="addlist" value="Submit" class="btn btn-primary">
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>
          <!-- /.row -->

<!-- The Modal Edit -->
          <div class="modal fade" id="myModal_exist">
            <div class="modal-dialog" style="overflow: scroll; height:350px;">
              <div class="modal-content scrollit">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">ADD EXISTING LIST</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
              <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="card-body">

                      <div class="form-group">
                        <label>Upload List ( CSV Format )<span style="color:red;">*</span></label>
                        <input type="file" name="uploadfile1" id="uploadfile1" class="form-control" required />
                      </div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
					  <input type="hidden" value="updatelist" name="updatelist">
                    <!--<button type="submit" class="btn btn-primary">Upload</button>-->
					   <input type="submit" name="updatelist_existing" value="Submit" class="btn btn-primary">
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>
          <!-- /.row -->

          <!-- The Modal Delete-->
          <div class="modal fade" id="myModal_delete" style="opacity: 3;top : 104px !important">
            <div class="modal-dialog">
              <div class="modal-content" style="top:40px;">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">DELETE UPLOADED LIST</h4>
                  <button type="button" class="close23" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="" method="POST">
                    <div class="card-body">
                      <div class="form-group">
                        <label for="UserID">List Name</label>
                        <input type="text" class="form-control" pattern="^[a-zA-Z0-9\s]*$" title="Please enter only numbers and characters" id="txt_deletelistId" name="txt_deletelistId" readonly>
                      </div>

                    </div>
                    <input type="hidden" value="" id="deleteVoiceblast" name="deleteVoiceblast">
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <input type="hidden" value="delete_Voiceblast" name="deleteVoiceblasting">
                      <input type="submit" class="btn btn-primary">
                    </div>
                  </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                </div>

              </div>
            </div>
          </div>



        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <div class="float-right d-none d-sm-block">
        <b>Version</b> 6.0
      </div>
      <strong>Copyright &copy; Haloocom Technologies All rights reserved.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->


  <!-- Bootstrap 4 -->
  <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Select2 -->
  <script src="../../plugins/select2/js/select2.full.min.js"></script>
  <!-- Bootstrap4 Duallistbox -->
  <script src="../../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
  <!-- InputMask -->
  <script src="../../plugins/moment/moment.min.js"></script>

  <!-- date-range-picker -->
  <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap color picker -->
  <script src="../../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Bootstrap Switch -->
  <script src="../../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
  <!-- BS-Stepper -->
  <script src="../../plugins/bs-stepper/js/bs-stepper.min.js"></script>
  <!-- dropzonejs -->
  <script src="../../plugins/dropzone/min/dropzone.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <!--<script src="../../dist/js/demo.js"></script> -->
  <!-- Page specific script -->
  <!--<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">-->

  <script src="offline_js/jquery-3.5.1.js"></script>
  <script src="offline_js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="offline_js/jquery.dataTables.min.css">


  <script>

  $(document).ready(function () {
			$('#something_reload').click(function() {
			location.reload();
			alert("Lead Updated");
		});
	});

  function stopListByajax (stop_listId){
	  //alert('inside stop');
	  //var stop_listId1 = document.getElementById('stop_list').value;
	  //alert("functionList"+stop_listId);
	  //alert("hiddenListId"+stop_listId1);

      var user = "<?php echo $loggedInuserName; ?>";
      var user_group = "<?php echo $user_group; ?>";
      $.ajax({
        url: 'ajax/list_log.php',
        type: 'POST',
        data: {
          'action' : 'Stop',
          'user' : user,
          'user_group' : user_group,
          'list_id' : stop_listId
      },
        success: function(response){
          console.log(response);
        }
      });


	  // Send the checked values via AJAX
			$.ajax({
				url: 'ajax/stopListByajax.php', // Replace with your server URL
				type: 'POST',
				data: { stop_listId: stop_listId},
				success: function(response){
					console.log(response);
					if(response == "list updated for stop"){
						// Handle the response from the server
						alert('List has been stop');
            location.reload();

						//document.getElementById("startlistBtn").style.display = "block";
						//document.getElementById("stoplistBtn").style.display = "none";
					}
					else {
						//document.getElementById("stoplistBtn").style.display = "block";
					}

				}

			});
  }

  function startListByajax (start_listId){
	  //alert('inside Start Resume');
	  //var start_listId1 = document.getElementById('start_list').value;
	  //alert("function"+start_listId);
	  //alert("hidden"+start_listId1);

      var user = "<?php echo $loggedInuserName; ?>";
      var user_group = "<?php echo $user_group; ?>";
      $.ajax({
        url: 'ajax/list_log.php',
        type: 'POST',
        data: {
          'action' : 'Start',
          'user' : user,
          'user_group' : user_group,
          'list_id' : start_listId
      },
        success: function(response){
          console.log(response);
        }
      });


	  // Send the checked values via AJAX
			$.ajax({
				url: 'ajax/startListByajax.php', // Replace with your server URL
				type: 'POST',
				data: {start_listId: start_listId},
				success: function(response){
					console.log(response);
					if(response == "list updated for restart"){
						// Handle the response from the server
						alert('List has been restart');
            location.reload();

						//document.getElementById("startlistBtn").style.display = "none";
					}
					else {
						//document.getElementById("startlistBtn").style.display = "block";
					}

				}

			});
  }
    document.addEventListener('DOMContentLoaded', async () => {
      const voiceDropdown = document.getElementById('voiceDropdown');
      const dropdownHeader = document.getElementById('dropdownHeader');

      let isOpen = false;

      dropdownHeader.addEventListener('click', () => {
        isOpen = !isOpen;
        voiceDropdown.classList.toggle('open', isOpen);

        if (isOpen && voiceDropdown.children.length === 0) {
          loadVoices();
        }
      });

      async function loadVoices() {
        try {
          const response = await fetch('https://api.elevenlabs.io/v1/voices', {
            method: 'GET',
            headers: {
              'accept': 'application/json',
              'xi-api-key': '8540f423a0e16397b4c1e07425d34fce',
            },
          });

          if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
          }

          const data = await response.json();

          data.voices.sort((a, b) => a.name.localeCompare(b.name));

          voiceDropdown.innerHTML = '';

          const chooseAudioTypeItem = document.createElement('div');
          chooseAudioTypeItem.classList.add('dropdown-item', 'choose-audio-type');
          chooseAudioTypeItem.addEventListener('click', (e) => {
            e.stopPropagation();
          });

          voiceDropdown.appendChild(chooseAudioTypeItem);

          data.voices.forEach(voice => {
            const dropdownItem = document.createElement('div');
            dropdownItem.classList.add('dropdown-item');

            const name = document.createElement('span');
            name.textContent = voice.name;

            const playButton = document.createElement('button');
            playButton.classList.add('play-button');
            playButton.textContent = 'Play';

            playButton.addEventListener('click', (e) => {
              e.stopPropagation();
              if (voice.preview_url) {
                const audioPlayer = new Audio(voice.preview_url);
                audioPlayer.play();
              }
            });

            dropdownItem.addEventListener('click', () => {
              dropdownHeader.querySelector('span').textContent = voice.name;
              isOpen = false;
              voiceDropdown.classList.remove('open');
              localStorage.setItem('selectedVoiceId', voice.voice_id);
              // alert(`Voice ID: ${voice.voice_id}`);
            });

            dropdownItem.appendChild(name);
            dropdownItem.appendChild(playButton);
            voiceDropdown.appendChild(dropdownItem);
          });
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      }
    });

    const main_level_convert_button = document.getElementById("main_div_convertButton");
    main_level_convert_button.onclick = function () {
    const levelTextInput = document.getElementById("main_tts_inputtext");
    main_level_convert_button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Converting';
    const selectedVoiceId = localStorage.getItem("selectedVoiceId");
    const audioPlayer = document.getElementById("audioPlayer");
    const inputText = levelTextInput.value;
    console.log('Input Text:', inputText);
    console.log(selectedVoiceId);

    const formData = new FormData();
    formData.append('text', inputText);
    formData.append('voice_id', selectedVoiceId);
    fetch('audioblast_tts.php', {
      method: 'POST',
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        // const updated_main_tts_url = "VoiceBlast/" + data.filename;
        main_level_convert_button.textContent = 'Convert';
        audioPlayer.src = data.filename;
        document.getElementById("newFilePath_audio").value = data.filename;
      })
      .catch(error => console.error(error));

  };

    function listAssign() {
      var assign_name = document.getElementById("list_assign").value;
	 // alert(assign_name);
      if (assign_name == "Predictive" || assign_name == "Userwise") {
        document.getElementById("user_display").style.display = "none";
        document.getElementById("remote_display").style.display = "none";
        document.getElementById("nocall_display").style.display = "none";
        document.getElementById("attempt_display").style.display = "none";
        document.getElementById("timeGap_display").style.display = "none";
        document.getElementById("blast_display").style.display = "none";
		 document.getElementById("preview_display").style.display = "none";
        document.getElementById("voice_diaplay").style.display = "none";
        document.getElementById("ivr_diaplay").style.display = "none";
        document.getElementById("campaign_display").style.display = "block";
      }else if (assign_name == "Preview") {
        document.getElementById("user_display").style.display = "none";
        document.getElementById("remote_display").style.display = "none";
        document.getElementById("nocall_display").style.display = "none";
        document.getElementById("attempt_display").style.display = "none";
        document.getElementById("timeGap_display").style.display = "none";
        document.getElementById("blast_display").style.display = "none";
        document.getElementById("voice_diaplay").style.display = "none";
        document.getElementById("ivr_diaplay").style.display = "none";
		document.getElementById("campaign_display").style.display = "none";
        document.getElementById("preview_display").style.display = "block";
      }else if (assign_name == "VB") {
        document.getElementById("campaign_display").style.display = "none";
		 document.getElementById("preview_display").style.display = "none";
        document.getElementById("user_display").style.display = "block";
        document.getElementById("remote_display").style.display = "block";
        document.getElementById("nocall_display").style.display = "block";
        document.getElementById("attempt_display").style.display = "block";
        document.getElementById("timeGap_display").style.display = "block";
        document.getElementById("blast_display").style.display = "block";

      } else {
        document.getElementById("campaign_display").style.display = "none";
        document.getElementById("user_display").style.display = "none";
        document.getElementById("remote_display").style.display = "none";
        document.getElementById("nocall_display").style.display = "none";
        document.getElementById("attempt_display").style.display = "none";
        document.getElementById("timeGap_display").style.display = "none";
        document.getElementById("blast_display").style.display = "none";
        document.getElementById("voice_diaplay").style.display = "none";
        document.getElementById("ivr_diaplay").style.display = "none";

      }
    }

    function blastAssign() {
      var blast_name = document.getElementById("blast_assign").value;
      if (blast_name == "Audio") {
        document.getElementById("ivr_diaplay").style.display = "none";
        document.getElementById("voice_diaplay").style.display = "block";
        document.getElementById("tts-display").style.display = "none";
      } else if (blast_name == "IVR") {
        document.getElementById("voice_diaplay").style.display = "none";
        document.getElementById("ivr_diaplay").style.display = "block";
        document.getElementById("tts-display").style.display = "none";
      } else {
        document.getElementById("tts-display").style.display = "block";
        document.getElementById("ivr_diaplay").style.display = "none";
        document.getElementById("voice_diaplay").style.display = "none";
      }
    }

    function campaignChange() {
      var campaign = document.getElementById("voiceBlast").value;
      //alert(campaign);
      const xhttp = new XMLHttpRequest();
      xhttp.onload = function() {
        var result = this.responseText;

        document.getElementById('remote_agent').innerHTML = result;
        //console.log(result);

      }
      xhttp.open("GET", "ajax/remoteAgents.php?campaign=" + campaign, true);
      xhttp.send();
    }
  </script>

  <script>
    $(function() {

      $('#table_camp').dataTable();
      //Initialize Select2 Elements
      /*$('.select2').select2()

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })*/

      //Datemask dd/mm/yyyy
      $('#datemask').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
      })
      //Datemask2 mm/dd/yyyy
      $('#datemask2').inputmask('mm/dd/yyyy', {
        'placeholder': 'mm/dd/yyyy'
      })
      //Money Euro
      $('[data-mask]').inputmask()

      //Date picker
      $('#reservationdate').datetimepicker({
        format: 'L'
      });

      //Date and time picker
      $('#reservationdatetime').datetimepicker({
        icons: {
          time: 'far fa-clock'
        }
      });

      //Date range picker
      $('#reservation').daterangepicker()
      //Date range picker with time picker
      $('#reservationtime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
          format: 'MM/DD/YYYY hh:mm A'
        }
      })
      //Date range as a button
      $('#daterange-btn').daterangepicker({
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function(start, end) {
          $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
        }
      )

      //Timepicker
      $('#timepicker').datetimepicker({
        format: 'LT'
      })

      //Bootstrap Duallistbox
      $('.duallistbox').bootstrapDualListbox()

      //Colorpicker
      $('.my-colorpicker1').colorpicker()
      //color picker with addon
      $('.my-colorpicker2').colorpicker()

      $('.my-colorpicker2').on('colorpickerChange', function(event) {
        $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
      })

      $("input[data-bootstrap-switch]").each(function() {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
      })

    })
    // BS-Stepper Init
    document.addEventListener('DOMContentLoaded', function() {
      window.stepper = new Stepper(document.querySelector('.bs-stepper'))
    })

    // DropzoneJS Demo Code Start
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#template")
    previewNode.id = ""
    var previewTemplate = previewNode.parentNode.innerHTML
    previewNode.parentNode.removeChild(previewNode)

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
      url: "/target-url", // Set the url
      thumbnailWidth: 80,
      thumbnailHeight: 80,
      parallelUploads: 20,
      previewTemplate: previewTemplate,
      autoQueue: false, // Make sure the files aren't queued until manually added
      previewsContainer: "#previews", // Define the container to display the previews
      clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
    })

    myDropzone.on("addedfile", function(file) {
      // Hookup the start button
      file.previewElement.querySelector(".start").onclick = function() {
        myDropzone.enqueueFile(file)
      }
    })

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function(progress) {
      document.querySelector("#total-progress .progress-bar").style.width = progress + "%"
    })

    myDropzone.on("sending", function(file) {
      // Show the total progress bar when upload starts
      document.querySelector("#total-progress").style.opacity = "1"
      // And disable the start button
      file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
    })

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function(progress) {
      document.querySelector("#total-progress").style.opacity = "0"
    })

    // Setup the buttons for all transfers
    // The "add files" button doesn't need to be setup because the config
    // `clickable` has already been specified.
    document.querySelector("#actions .start").onclick = function() {
      myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
    }
    document.querySelector("#actions .cancel").onclick = function() {
      myDropzone.removeAllFiles(true)
    }
    // DropzoneJS Demo Code End





    function listuploadedit() {

      // Get the modal
      var modal1 = document.getElementById('myModal_edit');
      // Get the button that opens the modal
      var btn1 = document.getElementById("myBtngrpedit");
      // Get the <span> element that closes the modal
      var span1 = document.getElementsByClassName("close1")[0];
      // When the user clicks the edit button, open the modal
      modal1.style.display = "block";
      // When the user clicks on <span> (x), close the modal
      span1.onclick = function() {

        modal1.style.display = "none";
      }
      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal1) {
          modal1.style.display = "none";
        }
      }

    }



	 function listuploaddnc() {

      // Get the modal
      var modal1 = document.getElementById('myModal_DNC');
      // Get the button that opens the modal
      var btn1 = document.getElementById("myBtngrpedit");
      // Get the <span> element that closes the modal
      var span1 = document.getElementsByClassName("close1_dnc")[0];
      // When the user clicks the edit button, open the modal
      modal1.style.display = "block";
      // When the user clicks on <span> (x), close the modal
      span1.onclick = function() {

        modal1.style.display = "none";
      }
      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal1) {
          modal1.style.display = "none";
        }
      }

    }


	function listdownloaddnc() {

      // Get the modal
      var modal1 = document.getElementById('myModal_DDNC');
      // Get the button that opens the modal
      var btn1 = document.getElementById("myBtngrpedit");
      // Get the <span> element that closes the modal
      var span1 = document.getElementsByClassName("close1_ddnc")[0];
      // When the user clicks the edit button, open the modal
      modal1.style.display = "block";
      // When the user clicks on <span> (x), close the modal
      span1.onclick = function() {

        modal1.style.display = "none";
      }
      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal1) {
          modal1.style.display = "none";
        }
      }

    }




	function listremovednc() {

      // Get the modal
      var modal1 = document.getElementById('remove_DNC');
      // Get the button that opens the modal
      var btn1 = document.getElementById("myBtngrpedit");
      // Get the <span> element that closes the modal
      var span1 = document.getElementsByClassName("close1_remove")[0];
      // When the user clicks the edit button, open the modal
      modal1.style.display = "block";
      // When the user clicks on <span> (x), close the modal
      span1.onclick = function() {

        modal1.style.display = "none";
      }
      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal1) {
          modal1.style.display = "none";
        }
      }

    }






    function voiceBlastDeleteFun(val_grpd, CampaignIDVal) {
      document.getElementById('deleteVoiceblast').value = val_grpd;
      document.getElementById('txt_deletelistId').value = CampaignIDVal;

      // Get the modal
      var modal2 = document.getElementById('myModal_delete');

      // Get the button that opens the modal
      var btn2 = document.getElementById("myBtn2");

      // Get the <span> element that closes the modal
      var span2 = document.getElementsByClassName("close23")[0];

      // When the user clicks the edit button, open the modal
      modal2.style.display = "block";

      // When the user clicks on <span> (x), close the modal
      span2.onclick = function() {
        modal2.style.display = "none";
      }
      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal2) {
          modal2.style.display = "none";
        }
      }


    }




    /*
  function bulkdelete(){

var checkboxes = document.getElementsByName('bulkList[]');
            var path = "";
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    path += "'" + checkboxes[i].value
                        + "'" + ",";
                }
            }


$.ajax({
			type: 'POST',
			url: "bulklist_delete.php",
			data: {
				'path': path
			},
			success: function(result) {

				//alert(result);
location.reload();
	}
		});
 //window.location.reload();
}
*/
  </script>
</body>

</html>
