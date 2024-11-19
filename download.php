<?php 

if ( isset($_GET['imagename']) ) {
	$filename = $_GET['imagename'];
	$filepath = 'images/'.$filename;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($filepath));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize('images/'. $filename));
readfile('images/'. $filename);
?> 
