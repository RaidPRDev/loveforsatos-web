<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$MAX_SIZE = 25 * 1000000;     // MB ( number * 1 million )

// upload properties
$target_dir = "../images/photos/";
$upload_success = false;
$upload_error = '';
$uploadLogger = array();    // declaring note PHP 5.4+

// list of the file names, both name and tmp_name
$fileNames = array();
$fileNamesLen = 0;
$fileTmpNames = array();
$fileTmpNamesLen = 0;

$targetFile = '';
$imageFileType = '';
$imageSize = 0;
//$uploadLogger[] = "upload error";
//echo json_encode($uploadLogger);
$log = '';

$formFields = array();
$formFields['name'] = $_POST['name'];
$formFields['age'] = $_POST['age'];
$formFields['gender'] = $_POST['gender'];
$formFields['description'] = $_POST['description'];
$formFields['history'] = $_POST['history'];

$validExtensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions

if (!empty($_FILES['files'])) {
	/*
	the code for file upload;
	$upload_success – becomes "true" or "false" if upload was unsuccessful;
	$upload_error – an error message of if upload was unsuccessful;
	*/

	$upload_success = true;

    // check if image folder exists and is writable CHMOD(0777)
    if (is_dir($target_dir) && is_writable($target_dir)) {
        $uploadLogger[] = "$target_dir is writable";
    } else {
        $upload_success = false;
        $upload_error = "Upload directory is not writable, or does not exist.";
        $uploadLogger[] = $upload_error;

        $data = array(
            "success" => $upload_success,
            "error" => $upload_error,
            "uploadLogger" => $uploadLogger
        );

        // silently quit and send data
        die(json_encode($data));
    }

	$fileNames = $_FILES["files"]["name"];
	$fileTmpNames = $_FILES["files"]["tmp_name"];
	$fileSizes = $_FILES["files"]["size"];

    $fileNamesLen = count($fileNames);
    $fileTmpNamesLen = count($fileTmpNames);

    for ($x = 0; $x < $fileNamesLen; $x++) {
        $imageSize = getimagesize($fileTmpNames[$x]);

        // check if image
        if ($imageSize !== false) {
            $upload_success = true;

            $targetFile = $target_dir . basename($fileNames[$x]);
            $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

            // check if file already exists
            if (file_exists($targetFile)) {
                $upload_success = false;
                $upload_error = "Sorry, file already exists.";
                $uploadLogger[] = $upload_error;
            }

            // check file size
            if ($fileSizes[$x] > $MAX_SIZE) {
                $upload_success = false;
                $upload_error = "Sorry, your file is too large.";
                $uploadLogger[] = $upload_error;
            }

            if (!in_array($imageFileType, $validExtensions))
            {

            }

            // allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                $upload_success = false;
                $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadLogger[] = $upload_error;
            }

            // if all goes well start upload
            if ($upload_success)
            {
                // "/tmp/phpSk1Qh7", "uploads/Icon-167.png"
                if (@move_uploaded_file($fileTmpNames[$x], $targetFile)) {
                    // the file has been uploaded.
                    $successMsg = "The file ". $fileNames[$x]. " has been uploaded.";
                    $uploadLogger[] = $successMsg;

                } else {
                    $upload_success = false;
                    $upload_error = $_FILES["files"];
                        //"Sorry, there was an error uploading your file.";
                    $fileError = "there was an error uploading your file";
                    $uploadLogger[] = $fileError;
                    //$uploadLogger[] = "Not uploaded because of error #".$fileNames["error"];
                }
            }
        }
        else
        {
            // not an image, do something...
            $upload_success = false;
            $upload_error = "Sorry, no data found.";
            $uploadLogger[] = $upload_error;
        }
    }

    // output data to app
    $data = array(
        "formFields" => $formFields,
        "success" => $upload_success,
		"error" => $upload_error,
		"fileNames" => $fileNames,
		"fileTmpNames" => $fileTmpNames,
        "fileSizes" => $fileSizes,
        "fileCount" => $fileNamesLen,
		"imageSize" => $imageSize,
        "maxSize" => $MAX_SIZE,
		"targetFile" => $targetFile,
		"uploadLogger" => $uploadLogger
	);

    // silently quit and send data
	die(json_encode($data));
}

?>