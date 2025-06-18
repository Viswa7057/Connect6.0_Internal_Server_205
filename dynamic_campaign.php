<?php

    $extension = $_REQUEST['extension'];
	$campaign = $_REQUEST['campaign'];
	//echo "===>user".$user;
	
				
			include_once("/home/common/db_connect.php");
			
			
			$query = "update user_live set campaign='$campaign' where extension='$extension'";
             mysqli_query($conn,$query);
			 
			
				  $query_conn = "update users set Campaign='$campaign' where extension_id='$extension'";
             mysqli_query($conn,$query_conn);
	
											
 ?>

