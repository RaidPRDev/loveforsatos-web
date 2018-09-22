<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once 'config/dbconfig.php';
include_once $ROOT_PATH . '/api/class.photos.php';

$postData = $_POST;
$postDataLen = count($postData);

// check if there are any fields added
// we will require at least the name
if ($postDataLen == 0 && empty($postData['itemID']))
{
    sendErrorWithCode("The itemID as not found.", 'itemID');
}

if (isset($postData['itemID']))
{

}

// check if id exists
$checkItemID = $dogs->fetchItemById($postData['itemID']);
if (!$checkItemID['success'])
{
    sendWarning('Missing ID. This item does not exist.');
}

// remove data from database
$removeItem = $dogs->removeItemByID($postData['itemID']);
// $removeItem["success"] = true;

if ($removeItem["success"] == false)
{
    // Something happened...
    sendErrorWithCode("Unable to remove item.", $removeItem['error']);
}

// removed data successfully!

// initialize photos
$photos = new PHOTOS($DB_con, "../images/photos/");

// remove all the photos

// get photo list
$photoList = $photos->getPhotosByItemID($postData['itemID']);
if ($photoList["success"] == false)
{
    // Something happened...
    sendErrorWithCode("****Unable to retrieve photos.", $photoList['error']);
}

// iterate through photo list and remove
$photoItemData = null;
$photoListLen = count($photoList['items']);
$photoItems = $photoList['items'];
for ($index = 0; $index < $photoListLen; $index++)
{
    $photoItemData = $photoItems[$index];

    $removeFullImage = $photos->removePhotoImageByName($photoItemData['full_image_url']);
    if (!$removeFullImage['success'])
    {
        sendWarning("Unable to remove image.");
        break;
    }

    $removeThumbImage = $photos->removePhotoImageByName($photoItemData['thumb_image_url']);
    if (!$removeThumbImage['success'])
    {
        // sendWarning("Unable to remove thumb.");
        // break;
    }

    $removePhotoData = $photos->removePhotoByItemID($photoItemData['dog_id']);
    if (!$removePhotoData['success'])
    {
        sendWarning("Unable to remove photos data.");
        break;
    }
}


$data = array(
    "success" => true,
    "error" => '',
    "postData" => $postData,
    "photoList" => $photoList
);

// silently quit and send data
die(json_encode($data));
exit;


unset($photoItemData);
unset($photoList);

// silently quit and send data
// die(json_encode($photoData));

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

function sendWarning($msg)
{
    $data = array(
        "success" => true,
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