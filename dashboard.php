<?php 
if (!isset($_SESSION)){
session_start();
}

include_once ('common.php');

global $connect;
global $con;

if (isset( $_SESSION['uid'] )){
	$uid = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
}
if (isset($_SESSION['sdate'])){
	$sdate = $_SESSION['sdate'];
	$edate = $_SESSION['edate'];
}

$sqlqry = "
			SELECT A.ACCD , A.ACNAME as ACCOUNT ,
			(
			(CASE WHEN A.OCRDR = 'D' AND A.UID = $uid AND A.YN = 'Y' THEN A.ACOPEN ELSE 0 END) -
			(CASE WHEN A.OCRDR = 'C'  AND A.UID = $uid AND A.YN = 'Y'  THEN A.ACOPEN ELSE 0 END)
			) +
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY1 AND TDATE < '$sdate')-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY1 AND TDATE < '$sdate')
			)+
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY2  AND TDATE < '$sdate')-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY2  AND TDATE < '$sdate')
			) AS OPENING,
			(
			(CASE WHEN A.OCRDR = 'D' AND A.UID = $uid AND A.YN = 'Y' THEN A.ACOPEN ELSE 0 END) -
			(CASE WHEN A.OCRDR = 'C'  AND A.UID = $uid AND A.YN = 'Y'  THEN A.ACOPEN ELSE 0 END)
			)+
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY1  AND TDATE < '$sdate')-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY1  AND TDATE < '$sdate')
			)+
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY2  AND TDATE < '$sdate')-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND A.YN = 'Y' AND A.ACCD = PARTY2  AND TDATE < '$sdate')
			)+
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE >= '$sdate' AND TDATE <= '$edate' AND A.ACCD = PARTY1 )-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE >= '$sdate' AND TDATE <= '$edate' AND A.ACCD = PARTY1 )
			)+
			(
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE >= '$sdate' AND TDATE <= '$edate' AND A.ACCD = PARTY2 )-
			(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE >= '$sdate' AND TDATE <= '$edate' AND A.ACCD = PARTY2 )
			) AS CLOSING
			FROM acmaster AS A 
			WHERE  A.DASHAC = 'Y' AND A.CLOSEAC = 'N' AND A.UID = $uid
			GROUP BY A.OCRDR,A.ACNAME,A.ACOPEN,A.ACCD,A.UID,A.YN
			ORDER BY ACCOUNT ";
$connect = mysqli_query($con , $sqlqry) ;
echo '<table class = table cellspacing = 5 cellpadding = 3 id = "dashtable" >';
	echo '<tr id="scrolltr">' ;
		echo '<th style = "display : none" ></th>';
		echo '<th>Account Name</th>';
		echo '<th class="text-right" >Opening</th>';
		echo '<th style = "display : none" ></th>';
		echo '<th class="text-right">Closing</th>';

	echo '</tr>';
if ( mysqli_num_rows($connect) > 0 ){
	while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		echo '<tr >' ;
			echo '<td style = "display : none" >'.$rows['ACCD'].'</td>' ;
			echo '<td align = "LEFT"  ><a href="#" onclick=loadReport('.$rows['ACCD'].')>'.$rows['ACCOUNT'].'</a></td>'; 
			echo '<td align = "RIGHT" >'.($rows['OPENING'] >= 0?number_format($rows['OPENING'],2).' Dr':number_format(-1*$rows['OPENING'],2).' Cr' ).'</td>'; 
			echo '<td style = "display : none" ></td>';
			echo '<td align = "RIGHT" >'.($rows['CLOSING'] >= 0?number_format($rows['CLOSING'],2).' Dr':number_format(-1*$rows['CLOSING'],2).' Cr').'</td>'; 
		echo '</tr>';
	}
}else{
	echo '<td align = "CENTER" colspan="6" style	= "color:red">No Account Selected For Dash Board.Set "In Dash Board Yes/No" Option In Account Master To "Yes" To Make Account Visible.</td>'; 
}
echo '</table >';

?>