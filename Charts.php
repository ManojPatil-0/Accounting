<?php
include_once('common.php');

//session_start();
$startdate = $_SESSION['sdate'];
$enddate = $_SESSION['edate'];

$monthmin = date("Y",strtotime($startdate)).'-'.date("m",strtotime($startdate));
$monthmax = date("Y",strtotime($enddate)).'-'.date("m",strtotime($enddate));

?>
<link rel="stylesheet" type="text/css" href="chart.css">
<div class="container-fluid">
	<div class="container">
		<div class="controls" >
			<div id="filter">
				<label for="month" class="radio-inline">
					<input type="radio" id="month" name="select" value="Month" checked onclick = 'getSelectionData()'; >&nbspMonth
				</label>
				<label for="year" class="radio-inline">
					<input type="radio" id="year" name="select" value="Year" onclick = 'getSelectionData()'; >&nbspYear
				</label>
				<label for="compare" class="radio-inline">
					<input type="checkbox" id="compare" name="select" value="compare"
					style = "vertical-align:text-bottom;height:18px;width:18px"
					title = "Use this option to comapare in Bar Chart, doughnout will remain Same."
					onclick = 'getSelectionData()'; >&nbsp Compare
				</label><br>
			</div>
			<div id = "chartfilter">
				<div id ="selcalendar">
					<input type="month" id="chartmonth" name="chartmonth" class="form-control" 
						value= <?php echo date("Y")."-".date("m"); ?>
						min = <?php  echo $monthmin; ?> max = <?php echo $monthmax; ?>
						style = "width:50%;float:left"
					/>
				</div>
				<div id ="calbutton">
					<button type="button" name="show" value="Show" id="show" class="btn btn-info"  style = "width : 25%;float:right;height:35px" onclick="getData()"> Filter </button>
                </div>
			</div>
		</div>
	</div>
	<div id="chartContainer" style="height:100%">
		<div id="chartContainer-1" style="height: 500px; width: 100%;"></div>
		<h5 id = "amount"></h5>
		<div id="chartContainer-2" style="height: auto; width: 100%;"></div>
	</div>
	<!-- This div is score space to show data on 000webhost log -->
	<div id="space" style = "margin-top:200px"></div>
</div>
<script>
//jquery to load ---
$(document).ready(function(){
	let choice;
	let curmonth = (document.getElementById("chartmonth").value);
	const filterdate = new Date(month)
	year = filterdate.getFullYear();
	makeAjaxCall(curmonth,year,curmonth,year,'M',false);
});

//get selection data------
function getSelectionData(selval){
	const iscompare = document.getElementById('compare').checked  ? true : false;
	selval = document.getElementById('month').checked ? "Month" : "Year"; 
	if(!document.getElementById('compare').checked){
		$('#compclass').toggleClass('rotated');
	}
	selDataAjaxCall( selval, iscompare );
}

//select data ajax call
function selDataAjaxCall( selval, iscompare ){
	$.ajax({
		type : "POST",
		url : "entrymode.php",
		dataType : "text",
		data : {
				page : 'chartfilter',
				valueselected : selval,
				iscompare : iscompare
				},
		success : function(response){
			$("#selcalendar").html(response);
		},
		error : function(xhr,textStatus,errorThrown){
			//alert(xhr.responseText);\
			alert("Server is Down, Try Again Later!")
			location.reload(true);
		}
	})	
}

//-------------------
function getData(){
	let choice,year,month,compyear,compmonth,iscompare;
	iscompare = document.getElementById("compare").checked;
	const ismonthsel = (document.getElementById("month"))
	if (ismonthsel.checked){
		month = (document.getElementById("chartmonth").value);
		year = "";
		if ( iscompare ){
			compmonth = (document.getElementById("chartcompmonth").value);
			compyear = "";
		}
	}else{
		year = $("#fyr").val();
		month = new Date().getMonth();
		if ( iscompare ){
			compyear = (document.getElementById("compfyr").value);
		}
	}
	choice = ( document.getElementById('month').checked ) ?"M" : "Y";
	makeAjaxCall(month,year,compmonth,compyear,choice,iscompare);		
}
//call to chartdata.php
function makeAjaxCall(selmonth,selyear,compmonth,compyear,choice,iscompare){
	const xhm = new XMLHttpRequest();
	xhm.onreadystatechange = () => {
		if (xhm.readyState === 4 && xhm.status === 200 ){
			const response = JSON.parse(xhm.responseText);
			//console.log(response);
			document.getElementById("amount").innerHTML = "Total Amount : " + response.amount[0];
			const newarrange = rearrangeResponse(response);
			//array destructure//
			doughnutChart(response.doughnout);
			multiBarChart(newarrange.resp_1,newarrange.resp_2,response.amount[0],response.amount[1],response.year[0],response.year[1]);
		}
	};
	xhm.open("POST","chartdata.php",false);
	xhm.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhm.send("month="+selmonth+"&year="+selyear+"&compmonth="+compmonth+"&compyear="+compyear+"&choice="+choice+"&iscompare="+iscompare);
}

var doughnutChart = (c_data) => {
	//console.log(c_data);
	var chart = new CanvasJS.Chart("chartContainer-1", {
	animationEnabled: true,
	title:{
		text: "",
		horizontalAlign: "left"
	},
	data: [{
		type: "doughnut",
		startAngle: 60,
		//innerRadius: 60,
		indexLabelFontSize: 14,
		indexLabel: "{label} - #percent%",
		toolTipContent: "<b>{label}:</b> {y} (#percent%)",
		dataPoints: c_data
	}]
});
chart.render();
}

var multiBarChart = (data1,data2,amount1,amount2,curyear,Prevyear) =>{
let curmonth, prevmonth
if ( document.getElementById('month').checked ){
	const monthnames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
	const month = (document.getElementById("chartmonth").value);
	const filterdate = new Date(month)
	let monthnumber = filterdate.getMonth();
	curmonth =  monthnames[monthnumber];
	if ( document.getElementById('compare').checked){
		const compmonth = (document.getElementById("chartcompmonth").value);
		const filtercompdate = new Date(compmonth)
		const compmonthnumber = filtercompdate.getMonth();
		prevmonth =  monthnames[compmonthnumber];
	}else{
		prevmonth = monthnames[(monthnumber)==0? 11:monthnumber-1];
	}
}	
const filter_sel = ( document.getElementById('month').checked ) ? choice = "M" : choice = "Y";
var chart = new CanvasJS.Chart("chartContainer-2", {
	animationEnabled: true,
	/*title:{
		text: "Monthly comparison"
	},	*/
	axisY: {
		title: "",
		titleFontColor: "#4F81BC",
		lineColor: "#4F81BC",
		labelFontColor: "#4F81BC",
		tickColor: "#4F81BC"
	},
	/*axisY2: {
		title: "Millions of Barrels/day",
		titleFontColor: "#C0504E",
		lineColor: "#C0504E",
		labelFontColor: "#C0504E",
		tickColor: "#C0504E"
	},*/	
	toolTip: {
		shared: true
	},
	legend: {
		cursor:"pointer",
		itemclick: toggleDataSeries
	},
	data: [{
		type: "column",
		name:  (filter_sel == "M" ? ''+curmonth+'('+ amount1 +')' : 'Fin - '+curyear+'('+ amount1 +')' ) ,
		legendText: "",
		showInLegend: true, 
		dataPoints: data1
	},
	{
		type: "column",	
		name: (filter_sel == "M" ? ''+prevmonth+'('+ amount2 +')' : 'Fin - '+Prevyear+'('+ amount2 +')' ) ,
		legendText: "",
		//axisYType: "secondary",
		showInLegend: true,
		dataPoints: data2
	}]
});
chart.render();
function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	}
	else {
		e.dataSeries.visible = true;
	}
	chart.render();
}
}

function rearrangeResponse(data){
bar_data_1 = data.bar_1;
bar_data_2 = data.bar_2;

//get matching labels with amount 0------------------------
/*let count = 0;
for( let i = 0; i< bar_data_1.length; i++  ){
	for( let j = 0; j< bar_data_2.length; j++){
		if ((bar_data_1[i].label == bar_data_2[j].label) && (bar_data_1[i].y == 0) && (bar_data_2[j].y == 0)) {
			 newarr[count] = ({"label": bar_data_1[i].label,"y": bar_data_2[j].y}) ;
			 count++;
		}
	}
}*/
//----------------------------------------------------------


//get matching labels with amount 0
const newarr = bar_data_1.filter( (arr1) => bar_data_2.some( (arr2) => ((arr1.label == arr2.label) && arr1.y ==0 && arr2.y ==0  )) )
//console.log(newarr);

// first data object using
const resp_1 = bar_data_1.filter((out1) => !newarr.some((out2)=> out1.label === out2.label ) );

// second data object
const resp_2 = bar_data_2.filter((out1) => !newarr.some((out2)=> out1.label === out2.label ) );

//

return {resp_1,resp_2};
}

</script>