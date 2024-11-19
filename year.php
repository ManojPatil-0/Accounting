<?php
include_once('common.php');

//session_start();
if (isset( $_SESSION['uid'] )){
	$id = $_SESSION['uid'];
	$fyr = $_SESSION['fyr'];
	$sdate = $_SESSION['sdate'];
	$edate = $_SESSION['edate'];
	$curyear = $_SESSION['curyr'];
	$startyear = GetDescription('userstat','SDATE',"UID = $id AND FYR = '$curyear' ");
	$endyear = GetDescription('userstat','EDATE',"UID = $id AND FYR = '$curyear' ");
}else{
	$id = 0;
}
?>
<html>
<head>
	
</head> 
<body>
<div class = "container">
	<div class = "fyr">
		<div class = "switchfyr">
			<h3 id = "stext">Switch Financial Year</h3>
			<select name="syear" id = "syear" class="cmb" >
				<?php FillCombo('userstat','FYR','FYR','UID ='.$id.' ORDER BY EDATE DESC '); ?>
			</select></br></br>
			<button type="button" name="switch" value="switch" id="switch" class="btn btn-info" style = "width : 70%">Switch</button>
		</div><br> <hr>
		<div class = "createfyr">
			<h3 id = "ctext">Create New Financial Year</h3>
			<?php
				//first two char of year
				$year1 = substr(intval(date('Y-m-d',strtotime('0 years',strtotime(date('Y/m/d') . '-1 day')))),-2);
				$year2 = substr(intval(date('Y-m-d',strtotime('+1 years',strtotime(date('Y/m/d') . '-1 day')))),-2);

				$year = $year1.$year2;
			?>
			<p id = 'yeardesc'> Create <i id = "yr"><?php echo $year ?></i> Financial Year </p>
			<button type="button" name="create" value="create" id="create" class="btn btn-info" style = "width : 70%">Create</button>
		</div><br> <hr>
		<div class = "extdyear">
			<h3 id = "etext">Extend Financial Year</h3>
				<input type = "date" id = "edate" class="form-control"  style = "width : 70%" 
					value = <?php echo date('Y-m-d',strtotime('0 years',strtotime($endyear . '+1 day'))); ?> 
					min = <?php echo date('Y-m-d',strtotime('0 years',strtotime($endyear . '+1 day'))); ?>
				></br>
			<button type="button" name="extend" value="extend" id="extend" class="btn btn-info" style = "width : 70%">Extend</button>
		</div><br><hr>
	</div>
	<div id = "msg"></div>
</div>
</body>
</html>
<script>
var id,year;
$(document).ready(function(){
	$("#syear").val(<?php echo $fyr;?>);
	$("#switch").click(function(){
		id = $(this).val()
		year = $("#syear").val()
		finatialyear(id,year)
	})
	$("#create").click(function(){
		id = $(this).val()
		year = $("#yr").text()
		var ask = confirm("Create New Financial Year.?");
		if ( ask == true ){
			finatialyear(id,year)
		}
	})
	$("#extend").click(function(){
		id = $(this).val()
		year = $("#edate").val()
		finatialyear(id,year)
	})
})
function finatialyear(id,year){
	$("#msg").show();
	$("#msg").html('');
	$.ajax({
				type : "POST",
				url : "finantialyear.php",
				dataType : "text",
				data : {
							id : id,
							year : year
						},
				success : function(response){
					$('#msg').html(response)
					$('#msg').addClass("msganimate")
					$("#msg").fadeOut(3000);
					setTimeout( function() {
						window.location.href = "landing.php";
					},500)
					setTimeout(function() {
						$("#msg").removeClass("msganimate");
					}, 3000);
				},
				error : function(textStatus, errorThrown){
					alert('error')
				}
			})
}
</script>