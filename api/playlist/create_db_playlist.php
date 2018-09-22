<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/18/2018
 * Time: 8:27 PM
 */
include_once '../config/dbconfig.php';
include_once $ROOT_PATH . '/api/playlist/class.playlist.php';

$playlist = new PLAYLIST($DB_con, 'playlist');

$playlistInfo = array();

if (!isset($_POST['user_id']))
{
    sendError("Missing uid");
}

if (isset($_POST['description']))
{
    $playlistInfo['description'] = $_POST['description'];
}

// create playlist token id
$token = md5( uniqid() );
$playlistInfo['token'] = $token;
$playlistInfo['user_id'] = $_POST['user_id'];
$playlistInfo['team_id'] = $_POST['team_id'];

$register = $playlist->create($playlistInfo);
if ($register['success'])
{
    $data = array(
        "success" => true,
        "error" => "",
        "errorCode" => 0,
        "lastInsertId" => $register['lastInsertId'],
        "playlist" => $playlistInfo
    );

    // silently quit and send data
    die(json_encode($data));

}
else
{
    sendErrorWithCode("Could not register", $register['error']);
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