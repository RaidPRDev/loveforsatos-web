<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include_once $ROOT_PATH . '/api/playlist/class.playlist.php';

/* =====> Global Page Properties */
$playlist = new PLAYLIST($DB_con, 'playlist');
$currScreen = $PLAYLIST_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Sato Lists";

/* =====> PHP/mySQL Application Start */
$playlistCollection = null;
$collectionCount = 0;

// check if we are logged in *required
if (!$isUserLogged)
{
    $errorMessage = "user not logged in";
    //echo $errorMessage;
    header('Location: satos.php');
    exit;
}

// echo  'id: ' . $_SESSION['user_id'];
// get user id and fetch user data
$uid = $_SESSION['user_id'];
$resultData = $users->getUser($uid);
if ($resultData["success"])
{
    $usersList = $resultData["userInfo"];
    $usersCount = count($usersList);
}
else
{
    $errorMessage = "Unable to retrieve users.";
    echo $errorMessage;
    exit;
}

// get playlist items by user id
$resultData = $playlist->getAllPlaylistsByUserID($uid);
if ($resultData["success"])
{
    $playlistCollection = $resultData["playlist"];
    $collectionCount = count($playlistCollection);
}
else
{
    $errorMessage = "Unable to retrieve playlist.";
    // echo $errorMessage;
    // exit;
}

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Add User Screen JS
<link type="text/javascript" src="js/screens/add_user_screen.js?t=<=MD5(uniqid())?>" />
-->

<!-- Application Start -->
<script type="text/javascript">

    // @Override called from app.js
    function appInitialized()
    {
        trace("create list page initialized");
    }

    function onListButtonClick(listItem)
    {
        trace("onListButtonClick:", listItem);

        var playlistID = $(listItem).attr('data-playlist-id');

        window.location.replace("update_playlist.php?id=" + playlistID + "&t=" + MD5(Date.now()));
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        const dataItemID = $(menuItem).attr('data-menu-id');
        const dataGroupID = $(menuItem).attr('data-menu-group');
        const dataItemIndex = $(menuItem).attr('data-menu-index');

        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'addplaylist':
                window.location.replace("add_playlist.php?t=" + MD5(Date.now()));
                break;
        }
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <div class="itemForm">
            <ul>
                <?php

                    for ($i = 0; $i < $collectionCount; $i++)
                    {
                        $buttonLabel = $playlistCollection[$i]['team_name'];

                        if (!empty($playlistCollection[$i]['description']))
                        {
                            $buttonLabel .= ' (' . $playlistCollection[$i]['description'] . ')';
                        }
                ?>
                <li class="listItem">
                    <label for="user">List #<?=$i + 1?></label>
                    <div class='itemListButton'>
                        <button type="button" class='itemButton align-left'
                                data-playlist-id="<?=$playlistCollection[$i]['playlist_id']?>"
                                onclick="onListButtonClick(this)">
                            <span class="navMenuIcon fas fa-list-alt"></span>
                            <span class="navMenuItemText"><?=$buttonLabel?></span>
                        </button>
                    </div>
                    <span></span>
                </li>
                <?php } ?>
            </ul>
        </div>
        <!-- End of Div -->
    </div>
</div>
<!-- Page Content End -->

<!-- Page Container End -->
<?php include ('layout/page_end_block.php'); ?>