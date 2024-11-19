<?php
include 'common.php';

session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$finacialyr = $_SESSION['fyr'];
}else{
	$id = 0;
}

$buttonid;$selectedyear;
if (isset($_POST['id']) &&  isset($_POST['year']) ){
	$buttonid = $_POST['id'];
	$selectedyear = $_POST['year'];
}

switch($buttonid){
	case 'switch':
		switchYear();
	Break;
	case 'create':
		createYear();
	Break;
	case 'extend':
		extendYear();
	break;
}

function switchYear(){
	global $selectedyear;
	global $id;
	if (isset($selectedyear) ){
		$_SESSION['fyr'] = "";
		$_SESSION['sdate'] = "";
        $_SESSION['edate']= "";
        $_SESSION['fyr'] = $selectedyear;
		$_SESSION['sdate'] = GetDescription('userstat','SDATE',"UID = $id AND FYR = '$selectedyear' " );
	    $_SESSION['edate'] = GetDescription('userstat','EDATE',"UID = $id AND FYR = '$selectedyear' " );
		echo '<div class = "success"><strong><i class="far fa-check-circle"></i>&nbsp Successfully Switched Financial Year </strong></div>';
	}else{
		echo '<div class = "error"><strong><i class="far fa-times-circle"></i>&nbsp Failed To Switch Financial Year </strong></div>';
	}
}

function createYear(){
	global $selectedyear;
	global $id;
	$year = $_SESSION['fyr'];
	$sdate = date('Y/m/d');
	$edate = date('Y-m-d',strtotime('+1 years',strtotime($sdate . '-1 day')));
	$udate = date('Y-m-d',strtotime('0 years',strtotime($sdate . '-1 day')));
	//check whether the year user trying to create is already exist or not
	if ( RecSearch("SELECT * FROM userstat WHERE UID = $id AND FYR = '".$selectedyear."'") == false  ) {
		if ( ExeQry("userstat","UID,FYR,SDATE,EDATE","$id,'$selectedyear','$sdate','$edate'") == true ) {
			echo '<div class = "success"><strong><i class="far fa-check-circle"></i>&nbsp Successfully Created New Financial Year </strong></div>';
			if ( UpdQry("userstat","EDATE = '$udate'","FYR = '$year' AND UID = $id") == true ){
				$_SESSION['fyr'] = "";
				$_SESSION['sdate'] = "";
                $_SESSION['edate']= "";
                $_SESSION['fyr'] = $selectedyear;
        		$_SESSION['sdate'] = GetDescription('userstat','SDATE',"UID = $id AND FYR = '$selectedyear' " );
        	    $_SESSION['edate'] = GetDescription('userstat','EDATE',"UID = $id AND FYR = '$selectedyear' " );
			}
		}else{
			echo '<div class = "error"><strong><i class="far fa-times-circle"></i>&nbsp Failed To Create Financial Year </strong></div>';
		}
	}else{
		echo '<div class = "error"><strong><i class="far fa-times-circle"></i>&nbsp '.$selectedyear.'Year Alreay Exits! </strong></div>';
	}
}

function extendYear(){
	global $selectedyear;
	global $finacialyr;
	global $id;
	$endyear = $todaydate = '';
	$todaydate = date('Y-m-d');
	$endyear = GetDescription('userstat','EDATE',"UID = '$id' AND FYR = '$finacialyr'");
	if ( $todaydate > $endyear ) {
		$check = UpdQry(
								'userstat',
								"EDATE = '$selectedyear'",
								"UID = $id  AND FYR = '$finacialyr' ");
				if ($check == true){
					$_SESSION['edate']= "";
					$_SESSION['edate']= $selectedyear;
					echo '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Financial Year Extended Successfully</div>';
				}else{
					echo '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Failed To Extend Financial Year</div>';
				}
	}else{
		echo '<div class = "error"><strong><i class="far fa-times-circle"></i>&nbsp Financial Year Ends At '.date('d-m-Y',strtotime($endyear)).' </strong></div>';
	}
}

?>