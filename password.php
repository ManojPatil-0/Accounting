<?php 
	include_once('common.php');
	include_once('scripts.html');

//session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$user = $_SESSION['user'];
}else{
	$id = 0;
}
?>


<!Doctype HTML>
<html lang = "en">
<head charset = "utf-8">
	<title> Change Password </title>
</head>
<body>

<div class = "container">
	<div class = "userdetail">
		<input type="text" name="uid" id="uid" class="form-control" value = <?php echo $id; ?> style= "display:none;" >
		<div class="form-group">
			<label for="uname">User Name :</label>
			<input type="text" name="uname" id="uname" class="form-control" autocomplete="off" value = <?php echo $user;?>  readonly = "true" >
		</div>
		<!-- <div id = "search"></div> -->
		<div class="form-group">
			<label for="opass">Old Password :</label>
			<input type="text" name ="opass" id="opass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' autofocus>
		</div>
		<div class="form-group">
			<label for="npass">New Password :</label>
			<input type="text" name ="npass" id="npass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)'>
		</div>
		<div class="form-group">
			<label for="cpass">Confirm Password :</label>
			<input type="password" name ="cpass" id="cpass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)'>
		</div></br>
		<button type="button" name="addentry" value="add" id="add" class="btn btn-info" >Add</button>
		<button type="button" name="saveentry" value="Save" id="save" class="btn btn-info" >Save</button> 
		<button type="button" name="editentry" value="Edit" id="edit" class="btn btn-info"  >Edit</button>
		<button type="button" name="cancel" value="Cancel" id="cancel" class="btn btn-info" >Cancel</button>
		<button type="button" name="deleteentry" value="Delete" id="delete" class="btn btn-info">delete</button>
		<div id = "msg"></div>
	</div>
</div>
</body>
<script type= "text/javascript">
var username,oldpass,newpass,confirmpass,id;
$(document).ready(function(){
	$("#add, #edit , #cancel, #delete").attr('disabled',true);
	//on save click call button function and take data
	$("#save").click(function (){
		//once save is clicked make search visible so user can click on edit and search again without refreshing
		id = $("#uid").val();
		username = $("#uname").val();
		userpass = $("#opass").val();
		newpass = $("#npass").val();
		confirmpass = $("#cpass").val();
		fdate = $("#fdate").val();
		SaveFunction('save','uname','userdetail');
	})

})
function SaveFunction(idname,setfocus ,edclass){
	//set default edit mode
	mode = 'E';
	//show loader
	$(".transloader").show();
	switch(idname){
		case 'save':
			//make ajax call to insert data
			$("#msg").show();
			$("#msg").html('');
			$.ajax({
				type : "POST",
				url : "entrymode.php",
				dataType : "text",
				data : {
							page : "password",
							entrymode : mode,
							uid : id,
							upass : userpass,
							npass :newpass,
							cpass : confirmpass,
							fdate :fdate
						},
				success : function(response){
					//hide loader
					$(".transloader").hide();
					$('#msg').html(response);
					$('#msg').addClass("msganimate");
					$("#msg").fadeOut(3000);
					setTimeout(function() {
						$("#msg").removeClass("msganimate");
					}, 3000);
					entrymode= "";
				},
				error : function(textStatus, errorThrown){
					alert('error')
				}
			})
			break
	}
}
</script>
</html>
