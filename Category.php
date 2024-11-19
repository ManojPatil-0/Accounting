
<?php include_once('common.php')?>
<script type = 'text/javascript' src = 'common.js' ></script> 

<div class = "container">
	<div class = "Category Name">
		<input type="text" name="catid" id="catid" class="form-control"  style= "display:none;">
		<div class="form-group">
			<label for="catname">Category Name :</label>
			<input type="text" name="catname" id="catname" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' onkeyup = 'Search(this.id,"category","CATID","CATNAME")'  >
		</div>
		<div class = "search" id = "search"></div>
        <label for="catname">Is Expense (Y/N) :</label></br>
		<select name="actv" id = "actv" class="combo" >
			<option value = "Y">Yes</option>
			<option value = "N">No</option>
		</select></br></br></br>
		<button type="button" name="addentry" value="add" id="add" class="btn btn-info" onclick = 'BtnControl(this.id,"catname","category");MaxNumber("catid","category","CATID")'>Add</button>
		<button type="button" name="saveentry" value="Save" id="save" class="btn btn-info" >Save</button> 
		<button type="button" name="editentry" value="Edit" id="edit" class="btn btn-info" onclick = 'BtnControl(this.id,"catname","category")' >Edit</button>
		<button type="button" name="cancel" value="Cancel" id="cancel" class="btn btn-info" onclick = 'ClearControl("category");BtnControl(this.id,"","category")'>Cancel</button>
		<button type="button" name="deleteentry" value="Delete" id="delete" class="btn btn-info" onclick = 'BtnControl(this.id,"category","category")'>delete</button>
		<div id = "msg"></div>
	</div>
</div>

<script type= "text/javascript">
var id,catname,catactive;
$(document).ready(function(){
	BtnControl('load','','category')
	
	//on save click call button function and take data
	$("#save").click(function (){
		//once save is clicked make search visible so user can click on edit and search again without refreshing
		//$("#search").text('');
		//$("#search").show();
		id = $("#catid").val();
		catname = $("#catname").val();
		catactive = $("#actv").val();
		SaveFunction('save','catname','category');
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
			$("#msg").show();
			$("#msg").html('');
			//make ajax call to insert data
			$.ajax({
				type : "POST",
				url : "entrymode.php",
				dataType : "text",
				data : {
							page : "category",
							entrymode : mode,
							catid : id,
							catname : catname,
							catactive : catactive
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

