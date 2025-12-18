<?php

if (!isset($_SESSION)){
session_start();
}

include_once("Head.php");

//get Sessions$finyr;
if ( isset($_SESSION['user']) ){
	$user = $_SESSION['user'];
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
	$userid = $_SESSION['uid'];
}
if ( isset($_SESSION['fyr']) ){
	$fyr = $_SESSION['fyr'];
}

//Send user to Login page if tried to come in without seting sessions
if ( $user == "" ||  $fyr == "" ){   //if any of one is not set
	session_destroy();
	echo "login Again!";
	echo "<script>  window.location.assign('index.php') </script>";
}


?>
<div class = "container-fluid">
	<div class = "landing" id = "landing">
		<html lang="en">
			<Head>
				<meta charset="utf-8"
				<meta name="viewport" content="width=device-width,initial-scale=1">
				<link rel="stylesheet" type="text/css" href="landing.css">
				<link rel="stylesheet" type="text/css" href="report.css">
			</head>
			<body>
				<Div class="container lastseen" id ="lastseen" >
					<span><i><?php lastSeenDays($userid); $_SESSION['firstlogin'] = 0; ?></i></span>
					
				</div>
				<div class="container land">	
					<h3 id = "dashhead">Dashboard</h3>
					<input type="date" name="frdate" id="frdate" class="form-control" 
						value = <?php echo $startdate ?> 
						min = <?php  echo $startdate; ?> max = <?php echo $enddate; ?> 
					style = "display:none">
					<input type="date" name="todate" id="todate" class="form-control" 
					value = <?php echo $enddate ?> 
					min = <?php  echo $enddate; ?> max = <?php echo $enddate; ?> 
					style = "display:none">
					<div class = "dashloader" style = "display:none">
						<img src='loader1.gif'/>
					</div> 
					<div class = "container-fluid dashboard">
					</div>
					<div id = "note">
						<strong>Note :</strong>
						<ul style="list-style-type: square;">
							<li>For Web Version <a id = "link" href = "#" onclick="CopyLink()">
							<span id = "linkval" style = "display:none;" >http://accountingbymanoj.lovestoblog.com/</span> Copy Link </a> And Open The App In Chrome Browser.</li>
							<li>Last Updated On 12/12/2025.</li>
							<li>Keep Updated By Clearing App Data, Goto Mobile Setting -> Apps ->Accounting -> Clear Data.</li>
							<li>Click On Dashboard Account For Detail Report.</li>
							<li>Upload Image Only When Necessary. Use Minimum Size Images.</li>
							<li>Larger The Image, Slower It Takes To Save. Max Size 3mb.</li>
							<li>Maximum 5 Images Are Allowed Per Transaction.</li>
						</ul>
					</div>
					<div id = "msg"></div></br></br>
				</div>
			</body>
		</html>
	</div>
</div>
<script type = "text/javascript">
$(document).ready(function(){
	loadDashboard();
})
function loadDashboard(){
	$(".dashloader").show();
	$.ajax({
		type : "POST",
		url : "dashboard.php",
		dataType : "text",
		success :function(response){
			$('.dashboard').html(response)
			$(".dashloader").hide();
		},
		error : function(textStatus, errorThrown){
			$('.dashboard').html("Error While Loading Dash Board.")
			$(".dashloader").hide();
				}
	})
}
function loadReport(acname){
	var sdate,edate
	sdate = $("#frdate").val();
	edate = $("#todate").val();
	//set coockies for report
	Cookies.set('fromdt',sdate);
	Cookies.set('todt',edate);
	Cookies.set('option',0);
	Cookies.set('account',acname);
	document.cookie = "reporttype = Report"; //set cookie for dashboard again
	//add or remove active class
	//load report based on cookies
	//$('#mySidenav a:contains("Report")').addClass("menuActive").siblings().removeClass("menuActive");
	GetPage('Report');
}
function CopyLink(){
	$("#msg").show();
	$("#msg").html('');
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val($("#linkval").text()).select();
	document.execCommand("copy");
	$temp.remove();
	$('#msg').html('<div class = "success"><i class="far fa-check-circle"></i>&nbsp Link Copied !</div>');
	$('#msg').addClass("msganimate")
	$("#msg").fadeOut(3000);
}
</script>