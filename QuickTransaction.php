<?php 
include_once('Head.php');
include_once('common.php');

//session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
}else{
	$id = 0;
}

$Transid = 0;
//grab the transaction id from link
if ( isset($_GET['transid']) ){
	$Transid = $_GET['transid'];
}

	global $sqlqry;
	global $connect;
	global $con;
	global $id;
	global $fyr;
	$sqlqry = "";
	$tid = $tdate = $tparty1 = $tparty2 = $tcrdr = $tamt = $tnarr ="";

	$sqlqry = "SELECT * FROM transactions WHERE TID = ".$Transid." AND SRNO = 1 AND UID = ".$id." AND FYR = "."$fyr" ;
	$connect = mysqli_query($con , $sqlqry);
	if ( mysqli_num_rows($connect) > 0 ) {
		while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
			$tid = $rows['TID'];
			$tdate = $rows['TDATE'];
			$tparty1 = $rows['PARTY1'];
			$tparty2 = $rows['PARTY2'];
			$tcrdr = $rows['CRDR'];
			$categoty = $rows['CATID'];//GetDescription('category','CATNAME','CATID ='.$rows['CATID']);
			$tamt = $rows['AMT'];
			$tnarr = $rows['NARR']; 
			$imgarr = $rows['IMGJSON'];
			if ($imgarr != '{}'){
				$timagename = count(explode(",",$imgarr)).' Image(s)';
			}else{
				$timagename = 'No Image';
			}
		}
	}


?>
<script type = 'text/javascript' src = 'common.js' ></script> 
	<div class = "transloader" style = "display:none">
		<img src  = "loader3.gif">
	</div>
<div class  = "container-fluid">
	<div class = "container quicktrans" style = "margin-top : 80px;">
		<div class = "transaction" id = "transaction">
			<div class="form-group">
				<label for="tid">Transaction Number :</label>
				<input type="text" name="tid" id="tid" class="form-control" autocomplete="off"  value = <?php echo $tid; ?>>
			</div>
			<div id = "search"></div>
			<div class="form-group">
				<label for="tdate">Record Date :</label>
				<input type="date" name="tdate" id="tdate" class="form-control" 
					value = <?php echo $tdate; ?> min = <?php echo $startdate; ?>  max = <?php echo $enddate; ?> 
				>
			</div>
			<label for="acname1"><span style = "color:red;text-decoration: underline">Credit </span>Account Name :</label></br>
			<select name="acname1" id = "acname1" class="cmb" >
				<option value = <?php echo $tparty1 ?> ><?php echo GetDescription('acmaster','ACNAME',"UID = $id AND ACCD = $tparty1 ") ?></option>
				<?php  FillCombo('acmaster','ACCD','ACNAME','CLOSEAC = "N" AND uid ='.$id.' AND ACCD NOT IN ('.$tparty1.') Order By ACNAME'); ?>
			</select></br></br>
			<label for="acname2"><span style = "color:green;text-decoration: underline">Debit </span>Account Name :</label></br>
			<select name="acname2" id = "acname2" class="cmb" >
				<option value = <?php echo $tparty2 ?> ><?php echo GetDescription('acmaster','ACNAME',"UID = $id AND ACCD = $tparty2 ") ?></option>
				<?php  FillCombo('acmaster','ACCD','ACNAME','CLOSEAC = "N" AND uid ='.$id.' AND ACCD NOT IN ('.$tparty2.') Order By ACNAME'); ?>
			</select></br></br>
			<label for="catg">Category :</label></br>
			<select name="catg" id = "catg" class="cmb" >
			<option value = <?php echo $categoty ?> ><?php echo GetDescription('category','CATNAME',"UID = $id AND CATID = $categoty ") ?></option>
				<?php  FillCombo('category','CATID','CATNAME','uid ='.$id.' Order By CATNAME'); ?>
			</select></br></br>
			<div class="form-group">
				<label for="amount">Amount :</label>
				<input type="number" name ="amount" id="amount" class="form-control"  autocomplete="off" onfocus = 'SetColor(this.id)'  onblur = 'ReleaseColor(this.id)' value = "<?php  echo $tamt;?>">
			</div>
			<div class="form-group">
				<label for="image" id = "labelimage" value = ""><?php echo 'Upload Image : '.$timagename ;  ?></label> 
				<ul id = "imglist"></ul> 
				<input type = "file" name = "Img_file_arr[]" id = "images" multiple class = "images" onchange='createImageList(event)';/> 
			</div>
			<div class="form-group">
				<label for="Narration">Narration :</label>
				<textarea id="nar" name="Narration" rows="4" cols="43.5" ><?php  echo $tnarr;?></textarea></br></br>
			</div>
			<button type="button" name="addentry" value="add" id="add" class="btn btn-info"  >Add</button>
			<button type="button" name="saveentry" value="Save" id="save" class="btn btn-info" >Save</button> 
			<button type="button" name="editentry" value="Edit" id="edit" class="btn btn-info" >Edit</button>
			<button type="button" name="cancel" value="Cancel" id="cancel" class="btn btn-info" onclick = 'ClearControl("transaction");BtnControl(this.id,"","transaction")'>Cancel</button>
			<button type="button" name="deleteentry" value="Delete" id="delete" class="btn btn-info" >delete</button>
			<div id = "msg"></div></br></br>
		</div>
	</div>
</div>
<script type= "text/javascript">
//direct the page to home if refresh on Quicltransaction
performance.navigation.type == 1 ? window.location.href = 'landing.php' : null;
var tdate,party1,party2,crdr,category,amount,nar,id,newfilename;
var filearr = [];
let imagearr = [];
$(document).ready(function(){
	$("#add").attr('disabled',true)
	$("#edit").attr('disabled',true)
	$("#cancel").attr('disabled',true)
	$("#tid").attr('disabled',true)

	//convert image response to array
	imagearr = <?php echo $imgarr  ?>;
	if( imagearr.length > 0 ) {
		updateUiList(imagearr,"P");
	}
	
	//BtnControl('load','','transaction')
	//on save click call button function and take data
	$("#save").click(function (){
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
			//filearr = [];
			getListImgData();
			SaveFunction('save','transactions','transaction');
		}
	})

	//deleteclick
	$("#delete").click(function (){
		id = $("#tid").val();
		SaveFunction('delete','transactions','transaction');
	})

	//hide search when clicked anywhere
	$("body").click(function(){
		$("#search").hide();
	})

})
function SaveFunction(idname,setfocus ,edclass){
	//default entry is Edit
	mode = "E"
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
							amount :amount,
							imagename : filearr,
							nar :nar
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
					window.setTimeout(function(){
						// Move to a new location or you can do something else
						//window.location.href = "landing.php";
						GetPage('Report');
					}, 3000);

				},
				error : function(textStatus, errorThrown){
					alert('error')
				}
			})
			break;
		case 'delete' :
			var ask = confirm("Do You Want To Delete This Entry ?");
			if ( ask == true ) {
					entrymode= deleteentry;
					id = id;
					$("#msg").show();
					$("#msg").html('');
					$(".transloader").show();
					$.ajax({
						type : "POST",
						url : "entrymode.php",
						dataType : 'text',
						data : { page: "transaction",
								id : id ,
								entrymode : entrymode
								},
						success : function(response){
							$(".transloader").hide();
							$("#msg").html(response);
							$('#msg').addClass("msganimate")
							$("#msg").fadeOut(3000);
							ClearControl(edclass);
							window.setTimeout(function(){
								// Move to a new location or you can do something else
								//window.location.href = "landing.php";
								GetPage('Report');
							}, 5);

						},
						error : function(textStatus,errorThrown){
							$("#msg").val('Problem occured while deleting');
						}
					})
					//disable controls
					DisableContrl(edclass);
					break;
				}
	}
}
function GetPage(head){
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
				$(".quicktrans").css("display","none");;
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

function createImageList(event){
	document.getElementById("imglist").innerHTML  = "";
	selectedfile = event.target.files;
	filearr = [...selectedfile]
	updateUiList(filearr,"N");
}

function updateUiList(arr,mode){
	for( let i = 0; i< arr.length; i++ ){
		if (mode !== "P"){
			//get file name
			const property = arr[i];
			const id = $("#tid").val();
			const fileExten = property.name.split('.');
			const userforimage = <?php echo $id; ?> ;
			const fyrforimage = <?php echo $fyr; ?> ;
			const newfilename = `${userforimage}${id}${fyrforimage}${i}.${fileExten[1]}` //userforimage + id + fyrforimage+i+'.'+fileExten;
			//----------------------
		}else{
			newfilename = arr[i];
		}	
		const newli = document.createElement('li');
		newli.textContent  = newfilename;
		const deletebtn = document.createElement("button");
		deletebtn.textContent  = "X"
		deletebtn.type = "button";
		deletebtn.onclick = function(){
			imageDelete(i,mode);
		}
		newli.appendChild(deletebtn);
		document.getElementById("imglist").appendChild(newli);
	}
}
function imageDelete(position,mode){
	let filteredfiles;
	if(mode === "P"){
		filteredfiles = imagearr.filter( (res,index) =>  index !== position );
		imagearr = filteredfiles;
	}else{
		filteredfiles = filearr.filter( (res,index) =>  index !== position );
		filearr = filteredfiles;
	}
	document.getElementById("imglist").innerHTML  = "";
	updateUiList(filteredfiles,mode);
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


