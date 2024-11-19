<?php
session_start();

if (isset( $_SESSION['uid'] )){
	$userid = $_SESSION['uid'];
}else{
	$userid = 0;
}

if ( isset($_SESSION['fyr']) ){
	$fyr = $_SESSION['fyr'];
}

include_once('common.php');
$repsonse = array();
$id = $tablename = "";
if (isset ($_POST['id']) ){
	$id = $_POST['id'];
	$tablename = $_POST['table'];
}

switch($tablename){
	case 'acmaster':
		AcFill();
	break;
	case 'transactions':
		TransFill();
	break;
	case 'usermaster':
		UserFill();
	break;
	case 'category':
		catFill();
	break;
}

function AcFill(){
	global $sqlqry;
	global $connect;
	global $con;
	global $id;
	global $userid;
	$sqlqry = "";

	$sqlqry = "SELECT * FROM acmaster WHERE ACCD = ".$id." AND UID = ".$userid ;

	$connect = mysqli_query($con , $sqlqry);
	if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
			$response[0] = $rows['ACCD'];
			$response[1] = $rows['ACNAME'];
			$response[2] = $rows['ACOPEN'];
			$response[3] = $rows['OCRDR'];
			$response[4] = $rows['YN'];
			$response[5] = $rows['CLOSEAC'];
			$response[6] = $rows['DASHAC'];
		}
		echo (json_encode($response));
		exit;
	}
}


function TransFill(){
	global $sqlqry;
	global $connect;
	global $con;
	global $id;
	global $userid;
	global $fyr;
	$sqlqry = "";

	$sqlqry = "SELECT * FROM transactions WHERE TID = ".$id." AND SRNO = 1 AND UID = ".$userid." AND FYR = "."$fyr" ;
	$connect = mysqli_query($con , $sqlqry);
	if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
			$response[0] = $rows['TID'];
			$response[1] = $rows['TDATE'];
			$response[2] = $rows['PARTY1'];
			$response[3] = $rows['PARTY2'];
			$response[4] = $rows['CRDR'];
			$response[5] = $rows['AMT'];
			$response[6] = $rows['NARR'];
			$imgarr = $rows['IMGJSON'];
			if ($imgarr != '{}'){
				$imgstatus = count($imgarr);
			}else{
				$imgstatus = 'No';
			}
			$response[7] = $imgstatus;
			$response[8] = $rows['CATID'];
		}
		echo (json_encode($response));
		exit;
	}
}

function UserFill(){
	global $sqlqry;
	global $connect;
	global $con;
	global $id;
	$sqlqry = "";

	$sqlqry = "SELECT * FROM usermaster WHERE UID = ".$id ;
	$connect = mysqli_query($con , $sqlqry);
	if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
			$response[0] = $rows['UID'];
			$response[1] = $rows['UNAME'];
			$response[2] = $rows['UPASS'];
			$response[3] = $rows['UPASS'];
			$response[4] = $rows['FDATE'];
			$response[5] = $rows['ADMIN'];
			$response[6] = $rows['SUBADMIN'];
		}
		echo (json_encode($response));
		exit;
	}
}

function catFill(){
	global $sqlqry;
	global $connect;
	global $con;
	global $id;
	global $userid;
	$sqlqry = "";

	$sqlqry = "SELECT * FROM category WHERE CATID = ".$id." AND UID = ".$userid  ;
	$connect = mysqli_query($con , $sqlqry);
	if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
			$response[0] = $rows['CATID'];
			$response[1] = $rows['CATNAME'];
			$response[2] = $rows['ACTIVE'];
		}
		echo (json_encode($response));
		exit;
	}
}


?>
