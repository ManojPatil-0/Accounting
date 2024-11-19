<?php
include_once('common.php');

require("phpmailer/src/PHPMailer.php");
require("phpmailer/src/SMTP.php");

EXPORT_TABLES($host,$username,$password,$db);

function EXPORT_TABLES($host,$username,$password,$db,$tables=false, $backup_name=false ){
	
	$mysqli = new mysqli($host,$username,$password,$db);
	$mysqli->select_db($db);
	$mysqli->query("SET NAMES 'utf8'");

	$queryTables = $mysqli->query('SHOW TABLES');
	while($row = $queryTables->fetch_row()){
		$target_tables[] = $row[0];
	}
	if($tables !== false){
		$target_tables = array_intersect( $target_tables, $tables);
	}
	foreach($target_tables as $table){
		$result = $mysqli->query('SELECT * FROM '.$table);
		$fields_amount=$result->field_count;
		$rows_num=$mysqli->affected_rows;
		$res = $mysqli->query('SHOW CREATE TABLE '.$table);
		$TableMLine=$res->fetch_row();
		$content = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";
		for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0){
			while($row = $result->fetch_row()){//when started (and every after 100 command cycle):
				if ($st_counter%100 == 0 || $st_counter == 0 ){
						$content .= "\nINSERT INTO ".$table." VALUES";
					}
				$content .= "\n(";
				for($j=0; $j<$fields_amount; $j++){
					$row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
					if (isset($row[$j])){
						$content .= '"'.$row[$j].'"' ;
					}else {
						$content .= '""';
					}
					if ($j<($fields_amount-1))
					{
						$content.= ',';
					}
				}
				$content .=")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num){
					$content .= ";";
				}else{
					$content .= ",";
				}
				$st_counter=$st_counter+1;
			}
		} $content .="\n\n\n";
	}
	$backup_name = $backup_name ? $backup_name : $db."___(".date('d-m-Y').").sql";
	/*header('Content-Type: application/stream');
	header("Content-Transfer-Encoding: Binary");
	header("Content-disposition: attachment; filename=\"".$backup_name."\"");
	echo $content;*/
	$handle = fopen($backup_name,'w+');
	fwrite($handle,$content);
	fclose($handle);
	
	/***********************************zip images*********************************************************************/
	$tozip = "images";
	$zipfile = 'images/images.zip';
	
	$zip = New ZipArchive;
	
	$this_zip = $zip -> open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	
	if ($this_zip) {
		
		$dir = opendir($tozip);
		
		while ($file = readdir($dir)){
			if ($file != "." && $file != ".."){
				$zip -> addFile('images/'.$file,$file);
			}
		}
		closedir($dir);
		$zip -> addFile($backup_name);
		$zip ->close();
	}
	
	downlodimageizp("images/images.zip");	
	//downloadbackup($backup_name);
	
	/*********************************** Backup as mail attachment ****************************************************/
	
	/*$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP(); // enable SMTP

	//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true; // authentication enabled
	$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
	$mail->Host = 'tls://smtp.gmail.com';
	$mail->Port = 587 ;
	$mail->IsHTML(true);
	$mail->Username = "itzmanojpatil@gmail.com";
	$mail->Password = "hhzknxjcelijidzd";
	$mail->SetFrom("appbackupac@gmail.com");
	$mail->Subject = 'Accounting Backup of Date '.date('d-m-Y');
	$mail->Body = "Accounting Backup";
	$mail->AddAddress("appbackupac@gmail.com");
	$mail->AddAttachment($backup_name );// add attachments
	$mail->AddAttachment("images/images.zip");// add attachments

	 if(!$mail->Send()) {
		//Mailer Error: ' .$mail->ErrorInfo;.
		echo "<!DOCTYPE HTML>";
		echo '<h2 style = "BACKGROUND:RED;COLOR:WHITE;FONT-SIZE:50PX;PADDING:50PX;">Backup Failed.!</h2>';
	 } else {
		echo "<!DOCTYPE HTML>";  
		echo '<h2 style = "BACKGROUND:GREEN;COLOR:WHITE;FONT-SIZE:50PX;PADDING:50PX;">Backup Successful.!</h2>';
		echo '<meta http-equiv="refresh" content="0.3; url=/landing.php">';
	 }*/


	/*****************************************************************************************************************/
	unlink($backup_name); // Delete the file once mail is attached and sent
	unlink($zipfile); // Delete Image Zip file

	/*********************************** simple mail Backup ****************************************************/
	/*$to = "itsmanojmpatil@gmail.com";
	$from = "Accounting";
	$subject = "Accounting Backup Of Date " .$backup_name;
	$header = "From: My Accoutning Website";
	$msg = $content;
	$mail = mail($to,$subject,$msg,$header);*/
	/****************************************************************************************************/
}
function downlodimageizp($imagezip){
	if ( file_exists($imagezip)){
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$imagezip");
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: binary");

		// read the file from disk
		readfile($imagezip);
		//exit();
	}
}

function downloadbackup ($filename) {
	if ( file_exists($filename)){
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: binary");

		// read the file from disk
		readfile($filename);
		//exit();
	}
}
?>