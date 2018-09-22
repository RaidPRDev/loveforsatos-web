<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/9/2018
 * Time: 11:01 AM
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once 'api/config/dbconfig.php';
include_once 'api/config/settings.php';
include ('api/playlist/class.playlist.php');

/*
$items = "184,185,186,193,194,195";
$selectedItems = explode(",", $items);

$allDogsList = $dogs->fetchAllItemsWithPhotos($selectedItems);

highlight_string(json_encode($allDogsList));

$total = count($allDogsList['items']);

$imploder = "( '" . implode( "','" , $selectedItems ) . "' ) ";

echo "<br><br>Total: " . $total;
echo "<br><br>Imploder: " . $imploder;
echo "<br><br>selectedItems : " . json_encode( $selectedItems );

if (!$allDogsList)
{
    $response["success"] = false;
    $response["error"] = "ERROR: Please check server.";
}
*/

/*$playlist = new PLAYLIST($DB_con, 'playlist');
$itemId = 12;

$resultData = $playlist->removePlaylistEntriesByID($itemId, 'playlist_entries');
if ($resultData["success"])
{
    highlight_string(json_encode($resultData));

}
else
{
    highlight_string(json_encode($resultData));
}

exit;*/

$dogList = array();

if ( isset($_GET['token']) )
{
    // validate playlist token
    $playlist = new PLAYLIST($DB_con, 'playlist');

    $resultData = $playlist->validateToken($_GET['token']);

    if ($resultData["success"])
    {
        highlight_string(json_encode($resultData));
        echo '<br><br>';

        $playlist_id = $resultData['playlist']['id'];
        $resultData = $playlist->getAllPlaylistsWithEntriesByID($playlist_id);
        if ($resultData["success"])
        {
            highlight_string(json_encode($resultData));
            echo '<br><br>';

            if (count($resultData["playlist"]) > 0)
            {
                for ($index = 0; $index < count($resultData["playlist"]); $index++)
                {
                    $dogList[] = $resultData["playlist"][$index]['dog_id'];
                }

                highlight_string(json_encode($dogList));
                echo '<br><br>';

                $resultData = $dogs->fetchAllItemsInIds($dogList);
                if ($resultData["success"])
                {
                    highlight_string(json_encode($resultData));
                    echo '<br><br>';

                    $dogList = $resultData['items'];
                    highlight_string(json_encode($dogList));
                    echo '<br><br>';
                }
                else
                {
                    highlight_string(json_encode($resultData));
                    echo '<br><br>';
                }
            }
        }
        else
        {
            highlight_string(json_encode($resultData));
            exit;
        }
    }
}
?>