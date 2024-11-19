<?php
//Database variables

$host = 'localhost';
$db = 'accounting';
$username = 'root';
$password =  '';

/*$host = 'localhost';
$db = 'id20544233_accounting';
$username = 'id20544233_root';
$password =  'Manoj@123456';*/


//connection variables
$con;
$flag;
$sqlqry;
$connect;

// Entrymodes
global $entrymode ;
static $addentry = "A";
static $editentry = "E";
static $viewentry = "V";
static $deleteentry = "D";

//Cookie variables
$cookie_name = '';
$cookie_value = '';

$error;
$finyr;
$userid;
$whereclause;

function ConnectDb(){
	global $host;
	global $db;
	global $username;
	global $password;
	global $con;

	$con = mysqli_connect($host, $username, $password, $db) or die('Connection Failed');
	return $con;
}
//for report
$rptcon = mysqli_connect($host, $username, $password, $db) or die('Connection Failed');
$rptcon2 = mysqli_connect($host, $username, $password, $db) or die('Connection Failed');

//connect to database 
ConnectDb();


//Record Search
function RecSearch($qry){
	global $connect; 
	global $con;
	//echo $qry;
	$connect = mysqli_query($con, $qry);
	if (mysqli_num_rows($connect)>0){
		return true;
	}else
	{
		return false;
	}
}


//Get Value
function GetDescription($table , $field, $where = null){
	global $connect;
	global $con;
	global $sqlqry;
	global $whereclause;
	
	$sqlqry = "";
	$whereclause = ( empty($where) ? "" : ' WHERE '.$where ) ;
	$sqlqry = ' SELECT '. $field. ' FROM '. $table . $whereclause;
	//echo $sqlqry;
	$connect = mysqli_query($con , $sqlqry);
	if (mysqli_num_rows($connect) > 0 ){
		while($rows = mysqli_fetch_array( $connect,MYSQLI_ASSOC)){
			return $rows[$field];
		}
	}
}

//insert Query
function ExeQry($table, $field ,$value){
	global $sqlqry;
	global $con;
	global $connect;
	
	$sqlqry = "";
	$sqlqry = 'INSERT INTO '.$table .' ( '. $field.' ) VALUES ( '.$value . ')';
	//echo $sqlqry;
	$connect = mysqli_query($con, $sqlqry);
	if ($connect){
		return true;
	}else{
		return false;
	}
}

//Update Query
function UpdQry($table , $value , $where = "" ){
	global $sqlqry;
	global $con;
	global $connect;
	global $whereclause;
	$sqlqry = "";
	$whereclause = ( $where == "" ? "" : ' WHERE '.$where ) ;
	$sqlqry = '	UPDATE '.$table. ' SET '.$value.' '.$whereclause ;
	//echo $sqlqry;
	$connect = mysqli_query($con, $sqlqry);
	if ($connect){
		return true;
	}else{
		return false;
	}
}

//Delete Query
function  DltQry($table ,$where = "" ){
	global $sqlqry;
	global $con;
	global $connect;
	global $whereclause;
	$sqlqry = "";
	$whereclause = ( $where == "" ? "" : ' WHERE '.$where ) ;
	$sqlqry = '	DELETE FROM '.$table.' '.$whereclause ;
	//echo $sqlqry;
	$connect = mysqli_query($con, $sqlqry);
	if ($connect){
		return true;
	}else{
		return false;
	}
}
//fill combo boxes
function FillCombo($table,$field1,$field2,$where = null){
	global $sqlqry;
	global $con;
	global $connect;
	global $whereclause;
	$sqlqry = "";
	$whereclause = ( empty($where) ? "" : ' WHERE '.$where ) ;
	$sqlqry = "SELECT ". $field1 ." , ".$field2 ." FROM ".$table." ".$whereclause ;
	//echo $sqlqry;
	$connect = mysqli_query($con,$sqlqry);
	while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		echo '<option value = '. $rows[$field1] .'>'.$rows[$field2].' </option>';
		}
}

//get Max Number
function MaxNumber($table,$id,$where = null){
	global $sqlqry;
	global $con;
	global $connect;
	global $whereclause;
	$maxnum = 0;
	$sqlqry = "";
	$whereclause = ( empty($where) ? "" : ' WHERE '.$where ) ;
	$sqlqry = "SELECT MAX(".$id.") as Max From ".$table." ".$whereclause ;
	$connect = mysqli_query($con , $sqlqry);
	if (mysqli_num_rows($connect) > 0 ){
		while($rows = mysqli_fetch_array( $connect,MYSQLI_ASSOC)){
			return  $rows['Max'] + 1;
		}
	}
}

//Last Seen
function lastSeen($userid,$count){
	date_default_timezone_set('Asia/Kolkata');
	$currentdatetime = date('Y-m-d H:i:s');
	if (  RecSearch("SELECT UID FROM lastseen WHERE UID = $userid ") == false ){
		$seenqry = ExeQry('lastseen','UID,SEENTIME,CURTIME',"$userid,'$currentdatetime','$currentdatetime'"); 
	}else{
		if ($count == 1){
			$seenqry = UpdQry('lastseen',"SEENTIME = CURTIME","UID = $userid");
			$seenqry = UpdQry('lastseen',"CURTIME = '$currentdatetime'","UID = $userid");
		}else{
			$seenqry = UpdQry('lastseen',"SEENTIME = '$currentdatetime',CURTIME = '$currentdatetime'","UID = $userid");
		}
	}
}

Function lastSeenDays($userid){
	$LastSeen = GetDescription('lastseen','TIMESTAMPDIFF(SECOND, SEENTIME, CURTIME)',"UID = $userid"); 
	//echo 'Last Seen '.$LastSeen;
	if ($LastSeen >= 2419200) {
		$LastSeen = GetDescription('lastseen','SEENTIME',"UID = $userid"); 
		echo 'Last Login '.date('d/m/Y h:i a ', strtotime($LastSeen));
	} elseif ($LastSeen >= 86400) {
		echo "Last Login " . ceil($LastSeen / 86400) . " days ago";
	} elseif ($LastSeen >= 3600) {
		echo "Last Login " . ceil($LastSeen / 3600) . " hours ago";
	} elseif ($LastSeen >= 60) {
		echo "Last Login " . ceil($LastSeen / 60) . " minutes ago";
	} elseif ($LastSeen <= 2) {
		echo "Online";		
	}else{
		echo "Last Login less than a minute ago";
	}
}

?>

































