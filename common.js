
//entrymodes
var entrymode ;
var addentry  = "A";
var editentry  = "E";
var deleteentry = "D";
var id;

//clear all the controls
function ClearControl(classname){
	$("."+classname).find(':input').each(function(){
		switch(this.type){
			case 'password':
			case 'text':
			case 'textarea':
			case 'file':
			/*case 'date':*/
			case 'number':
			case 'tel':
			case 'email':
				$(this).val('');
				break;
			case 'select-one':
				$(this).prop("selectedIndex",0);
				break;
			case 'checkbox':
			case 'radio':
				$(this).checked = false;
				break;
		}
		//set image lable to upload.
		$("label[for = image]").text('Upload Image : ');
	});
	BtnControl('cancel','',classname);
}


//Disable Controls
function DisableContrl(classname){
	$("."+classname).find(':input').each(function(){
		switch(this.type){
			case 'password':
			case 'text':
			case 'textarea':
			case 'select-one':
			case 'file':
			case 'date':
			case 'number':
			case 'tel':
			case 'email':
			case 'checkbox':
			case 'radio':
				$(this).attr('disabled',true);
				break;
		}
	});
}

//Enable Controls
function EnableContrl(classname){
	$("."+classname).find(':input').each(function(){
		switch(this.type){
			case 'password':
			case 'text':
			case 'textarea':
			case 'select-one':
			case 'file':
			case 'date':
			case 'number':
			case 'tel':
			case 'email':
			case 'checkbox':
			case 'radio':
				$(this).attr('disabled',false);
				break;
		}
	});
}


//setcolor
function SetColor(idname){
	document.getElementById(idname).style.background = "yellow";
}

//relesecolor
function ReleaseColor(idname){
	document.getElementById(idname).style.background = "white";
}


//Button enable disable
function BtnControl(idname,setfocus ,edclass){
	var value;
	var val;
	switch(idname){
		case 'load':
			$("#add").attr('disabled',false)
			$("#save").attr('disabled',true)
			$("#edit").attr('disabled',false)
			$("#cancel").attr('disabled',false)
			$("#delete").attr('disabled',true)
			$("#add").focus();
			entrymode= "";
			//disable controls
			DisableContrl(edclass);
			break;
		case 'add':
			$("#add").attr('disabled',true)
			$("#save").attr('disabled',false)
			$("#edit").attr('disabled',true)
			$("#cancel").attr('disabled',false)
			$("#delete").attr('disabled',true)
			entrymode = addentry;
			//Enable controls
			EnableContrl(edclass);
			//scroll to top
			$('html, body').animate({scrollTop: $('#page').offset().top -100 }, 'slow');
			document.getElementById(setfocus).focus();
			break;
		case 'edit':
			$("#add").attr('disabled',true)
			$("#save").attr('disabled',false)
			$("#edit").attr('disabled',true)
			$("#cancel").attr('disabled',false)
			$("#delete").attr('disabled',false)
			entrymode = editentry;
			//enable controls
			EnableContrl(edclass);
			//scroll to top
			$('html, body').animate({scrollTop: $('#page').offset().top -100 }, 'slow');
			document.getElementById(setfocus).focus();
			break;
		case 'cancel':
			$("#add").attr('disabled',false)
			$("#save").attr('disabled',true)
			$("#edit").attr('disabled',false)
			$("#cancel").attr('disabled',false)
			$("#delete").attr('disabled',true)
			$("#add").focus();
			entrymode= "";
			//disable controls
			DisableContrl(edclass);
			//once cancel is clicked make search visible so user can click on edit and search again without refreshing
			$("#search").text('');
			$("#search").hide();
			break;
		case 'delete':
			var ask = confirm("Do You Want To Delete This Entry ?");
			if ( ask == true ) {
					$("#add").attr('disabled',false)
					$("#save").attr('disabled',true)
					$("#edit").attr('disabled',false)
					$("#cancel").attr('disabled',false)
					$("#delete").attr('disabled',true)
					$("#add").focus();
					entrymode= deleteentry;
					//get id from test.php
					id = id
					$("#msg").show();
					$("#msg").html('');
					$(".transloader").show();
					$.ajax({
						type : "POST",
						url : "entrymode.php",
						dataType : 'text',
						data : { page: setfocus,
								id : id ,
								entrymode : entrymode
								},
						success : function(response){
							$(".transloader").hide();
							$("#msg").html(response);
							$('#msg').addClass("msganimate")
							$("#msg").fadeOut(3000);
							setTimeout(function() {
								$("#msg").removeClass("msganimate");
							}, 3000);
					entrymode= "";
							ClearControl(edclass);
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

//search on key up
function Search(idname,table,field1,field2){
	var txt = $('#'+idname).val();
	if (entrymode == editentry){
		$("#search").show();
		$("#search").css("top","135px")
			$.ajax({
					type: "POST",
					url: "Search.php",
					dataType: "text",
					data :{
							whereclause : txt,
							table : table,
							field1 : field1,
							field2 : field2
						},
					success : function(response){
						$("#search").html(response);
					},
					error: function(textStatus, errorThrown){
						alert('error');
					}
				})
	}else{
		// dp nothing;
	}
}

//get MaxNumber
function MaxNumber($idname,$table,$field){
	$(".transloader").show();
	$.ajax({
		type: "POST",
		url: "entrymode.php",
		dataType: "text",
		data : { 
				page : 'add',
				table : $table,
				field : $field
				},
		success : function(response){
			$("#"+$idname).val(response);
			$(".transloader").hide();
		},
		error: function(textStatus, errorThrown){
			alert('error');
		}
	})
}



















