<!DOCTYPE html>
<?php
session_start();
if (isset( $_SESSION['user'] )){
	$username = $_SESSION['user'];
	$password = $_SESSION['Pass'];
}else{
	$username = "";
}
include_once('scripts.html');
?>
<html lang="en">
<head>
	<title>Menu</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class = "container-fluid">
	<nav class="navbar navbar-inverse">
		<div class="navbar-header">
			 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>                        
			  </button>
		<a class="navbar-brand" href="#"><i class="fab fa-asymmetrik"></i><strong>&nbsp Accounting</strong></a>
		</div>
		<ul class="collapse nav navbar-nav"  id="myNavbar">
			<li class="active"><a data-toggle="collapse" data-target=".in" href="landing.php"><i class="fas fa-home"></i>&nbsp&nbsp <strong>Home</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-chart-bar"></i>&nbsp&nbsp <strong>Report</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-"></i>&nbsp <strong>Transaction</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-address-book"></i>&nbsp&nbsp <strong>Account Master</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-key"></i></i>&nbsp&nbsp <strong>Change Password</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-cogs"></i></i>&nbsp&nbsp <strong>Finantial Year</strong></a></li>
			<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-sign-out-alt"></i></i>&nbsp&nbsp <strong>Logout</strong></a></li>
			<?php
				if ($username == "manoj" && base64_decode($password) == '9888'){?>
					<li><a data-toggle="collapse" data-target=".in" href="#"><i class="fas fa-user"></i>&nbsp&nbsp <strong>User Master</strong></a></li>
					<li><a data-toggle="collapse" data-target=".in" href="export.php"><i class="fas fa-database"></i>&nbsp&nbsp <strong>Backup</strong></a></li>
				<?php } ?>
		</ul>
	</nav>
	<div class = "menuloader" style = "display:none"><img src  = "loader2.gif"></div>
	<div id = "page"></div> 
</div>
</body>
</html>
<script type = "text/javascript">
$(document).ready(function(){
	var head;
	$(".navbar-nav li a strong").click(function(){
		head = $(this).html();
		GetPage(head);
	})
})
function GetPage(head){
	$(".menuloader").show();
	$.ajax({
		type : "POST",
		url : "menuload.php",
		data : { head : head },
		success : function(response){
			$("#page").html(response);
			$(".landing").css("display","none");
			$(".menuloader").hide();
		},
		error : function(xhr,textStatus,errorThrows){
			alert(xhr.responseText);
		}
	})
}
</script>