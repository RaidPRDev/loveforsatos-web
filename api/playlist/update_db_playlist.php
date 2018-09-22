<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/18/2018
 * Time: 8:27 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once '../config/dbconfig.php';
include_once $ROOT_PATH . '/api/playlist/class.playlist.php';

$playlist = new PLAYLIST($DB_con, 'playlist');

$playlistInfo = array();

if (!isset($_POST['playlist_id']))
{
    sendError("Missing id");
}

if (isset($_POST['description']))
{
    $playlistInfo['description'] = $_POST['description'];
}

$postSelectedItems = ( isset($_POST['addItemIds']) ) ? $_POST['addItemIds'] : "184,185,186,193,194,195";
$selectedItems = explode(",", $postSelectedItems);

// create playlist token id
$playlistInfo['id'] = $_POST['playlist_id'];
$playlistInfo['team_id'] = $_POST['team_id'];

$update = $playlist->update($playlistInfo);
if ($update['success'])
{
    // now add the dog_ids if any
    $playEntries = array();
    $duplicateEntries = array();
    $playEntries['playlist_id'] = $_POST['playlist_id'];

    // remove all entries first
    $resultData = $playlist->removePlaylistEntriesByID($playEntries['playlist_id'], 'playlist_entries');
    if ($resultData["success"])
    {
        for ($index = 0; $index < count($selectedItems); $index++)
        {
            $playEntries['dog_id'] = $selectedItems[$index];

            $addEntry = $playlist->addEntry($playEntries);
            if ($addEntry['success'])
            {
                // move on...
            }
        }

        $data = array(
            "success" => true,
            "error" => "",
            "errorCode" => 0,
            "selectedItems" => $selectedItems
        );

        // silently quit and send data
        die(json_encode($data));
    }
    else
    {
        $data = array(
            "success" => error,
            "error" => $resultData['error'],
            "errorCode" => 0,
            "selectedItems" => $selectedItems
        );

        // silently quit and send data
        die(json_encode($data));
    }
}
else
{
    sendErrorWithCode("Could not update", $register['error']);
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