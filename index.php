<?php 
if(isset ($_POST['submit'])){
	$username = $_POST['username'];
	$userpass = base64_encode($_POST['pass']);
	if (isset($_POST['fyr'])){
		$fyr = $_POST['fyr'];
	}else{
		$fyr = "";
	}
    if ( isset($_POST["checkbox"]) ){
    	setcookie("username",$username,time()+60*60*24*30,'/');
    	setcookie("pass",$userpass,time()+60*60*24*30,'/');
    	setcookie("fyr",$fyr,time()+60*60*24*30,'/');
    	setcookie("checkbox","Checked",time()+60*60*24*30,'/');
    }else{
    	setcookie("username",null,time()+60*60*24*30,'/');
    	setcookie("pass",null,time()+60*60*24*30,'/');
    	setcookie("fyr",null,time()+60*60*24*30,'/');
    	setcookie("checkbox",0,time()+60*60*24*30,'/');
    }
}

@session_start();

include_once("common.php") ;
include_once('scripts.html');

?>

<!DOCTYPE html>
<link rel="stylesheet" type="text/css" href="index.css">

<html  lang="en">
<head>
	<title> Accounting </title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<!-- <h3 style= 'text-align:center;color:green'>Under Maintenance, Try Again Later.</h3> -->
	<div class = "container">
		<div class="login">
			<form action = "" method = "POST" enctype = "multipart/form-data" id="loginform">
				<div class="form-group">
					<label for="username"><i class="fas fa-user"></i>&nbsp User Name </label>
					<input type="text" name="username" id="username" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' required autofocus <?php if(isset($_COOKIE["username"])) { echo 'value = '.$_COOKIE["username"]; }; ?> >
				</div>
				<div class="form-group">
					<label for="pass"><i class="fas fa-key"></i>&nbsp Password </label>
					<input type="password" name ="pass" id="pass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id);GetFyr("login")'  onblur = 'ReleaseColor(this.id)' required  <?php if(isset($_COOKIE["pass"])) { echo 'value = '.base64_decode($_COOKIE["pass"]); }; ?> >
				</div>
				<div class="form-group">
					<label for="fyr"><i class="far fa-calendar-alt"></i></i>&nbsp Year </label></br>
					<select name="fyr" id = "fyr" class="cmb" <?php if(isset($_COOKIE["fyr"])){ echo 'onfocus = GetFyr("login")'; } ?>>
						<?php  
							if(isset($_COOKIE["fyr"]))
							{
								echo '<option value = '. $_COOKIE["fyr"] .'>'.$_COOKIE["fyr"].' </option>';
							}
						?>
					</select></br></br>
				</div>
				<div class="form-group">
				    
					<input type = "checkbox" name = "checkbox" id = "checkbox" style = "vertical-align:text-bottom;height:18px;width:18px" <?php if(isset($_COOKIE["checkbox"])){ echo $_COOKIE["checkbox"]; }; ?> >
					<label for="checkbox" style="color:#0d98e8">&nbsp Remember  Me</label></br> 
				</div>
				<button type="submit" name="submit" value="login" id="submit" class="btn btn-info" ><i class= "<?php echo ( isset($_SESSION['uid']) && isset($_SESSION['fyr'])?"fas fa-unlock": "fas fa-lock"); ?>" ></i>&nbsp Login </button> 
			</form>
		</div>
	</div>
	<div class = "transloader" style = "display:none">
		<img src  = "loader3.gif">
	</div>
</body>
<?php
if(isset ($_POST['submit'])){
	$username = $_POST['username'];
	$userpass = base64_encode($_POST['pass']);
	if (isset($_POST['fyr'])){
		$fyr = $_POST['fyr'];
	}else{
		$fyr = "";
	}
	if ( RecSearch("SELECT * FROM usermaster WHERE UNAME = '$username' and UPASS = '$userpass'")  == true){
		//set sessions of user id and finantial year
		if ( $fyr != "" && recSearch("SELECT FYR FROM userstat WHERE UID = (SELECT UID FROM usermaster WHERE UNAME = '$username' ) and FYR  = '$fyr' ") == true) {	
			$_SESSION['uid'] = GetDescription('usermaster','UID',"UNAME = '$username' and UPASS = '$userpass'" );
			$_SESSION['fyr'] = $fyr;
			$_SESSION['user'] = $username;
			$_SESSION['Pass'] = $userpass;
			$userid = $_SESSION['uid'];
			$_SESSION['sdate'] = GetDescription('userstat','SDATE',"UID = $userid AND FYR = '$fyr' " );
			$_SESSION['edate'] = GetDescription('userstat','EDATE',"UID = $userid AND FYR = '$fyr' " );
			$_SESSION['curyr'] = GetDescription('userstat','FYR',"UID = $userid ORDER BY SDATE DESC LIMIT 1");
			$_SESSION['firstlogin'] = 1;
			
			echo '<div class = "container login" id = "msg"><div class = "success"><i class="far fa-check-circle"></i>&nbsp Login Successfull</div></div> ' ;
			//go to next page when login is success
			//used jquery to redirect the page
			echo "<script>  window.location.assign('landing.php') </script>";
		}else{
			echo '<div class = "container login" id = "msg"><div class = "error"><i class="far fa-times-circle"></i>&nbsp Select / Invalid Finantial Year </div></div> ';
		}
	}else {
		echo '<div class = "container login" id = "msg"><div class = "error"><i class="far fa-times-circle"></i>&nbsp Login Failed </div></div> ';
	}
	echo "<script> $('#msg').addClass('msglogin');$('#msg').fadeOut(3000);  </script>"; 
}
?>
<script type= "text/javascript">
var username,page;
function GetFyr(page){
username = $("#username").val();
page = page;
$(".transloader").show();
$.ajax({
	type : "POST",
	url : "entrymode.php",
	dataType : "text",
	data : {
			page : page,
			username : username
			},
	success : function(response){
		$("#fyr").html(response);
		$(".transloader").hide();
	},
	error : function(xhr,textStatus,errorThrown){
		//alert(xhr.responseText);\
		$(".transloader").hide();
		alert("Server is Down, Try Again Later!")
		location.reload(true);
	}
})
}
</script>
</html>