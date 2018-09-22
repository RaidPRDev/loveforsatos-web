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
include ('playlist/class.playlist.php');

if (!isset($_POST['id']))
{
    sendError("Missing id");
}

if (!isset($_POST['pid']))
{
    sendError("Missing pid");
}

$dog_id = $_POST['id'];
$playlist_id = $_POST['pid'];
// $token = $_POST['tid'];

$team_id = -1;

$playlist = new PLAYLIST($DB_con, 'playlist');
$resultData = $playlist->getPlaylistByID($_POST['pid']);

if ($resultData["success"])
{
    $team_id = $resultData["playlist"]['team_id'];

    $resultData = $dogs->fetchPromiseByID($_POST['id']);
    if ($resultData["success"])
    {
        $isPromise = 'no';
        $promiseEnabled = $resultData['item']['adopted'];
        $promiseBy = $resultData['item']['adopted_by'];

        if ($promiseEnabled == 'no')
        {
            $isPromise = 'yes';
        }
        else
        {
            $isPromise = 'no';
        }

        $promiseInfo = array();
        $promiseInfo['id'] = $dog_id;
        $promiseInfo['adopted'] = $isPromise;
        $promiseInfo['adopted_by'] = $team_id;

        $resultData = $dogs->updatePromise($promiseInfo);
        if ($resultData["success"])
        {
            $response["success"] = true;
            $data = array(
                "success" => true,
                "isPromise" => $isPromise,
                "error" => "",
                "errorCode" => 0
            );

            // silently quit and send data
            die(json_encode($data));
        }
        else
        {
            $response["success"] = false;
            $response["error"] = $resultData["error"];
        }
    }
    else
    {
        $response["success"] = false;
        $response["error"] = $resultData["error"];
    }
}
else
{
    $response["success"] = false;
    $response["error"] = $resultData["error"];
    sendErrorWithCode("Could not update", $response["error"]);
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