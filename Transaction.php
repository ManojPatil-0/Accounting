<?php 

//session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
	$fyr = $_SESSION['fyr'];
}else{
	$id = 0;
}

include_once('common.php');

?>
<script type = 'text/javascript' src = 'common.js' ></script> 

<div class = "container">
	<div class = "transaction">
		<div class="form-group">
			<label for="tid">Transaction Number :</label>
			<input type="text" name="tid" id="tid" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' onkeyup = 'Search(this.id,"transactions","TID","TDATE");' >
		</div>
		<div id = "search"></div>
		<div class="form-group">
			<label for="tdate">Record Date :</label>
			<input type="date" name="tdate" id="tdate" class="form-control" 
				value = <?php echo (date('Y-m-d') <= $enddate ?date('Y-m-d'):$enddate) ?> 
					min = <?php  echo $startdate; ?> max = <?php echo $enddate; ?> 
			>
		</div>
		<label for="acname1"><span style = "color:red;text-decoration: underline">Credit </span>Account Name :</label></br>
		<select name="acname1" id = "acname1" class="cmb" >
			<?php  FillCombo('acmaster','ACCD','ACNAME','CLOSEAC = "N" AND uid ='.$id.' Order By ACNAME'); ?>
		</select></br></br>
		<label for="acname2"><span style = "color:green;text-decoration: underline">Debit </span>Account Name :</label></br>
		<select name="acname2" id = "acname2" class="cmb" >
			<?php  FillCombo('acmaster','ACCD','ACNAME','CLOSEAC = "N" AND uid ='.$id.' Order By ACNAME'); ?>
		</select></br></br>
		<label for="catg">Category :</label></br>
		<select name="catg" id = "catg" class="cmb" >
			<?php  FillCombo('category','CATID','CATNAME','uid ='.$id.' Order By CATNAME'); ?>
		</select></br></br>
		<div class="form-group">
			<label for="amount">Amount :</label>
			<input type="number" name ="amount" id="amount" class="form-control" autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)'>
		</div>
		<div class="form-group">
			<label for="image" id = "labelimage" value = "" >Upload Image :</label>
			<ul id = "imglist"></ul>
			<input type = "file" name = "Img_file_arr[]" value = "" id = "images" multiple class = "images" onchange='createImageList(event);'/> 
		</div>
		<div class="form-group">
			<label for="Narration">Narration :</label>
			<textarea id="nar" name="Narration" rows="4" cols="43.5"></textarea></br></br>
		</div>
		<button type="button" name="addentry" value="add" id="add" class="btn btn-info" onclick = 'BtnControl(this.id,"tdate","transaction");MaxNumber("tid","transactions","TID")'>Add</button>
		<button type="button" name="saveentry" value="Save" id="save" class="btn btn-info" >Save</button> 
		<button type="button" name="editentry" value="Edit" id="edit" class="btn btn-info" onclick = 'BtnControl(this.id,"tid","transaction")' >Edit</button>
		<button type="button" name="cancel" value="Cancel" id="cancel" class="btn btn-info" onclick = 'ClearControl("transaction");BtnControl(this.id,"","transaction")'>Cancel</button>
		<button type="button" name="deleteentry" value="Delete" id="delete" class="btn btn-info" onclick = 'BtnControl(this.id,"transaction","transaction")'>delete</button>
		<div id = "msg"></div></br></br>
	</div>
</div>
<script type= "text/javascript">
var tdate,party1,party2,category,amount,nar,id,newfilename;
var filearr = [];
$(document).ready(function(){
	BtnControl('load','','transaction')
	//on save click call button function and take data
	$("#save").click(function (){
		//once save is clicked make search visible so user can click on edit and search again without refreshing
		$("#search").text('');
		$("#search").show();
		id = $("#tid").val();
		tdate = $("#tdate").val();
		party1 = $("#acname1").val();
		party2 = $("#acname2").val();
		category = $("#catg").val();
		amount = $("#amount").val();
		nar = $("#nar").val();
		var $fileUpload = $("input#images[type='file']");
		if ( parseInt($fileUpload.get(0).files.length) > 5 ){
			alert("Maximum 5 Files allowed");
			return false;
		}

		
		//send image details
		//if it is suuccess then insert data
	    if($("#images").val() !== ""){
			/*** Image Details with Adding New Name *******************************/
			var form_data = new FormData();
			vartotale = document.getElementById("images").files.length
			filearr = []; //empty the array
			for( var i = 0 ; i < vartotale;i++ ){
				var property = document.getElementById("images").files[i];
				var fileExten = property.name.split('.').pop();
				var userforimage = <?php echo $id; ?> ;
				var fyrforimage = <?php echo $fyr; ?> ;
				newfilename = userforimage + id + fyrforimage+i+'.'+fileExten;
				filearr.push(newfilename);
				form_data.append("file[]",property,filearr[i]);
				//alert(newfilename);
			}
			/**********************************************************************/
			$("#msg").show();
			$("#msg").html('');
			$(".transloader").show();
			$.ajax({
				type : "POST",
				url : "ImageProcess.php",
				data :  form_data,
				dataType: 'json',
				contentType: false,
				cache: false,
				processData:false,
				success : function(response){
					//handel response useong json response
					if (response['error']){
						$(".transloader").hide();
						$('#msg').html(response['error']);
						$('#msg').addClass("msganimate")
						$("#msg").fadeOut(3000);
						setTimeout(function() {
							$("#msg").removeClass("msganimate");
						}, 3000);
					}else{
						//Call Ajax Save Function if image varifiaction is successfull
						$(".transloader").hide();
						SaveFunction('save','transactions','transaction');
					}
				}
			})
		}else{
			newfilename = '';
			//filearr = [] ;
			getListImgData();
			SaveFunction('save','transactions','transaction');
		}
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
	//geting id from test.php and asinging it to variable('id')
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
							page : "transaction",
							entrymode : mode,
							id : id,
							date : tdate,
							party1 : party1,
							party2 : party2,
							category : category,
							imagename : filearr,
							amount :amount,
							nar :nar
						},
				success : function(response){
					//hide loader
					$(".transloader").hide();
					$('#msg').html(response);
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

function createImageList(event){
	document.getElementById("imglist").innerHTML  = "";
	selectedfile = event.target.files;
	filearr = [...selectedfile]
	updateUiList(filearr);
}

function updateUiList(arr){
	for( let i = 0; i< arr.length; i++ ){	
		//get file name
		const property = arr[i];
		const id = $("#tid").val();
		const fileExten = property.name.split('.');
		const userforimage = <?php echo $id; ?> ;
		const fyrforimage = <?php echo $fyr; ?> ;
		const newfilename = `${userforimage}${id}${fyrforimage}${i}.${fileExten[1]}` //userforimage + id + fyrforimage+i+'.'+fileExten;
		//----------------------
		const newli = document.createElement('li');
		newli.textContent  = newfilename;
		const deletebtn = document.createElement("button");
		deletebtn.textContent  = "X"
		deletebtn.type = "button";
		deletebtn.onclick = function(){
			imageDelete(i);
		}
		newli.appendChild(deletebtn);
		document.getElementById("imglist").appendChild(newli);
	}
}

function imageDelete(position){
	const filteredfiles = filearr.filter( (res,index) =>  index !== position );
	document.getElementById("imglist").innerHTML  = "";
	updateUiList(filteredfiles);
	filearr = filteredfiles
	updateFileInputFiles();
}

function getListImgData(){
	//in edit mode fill image names from list.
	const itemlistelemt = document.getElementById('imglist');
	const listitem = itemlistelemt.querySelectorAll("li");
	filearr = [];
	listitem.forEach( itemname => {
		let imgname =  itemname.textContent;
		imgname = imgname.replace(/X/g,"")
		filearr.push(imgname);
	});
}

function updateFileInputFiles(){
	//this function will update the input type directly.
	const fileInput = document.getElementById('images');
    const dataTransfer = new DataTransfer(); 
    filearr.forEach(file => {
        dataTransfer.items.add(file);
    });
    fileInput.files = dataTransfer.files; 
}

</script>


