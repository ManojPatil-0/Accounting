<?php

if (!isset($_SESSION)){
session_start();
}

$header = $response = '' ;
if ( isset($_POST['head']) ){
	$header = $_POST['head'];
}

//do not send response if session is not set, Send user to login page
if ( isset($_SESSION['uid']) &&  isset ($_SESSION['fyr'])) {

	switch($header){
		case "Report":   
		case "Category Rpt" :
		case "Narration Rpt" :
		ob_start();
		include "Report.php";
		$response = ob_get_clean();
		break;

		case "Charts":    
		ob_start();
		include "Charts.php";
		$response = ob_get_clean();
		break;
	

		case "Transaction":
		ob_start();
		include "Transaction.php";
		$response = ob_get_clean();
		break;

		case "Account Master":
		ob_start();
		include "Accountmaster.html";
		$response = ob_get_clean();
		break;
		
		case "Category Master":
		ob_start();
		include "Category.php";
		$response = ob_get_clean();
		break;
		
		case "User Master":
		ob_start();
		include "Usermaster.php";
		$response = ob_get_clean();
		break;

		case "Change Password":
		ob_start();
		include "password.php";
		$response = ob_get_clean();
		break;

		case "Financial Year":
		ob_start();
		include "year.php";
		$response = ob_get_clean();
		break;

		case "Logout" :
		session_destroy();
		echo "<script>window.location.assign('index.php')</script>";
		break;
	}
	echo $response;
}else{
	$response = '<div  id = "msg"><i class="far fa-times-circle"><div class = "error">&nbsp Login Again </dv></dv> ';
	echo $response;
	echo "<script>window.location.assign('index.php')</script>";
}
?>