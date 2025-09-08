<?php
if (!isset($_SESSION)){
	session_start();
}
if (isset( $_SESSION['uid'] )){
	$userid = $_SESSION['uid'];
}else{
	$userid = 0;
}
//get Sessions$finyr;
if ( isset($_SESSION['user']) ){
	$user = $_SESSION['user'];
}
if ( isset($_SESSION['fyr']) ){
	$fyr = $_SESSION['fyr'];
}
include_once('scripts.html');
include_once('common.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Menu</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class = "container-fluid">
	<nav class="navbar navbar-inverse">
		<div class="navbar-header" >
			<!--<a class="navbar-brand" href="#" style = "float:right;font-size: 25px;padding: 10px"><i class="fa"><img src = "head.png"></i><strong>&nbsp Accounting</strong></a> -->
			<a class="navbar-brand" href="#" style = "float:right;"><i class="fas fa-user-circle"></i><strong>&nbsp <?php echo strtoupper($user).' - '.$fyr; ?></strong></a>
			<!--​<span id = "button" onclick="openNav()">&#9776;</span>-->
			<button type="button" class="navbar-toggle menubutton" onclick="openNav()">
				<span class="icon-bar" id = "one"></span>
				<span class="icon-bar" id = "two"></span>
				<span class="icon-bar" id = "three"></span>                        
		  </button>
		</div>
	</nav>
	<div id="mySidenav" class="sidenav">
		<!-- <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a> -->
			<a href="landing.php" id = "menu">Menu</a>
			<a href="landing.php" class = "menuActive"><i class="fas fa-home"></i>&nbsp&nbsp <strong>Home</strong><hr></a>
			<a href="#"><i class="fa-solid fa-chart-line fa-bounce"></i>&nbsp&nbsp <strong>Charts</strong><hr></a>
			<a href="#"><i class="fa-solid fa-file-invoice fa-beat"></i>&nbsp&nbsp <strong>Report</strong><hr></a>
			<a href="#"><i class="fa-solid fa-file-invoice"></i>&nbsp&nbsp <strong>Category Rpt</strong><hr></a>
			<a href="#"><i class="fa-solid fa-file-invoice"></i>&nbsp&nbsp <strong>Narration Rpt</strong><hr></a>
			<a href="#"><i class="fas fa-handshake"></i>&nbsp <strong>Transaction</strong><hr></a>
			<a href="#"><i class="fas fa-user-circle"></i>&nbsp&nbsp <strong>Account Master</strong><hr></a>
			<a href="#"><i class="fa fa-list-alt"></i>&nbsp&nbsp <strong>Category Master</strong><hr></a>
			<a href="#"><i class="fas fa-key"></i>&nbsp&nbsp <strong>Change Password</strong><hr></a>
			<a href="#"><i class="fas fa-calendar-alt"></i>&nbsp&nbsp <strong>Financial Year</strong><hr></a>
			<?php
				if ( GetDescription('usermaster','ADMIN',"UID = $userid ") =='Y' ){?>
					<a href="#"><i class="fas fa-user"></i>&nbsp&nbsp <strong>User Master</strong><hr></a>
					<?php } 
				if ( (GetDescription('usermaster','ADMIN',"UID = $userid ")  =='Y') || (GetDescription('usermaster','SUBADMIN',"UID = '$userid' ")  =='Y') ){ ?>
					<a href="export.php"><i class="fas fa-database"></i>&nbsp&nbsp <strong>Backup</strong><hr></a>
					<?php } ?>
					<a href="#"><i class="fas fa-sign-out-alt"></i>&nbsp&nbsp <strong>Logout</strong><hr></a>
	</div>
	<div class = "menuloader" style = "display:none">
		<!--<img src  = "loader2.gif">
		<h4 id = "loading">Loading...</h4> -->
		<div class = "cssloader">
			<span>Loading</span>
		</div>
	</div>
	<div class = "transloader" style = "display:none">
		<img src  = "loader3.gif">
	</div>
	<!-- load pages here -->
	<div id = "page"></div> 
</div>
</body>
</html>
<script type = "text/javascript">
var curpage;
$(document).ready(function(){
	var head,navwidth,reporttype;
	//Get The Current Page
	curpage = document.location.href.match(/[^\/]+$/)[0];
	curpage = curpage.charAt(0);
	$("#mySidenav a").click(function(){
		head = $(this).find("strong").text();
		document.cookie = "reporttype = "+ head ;
		closeNav();
		if ( head != 'Home' ) {
			GetPage(head);
		}
		//add or remove active class
		$(this).addClass("menuActive").siblings().removeClass("menuActive");
	})
	//close side nav when clicked outside
	$("body").click(function(){
		navwidth = $("#mySidenav").width();
		if ( navwidth == 280){
			closeNav();
		}
	})
})
function GetPage(head){
	//save successfull login datetime
	<?php lastSeen($userid,$_SESSION['firstlogin']); ?>
	
	//$(".menuloader").show();
	$('.menuloader').fadeIn(200);
	$("#page").hide();
	$.ajax({
		type : "POST",
		url : "menuload.php",
		data : { head : head },
		success : function(response){
			//delay the response
			setTimeout(function(){
				$("#page").html(response);
				$("#page").show("slide", {direction: "left"},300);
				$(".landing").css("display","none");
				$(".menuloader").hide();
				//hide the quick transaction entry from coming as response
				$(".quicktrans").hide();
				//closeNav();
			},200); 
		},
		error : function(xhr,textStatus,errorThrows){
			//alert(xhr.responseText);
			alert("Server is Down, Try Again Later!")
			$(".menuloader").hide();
			location.reload(true);
		}
	})
}
/****************** open and close side nav ************************/
function openNav() {
	//close other loaders
	$('.loader').hide();
	navwidth = $("#mySidenav").width();
	//unset all Cookies
	if (curpage != 'Q'){
		Cookies.remove('fromdt');
		Cookies.remove('todt');
		Cookies.remove('option');
		Cookies.remove('account'); 
		Cookies.remove('pageno');
		Cookies.remove('today'); 
		Cookies.remove('page');
		Cookies.remove('first');
		Cookies.remove('last');
	}
	//do the animation if nav bar is closed if it is open then call closeing function
	if ( navwidth != 280){
		$("#one").addClass("topbar")
		$("#two").addClass("midbar")
		$("#three").addClass("botbar")
		document.getElementById("mySidenav").style.width = "280px";
		document.getElementById("page").style.marginLeft = "280px";
		//dont animate landing when on Quick Transaction as there is no Landing Page Available
		if (curpage != 'Q'){
			document.getElementById("landing").style.marginLeft = "280px";
			document.getElementById("landing").style.width = "100%";
		}else{
			document.getElementById("transaction").style.marginLeft = "280px";
			document.getElementById("transaction").style.width = "100%";
		}
		document.getElementById("page").style.width = "100%";
	}else{
		closeNav()
	}
}
function closeNav() {
	$("#one").removeClass("topbar")
	$("#two").removeClass("midbar")
	$("#three").removeClass("botbar")
	document.getElementById("mySidenav").style.width = "0";
	document.getElementById("page").style.marginLeft = "0px";
	if (curpage != 'Q'){
		document.getElementById("landing").style.marginLeft = "0px";
	}else{
		document.getElementById("transaction").style.marginLeft = "0px";
	}
}
/*******************************************************************/
</script>