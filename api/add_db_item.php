<?php

include_once 'config/dbconfig.php';
include_once $ROOT_PATH . '/api/class.photos.php';

// Add ImageResizer Lib
include 'resizer/ImageResize.php';
include 'resizer/ImageResizeException.php';

use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;

$postData = $_POST;
$postDataLen = count($postData);

// check if there are any fields added
// we will require at least the name
if ($postDataLen == 0 || empty($postData['name']))
{
    sendErrorWithCode("Empty fields, please enter a name.", 'name');
}

// add data to database
$addItem = $dogs->addItem($postData);

if ($addItem["success"] == false)
{
    // Something happened...
    sendErrorWithCode("Unable to add item.", $addItem['error']);
}

// added item data successfully!

// get last insert id
$lastInsertId = $addItem['lastInsertId'];
if ($lastInsertId == null)  sendErrorWithCode("Unable to add item.", $addItem['error']);

$filesEmpty = false;
$totalFiles = 0;
if (!empty($_FILES['files']))
{
    $totalFiles = count($_FILES['files']['name']);
    if ($totalFiles == 1)
    {
        $fileListName = $_FILES['files']['name'][0];
        if (empty($fileListName))
        {
            $filesEmpty = true;
        }
    }
}

// check if we have any photos to upload
if (empty($_FILES['files'])|| $filesEmpty) sendSuccessWithID($lastInsertId);

// initialize photos
$prefixPath = '../';
$photos = new PHOTOS($DB_con, "images/photos/");

// check if image folder exists and is writable CHMOD(0777)
if (!$photos->checkIfUploadFolderExists())
{
    sendError("Upload directory is not writable, or does not exist.");
}

// create photo data based of lastInsertId
$photoData = $photos->uploadPhotos($_FILES, $prefixPath);
$photoData['itemID'] = $lastInsertId;
$photoData['postData'] = $postData;

if (!$photoData["success"])
{
    sendWarning($photoData['error']);
}

$itemInfo['dog_id'] = $lastInsertId;
$itemInfo['description'] = '';
$ext = "." . $photoData['imageFileType'];
foreach($photoData['maskedFiles'] as $key=>$value)
{
    $file = $photoData['maskedFiles'][$key];

    $thumb_image_url = $photos->convertHashToThumb($file, $ext); //  $hash . "_thumb" . $ext;
    $full_image_url = $photos->convertHashToFull($file, $ext); // $hash . "_full" . $ext;
    $original_image_url = substr($file, 0, count($file) - (strlen($ext) + 1)) . $ext;

    $itemInfo['full_image_url'] = $full_image_url;
    $itemInfo['thumb_image_url'] = $thumb_image_url;
    $itemInfo['original_image_url'] = $photoData['maskedFiles'][$key];

    $image = __DIR__ . '/' . $prefixPath . $photos->target_dir . $original_image_url;

    try {
        resizer($image, $prefixPath.$photos->target_dir.$thumb_image_url, 180, 180);
        resizer($image, $prefixPath.$photos->target_dir.$full_image_url, 720, 720);
    } catch (Exception $e) {
        $photoData['success'] = false;
        $photoData['error'] = $e->getMessage();
    }

    // record file names to database
    $addPhoto = $photos->addPhoto($itemInfo);
    if ($addPhoto['success'])
    {
        // success
    }
}

// This will scale the image to as close as it can to the passed dimensions,
// and then crop and center the rest.
function resizer($sourceFile, $destFile, $width, $height)
{
    // echo "<br>" . $sourceFile;
    // echo "<br>" . $destFile;

    $resize = new ImageResize($sourceFile);
    $resize->crop($width, $height);
    $resize->save($destFile);
}


// silently quit and send data
die(json_encode($photoData));

// ==================================================================
// convenience functions
function sendSuccess()
{
    $data = array(
        "success" => true,
        "error" => ''
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendSuccessWithID($itemID)
{
    $data = array(
        "success" => true,
        "error" => '',
        "itemID" => $itemID
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendWarning($msg)
{
    $data = array(
        "success" => false,
        "error" => $msg
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendError($msg)
{
    $data = array(
        "success" => false,
        "error" => $msg
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendErrorWithCode($msg, $error)
{
    $data = array(
        "success" => false,
        "error" => $msg,
        "errorCode" => $error
    );

    // silently quit and send data
    die(json_encode($data));
}

/*$fileNames = $_FILES["files"]["name"];
$fileTmpNames = $_FILES["files"]["tmp_name"];
$fileSizes = $_FILES["files"]["size"];
$data = array(
    "success" => true,
    "error" => '',
    "postData" => $postData,
    "fileNames" => $fileNames,
    "fileTmpNames" => $fileTmpNames,
    "fileSizes" => $fileSizes
);

// silently quit and send data
die(json_encode($data));*/
?>