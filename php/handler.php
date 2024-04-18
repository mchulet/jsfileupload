<?php

$actionType = $_REQUEST['actionType'];
$uploaddir = '../uploads/';

if($actionType == "uploadFile") {

    echo 'Upload Filename: ' . $_FILES["profilePicFile"]["name"];
    $uploadedFilename = $_FILES["profilePicFile"]["name"];

    //no photo uploaded
    if( strpos($uploadedFilename, "avatar.png") !== false)
        $outputExt = '';
    else {
        //new filename
        $outputExt = strtolower(pathinfo($uploadedFilename,PATHINFO_EXTENSION));
        $rndVal = rand(1, 10000000);
        $newfilename = 'out_' . $rndVal . '.' . $outputExt;

        move_uploaded_file($_FILES["profilePicFile"]["tmp_name"], $uploaddir . $newfilename);
    }
}
if($actionType == "uploadFileBase64") {

    //new filename
    $rndVal = rand(1, 10000000);
    $newfilename = 'out_' . $rndVal . $outputExt;

    saveBase64ImagePng($_REQUEST['profilePicFile'], $uploaddir . $newfilename);
}

function saveBase64ImagePng($base64Image, $imageNameDir) {
    $base64Image = trim($base64Image);

    if(strstr($base64Image, 'data:image/jpg;base64,') || strstr($base64Image, 'data:image/jpeg;base64,'))
    {
        $exifStatus = true;
        error_log('saveBase64ImagePng: exifStatus - TRUE');
    }
    else
    {
        error_log('saveBase64ImagePng: exifStatus - FALSE');
    }

    $base64Image = str_replace('data:image/png;base64,', '', $base64Image);
    $base64Image = str_replace('data:image/jpg;base64,', '', $base64Image);
    $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
    $base64Image = str_replace('data:image/gif;base64,', '', $base64Image);
    $base64Image = str_replace('data:video/mp4;base64,', '', $base64Image);

    $base64Image = str_replace(' ', '+', $base64Image);

    $imageData = base64_decode($base64Image);

   file_put_contents($imageNameDir, $imageData);

   if($exifStatus)
   {
       $exif = exif_read_data($imageNameDir);

       if($exif && isset($exif['Orientation'])) 
       {
           error_log('saveBase64ImagePng: ReOrientation Start');
           correctImageOrientation($imageNameDir,  $exif['Orientation']);
       }
   }
   else
   {
       error_log('saveBase64ImagePng: No EXIF data found');
   }
}

function correctImageOrientation($filename, $orientation) 
{
      if($orientation != 1)
	  {
        $img = imagecreatefromjpeg($filename);
		
        $deg = 0;
        switch ($orientation) 
		{
          case 3:
            $deg = 180;
            break;
          case 6:
            $deg = 270;
            break;
          case 8:
            $deg = 90;
            break;
        }

		//echo "New Degree: " .  $deg;
		
        if ($deg) 
		{
          $img = imagerotate($img, $deg, 0);        
        }
		
        // then rewrite the rotated image back to the disk as $filename 
        $status = imagejpeg($img, $filename, 95);
		//echo $status;
		
      } // if there is some rotation necessary
}

function getExtension($base64Image) {
    $img = explode(',', $base64Image);
    $ini = substr($img[0], 11);
    $type = explode(';', $ini);
    return $type[0]; // result png
}

?>