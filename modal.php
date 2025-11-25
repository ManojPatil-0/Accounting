<?php
include_once ( 'common.php' );

$rowid  = 0;
$transid =  0 ;
$date = '';
$narr = '';
$amount = 0;

@session_start();

if (isset( $_SESSION['uid'] )){
	$uid = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
}else{
	$uid = 0;
	$fyr = '1819';
}

if (isset ( $_POST['rowid'] )){
	$rowid = $_POST['rowid'];
}

global $sqlqry;
global $connect;
global $con;
$imgarr = array();


$sqlqry = "";

$sqlqry = "SELECT * FROM transactions WHERE TID = $rowid AND UID = $uid AND FYR = '$fyr'";
$connect = mysqli_query($con , $sqlqry);
if ( mysqli_num_rows($connect) > 0 ){
		while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
			$transid = $rows['TID'];
			$date = $rows['TDATE'];
			$date = date("d-m-Y", strtotime($date));
			$narr = $rows['NARR'];
			$category = GetDescription('category','CATNAME','CATID ='.$rows['CATID'].' AND UID='.$uid);
			$amount = $rows['AMT']; 
			$imagejson = $rows['IMGJSON']; 
		}
}

//MODAL CONTENT
echo '<div class="modal-dialog" style = "background-color:white">';
	echo '<div class="modal-content">';
		echo '<div class="modal-header">';
			echo '<div id = modalamt> &#8377 '.number_format($amount,2).'</div>';
			echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
		echo '</div>';
		echo '<div class="modal-body">';
			echo '<strong>Transaction No. :</strong>   '.$transid.'</br></br>'; 
			echo '<strong>Date :</strong>   '.$date.'</br></br>';
			echo '<strong>Category :</strong>   '.$category.'</br></br>';
			echo '<strong>Narration :</strong>    '.$narr.'</br>';
			if ( json_decode($imagejson,true)){
				echo '</br>';
				//echo $imagejson;
				$imgarr = json_decode($imagejson);
				$count = count($imgarr);
				//cachebuster date is added to avoid caching of image 
				//<a> tag added for downloading the image.
				//echo '<a href = "download.php?imagename='.$imgname.'"><img src='.$showimage.'?cachebuster='.Date("Y-m-d H:i:s").' id = "modalimage"></a></br>';
				echo '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">';
					echo '<ol class="carousel-indicators">';
						if ($count > 1){
							for($i=0;$i<$count;$i++){
								if ($i==0){
									echo '<li data-target="#carouselExampleIndicators" data-slide-to="'.$i.'" class="active"></li>';
								}else{
									echo '<li data-target="#carouselExampleIndicators" data-slide-to="'.$i.'" ></li>';
								}
							}
						}
					echo '</ol>';
					echo '<div class="carousel-inner">';
						$i=1;
						foreach ((array) $imgarr as $newimg){
							if ($i==1) {
								echo '<div class="item active">';
							}else{
								echo '<div class="item">';
							}
							echo '<a  href=images/'.$newimg.' download> <img src=images/'.$newimg.'?cachebuster='.Date("Y-m-d H:i:s").' id = "modalimage" alt = "Image"'.$i.'></a></br>';
							$i = $i+1;
								echo '</div>';
						}
					echo '</div>';
					if ($count > 1 ) {
						echo '<a class="left carousel-control" href="#carouselExampleIndicators" role="button" data-slide="prev">';
							echo '<span class="glyphicon glyphicon-chevron-left"></span>';
						echo '</a>';
						echo '<a class="right carousel-control" href="#carouselExampleIndicators" role="button" data-slide="next">';
							echo '<span class="glyphicon glyphicon-chevron-right"></span>';
						echo '</a>';
					}
				echo '</div>';
			}
		echo '</div>';
		echo '<div class="modal-footer">';
			echo '<div id = "quicktransaction">For Quick Edit <a href = "QuickTransaction.php?transid='.$transid.'">Click here</a></div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

?>   
 