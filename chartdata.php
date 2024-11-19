<?php
include_once('common.php');

session_start();
$f_yr = $_SESSION['fyr'];
$u_id = $_SESSION['uid'];
$prevfinyr = "";

if( isset($_POST['month']) || isset($_POST['year'])){
    $selmonth = $_POST['month'];
    $selyear = $_POST['year'];
    $choice = $_POST['choice'];
    
    if ( $choice == "M" ){
        $firstday = date("Y-m-d", strtotime($selmonth));
        $lastday =  date("Y-m-t", strtotime($selmonth));
        $prevfirstday = date("Y-m-d", strtotime ("-1 month",strtotime ($selmonth)));
        $prevlastday = date("Y-m-t", strtotime ("-1 month",strtotime ($selmonth)));
    }else{
        $firstday = $_SESSION['sdate'];
        $lastday =  $_SESSION['edate'];

        $prevfinyr = GetDescription('userstat','FYR', 'FYR not in ('."$f_yr".') AND UID = '.$u_id.' AND SDATE < (SELECT SDATE FROM userstat where FYR = '."$f_yr".' AND UID = '.$u_id.')order by sdate DESC LIMIT 1');
        $prevfirstday = GetDescription('userstat','SDATE',"UID = $u_id AND FYR = '$prevfinyr' " );
        $prevlastday = GetDescription('userstat','EDATE',"UID = $u_id AND FYR = '$prevfinyr' " );   
    }
}
$pichartdata = array();
$data_point = array();
$data_point_2 = array();
$catarr = array('NA'); // default NA as SQL have NA on blank category
$catgarr1 = array();
$catgarr2 = array();
$amount = array();
$financeyear = array();
$pichartamt = 0;
$barchartamt = 0;
$i = 0;

$sqlqry = "SELECT CATNAME FROM category WHERE ACTIVE = 'Y' ORDER BY CATNAME";
$connect = mysqli_query($con , $sqlqry) ;
if ( mysqli_num_rows($connect) > 0 ) {
	while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		$catarr[] = $rows['CATNAME'];
	}
}

//echo  print_r($catarr);

$sqlqry = "
		SELECT SUM(T.AMT) as Amt,
		case when isnull(C.CATNAME) or C.CATNAME = '' then 'NA' else C.CATNAME end as Catname
		FROM transactions AS T 
		INNER JOIN category AS C ON C.CATID = T.CATID AND C.ACTIVE = 'Y' AND C.UID = T.UID
		WHERE T.UID = $u_id AND T.TDATE BETWEEN '$firstday' AND '$lastday' 
		GROUP BY C.CATNAME

		UNION ALL

		SELECT SUM(T.AMT) as Amt,'NA' as Catname
		FROM transactions AS T 
		WHERE T.UID = $u_id AND T.TDATE BETWEEN '$firstday' AND '$lastday' AND T.CATID = 0
		HAVING SUM(T.AMT) > 0
		ORDER BY Catname";

$connect = mysqli_query($con , $sqlqry) ;
if ( mysqli_num_rows($connect) > 0 ) {
	while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		$pichartdata[] = array("label"=>$rows['Catname'],"y"=>$rows['Amt']);
		$data_point[] = array("label"=>$rows['Catname'],"y"=>$rows['Amt']);
		$catgarr1[] = $rows['Catname'];
		$pichartamt = $pichartamt + $rows['Amt'];
	}
}
$sqlqry ="";
$sqlqry = "
		SELECT SUM(T.AMT) as Amt,
		case when isnull(C.CATNAME) or C.CATNAME = '' then 'NA' else C.CATNAME end as Catname
		FROM transactions AS T 
		INNER JOIN category AS C ON C.CATID = T.CATID AND C.ACTIVE = 'Y' AND C.UID = T.UID
		WHERE T.UID = $u_id AND T.TDATE BETWEEN '$prevfirstday' AND '$prevlastday' 
		GROUP BY C.CATNAME

		UNION ALL

		SELECT SUM(T.AMT) as Amt,'NA' as Catname
		FROM transactions AS T 
		WHERE T.UID = $u_id AND T.TDATE BETWEEN '$prevfirstday' AND '$prevlastday' AND T.CATID = 0
		HAVING SUM(T.AMT) > 0
		ORDER BY Catname";
		
$connect = mysqli_query($con , $sqlqry) ;
if ( mysqli_num_rows($connect) > 0 ) {
	while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		$data_point_2[] = array("label"=>$rows['Catname'],"y"=>$rows['Amt']);
		$catgarr2[] = $rows['Catname'];
		$barchartamt = $barchartamt + $rows['Amt'];
	}
}
$result_1 = array_diff($catarr,$catgarr1);
sort($result_1);
$result_2 = array_diff($catarr,$catgarr2);
sort($result_2);
for( $i = 0; $i < count($result_1); $i++ ){
	$data_point[] = array("label"=>$result_1[$i],"y"=>0);
}
for( $i = 0; $i < count($result_2); $i++ ){
	$data_point_2[] = array("label"=>$result_2[$i],"y"=>0);
}
// fill amount array;
$amount = [$pichartamt,$barchartamt];
//Fill current and Previous fin years
$financeyear =[$f_yr,$prevfinyr];

//sort the array's
sort($pichartdata);
sort($data_point);
sort($data_point_2);



$reponsearr = array("doughnout"=>$pichartdata,"bar_1"=>$data_point,"bar_2"=>$data_point_2,"amount"=>$amount,"year"=>$financeyear);

//reponse as JSON object
echo  json_encode($reponsearr, JSON_NUMERIC_CHECK);
?> 