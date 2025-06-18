<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
$loggedInuserName = $_SESSION['AgentUserName'];
$loggedInPassword = $_SESSION['AgentPassword'];
$extension        = $_SESSION['extension'];
$campaign        = $_SESSION['campaign'];
$Conf_Number = $_SESSION['conf_num'];


if ($loggedInuserName == '' && $loggedInPassword == '') {
	header('Location:/haloocomConnect5/pages/Agent/connect5Login/index.php');

}

require_once("db_connect.php");


$stmt_select="SELECT * from campaign where campaign_name='$campaign'";
	      $rslt_rs= mysqli_query($conn,$stmt_select);

			   if(mysqli_num_rows($rslt_rs)>=1){
			   while($row = mysqli_fetch_assoc($rslt_rs)) {
				  $dial_method = $row["dial_method"];
				  $feedback_ivr = $row["feedback_ivr"];
                  $user_group = $row["user_group"];
			   }
		   }
/*
if($user_group=='Smartcoin Sales')
{
$wrapup_sec = 0;
$cam_stmt = "select wrapup_sec from campaign where campaign_id='$campaign' and wrapup_sec!=0;";
$cam_rslt = mysqli_query($conn,$cam_stmt);
$cam_rows = mysqli_fetch_assoc($cam_rslt);
$wrapup_sec = $cam_rows['wrapup_sec'];
$wrapup_sec = $wrapup_sec."000";
}
*/

$stmt_select_fe="SELECT * from features_settings";
	      $rslt_res= mysqli_query($conn,$stmt_select_fe);

			  if(mysqli_num_rows($rslt_res)>=1){
			   while($row_fe = mysqli_fetch_assoc($rslt_res)) {
				  $phone_book = $row_fe["phone_book"];
			   }
			  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
	<link rel="icon" type="image/png" href="./assets/img/haloo_new.png">
	<title>
		Welcome to Agent Page
	</title>
	<!--     Fonts and icons     -->
	<link href="css/custom.css" rel="stylesheet" />
	<!-- Nucleo Icons -->
	<link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
	<link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
	<!-- Font Awesome Icons -->
	<script src="css/font_awsome.js" crossorigin="anonymous"></script>
	<link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
	<!-- CSS Files -->
	<link id="pagestyle" href="./assets/css/argon-dashboard.css?v=2.0.2" rel="stylesheet" />

	<link href="./assets/css/custom1.css" rel="stylesheet" />
	<script type="text/javascript" src="css/jquery.js"></script>
	<link rel="stylesheet" href="css/data_table.css">
	<script type="text/javascript" src="css/data_table.js"></script>

<link rel="stylesheet" href="css/w3.css">
	<link href="css/api.css" rel="stylesheet">
	<link href="css/font_awsome_min.css" rel="stylesheet">
 <link rel="stylesheet" href="css/main.css">


	<script>
		$(document).ready(function() {
			$('#example').DataTable();
			$('#callBackDatatable').DataTable();
			$('#inboundTABLEDATA').DataTable();
	        $('#outboundTABLEDATA').DataTable();
			$('#dropTABLEDATA').DataTable();
			$('#queueTABLEDATA').DataTable();


		});

function Auto_Voicefile_Play() {
   var user = '<?php echo $loggedInuserName; ?>';
    $.ajax({
      type: 'POST',
      url: "ajax/autoplay_wrapup.php",
      data: {
        'user':user
      },
      success: function(result) {
        if(result=="N"){
            //alert("Inside If " + result);
            var audio = new Audio('call_hangup.mp3');
            audio.play(); // Play the audio
        }else {
          //alert("Inside Else " + result);
        }
      }
    });
  }


function Auto_Hangup_Wrapup() {
   var user = '<?php echo $loggedInuserName; ?>';
   var wrapup_sec = '<?php echo $wrapup_sec; ?>';
    $.ajax({
      type: 'POST',
      url: "ajax/autoplay_wrapup.php",
      data: {
        'user':user
      },
      success: function(result) {
        if(result=="N"){
            //alert("Inside If " + result);
              timeoutId = setTimeout(AutoDispose, wrapup_sec);
        }else {
          //alert("Inside Else " + result);
        }
      }
    });
  }


	</script>






	<style>
	
	/* WEBRTC POPUP */	
	.content {
      position: fixed; /* Ensure it's above all other content */
      top: 65%;
      left: 85%;
      transform: translate(-50%, -50%);
      max-height: 0;
      overflow: hidden;
      background-color: #1A2B6D;
      border-radius: 5px;
      transition: max-height 0.5s ease, padding 0.5s ease;
      z-index: 1000; /* Ensure it's above other elements */
      width: 340px;
      padding: 0;
    }

    .content.visible {
      max-height: 400px;
	  width: 340px;
      padding: 10px;
    }

    .hidden {
      max-height: 0;
	  width: 340px;
      padding: 0;
    }
/* WEBRTC POPUP */	


		.header {
			padding: 6px;
			// text-align: center;
			background: #1A2B6D;
			color: white;
			font-size: 10px;

		}

		.top-container {
			background-color: #ffff;
			padding: 30px;
			text-align: center;
		}

		.header1 {
			padding: 10px 10px;
			background: #ffff;
			color: #f1f1f1;
		}

		.content1 {
			padding: 16px;
		}

		.sticky {
			position: fixed !important;
			top: 0 !important;
			width: 100%;
		}

		.sticky+.content1 {
			padding-top: 102px;
		}



		/* Latest compiled and minified CSS included as External Resource*/

		/* Optional theme */
		@import url('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css');


		.btn-space {
			margin-right: 0.5rem;
		}

		textarea::placeholder {
			font-size: 13px;
		}

		/*.switch1 {
			position: relative;
			display: inline-block;
			width: 60px;
			height: 14px;
		} */
		#check_display{
			position: relative;
			display: inline-block;
			width: 60px;
			height: 14px;

		}

		.switch1 input {
			opacity: 0;
			width: 0;
			height: 0;
		}

		.slider1 {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #ccc;
			-webkit-transition: .4s;
			transition: .4s;
		}

		.slider1:before {
			position: absolute;
			content: "";
			height: 26px;
			width: 26px;
			left: 4px;
			bottom: 4px;
			background-color: white;
			-webkit-transition: .4s;
			transition: .4s;
		}

		.inboundCheckup:checked+.slider1 {
			background-color: #2196F3;
		}

		.inboundCheckup:focus+.slider1 {
			box-shadow: 0 0 1px #2196F3;
		}

		.inboundCheckup:checked+.slider1:before {
			-webkit-transform: translateX(26px);
			-ms-transform: translateX(26px);
			transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider1.round1 {
			border-radius: 24px !important;
		}

		.round1 {
			border-radius: 24px !important;
		}

		.slider1.round1:before {
			border-radius: 100% !important;
			height: 20px;
			width: 20px;
			position: absolute;
			top: -4px;
			left: -6px;
			//background-color: green;
		}




		.chat-list {
			padding: 0;
			font-size: .8rem;
		}

		.chat-list li {
			margin-bottom: 10px;
			overflow: auto;
			color: #060606;
		}

		.chat-list .chat-img {
			float: left;
			width: 48px;
		}

		.chat-list .chat-img img {
			-webkit-border-radius: 50px;
			-moz-border-radius: 50px;
			border-radius: 50px;
			width: 100%;
		}

		.chat-list .chat-message {
			-webkit-border-radius: 50px;
			-moz-border-radius: 50px;
			border-radius: 50px;
			background: #CCFFFF;
			display: inline-block;
			padding: 10px 20px;
			position: relative;
		}

		.chat-list .chat-message:before {
			content: "";
			position: absolute;
			top: 15px;
			width: 0;
			height: 0;
		}

		.chat-list .chat-message h5 {
			margin: 0 0 5px 0;
			font-weight: 600;
			line-height: 100%;
			font-size: .9rem;
		}

		.chat-list .chat-message p {
			line-height: 18px;
			margin: 0;
			padding: 0;
		}

		.chat-list .chat-body {
			margin-left: 20px;
			float: left;
			width: 70%;
		}

		.chat-list .in .chat-message:before {
			left: -12px;
			border-bottom: 20px solid transparent;
			border-right: 20px solid #CCFFFF;
		}

		.chat-list .out .chat-img {
			float: right;
		}

		.chat-list .out .chat-body {
			float: right;
			margin-right: 20px;
			text-align: right;
		}

		.chat-list .out .chat-message {
			background: #F0FFF0;
		}

		.chat-list .out .chat-message:before {
			right: -12px;
			border-bottom: 20px solid transparent;
			border-left: 20px solid #F0FFF0;
		}

		.card .card-header:first-child {
			-webkit-border-radius: 0.3rem 0.3rem 0 0;
			-moz-border-radius: 0.3rem 0.3rem 0 0;
			border-radius: 0.3rem 0.3rem 0 0;
		}

		.card .card-header {
			//  background: #17202b;
			border: 0;
			font-size: 1rem;
			padding: .65rem 1rem;
			position: relative;
			font-weight: 600;
			color: #ffffff;
		}

		.content {
			margin-top: 40px;
		}

		#example_wrapper {
			font-size: 12px;
		}



		.rowData12 {
			margin: 0 auto;
			width: 280px;
			clear: both;
			text-align: center;
			font-family: 'Exo';
			margin-left: 45px;
			background-color: red;
		}

		.digit123,
		.dig {
			float: left;
			padding: 10px 30px;
			width: 30px;
			font-size: 20px;
			cursor: pointer;
		}

		.sub {
			font-size: 0.8rem;
			color: grey;
		}

		.container123 {
			background-color: white;
			width: 310px;
			padding: 20px;
			margin: 30px auto;
			height: 450px;
			text-align: center;
			box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
			border-radius: 47px;
		}

		#output {
			font-family: "Exo";
			font-size: 26px;
			height: 60px;
			font-weight: bold;
			color: #1A2B6D;
		}

		#call {
			display: inline-block;
			background-color: #6eba01;
			padding: 11px;
			border-radius: 20px !important;
			margin: 10px;
			color: white;
			border-radius: 4px;
			box-shadow: 5px 10px #888888;
			cursor: pointer;
			margin-left: -62px;
			width: 50px;
		}

		.botrowData12 {
			margin: 0 auto;
			width: 280px;
			clear: both;
			text-align: center;
			font-family: 'Exo';

		}

		.digit123:active,
		.dig:active {
			background-color: #e6e6e6;
		}

		#call:hover {
			background-color: #81c784;
		}

		.dig {
			float: left;
			padding: 10px 20px;
			margin: 10px;
			width: 30px;
			cursor: pointer;
		}

		#output:focus {
			outline: none;
		}

		#output::placeholder {
			font-size: 13px;
		}

		.dropbtn {
			background-color: #04AA6D;
			color: white;
			//  padding: 16px;
			font-size: 14px;
			border: none;
		}

		.dropdown {
			position: relative;
			display: inline-block;
		}

		.dropdown-content {
			display: none;
			position: absolute;
			background-color: #f1f1f1;
			min-width: 160px;
			box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
			z-index: 1;
			border-radius: 20px;
		}

		.dropdown-content a {
			color: black;
			padding: 12px 16px;
			text-decoration: none;
			display: block;
		}

		.dropdown-content a:hover {
			background-color: #ddd;
		}

		.dropdown:hover .dropdown-content {
			display: block;
		}

		.dropdown:hover .dropbtn {
			background-color: #3e8e41;
		}


		.container {
			margin-top: 200px;
			margin-left: 150px;
		}

		.f-group {
			float: left;
		}

		.f-group label {
			display: block;
			margin-bottom: 8px;
			font-family: 500;
		}

		select {
			border: 1px solid #555;
			height: 40px;
			width: 200px;
			padding: 0 15px;
			margin-right: 20px;
		}

        /* Styling for the popup */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
			z-index: 2;
        }
        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }

.scrolling-text-container {
    overflow: hidden;
    position: relative;
    width: 100%;
}

.scrolling-text {
    position: absolute;
    white-space: nowrap;
    animation: scrollText 10s linear infinite;
}

@keyframes scrollText {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(-100%);
    }
}
	</style>
	<link rel="stylesheet" type="text/css" href="css/all_min.css">
	<style type="text/css">
		.phone-arrow {
			transform: rotate(45deg) translateY(-30%);
		}
	</style>
</head>

<body class="g-sidenav-show   bg-gray-100" style="font-family: Open Sans;">

	<div class="header" id="mainheader">
		<div><img src="./assets/img/haloo_new.png" width="100px" height="45px">&nbsp;&nbsp;<b></b>
            
			<span style="float:right;margin-right: 420px;background-color: #ffff;color: #060606;padding: 9px;border-radius: 5px;font-weight: 600;height:48px;">
			<select class="form-control" name ="update_campaign" id ="update_campaign" onchange="campaignChange()" style="margin:-4px;">
						<option value="">Select Campaign &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
						
						<?php			
				$stmt_select="SELECT * from campaign where user_group='$user_group'";
	                  $rslt_rs= mysqli_query($conn,$stmt_select);
	                 
					 $x = 1;		
			   while($row = mysqli_fetch_assoc($rslt_rs)) {
								 ?>
								
									<option value = "<?php echo $row["campaign_name"]; ?>" ><?php echo $row["campaign_name"]; ?></option> 
							 <?php } ?>
					  </select>
			</span>
			
			<span style="float:right;margin-right: -370px;background-color: #ffff;color: #060606;padding: 9px;border-radius: 5px;font-weight: 600;">
			<span id="agentStatus"></span>
			<span id="check_display" style="display:none;">
				<label class="switch1">
				<input type="checkbox" class="inboundCheckup" id="AgentStatusChecking" onclick="AgentStatusChecking()">
		       <span class="slider1 round1" style="padding: 7px;margin-top: 2px;height: -121px !important;width: 38px;"></span>
				</label>
				</span>

			</span>
			<span style="float:right;margin-right: -550px;background-color: #04AA6D;color: #060606;padding: 9px;border-radius: 5px;font-weight: 600;">
				<div class="dropdown">
					<button class="dropbtn">
						<i class="fa fa-user" aria-hidden="true"></i>
						<?php echo $loggedInuserName; ?>
					</button>
					<div class="dropdown-content">
						<a href="logout.php" style="position:absolute;background-color: red;color:#FFFFFF;border-radius:15px;text-align:center"><i class="fa fa-sign-out" aria-hidden="true"></i> &nbsp;&nbsp;Logout</a>

					</div>
				</div>

			</span>

		</div>

	</div>
	<div class="min-height-300 bg-primary position-absolute w-100"></div>
	<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main" style="font-size:150px !important;height:550px;">

		<div class="sidenav-header" style="margin-bottom:10px;">
	<a class="navbar-brand m-0" href="#" onclick="home()">
				<div style="color:white;"><img src="./assets/img/hexa.png" alt="Admin Logo" class="brand-image" width ="100px" height="40px" style="margin-bottom:10px;"></div>
			</a>

		</div>
		<hr class="horizontal dark mt-0">
		<div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main" style="height:430px;">
			<ul class="navbar-nav">

				<li class="nav-item" onclick="callLogData();">
					<a class="nav-link" href="#" id="callLogID">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-list-ol" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
						<span style="font-size:10px !important">CALL LOG</span>
					</a>
				</li>
				<li class="nav-item" onclick="callBAckData();">
					<a class="nav-link " href="#" id="callBackID">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-reply" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div><br>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">CALL BACK LIST</span>
					</a>
				</li>
				<li class="nav-item" onclick="dialpad();">
					<a class="nav-link " href="#" id="dialPadID">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<img src="assets/img/dialPad.png" alt="drop call" width="20" height="20" style="margin-top: -5px;">
						</div>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">DIAL PAD</span>
					</a>
				</li>
				<li class="nav-item" onclick="internalChat()">
					<a class="nav-link " href="#" id="internalChatID">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-weixin" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
	<span style="font-size:10px !important" class="nav-link-text1 ms1-1">INTERNAL CHAT</span><span id="ChatMsgCount" style="color:#F8F8FF;font-size:12px;background-color:#228B22;border:1px solid white;border-radius:5px;width:11px;height:18px; text-align: center;">0</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link " href="mycontacts.php" target="_blank">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-phone" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">MY CONTACTS</span>
					</a>
				</li>
				<li class="nav-item" onclick="breakpop();">
					<a class="nav-link " href="#" id="breakTime">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-clock-o" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">BREAK TIMINGS</span>
					</a>
				</li>
				<li class="nav-item">
				    <a class="nav-link " id="activeLeads">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-headphones-alt" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">ACTIVE LEADS</span>&nbsp;&nbsp;<span id="ActiveLeadCount" style="color:#F8F8FF;font-size:12px;background-color:#228B22;border:1px solid white;border-radius:5px;width:11px;height:18px; text-align: center;">0</span>
					</a>
				</li>
				<?php
				if($feedback_ivr == "Yes"){
					?>
				<li class="nav-item" onclick="feedback();">
					<a class="nav-link " href="#" id="breakTime">
						<div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
							<i class="fa fa-commenting-o" aria-hidden="true" style="color:#1A2B6D;font-size:15px"></i>
						</div>
						<span style="font-size:10px !important" class="nav-link-text1 ms1-1">FEEDBACK IVR</span>
					</a>
				</li>
				<?php
				}else{
				}
                ?>

			</ul>
		</div>

	</aside>
	<main class="main-content position-relative border-radius-lg " style="margin-left:173px;">
		<div class="header1" id="myHeader1">
			<div class="btn-group">
				<span  onclick="inboundInfo();" class="btn btn-warning btn-space" style="background-color:#cfebfd;border-radius: 10px; width:118px;height:65px; font-weight:600;padding:15px;">
					<span class="fa-stack fa-2x" style="font-size: 7px;color:green">
						<i class="fas fa-phone-alt fa-stack-2x"></i>
						<i class="fas fa-arrow-down fa-stack-1x phone-arrow"></i>
					</span>
					<span style="color:#000033">Inbound</span><br><span style="color:green" id="inboundCallCount">00</span></span>
				<span onclick="outboundInfo();" class="btn btn-success" style="background-color: #FFDAB9;border-radius: 10px; width:118px;height:65px;font-weight:600;padding:15px;">
					<span class="fa-stack fa-2x" style="font-size: 7px;color:orange">
						<i class="fas fa-phone-alt fa-stack-2x"></i>
						<i class="fas fa-arrow-up fa-stack-1x phone-arrow"></i>
					</span>
					<span style="color:#000033">Outbound</span> <span style="color:orange" id="outboundCallCount">00</span></span>&nbsp;&nbsp;
				<span onclick="dropCallInfo()" class="btn btn-warning btn-space" style="background-color:#FA8072;border-radius: 10px; width:118px;height:65px;font-weight:600;padding:15px;">
					<img src="assets/img/missed-call.png" alt="drop call" width="17" height="15" style="margin-top: -5px;color:red;">
					<span style="color:#000033">Drop calls</span> <span style="color:red" id="dropCallCount">00</span></span>
				<span class="btn btn-success" style="background-color:#F0F0F0;border-radius: 10px; width:118px;height:65px;font-weight:600;padding:15px;">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-alarm" viewBox="0 0 16 16" style="color:#000033">
						<path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5z" />
						<path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1h-3zm1.038 3.018a6.093 6.093 0 0 1 .924 0 6 6 0 1 1-.924 0zM0 3.5c0 .753.333 1.429.86 1.887A8.035 8.035 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5zM13.5 1c-.753 0-1.429.333-1.887.86a8.035 8.035 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1z" />
					</svg>
					<span style="color:#000033">Seconds</span><br><span style="color:#000033;" id="seconds_time">00:00:00</span></span>&nbsp;&nbsp;
				<span onclick="queueCallInfo()" class="btn btn-warning btn-space" style="background-color:#F0F0F0;border-radius: 10px; width:118px;height:65px;font-weight:600;padding:15px;">
					<img src="assets/img/queue.png" alt="drop call" width="17" height="15" style="margin-top: -5px;filter: brightness(1.5);">
					<span style="color:#000033" >Queue</span><br><span style="color:#000033;" id="queueCallCount">0</span></span>
				<span class="btn btn-success" style="background-color:#F0F0F0;border-radius: 10px;width:118px;height:65px;font-weight:600;padding:17px;">

					<span style="color:#000033"> <i class="fa fa-shield" aria-hidden="true"></i>AHT</span><br><span id="AHTCount" style="color:#000033">0</span></span>&nbsp;&nbsp;
				<span class="btn btn-warning btn-space" style="background-color:#F0F0F0;border-radius: 10px; width:124px;height:65px;font-weight:600;padding:15px;">
					<span style="color:#000033" ><i class="fa fa-clock-o" aria-hidden="true"></i> Login Hours</span><br><span style="color:#000033;" id="Lhour">00:00:00</span></span>
				<span class="btn btn-success" style="background-color:#F0F0F0;border-radius: 10px;width:118px;height:65px;font-weight:600;padding:15px;">

          <span style="color:#000033" ><i class="fa fa-clock-o" aria-hidden="true"></i>Talktime</span><br><span style="color:#000033;" id="TotalTalk">00:00:00</span></span>
        <span class="btn btn-success" style="background-color:#F0F0F0;border-radius: 10px;width:118px;height:65px;font-weight:600;padding:15px;">

			<span style="color:#000033" ><span style="color:#000033;" id="camps"></span></span><br>
					<span style="color:#000033;"><i class="fa fa-mobile" aria-hidden="true"></i> <?php echo $extension; ?></span></span>&nbsp;&nbsp;

					<span class="btn btn-warning btn-space" id="StatusCall" style="background-color:#F0F0F0;border-radius: 10px;color:red;font-weight:600;width:118px;height:65px;font-size:14px;padding:20px;">
						<span id="liveCallNotification" style="font-size: 18px;"><i class="fa fa-volume-control-phone" aria-hidden="true"></i></span>
						&nbsp;<b><span id="CallStatus">IDLE</span></b></span>&nbsp;&nbsp;

						<span class="btn btn-warning btn-space" id="webform" style="background-color:#F0F0F0;border-radius: 10px;color:green;font-weight:600;width:118px;height:65px;font-size:14px;">
						<span id="webformNotification" style="font-size: 18px;color:#000033;"><i class="fa fa-wpforms" aria-hidden="true"></i></span>
						&nbsp;<b><a href="webFormFAQ1.php" target="_blank"><span id="webformStatus1" style="color:#000033;">Webform</span></a></b></span>&nbsp;&nbsp;
			</div>
		</div>

		<!-- Navbar -->
		<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl content1" id="navbarBlur" data-scroll="false">

			<div class="container-fluid py-1 px-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5" style="display:none">
						<li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
						<li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
					</ol>
					<div class="row">
						<div class="col-16">
							<h6 class="font-weight-bolder text-white mb-0" style="margin-top: 7px;font-weight:600;border-radius:10px;">Call Controls</h6>
						</div>
						<div id="cust_hang" style="display:none;">
<h6 style="margin-top: 7px;font-weight:600;border-radius:10px;color: red;"><b style="color:red;">Call Hangup From Customer....</b></h6>
						</div>
					</div>
				</nav>
				<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
					<div class="ms-md-auto pe-md-3 d-flex align-items-center">
						<div class="input-group" style="display:none">
							<span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
							<input type="text" class="form-control" placeholder="Type here...">
						</div>
					</div>
					<ul class="navbar-nav  justify-content-end">

						<li class="nav-item d-xl-none ps-3 d-flex align-items-center">
							<a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
								<div class="sidenav-toggler-inner">
									<i class="sidenav-toggler-line bg-white"></i>
									<i class="sidenav-toggler-line bg-white"></i>
									<i class="sidenav-toggler-line bg-white"></i>
								</div>
							</a>
						</li>
						<li class="nav-item px-3 d-flex align-items-center" style="display:none">
							<a href="javascript:;" class="nav-link text-white p-0" style="display:none">
								<i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
							</a>
						</li>
						<li class="nav-item dropdown pe-2 d-flex align-items-center">


						</li>
					</ul>
				</div>
			</div>
		</nav>
		<!-- End Navbar -->
		<div class="container-fluid py-4" style="margin-top: -15px;">
			<div class="row">
				<div class="btn-group">

					<span class="btn btn-success" id="parkButtonID" style="background-color: #ffff;border-radius: 10px;color:#1A2B6D;font-weight:600;font-size:13px;width:56px" onclick="park();">
						<i class="fa fa-h-square" aria-hidden="true" style="font-size: 25px;"></i>
					<img src="assets/img/hold_con.jpg" alt="drop call" width="30" height="30" style="margin-top: -5px; display:none;">
						<br><span id="parkName">Hold</span></span>&nbsp;&nbsp;
					<span class="btn btn-warning btn-space" id="tranferButtonID" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:56px;font-weight:600;font-size:13px" onclick="transfer()">
						<span class="fa-stack fa-2x" style="font-size: 7px;display:none">
							<i class="fas fa-phone-alt fa-stack-2x"></i>
							<i class="fas fa-arrow-up fa-stack-1x phone-arrow" style="margin-top: -3px;transform: rotate(83deg) translateY(-30%);"></i>
						</span>&nbsp;
						<i class="fa fa-handshake-o" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="tranferName">Transfer</span></span>
					<span class="btn btn-warning btn-space" id="emailButtonID" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;font-weight:600;font-size:13px;width: 56px" onclick="email();">
						<i class="fa fa-envelope" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="emailName">Email</span></span>
						<span class="btn btn-success" id="whatsAppButtonID" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;font-weight:600;font-size:13px;width: 56px">
					<a href="#" style="text-decoration: none;">
						<i class="fa fa-whatsapp" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="whatsAppName">WhatsApp</span></a></span>&nbsp;&nbsp;
					<span class="btn btn-success" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:56px;font-weight:600;font-size:13px">
					<a href="#"  style="text-decoration: none;">
						<i class="fa fa-facebook-square" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">Facebook</span></a></span>&nbsp;&nbsp;
						<span class="btn btn-success" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:56px;font-weight:600;font-size:13px">
						<a href="#"  style="text-decoration: none;">
						<i class="fa fa-telegram" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">Telegram</span></a></span>&nbsp;&nbsp;
						<span class="btn btn-success" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:60px;font-weight:600;font-size:13px" onclick="goLiveCall()">
						<i class="fa fa-check-square" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">Add Contact</span></span>&nbsp;&nbsp;
						<?php
                     if($phone_book == 1){
						?>
						<span class="btn btn-success" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:60px;font-weight:600;font-size:13px" onclick="phoneBookClick()">
						<i class="fa fa-address-book-o" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">Phone Book</span></span>&nbsp;&nbsp;
						<?php
					 }
						?>
						<span class="btn btn-success" id="hang_up" style="background-color:#ffff;border-radius: 10px;color:red;width:56px;font-weight:600;font-size:13px;" onclick="callHangupFun('Answered','<?php echo $loggedInuserName; ?>')">
						<i class="fa fa-phone-square" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">Hangup</span></span>&nbsp;&nbsp;
						
					    <span class="btn btn-success" id="toggleButton" style="background-color:#ffff;border-radius: 10px;color:#1A2B6D;width:56px;font-weight:600;font-size:13px;">
						<i class="fa fa-headphones" aria-hidden="true" style="font-size: 25px;"></i>
						<br><span id="goLiveName">WEBRTC</span></span>&nbsp;&nbsp;


				</div>

			</div>



			<div id="holdID" style="display:none;background-color: #1A2B6D;padding: 5px;color: #ffff;text-align: center;border-radius:7px;width:1100px;margin-top:5px;">
				CALL ON HOLD
			</div>

			<div class="row mt-4" id="callBackInfo" style="display:none;background-color:white;padding: 20px;border-radius:5px;">
			<span onclick="CALLBACK_Close();" class="button" style="color:red;margin-left:1100px;font-size:20px;font-weight:bold;">&times;</span>
			<div id="CALLbackID" style="color: #1A2B6D;text-align: center;font-weight:bold;font-size:20px;">
			CALL BACKS
			</div>
				<div id="callback2Table" style="width:100%;">
			</div>
			</div>

			<div class="row mt-4" id="CallLogInfo" style="display:none;background-color:white;padding: 20px;border-radius:5px;">
			<span onclick="callLogClose();" class="button" style="color:red; margin-left:1100px;font-size:20px;font-weight:bold;">&times;</span>
			<div id="CALLlogID" style="color: #1A2B6D;text-align: center;font-weight:bold;font-size:20px;">
			CALL LOGS
			</div>
			<div id="clickLogTable" style="width:100%;">
			</div>

			</div>
			<div class="row mt-4" id="InActiveStatus" style="font-weight: 600;margin-left: 170px;font-size: 22px;">
				<span>
					<img alt="sad" src="assets/img/smile1.png" width="100px" height="90px" style="margin-left: 300px;" /><br>
					Session not active ,Please make yourself ready to receive calls</span>
			</div>

			<div class="row mt-4" id="pausebreak" style="font-weight: 1000;margin-left: 170px;font-size: 22px;display:none">
				<span id="pausecode" style="margin-left: 300px;color:red;"></span>
			</div>

<div id="toggleContent" class="content hidden"><br>
  <iframe 
    src="https://haloocom.shriramlife.me/testphone/Phone/?agent=<?php echo $extension; ?>" 
    width="320" 
    height="300" 
    frameborder="0" 
    allowfullscreen>
</iframe>
  </div>
  
			<div class="row mt-4">
				<div class="col-lg-15 mb-lg-0 mb-4" id="fielddData" style="display:none;">

				<div class="card z-index-2 h-100" style="padding: 20px;width:100%; border-radius:5px;">
						<table>
						<tr>
						<div class="form-group" style="text-align: center; font-weight: bold;"> CUSTOMER INFORMATION</div>
						</tr>
						</table>
							<form method='POST'>
							<?php

							$stmt_select1="SELECT screen_label from campaign where campaign_id='$campaign';";

	                  $rslt_rs1= mysqli_query($conn,$stmt_select1);

			while($row1 = mysqli_fetch_assoc($rslt_rs1)) {
				   $label = $row1["screen_label"];
			   }

				$stmt_select="SELECT * from screen_labels where label_id='$label';";

			     $rslt_rs= mysqli_query($conn,$stmt_select);

					 $x = 1;
			   while($row = mysqli_fetch_assoc($rslt_rs)) {
				   ?>

				   <?php
				   if($row["label_first_name"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field1"><?php echo $row["label_first_name"]; ?></label>
											<input type="text" class="form-control" id="first_name" name="first_name">
										</div>

					   	<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="first_name" name="first_name">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_middle_name"] != "")
				   {
					   ?>


									<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right: 7px;">
											<label for="field1"><?php echo $row["label_middle_name"]; ?></label>
											<input type="text" class="form-control" id="middle_name" name="middle_name">
										</div>




					   	<?php
				   }else{
					   ?>
					   <div class="form-group" style="display:none;">

											<input type="text" class="form-control" id="middle_name" name="middle_name">
										</div>
					<?php

				   }
				   ?>

				   <?php
				   if($row["label_last_name"] != "")
				   {
					   ?>


									<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field3"><?php echo $row["label_last_name"]; ?></label>
											<input type="text" class="form-control" id="last_name" name="last_name">
										</div>

					   	<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="last_name" name="last_name">
										</div>
					<?php

				   }
				   ?>

				   <?php
				   if($row["label_address1"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right: 7px;">
											<label for="field4"><?php echo $row["label_address1"]; ?></label>
											<input type="text" class="form-control" id="address1" name="address1">
										</div>


					   	<?php
				   }else{
					   ?>
					   <div class="form-group" style="display:none;">

											<input type="text" class="form-control" id="address1" name="address1">
										</div>
					<?php

				   }
				   ?>

				    <?php
				   if($row["label_address2"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field4"><?php echo $row["label_address2"]; ?></label>
											<input type="text" class="form-control" id="address2" name="address2">
										</div>

					   	<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="address2" name="address2">
										</div>
					<?php

				   }
				   ?>

				    <?php
				   if($row["label_address3"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field4"><?php echo $row["label_address3"]; ?></label>
											<input type="text" class="form-control" id="address3" name="address3">
										</div>

					   	<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="address3" name="address3">
										</div>
					<?php

				   }
				   ?>

				    <?php
				   if($row["label_city"] != "")
				   {
					   ?>
					  			<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field7"><?php echo $row["label_city"]; ?></label>
											<input type="text" class="form-control" id="label_city" name="label_city">
										</div>

				     	<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_city" name="label_city">
										</div>
					<?php

				   }
				   ?>


				 <?php
				   if($row["label_state"] != "")
				   {
					   ?>
				   				<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field8"><?php echo $row["label_state"]; ?></label>
											<input type="text" class="form-control" id="label_state" name="label_state">
										</div>

					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_state" name="label_state">
										</div>
					<?php

				   }
				   ?>

					<?php
				   if($row["label_province"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_province"]; ?></label>
											<input type="text" class="form-control" id="label_province" name="label_province">
										</div>

					<?php
				   }else{
					  ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_province" name="label_province">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_gender"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_gender"]; ?></label>
											<select class="form-control select2" class="form-control" id="label_gender" name="label_gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
											<option value="Female">Female</option>
											<option value="Others">Others</option>

                                          </select>
										</div>

					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_gender" name="label_gender">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_phone_number"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_phone_number"]; ?></label>
											<input type="number" class="form-control" pattern="[0-9]{10}" id="label_phone_number" name="label_phone_number" readonly>
										</div>


					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_phone_number" name="label_phone_number">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_phone_code"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_phone_code"]; ?></label>
											<input type="text" class="form-control" id="label_phone_code" name="label_phone_code">
										</div>


					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_phone_code" name="label_phone_code">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone1"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone1"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone1" name="alt_phone1">
										</div>


					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone1" name="alt_phone1">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone2"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone2"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone2" name="alt_phone2">
										</div>



					<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone2" name="alt_phone2">
										</div>
					<?php

				   }
				   ?>



				    <?php
				   if($row["label_alt_phone3"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_alt_phone3"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone3" name="alt_phone3">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone3" name="alt_phone3">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone4"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone4"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone4" name="alt_phone4">
										</div>
									</td>
									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone4" name="alt_phone4">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone5"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone5"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone5" name="alt_phone5">
										</div>


									<?php
				   }else{

					  ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone5" name="alt_phone5">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_alt_phone6"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_alt_phone6"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone6" name="alt_phone6">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone6" name="alt_phone6">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone7"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone7"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone7" name="alt_phone7">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone7" name="alt_phone7">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_alt_phone8"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_alt_phone8"]; ?></label>
											<input type="text" pattern="[0-9]{10}" class="form-control" id="alt_phone8" name="alt_phone8">
										</div>


									<?php
				   }else{

					  ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="alt_phone8" name="alt_phone8">
										</div>
					<?php
				   }
				   ?>

				   </tr>
				   <tr>

					 <?php
				   if($row["label_field1"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field1"]; ?></label>
											<input type="text" class="form-control" id="label_field1" name="label_field1">
										</div>

									<?php
				   }else{
					  ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field1" name="label_field1">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field2"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field2"]; ?></label>
											<input type="text" class="form-control" id="label_field2" name="label_field2">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field2" name="label_field2">
										</div>
					<?php

				   }
				   ?>

					 <?php
				   if($row["label_field3"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field3"]; ?></label>
											<input type="text" class="form-control" id="label_field3" name="label_field3">
										</div>


									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field3" name="label_field3">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field4"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field4"]; ?></label>
											<input type="text" class="form-control" id="label_field4" name="label_field4">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field4" name="label_field4">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field5"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field5"]; ?></label>
											<input type="text" class="form-control" id="label_field5" name="label_field5">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field5" name="label_field5">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field6"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field6"]; ?></label>
											<input type="text" class="form-control" id="label_field6" name="label_field6">
										</div>


									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field6" name="label_field6">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field7"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field7"]; ?></label>
											<input type="text" class="form-control" id="label_field7" name="label_field7">
										</div>


                 <?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field7" name="label_field7">
										</div>
					<?php
				   }
				   ?>


					 <?php
				   if($row["label_field8"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field8"]; ?></label>
											<input type="text" class="form-control" id="label_field8" name="label_field8">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field8" name="label_field8">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field9"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field9"]; ?></label>
											<input type="text" class="form-control" id="label_field9" name="label_field9">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field9" name="label_field9">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field10"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field10"]; ?></label>
											<input type="text" class="form-control" id="label_field10" name="label_field10">
										</div>


									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field10" name="label_field10">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field11"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field11"]; ?></label>
											<input type="text" class="form-control" id="label_field11" name="label_field11">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field11" name="label_field11">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field12"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field12"]; ?></label>
											<input type="text" class="form-control" id="label_field12" name="label_field12">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field12" name="label_field12">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field13"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field13"]; ?></label>
											<input type="text" class="form-control" id="label_field13" name="label_field13">
										</div>


									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field13" name="label_field13">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field14"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field14"]; ?></label>
											<input type="text" class="form-control" id="label_field14" name="label_field14">
										</div>


                  <?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field14" name="label_field14">
										</div>
					<?php
				   }
				   ?>
				   </tr>
				   <tr>

					 <?php
				   if($row["label_field15"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field15"]; ?></label>
											<input type="text" class="form-control" id="label_field15" name="label_field15">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field15" name="label_field15">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field16"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field16"]; ?></label>
											<input type="text" class="form-control" id="label_field16" name="label_field16">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field16" name="label_field16">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field17"] != "")
				   {
					   ?>
										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field17"]; ?></label>
											<input type="text" class="form-control" id="label_field17" name="label_field17">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field17" name="label_field17">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field18"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field18"]; ?></label>
											<input type="text" class="form-control" id="label_field18" name="label_field18">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field18" name="label_field18">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field19"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field19"]; ?></label>
											<input type="text" class="form-control" id="label_field19" name="label_field19">
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field19" name="label_field19">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field20"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field20"]; ?></label>
											<input type="text" class="form-control" id="label_field20" name="label_field20">
										</div>


									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field20" name="label_field20">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field21"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field21"]; ?></label>
											<input type="text" class="form-control" id="label_field21" name="label_field21">
										</div>

               <?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field21" name="label_field21">
										</div>
					<?php
				   }
				   ?>
				   </tr>
				   <tr>

					 <?php
				   if($row["label_field22"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field22"]; ?></label>
											<input type="text" class="form-control" id="label_field22" name="label_field22">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field22" name="label_field22">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field23"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field23"]; ?></label>
											<input type="text" class="form-control" id="label_field23" name="label_field23">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field23" name="label_field23">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field24"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field24"]; ?></label>
											<input type="text" class="form-control" id="label_field24" name="label_field24">
										</div>


									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field24" name="label_field24">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field25"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field25"]; ?></label>
											<input type="text" class="form-control" id="label_field25" name="label_field25">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field25" name="label_field25">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field26"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field26"]; ?></label>
											<input type="text" class="form-control" id="label_field26" name="label_field26">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field26" name="label_field26">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field27"] != "")
				   {
					   ?>

										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field2"><?php echo $row["label_field27"]; ?></label>
											<input type="text" class="form-control" id="label_field27" name="label_field27">
										</div>


									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field27" name="label_field27">
										</div>
					<?php
				   }
				   ?>

					 <?php
				   if($row["label_field28"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_field28"]; ?></label>
											<input type="text" class="form-control" id="label_field28" name="label_field28">
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="label_field28" name="label_field28">
										</div>
					<?php
				   }
				   ?>



								 <?php
				   if($row["label_comments"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_comments"]; ?></label>
											<textarea class="form-control" id="comments" name="comments" rows="1" cols="20">
											</textarea>
										</div>

								<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="comments" name="comments">
										</div>
					<?php
				   }
				   ?>


				   <?php
				   if($row["label_dropdown1"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown1"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown1_list" name="dropdown1_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down1 = explode(',',$row["label_dropdown_value1"]);
                                            foreach($drop_down1 as $drop1) {
		                                    ?>
                                            <option value="<?php echo $drop1; ?>"><?php echo $drop1; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					    ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown1_list" name="dropdown1_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown2"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown2"]; ?><span style="color:red;">*</span></label>
											<select class="form-control select2" class="form-control" id="dropdown2_list" name="dropdown2_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down2 = explode(',',$row["label_dropdown_value2"]);
                                            foreach($drop_down2 as $drop2) {
		                                    ?>
                                            <option value="<?php echo $drop2; ?>"><?php echo $drop2; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown2_list" name="dropdown2_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown3"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown3"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown3_list" name="dropdown3_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down3 = explode(',',$row["label_dropdown_value3"]);
                                            foreach($drop_down3 as $drop3) {
		                                    ?>
                                            <option value="<?php echo $drop3; ?>"><?php echo $drop3; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown3_list" name="dropdown3_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown4"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown4"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown4_list" name="dropdown4_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down4 = explode(',',$row["label_dropdown_value4"]);
                                            foreach($drop_down4 as $drop4) {
		                                    ?>
                                            <option value="<?php echo $drop4; ?>"><?php echo $drop4; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown4_list" name="dropdown4_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown5"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown5"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown5_list" name="dropdown5_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down5 = explode(',',$row["label_dropdown_value5"]);
                                            foreach($drop_down5 as $drop5) {
		                                    ?>
                                            <option value="<?php echo $drop5; ?>"><?php echo $drop5; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown5_list" name="dropdown5_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown6"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown6"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown6_list" name="dropdown6_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down6 = explode(',',$row["label_dropdown_value6"]);
                                            foreach($drop_down6 as $drop6) {
		                                    ?>
                                            <option value="<?php echo $drop6; ?>"><?php echo $drop6; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
					   ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown6_list" name="dropdown6_list">
										</div>
					<?php
				   }
				   ?>

				    <?php
				   if($row["label_dropdown7"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row["label_dropdown7"]; ?></label>
											<select class="form-control select2" class="form-control" id="dropdown7_list" name="dropdown7_list">
                                            <option value="">Select Option</option>
											<?php
											$drop_down7 = explode(',',$row["label_dropdown_value7"]);
                                            foreach($drop_down7 as $drop7) {
		                                    ?>
                                            <option value="<?php echo $drop7; ?>"><?php echo $drop7; ?></option>
                                            <?php
	                                           }
	                                            ?>
                                          </select>
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="dropdown7_list" name="dropdown7_list">
										</div>
					<?php
				   }
			   }
				   ?>
				  <br>
				  <?php
				  $stmt_rs="SELECT * from drop_downs where label_id='$label';";
				 // echo $stmt_rs;
	                           $rslt_drop= mysqli_query($conn,$stmt_rs);

			if (mysqli_num_rows($rslt_drop) > 0) {
				    $x = 1;
			   while($row_drop = mysqli_fetch_assoc($rslt_drop)) {
				   ?>

				   <?php
				   if($row_drop["dropdown1"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row_drop["dropdown1"]; ?></label>
											<select class="form-control select2" style="width: 100%;" id="drop_down1_value" name="drop_down1_value" onchange="select1_Dropdown()">
						                <option value="">Select Option</option>
											<?php
											$dropdown1_value = explode(',',$row_drop["dropdown1_value"]);
                                            foreach($dropdown1_value as $drop_down1_value) {
		                                    ?>
                                            <option value="<?php echo $drop_down1_value; ?>"><?php echo $drop_down1_value; ?></option>
                                            <?php
	                                           }
	                                            ?>

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_dropdown1"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_drop_down1_value" name="sub_drop_down1_value" onchange="select1_sub_Dropdown()">

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_sub_dropdown1"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_sub_drop_down1_value" name="sub_sub_drop_down1_value">

					                    </select>
										<input type="hidden" class="form-control" id="lead_id" name="lead_id" value="<?php echo $label; ?>">
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="drop_down1_value" name="drop_down1_value">
										<input type="text" class="form-control" id="sub_drop_down1_value" name="sub_drop_down1_value">
								<input type="text" class="form-control" id="sub_sub_drop_down1_value" name="sub_sub_drop_down1_value">

										</div>
					<?php
				   }
				   ?>

				   <?php
				   if($row_drop["dropdown2"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row_drop["dropdown2"]; ?></label>
											<select class="form-control select2" style="width: 100%;" id="drop_down2_value" name="drop_down2_value" onchange="select2_Dropdown()">
						                <option value="">Select Option</option>
											<?php
											$dropdown2_value = explode(',',$row_drop["dropdown2_value"]);
                                            foreach($dropdown2_value as $drop_down2_value) {
		                                    ?>
                                            <option value="<?php echo $drop_down2_value; ?>"><?php echo $drop_down2_value; ?></option>
                                            <?php
	                                           }
	                                            ?>

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_dropdown2"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_drop_down2_value" name="sub_drop_down2_value" onchange="select2_sub_Dropdown()">

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_sub_dropdown2"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_sub_drop_down2_value" name="sub_sub_drop_down2_value">

					                    </select>
										<input type="hidden" class="form-control" id="lead_id" name="lead_id" value="<?php echo $label; ?>">
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="drop_down2_value" name="drop_down2_value">
										<input type="text" class="form-control" id="sub_drop_down2_value" name="sub_drop_down2_value">
								<input type="text" class="form-control" id="sub_sub_drop_down2_value" name="sub_sub_drop_down2_value">
										</div>
					<?php
				   }
				   ?>

				   <?php
				   if($row_drop["dropdown3"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row_drop["dropdown3"]; ?></label>
											<select class="form-control select2" style="width: 100%;" id="drop_down3_value" name="drop_down3_value" onchange="select3_Dropdown()">
						                <option value="">Select Option</option>
											<?php
											$dropdown3_value = explode(',',$row_drop["dropdown3_value"]);
                                            foreach($dropdown3_value as $drop_down3_value) {
		                                    ?>
                                            <option value="<?php echo $drop_down3_value; ?>"><?php echo $drop_down3_value; ?></option>
                                            <?php
	                                           }
	                                            ?>

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_dropdown3"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_drop_down3_value" name="sub_drop_down3_value" onchange="select3_sub_Dropdown()">

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_sub_dropdown3"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_sub_drop_down3_value" name="sub_sub_drop_down3_value">

					                    </select>
										<input type="hidden" class="form-control" id="lead_id" name="lead_id" value="<?php echo $label; ?>">
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="drop_down3_value" name="drop_down3_value">
										<input type="text" class="form-control" id="sub_drop_down3_value" name="sub_drop_down3_value">
								<input type="text" class="form-control" id="sub_sub_drop_down3_value" name="sub_sub_drop_down3_value">
										</div>
					<?php
				   }
				   ?>

				   <?php
				   if($row_drop["dropdown4"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row_drop["dropdown4"]; ?></label>
											<select class="form-control select2" style="width: 100%;" id="drop_down4_value" name="drop_down4_value" onchange="select4_Dropdown()">
						                <option value="">Select Option</option>
											<?php
											$dropdown4_value = explode(',',$row_drop["dropdown4_value"]);
                                            foreach($dropdown4_value as $drop_down4_value) {
		                                    ?>
                                            <option value="<?php echo $drop_down4_value; ?>"><?php echo $drop_down4_value; ?></option>
                                            <?php
	                                           }
	                                            ?>

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_dropdown4"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_drop_down4_value" name="sub_drop_down4_value" onchange="select4_sub_Dropdown()">

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_sub_dropdown4"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_sub_drop_down4_value" name="sub_sub_drop_down4_value">

					                    </select>
										<input type="hidden" class="form-control" id="lead_id" name="lead_id" value="<?php echo $label; ?>">
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="drop_down4_value" name="drop_down4_value">
										<input type="text" class="form-control" id="sub_drop_down4_value" name="sub_drop_down4_value">
								<input type="text" class="form-control" id="sub_sub_drop_down4_value" name="sub_sub_drop_down4_value">
										</div>
					<?php
				   }
				   ?>

				   <?php
				   if($row_drop["dropdown5"] != "")
				   {
					   ?>


										<div class="form-group" style="display: inline-block;width: 18%;vertical-align: top;margin-right:7px;">
											<label for="field12"><?php echo $row_drop["dropdown5"]; ?></label>
											<select class="form-control select2" style="width: 100%;" id="drop_down5_value" name="drop_down5_value" onchange="select5_Dropdown()">
						                <option value="">Select Option</option>
											<?php
											$dropdown5_value = explode(',',$row_drop["dropdown5_value"]);
                                            foreach($dropdown5_value as $drop_down5_value) {
		                                    ?>
                                            <option value="<?php echo $drop_down5_value; ?>"><?php echo $drop_down5_value; ?></option>
                                            <?php
	                                           }
	                                            ?>

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_dropdown5"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_drop_down5_value" name="sub_drop_down5_value" onchange="select5_sub_Dropdown()">

					                    </select>
										<label for="field12"><?php echo $row_drop["sub_sub_dropdown5"]; ?></label>
										<select class="form-control select2" style="width: 100%;" id="sub_sub_drop_down5_value" name="sub_sub_drop_down5_value">

					                    </select>
										<input type="hidden" class="form-control" id="lead_id" name="lead_id" value="<?php echo $label; ?>">
										</div>

									<?php
				   }else{
 ?>
					   <div class="form-group"style="display:none;">

											<input type="text" class="form-control" id="drop_down5_value" name="drop_down5_value">
										<input type="text" class="form-control" id="sub_drop_down5_value" name="sub_drop_down5_value">
								<input type="text" class="form-control" id="sub_sub_drop_down5_value" name="sub_sub_drop_down5_value">
										</div>
					<?php
				   }
				   ?>

				<?php
			   }
			}else{
				?>

				<div class="form-group"style="display:none;">

												<input type="text" class="form-control" id="drop_down1_value" name="drop_down1_value">
										<input type="text" class="form-control" id="sub_drop_down1_value" name="sub_drop_down1_value">
								<input type="text" class="form-control" id="sub_sub_drop_down1_value" name="sub_sub_drop_down1_value">

											<input type="text" class="form-control" id="drop_down2_value" name="drop_down2_value">
										<input type="text" class="form-control" id="sub_drop_down2_value" name="sub_drop_down2_value">
								<input type="text" class="form-control" id="sub_sub_drop_down2_value" name="sub_sub_drop_down2_value">

											<input type="text" class="form-control" id="drop_down3_value" name="drop_down3_value">
										<input type="text" class="form-control" id="sub_drop_down3_value" name="sub_drop_down3_value">
								<input type="text" class="form-control" id="sub_sub_drop_down3_value" name="sub_sub_drop_down3_value">

											<input type="text" class="form-control" id="drop_down4_value" name="drop_down4_value">
										<input type="text" class="form-control" id="sub_drop_down4_value" name="sub_drop_down4_value">
								<input type="text" class="form-control" id="sub_sub_drop_down4_value" name="sub_sub_drop_down4_value">

											<input type="text" class="form-control" id="drop_down5_value" name="drop_down5_value">
										<input type="text" class="form-control" id="sub_drop_down5_value" name="sub_drop_down5_value">
								<input type="text" class="form-control" id="sub_sub_drop_down5_value" name="sub_sub_drop_down5_value">
										</div>

				<?php
			}
				   ?>

							</form>

<div id="announcement" style="height:20px;">
<span style="color:#cc0000;"> <b><i class="fa fa-bullhorn" aria-hidden="true"></i></b></span>
</div>

<div class="scrolling-text-container" style="height:22px;font-family: Open Sans;color:#1A2B6D;font-size:13px;font-weight:bold;">
<span class="scrolling-text" id="announce"></span>
			</div>
					</div>

				</div>

			</div>

			<div class="row mt-4" style="display:none">
				<div class="col-lg-7 mb-lg-0 mb-4">
					<div class="card ">
						<div class="card-header pb-0 p-3">
							<div class="d-flex justify-content-between">
								<h6 class="mb-2">Sales by Country</h6>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table align-items-center ">
								<tbody>
									<tr>
										<td class="w-30">
											<div class="d-flex px-2 py-1 align-items-center">
												<div>
													<img src="./assets/img/icons/flags/US.png" alt="Country flag">
												</div>
												<div class="ms-4">
													<p class="text-xs font-weight-bold mb-0">Country:</p>
													<h6 class="text-sm mb-0">United States</h6>
												</div>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Sales:</p>
												<h6 class="text-sm mb-0">2500</h6>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Value:</p>
												<h6 class="text-sm mb-0">$230,900</h6>
											</div>
										</td>
										<td class="align-middle text-sm">
											<div class="col text-center">
												<p class="text-xs font-weight-bold mb-0">Bounce:</p>
												<h6 class="text-sm mb-0">29.9%</h6>
											</div>
										</td>
									</tr>
									<tr>
										<td class="w-30">
											<div class="d-flex px-2 py-1 align-items-center">
												<div>
													<img src="./assets/img/icons/flags/DE.png" alt="Country flag">
												</div>
												<div class="ms-4">
													<p class="text-xs font-weight-bold mb-0">Country:</p>
													<h6 class="text-sm mb-0">Germany</h6>
												</div>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Sales:</p>
												<h6 class="text-sm mb-0">3.900</h6>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Value:</p>
												<h6 class="text-sm mb-0">$440,000</h6>
											</div>
										</td>
										<td class="align-middle text-sm">
											<div class="col text-center">
												<p class="text-xs font-weight-bold mb-0">Bounce:</p>
												<h6 class="text-sm mb-0">40.22%</h6>
											</div>
										</td>
									</tr>
									<tr>
										<td class="w-30">
											<div class="d-flex px-2 py-1 align-items-center">
												<div>
													<img src="./assets/img/icons/flags/GB.png" alt="Country flag">
												</div>
												<div class="ms-4">
													<p class="text-xs font-weight-bold mb-0">Country:</p>
													<h6 class="text-sm mb-0">Great Britain</h6>
												</div>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Sales:</p>
												<h6 class="text-sm mb-0">1.400</h6>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Value:</p>
												<h6 class="text-sm mb-0">$190,700</h6>
											</div>
										</td>
										<td class="align-middle text-sm">
											<div class="col text-center">
												<p class="text-xs font-weight-bold mb-0">Bounce:</p>
												<h6 class="text-sm mb-0">23.44%</h6>
											</div>
										</td>
									</tr>
									<tr>
										<td class="w-30">
											<div class="d-flex px-2 py-1 align-items-center">
												<div>
													<img src="./assets/img/icons/flags/BR.png" alt="Country flag">
												</div>
												<div class="ms-4">
													<p class="text-xs font-weight-bold mb-0">Country:</p>
													<h6 class="text-sm mb-0">Brasil</h6>
												</div>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Sales:</p>
												<h6 class="text-sm mb-0">562</h6>
											</div>
										</td>
										<td>
											<div class="text-center">
												<p class="text-xs font-weight-bold mb-0">Value:</p>
												<h6 class="text-sm mb-0">$143,960</h6>
											</div>
										</td>
										<td class="align-middle text-sm">
											<div class="col text-center">
												<p class="text-xs font-weight-bold mb-0">Bounce:</p>
												<h6 class="text-sm mb-0">32.14%</h6>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-lg-5" style="display:none1">
					<div class="card">
						<div class="card-header pb-0 p-3">
							<h6 class="mb-0">Categories</h6>
						</div>
						<div class="card-body p-3">
							<ul class="list-group">
								<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
									<div class="d-flex align-items-center">
										<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
											<i class="ni ni-mobile-button text-white opacity-10"></i>
										</div>
										<div class="d-flex flex-column">
											<h6 class="mb-1 text-dark text-sm">Devices</h6>
											<span class="text-xs">250 in stock, <span class="font-weight-bold">346+ sold</span></span>
										</div>
									</div>
									<div class="d-flex">
										<button class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></button>
									</div>
								</li>
								<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
									<div class="d-flex align-items-center">
										<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
											<i class="ni ni-tag text-white opacity-10"></i>
										</div>
										<div class="d-flex flex-column">
											<h6 class="mb-1 text-dark text-sm">Tickets</h6>
											<span class="text-xs">123 closed, <span class="font-weight-bold">15 open</span></span>
										</div>
									</div>
									<div class="d-flex">
										<button class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></button>
									</div>
								</li>
								<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
									<div class="d-flex align-items-center">
										<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
											<i class="ni ni-box-2 text-white opacity-10"></i>
										</div>
										<div class="d-flex flex-column">
											<h6 class="mb-1 text-dark text-sm">Error logs</h6>
											<span class="text-xs">1 is active, <span class="font-weight-bold">40 closed</span></span>
										</div>
									</div>
									<div class="d-flex">
										<button class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></button>
									</div>
								</li>
								<li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
									<div class="d-flex align-items-center">
										<div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
											<i class="ni ni-satisfied text-white opacity-10"></i>
										</div>
										<div class="d-flex flex-column">
											<h6 class="mb-1 text-dark text-sm">Happy users</h6>
											<span class="text-xs font-weight-bold">+ 430</span>
										</div>
									</div>
									<div class="d-flex">
										<button class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></button>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

	</main>

	<!--   Core JS Files   -->
	<script src="./assets/js/core/popper.min.js"></script>
	<script src="./assets/js/core/bootstrap.min.js"></script>
	<script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
	<script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
	<script src="./assets/js/plugins/chartjs.min.js"></script>
	<script>
		var ctx1 = document.getElementById("chart-line").getContext("2d");

		var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

		gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
		gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
		gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
		new Chart(ctx1, {
			type: "line",
			data: {
				labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
				datasets: [{
					label: "Mobile apps",
					tension: 0.4,
					borderWidth: 0,
					pointRadius: 0,
					borderColor: "#5e72e4",
					backgroundColor: gradientStroke1,
					borderWidth: 3,
					fill: true,
					data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
					maxBarThickness: 6

				}],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: false,
					}
				},
				interaction: {
					intersect: false,
					mode: 'index',
				},
				scales: {
					y: {
						grid: {
							drawBorder: false,
							display: true,
							drawOnChartArea: true,
							drawTicks: false,
							borderDash: [5, 5]
						},
						ticks: {
							display: true,
							padding: 10,
							color: '#fbfbfb',
							font: {
								size: 11,
								family: "Open Sans",
								style: 'normal',
								lineHeight: 2
							},
						}
					},
					x: {
						grid: {
							drawBorder: false,
							display: false,
							drawOnChartArea: false,
							drawTicks: false,
							borderDash: [5, 5]
						},
						ticks: {
							display: true,
							color: '#ccc',
							padding: 20,
							font: {
								size: 11,
								family: "Open Sans",
								style: 'normal',
								lineHeight: 2
							},
						}
					},
				},
			},
		});
	</script>
	<script>
		var win = navigator.platform.indexOf('Win') > -1;
		if (win && document.querySelector('#sidenav-scrollbar')) {
			var options = {
				damping: '0.5'
			}
			Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
		}
	</script>
	<!-- Github buttons -->
	<script async defer src="css/git/dist/buttons.js"></script>
	<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="./assets/js/argon-dashboard.min.js?v=2.0.2"></script>

	<link rel="stylesheet" href="/connect5AgentPage/assets/css/w3.css">





	<div id="id01" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 139px;width: 450px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="parkClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">PARK</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p></p>
			</footer>
		</div>
	</div>


	<div id="id02" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 100px;width: 450px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="transferClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">TRANSFER</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container" id="TransferID">


			</div>
			<table>
			<tr>
			<td>
			<div class="form-group" style="width:300px;margin-left:18px;">
  <span style="font-size: 13px;font-weight: 600;">Phone Number</span>
  <input type="tel" class="form-control" pattern="[0-9]{10}" placeholder="Enter 10 digits mobile number" id="transfer_number" name="transfer_number" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
</div>
</td> </tr>
					<tr>
					<td>
					&nbsp;&nbsp;&nbsp;<span><button type="button" class="btn" onclick="phonetransferAgent();">Conference</button></span>&nbsp;&nbsp;&nbsp;<span><button type="button" class="btn" onclick="blindtransferAgent();">Blind Transfer</button></span>
					</td>
					</tr>
					</table>
			<footer class="w3-container">
				<p></p>
			</footer>
		</div>
	</div>

	<div id="id03" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 30px;width: 620px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="emailClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">SEND EMAIL</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
			 <form id="myForm" enctype="multipart/form-data">
				<table>
					<tr>
						<td>
							<span style="font-size: 12px;font-weight: 600;font-family: Open Sans;">To :</span><input type="email" name="sendEmail" id="sendEmail" class="form-control" pattern="/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/" required>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-size: 12px;font-weight: 600;font-family: Open Sans;">Subject :</span><input type="text" name="subjectEmail" id="subjectEmail" class="form-control" pattern="^[a-zA-Z0-9\s]*$" title="Please enter only numbers and characters" required>
						</td>
						<td style="display:none;">
							<span style="font-size: 12px;font-weight: 600;font-family: Open Sans;">User:</span><input type="text" name="agent" id="agent" class="form-control" value='<?php echo $loggedInuserName; ?>'>
						</td>
					</tr>
					<tr>
						<td>
							<textarea rows="4" cols="50" style="width: 407px;height: 202px;border:none;font-family: Open Sans;font-size: 12px;" class="form-control" id="content" name="content" placeholder="Type message here :) "></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<span style="font-size: 12px;font-weight: 600;font-family: Open Sans;">Attach Files :</span><br><button class="btn" type="button" style="width:270px;height:60px;"><input type="file" class="form-control" id="attachment" name="attachment"></button><span style="font-size: 10px;font-family: Open Sans;">Max Size 5MB(JPG, JPEG, PNG, GIF, PDF, CSV, EXCEL, DOCX)</span>
						</td>
					</tr>
				</table>
				<div align="right">
			<button type="button" class="btn btn-primary" id="submitBtn">Submit</button>
				</div>
                </form>

			</div>
			<footer class="w3-container">

			</footer>
		</div>
	</div>


	<div id="id04" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 10px;width: 600px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="goLiveClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">ADD INFORMATION</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
			<form action = "" method = "POST">
			<table>
					<tr>
						<th>
						<div class="form-group">
						<label for="UserID">Name</label>
						<input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter User Name" >
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">City</label>
						<input type="text" class="form-control" id="user_city" name="user_city" placeholder="Enter User City" >
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">Mobile Number</label>
						<input type="text" class="form-control" pattern="[0-9]{10}" id="user_number" name="user_number" placeholder="Enter Mobile Number">
					</div>
						</th>
					</tr>

				      <tr>
						<th>
						<div class="form-group">
						<label for="UserID">Email ID</label>
						<input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter User Email">
					</div>
						</th>


						<th>
						<div class="form-group">
						<label for="UserID">Last Agent</label>
						<input type="text" class="form-control" id="user_agent" name="user_agent" placeholder="Enter Agent">
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">Field1</label>
						<input type="text" class="form-control" id="user_field1" name="user_field1" placeholder="Enter field1">
					</div>
						</th>
						</tr>

					<tr>
						<th>
							<div class="form-group">
						<label for="UserID">Field2</label>
						<input type="text" class="form-control" id="user_field2" name="user_field2" placeholder="Enter field2">
					</div>
						</th>

						<th>
						<div class="form-group">
						<label for="UserID">Field3</label>
						<input type="text" class="form-control" id="user_field3" name="user_field3" placeholder="Enter field3">
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">Field4</label>
						<input type="text" class="form-control" id="user_field4" name="user_field4" placeholder="Enter field4">
					</div>
						</th>
						</tr>

					<tr>
						<th>
							<div class="form-group">
						<label for="UserID">Field5</label>
						<input type="text" class="form-control" id="user_field5" name="user_field5" placeholder="Enter field5">
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">Last Disposition</label>
						<input type="text" class="form-control" id="user_disposition" name="user_disposition" placeholder="Enter disposition">
					</div>
						</th>
						<th>
							<div class="form-group">
						<label for="UserID">Last Comments</label>
						<textarea class="form-control" id="user_comments" name="user_comments" rows="2" cols="4" placeholder="Enter comments"></textarea>
					</div>
						</th>
					</tr>
				</table>
             <div class="card-footer">

				  <button type="button" class="btn btn-success" onclick="userclick()">Submit</button>
                </div>
            </form>


			</div>
			<footer class="w3-container">
				<p></p>
			</footer>
		</div>
	</div>


	<div id="id05" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 10px;width: 800px;">
			<header class="w3-container w3-teal">
				<span onclick="whatsAppClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">WhatsApp</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
				<div class="row1">
					<div class="col-12">
						<div class="card">
							<div class="card-body height3">
								<ul class="chat-list">
									<li class="in">
										<div class="chat-img">
											<img alt="Avtar" src="/connect5AgentPage/assets/img/user.png">
										</div>
										<div class="chat-body">
											<div class="chat-message">
												<p>Hi </p>
											</div>
										</div>
									</li>
									<li class="out">
										<div class="chat-img">
											<img alt="Avtar" src="/connect5AgentPage/assets/img/wp_logo.png">
										</div>
										<div class="chat-body">
											<div class="chat-message">
												<p>Hey,How are you ?</p>
											</div>
										</div>
									</li>
									<li class="in">
										<div class="chat-img">
											<img alt="Avtar" src="/connect5AgentPage/assets/img/user.png">
										</div>
										<div class="chat-body">
											<div class="chat-message">
												<p>I am good, what about you.</p>
											</div>
										</div>
									</li>
									<li class="out">
										<div class="chat-img">
											<img alt="Avtar" src="/connect5AgentPage/assets/img/wp_logo.png">
										</div>
										<div class="chat-body">
											<div class="chat-message">
												<p>Good</p>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<input type="text" class="form-control" name="whatsAppData" id="whatsAppData" placeholder="type message here :)">
						</div>
					</div>
				</div>
			</div>
			<footer class="w3-container w3-teal">
				<p> </p>
			</footer>
		</div>
	</div>

	</div>

	<div id="id06" class="w3-modal" style="display:none">
		<div class="w3-animate-zoom" style="margin-left: -700px;">

			<div class="w3-container">
				<div class="container123" style="background-color: #FFFFFF;padding-top: 20px;border-radius:40px;">

				<span onclick="dialpadClose();" class="w3-button" style="color:red; margin-left:220px;font-size:15px;">&times;</span>
    <div>
        <input type="text" placeholder="Enter phone number.." pattern="[0-9]{10}" id="output" name="output" style="border:none;width: 240px;background-color: #FFFFFF; text-align: center;" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" /></div>
						<div class="rowData12">
							<div class="digit123" id="one" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">1</div>
							<div class="digit123" id="two" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">2
							</div>
							<div class="digit123" id="three" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">3
							</div>
						</div>
						<div class="rowData12">
							<div class="digit123" id="four" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">4
							</div>
							<div class="digit123" id="five" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">5
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21pxmargin-bottom: 15px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">6
							</div>
						</div>
						<div class="rowData12">
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">7
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">8
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">9
							</div>
						</div>
						<div class="rowData12">
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">*
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">0
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial">#
							</div>
						</div>
						<div class="rowData12" style="display:none">
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial;display:none1"><i class='fas fa-sync' style="display:none"></i>
							</div>
							<div class="digit123" style="background-color: #6eba01;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial"><i class="fa fa-phone" aria-hidden="true"></i>
							</div>
							<div class="digit123" style="background-color: #FFFFFF;border-radius: 59px;padding: 8px;box-shadow: white;margin-right: 21px;margin-bottom: 15px; box-shadow: 5px 10px #888888;box-sizing: initial;display:none1"><i class='fas fa-long-arrow-alt-left' style="display:none"></i>
							</div>
						</div>
						<div class="botrowData12" style="display:none1" onclick="click2call()">
							<div id="call"><i class="fa fa-phone" aria-hidden="true"></i></div>
							<i class="fa fa-long-arrowData12-left dig"></i>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div id="id07" class="w3-modal" style="display:none;">
		<div class="w3-modal-content w3-animate-zoom" style="background-color: #FFFFFF;border-radius: 22px;margin-top: 30px;width: 311px;margin-left: 171px;height: 500px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="internalChatClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;background-color: #FFFFFF;">INTERNAL CHAT AGENT LIST</h2>
				<hr style="color:#060606">
			</header>
			<div class="w3-container" id="ChatRefreshID" style="background-color: #FFFFFF;">


			</div>
			<footer class="w3-container" style="background-color: #FFFFFF;">
				<p> </p>
			</footer>
		</div>
	</div>

	<div id="id08" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 30px;width: 311px;margin-left: 171px;height: 500px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="document.getElementById('id08').style.display = 'none';" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">INTERNAL CHAT</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
				<span> <i class='fas fa-angle-left' style='style="font-size:24px;cursor:pointer;color: #1A2B6D;"' onclick="openInternalChatAgentList()"></i></span>&nbsp;&nbsp; <i class='fas fa-user-check' style='font-size:13px ;color:green'></i> &nbsp;&nbsp; <span id="ChatAgentName"></span>

				<div class="row1">
					<div class="col-12">
						<div class="card">
							<div class="card-body height3" id="internalChatHistroy" style="width: 268px;height: 347px;line-height: 3em;overflow: auto; border: none #000 solid;  padding: 5px;">

							</div>
							<input type="hidden" value="" id="AgentNameToChat">
							<input type="text" class="form-control" name="InternalChatWithAgentSend" id="InternalChatWithAgentSend" placeholder="type message here :)">
						</div>
					</div>
				</div>

			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

	<div id="id09" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="background-color: #FFFFFF;border-radius: 22px;margin-top: 30px;width: 311px;margin-left: 171px;height: 500px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="scriptClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">SCRIPT</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

<div id="id10" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="background-color: #FFFFFF;border-radius: 22px;margin-top: 30px;width: 311px;margin-left: 171px;height: 300px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="breakClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">TOTAL BREAK TIME</h2>
				<hr style="color:#060606">
			</header>

    <div class="w3-container" id="BreakRefresh">


			</div>

			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>


	<div id="phonebook_id" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="background-color: #FFFFFF;border-radius: 22px;margin-top: 30px;width: 350px;margin-right: 100px;height: 400px;overflow-x: auto;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="phoneClose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;"><i class="fa fa-address-book-o" aria-hidden="true"></i>&nbsp;PHONE BOOK</h2>
				<hr style="color:#060606">
			</header>
<div class="w3-container">
<input type = "text" class="form-control" name="searchPhoneBook" id="searchPhoneBook" placeholder="search for name or number ......" onkeyup="myphoneBook()">
</div>
<div class="w3-container" id="phoneBookRefresh" style="margin-top:5px;">


			</div>

			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

<div id="id11" class="w3-modal" style="display:none">
		<div class="w3-modal-content w3-animate-zoom" style="background-color: #FFFFFF;border-radius: 22px;margin-top: 30px;width: 311px;margin-left: 171px;height: 370px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="callbackclose();" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">CALLBACK DATE & TIME</h2>
				<hr style="color:#060606">
			</header>

			<div class="w3-container">

<div class="form-group">
  <label for="callbackdate">CallBack Date:</label>
  <input type="date" id="callback_date" name="callback_date" class="form-control">
</div>
<div class="form-group">
  <label for="callbacktime">CallBack Time:</label>
  <input type="time" id="callback_time" name="callback_time" class="form-control">
</div>
<div class="form-group">
  <label for="user">Me Only</label> <input type="checkbox" id="me_user" name="me_user"  value="<?php echo $extension; ?>"
  style="width: 14px; height:14px;">
 </div>
<div class="form-group">
<center>
<button type="button" class="btn btn-success" onclick="setCallback()">Submit</button>
</center>
</div>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

	<div id="inboundInfPop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 790px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="document.getElementById('inboundInfPop').style.display='none'" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">INBOUND INFO</h2>
				<hr style="color:#060606">
			</header>
			<div id="inboundTable" style="width:99%">
			</div>

			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

	<div id="outboundInfPop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 790px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="document.getElementById('outboundInfPop').style.display='none'" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">OUTBOUND INFO</h2>
				<hr style="color:#060606">
			</header>
			<div id="outboundTable" style="width:99%">
			</div>
			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

	<div id="dropCallPop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 790px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
	<span onclick="document.getElementById('dropCallPop').style.display='none'" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">DROP INFO</h2>
				<hr style="color:#060606">
			</header>
			<div id="dropTable" style="width:99%">
			</div>
			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>

<div id="queueCallPop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 790px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="document.getElementById('queueCallPop').style.display='none'" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">CALLS IN QUEUE INFO</h2>
				<hr style="color:#060606">
			</header>
			<div id="queueTable" style="width:99%">
			</div>
			<div class="w3-container">
				<p> </p>
				<p> </p>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>



	<div id="dispoPop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 370px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">
				<span onclick="document.getElementById('dispoPop').style.display='none'" class="w3-button w3-display-topright" style="color:red">&times;</span>
				<h2 style="font-size: 18px;font-weight: 500;">DISPOSITION</h2>
				<hr style="color:#060606">
			</header>
			<div id="dropTable" style="width:99%">
			</div>
			<div class="w3-container">
				<table>
					<tr>

		<td style="padding:20px"><button class="btn" onclick="dispoFun('ANSWERED','<?php echo $loggedInuserName; ?>')">ANSWERED</button></td>
		<td style="padding:20px"><button class="btn" onclick="dispoFun('NO ANSWERED','<?php echo $loggedInuserName; ?>')">NO ANSWERED</button></td></tr><tr>
		<td style="padding:20px"><button class="btn" onclick="dispoFun('CALLBACK','<?php echo $loggedInuserName; ?>')">CALLBACK</button></td>
					</tr>

				</table>
<div class="form-group">
  <input type="checkbox" id="go_pause" name="go_pause"  value="GOPAUSE" style="width: 14px; height:14px;"><label for="user" style="color:red;">After Hangup Go To Pause....</label>
 </div>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>



<div id="pause_pop" class="w3-modal" style="display:none;font-size:12px">
		<div class="w3-modal-content w3-animate-zoom" style="border-radius: 22px;margin-top: 33px;width: 500px;">
			<header class="w3-container" style="background-color: #FFFFFF;border-radius: 22px;">

				<h2 style="font-size: 18px;font-weight: 500;">PAUSE CODES</h2>
				<hr style="color:#060606">
			</header>
			<div id="dropTable1245" style="width:99%">
			</div>
			<div class="w3-container">
			<?php
			$stmt_select="SELECT * from pause_code where user_group='$user_group'";
	                  $rslt_rs= mysqli_query($conn,$stmt_select);

					 $x = 1;
			   while($row = mysqli_fetch_assoc($rslt_rs)) {
				   ?>

                   <div class="form-group" style="display: inline-block;vertical-align:top;width:30%;height:60px;font-weight:600;margin-left:10px;padding:10px;color:#1A2B6D;">
					<button class="btn"  onclick="pauseFun('<?php echo $row["pause_id"]; ?>','<?php echo $row["pause_name"]; ?>')"><?php echo $row["pause_name"]; ?></button>
					</div>
					 <?php $x++; } ?>
			</div>
			<footer class="w3-container">
				<p> </p>
			</footer>
		</div>
	</div>


<div class="popup-overlay" id="popupOverlay">
    <div class="popup-content">
        <div id="calendarContainer"></div>
        <button class="btn btn-warning" onclick="hidePopup()">Close</button>
    </div>
</div>

<div id="alertDiv"></div>
</body>

</html>

<script src="css/moment.js"></script>
<script src="css/calendar.js"></script>

<script>
        // Get the current date
        const today = new Date();

        // Format the date as "YYYY-MM-DD"
        const year = today.getFullYear();
       const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is zero-based, so we add 1
       const day = String(today.getDate()).padStart(2, '0');

     const formattedDate = `${year}-${month}-${day}`;

        // Set the default value of the input element to today's date
        document.getElementById("datePicker").value = formattedDate;
    </script>
<script>

document.getElementById("toggleButton").addEventListener("click", function() {
  var content = document.getElementById("toggleContent");
  content.classList.toggle("visible");
  content.classList.toggle("hidden");
});

function userclick(){
	var user = '<?php echo $loggedInuserName; ?>';
	var user_name = document.getElementById('user_name').value;
var user_city = document.getElementById('user_city').value;
var user_number = document.getElementById('user_number').value;
var user_email = document.getElementById('user_email').value;
var user_disposition = document.getElementById('user_disposition').value;
var user_comments = document.getElementById('user_comments').value;
var user_agent = document.getElementById('user_agent').value;
var user_field1 = document.getElementById('user_field1').value;
var user_field2 = document.getElementById('user_field2').value;
var user_field3 = document.getElementById('user_field3').value;
var user_field4 = document.getElementById('user_field4').value;
var user_field5 = document.getElementById('user_field5').value;

const namePattern = /^[a-zA-Z\s]+$/;
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
const mobilePattern = /^[0-9]{10}$/;
const allPattern = /^[a-zA-Z0-9\s]+$/;
const addressPattern = /^[a-zA-Z0-9\s.,-]+$/;

if (!namePattern.test(user_name) && user_name !='') {
                alert('Username is invalid. Please enter a valid username.');
				return true;
            }
if (!namePattern.test(user_city) && user_city !='') {
                alert('City is invalid. Please enter a valid city.');
				return true;
            }
if (!mobilePattern.test(user_number) && user_number !='') {
                alert('Mobile number is invalid. Please enter a valid 10digit mobile number.');
				return true;
            }
if (!emailPattern.test(user_email) && user_email !='') {
                alert('Email is invalid. Please enter a valid email.');
				return true;
            }
if (!namePattern.test(user_disposition) && user_disposition !='') {
                alert('Disposition is invalid. Please enter a valid disposition.');
				return true;
            }
if (!namePattern.test(user_agent) && user_agent !='') {
                alert('Agent is invalid. Please enter a valid agent.');
				return true;
            }
if ((!allPattern.test(user_field1) && user_field1 !='')||(!allPattern.test(user_field2) && user_field2 !='')||(!allPattern.test(user_field3) && user_field3 !='')||(!allPattern.test(user_field4) && user_field4 !='')||(!allPattern.test(user_field5) && user_field5 !='')) {
                alert('User fields are invalid. Please enter a valid user fields.');
				return true;
            }
if (!addressPattern.test(user_comments) && user_comments !='') {
                alert('Comments are invalid. Please enter a valid comments.');
				return true;
            }

document.getElementById('id04').style.display = 'none';
$.ajax({
			type: 'POST',
			url: "ajax/user_click.php",
			data: {
'user_name':user_name,
'user_city':user_city,
'user_number':user_number,
'user_email':user_email,
'user_disposition':user_disposition,
'user_comments':user_comments,
'user_agent':user_agent,
'user_field1':user_field1,
'user_field2':user_field2,
'user_field3':user_field3,
'user_field4':user_field4,
'user_field5':user_field5,
'user' : user

			},
			success: function(result) {
				//alert(result);
				console.log("Response: "+result);
			}
		});

document.getElementById('user_name').value = "";
document.getElementById('user_city').value = "";
document.getElementById('user_number').value = "";
document.getElementById('user_email').value = "";
document.getElementById('user_disposition').value = "";
document.getElementById('user_comments').value = "";
document.getElementById('user_agent').value = "";
document.getElementById('user_field1').value = "";
document.getElementById('user_field2').value = "";
document.getElementById('user_field3').value = "";
document.getElementById('user_field4').value = "";
document.getElementById('user_field5').value = "";
//document.getElementById('id04').style.display = 'none';

alert('New contact added successfully.');
return true;
}
</script>
<script>


	var header = document.getElementById("myHeader1");
	var sticky = header.offsetTop;

	function myFunction() {
		if (window.pageYOffset > sticky) {
			header.classList.add("sticky");
		} else {
			header.classList.remove("sticky");
		}
	}

	var header2 = document.getElementById("mainheader");
	var sticky2 = header2.offsetTop;

	function myFunction1() {
		if (window.pageYOffset > sticky2) {
			header2.classList.add("sticky");
		} else {
			header2.classList.remove("sticky");
		}

	}

	function transferAgent(){
		var user = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;

		var next_agent = document.getElementById('transferDIV').value;
		var blind = 'yes';
		alert('Call is transfering to agent:'+next_agent);
		//alert(next_agent);
		$.ajax({
			type: 'POST',
			url: "ajax/calltransfer.php",
			data: {
				'user': user,
				'next_agent' : next_agent,
				'blind' : blind,
				'campaign' : campaign
			},
			success: function(result) {
				console.log(result);
			}
		});
	}

	function conferenceAgent(){
		var user = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;

		var next_agent = document.getElementById('transferDIV').value;
		var blind = 'no';
		alert('Call is Conferencing to agent:'+next_agent);
		//alert(next_agent);
		$.ajax({
			type: 'POST',
			url: "ajax/calltransfer.php",
			data: {
				'user': user,
				'next_agent' : next_agent,
				'blind' : blind,
				'campaign' : campaign
			},
			success: function(result) {
				console.log(result);
			}
		});
	}

	function phonetransferAgent(){

		const mobileTransferNumber = document.getElementById("transfer_number").value;
        const pattern = /^\d+$/;
		if (pattern.test(mobileTransferNumber)) {
		var campaign = document.getElementById("update_campaign").value;

		var user = '<?php echo $extension; ?>';
		var transfer_number = document.getElementById('transfer_number').value;
		var blind = 'no';
		//alert(user);
		//alert(next_agent);
		$.ajax({
			type: 'POST',
			url: "ajax/calltransfer.php",
			data: {
				'user': user,
				'transfer_number' : transfer_number,
				'blind' : blind,
				'campaign' : campaign
			},
			success: function(result) {
				console.log(result);
			}
		});
		alert('Call is Conferencing to number:'+ transfer_number);
		}else{
			alert("Invalid number. Please enter valid number.");
			return true;
		}
		document.getElementById('transfer_number').value="";
	}


function blindtransferAgent(){

		const mobileTransferNumber = document.getElementById("transfer_number").value;
        const pattern = /^\d+$/;
		if (pattern.test(mobileTransferNumber)) {
		var campaign = document.getElementById("update_campaign").value;

		var user = '<?php echo $extension; ?>';
		var transfer_number = document.getElementById('transfer_number').value;
		var blind = 'yes';
		//alert(user);
		//alert(next_agent);
		$.ajax({
			type: 'POST',
			url: "ajax/calltransfer.php",
			data: {
				'user': user,
				'transfer_number' : transfer_number,
				'blind' : blind,
				'campaign' : campaign
			},
			success: function(result) {
				console.log(result);
			}
		});
		alert('Call is transfering to number:'+ transfer_number);
		}else{
			alert("Invalid number. Please enter valid number.");
			return true;
		}
		document.getElementById('transfer_number').value="";
	}


	function park() {
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		var status = document.getElementById('CallStatus').textContent;
		//alert(status);
		if((status == "DIAL") || (status == "ONCALL")){

		var x = document.getElementById("holdID");
		if (x.style.display === "none") {

			var user = '<?php echo $extension; ?>';
			var rejoin ='';

		$.ajax({
			type: 'POST',
			url: "ajax/call_park.php",
			data: {
				'user': user,
				'rejoin' : rejoin
			},
			success: function(result) {
				console.log(result);
			}
		});
			x.style.display = "block";
			document.getElementById('holdButton').style.backgroundColor = '#1A2B6D';
			document.getElementById('holdName').style.color = '#FFFF';

		} else {

			var user = '<?php echo $extension; ?>';
			var rejoin ='rejoin';

		$.ajax({
			type: 'POST',
			url: "ajax/call_park.php",
			data: {
				'user': user,
				'rejoin' : rejoin
			},
			success: function(result) {
				console.log(result);

			}
		});

		x.style.display = "none";
			document.getElementById('holdButton').style.backgroundColor = '#FFFF';
			document.getElementById('holdName').style.color = '#1A2B6D';

		}
		}else{
		}
	}

	function parkClose() {
		document.getElementById('id01').style.display = 'none';
		document.getElementById('parkButtonID').style.backgroundColor = '#FFFF';
		document.getElementById('parkName').style.color = '#1A2B6D';
	}

	function transfer() {
		CallTransferCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		var status = document.getElementById('CallStatus').textContent;

		if((status == "DIAL") || (status == "ONCALL")){
		document.getElementById('id02').style.display = 'block';
		document.getElementById('tranferButtonID').style.backgroundColor = '#1A2B6D';
		document.getElementById('tranferName').style.color = '#FFFF';
		}else{
		}
	}

	function transferClose() {
		document.getElementById('id02').style.display = 'none';
		document.getElementById('tranferButtonID').style.backgroundColor = '#FFFF';
		document.getElementById('tranferName').style.color = '#1A2B6D';
	}

	function email() {
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('id03').style.display = 'block';
		document.getElementById('emailButtonID').style.backgroundColor = '#1A2B6D';
		document.getElementById('emailName').style.color = '#FFFF';
	}

	function emailClose() {
		document.getElementById('id03').style.display = 'none';
		document.getElementById('emailButtonID').style.backgroundColor = '#FFFF';
		document.getElementById('emailName').style.color = '#1A2B6D';
	} //

	function goLiveCall() {
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('id04').style.display = 'block';
		document.getElementById('goliveButtonID').style.backgroundColor = '#1A2B6D';
		document.getElementById('goLiveName').style.color = '#FFFF';
	}

	function goLiveClose() {
		document.getElementById('id04').style.display = 'none';
		document.getElementById('goliveButtonID').style.backgroundColor = '#FFFF';
		document.getElementById('goLiveName').style.color = '#1A2B6D';
	}

	function whatsApp() {
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('id05').style.display = 'block';
		document.getElementById('whatsAppButtonID').style.backgroundColor = '#1A2B6D';
		document.getElementById('whatsAppName').style.color = '#FFFF';
	}

	function whatsAppClose() {
		document.getElementById('id05').style.display = 'none';
		document.getElementById('whatsAppButtonID').style.backgroundColor = '#FFFF';
		document.getElementById('whatsAppName').style.color = '#1A2B6D';
	}

function callLogClose(){
	document.getElementById("CallLogInfo").style.display = 'none';
	document.getElementById('fielddData').style.display = 'block';
	document.getElementById('callLogID').style.backgroundColor = '#FFFF';
	document.getElementById('callLogID').style.color = '#1A2B6D';

}
function CALLBACK_Close(){
	document.getElementById("callBackInfo").style.display = 'none';
	document.getElementById('fielddData').style.display = 'block';
	document.getElementById('callBackID').style.backgroundColor = '#FFFF';
	document.getElementById('callBackID').style.color = '#1A2B6D';
}
	function callLogData() {
		CallBackCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		var x = document.getElementById("CallLogInfo");
		if (x.style.display === "none") {
			document.getElementById('fielddData').style.display = 'none';

			x.style.display = "block";

			document.getElementById('callLogID').style.backgroundColor = '#1A2B6D';
			document.getElementById('callLogID').style.color = '#FFFF';
            document.getElementById('call2callInfo').style.display = 'none';
			document.getElementById('callBackInfo').style.display = 'none';

			document.getElementById('callBackID').style.backgroundColor = '#FFFF';
			document.getElementById('callBackID').style.color = '#1A2B6D';
		} else {
			x.style.display = "none";
			document.getElementById('callLogID').style.backgroundColor = '#FFFF';
			document.getElementById('callLogID').style.color = '#1A2B6D';

			document.getElementById('fielddData').style.display = 'block';

		}
	}


	function callBAckData() {
		CallBackCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		var x = document.getElementById("callBackInfo");
		if (x.style.display === "none") {
			document.getElementById('fielddData').style.display = 'none';
			x.style.display = "block";
			document.getElementById('callBackID').style.backgroundColor = '#1A2B6D';
			document.getElementById('callBackID').style.color = '#FFFF';
           document.getElementById('call2callInfo').style.display = 'none';
			document.getElementById('CallLogInfo').style.display = 'none';

			document.getElementById('callLogID').style.backgroundColor = '#FFFF';
			document.getElementById('callLogID').style.color = '#1A2B6D';
		} else {
			x.style.display = "none";
			document.getElementById('callBackID').style.backgroundColor = '#FFFF';
			document.getElementById('callBackID').style.color = '#1A2B6D';

			document.getElementById('fielddData').style.display = 'block';

		}
	}

	function home() {
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}

		document.getElementById('fielddData').style.display = 'block';

		document.getElementById('CallLogInfo').style.display = 'none';
		document.getElementById('callBackInfo').style.display = 'none';

		document.getElementById('callBackID').style.backgroundColor = '#FFFF';
		document.getElementById('callBackID').style.color = '#1A2B6D';

		document.getElementById('callLogID').style.backgroundColor = '#FFFF';
		document.getElementById('callLogID').style.color = '#1A2B6D';
	}

	function dialpad() {

		var dial_method = '<?php echo $dial_method; ?>';
		//alert(dial_method);
		if(dial_method == "Manual" || dial_method == "Blended" || dial_method == "Predictive" || dial_method == "Blended Predictive"){

		var status = document.getElementById('CallStatus').textContent;
		//alert(status);
		if(status == "IDLE" || status == "Manual"){

		var x = document.getElementById("id06");
		if (x.style.display === "none") {
			x.style.display = "block";
			document.getElementById('dialPadButton').style.backgroundColor = '#1A2B6D';
			document.getElementById('dialPadName').style.color = '#FFFF';
		} else {
			x.style.display = "none";
			document.getElementById('dialPadButton').style.backgroundColor = '#FFFF';
			document.getElementById('dialPadName').style.color = '#1A2B6D';
		}
		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}
		}else{
		}
	}

	function dialpadClose(){
		var x = document.getElementById("id06");
		x.style.display = "none";
	}

	function internalChat() {
		ChatRefreshCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('id07').style.display = 'block';
	}

	function internalChatClose() {
		document.getElementById('id07').style.display = 'none';
	}

	function script() {
		CallBackCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('call2callInfo').style.display = 'block';
		document.getElementById('fielddData').style.display = 'none';
	   document.getElementById('CallLogInfo').style.display = 'none';
	}

	function click2call_Close() {
		document.getElementById('call2callInfo').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';
	}

function breakpop() {

		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		breakpop1();
		document.getElementById('id10').style.display = 'block';
	}

	function breakClose() {
		document.getElementById('id10').style.display = 'none';
	}

function breakpop1(){

		var user = '<?php echo $loggedInuserName; ?>';
		var user_group = '<?php echo $user_group; ?>';

		$.ajax({
			type: 'POST',
			url: "ajax/breakCount.php",
			data: {
				'user': user,
				'user_group': user_group

			},
			success: function(result) {

				document.getElementById('BreakRefresh').innerHTML = result;

			}

		});


	}


	function internalChatWithAgent(agentName, status) {
		ChatRefreshCount();


		if (document.getElementById('AgentStatusChecking').checked) {

		} else {
			alert("Please make yourself ready.");
			return true;
		}
		if (status == 'offline') {
			alert(agentName + ' is now offline');
			return true;
		}

		var user = '<?php echo $loggedInuserName; ?>';
		//	alert("My name "+ user);
		$.ajax({
			type: 'POST',
			url: "ajax/internalChatWithAgent.php",
			data: {
				'user': user,
				'agentName' : agentName
			},
			success: function(result) {
				$('#internalChatHistroy').html(result);

	var scrollableDiv = document.getElementById("internalChatHistroy");
        scrollableDiv.scrollTop = scrollableDiv.scrollHeight;

			}
		});


		document.getElementById('id08').style.display = 'block';
		document.getElementById('id07').style.display = 'none';
		document.getElementById('ChatAgentName').innerHTML = agentName;
		document.getElementById('AgentNameToChat').value = agentName;


	}



	function openInternalChatAgentList() {
		ChatRefreshCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('id07').style.display = 'block';
		document.getElementById('id08').style.display = 'none';
	}
	var wage = document.getElementById("InternalChatWithAgentSend");
	wage.addEventListener("keydown", function(e) {
		if (e.keyCode === 13) {
			InternalChatWithAgentSend();
		}
	});


	function InternalChatWithAgentSend() {

		var user = '<?php echo $loggedInuserName; ?>';
		var campaignlog = document.getElementById("update_campaign").value;
		//alert(campaignlog);
		var agent_Name = document.getElementById('AgentNameToChat').value;
		var InternalChatContent = document.getElementById('InternalChatWithAgentSend').value;
		$.ajax({
			type: 'POST',
			url: "ajax/saveInternalChat.php",
			data: {
				'agent_Name': agent_Name,
				'InternalChatContent': InternalChatContent,
				'user': user,
				'campaignlog': campaignlog
			},
			success: function(result) {
				//alert(result);
				$('#chatLIST').append(result);
			}
		});
		document.getElementById('InternalChatWithAgentSend').value = '';

	}


	var count = 0;

	$(".digit123").on('click', function() {
		var num = ($(this).clone().children().remove().end().text());
		if (count < 11) {
			// alert(num.trim());
			$("#output").val(function() {
				return this.value + num.trim();
			});
			// $("#output").append();

			count++
		}
	});

	$('.fa-long-arrowData12-left').on('click', function() {
		$('#output:last-child').remove();
		count--;
	});

	function AgentStatusChecking(){

		var status = document.getElementById('CallStatus').textContent;

		if(status == "IDLE" || status == "BREAK" || status == "DIAL" || status == "Manual" || status == "ONCALL" || status == "WRAPUP"){

		if (document.getElementById('AgentStatusChecking').checked) {
			document.getElementById('fielddData').style.display = 'block';
		    document.getElementById('InActiveStatus').style.display = 'none';
			document.getElementById('pausebreak').style.display = 'none';

			var user = '<?php echo $loggedInuserName; ?>';
            var campaign = document.getElementById("update_campaign").value;


			$.ajax({
				type: 'POST',
				url: "ajax/updateUser_liveStstus.php",
				data: {
					'user': user,
					'campaign':campaign
				},
				success: function(result) {
					//alert(result);
				}
			});
		} else {

			$.ajax({
			type: 'POST',
			url: "ajax/PauseStatus.php",
			data: {
				'user': user
			},
			success: function(result) {

				//alert(result);

		       if (result == '0') {

		    document.getElementById('InActiveStatus').style.display = 'block';
			document.getElementById('fielddData').style.display = 'none';

			var user = '<?php echo $loggedInuserName; ?>';
			var campaign = document.getElementById("update_campaign").value;


			$.ajax({
				type: 'POST',
				url: "ajax/updateUser_liveStstus.php",
				data: {
					'user': user,
					'campaign':campaign
				},
				success: function(result) {
					//alert(result);
				}
			});

				}else {
					//alert("pause");
			var user = '<?php echo $loggedInuserName; ?>';
			var campaign = document.getElementById("update_campaign").value;

					$.ajax({
				type: 'POST',
				url: "ajax/oncall_pause.php",
				data: {
					'user': user,
					'campaign':campaign
				},
				success: function(result) {
					//alert(result);
				}
			});
		document.getElementById('fielddData').style.display = 'none';
		document.getElementById('pause_pop').style.display = 'block';
	    pauseFun(pauseCodeid,pauseCodename);

				}
			}
		});

		}

		}else{
			alert("Please end current call before go to pause mode.");
			return true;
		}
	}

	function pauseFun(pauseCodeid,pauseCodename) {
			//alert(pauseCode);
		if(pauseCodename == "Manual" || pauseCodeid == "Manual"){
		document.getElementById('pause_pop').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';
		}else{
		document.getElementById('pause_pop').style.display = 'none';
		document.getElementById('pausecode').innerHTML = pauseCodename;
		document.getElementById('InActiveStatus').style.display = 'block';
		document.getElementById('pausebreak').style.display = 'block';
		}
			var user = '<?php echo $loggedInuserName; ?>';
			var campaign = document.getElementById("update_campaign").value;

			$.ajax({
				type: 'POST',
				url: "ajax/updateUser_liveStstus.php",
				data: {
					'user': user,
					'pauseCodeid': pauseCodeid,
					'campaign':campaign
				},
				success: function(result) {
					//alert(result);
				}
			});

			}


	function allCallCount() {
		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;

		//alert(extension);

		$.ajax({
			type: 'POST',
			url: "ajax/allCallCount.php",
			data: {
				'user': user,
				'extension': extension,
				'campaign': campaign
			},
			success: function(result) {
				//alert(result);
				console.log(result);
				var data = result.split('=====');
				document.getElementById('inboundCallCount').innerHTML = data[0];
				document.getElementById('outboundCallCount').innerHTML = data[1];
				document.getElementById('dropCallCount').innerHTML = data[2];
				document.getElementById('inboundTable').innerHTML = data[3];
				document.getElementById('outboundTable').innerHTML = data[4];
				document.getElementById('dropTable').innerHTML = data[5];
				document.getElementById('queueTable').innerHTML = data[6];


				$('#inboundTABLEDATA').DataTable();
				$('#outboundTABLEDATA').DataTable();
				$('#dropTABLEDATA').DataTable();
				$('#queueTABLEDATA').DataTable();




			}
		});

      $.ajax({
      type: 'POST',
      url: "ajax/allAHT_Queue.php",
      data: {
        'user': user,
        'extension': extension,
		'campaign': campaign
      },
      success: function(result) {
        //alert(result);
        console.log(result);
        var data = result.split('=====');
        document.getElementById('AHTCount').innerHTML = data[0];
        document.getElementById('queueCallCount').innerHTML = data[1];
      }
    });

	}
	allCallCount();

function CallBackCount(){
		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		//alert(extension);

		$.ajax({
			type: 'POST',
			url: "ajax/CallBackCount.php",
			data: {
				'user': user,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');

				document.getElementById('callback2Table').innerHTML = data[0];
				document.getElementById('clickLogTable').innerHTML = data[1];

				$('#callBackDatatable').DataTable();
				$('#example').DataTable();
			}
		});
	}
	CallBackCount();

	function CallTransferCount(){
		var user = '<?php echo $loggedInuserName; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(extension);

		$.ajax({
			type: 'POST',
			url: "ajax/CallTransferCount.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {
				//alert(result);
				//var data = result.split('=====');
				document.getElementById('TransferID').innerHTML = result;

			}
		});
	}
	CallTransferCount();


	function ChatRefreshCount(){
		var user = '<?php echo $loggedInuserName; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(extension);

		$.ajax({
			type: 'POST',
			url: "ajax/chat_refresh.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {
				//alert(result);
				//var data = result.split('=====');
				document.getElementById('ChatRefreshID').innerHTML = result;

			}
		});
	}
	ChatRefreshCount();



	var wage = document.getElementById("output");
	wage.addEventListener("keydown", function(e) {
		if (e.keyCode === 13) {
			click2call();
		}
	});


//Call logs DIAL
function clickToCallLog(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		//alert(phoneNumber);
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{

		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;

		document.getElementById('CallLogInfo').style.display = 'none';
		document.getElementById('callBackInfo').style.display = 'none';
	    document.getElementById('fielddData').style.display = 'block';


		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];


			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}
	}
		}

		});
	}
//Callbacks DIAL
function clickToCallback(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{

		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;

		document.getElementById('callBackInfo').style.display = 'none';
		document.getElementById('CallLogInfo').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';


		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];


			}
		});

		$.ajax({
			type: 'POST',
			url: "ajax/CallBack.php",
			data: {
				'phoneNumber': phoneNumber
			},
			success: function(result) {
				//alert(result);

			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}

		}
		}

		});
	}
//Drop call DIAL
function clickToCalldrop(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{
		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;

		document.getElementById('dropCallPop').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';
		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];

			}
		});

		$.ajax({
			type: 'POST',
			url: "ajax/CallDrop.php",
			data: {
				'phoneNumber': phoneNumber
			},
			success: function(result) {
				//alert(result);

			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}

			}
		}

		});
	}


//Outbound call DIAL
function clickToCalloutbound(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{
		//alert(status);
		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;

		document.getElementById('outboundInfPop').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';
		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];

			}
		});

		$.ajax({
			type: 'POST',
			url: "ajax/CallDrop.php",
			data: {
				'phoneNumber': phoneNumber
			},
			success: function(result) {
				//alert(result);

			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}

		}
		}

		});
	}


//Inound call DIAL
function clickToCallinbound(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{
		//alert(status);
		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;

		document.getElementById('inboundInfPop').style.display = 'none';
		document.getElementById('fielddData').style.display = 'block';
		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];

			}
		});

		$.ajax({
			type: 'POST',
			url: "ajax/CallDrop.php",
			data: {
				'phoneNumber': phoneNumber
			},
			success: function(result) {
				//alert(result);

			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}

			}
		}

		});
	}





// Manual dial
	function click2call() {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = document.getElementById('output').value;
		/*alert(user);
		alert(extension);
		alert(phoneNumber);*/
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{

		const mobileNumber = document.getElementById("output").value;
        const pattern = /^[0-9]{10}$/;
		if (pattern.test(mobileNumber)) {

		document.getElementById('label_phone_number').value = phoneNumber;


		document.getElementById('id06').style.display = 'none';
		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];


			}
		});
		document.getElementById('output').value = '';

		}else{
			alert("Invalid mobile number. Please enter valid 10 digits mobile number.");
			return true;
		}

		}
		}

		});
	}



	function inboundInfo() {
		//getInboundInfo();
		allCallCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('inboundInfPop').style.display = 'block';
	} // outboundInfo
	function outboundInfo() {
		allCallCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('outboundInfPop').style.display = 'block';
	}

	function dropCallInfo() {
		allCallCount();
		if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}
		document.getElementById('dropCallPop').style.display = 'block';
	}
function queueCallInfo() {
	allCallCount();
		document.getElementById('queueCallPop').style.display = 'block';
	}

	function AgentStatus() {
		var user = '<?php echo $loggedInuserName; ?>';
		var extension  = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(extension);
		$.ajax({
			type: 'POST',
			url: "ajax/CallStatus.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {

				//alert(result);
				console.log(result);
				var result=result.trim();
		       if (result == 'READY') {
					var displayStatus = "READY";
					//alert(result);
					$.ajax({
			type: 'POST',
			url: "ajax/idle_api.php",
			data: {
				'extension': extension
			},
			success: function(result) {

				console.log(result);



			}
		});
				}
				else if (result == 'PAUSED') {
					var displayStatus = "PAUSED";
					allCallCount();
				}
				else {
					var displayStatus = result;
				}

				document.getElementById('agentStatus').innerHTML = displayStatus;

			}
		});


	}

	const interval = setInterval(function() {
		AgentStatus()

	}, 1000);


	function chatCount() {
		var user = '<?php echo $loggedInuserName; ?>';
		//alert(user);
		$.ajax({
			type: 'POST',
			url: "ajax/ChatCount.php",
			data: {
				'user': user
			},
			success: function(result) {

				//alert(result);
				if(result == "0"){
					document.getElementById('ChatMsgCount').innerHTML = '0';
				}else{
			document.getElementById('ChatMsgCount').innerHTML = result;
			}

			}
		});
	}
		setInterval(function() {
		chatCount()
	}, 1000);

function activeLeadsCount() {
		var user = '<?php echo $loggedInuserName; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(campaign);
		$.ajax({
			type: 'POST',
			url: "ajax/activeLeadsCount.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {
			//alert(result);
			if(result == "0"){
					document.getElementById('ActiveLeadCount').innerHTML = '0';
				}else{
			document.getElementById('ActiveLeadCount').innerHTML = result;
			}
			}
		});
	}
setInterval(function() { activeLeadsCount() }, 1000);

	function announcementCount() {
		var user = '<?php echo $loggedInuserName; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(user);
		$.ajax({
			type: 'POST',
			url: "ajax/announcement.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {

				//alert(result);

			document.getElementById('announce').innerHTML = result;


			}
		});
	}
		setInterval(function() {
		announcementCount()
	}, 1000);


	function ExtensionStatus() {
		var user = '<?php echo $loggedInuserName; ?>';
		//alert(user);
		$.ajax({
			type: 'POST',
			url: "ajax/call.php",
			data: {
				'user': user
			},
			success: function(result) {

				//alert(result);


			}
		});
	}
	ExtensionStatus();


	function callStatus() {
		var user = '<?php echo $loggedInuserName; ?>';
    var user_group = "<?php echo $user_group; ?>";
		//alert(user);
		$.ajax({
			type: 'POST',
			url: "ajax/CallStatus_wrap.php",
			data: {
				'user': user
			},
			success: function(result) {
				//console.log(result);
			//alert(result);
				var status = result.split('=====');
				if ((status[0] == 'ONCALL' || status[0] == 'WRAPUP') && status[1] == '0') {
					//var audio = new Audio('Anouncement.mp3');
                         //audio.play();
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // alert(this.responseText);
       const data = this.responseText.split("||");

document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];


    }
  };
  xhttp.open("GET", "demo_get.php?user="+user, true);
  xhttp.send();
				}

				if(status[0] =="ONCALL" || status[0] =="DIAL"){
					document.getElementById('check_display').style.display = 'none';
					document.getElementById('cust_hang').style.display = 'none';
				document.getElementById("StatusCall").style.backgroundColor = "#29a329";
				document.getElementById("liveCallNotification").style.color = "#ffffff";
				document.getElementById("CallStatus").style.color = "#ffffff";

				document.getElementById("hang_up").style.backgroundColor = "#29a329";
				document.getElementById("hang_up").style.color = "#ffffff";
				//allCallCount();
				}else if(status[0] =="WRAPUP"){
					/*
          if(user_group=="Smartcoin Group")
          {
            Auto_Voicefile_Play();
          }
          else if(user_group=="Smartcoin Sales")
          {
            Auto_Hangup_Wrapup();
          }
					 */
					document.getElementById('check_display').style.display = 'none';
					document.getElementById('cust_hang').style.display = 'block';
					document.getElementById("StatusCall").style.backgroundColor = "#e6e600";
					document.getElementById("liveCallNotification").style.color = "black";
				document.getElementById("CallStatus").style.color = "black";

				document.getElementById("hang_up").style.backgroundColor = "#29a329";
				document.getElementById("hang_up").style.color = "#ffffff";
				//allCallCount();
				}else{
					document.getElementById('cust_hang').style.display = 'none';
					document.getElementById('check_display').style.display = 'block';
			    document.getElementById("StatusCall").style.backgroundColor = "#F0F0F0";
				document.getElementById("liveCallNotification").style.color = "red";
				document.getElementById("CallStatus").style.color = "red";

				document.getElementById("hang_up").style.backgroundColor = "#ffff";
				document.getElementById("hang_up").style.color = "red";
document.getElementById('first_name').value='';
document.getElementById('middle_name').value='';
document.getElementById('last_name').value='';
document.getElementById('address1').value='';
document.getElementById('address2').value='';
document.getElementById('address3').value='';
document.getElementById('label_city').value='';
document.getElementById('label_state').value='';
document.getElementById('label_province').value='';
document.getElementById('label_gender').value='';
document.getElementById('label_phone_number').value='';
document.getElementById('label_phone_code').value='';
document.getElementById('alt_phone1').value='';
document.getElementById('alt_phone2').value='';
document.getElementById('alt_phone3').value='';
document.getElementById('alt_phone4').value='';
document.getElementById('alt_phone5').value='';
document.getElementById('alt_phone6').value='';
document.getElementById('alt_phone7').value='';
document.getElementById('alt_phone8').value='';
document.getElementById('comments').value='';
document.getElementById('label_field1').value='';
document.getElementById('label_field2').value='';
document.getElementById('label_field3').value='';
document.getElementById('label_field4').value='';
document.getElementById('label_field5').value='';
document.getElementById('label_field6').value='';
document.getElementById('label_field7').value='';
document.getElementById('label_field8').value='';
document.getElementById('label_field9').value='';
document.getElementById('label_field10').value='';
document.getElementById('label_field11').value='';
document.getElementById('label_field12').value='';
document.getElementById('label_field13').value='';
document.getElementById('label_field14').value='';
document.getElementById('label_field15').value='';
document.getElementById('label_field16').value='';
document.getElementById('label_field17').value='';
document.getElementById('label_field18').value='';
document.getElementById('label_field19').value='';
document.getElementById('label_field20').value='';
document.getElementById('label_field21').value='';
document.getElementById('label_field22').value='';
document.getElementById('label_field23').value='';
document.getElementById('label_field24').value='';
document.getElementById('label_field25').value='';
document.getElementById('label_field26').value='';
document.getElementById('label_field27').value='';
document.getElementById('label_field28').value='';
document.getElementById('dropdown1_list').value='';
document.getElementById('dropdown2_list').value='';
document.getElementById('dropdown3_list').value='';
document.getElementById('dropdown4_list').value='';
document.getElementById('dropdown5_list').value='';
document.getElementById('dropdown6_list').value='';
document.getElementById('dropdown7_list').value='';
document.getElementById('callback_date').value = '';
document.getElementById('callback_time').value = '';
document.getElementById('me_user').value = '';
document.getElementById('user_name').value = "";
document.getElementById('user_city').value = "";
document.getElementById('user_number').value = "";
document.getElementById('user_email').value = "";
document.getElementById('user_disposition').value = "";
document.getElementById('user_comments').value = "";
document.getElementById('user_agent').value = "";
document.getElementById('user_field1').value = "";
document.getElementById('user_field2').value = "";
document.getElementById('user_field3').value = "";
document.getElementById('user_field4').value = "";
document.getElementById('user_field5').value = "";
document.getElementById("drop_down1_value").value="";
document.getElementById("drop_down2_value").value="";
document.getElementById("drop_down3_value").value="";
document.getElementById("drop_down4_value").value="";
document.getElementById("drop_down5_value").value="";
document.getElementById("sub_drop_down1_value").value="";
document.getElementById("sub_drop_down2_value").value="";
document.getElementById("sub_drop_down3_value").value="";
document.getElementById("sub_drop_down4_value").value="";
document.getElementById("sub_drop_down5_value").value="";
document.getElementById("sub_sub_drop_down1_value").value="";
document.getElementById("sub_sub_drop_down2_value").value="";
document.getElementById("sub_sub_drop_down3_value").value="";
document.getElementById("sub_sub_drop_down4_value").value="";
document.getElementById("sub_sub_drop_down5_value").value="";
				}

				document.getElementById('CallStatus').innerHTML = status[0];
				document.getElementById('camps').innerHTML = status[2];
			}
		});
	}
	setInterval(function() {
		callStatus()
	}, 1000);


function callbackclose() {
		document.getElementById('id11').style.display = 'none';
	}


	function callHangupFun(Disposition, LoginUser) {
			//alert(Disposition);
		var status = document.getElementById('CallStatus').textContent;
		var user_group = '<?php echo $user_group; ?>';
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		var drop_down1_value = document.getElementById('drop_down1_value').value;
		var sub_drop_down1_value = document.getElementById('sub_drop_down1_value').value;
		var sub_sub_drop_down1_value = document.getElementById('sub_drop_down1_value').value;
		if(drop_down1_value==""){
				 alert('Please Select The Disposition Before Dispose The Call.');
	          		return true;
		}
                if(sub_drop_down1_value==""){
                                 alert('Please Select The Sub Disposition Before Dispose The Call.');
                                return true;
                }
                if(sub_sub_drop_down1_value==""){
                                 alert('Please Select The Sub Sub Disposition Before Dispose The Call.');
                                return true;
                }
		//alert(status);
		if(status == "DIAL" || status =="ONCALL" || status =="WRAPUP"){

				$.ajax({
			type: 'POST',
			url: "ajax/dispo_hang.php",
			data: {
				'extension': extension,
				'campaign' : campaign,
				'dropdown1_list' : drop_down1_value,
				'drop_down1_value' : drop_down1_value,
				'sub_drop_down1_value' : sub_drop_down1_value,
				'sub_sub_drop_down1_value' : sub_sub_drop_down1_value
			},
			success: function(result) {
        			console.log(result);

			}
		});
		document.getElementById('dispoPop').style.display = "block";
		}
		dispoFun(Disposition, LoginUser);
	}



	function dispoFun(Disposition, LoginUser){
		var disposition = document.getElementById('dropdown1_list').value;
		var user_group = '<?php echo $user_group; ?>';


		//alert(Disposition);
	     updateTimer();
		 document.getElementById('holdID').style.display = "none";
		document.getElementById('dispoPop').style.display = "none";
		allCallCount();


			if(Disposition == "CALLBACK"){

				document.getElementById('id11').style.display = 'block';
              }else{
			 // alert(Disposition);
		var DispositionVal = Disposition;
		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		var phoneNumber = document.getElementById('label_phone_number').value;

var first_name = document.getElementById('first_name').value;
var middle_name = document.getElementById('middle_name').value;
var last_name = document.getElementById('last_name').value;
var address1 = document.getElementById('address1').value;
var address2 = document.getElementById('address2').value;
var address3 = document.getElementById('address3').value;
var label_city = document.getElementById('label_city').value;
var label_state = document.getElementById('label_state').value;
var label_province = document.getElementById('label_province').value;
var label_gender = document.getElementById('label_gender').value;
var label_phone_number = document.getElementById('label_phone_number').value;
var label_phone_code = document.getElementById('label_phone_code').value;
var alt_phone1 = document.getElementById('alt_phone1').value;
var alt_phone2 = document.getElementById('alt_phone2').value;
var alt_phone3 = document.getElementById('alt_phone3').value;
var alt_phone4 = document.getElementById('alt_phone4').value;
var alt_phone5 = document.getElementById('alt_phone5').value;
var alt_phone6 = document.getElementById('alt_phone6').value;
var alt_phone7 = document.getElementById('alt_phone7').value;
var alt_phone8 = document.getElementById('alt_phone8').value;
var comments = document.getElementById('comments').value;
var label_field1 = document.getElementById('label_field1').value;
var label_field2 = document.getElementById('label_field2').value;
var label_field3 = document.getElementById('label_field3').value;
var label_field4 = document.getElementById('label_field4').value;
var label_field5 = document.getElementById('label_field5').value;
var label_field6 = document.getElementById('label_field6').value;
var label_field7 = document.getElementById('label_field7').value;
var label_field8 = document.getElementById('label_field8').value;
var label_field9 = document.getElementById('label_field9').value;
var label_field10 = document.getElementById('label_field10').value;
var label_field11 = document.getElementById('label_field11').value;
var label_field12 = document.getElementById('label_field12').value;
var label_field13 = document.getElementById('label_field13').value;
var label_field14 = document.getElementById('label_field14').value;
var label_field15 = document.getElementById('label_field15').value;
var label_field16 = document.getElementById('label_field16').value;
var label_field17 = document.getElementById('label_field17').value;
var label_field18 = document.getElementById('label_field18').value;
var label_field19 = document.getElementById('label_field19').value;
var label_field20 = document.getElementById('label_field20').value;
var label_field21 = document.getElementById('label_field21').value;
var label_field22 = document.getElementById('label_field22').value;
var label_field23 = document.getElementById('label_field23').value;
var label_field24 = document.getElementById('label_field24').value;
var label_field25 = document.getElementById('label_field25').value;
var label_field26 = document.getElementById('label_field26').value;
var label_field27 = document.getElementById('label_field27').value;
var label_field28 = document.getElementById('label_field28').value;
var dropdown1_list = document.getElementById('dropdown1_list').value;
var dropdown2_list = document.getElementById('dropdown2_list').value;
var dropdown3_list = document.getElementById('dropdown3_list').value;
var dropdown4_list = document.getElementById('dropdown4_list').value;
var dropdown5_list = document.getElementById('dropdown5_list').value;
var dropdown6_list = document.getElementById('dropdown6_list').value;
var dropdown7_list = document.getElementById('dropdown7_list').value;
var callback_date = "";
var callback_time = "";
var user_me = "";
var drop_down1_value = document.getElementById("drop_down1_value").value;
var drop_down2_value = document.getElementById("drop_down2_value").value;
var drop_down3_value = document.getElementById("drop_down3_value").value;
var drop_down4_value = document.getElementById("drop_down4_value").value;
var drop_down5_value = document.getElementById("drop_down5_value").value;
var sub_drop_down1_value = document.getElementById("sub_drop_down1_value").value;
var sub_drop_down2_value = document.getElementById("sub_drop_down2_value").value;
var sub_drop_down3_value = document.getElementById("sub_drop_down3_value").value;
var sub_drop_down4_value = document.getElementById("sub_drop_down4_value").value;
var sub_drop_down5_value = document.getElementById("sub_drop_down5_value").value;
var sub_sub_drop_down1_value = document.getElementById("sub_sub_drop_down1_value").value;
var sub_sub_drop_down2_value = document.getElementById("sub_sub_drop_down2_value").value;
var sub_sub_drop_down3_value = document.getElementById("sub_sub_drop_down3_value").value;
var sub_sub_drop_down4_value = document.getElementById("sub_sub_drop_down4_value").value;
var sub_sub_drop_down5_value = document.getElementById("sub_sub_drop_down5_value").value;

if(document.getElementById("go_pause").checked){
var go_pause = document.getElementById('go_pause').value;
}else{
	var go_pause = '';
}

		//alert("callback_date: "+callback_date+" callback_time: "+callback_time);
		//alert(user_me);
		$.ajax({
			type: 'POST',
			url: "ajax/HANGUP.php",
			data: {
				'user': user,
				'DispositionVal': DispositionVal,
				'phoneNumber': phoneNumber,
				'extension': extension,
				'campaign' : campaign,
				'first_name' : first_name,
'middle_name' : middle_name,
'last_name' : last_name,
'address1' : address1,
'address2' : address2,
'address3' : address3,
'label_city' : label_city,
'label_state' : label_state,
'label_province' : label_province,
'label_gender' : label_gender,
'label_phone_number' : label_phone_number,
'label_phone_code' : label_phone_code,
'alt_phone1' : alt_phone1,
'alt_phone2' : alt_phone2,
'alt_phone3' : alt_phone3,
'alt_phone4' : alt_phone4,
'alt_phone5' : alt_phone5,
'alt_phone6' : alt_phone6,
'alt_phone7' : alt_phone7,
'alt_phone8' : alt_phone8,
'comments' : comments,
'label_field1' : label_field1,
'label_field2' : label_field2,
'label_field3' : label_field3,
'label_field4' : label_field4,
'label_field5' : label_field5,
'label_field6' : label_field6,
'label_field7' : label_field7,
'label_field8' : label_field8,
'label_field9' : label_field9,
'label_field10' : label_field10,
'label_field11' : label_field11,
'label_field12' : label_field12,
'label_field13' : label_field13,
'label_field14' : label_field14,
'label_field15' : label_field15,
'label_field16' : label_field16,
'label_field17' : label_field17,
'label_field18' : label_field18,
'label_field19' : label_field19,
'label_field20' : label_field20,
'label_field21' : label_field21,
'label_field22' : label_field22,
'label_field23' : label_field23,
'label_field24' : label_field24,
'label_field25' : label_field25,
'label_field26' : label_field26,
'label_field27' : label_field27,
'label_field28' : label_field28,
'dropdown1_list' : dropdown1_list,
'dropdown2_list' : dropdown2_list,
'dropdown3_list' : dropdown3_list,
'dropdown4_list' : dropdown4_list,
'dropdown5_list' : dropdown5_list,
'dropdown6_list' : dropdown6_list,
'dropdown7_list' : dropdown7_list,
'callback_date' : callback_date,
'callback_time' : callback_time,
'user_me' : user_me,
'drop_down1_value' : drop_down1_value,
'drop_down2_value' : drop_down2_value,
'drop_down3_value' : drop_down3_value,
'drop_down4_value' : drop_down4_value,
'drop_down5_value' : drop_down5_value,
'sub_drop_down1_value' : sub_drop_down1_value,
'sub_drop_down2_value' : sub_drop_down2_value,
'sub_drop_down3_value' : sub_drop_down3_value,
'sub_drop_down4_value' : sub_drop_down4_value,
'sub_drop_down5_value' : sub_drop_down5_value,
'sub_sub_drop_down1_value' : sub_sub_drop_down1_value,
'sub_sub_drop_down2_value' : sub_sub_drop_down2_value,
'sub_sub_drop_down3_value' : sub_sub_drop_down3_value,
'sub_sub_drop_down4_value' : sub_sub_drop_down4_value,
'sub_sub_drop_down5_value' : sub_sub_drop_down5_value,
'go_pause' : go_pause
},
			success: function(result) {
				//alert("Response: "+result);
				console.log("Response: "+result);
				document.getElementById("btn_hangup").disabled = true;
				document.getElementById('btn_hangup').className = "btnaaInput2";
				document.getElementById('myHangup').style.display = "none";

			}
		});

		document.getElementById('first_name').value='';
document.getElementById('middle_name').value='';
document.getElementById('last_name').value='';
document.getElementById('address1').value='';
document.getElementById('address2').value='';
document.getElementById('address3').value='';
document.getElementById('label_city').value='';
document.getElementById('label_state').value='';
document.getElementById('label_province').value='';
document.getElementById('label_gender').value='';
document.getElementById('label_phone_number').value='';
document.getElementById('label_phone_code').value='';
document.getElementById('alt_phone1').value='';
document.getElementById('alt_phone2').value='';
document.getElementById('alt_phone3').value='';
document.getElementById('alt_phone4').value='';
document.getElementById('alt_phone5').value='';
document.getElementById('alt_phone6').value='';
document.getElementById('alt_phone7').value='';
document.getElementById('alt_phone8').value='';
document.getElementById('comments').value='';
document.getElementById('label_field1').value='';
document.getElementById('label_field2').value='';
document.getElementById('label_field3').value='';
document.getElementById('label_field4').value='';
document.getElementById('label_field5').value='';
document.getElementById('label_field6').value='';
document.getElementById('label_field7').value='';
document.getElementById('label_field8').value='';
document.getElementById('label_field9').value='';
document.getElementById('label_field10').value='';
document.getElementById('label_field11').value='';
document.getElementById('label_field12').value='';
document.getElementById('label_field13').value='';
document.getElementById('label_field14').value='';
document.getElementById('label_field15').value='';
document.getElementById('label_field16').value='';
document.getElementById('label_field17').value='';
document.getElementById('label_field18').value='';
document.getElementById('label_field19').value='';
document.getElementById('label_field20').value='';
document.getElementById('label_field21').value='';
document.getElementById('label_field22').value='';
document.getElementById('label_field23').value='';
document.getElementById('label_field24').value='';
document.getElementById('label_field25').value='';
document.getElementById('label_field26').value='';
document.getElementById('label_field27').value='';
document.getElementById('label_field28').value='';
document.getElementById('dropdown1_list').value='';
document.getElementById('dropdown2_list').value='';
document.getElementById('dropdown3_list').value='';
document.getElementById('dropdown4_list').value='';
document.getElementById('dropdown5_list').value='';
document.getElementById('dropdown6_list').value='';
document.getElementById('dropdown7_list').value='';
document.getElementById('callback_date').value = '';
document.getElementById('callback_time').value = '';
document.getElementById('me_user').value = '';
document.getElementById('user_name').value = "";
document.getElementById('user_city').value = "";
document.getElementById('user_number').value = "";
document.getElementById('user_email').value = "";
document.getElementById('user_disposition').value = "";
document.getElementById('user_comments').value = "";
document.getElementById('user_agent').value = "";
document.getElementById('user_field1').value = "";
document.getElementById('user_field2').value = "";
document.getElementById('user_field3').value = "";
document.getElementById('user_field4').value = "";
document.getElementById('user_field5').value = "";
document.getElementById("drop_down1_value").value="";
document.getElementById("drop_down2_value").value="";
document.getElementById("drop_down3_value").value="";
document.getElementById("drop_down4_value").value="";
document.getElementById("drop_down5_value").value="";
document.getElementById("sub_drop_down1_value").value="";
document.getElementById("sub_drop_down2_value").value="";
document.getElementById("sub_drop_down3_value").value="";
document.getElementById("sub_drop_down4_value").value="";
document.getElementById("sub_drop_down5_value").value="";
document.getElementById("sub_sub_drop_down1_value").value="";
document.getElementById("sub_sub_drop_down2_value").value="";
document.getElementById("sub_sub_drop_down3_value").value="";
document.getElementById("sub_sub_drop_down4_value").value="";
document.getElementById("sub_sub_drop_down5_value").value="";

if(go_pause == "GOPAUSE"){
				document.getElementById('AgentStatusChecking').checked = false;
				AgentStatusChecking();
              }
 document.getElementById('go_pause').checked = false;
       }

    loginhours();

}

    function AutoDispose(){

    var Disposition = 'ANSWERED';
    var LoginUser = "<?php echo $loggedInuserName; ?>";
    var auto_dispo_status = "Agent does not dispose on time";

		var disposition = document.getElementById('dropdown2_list').value;
		var user_group = '<?php echo $user_group; ?>';
   
		updateTimer();
		 document.getElementById('holdID').style.display = "none";
		document.getElementById('dispoPop').style.display = "none";
		allCallCount();


			if(Disposition == "CALLBACK"){

				document.getElementById('id11').style.display = 'block';
              }else{
			 // alert(Disposition);
		var DispositionVal = Disposition;
		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		var phoneNumber = document.getElementById('label_phone_number').value;

var first_name = document.getElementById('first_name').value;
var middle_name = document.getElementById('middle_name').value;
var last_name = document.getElementById('last_name').value;
var address1 = document.getElementById('address1').value;
var address2 = document.getElementById('address2').value;
var address3 = document.getElementById('address3').value;
var label_city = document.getElementById('label_city').value;
var label_state = document.getElementById('label_state').value;
var label_province = document.getElementById('label_province').value;
var label_gender = document.getElementById('label_gender').value;
var label_phone_number = document.getElementById('label_phone_number').value;
var label_phone_code = document.getElementById('label_phone_code').value;
var alt_phone1 = document.getElementById('alt_phone1').value;
var alt_phone2 = document.getElementById('alt_phone2').value;
var alt_phone3 = document.getElementById('alt_phone3').value;
var alt_phone4 = document.getElementById('alt_phone4').value;
var alt_phone5 = document.getElementById('alt_phone5').value;
var alt_phone6 = document.getElementById('alt_phone6').value;
var alt_phone7 = document.getElementById('alt_phone7').value;
var alt_phone8 = document.getElementById('alt_phone8').value;
var comments = document.getElementById('comments').value;
var label_field1 = document.getElementById('label_field1').value;
var label_field2 = document.getElementById('label_field2').value;
var label_field3 = document.getElementById('label_field3').value;
var label_field4 = document.getElementById('label_field4').value;
var label_field5 = document.getElementById('label_field5').value;
var label_field6 = document.getElementById('label_field6').value;
var label_field7 = document.getElementById('label_field7').value;
var label_field8 = document.getElementById('label_field8').value;
var label_field9 = document.getElementById('label_field9').value;
var label_field10 = document.getElementById('label_field10').value;
var label_field11 = document.getElementById('label_field11').value;
var label_field12 = document.getElementById('label_field12').value;
var label_field13 = document.getElementById('label_field13').value;
var label_field14 = document.getElementById('label_field14').value;
var label_field15 = document.getElementById('label_field15').value;
var label_field16 = document.getElementById('label_field16').value;
var label_field17 = document.getElementById('label_field17').value;
var label_field18 = document.getElementById('label_field18').value;
var label_field19 = document.getElementById('label_field19').value;
var label_field20 = document.getElementById('label_field20').value;
var label_field21 = document.getElementById('label_field21').value;
var label_field22 = document.getElementById('label_field22').value;
var label_field23 = document.getElementById('label_field23').value;
var label_field24 = document.getElementById('label_field24').value;
var label_field25 = document.getElementById('label_field25').value;
var label_field26 = document.getElementById('label_field26').value;
var label_field27 = document.getElementById('label_field27').value;
var label_field28 = document.getElementById('label_field28').value;
var dropdown1_list = document.getElementById('dropdown1_list').value;
var dropdown2_list = document.getElementById('dropdown2_list').value;
var dropdown3_list = document.getElementById('dropdown3_list').value;
var dropdown4_list = document.getElementById('dropdown4_list').value;
var dropdown5_list = document.getElementById('dropdown5_list').value;
var dropdown6_list = document.getElementById('dropdown6_list').value;
var dropdown7_list = document.getElementById('dropdown7_list').value;
var callback_date = "";
var callback_time = "";
var user_me = "";
var drop_down1_value = document.getElementById("drop_down1_value").value;
var drop_down2_value = document.getElementById("drop_down2_value").value;
var drop_down3_value = document.getElementById("drop_down3_value").value;
var drop_down4_value = document.getElementById("drop_down4_value").value;
var drop_down5_value = document.getElementById("drop_down5_value").value;
var sub_drop_down1_value = document.getElementById("sub_drop_down1_value").value;
var sub_drop_down2_value = document.getElementById("sub_drop_down2_value").value;
var sub_drop_down3_value = document.getElementById("sub_drop_down3_value").value;
var sub_drop_down4_value = document.getElementById("sub_drop_down4_value").value;
var sub_drop_down5_value = document.getElementById("sub_drop_down5_value").value;
var sub_sub_drop_down1_value = document.getElementById("sub_sub_drop_down1_value").value;
var sub_sub_drop_down2_value = document.getElementById("sub_sub_drop_down2_value").value;
var sub_sub_drop_down3_value = document.getElementById("sub_sub_drop_down3_value").value;
var sub_sub_drop_down4_value = document.getElementById("sub_sub_drop_down4_value").value;
var sub_sub_drop_down5_value = document.getElementById("sub_sub_drop_down5_value").value;

if(document.getElementById("go_pause").checked){
var go_pause = document.getElementById('go_pause').value;
}else{
	var go_pause = '';
}

if((campaign == "CollectionAgency" || campaign == "CustomerRetention") && (drop_down1_value == "" && sub_drop_down1_value == "")){

	alert('Please Select Disposition & Sub disposition Before Dispose The Call.');
	return true;
}else{
	var drop_down1_value = document.getElementById("drop_down1_value").value;
	var sub_drop_down1_value = document.getElementById("sub_drop_down1_value").value;
}

if(campaign == "CollectionAgency" && drop_down1_value == "PTP" && sub_drop_down1_value == "Yes" && first_name == "" && middle_name == ""){
	alert('Please Enter PTP Amount & Total Amount Before Dispose The Call.');
	return true;
}else{
	var first_name = document.getElementById('first_name').value;
var middle_name = document.getElementById('middle_name').value;
}
		//alert("callback_date: "+callback_date+" callback_time: "+callback_time);
		//alert(user_me);
		$.ajax({
			type: 'POST',
			url: "ajax/HANGUP.php",
			data: {
				'user': user,
				'DispositionVal': DispositionVal,
				'phoneNumber': phoneNumber,
				'extension': extension,
				'campaign' : campaign,
				'first_name' : first_name,
'middle_name' : middle_name,
'last_name' : last_name,
'address1' : address1,
'address2' : address2,
'address3' : address3,
'label_city' : label_city,
'label_state' : label_state,
'label_province' : label_province,
'label_gender' : label_gender,
'label_phone_number' : label_phone_number,
'label_phone_code' : label_phone_code,
'alt_phone1' : alt_phone1,
'alt_phone2' : alt_phone2,
'alt_phone3' : alt_phone3,
'alt_phone4' : alt_phone4,
'alt_phone5' : alt_phone5,
'alt_phone6' : alt_phone6,
'alt_phone7' : alt_phone7,
'alt_phone8' : alt_phone8,
'comments' : comments,
'label_field1' : label_field1,
'label_field2' : label_field2,
'label_field3' : label_field3,
'label_field4' : label_field4,
'label_field5' : label_field5,
'label_field6' : label_field6,
'label_field7' : label_field7,
'label_field8' : label_field8,
'label_field9' : label_field9,
'label_field10' : label_field10,
'label_field11' : label_field11,
'label_field12' : label_field12,
'label_field13' : label_field13,
'label_field14' : label_field14,
'label_field15' : label_field15,
'label_field16' : label_field16,
'label_field17' : label_field17,
'label_field18' : label_field18,
'label_field19' : label_field19,
'label_field20' : label_field20,
'label_field21' : label_field21,
'label_field22' : label_field22,
'label_field23' : label_field23,
'label_field24' : label_field24,
'label_field25' : label_field25,
'label_field26' : label_field26,
'label_field27' : label_field27,
'label_field28' : label_field28,
'dropdown1_list' : dropdown1_list,
'dropdown2_list' : dropdown2_list,
'dropdown3_list' : dropdown3_list,
'dropdown4_list' : dropdown4_list,
'dropdown5_list' : dropdown5_list,
'dropdown6_list' : dropdown6_list,
'dropdown7_list' : dropdown7_list,
'callback_date' : callback_date,
'callback_time' : callback_time,
'user_me' : user_me,
'drop_down1_value' : drop_down1_value,
'drop_down2_value' : drop_down2_value,
'drop_down3_value' : drop_down3_value,
'drop_down4_value' : drop_down4_value,
'drop_down5_value' : drop_down5_value,
'sub_drop_down1_value' : sub_drop_down1_value,
'sub_drop_down2_value' : sub_drop_down2_value,
'sub_drop_down3_value' : sub_drop_down3_value,
'sub_drop_down4_value' : sub_drop_down4_value,
'sub_drop_down5_value' : sub_drop_down5_value,
'sub_sub_drop_down1_value' : sub_sub_drop_down1_value,
'sub_sub_drop_down2_value' : sub_sub_drop_down2_value,
'sub_sub_drop_down3_value' : sub_sub_drop_down3_value,
'sub_sub_drop_down4_value' : sub_sub_drop_down4_value,
'sub_sub_drop_down5_value' : sub_sub_drop_down5_value,
'go_pause' : go_pause,
'auto_dispo_status' : auto_dispo_status
},
			success: function(result) {
				//alert("Response: "+result);
				console.log("Response: "+result);
				document.getElementById("btn_hangup").disabled = true;
				document.getElementById('btn_hangup').className = "btnaaInput2";
				document.getElementById('myHangup').style.display = "none";

			}
		});

		document.getElementById('first_name').value='';
document.getElementById('middle_name').value='';
document.getElementById('last_name').value='';
document.getElementById('address1').value='';
document.getElementById('address2').value='';
document.getElementById('address3').value='';
document.getElementById('label_city').value='';
document.getElementById('label_state').value='';
document.getElementById('label_province').value='';
document.getElementById('label_gender').value='';
document.getElementById('label_phone_number').value='';
document.getElementById('label_phone_code').value='';
document.getElementById('alt_phone1').value='';
document.getElementById('alt_phone2').value='';
document.getElementById('alt_phone3').value='';
document.getElementById('alt_phone4').value='';
document.getElementById('alt_phone5').value='';
document.getElementById('alt_phone6').value='';
document.getElementById('alt_phone7').value='';
document.getElementById('alt_phone8').value='';
document.getElementById('comments').value='';
document.getElementById('label_field1').value='';
document.getElementById('label_field2').value='';
document.getElementById('label_field3').value='';
document.getElementById('label_field4').value='';
document.getElementById('label_field5').value='';
document.getElementById('label_field6').value='';
document.getElementById('label_field7').value='';
document.getElementById('label_field8').value='';
document.getElementById('label_field9').value='';
document.getElementById('label_field10').value='';
document.getElementById('label_field11').value='';
document.getElementById('label_field12').value='';
document.getElementById('label_field13').value='';
document.getElementById('label_field14').value='';
document.getElementById('label_field15').value='';
document.getElementById('label_field16').value='';
document.getElementById('label_field17').value='';
document.getElementById('label_field18').value='';
document.getElementById('label_field19').value='';
document.getElementById('label_field20').value='';
document.getElementById('label_field21').value='';
document.getElementById('label_field22').value='';
document.getElementById('label_field23').value='';
document.getElementById('label_field24').value='';
document.getElementById('label_field25').value='';
document.getElementById('label_field26').value='';
document.getElementById('label_field27').value='';
document.getElementById('label_field28').value='';
document.getElementById('dropdown1_list').value='';
document.getElementById('dropdown2_list').value='';
document.getElementById('dropdown3_list').value='';
document.getElementById('dropdown4_list').value='';
document.getElementById('dropdown5_list').value='';
document.getElementById('dropdown6_list').value='';
document.getElementById('dropdown7_list').value='';
document.getElementById('callback_date').value = '';
document.getElementById('callback_time').value = '';
document.getElementById('me_user').value = '';
document.getElementById('user_name').value = "";
document.getElementById('user_city').value = "";
document.getElementById('user_number').value = "";
document.getElementById('user_email').value = "";
document.getElementById('user_disposition').value = "";
document.getElementById('user_comments').value = "";
document.getElementById('user_agent').value = "";
document.getElementById('user_field1').value = "";
document.getElementById('user_field2').value = "";
document.getElementById('user_field3').value = "";
document.getElementById('user_field4').value = "";
document.getElementById('user_field5').value = "";
document.getElementById("drop_down1_value").value="";
document.getElementById("drop_down2_value").value="";
document.getElementById("drop_down3_value").value="";
document.getElementById("drop_down4_value").value="";
document.getElementById("drop_down5_value").value="";
document.getElementById("sub_drop_down1_value").value="";
document.getElementById("sub_drop_down2_value").value="";
document.getElementById("sub_drop_down3_value").value="";
document.getElementById("sub_drop_down4_value").value="";
document.getElementById("sub_drop_down5_value").value="";
document.getElementById("sub_sub_drop_down1_value").value="";
document.getElementById("sub_sub_drop_down2_value").value="";
document.getElementById("sub_sub_drop_down3_value").value="";
document.getElementById("sub_sub_drop_down4_value").value="";
document.getElementById("sub_sub_drop_down5_value").value="";

if(go_pause == "GOPAUSE"){
				document.getElementById('AgentStatusChecking').checked = false;
				AgentStatusChecking();
              }
 document.getElementById('go_pause').checked = false;
       }

    loginhours();

}

function setCallback(){

updateTimer();

		var callback_date = $('#callback_date').val();
		var callback_time = $('#callback_time').val();
		//alert("callback_date: "+callback_date+" callback_time: "+callback_time);
		document.getElementById('id11').style.display = 'none';

     var DispositionVal = "CALLBACK";
		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		var phoneNumber = document.getElementById('label_phone_number').value;

var first_name = document.getElementById('first_name').value;
var middle_name = document.getElementById('middle_name').value;
var last_name = document.getElementById('last_name').value;
var address1 = document.getElementById('address1').value;
var address2 = document.getElementById('address2').value;
var address3 = document.getElementById('address3').value;
var label_city = document.getElementById('label_city').value;
var label_state = document.getElementById('label_state').value;
var label_province = document.getElementById('label_province').value;
var label_gender = document.getElementById('label_gender').value;
var label_phone_number = document.getElementById('label_phone_number').value;
var label_phone_code = document.getElementById('label_phone_code').value;
var alt_phone1 = document.getElementById('alt_phone1').value;
var alt_phone2 = document.getElementById('alt_phone2').value;
var alt_phone3 = document.getElementById('alt_phone3').value;
var alt_phone4 = document.getElementById('alt_phone4').value;
var alt_phone5 = document.getElementById('alt_phone5').value;
var alt_phone6 = document.getElementById('alt_phone6').value;
var alt_phone7 = document.getElementById('alt_phone7').value;
var alt_phone8 = document.getElementById('alt_phone8').value;
var comments = document.getElementById('comments').value;
var label_field1 = document.getElementById('label_field1').value;
var label_field2 = document.getElementById('label_field2').value;
var label_field3 = document.getElementById('label_field3').value;
var label_field4 = document.getElementById('label_field4').value;
var label_field5 = document.getElementById('label_field5').value;
var label_field6 = document.getElementById('label_field6').value;
var label_field7 = document.getElementById('label_field7').value;
var label_field8 = document.getElementById('label_field8').value;
var label_field9 = document.getElementById('label_field9').value;
var label_field10 = document.getElementById('label_field10').value;
var label_field11 = document.getElementById('label_field11').value;
var label_field12 = document.getElementById('label_field12').value;
var label_field13 = document.getElementById('label_field13').value;
var label_field14 = document.getElementById('label_field14').value;
var label_field15 = document.getElementById('label_field15').value;
var label_field16 = document.getElementById('label_field16').value;
var label_field17 = document.getElementById('label_field17').value;
var label_field18 = document.getElementById('label_field18').value;
var label_field19 = document.getElementById('label_field19').value;
var label_field20 = document.getElementById('label_field20').value;
var label_field21 = document.getElementById('label_field21').value;
var label_field22 = document.getElementById('label_field22').value;
var label_field23 = document.getElementById('label_field23').value;
var label_field24 = document.getElementById('label_field24').value;
var label_field25 = document.getElementById('label_field25').value;
var label_field26 = document.getElementById('label_field26').value;
var label_field27 = document.getElementById('label_field27').value;
var label_field28 = document.getElementById('label_field28').value;
var dropdown1_list = document.getElementById('dropdown1_list').value;
var dropdown2_list = document.getElementById('dropdown2_list').value;
var dropdown3_list = document.getElementById('dropdown3_list').value;
var dropdown4_list = document.getElementById('dropdown4_list').value;
var dropdown5_list = document.getElementById('dropdown5_list').value;
var dropdown6_list = document.getElementById('dropdown6_list').value;
var dropdown7_list = document.getElementById('dropdown7_list').value;
var callback_date = document.getElementById('callback_date').value;
var callback_time =  document.getElementById('callback_time').value;
var user_me = '<?php echo $extension; ?>';
var drop_down1_value = document.getElementById("drop_down1_value").value;
var drop_down2_value = document.getElementById("drop_down2_value").value;
var drop_down3_value = document.getElementById("drop_down3_value").value;
var drop_down4_value = document.getElementById("drop_down4_value").value;
var drop_down5_value = document.getElementById("drop_down5_value").value;
var sub_drop_down1_value = document.getElementById("sub_drop_down1_value").value;
var sub_drop_down2_value = document.getElementById("sub_drop_down2_value").value;
var sub_drop_down3_value = document.getElementById("sub_drop_down3_value").value;
var sub_drop_down4_value = document.getElementById("sub_drop_down4_value").value;
var sub_drop_down5_value = document.getElementById("sub_drop_down5_value").value;
var sub_sub_drop_down1_value = document.getElementById("sub_sub_drop_down1_value").value;
var sub_sub_drop_down2_value = document.getElementById("sub_sub_drop_down2_value").value;
var sub_sub_drop_down3_value = document.getElementById("sub_sub_drop_down3_value").value;
var sub_sub_drop_down4_value = document.getElementById("sub_sub_drop_down4_value").value;
var sub_sub_drop_down5_value = document.getElementById("sub_sub_drop_down5_value").value;

if(document.getElementById("go_pause").checked){
var go_pause = document.getElementById('go_pause').value;
}else{
	var go_pause = '';
}
if((campaign == "CollectionAgency" || campaign == "CustomerRetention") && (drop_down1_value == "" && sub_drop_down1_value == "")){

	alert('Please Select Disposition & Sub disposition Before Dispose The Call.');
	return true;
}else{
	var drop_down1_value = document.getElementById("drop_down1_value").value;
	var sub_drop_down1_value = document.getElementById("sub_drop_down1_value").value;
}

if(campaign == "CollectionAgency" && drop_down1_value == "PTP" && sub_drop_down1_value == "Yes" && first_name == "" && middle_name == ""){
	alert('Please Enter PTP Amount & Total Amount Before Dispose The Call.');
	return true;
}else{
	var first_name = document.getElementById('first_name').value;
var middle_name = document.getElementById('middle_name').value;
}
		//alert("callback_date: "+callback_date+" callback_time: "+callback_time);
		//alert(user_me);
		$.ajax({
			type: 'POST',
			url: "ajax/HANGUP.php",
			data: {
				'user': user,
				'DispositionVal': DispositionVal,
				'phoneNumber': phoneNumber,
				'extension': extension,
				'campaign' : campaign,
				'first_name' : first_name,
'middle_name' : middle_name,
'last_name' : last_name,
'address1' : address1,
'address2' : address2,
'address3' : address3,
'label_city' : label_city,
'label_state' : label_state,
'label_province' : label_province,
'label_gender' : label_gender,
'label_phone_number' : label_phone_number,
'label_phone_code' : label_phone_code,
'alt_phone1' : alt_phone1,
'alt_phone2' : alt_phone2,
'alt_phone3' : alt_phone3,
'alt_phone4' : alt_phone4,
'alt_phone5' : alt_phone5,
'alt_phone6' : alt_phone6,
'alt_phone7' : alt_phone7,
'alt_phone8' : alt_phone8,
'comments' : comments,
'label_field1' : label_field1,
'label_field2' : label_field2,
'label_field3' : label_field3,
'label_field4' : label_field4,
'label_field5' : label_field5,
'label_field6' : label_field6,
'label_field7' : label_field7,
'label_field8' : label_field8,
'label_field9' : label_field9,
'label_field10' : label_field10,
'label_field11' : label_field11,
'label_field12' : label_field12,
'label_field13' : label_field13,
'label_field14' : label_field14,
'label_field15' : label_field15,
'label_field16' : label_field16,
'label_field17' : label_field17,
'label_field18' : label_field18,
'label_field19' : label_field19,
'label_field20' : label_field20,
'label_field21' : label_field21,
'label_field22' : label_field22,
'label_field23' : label_field23,
'label_field24' : label_field24,
'label_field25' : label_field25,
'label_field26' : label_field26,
'label_field27' : label_field27,
'label_field28' : label_field28,
'dropdown1_list' : dropdown1_list,
'dropdown2_list' : dropdown2_list,
'dropdown3_list' : dropdown3_list,
'dropdown4_list' : dropdown4_list,
'dropdown5_list' : dropdown5_list,
'dropdown6_list' : dropdown6_list,
'dropdown7_list' : dropdown7_list,
'callback_date' : callback_date,
'callback_time' : callback_time,
'user_me' : user_me,
'drop_down1_value' : drop_down1_value,
'drop_down2_value' : drop_down2_value,
'drop_down3_value' : drop_down3_value,
'drop_down4_value' : drop_down4_value,
'drop_down5_value' : drop_down5_value,
'sub_drop_down1_value' : sub_drop_down1_value,
'sub_drop_down2_value' : sub_drop_down2_value,
'sub_drop_down3_value' : sub_drop_down3_value,
'sub_drop_down4_value' : sub_drop_down4_value,
'sub_drop_down5_value' : sub_drop_down5_value,
'sub_sub_drop_down1_value' : sub_sub_drop_down1_value,
'sub_sub_drop_down2_value' : sub_sub_drop_down2_value,
'sub_sub_drop_down3_value' : sub_sub_drop_down3_value,
'sub_sub_drop_down4_value' : sub_sub_drop_down4_value,
'sub_sub_drop_down5_value' : sub_sub_drop_down5_value,
'go_pause' : go_pause
},
			success: function(result) {
				//alert("Response: "+result);
				console.log("Response: "+result);
				document.getElementById("btn_hangup").disabled = true;
				document.getElementById('btn_hangup').className = "btnaaInput2";
				document.getElementById('myHangup').style.display = "none";

			}
		});

		document.getElementById('first_name').value='';
document.getElementById('middle_name').value='';
document.getElementById('last_name').value='';
document.getElementById('address1').value='';
document.getElementById('address2').value='';
document.getElementById('address3').value='';
document.getElementById('label_city').value='';
document.getElementById('label_state').value='';
document.getElementById('label_province').value='';
document.getElementById('label_gender').value='';
document.getElementById('label_phone_number').value='';
document.getElementById('label_phone_code').value='';
document.getElementById('alt_phone1').value='';
document.getElementById('alt_phone2').value='';
document.getElementById('alt_phone3').value='';
document.getElementById('alt_phone4').value='';
document.getElementById('alt_phone5').value='';
document.getElementById('alt_phone6').value='';
document.getElementById('alt_phone7').value='';
document.getElementById('alt_phone8').value='';
document.getElementById('comments').value='';
document.getElementById('label_field1').value='';
document.getElementById('label_field2').value='';
document.getElementById('label_field3').value='';
document.getElementById('label_field4').value='';
document.getElementById('label_field5').value='';
document.getElementById('label_field6').value='';
document.getElementById('label_field7').value='';
document.getElementById('label_field8').value='';
document.getElementById('label_field9').value='';
document.getElementById('label_field10').value='';
document.getElementById('label_field11').value='';
document.getElementById('label_field12').value='';
document.getElementById('label_field13').value='';
document.getElementById('label_field14').value='';
document.getElementById('label_field15').value='';
document.getElementById('label_field16').value='';
document.getElementById('label_field17').value='';
document.getElementById('label_field18').value='';
document.getElementById('label_field19').value='';
document.getElementById('label_field20').value='';
document.getElementById('label_field21').value='';
document.getElementById('label_field22').value='';
document.getElementById('label_field23').value='';
document.getElementById('label_field24').value='';
document.getElementById('label_field25').value='';
document.getElementById('label_field26').value='';
document.getElementById('label_field27').value='';
document.getElementById('label_field28').value='';
document.getElementById('dropdown1_list').value='';
document.getElementById('dropdown2_list').value='';
document.getElementById('dropdown3_list').value='';
document.getElementById('dropdown4_list').value='';
document.getElementById('dropdown5_list').value='';
document.getElementById('dropdown6_list').value='';
document.getElementById('dropdown7_list').value='';
document.getElementById('callback_date').value = '';
document.getElementById('callback_time').value = '';
document.getElementById('me_user').value = '';
document.getElementById('user_name').value = "";
document.getElementById('user_city').value = "";
document.getElementById('user_number').value = "";
document.getElementById('user_email').value = "";
document.getElementById('user_disposition').value = "";
document.getElementById('user_comments').value = "";
document.getElementById('user_agent').value = "";
document.getElementById('user_field1').value = "";
document.getElementById('user_field2').value = "";
document.getElementById('user_field3').value = "";
document.getElementById('user_field4').value = "";
document.getElementById('user_field5').value = "";
document.getElementById("drop_down1_value").value="";
document.getElementById("drop_down2_value").value="";
document.getElementById("drop_down3_value").value="";
document.getElementById("drop_down4_value").value="";
document.getElementById("drop_down5_value").value="";
document.getElementById("sub_drop_down1_value").value="";
document.getElementById("sub_drop_down2_value").value="";
document.getElementById("sub_drop_down3_value").value="";
document.getElementById("sub_drop_down4_value").value="";
document.getElementById("sub_drop_down5_value").value="";
document.getElementById("sub_sub_drop_down1_value").value="";
document.getElementById("sub_sub_drop_down2_value").value="";
document.getElementById("sub_sub_drop_down3_value").value="";
document.getElementById("sub_sub_drop_down4_value").value="";
document.getElementById("sub_sub_drop_down5_value").value="";

if(go_pause == "GOPAUSE"){
				document.getElementById('AgentStatusChecking').checked = false;
				AgentStatusChecking();
              }
 document.getElementById('go_pause').checked = false;
	}


//Seconds in call
var check = null;
	function updateTimer() {

		var user = '<?php echo $loggedInuserName; ?>';
		$.ajax({
			url: "ajax/updateDateTimer.php",
			data: {
				'user': user
			},
			success: function(result) {
				//alert(result);

				function addSeconds(timeString, cnt) {
  // Parse the time string to get hours, minutes, and seconds
  var timeArray = timeString.split(":");
  var hours = parseInt(timeArray[0]);
  var minutes = parseInt(timeArray[1]);
  var seconds = parseInt(timeArray[2]);

  // Convert everything to seconds and add the seconds to the total
  var totalSeconds = hours * 3600 + minutes * 60 + seconds + cnt;

  // Calculate new hours, minutes, and seconds
  var newHours = Math.floor(totalSeconds / 3600);
  var newMinutes = Math.floor((totalSeconds % 3600) / 60);
  var newSeconds = totalSeconds % 60;

  // Add leading zeros if necessary
  newHours = (newHours < 10) ? "0" + newHours : newHours;
  newMinutes = (newMinutes < 10) ? "0" + newMinutes : newMinutes;
  newSeconds = (newSeconds < 10) ? "0" + newSeconds : newSeconds;

  return newHours + ":" + newMinutes + ":" + newSeconds;
}

				var initialTime = "00:00:00";
				if (result == "starttimer") {
					if (check == null) {
			              var cnt = 0;
						  check = setInterval(function () {
                                  cnt += 1;
                    document.getElementById("seconds_time").innerHTML = addSeconds(initialTime, cnt);

                                   }, 1000);

		                           }
				} else if (result == "stoptimer") {

			clearInterval(check);
            check = null;
			  cnt = 0;
		document.getElementById("seconds_time").innerHTML = addSeconds(initialTime, cnt);
				}




			}
		});
	}

  setInterval(function() {
		updateTimer()
	}, 1000);


	function currentTime() {
		var datetime = new Date();
		var time = datetime.getHours() + ":" + datetime.getMinutes() + ":" + datetime.getSeconds();
		document.getElementById("currentTime").textContent = time;
	}
	const interval1 = setInterval(function() {
		currentTime()
	}, 1000);


	function inboundDate(){
		var infromdate = document.getElementById("inboundfromdate").value;
		var intodate = document.getElementById("inboundtodate").value;
		var extension = '<?php echo $extension; ?>';
		//alert(infromdate);
		$.ajax({
			type: 'POST',
			url: "ajax/inbounddate.php",
			data: {
				'infromdate': infromdate,
				'intodate' : intodate,
				'extension' : extension
			},
			success: function(result) {

				//alert(result);
				//console.log(result);
		document.getElementById('inboundTable').innerHTML = result;
		$('#inboundTABLEDATA').DataTable();
		document.getElementById('inboundInfPop').style.display = 'block';


			}
		});
	}

	function outboundDate(){
		var outfromdate = document.getElementById("outboundfromdate").value;
		var outtodate = document.getElementById("outboundtodate").value;
		var extension = '<?php echo $extension; ?>';
		//alert(outfromdate);
		$.ajax({
			type: 'POST',
			url: "ajax/outbounddate.php",
			data: {
				'outfromdate': outfromdate,
				'outtodate' : outtodate,
				'extension' : extension
			},
			success: function(result) {

				//alert(result);
				//console.log(result);
		document.getElementById('outboundTable').innerHTML = result;
		$('#outboundTABLEDATA').DataTable();
		document.getElementById('outboundInfPop').style.display = 'block';


			}
		});
	}

	function calllogDate(){
		var logfromdate = document.getElementById("logfromdate").value;
		var logtodate = document.getElementById("logtodate").value;
		var extension = '<?php echo $extension; ?>';
		//alert(outfromdate);
		$.ajax({
			type: 'POST',
			url: "ajax/calllogdate.php",
			data: {
				'logfromdate': logfromdate,
				'logtodate' : logtodate,
				'extension' : extension
			},
			success: function(result) {

				//alert(result);
				//console.log(result);
		document.getElementById('clickLogTable').innerHTML = result;
				$('#example').DataTable();
		document.getElementById('fielddData').style.display = 'none';
		document.getElementById("callBackInfo").style.display = 'none';
		document.getElementById("CallLogInfo").style.display = 'block';


			}
		});
	}




	 // Disable right-click on the entire page
	/*
    window.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

*/

// email sending

	document.getElementById('submitBtn').addEventListener('click', function() {

var toemail = document.getElementById('sendEmail').value;
var subject = document.getElementById('subjectEmail').value;

const toemailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
const subjectPattern = /^[a-zA-Z0-9-\s]*$/;
if(toemail ==''){
	  alert('Please enter email id.');
				return true;
}
if (!toemailPattern.test(toemail)) {
                alert('Email is invalid. Please enter a valid email id.');
				return true;
            }
if(subject ==''){
	 alert('Please enter subject.');
				return true;
}

if (!subjectPattern.test(subject)) {
                alert('Subject is invalid. Please enter a valid subject.');
				return true;
            }
  // Get form data
  const form = document.getElementById('myForm');
  const formData = new FormData(form);
//alert('email');
  // Create an XMLHttpRequest object
  const xhr = new XMLHttpRequest();

  // Configure the request
  xhr.open('POST', 'target_email.php'); // Replace 'target_page.php' with the URL of your server-side script
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Optional, to indicate an AJAX request

  // Set up a callback function to handle the response
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      // Handle the response here
	  alert(xhr.responseText);
	  return true;
      //console.log(xhr.responseText);

    }
  };

  // Send the FormData
  xhr.send(formData);
  document.getElementById('sendEmail').value="";
document.getElementById('subjectEmail').value="";
document.getElementById('content').value="";
document.getElementById('attachment').value="";
});


// Dependet drop downs data popup
function select1_Dropdown(){
	//alert();
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down1_value = document.getElementById("drop_down1_value").value;
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_drop_down1_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_Dropdown1.php?drop_down1_value="+drop_down1_value+"&sub_sub_Labelid="+sub_sub_Labelid, true);
				  xhttp.send();
}
function select2_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down2_value = document.getElementById("drop_down2_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_drop_down2_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_Dropdown2.php?drop_down2_value="+drop_down2_value+"&sub_sub_Labelid="+sub_sub_Labelid, true);
				  xhttp.send();
}
function select3_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down3_value = document.getElementById("drop_down3_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_drop_down3_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_Dropdown3.php?drop_down3_value="+drop_down3_value+"&sub_sub_Labelid="+sub_sub_Labelid, true);
				  xhttp.send();
}
function select4_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down4_value = document.getElementById("drop_down4_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_drop_down4_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_Dropdown4.php?drop_down4_value="+drop_down4_value+"&sub_sub_Labelid="+sub_sub_Labelid, true);
				  xhttp.send();
}
function select5_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down5_value = document.getElementById("drop_down5_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_drop_down5_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_Dropdown5.php?drop_down5_value="+drop_down5_value+"&sub_sub_Labelid="+sub_sub_Labelid, true);
				  xhttp.send();
}





// Sub sub drop downs
function select1_sub_Dropdown(){
	//alert();
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down1_value = document.getElementById("drop_down1_value").value;
	var subdrop_down1_value = document.getElementById("sub_drop_down1_value").value;
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_sub_drop_down1_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_SubDropdown1.php?drop_down1_value="+drop_down1_value+"&sub_sub_Labelid="+sub_sub_Labelid+"&subdrop_down1_value="+subdrop_down1_value, true);
				  xhttp.send();
}
function select2_sub_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down2_value = document.getElementById("drop_down2_value").value;
	var subdrop_down2_value = document.getElementById("sub_drop_down2_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_sub_drop_down2_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_SubDropdown2.php?drop_down2_value="+drop_down2_value+"&sub_sub_Labelid="+sub_sub_Labelid+"&subdrop_down2_value="+subdrop_down2_value, true);
				  xhttp.send();
}
function select3_sub_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down3_value = document.getElementById("drop_down3_value").value;
	var subdrop_down3_value = document.getElementById("sub_drop_down3_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_sub_drop_down3_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_SubDropdown3.php?drop_down3_value="+drop_down3_value+"&sub_sub_Labelid="+sub_sub_Labelid+"&subdrop_down3_value="+subdrop_down3_value, true);
				  xhttp.send();
}
function select4_sub_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down4_value = document.getElementById("drop_down4_value").value;
	var subdrop_down4_value = document.getElementById("sub_drop_down4_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_sub_drop_down4_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_SubDropdown4.php?drop_down4_value="+drop_down4_value+"&sub_sub_Labelid="+sub_sub_Labelid+"&subdrop_down4_value="+subdrop_down4_value, true);
				  xhttp.send();
}
function select5_sub_Dropdown(){
	var sub_sub_Labelid = document.getElementById("lead_id").value;
	var drop_down5_value = document.getElementById("drop_down5_value").value;
	var subdrop_down5_value = document.getElementById("sub_drop_down5_value").value;
	//alert(drop_down1_value);
	const xhttp = new XMLHttpRequest();
				  xhttp.onload = function() {
					var result =  this.responseText;
					//console.log(result);
            	document.getElementById('sub_sub_drop_down5_value').innerHTML = result;
	}
				  xhttp.open("GET", "dropdown/check_SubDropdown5.php?drop_down5_value="+drop_down5_value+"&sub_sub_Labelid="+sub_sub_Labelid+"&subdrop_down5_value="+subdrop_down5_value, true);
				  xhttp.send();
}


function feedback(){

	var status = document.getElementById('CallStatus').textContent;

		if((status == "DIAL") || (status == "ONCALL")){

			var user = '<?php echo $extension; ?>';

		$.ajax({
			type: 'POST',
			url: "ajax/rating_customer.php",
			data: {
				'user': user

			},
			success: function(result) {
				console.log(result);
			}
		});
		alert("Call Is Passing To Feedback IVR");
		}else{
		}
}

function phoneClose() {
		document.getElementById('phonebook_id').style.display = 'none';
	}

function phoneBookClick(){
	//alert("Hi");
	if (document.getElementById('AgentStatusChecking').checked) {} else {
			alert("Please make yourself ready.");
			return true;
		}

	var campaign = document.getElementById("update_campaign").value;
    var valid = "click";
		$.ajax({
			type: 'POST',
			url: "ajax/search_phonebook.php",
			data: {
				'campaign': campaign,
				'valid': valid

			},
			success: function(result) {
				//console.log(result);
		document.getElementById("phoneBookRefresh").innerHTML = result;
		document.getElementById("phonebook_id").style.display = 'block';
			}
		});
}

function myphoneBook(){
	var campaign = document.getElementById("update_campaign").value;
    var valid = "search";
	var key = document.getElementById("searchPhoneBook").value;
	//alert(key);
		$.ajax({
			type: 'POST',
			url: "ajax/search_phonebook.php",
			data: {
				'campaign': campaign,
				'valid': valid,
				'key': key

			},
			success: function(result) {
				//console.log(result);
		document.getElementById("phoneBookRefresh").innerHTML = result;
		document.getElementById("phonebook_id").style.display = 'block';
			}
		});
}

window.onclick = function(event) {
					if (event.target == document.getElementById("phonebook_id")) {
					document.getElementById("phonebook_id").style.display = "none";
												}
}

// Phonebook clcik2call
function clickToPhonebbok(val) {

		var user = '<?php echo $loggedInuserName; ?>';
		var extension = '<?php echo $extension; ?>';
		var phoneNumber = val;
		var status = document.getElementById('CallStatus').textContent;
		//alert(phoneNumber);
		$.ajax({
			type: 'POST',
			url: "ajax/dnc_check.php",
			data: {
				'phoneNumber': phoneNumber

			},
			success: function(result) {
				//alert(result);
		if(result == "DNC"){
				alert("This phone number is in the DNC list: "+phoneNumber);
			return true;
				}else{

		if(status == "IDLE"){
		document.getElementById('label_phone_number').value = phoneNumber;
		document.getElementById("phonebook_id").style.display = 'none';
		document.getElementById('CallLogInfo').style.display = 'none';
		document.getElementById('callBackInfo').style.display = 'none';
	    document.getElementById('fielddData').style.display = 'block';


		$.ajax({
			type: 'POST',
			url: "ajax/click2call.php",
			data: {
				'user': user,
				'phoneNumber': phoneNumber,
				'extension': extension
			},
			success: function(result) {
				//alert(result);
				var data = result.split('=====');
		document.getElementById('first_name').value= data[0];
document.getElementById('middle_name').value= data[1];
document.getElementById('last_name').value= data[2];
document.getElementById('address1').value= data[3];
document.getElementById('address2').value= data[4];
document.getElementById('address3').value= data[5];
document.getElementById('label_city').value= data[6];
document.getElementById('label_state').value= data[7];
document.getElementById('label_province').value= data[8];
document.getElementById('label_gender').value= data[9];
//document.getElementById('label_phone_number').value= data[10];
document.getElementById('label_phone_code').value= data[11];
document.getElementById('alt_phone1').value= data[12];
document.getElementById('alt_phone2').value= data[13];
document.getElementById('alt_phone3').value= data[14];
document.getElementById('alt_phone4').value= data[15];
document.getElementById('alt_phone5').value= data[16];
document.getElementById('alt_phone6').value= data[17];
document.getElementById('alt_phone7').value= data[18];
document.getElementById('alt_phone8').value= data[19];
document.getElementById('comments').value= data[20];
document.getElementById('label_field1').value= data[21];
document.getElementById('label_field2').value= data[22];
document.getElementById('label_field3').value= data[23];
document.getElementById('label_field4').value= data[24];
document.getElementById('label_field5').value= data[25];
document.getElementById('label_field6').value= data[26];
document.getElementById('label_field7').value= data[27];
document.getElementById('label_field8').value= data[28];
document.getElementById('label_field9').value= data[29];
document.getElementById('label_field10').value= data[30];
document.getElementById('label_field11').value= data[31];
document.getElementById('label_field12').value= data[32];
document.getElementById('label_field13').value= data[33];
document.getElementById('label_field14').value= data[34];
document.getElementById('label_field15').value= data[35];
document.getElementById('label_field16').value= data[36];
document.getElementById('label_field17').value= data[37];
document.getElementById('label_field18').value= data[38];
document.getElementById('label_field19').value= data[39];
document.getElementById('label_field20').value= data[40];
document.getElementById('label_field21').value= data[41];
document.getElementById('label_field22').value= data[42];
document.getElementById('label_field23').value= data[43];
document.getElementById('label_field24').value= data[44];
document.getElementById('label_field25').value= data[45];
document.getElementById('label_field26').value= data[46];
document.getElementById('label_field27').value= data[47];
document.getElementById('label_field28').value= data[48];
document.getElementById('dropdown1_list').value= data[49];
document.getElementById('dropdown2_list').value= data[50];
document.getElementById('dropdown3_list').value= data[51];
document.getElementById('dropdown4_list').value= data[52];
document.getElementById('dropdown5_list').value= data[53];
document.getElementById('dropdown6_list').value= data[54];
document.getElementById('dropdown7_list').value= data[55];

//Forms
document.getElementById('user_name').value = data[57];
document.getElementById('user_city').value = data[58];
document.getElementById('user_number').value = data[10];
document.getElementById('user_email').value = data[59];
document.getElementById('user_disposition').value = data[49];
document.getElementById('user_comments').value = data[20];
document.getElementById('user_agent').value = data[56];
document.getElementById('user_field1').value = data[60];
document.getElementById('user_field2').value = data[61];
document.getElementById('user_field3').value = data[62];
document.getElementById('user_field4').value = data[63];
document.getElementById('user_field5').value = data[64];


			}
		});

		}else{
			alert("Please end current call before dialing other number.");
			return true;
		}
	}
		}

		});
	}


//phonebook Email
function mailToPhonebbok(val){
	document.getElementById("phonebook_id").style.display = 'none';
	document.getElementById('sendEmail').value = val;
	document.getElementById('id03').style.display = 'block';
}


function loginhours() {
		var user = '<?php echo $loggedInuserName; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(user);

		$.ajax({
			type: 'POST',
			url: "ajax/login_hours.php",
			data: {
				'user': user,
				'campaign': campaign
			},
			success: function(result) {
        //console.log(result);
				var output = result.split("=====");
				//alert(output[0]);

			document.getElementById('Lhour').innerHTML = output[0];
			document.getElementById('TotalTalk').innerHTML = output[1];
			//document.getElementById('Bhour').innerHTML = output[1];


			}
		});
	}



function campaignChange() {
		var extension = '<?php echo $extension; ?>';
		var campaign = document.getElementById("update_campaign").value;
		//alert(campaign);

		$.ajax({
			type: 'POST',
			url: "ajax/dynamic_campaign.php",
			data: {
				'extension': extension,
				'campaign': campaign
			},
			success: function(result) {
        //console.log(result);
			document.getElementById("update_campaign").value = campaign;
			}
		});
		
	} 



</script>
