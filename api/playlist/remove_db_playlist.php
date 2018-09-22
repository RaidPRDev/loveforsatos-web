<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once '../config/dbconfig.php';
include_once $ROOT_PATH . '/api/playlist/class.playlist.php';

$postData = $_POST;
$postDataLen = count($postData);

// check if there are any fields added
// we will require at least the name
if ($postDataLen == 0 && empty($postData['itemID']))
{
    sendErrorWithCode("The itemID as not found.", 'itemID');
}

$playlist = new PLAYLIST($DB_con, 'playlist');

// check if id exists
$checkItemID = $playlist->getPlaylistByID($postData['itemID']);
if (!$checkItemID['success'])
{
    sendWarning('Missing ID. This playlist does not exist.');
}

// remove playlist from database
$removePlaylist = $playlist->removePlaylistByID($postData['itemID']);
if ($removePlaylist["success"] == false)
{
    // Something happened...
    sendErrorWithCode("Unable to remove playlist.", $removePlaylist['error']);
}

// remove playlist entries
$removeEntries = $playlist->removePlaylistEntriesByID($postData['itemID'], 'playlist_entries');
if ($removeEntries["success"] == false)
{
    // Something happened...
    sendErrorWithCode("Unable to remove playlist entries.", $removeEntries['error']);
}


// removed data successfully!

$data = array(
    "success" => true,
    "error" => '',
    "postData" => $postData
);

// silently quit and send data
die(json_encode($data));

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