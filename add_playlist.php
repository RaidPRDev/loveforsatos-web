<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');
include ('api/playlist/class.playlist.php');


/* =====> Global Page Properties */
$currScreen = $ADD_PLAYLIST_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Add List";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();
$itemInfo = null;
$itemName = '';
$playlist = new PLAYLIST($DB_con, 'playlist');

// check if we are logged in *required
if (!$isUserLogged)
{
    $errorMessage = "user not logged in";
    //echo $errorMessage;
    header('Location: satos.php');
    exit;
}

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
        trace("add playlist page initialized");

        initializePlaylistAdd();
     }

    function initializePlaylistAdd()
    {
        var $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );
        $form.on( 'submit', onSubmitCreatePlaylist);
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

            case 'create':
                onCreatePlaylistSelect(menuItem);
                break;

            case 'playlists':
                window.location.replace("createlist_home.php?t=" + MD5(Date.now()));
                break;
        }
    }

    function onCreatePlaylistSelect(navItem)
    {
        trace("onCreatePlaylistSelect.navItem: ", navItem);

        $form = $('.itemForm');
        $form.trigger( 'submit' );
    }

    function onSubmitCreatePlaylist(e)
    {
        e.preventDefault();

        trace("onSubmitCreatePlaylist");

        var $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        var ajaxData    = new FormData( $form[0] );

        // check fields
        var i;
        for (i = 0; i < $form[0].length; i++)
        {
            if ($form[0][i].type == "submit"
                || $form[0][i].name == "" ) continue;

            trace("createPlaylist: ", $form[0][i].name + ': ' + $form[0][i].value);

            ajaxData.append( $form[0][i].name, $form[0][i].value );
        }

        trace("ajaxData:", ajaxData);

        // ajax request
        $.ajax(
        {
            url: 			'api/playlist/create_db_playlist.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("createPlaylist.complete");

            },
            success: function( data )
            {
                trace("createPlaylist.success.data: ", data);
                $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                }
                else
                {
                    showAlert("Created List",
                        "List item has been saved.",
                        "success",
                        goUpdatePlaylist,
                        data.lastInsertId);
                }
            },
            error: function(request, status, error)
            {
                trace("createPlaylist.error", error);

                showAlert("Error", error, "error");
            }
        });
    }

    function goUpdatePlaylist(id)
    {
        window.location.replace("update_playlist.php?id=" + id + "&t=" + MD5(Date.now()));
    }

    function validateInputField(input, name)
    {
        const nameLabel = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();

        if (input.val() < 5)
        {
            showAlert(nameLabel, nameLabel + " is missing or invalid", "error");

            getInputField(name);

            return false;
        }

        return true;
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <form class="itemForm" method="post" action="api/playlist/create_db_playlist.php" autocomplete="on"
              enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?=$uid;?>"/>
            <ul>
                <li>
                    <label for="team_id">Team Name</label>
                    <select id="team_id" name="team_id" class="itemSelect arrowIcon">
                        <optgroup label="Choose Team">
        <?php

            $selectedTeamId = $itemInfo['team_id'];
            $teams = $users->getUsersByRole('team');
            $divElement = '';
            if ($teams['success'])
            {
                $teamUsers = $teams['users'];
                for ($i = 0; $i < count($teamUsers); $i++) {

                    $teamID = $teamUsers[$i]['id'];
                    $label = $teamUsers[$i]['name'];
                    $selected = ($teamUsers[$i] == $selectedTeamId) ? "selected" : "";
                    $divElement .= "<option $selected value='$teamID'>";
                    $divElement .= ucwords($label)."</option>";
                    $divElement .= PHP_EOL;
                }
                echo $divElement;
            }
        ?>
                        </optgroup>
                    </select>
                    <span>Teams</span>
                </li>
                <li>
                    <label for="description">Brief Description</label>
                    <input type="text" name="description" value="<?=$itemInfo['description']?>"
                           maxlength="30"/>
                    <span>max 30 chars</span>
                </li>
                <li class="li-nopadding">
                    <div class='itemEditButton'>
                        <button type="submit" class='itemButton'>
                            <span class="navMenuIcon fas fa-plus-circle"></span>
                            <span class="navMenuItemText">ADD LIST</span>
                        </button>
                    </div>
                </li>
            </ul>
        </form>
        <!-- End of Form -->
    </div>
</div>
<!-- Page Content End -->

<!-- Page Container End -->
<?php include ('layout/page_end_block.php'); ?>