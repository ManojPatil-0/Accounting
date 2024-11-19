<?php
//session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
	$startdate = $_SESSION['sdate'];
	$enddate = $_SESSION['edate'];
}else{
	$id = 0;
}
 include_once("common.php") ;
?>
<link rel="stylesheet" type="text/css" href="report.css">
<script src="cookie.js"></script>
<div class = "container Filter">
	<div id = "msg"></div>
	<div class="report">
		<div class="form-group">
			<label for="frdate">From Date :</label> 
			<label for="frdate" style = "float:right">&nbsp Today</label>
			<Label  class = "switch" id = "switch">
				<input type = "checkbox" name = "todaydate" value = "Today" id = "today" >
					<span class="slider round"></span>
			</label> 
			<input type="date" name="frdate" id="frdate" class="form-control" 
				value = <?php  echo $startdate ?> 
				min = <?php  echo $startdate; ?> max = <?php echo $enddate; ?> 
			>
		</div>
		<div class="form-group">
			<label for="todate">To Date :</label>
			<input type="date" name="todate" id="todate" class="form-control" 
			value = <?php echo $enddate ?> 
			min = <?php  echo $startdate; ?> max = <?php echo $enddate; ?> 
			>
		</div>
		<input type="radio" name="opt" id="all" value = 'A' class="form-control" style = "display:none"> 
		<input type="radio" name="opt" id="select" value = 'S' class="form-control" style = "display:none">
		<label for="account">Account Name :</label></br>
		<select name="account" id = "account" class="cmb" >
			<?php  
				FillCombo('acmaster','ACCD','ACNAME','CLOSEAC = "N" AND uid ='.$id.' Order By ACNAME');
			?>
		</select>&nbsp&nbsp&nbsp
		<button type="button" name="show" value="Show" id="show" class="btn btn-info"  style = "height : 40px;width : 25%"> Search </button></br></br>
	</div>
</div>
<div class = "container-fluid">
	<div class="modal fade" id="myModal" role="dialog" ></div> 
	<div class = "Rptshow" id = "Rptshow" >
		<div class = "loader" style = "display:none">
			<img src='loader1.gif'/>
		</div> 
	</div>
</div>
<script type= "text/javascript">
$(document).ready( function(){
	//if cookie set call the table function
	//this has to work when user come back from quick transaction
	if ( Cookies('fromdt') && Cookies('todt') && Cookies('account') ){
		//set menu to report
		$("#mySidenav a:nth-child(3)").addClass("menuActive").siblings().removeClass("menuActive");
		$("#frdate").val(Cookies('fromdt')) 
		$("#todate").val(Cookies('todt')) 
		$("#account").val(Cookies('account'))
		if ( Cookies('today')){
			$("#today").attr('checked',true)
		}else{
			$("#today").attr('checked',false)
		}
		GetTable(Cookies('fromdt'),Cookies('todt'),Cookies('option'),Cookies('account'));
	}
	var fdate,tdate,cmb,opt,partycd
	$("#all").click(function(){
		$("#account").attr("disabled",true)
	})
	$("#select").click(function(){
		$("#account").attr("disabled",false)
	})
	$("#show").click(function(){
		fdate = $("#frdate").val();
		tdate = $("#todate").val();

		if ( $("#all").is(':checked') ){
			opt = "A";
			cmb = 0;
		}else{
			opt = "P";
			cmb = $("#account").val();
		}
		//set all required Cookies
		Cookies.set('fromdt',fdate);
		Cookies.set('todt',tdate);
		Cookies.set('option',opt);
		Cookies.set('account',cmb);
		GetTable(fdate,tdate,opt,cmb);
	})

	//get todays date if TODAY Checkbox is checked
	$("#today").change(function(){
		var todaysdate;
		var checkstatus = $(this).is(':checked')
		$("#msg").html('');
		$("#msg").hide();
		if (checkstatus){
			Cookies.set('today',checkstatus);
			todaysdate = new Date().toISOString().split('T')[0];
			$("#msg").append("<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Today's Date Selected</div>")
		}else{
			todaysdate = "<?php echo $startdate ;?>"
			$("#msg").append("<div class = 'success'><i class='far fa-check-circle'></i>&nbsp Financial Year Start Date Selected</div>")
		}
		$("#msg").show();
		$('#msg').addClass("msganimate")
		$("#msg").fadeOut(2000);
		setTimeout(function() {
			$("#msg").removeClass("msganimate");
		}, 2000);
		//assign From date
		$('#frdate').val(todaysdate);
	})
	$("#today").click(function(){
		Cookies.set('flag','curdate');  //set the flag to curdate when today is clicked
	});
	$("#account").click(function(){
		//reset all cookies when account name is clicked
		Cookies.remove('pageno');
		Cookies.remove('today'); 
		Cookies.remove('page');
		Cookies.remove('first');
		Cookies.remove('last');
	});
});

//Get the value of first coloumn when cliked on row
	$(document).on("click", "#table tr", function(e) {
		var trow;
		trow = 0;
		trow = $(this).find("td:first").text();
		//show modal only on records not on opening and total
		if (trow != 0){
			GetModal(trow);
		}else{
			 $("#myModal").html('');
			 $('#myModal').modal("hide");
		}
	});

function GetTable(fdate,tdate,opt,cmb){
		partycd = cmb
		$(".loader").show();
		$('.table-responsive').hide()
	//ajax call to get the report
	$.ajax({
		type : "POST",
		url : "Reporttable.php",
		dataType : "text",
		data : {
				fromdt : fdate,
				todt : tdate,
				cmb : cmb,
				opt : opt
			},
		success : function(response){
			$("#Rptshow").html(response);
			$(".loader").hide();
			$(".quicktrans").hide();
			$('.table-responsive').show()
		},
		error: function(xhr,textStatus,errorThrown){
			console.log(xhr.responseText);
			$(".loader").hide();
		}
	})
}

//ajax call to popup modal
function GetModal(rowid){
	$("#myModal").html('');
	$.ajax({
		type : "POST",
		url : "modal.php",
		dataType : "text",
		data : {
				rowid :rowid
			},
		success : function(response){
			$("#myModal").html(response);
		},
		error: function(xhr,textStatus,errorThrown){
			console.log(xhr.responseText);
		}
	})
}

</script>