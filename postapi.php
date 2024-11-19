<?php
include_once('common.php');

$content = trim(file_get_contents("php://input"));
$decodede = json_decode($content,true);
$uid = $decodede['uid'];
$name = $decodede['name'];
$qry = "SELECT CODE,STATUS FROM actstatus WHERE CODE = '$uid'";
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
