<?php include_once('common.php');
	 include_once('scripts.html');
?>


<!Doctype HTML>
<html lang = "en">
<head charset = "utf-8">
	<title> User Master </title>
</head>
<body>

<div class = "container">
	<div class = "userdetail">
		<input type="text" name="uid" id="uid" class="form-control" style= "display:none;">
		<div class="form-group">
			<label for="uname">User Name :</label>
			<input type="text" name="uname" id="uname" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' onkeyup = 'Search(this.id,"usermaster","UID","UNAME");'  >
		</div>
		<div id = "search"></div>
		<div class="form-group">
			<label for="upass">Password :</label>
			<input type="text" name ="upass" id="upass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)'>
		</div>
		<div class="form-group">
			<label for="cpass">Confirm Password :</label>
			<input type="password" name ="cpass" id="cpass" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)'>
		</div>
		<div class="form-group">
			<label for="admin">Admin :</label></br>
			<select name="admin" id = "admin" class="combo" >
				<option value = "N">No</option>
				<option value = "Y">Yes</option>
			</select></br>
		</div>
		<div class="form-group">
			<label for="subadmin">Sub Admin :</label></br>
			<select name="subadmin" id = "subadmin" class="combo" >
				<option value = "N">No</option>
				<option value = "Y">Yes</option>
			</select></br>
		</div>
		<div class="form-group">
			<label for="fdate">Year Start :</label>
			<input type="date" name="fdate" id="fdate" class="form-control" value = <?php echo date('Y-m-d'); ?>>
		</div></br>
		<button type="button" name="addentry" value="add" id="add" class="btn btn-info" onclick = 'BtnControl(this.id,"uname","userdetail");MaxNumber("uid","usermaster","UID")'>Add</button>
		<button type="button" name="saveentry" value="Save" id="save" class="btn btn-info" >Save</button> 
		<button type="button" name="editentry" value="Edit" id="edit" class="btn btn-info" onclick = 'BtnControl(this.id,"uname","userdetail")' >Edit</button>
		<button type="button" name="cancel" value="Cancel" id="cancel" class="btn btn-info" onclick = 'ClearControl("userdetail");BtnControl(this.id,"","userdetail")'>Cancel</button>
		<button type="button" name="deleteentry" value="Delete" id="delete" class="btn btn-info" onclick = 'BtnControl(this.id,"usermast","userdetail")'>delete</button>
		<div id = "msg"></div>
	</div>
</div>
</body>
<script type= "text/javascript">
var username,userpass,confirmpass,fdate,id,admin,subadmin;
$(document).ready(function(){
	BtnControl('load','','userdetail')
	//on save click call button function and take data
	$("#save").click(function (){
		//once save is clicked make search visible so user can click on edit and search again without refreshing
		$("#search").text('');
		$("#search").show();
		id = $("#uid").val();
		username = $("#uname").val();
		userpass = $("#upass").val();
		confirmpass = $("#cpass").val();
		fdate = $("#fdate").val();
		admin = $("#admin").val();
		subadmin = $("#subadmin").val();
		SaveFunction('save','uname','userdetail');
	})

	//hide search when clicked anywhere
	$("body").click(function(){
		$("#search").hide();
	})
})
function SaveFunction(idname,setfocus ,edclass){
	mode = entrymode
	//show loader
	$(".transloader").show();
	switch(idname){
		case 'save':
			$("#add").attr('disabled',false)
			$("#save").attr('disabled',true)
			$("#edit").attr('disabled',false)
			$("#cancel").attr('disabled',false)
			$("#delete").attr('disabled',true)
			$("#add").focus();
			ClearControl(edclass);
			//make ajax call to insert data
			$("#msg").show();
			$("#msg").html('');
			$.ajax({
				type : "POST",
				url : "entrymode.php",
				dataType : "text",
				data : {
							page : "usermast",
							entrymode : mode,
							uid : id,
							uname : username,
							upass : userpass,
							cpass : confirmpass,
							admin : admin,
							subadmin : subadmin,
							fdate :fdate
						},
				success : function(response){
					//hide loader
					$(".transloader").hide();
					$('#msg').html(response)
					$('#msg').addClass("msganimate")
					$("#msg").fadeOut(3000);
					setTimeout(function() {
						$("#msg").removeClass("msganimate");
					}, 3000);
					entrymode= "";
					entrymode= "";
				},
				error : function(textStatus, errorThrown){
					alert('error')
					$(".transloader").hide();
				}
			})
			break
	}
}
</script>
</html>
