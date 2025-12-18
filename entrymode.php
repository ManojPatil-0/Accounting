<?php
	session_start();
	//get Sessions$finyr;
	if ( isset($_SESSION['uid']) ){
		$userid = $_SESSION['uid'];
	}
	if ( isset($_SESSION['fyr']) ){
		$fyr = $_SESSION['fyr'];
	}
	
	include_once('common.php');
	
	global $flag;
	$addentry = 'A';
	$editentry = 'E';
	$deleteentry = 'D';
	$response = "";
	if ( isset($_POST['page']) ){
		$flag= $_POST['page'];
	}
	switch($flag){
		case "add" :
		//seperate function for userid max
			if ($_POST['table'] == "usermaster" ){
				getUserMax($_POST['table'],$_POST['field']);
			}else{
				GetMax($_POST['table'],$_POST['field']);
			}
			break;
		case "acmaster":
			if ($_POST['entrymode'] == $addentry) {
				AcmasterSave();
			}else if  ($_POST['entrymode'] == $editentry) {
				AcmasterUpdate();
			}else if ($_POST['entrymode'] == $deleteentry) {
				if (isset($_POST['id'])){
					AcmasterDelete($_POST['id']);
				}
			}
			break;
		case "transaction":
			if ($_POST['entrymode'] == $addentry) {
				TransSave();
			}else if  ($_POST['entrymode'] == $editentry) {
				TransUpdate();
			}else if ($_POST['entrymode'] == $deleteentry) {
				if (isset($_POST['id'])){
					TransDelete($_POST['id']);
				}
			}
			break;
		case "usermast":
			if ($_POST['entrymode'] == $addentry) {
				UserSave();
			}else if  ($_POST['entrymode'] == $editentry) {
				UserUpdate();
			}else if ($_POST['entrymode'] == $deleteentry) {
				if (isset($_POST['id'])){
					UserDelete($_POST['id']);
				}
			}
			break;
		case "category":
				if ($_POST['entrymode'] == $addentry) {
					catSave();
				}else if  ($_POST['entrymode'] == $editentry) {
					catUpdate();
				}else if ($_POST['entrymode'] == $deleteentry) {
					if (isset($_POST['id'])){
						catDelete($_POST['id']);
					}
				}
				break;
		case "login":
			GetFyr();
			break;
		case "password":
			PassUpdate();
			break;
		case "chartfilter":
			filterchartsel( $_POST['valueselected'],$_POST['iscompare'] );
			break;
	}

function GetMax($table,$field){
	global $userid;
	global $fyr;
	$fyrcheck = ($table == "transactions" ? " AND FYR = '$fyr'": '') ;
	$response = "";
	$response =  MaxNumber($table,$field,"UID = $userid $fyrcheck");
	echo $response;
}

function getUserMax($table,$field){
	$response = "";
	$response =  MaxNumber($table,$field);
	echo $response;
}
function AcmasterDelete($id){
	global $userid;
	$whereclause = "ACCD = $id AND UID = $userid";
	if (  RecSearch("SELECT * FROM transactions WHERE (PARTY1 = $id OR PARTY2 = $id ) AND UID = $userid "  ) == false ){
		if (DltQry('acmaster',$whereclause) == true ){
		    $response ='';
			$response = "<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Delete SuccessFull</div>";
		}else{
		    $response ='';
			$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Failed to Delete</div>";
		}
	}else{
	    $response ='';
		$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Can Not Delete, Party Found In Transaction!</div>";
	}
	echo $response;
}
function AcmasterSave(){
	//check for empty fields
	unset($response);
	global $userid;
	if ( ($_POST['acname'] <> "") &&  ($_POST['opening'] <> "") )   {
		$name = $_POST['acname'];
			//check for record exist or not
			if (  RecSearch("SELECT ACNAME FROM acmaster WHERE ACNAME = '$name' AND UID = $userid  ") == false ){
				$accd = $_POST['accd'];
				$open = $_POST['opening'];
				$crdr = $_POST['crdr'];
				$yn = $_POST['yn'];
				$cyn = $_POST['cyn'];
				$dyn = $_POST['dyn'];
				$check = ExeQry(
								'acmaster',
								'ACCD,ACNAME,ACOPEN,OCRDR,YN,CLOSEAC,DASHAC,UID',
								"$accd ,'$name','$open','$crdr','$yn','$cyn','$dyn',$userid ");
				if ($check == true){
			    $response ='';
				$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp SuccessFull</div>';
				}else{
				     $response ='';
					 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
				}
			}else{
			     $response ='';
				 $response = '<div class ="error"><i class="far fa-times-circle"></i>&nbsp Account Name Already Exist</div>';
			}
	}else{
	     $response ='';
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function AcmasterUpdate(){
	unset($response);
	global $userid;
	//check for empty fields
	if ( ($_POST['acname'] <> "") &&  ($_POST['opening'] <> "") )   {
	$accd = $_POST['accd'];
	$name = $_POST['acname'];
	$open = $_POST['opening'];
	$crdr = $_POST['crdr'];
	$yn = $_POST['yn'];
	$cyn = $_POST['cyn'];
	$dyn = $_POST['dyn'];
			//check for record exist or not
		if (  RecSearch("SELECT ACCD FROM acmaster WHERE ACCD = '$accd' AND UID = $userid ") == true ) {
			$check = UpdQry(
							'acmaster',
							"ACNAME = '$name',ACOPEN = $open,OCRDR = '$crdr',YN='$yn',CLOSEAC='$cyn',DASHAC='$dyn'",
							"ACCD = $accd AND UID = $userid");
			if ($check == true){
		        $response ='';
			    $response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Update SuccessFull</div>';
			}else{
			     $response ='';
				 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
			}
		}else{
		    $response ='';
			$response = '</div class = "error"><i class="far fa-times-circle"></i>&nbsp Invalid Entry</div>';
		}
	}else{
	     $response ='';
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function transSave(){
	//check for empty fields
	$response ='';
	global $fyr;
	global $userid;
	$imagename = array();
	if ( ($_POST['amount'] <> "") )   {
		$id = $_POST['id'];
		$date = $_POST['date'];
		$party1 = $_POST['party1'];
		$party2 = $_POST['party2'];
		$category = $_POST['category'];
		$amount = $_POST['amount'];
		$nar = $_POST['nar'];
		$imagename = ( isset ( $_POST['imagename'] ) ? $_POST['imagename'] : (Object)[] );
		$imagename = json_encode($imagename);
		//echo $imagename;
		if (  RecSearch("SELECT * FROM transactions WHERE  UID = $userid AND FYR = '$fyr' AND  TID = $id ") == false ) {
			$check1 = ExeQry(
							'transactions',
							'TID,TDATE,PARTY1,PARTY2,CRDR,CATID,AMT,IMGJSON,SRNO,NARR,FYR,UID',
							" $id,'$date',$party1,$party2,'C',$category,$amount,'$imagename' ,1,'$nar','$fyr',$userid");
			/*$check2 = ExeQry(
							'transactions',
							'TID,TDATE,PARTY1,PARTY2,CRDR,AMT,IMGJSON,SRNO,NARR,FYR,UID',
							" $id,'$date',$party2,$party1,'$crdr',$amount,'$imagename',2,'$nar','$fyr',$userid*/
			if (($check1 == true ) ){  /*&& $check2 == true */
			$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp SuccessFull</div>';
			}else{
				 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
			}
		}else{
			$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Entry Number Already Exist.</div>';
		}
	}else{
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function TransUpdate(){
	$response ='';
	global $userid;
	global $fyr;
	$imagename = array();
	$PrevImgArr = array();
	$deleteimg = array();
	//check for empty fields
	if ( ($_POST['amount'] <> "") )   {
		$id = $_POST['id'];
		$date = $_POST['date'];
		$party1 = $_POST['party1'];
		$party2 = $_POST['party2'];
		$category = $_POST['category'];
		$amount = $_POST['amount'];
		$nar = $_POST['nar'];
		$imagename = ( isset ( $_POST['imagename'] ) ? $_POST['imagename'] : '' );
		if (!empty($imagename)){
			//echo $imagename;
			//Remove Previous Image(s)
			$prevImg = GetDescription('transactions','IMGJSON',"UID = $userid AND TID = $id AND FYR = '$fyr' "); 
			$PrevImgArr = json_decode($prevImg);
			if (!($PrevImgArr)){
				$deleteimg = array_diff($PrevImgArr,$imagename);
				$countimg = count($deleteimg);
				for($i=0;$i<$countimg;$i++){ 
					unlink('images/'.$PrevImgArr[$i]); 
				}
			}
			$imagename = json_encode($imagename);
		}else{
			$imagename = GetDescription('transactions','IMGJSON',"UID = $userid AND TID = $id AND FYR = '$fyr' "); 
		}
		//echo $imagename;
		//check for record exist or not
		if (  RecSearch("SELECT TID FROM transactions WHERE TID = '$id' AND UID = $userid ") == true ) {
			$check1 = UpdQry(
							'transactions',
							"TDATE = '$date',PARTY1 = $party1, PARTY2 = $party2,CATID = '$category',AMT = $amount ,IMGJSON = '$imagename', NARR = '$nar'",
							"TID = $id AND UID = $userid  AND SRNO = 1 AND FYR = '$fyr'");
			/*if ($crdr == "D"){
				$crdr = "C";
			}else{
				$crdr = "D";
			}
				$check2 = UpdQry(
							'transactions',
							"TDATE = '$date',PARTY1 = $party2, PARTY2 = $party1,CRDR = '$crdr',AMT = $amount ,IMGJSON = '$imagename', NARR = '$nar'",
							"TID = $id AND UID = $userid  AND SRNO = 2 AND FYR = '$fyr'");*/
			if ($check1 == true ){                              /*&& $check2 == true*/
			$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Update SuccessFull</div>';
			}else{
				 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
			}
		}else{
			$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Invalid Record Number</div>';
		}
	}else{
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function TransDelete($id){
    $response ='none';
	global $userid;
	global $fyr;
	$eachimg = array();
	$whereclause = "TID = $id AND UID = $userid AND FYR = '$fyr'";
	$imagetodelete = GetDescription('transactions','IMGJSON',"UID = $userid AND TID = $id AND FYR = '$fyr' ");
	if (DltQry('transactions',$whereclause) == true ){
		$response = "<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Delete SuccessFull</div>";
		if (!empty($imagetodelete)){
			$countimg = count(json_decode($imagetodelete,true));
			$eachimg = json_decode($imagetodelete,true);
			for($i=0;$i<$countimg;$i++){ 
					unlink('images/'.$eachimg[$i]); 
				}
		}
	}else{
		$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Failed to Delete</div>";
	}
	echo $response;
}



function UserSave(){
	$response ='';
	$year = $fyr = $sdate = $edate = $admin = $sadmin = '';
	//check for empty fields
	if ( ($_POST['uname'] <> "") &&  ($_POST['upass'] <> "") &&  ($_POST['cpass'] <> "") && ($_POST['fdate']))   {
		$name = $_POST['uname'];
			//check for record exist or not
			if (  RecSearch("SELECT UNAME FROM usermaster WHERE UNAME = '$name' ") == false ){
				$uid = $_POST['uid'];
				$upass =  base64_encode($_POST['upass']);
				$cpass = base64_encode($_POST['cpass']);
				$fdate = $_POST['fdate'];
				$admin = $_POST['admin'];
				$sadmin = $_POST['subadmin'];
				//GET START DATE,END DATE,FINANTIAL YEAR USING FDATE
				####################################################################################
				$year = intval($fdate);
				$year = substr($year,-2);
				$fyr = ($year).($year+1);
				$sdate =  $fdate;
				$edate = date('Y-m-d',strtotime('+1 years',strtotime($fdate . '-1 day')));
				####################################################################################

				if (  $upass == $cpass ){
					$check = ExeQry(
									'usermaster',
									'UID,UNAME,UPASS,ADMIN,SUBADMIN,FDATE',
									"$uid,'$name','$upass','$admin','$sadmin','$fdate' ");
					if ($check == true){
					    $uid = GetDescription('usermaster','UID',"UNAME = '$name' ");
						$stat = ExeQry(
									'userstat',
									'UID,FYR,SDATE,EDATE',
									"$uid ,'$fyr','$sdate','$edate' ");
						if ($stat == true){
							$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp SuccessFull</div>';
						}else{
							$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Stat Error!</div>';
							UserDelete($uid);
						}
					}else{
						 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
					}
				}else{
					$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Both Passwords Are Not Same</div>';
				}
			}else{
				 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp User Name Already Exist</div>';
			}
	}else{
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}


function UserUpdate(){
	$response ='';
	//check for empty fields
	if ( ($_POST['uname'] <> "") &&  ($_POST['upass'] <> "") &&  ($_POST['cpass'] <> "") )   {
			$name = $_POST['uname'];
			$uid = $_POST['uid'];
			$admin = $_POST['admin'];
			$sadmin = $_POST['subadmin'];
			$upass = base64_encode($_POST['upass']);
			$cpass = base64_encode($_POST['cpass']);
			$fdate = $_POST['fdate'];
			//check for record exist or not
		if (  RecSearch("SELECT UID FROM usermaster WHERE UID = '$uid'  ") == true ) {
			if (  $upass == $cpass ){
				$check = UpdQry(
								'usermaster',
								"UNAME = '$name',UPASS = '$upass',ADMIN = '$admin',SUBADMIN = '$sadmin'",
								"UID = $uid");
				if ($check == true){
				$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Update SuccessFull</div>';
				}else{
					 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
				}
			}else{
				$response =  "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Both Passwords Are Not Same</div>";
			}
		}else{
			$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Invalid Entry</div>';
		}
	}else{
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function UserDelete($id){
    $response ='';
	$whereclause = "UID =".$id;
	if (DltQry('usermaster',$whereclause) == true ){
		if (DltQry('userstat',$whereclause) == true ){
			if (DltQry('acmaster',$whereclause) == true ){
				if( DltQry('acmaster',$whereclause) == true  ){
					if (DltQry('category',$whereclause) == true ){
						if (DltQry('transactions',$whereclause) == true ){
							$response = "<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Delete SuccessFull</div>";
						}
					}
				}
			}
		}else{
			$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Cound Not Delete User Stats</div>";
		}
	}else{
		$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Failed to Delete</div>";
	}
	echo $response;
}

function GetFyr(){
	$response = '';
	$username = $_POST['username'];
	$userid = GetDescription('usermaster','UID',"UNAME = '$username' ");
	$response = FillCombo('userstat','FYR','FYR',"UID =$userid ORDER BY SDATE DESC" );
	echo $response;
}

function PassUpdate(){
	$response = '';
	$oldpass = $newpass = $confpass = '';
	$uid = $_POST['uid'];
	$oldpass = trim(base64_encode($_POST['upass']));
	$newpass = trim(base64_encode($_POST['npass']));
	$confpass =trim(base64_encode($_POST['cpass']));
	$getoldpass = '';

	$getoldpass = trim(GetDescription('usermaster','UPASS',"UID = '$uid' "));
	if ( $oldpass <> "" && $newpass <> "" && $confpass <> "" )  {
		if ( $oldpass <> $newpass ){
			if ( $oldpass == $getoldpass){
				if ( $confpass == $newpass ){
					$check = UpdQry(
									'usermaster',
									"UPASS = '$newpass'",
									"UID = $uid");
						if ($check == true){
							$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Password Change SuccessFull</div>';
						}else{
							$response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
						}
				}else{
					$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp New And Confirm Password Should Match!</div>";
				}
			}else{
				$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Wrong Old Password!</div>";
			}
		}else{
			$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp New And Old Passwords Are Same!</div>";
		}
	}else{
		$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Fill All Password Fields!</div>";
	}
	echo $response;
}

function catSave(){
	//check for empty fields
	unset($response);
	global $userid;
	if ( ($_POST['catname'] <> ""))   {
		$name = $_POST['catname'];
			//check for record exist or not
			if (  RecSearch("SELECT CATNAME FROM category WHERE CATNAME = '$name' AND UID = $userid  ") == false ){
				$catid = $_POST['catid'];
				$catname = $_POST['catname'];
				$active = $_POST['catactive'];
				$check = ExeQry(
								'category',
								'CATID,CATNAME,ACTIVE,UID',
								"$catid ,'$catname','$active',$userid ");
				if ($check == true){
			    $response ='';
				$response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp SuccessFull</div>';
				}else{
				     $response ='';
					 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
				}
			}else{
			     $response ='';
				 $response = '<div class ="error"><i class="far fa-times-circle"></i>&nbsp Categry Name Already Exist</div>';
			}
	}else{
	     $response ='';
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function catUpdate(){
	unset($response);
	global $userid;
	//check for empty fields
	if ( ($_POST['catname'] <> "") )   {
	$catid = $_POST['catid'];
	$catname = $_POST['catname'];
	$active = $_POST['catactive'];
			//check for record exist or not
		if (  RecSearch("SELECT CATID FROM category WHERE CATID = $catid AND UID = $userid ") == true ) {
			$check = UpdQry(
							'category',
							"CATNAME = '$catname',ACTIVE = '$active'",
							"CATID = $catid AND UID = $userid");
			if ($check == true){
		        $response ='';
			    $response = '<div class = "success"><i class="far fa-check-circle"></i>&nbsp Update SuccessFull</div>';
			}else{
			     $response ='';
				 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fail</div>';
			}
		}else{
		    $response ='';
			$response = '</div class = "error"><i class="far fa-times-circle"></i>&nbsp Invalid Entry</div>';
		}
	}else{
	     $response ='';
		 $response = '<div class = "error"><i class="far fa-times-circle"></i>&nbsp Fill All Fields</div>';
	}
	echo $response;
}

function catDelete($id){
	global $userid;
	$whereclause = "CATID = $id AND UID = $userid";
	if (  RecSearch("SELECT * FROM transactions WHERE CATID = $id AND UID = $userid "  ) == false ){
		if (DltQry('category',$whereclause) == true ){
		    $response ='';
			$response = "<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Delete SuccessFull</div>";
		}else{
		    $response ='';
			$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Failed to Delete</div>";
		}
	}else{
	    $response ='';
		$response = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Can Not Delete, Category Found In Transaction!</div>";
	}
	echo $response;
}

function filterchartsel($selectedvalue,$iscompare){
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
	global $fyr;
	global $userid;
	$is_compare_bool = ($iscompare === "true"); //convert to boolean from text
	$style = ($is_compare_bool) ? "width:35%;float:left;height:35px" :  "width:50%;float:left;height:35px";
	if ($selectedvalue === "Month"){
		$monthmin = date("Y-m", strtotime($startdate));
		$monthmax = date("Y-m", strtotime($enddate));
		$calendarmonth =  date('Y-m'); //date("Y")."-".date("m");
		echo '<input type="month" id="chartmonth" name="chartmonth" class="form-control" 
		value='.$calendarmonth.'
		min = '.$monthmin.' max = '.$monthmax.'
		style = '.$style.'>';
	}else{
		echo '<select name="fyr" id = "fyr" class="cmb" style='.$style.'>';
			FillCombo('userstat','FYR','FYR',"UID =$userid ORDER BY SDATE DESC" );
		echo '</select>';
	}

	if ($is_compare_bool){
		if ($selectedvalue === "Month"){
			$monthmin = date("Y-m", strtotime($startdate));
			$monthmax = date("Y-m", strtotime($enddate));
			$calendarmonth =  date('Y-m'); //date("Y")."-".date("m");
			echo '<input type="month" id="chartcompmonth" name="chartmonth" class="form-control" 
			value='.$calendarmonth.'
			style = '.$style.';margin-left:5px>';
		}else{
			echo '<select name="fyr" id = "compfyr" class="cmb" style='.$style.';margin-left:5px>';
				FillCombo('userstat','FYR','FYR',"UID =$userid ORDER BY SDATE DESC" );
			echo '</select>';
		}
	}

}