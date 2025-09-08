<?php
	///////  Pagination //////////////////////////////////
	include_once ('common.php');

	$page = $perpage = $limit = $total = $pages = 0;
	$cmb = (isset($_POST['cmb'])? $_POST['cmb'] : 0);
	$reporttype = $_POST['type'];
	$perpage = 10;
	$total = Getdescription("tmpreport","COUNT(ACCD)");
	$pages = ceil($total/$perpage);
	//$page = (isset($_POST['page'])) && $_POST['page'] > 0 ? ($_POST['page']) : 1;
	//limit for the first and last page
	if ((isset($_POST['page'])) && $_POST['page'] > 0){
		$page = $_POST['page'];
		if ( $page > $pages ){
			$page = $pages;
		}else{
			$page = $_POST['page'];
		}
	}else{
		$page = 1;
	}
	echo '<div id = pageval style = "display:none;">'.$page.'</div>';

	$limit = ($page > 1) ? ($page * $perpage) - $perpage : 0;

	$sqlqr = "";
	if ($reporttype !== "Narration Rpt"){
		$sqlqry = "SELECT * FROM tmpreport WHERE ACCD = $cmb  LIMIT $limit,$perpage";
	}else{
		$sqlqry = "SELECT * FROM tmpreport WHERE NARR LIKE '%$cmb%'  LIMIT $limit,$perpage";
	}
	$conn = mysqli_query($con,$sqlqry);
	echo '<div class = "curpage">'.$page.'/'.$pages.'</div>';
	echo '<div id="sugg">Rotate Screen For Detailed View </div>';
	echo '<div id="sugg2" style = "font-size:10px;text-align:center"> Click On First Account Name For Narration </div>';
	//////////////////////////////////////////////////////
	echo '<div class = "table-responsive">';
		echo '<table class = table cellspacing = 5 cellpadding = 3 id = "table" >';
			echo '<tr>';
				echo '<th style = "display : none" >Id</th>';
				if ($reporttype === "Report"){
					echo '<th>Date</th>';
					//echo '<th>Account</th>';
					echo '<th>Account</th>';
					echo '<th>Narration</th>';
					echo '<th class="text-right" >Debit</th>';
					echo '<th class="text-right" >Credit</th>';
					echo '<th class="text-right">Balance</th>';
				}else{
					echo '<th>Date</th>';
					echo '<th>Account</th>';
					echo '<th style="display:block;background-color: rgba(0,123,255,.0627451);color:rgba(0,123,255,.84)">Account</th>';
					echo '<th style="display:none">Narration</th>';
					echo '<th class="text-right" style = "background-color: rgba(40,167,69,.12549);color:rgba(40,167,69,.84)" >Debit</th>';
					echo '<th class="text-right" style = "background-color:  rgba(255,7,58,.12549);color:ff073a"  >Credit</th>';
				}
			echo '</tr>';
			//intialize a variable
			$i = 0;
			while ( $rows = mysqli_fetch_array($conn,MYSQLI_ASSOC) ){	
				if ($reporttype === "Report"){
					if ($i == 0) { //print account head on first line
						echo '<tr>';
							echo '<td  colspan="6" id = "achead">'.$rows['ACCOUNT'].'</td>' ;
						echo '</tr>';
					}
					//increment i;
					$i += 1;
						//echo '<tr style = "background-color :'.$rows['COLOR'].'">' ;
						echo '<tr '.( $i == mysqli_num_rows($conn) && $pages == $page ? ' style = "background-color : '.$rows['COLOR'].' !important "' : ' style = "background-color : '.$rows['COLOR'].' "' ).' >' ;
							echo '<td style = "display : none" >'.$rows['ID'] .'</td>' ;
							//format date to dd/mm/yyyy//////
							$date = $rows['DATE'];
							$date = date("d-m-Y", strtotime($date));
							////////////////////////////////
							echo '<td>'.( $rows['DATE'] == '1900-01-01'? "-" : $date ) .'</td>' ;
							//echo '<td data-toggle="modal" data-target="#myModal" >'.$rows['ACCOUNT'].'</td>' ;
							echo '<td data-toggle="modal" data-target="#myModal" >'.( $rows['SUBACCOUNT'] == "0" ? "": $rows['SUBACCOUNT'] ).'</td>'; 
							echo '<td align = "LEFT">'.( $rows['NARR'] == "" ? '-' : $rows['NARR'] ).'</td>'; 
							echo '<td align = "RIGHT" >'.( $rows['DEBIT'] == 0 ? '-' : number_format($rows['DEBIT'],2) ).'</td>'; 
							echo '<td align = "RIGHT" >'.( $rows['CREDIT'] == 0 ? '-' : number_format($rows['CREDIT'],2) ).'</td>'; 
							echo '<td align = "RIGHT" '.( $rows['BALANCE'] >= 0 ? 'style = "color:green "': 'style = "color:red ".' ).' >'.( $rows['BALANCE'] >= 0 ? number_format($rows['BALANCE'],2).' Dr':number_format(abs($rows['BALANCE']),2, '.', '').' Cr').'</td>' ;
						echo '</tr>';
				}else{
					//increment i;
					$i += 1;
					//echo '<tr style = "background-color :'.$rows['COLOR'].'">' ;
					echo '<tr '.( $i == mysqli_num_rows($conn) && $pages == $page ? ' style = "background-color : '.$rows['COLOR'].' !important "' : ' style = "background-color : '.$rows['COLOR'].' "' ).' >' ;
						echo '<td style = "display : none" >'.$rows['ID'] .'</td>' ;
						//format date to dd/mm/yyyy//////
						$date = $rows['DATE'];
						$date = date("d-m-Y", strtotime($date));
						////////////////////////////////
						echo '<td>'.( $rows['DATE'] == '1900-01-01'? "-" : $date ) .'</td>' ;
						echo '<td data-toggle="modal" data-target="#myModal" >'.$rows['ACCOUNT'].'</td>' ;
						echo '<td data-toggle="modal" data-target="#myModal" style="display:block">'.( $rows['SUBACCOUNT'] == "0" ? "": $rows['SUBACCOUNT'] ).'</td>'; 
						echo '<td align = "LEFT" style="display:None">'.( $rows['NARR'] == "" ? '-' : $rows['NARR'] ).'</td>'; 
						echo '<td align = "RIGHT" >'.( $rows['DEBIT'] == 0 ? '-' : number_format($rows['DEBIT'],2) ).'</td>'; 
						echo '<td align = "RIGHT" >'.( $rows['CREDIT'] == 0 ? '-' : number_format($rows['CREDIT'],2) ).'</td>'; 
						//echo '<td align = "RIGHT" '.( $rows['BALANCE'] >= 0 ? 'style = "color:green "': 'style = "color:red ".' ).' >'.( $rows['BALANCE'] >= 0 ? number_format($rows['BALANCE'],2).' Dr':number_format(abs($rows['BALANCE']),2, '.', '').' Cr').'</td>' ;
					echo '</tr>';
				}
			}	
		echo '</table >';
	echo '</div >';
?>
