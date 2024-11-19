<?php
include_once("common.php");
Header('Content-Type:application\json');
if(isset($_GET['uid'])){
	$userid = $_GET['uid'];
}else{
	$userid = 0;
}

$qry = "SELECT CODE,STATUS FROM actstatus WHERE CODE = '$userid'";
$connect = mysqli_query($con , $qry);
if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_assoc($connect)){
			$arr[] = $rows;
		}
		echo json_encode(['status'=>1,'data'=>$arr]);
	}else{
		echo json_encode(['status'=>0,'data'=>$arr]);
	}
?>