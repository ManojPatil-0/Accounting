<?php
$response = array();
if (!empty($_FILES['file']['name'])){
	$count = count(($_FILES['file']['name']));
}
for($i=0;$i<$count;$i++){
	if ( !empty($_FILES['file']['name'])) {
		$filename = 'images/'.$_FILES ['file']['name'][$i];
		$tmpfilename =  $_FILES ['file']['tmp_name'][$i];
		$imagesize = $_FILES['file']['size'][$i];
		$filetype = pathinfo($filename,PATHINFO_EXTENSION);
		// echo 'filetype'. $filetype . PHP_EOL;
		// echo 'filename'. $filename . PHP_EOL;

		// Allow certain file formats 
		$allowTypes = array('jpg','png','jpeg'); 
		
		if ( in_array($filetype,$allowTypes )  ) {
			if ( $imagesize >=  51200 && $imagesize <= 3145728 ) {  //3mb and 50kb then process
				$compressedImage = compressImage($tmpfilename, $filename);
				if ( !$compressedImage ) {
					$response['error'] = "<div class = 'error'><i class='far fa-times-circle'></i>Failed To Store Image.</div>";
				}
			}elseif ($imagesize < 51200   ){
				if (move_uploaded_file($tmpfilename, $filename)) {
                    $fileSuccessfullySaved = true;
                } else {
                    $response['error'] = "<div class = 'error'><i class='far fa-times-circle'></i>Failed To Move Small Image.</div>";
                }
			}
			else{
				$response['error'] = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Image Size Is Too Big.</div>";
			}
		}else{
			$response['error'] = "<div class = 'error'><i class='far fa-times-circle'></i>&nbsp Invalid Image Format.</div>";
		}	
	}
}
exit(json_encode($response));
//function to comperss the image ---------------------------------------------
function compressImage($source, $destination) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Create a new image from file 
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/jpg': 
            $image = imagecreatefromjpg($source); 
            break;  
    } 
     
    // Save image 
	imageResize($image,550,$destination,70); //prev -> 500,60
    //imagejpeg($image, $destination, $quality); 
	return $destination;
} 

function imageResize( $file, $max_resolution,$destination,$quality ){
	$original_width = imagesx($file);
	$original_height = imagesy($file);
	
	$ratio = $max_resolution / $original_width;
	$new_width = $max_resolution;
	$new_height = round($original_height * $ratio);
	
	if ($new_height > $max_resolution ){
		$ratio = $max_resolution / $original_height;
		$new_height = $max_resolution;
		$new_width = round($original_width * $ratio);
	}
	
	if ( $file ) {
		$new_image = imagecreatetruecolor($new_width , $new_height);
		imagecopyresampled($new_image,$file,0,0,0,0,$new_width,$new_height,$original_width,$original_height );
		imagejpeg($new_image, $destination, $quality); 
	}
}

//------------------------------------------------------------------------------
?>