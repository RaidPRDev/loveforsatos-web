<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/18/2018
 * Time: 8:27 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once 'config/dbconfig.php';

// $selectedItems = dog_ids

$postSelectedItems = ( isset($_POST['selectedItems']) ) ? $_POST['selectedItems'] : "184,185,186,193,194,195";
$selectedItems = explode(",", $postSelectedItems);
$allDogsList = $dogs->fetchAllItemsWithPhotos($selectedItems);

if ($allDogsList['success'])
{
    $data = array(
        "success" => true,
        "items" => $allDogsList['items'],
        "selectedItems" => $selectedItems,
        "error" => "",
        "errorCode" => 0
    );

    // silently quit and send data
    die(json_encode($data));

}
else
{
    sendErrorWithCode("Could not update", $allDogsList['error']);
}

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

function sendError($msg)
{
    $data = array(
        "success" => false,
        "error" => $msg
    );

    // silently quit and send data
    die(json_encode($data));
}
?>