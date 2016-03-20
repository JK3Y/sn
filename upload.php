<?php
/**************************************
	ACTIONS
**************************************/
session_start();
$post = isset($_POST) ? $_POST: array();

switch ($post['action']) {
	case 'save':
		saveAvatarTmp();
	break;
	default:
		changeAvatar();
}

/**************************************
	UPDATE IMAGE (BEFORE CROP)
**************************************/
function changeAvatar() {
	$post 		   = isset($_POST) ? $_POST: array();
	$max_width     =  "500";
	$userid 	   = isset($post['hdn-profile-id']) ? intval($post['hdn-profile-id']) : $_SESSION['id'];
	$path 		   = 'src/profiles';
	$valid_formats = array("jpg", "png", "gif", "jpeg");

	$filename 	   = $_FILES['image']['name'];
	$filesize 	   = $_FILES['image']['size'];

	// If the name is not empty
	if (strlen($filename)) {
        // Get the extension of the uploaded image
		$fileinfo = new SplFileInfo($filename);
        $ext      = $fileinfo->getExtension();
        // If the extension matches one of the $valid_formats
		if (in_array($ext, $valid_formats)) {
			// If the image is less than 1MB
			if ($filesize < (1024*1024)) {
				$image_name = 'avatar_' . $userid . '.' . $ext;
				$filepath   = $path . '/' . $image_name;
				$temp_file  = $_FILES['image']['tmp_name'];

				// Store temporary image file on server to ~/src/tmp
				if (move_uploaded_file($temp_file, $filepath)) {
					$width  = getWidth($filepath);
					$height = getHeight($filepath);
					if ($width > $max_width) {
						$scale 	  = $max_width / $width;
						$uploaded = resizeImage($filepath, $width, $height, $scale);
					}
					else {
						$scale    = 1;
						$uploaded = resizeImage($filepath, $width, $height, $scale);
					}
					echo "<img id='photo' file-name='" . $image_name . "' class='preview' src='" . $filepath . '?' . time() . "'>";
				}
				else {
					echo "Upload failed.";
				}
			}
			else {
				echo "Maximum file size is 1MB";
			}
		}
		else {
			echo "Invalid file format. (jpg, jpeg, png, gif are all accepted formats)";
		}
	}
	else {
		echo "No image has been selected!";
	}
	exit;
}

/**************************************
	SAVE IMAGE (AFTER CROP)
**************************************/
function saveAvatarTmp() {
	$post   = isset($_POST) ? $_POST : array();
	$userid = isset($post['id']) ? intval($post['id']) : $_SESSION['id'];

	// Thumbnail width and height
	$t_width  = 50;
	$t_height = 50;

	if (isset($_POST['type']) and $_POST['type'] == "ajax") {
		extract($_POST);

		$imagepath = 'src/profiles/' . $_POST['image_name'];
		$ratio	   = ($t_width / $w1);
		$n_width   = ceil($w1 * $ratio);
		$n_height  = ceil($h1 * $ratio);

        // Check extension to create the thumbnail that matches the original's extension
        $imginfo   = new SplFileInfo($imagepath);
        $ext       = $imginfo->getExtension();
        $n_image   = imagecreatetruecolor($n_width, $n_height);

        switch($ext){
            case 'jpg':
                $imgsource = imagecreatefromjpeg($imagepath);
                imagecopyresampled($n_image, $imgsource, 0, 0, $x1, $y1, $n_width, $n_height, $w1, $h1);
                imagejpeg($n_image, $imagepath, 90);
                break;
            case 'jpeg':
                $imgsource = imagecreatefromjpeg($imagepath);
                imagecopyresampled($n_image, $imgsource, 0, 0, $x1, $y1, $n_width, $n_height, $w1, $h1);
                imagejpeg($n_image, $imagepath, 90);
                break;
            case 'png':
                $imgsource = imagecreatefrompng($imagepath);
                imagecopyresampled($n_image, $imgsource, 0, 0, $x1, $y1, $n_width, $n_height, $w1, $h1);
                imagepng($n_image, $imagepath, 90);
                break;
            case 'gif':
                $imgsource = imagecreatefromgif($imagepath);
                imagecopyresampled($n_image, $imgsource, 0, 0, $x1, $y1, $n_width, $n_height, $w1, $h1);
                imagegif($n_image, $imagepath, 90);
                break;
            default:
                echo '';
                break;
        } 
    }
    savePicture($imagepath);
    echo $imagepath . '?' . time();;
    exit(0);
}

/**************************************
	RESIZE IMAGE
**************************************/
function resizeImage($image, $width, $height, $scale) {
	$newImageWidth  = ceil($width * $scale);
	$newImageHeight = ceil($height * $scale);

    // Check extension to create the thumbnail that matches the original's extension
    $imginfo = new SplFileInfo($image);
    $ext     = $imginfo->getExtension();
    $newImage       = imagecreatetruecolor($newImageWidth, $newImageHeight);
    switch($ext){
        case 'jpg':
            $source = imagecreatefromjpeg($image);
            break;
        case 'jpeg':
            $source = imagecreatefromjpeg($image);
            break;
        case 'png':
            $source = imagecreatefrompng($image);
            break;
        case 'gif':
            $source = imagecreatefromgif($image);
            break;
    }

	imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
	imagejpeg($newImage, $image, 90);
	chmod($image, 0777);
	return $image;
}

/**************************************
    GET IMAGE WIDTH (X coordinate)
**************************************/
function getWidth($image) {
    $sizes = getimagesize($image);
    $width = $sizes[0];
    return $width;
}

/**************************************
	GET IMAGE HEIGHT (Y coordinate)
**************************************/
function getHeight($image) {
	$sizes  = getimagesize($image);
	$height = $sizes[1];
	return $height;
}

/**************************************
    WRITE IMAGE LOCATION TO DATABASE
**************************************/
function savePicture($imgsrc) {
    require_once 'scripts/php/classes.php';
    $userid = $_SESSION['id'];
    $db     = Db::getInstance();
    $sql    = $db->prepare("UPDATE user
                        SET avatar= :avatar
                        WHERE id= :userid");
    $sql->execute(array('userid' => $userid, 'avatar' => $imgsrc));
    $_SESSION['avatar'] = $imgsrc;
}
?>