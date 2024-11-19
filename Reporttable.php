<script src="cookie.js"></script>
<?php
session_start();

$frdt = $todt = $cmb = $opt = $fyr = $uid = $partyid = $narration = "";
$bal = $granddr = $garandcr = $transid = 0;

if (isset( $_SESSION['uid'] )){
	$uid = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
}else{
	$uid = 1;
	$fyr = '1819';
}


include_once ('common.php');

global $sqlqry;
global $connect;
global $con;
global $rptcon;
global $rptcon2;

if ( isset( $_POST['fromdt']) ) {
	$frdt = $_POST['fromdt'];
	$todt = $_POST['todt'];
	$cmb = $_POST['cmb'];
	$opt = $_POST['opt'];
}

$sqlqry = "";
$sqlqry = "DELETE FROM tmpreport";
$connect = mysqli_query($con , $sqlqry);
$connect = "";

$sqlqr = "SELECT ACCD FROM acmaster WHERE UID = $uid";
$conn = mysqli_query($con , $sqlqr);

/*while ( $m = mysqli_fetch_array($conn,MYSQLI_ASSOC) ){
	$partyid = $m['ACCD'];*/

	//$sqlqry = "call REPORT($uid,'$fyr','$frdt', '$todt','P',$cmb)";
	
	$sqlqry = "
				SELECT T.TID AS SRNO , T.TDATE AS DATE , A.ACCD ,A.ACNAME AS ACCOUNT, A1.ACNAME AS SUBACCOUNT,T.NARR AS NARRATION,
				(CASE WHEN T.CRDR = 'D' AND T.UID = $uid THEN T.AMT ELSE 0 END) AS DEBIT,
				(CASE WHEN T.CRDR = 'C' AND T.UID = $uid THEN T.AMT ELSE 0 END) AS CREDIT,
				(CASE WHEN T.CRDR = 'D' AND T.UID = $uid THEN T.AMT ELSE 0 END) - (CASE WHEN T.CRDR = 'C' 
				AND T.UID = $uid THEN T.AMT ELSE 0 END) AS BALANCE
				FROM transactions AS T 
				INNER JOIN acmaster AS A ON A.ACCD = T.PARTY1 AND A.UID = T.UID
				INNER JOIN acmaster AS A1 ON A1.ACCD = T.PARTY2 AND A1.UID = T.UID
				WHERE  T.UID = $uid AND T.FYR = '$fyr' AND (T.TDATE BETWEEN '$frdt' AND '$todt' ) AND T.PARTY1 = $cmb 

				UNION ALL
				
				SELECT T.TID AS SRNO , T.TDATE AS DATE , A1.ACCD ,A1.ACNAME AS ACCOUNT, A.ACNAME AS SUBACCOUNT,T.NARR AS NARRATION,
				(CASE WHEN T.CRDR = 'C' AND T.UID = $uid THEN T.AMT ELSE 0 END) AS DEBIT,
				(CASE WHEN T.CRDR = 'D' AND T.UID = $uid THEN T.AMT ELSE 0 END) AS CREDIT,
				(CASE WHEN T.CRDR = 'C' AND T.UID = $uid THEN T.AMT ELSE 0 END) - (CASE WHEN T.CRDR = 'D' 
				AND T.UID = $uid THEN T.AMT ELSE 0 END) AS BALANCE
				FROM transactions AS T 
				INNER JOIN acmaster AS A ON A.ACCD = T.PARTY1 AND A.UID = T.UID
				INNER JOIN acmaster AS A1 ON A1.ACCD = T.PARTY2 AND A1.UID = T.UID
				WHERE  T.UID = $uid AND T.FYR = '$fyr' AND (T.TDATE BETWEEN '$frdt' AND '$todt' ) AND T.PARTY2 = $cmb 

				UNION ALL

				SELECT '' AS SRNO,'' AS DATE, A.ACCD , A.ACNAME as ACCOUNT , 'Opening' AS SUBACCOUNT,'' AS NARRATION,
				(
				(CASE WHEN A.OCRDR = 'D' AND A.UID = $uid THEN A.ACOPEN ELSE 0 END) -
				(CASE WHEN A.OCRDR = 'C'  AND A.UID = $uid  THEN A.ACOPEN ELSE 0 END)
				) +
				(
				(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE < '$frdt' AND A.ACCD = PARTY1 )-
				(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE < '$frdt' AND A.ACCD = PARTY1 )
				)+
				(
				(SELECT IFNULL(SUM(CASE WHEN CRDR = 'C' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE < '$frdt' AND A.ACCD = PARTY2 )-
				(SELECT IFNULL(SUM(CASE WHEN CRDR = 'D' THEN AMT ELSE 0 END),0) FROM transactions WHERE UID = $uid AND TDATE < '$frdt' AND A.ACCD = PARTY2 )
				),0,0 AS BALANCE
				FROM acmaster AS A 
				WHERE  A.YN = 'Y' AND A.ACCD = $cmb AND A.UID = $uid
				GROUP BY A.OCRDR,A.ACNAME,A.ACOPEN,A.ACCD,A.UID

				ORDER BY ACCOUNT,DATE,SRNO;
	";
	$connect = mysqli_query($con , $sqlqry) ;
	$grandcr = $granddr = 0;
	if ( mysqli_num_rows($connect) > 0 ){
		while ( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC) ){
		    $transid = $rows['SRNO'];
			$date = $rows['DATE'];
			$accd = $rows['ACCD'];
			$ac = $rows['ACCOUNT'];
			$subac = $rows['SUBACCOUNT'];
			$cr = $rows['CREDIT'];
			$narration = $rows['NARRATION'];
			if ( $rows['DEBIT'] < 0 ){
				$cr = -1 * $rows['DEBIT'];
				$dr = 0;
			}else{
				$dr = $rows['DEBIT'];
			}
			$granddr = $granddr  + $dr;
			$grandcr = $grandcr + $cr;
			$x = $dr -$cr;
			$bal = $bal + $x;
			if ($date == ""){
				$date = "1900/01/01";
			}
			if ($transid == "") {
			    $transid = 0;
			}
			//insert only if balace is ot zer, it may be credit(-0) or debit(+0)
			if ( $x <> 0 ){
    			$sqlqryy = "INSERT INTO tmpreport VALUES(
    						$transid,'$date','$ac',$accd,'$subac','$narration',$dr,$cr,$bal,'#f8f9fa'
    						)";
    						mysqli_query($rptcon , $sqlqryy);
			}
		}
		$sqlqryyy = "INSERT INTO tmpreport VALUES(
						0,'1900/01/01','',$accd,'Total','',$granddr,$grandcr,$bal,'#b2b6b9'
						)";
					mysqli_query($rptcon2 , $sqlqryyy);
	}else{
		echo "<div class = 'errormsg'><i class='far fa-times-circle'></i>&nbsp No Record Found!</div>";
	} 
	////////////////   pagination   //////////////////////
	$perpage = 10;
	$total = Getdescription("tmpreport","COUNT(ACCD)");
	$pagebuttons = 4;
	$pages = ceil($total/$perpage);
	$condition = ($pagebuttons > $pages ? $pages : $pagebuttons );
	echo '<div class = "container paginations">';
		echo '<ul class="pagination">';
			echo '<li><a herf = "" id = "first" data = '.$cmb.' ><i class="fas fa-angle-double-left"></i></a></li>';
			echo '<li><a herf = "" id = "prev" data = '.$cmb.' ><i class="fas fa-angle-left"></i></a></li>';
			for($i = 1; $i<= $condition  ; $i++){
				echo '<li class = "pagenum" id = '.$i.'><a herf = ""  class = '.$i.' data = '.$cmb.' >'.$i.'</a></li>';
			}
			echo '<li><a herf = "" id = "next" data = '.$cmb.' ><i class="fas fa-angle-right"></i></a></li>';
			echo '<li><a herf = "" id = "last" data = '.$cmb.' ><i class="fas fa-angle-double-right"></i></a></li>';
		echo '</ul>';
	echo '</div>';
	echo '<div class = "loader" style = "display:none" ><img src="loader1.gif"/></div> ';
	echo '<div class = "Rpt"></div>';
	//////////////////////////////////////////////////////
//}

?>
<script>
$(document).ready(function(){
	//on load make 1st page as active
	if ($(".pagenum").hasClass('active')){
			$(".pagenum").removeClass('active')
			$('#1').addClass('active')
		}else{
			$('#1').addClass('active')
		}
	var varpage,accd,varbuttons,varpages;
	varbuttons = "<?php echo $pagebuttons; ?>"
	varpages = "<?php echo $pages; ?>"
	Cookies.set('pages',varpages)
	$(".pagenum").click(function(){
		if ($(".pagenum").hasClass('active')){
			$(".pagenum").removeClass('active')
			$(this).addClass('active')
		}else{
			$(this).addClass('active')
		}
	})
	//Previous Button
	$("#prev").click(function(e){
		Cookies.set('flag','prev')//set button flag
		var varlastli;
		var varfirstli;
		varpage = 0;
		varfirstli = 0;
		varlastli = 0;
		//get the last li to stop adding active calss when pages are finished
		varfirstli = $(".pagenum").first().attr('id')
		varlastli = $(".pagenum").last().attr('id')
		e.preventDefault();
		varpage = $("#pageval").text();
		varpage = parseInt(varpage) - parseInt(1);
		//Set First And Last Cookies For Moving Pages When User Comes Back From Quick Transaction
		Cookies.set('first',varfirstli)
		Cookies.set('last',varlastli)
		//shift page number
		pagePrevNumber(varpage,varpages,varfirstli,varlastli);
		//shift active calass
		swithActiveClass(varpage,varfirstli,varlastli)
		accd = $(this).attr('data');
		Cookies.set('pageno',varpage); //set page cooke
		pagination(varpage,accd);
	})
	//Next Button
	$("#next").click(function(e){
		Cookies.set('flag','next')//set button flag
		var varlastli;
		var varfirstli;
		varpage = 0;
		varlastli = 0;
		varfirstli = 0;
		//get the last li to stop adding active calss when pages are finished
		varlastli = $(".pagenum").last().attr('id')
		varfirstli = $(".pagenum").first().attr('id')
		e.preventDefault();
		varpage = $("#pageval").text();
		varpage = parseInt(varpage) + parseInt(1);
		//Set First And Last Cookies For Moving Pages When User Comes Back From Quick Transaction
		Cookies.set('first',varfirstli)
		Cookies.set('last',varlastli)
		//shift page number
		pageNextNumber(varpage,varpages,varfirstli,varlastli);
		//shift active calass
		//shift active class to cookie page if its set
		swithActiveClass(varpage,varfirstli,varlastli)
		accd = $(this).attr('data');
		Cookies.set('pageno',varpage); //set page cooke
		pagination(varpage,accd);
	})

	//first button click show first page
	$("#first").click(function(e){
		Cookies.set('flag','first')//set button flag
		var varlastli;
		varlastli = 0;
		varlastli = $(".pagenum").last().attr('id')
		//set only last li Cookie
		Cookies.set('last',varlastli)
		accd = $(this).attr('data');
		FirstButClick(varlastli);
		$('#1').addClass('active').siblings().removeClass("active");
		Cookies.set('pageno',1); //set page cooke
		pagination(1,accd);
	})

	//last button click
	$("#last").click(function(e){
		Cookies.set('flag','last') //set button flag
		var varfirstli;
		varfirstli = $(".pagenum").first().attr('id')
		//set only first li Cookie
		Cookies.set('first',varfirstli)
		accd = $(this).attr('data');
		LastButClick(varfirstli,varpages);
		$('#'+varpages).addClass('active').siblings().removeClass("active");
		Cookies.set('pageno',varpages); //set page cooke with no of pages, as No of pages Digit is last page
		pagination(varpages,accd);
	})

	////Remaining Buttons
	$(".pagenum a").click(function(e){
		Cookies.set('flag','current')//set button flag
		var varlastli;
		var varfirstli;
		varpage = 0;
		varlastli = 0;
		varfirstli = 0;
		e.preventDefault();
		varlastli = $(".pagenum").last().attr('id')
		varfirstli = $(".pagenum").first().attr('id')
		Cookies.set('first',varfirstli)
		Cookies.set('last',varlastli)
		varpage = $(this).attr('class');
		accd = $(this).attr('data');
		Cookies.set('pageno',varpage); //set page cooke
		pagination(varpage,accd);
	})
	//if the page and account cookies set load perticular page elseload first page
	if ( Cookies('pageno') && Cookies('account') ) {
		pagination(Cookies('pageno'),Cookies('account')) //Cookies('account') set in Report.php
		//mover the page numbers based on the flags and calling appropriate move forward backward funtions
		if ( Cookies('flag') == 'prev' || Cookies('flag') == 'current'){
			pagePrevNumber(Cookies('pageno'),Cookies('pages'),Cookies('first'),Cookies('last'));
		}else if ( Cookies('flag') == 'next' ){
			pageNextNumber(Cookies('pageno'),Cookies('pages'),Cookies('first'),Cookies('last'));
		}else if ( Cookies('flag') == 'last' ){
			LastButClick(Cookies('first'),Cookies('pages'));
		}
		$('#'+Cookies('pageno')).addClass('active').siblings().removeClass("active");
	}else{
		pagination(1,partycd); //partycd variable is set in getTable() in Report.php
	}
})

/***************************************** pagination button function ************************************/

function pageNextNumber(pagenum,noofpage,firstbutt,lastbutt){
	lastbutt = parseInt(lastbutt)
	firstbutt = parseInt(firstbutt)
	//if (pagenum > lastbutt){
		if (  pagenum !=0 &&  lastbutt != noofpage ){
			//update li , a id and classes and text of a
			$(".pagination li:nth-child(3) a").text(firstbutt + 1);
			$(".pagination li:nth-child(3) a").attr('class', firstbutt + 1);
			$(".pagination li:nth-child(3)").attr('id', firstbutt + 1);
			$(".pagination li:nth-child(4) a").text(firstbutt + 2);
			$(".pagination li:nth-child(4) a").attr('class', firstbutt + 2);
			$(".pagination li:nth-child(4)").attr('id', firstbutt + 2);
			$(".pagination li:nth-child(5) a").text(firstbutt + 3);
			$(".pagination li:nth-child(5) a").attr('class', firstbutt + 3);
			$(".pagination li:nth-child(5)").attr('id', firstbutt + 3);
			$(".pagination li:nth-child(6) a").text(firstbutt + 4);
			$(".pagination li:nth-child(6) a").attr('class', firstbutt + 4);
			$(".pagination li:nth-child(6)").attr('id', firstbutt + 4);
		}
	//}
}

function pagePrevNumber(pagenum,noofpage,firstbutt,lastbutt){
	lastbutt = parseInt(lastbutt)
	firstbutt = parseInt(firstbutt)
	//if (firstbutt > pagenum ){
		if (  firstbutt > 1){
			//update li , a id and classes and text of a
			$(".pagination li:nth-child(3) a").text(lastbutt - 4);
			$(".pagination li:nth-child(3) a").attr('class', lastbutt - 4);
			$(".pagination li:nth-child(3)").attr('id', lastbutt - 4);
			$(".pagination li:nth-child(4) a").text(lastbutt - 3);
			$(".pagination li:nth-child(4) a").attr('class', lastbutt - 3);
			$(".pagination li:nth-child(4)").attr('id', lastbutt - 3);
			$(".pagination li:nth-child(5) a").text(lastbutt - 2);
			$(".pagination li:nth-child(5) a").attr('class', lastbutt - 2);
			$(".pagination li:nth-child(5)").attr('id', lastbutt - 2);
			$(".pagination li:nth-child(6) a").text(lastbutt - 1);
			$(".pagination li:nth-child(6) a").attr('class', lastbutt - 1);
			$(".pagination li:nth-child(6)").attr('id', lastbutt - 1);
		}
	//}
}

function FirstButClick(lastbut){
	if( lastbut > 4 ){
		$(".pagination li:nth-child(3) a").text(1);
		$(".pagination li:nth-child(3) a").attr('class', 1);
		$(".pagination li:nth-child(3)").attr('id', 1);
		$(".pagination li:nth-child(4) a").text(2);
		$(".pagination li:nth-child(4) a").attr('class', 2);
		$(".pagination li:nth-child(4)").attr('id', 2);
		$(".pagination li:nth-child(5) a").text(3);
		$(".pagination li:nth-child(5) a").attr('class',3);
		$(".pagination li:nth-child(5)").attr('id', 3);
		$(".pagination li:nth-child(6) a").text(4);
		$(".pagination li:nth-child(6) a").attr('class', 4);
		$(".pagination li:nth-child(6)").attr('id', 4);
	}
}


function LastButClick(firstbut,lastpage){
	if( lastpage > 4 ){
		$(".pagination li:nth-child(3) a").text(lastpage - 3);
		$(".pagination li:nth-child(3) a").attr('class', lastpage - 3);
		$(".pagination li:nth-child(3)").attr('id', lastpage - 3);
		$(".pagination li:nth-child(4) a").text(lastpage - 2);
		$(".pagination li:nth-child(4) a").attr('class', lastpage - 2);
		$(".pagination li:nth-child(4)").attr('id', lastpage - 2);
		$(".pagination li:nth-child(5) a").text(lastpage - 1);
		$(".pagination li:nth-child(5) a").attr('class',lastpage - 1);
		$(".pagination li:nth-child(5)").attr('id', lastpage - 1);
		$(".pagination li:nth-child(6) a").text(lastpage);
		$(".pagination li:nth-child(6) a").attr('class', lastpage);
		$(".pagination li:nth-child(6)").attr('id', lastpage);
	}
}

function swithActiveClass(pagenum,firstbut,lastbut){
	//shwitch active calss when previous button clicked
	if ($(".pagenum").hasClass('active')){
		if ( firstbut <= pagenum && lastbut >= pagenum ) {
			$(".pagenum").removeClass('active')
			$('#'+pagenum).addClass('active');
		}else{
			$('#'+pagenum).addClass('active');
		}
	}
}
/********************************************************************************************************/
function pagination(page,cmb){
	$('.loader').show();
	$('.table-responsive').hide()
	$.ajax({
		type : "POST",
		url : "pagination.php",
		dataType : "text",
		data : {
				page : page,
				cmb : cmb
			},
		success : function(response){
			$(".Rpt").html(response);
			$('.loader').hide();
			$('.table-responsive').show()
		},
		error: function(xhr,textStatus,errorThrown){
			console.log(xhr.responseText);
		}
	})
}
</script>