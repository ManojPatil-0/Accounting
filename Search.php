<?php
	session_start();
	//get Sessions$finyr;
	if ( isset($_SESSION['uid']) ){
		$userid = $_SESSION['uid'];
	}
	if ( isset($_SESSION['fyr']) ){
		$fyr = $_SESSION['fyr'];
	}
	
	include ('common.php');

	 $tablename = $field1 = $field2 = $sqlqry =  $whereclause = '' ;
	 $result1 = $result2 = '' ;
	 global $connect;
	if ( isset($_POST['table']) ){
		$tablename =  $_POST['table'];
		$field1 =$_POST['field1'];
		$field2 =$_POST['field2'];
		$whereclause = $_POST['whereclause'];
	}

	//show the search in list
	echo '<ul class = "search" id = "searchlist" >';
		if (($tablename == 'acmaster') ){
			$sqlqry = "SELECT ".$field1." as id,".$field2." as name FROM ".$tablename . " WHERE (". $field1 ." LIKE '%".$whereclause."%' OR ".$field2." LIKE '%".$whereclause."%')  AND UID = ".$userid." ORDER BY ".$field1 ;
		}elseif ( $tablename == 'transactions' ){
			$sqlqry = "SELECT ".$field1." as id,".$field2." as name FROM ".$tablename . " WHERE ". $field1 ." LIKE '%".$whereclause."%' AND FYR = ".$fyr." AND UID = ".$userid." AND SRNO = 1 ORDER BY ".$field1;
		}elseif (($tablename == 'usermaster') ) {
			$sqlqry = "SELECT ".$field1." as id,".$field2." as name FROM ".$tablename . " WHERE (". $field1 ." LIKE '%".$whereclause."%' OR ".$field2." LIKE '%".$whereclause."%' ) ORDER BY ".$field1;
		}elseif ( $tablename == 'category' ){
			$sqlqry = "SELECT ".$field1." as id,".$field2." as name FROM ".$tablename . " WHERE (". $field1 ." LIKE '%".$whereclause."%' OR ".$field2." LIKE '%".$whereclause."%')  AND UID = ".$userid." ORDER BY ".$field1;
		}
		$connect = mysqli_query($con , $sqlqry);
		if ( mysqli_num_rows($connect) > 0 ) {
			while( $rows = mysqli_fetch_array($connect,MYSQLI_ASSOC )){
				$result1 = $rows['id'];
				$result2 = $rows['name'];
				echo " <li id = ".$result1."><p id = 'field1'>".$result1."</p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<p id = 'field2'>".$result2 ." </li>";
			}
		}else{
			echo ' <li><div class = "errormsg"><i class="far fa-times-circle"></i> No Record Found</div></li> ';
		}
	echo '</ul>';
?>

<script>
var id,table;
$(document).ready(function(){
	$("#searchlist li").click(function(){
		var listclass;
		listclass = "";
		listclass = $("#searchlist li div").attr("class");
		//read only when serch returns some value
		if ( listclass != 'errormsg' ) {
			//show loader
			$(".transloader").show();
			id = (this.id)
			table = "<?php echo $tablename; ?>";
			$.ajax({
				type : "POST",
				url : "FinalResult.php",
				dataType : "json",
				data : {
						table : table,
						id : id
						},
				success: function(response){
					$(".transloader").hide();
					if (table == 'acmaster'){
						$("#accd").val(response[0]);
						$("#acname").val(response[1]);
						$("#acopen").val(response[2]);
						$("#crdr").val(response[3]);
						$("#yn").val(response[4]);
						$("#cyn").val(response[5]);
						$("#dyn").val(response[6]);
					}else if ( table == 'transactions' ){
						$("#tid").val(response[0]);
						$("#tdate").val(response[1]);
						$("#acname1").val(response[2]);
						$("#acname2").val(response[3]);
						$("#crdr").val(response[4]);
						$("#amount").val(response[5]);
						$("#nar").val(response[6]);
						$("#catg").val(response[8]);
						//$("label[for = image]").text('Uploaded Image : '+response[7]+' Image(s).');
						document.getElementById("imglist").innerHTML  = "";
						let imagearr;
						if (response[7] !== "{}"){
							imagearr= response[7].replace(/[\[\]"]/g, '').split(",");
							console.log('aarray',imagearr)
							updateUiList(imagearr);
						}
						function updateUiList(arr){
							for( let i = 0; i< arr.length ; i++ ){
								const newli = document.createElement('li');
								newli.textContent  = arr[i];
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
							const filteredfiles = imagearr.filter( (res,index) =>  index !== position );
							document.getElementById("imglist").innerHTML  = "";
							updateUiList(filteredfiles);
							imagearr = filteredfiles;
						}
						//$("#images").empty().append('<img src="images/'+response[7]);
						$("#search").hide();
					}else if( table == 'usermaster' ){
						$("#uid").val(response[0]);
						$("#uname").val(response[1]);
						$("#upass").val(response[2]);
						$("#cpass").val(response[3]);
						$("#admin").val(response[5]);
						$("#subadmin").val(response[6]);
						$("#fdate").val(response[4]);
						$("#fdate").attr("disabled",true);
					}else if ( table == 'category'){
						$("#catid").val(response[0]);
						$("#catname").val(response[1]);
						$("#actv").val(response[2]);
					}
					//hide loader
					$("#search").hide();
				},
				error : function(xhr,textStatus, errorThrown){
					console.log(xhr.responseText);
				}
			});
		}
	});
});
</script>
