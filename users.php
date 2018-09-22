<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');

/* =====> Global Page Properties */
$currScreen = $USERS_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Users";

/* =====> PHP/mySQL Application Start */
$usersList = null;
$usersCount = 0;

$resultData = $users->getAllUsers();
if ($resultData["success"])
{
    $usersList = $resultData["users"];
    $usersCount = count($usersList);
}
else
{
    $errorMessage = "Unable to retrieve users.";
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
        trace("users page initialized");
    }

    function onListButtonClick(listItem)
    {
        trace("onListButtonClick:", listItem);

        const userID = $(listItem).attr('data-user-id');

        window.location.replace("update_user.php?id=" + userID + "&t=" + MD5(Date.now()));
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        var dataItemID = $(menuItem).attr('data-menu-id');
        var dataGroupID = $(menuItem).attr('data-menu-group');
        var dataItemIndex = $(menuItem).attr('data-menu-index');

        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'adduser':
                window.location.replace("register_user.php?t=" + MD5(Date.now()));
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

                    for ($i = 0; $i < $usersCount; $i++)
                    {
                ?>
                <li class="listItem">
                    <label for="user"><?=ucwords($usersList[$i]['role'])?></label>
                    <div class='itemListButton'>
                        <button type="button" class='itemButton' data-user-id="<?=$usersList[$i]['id']?>"
                                onclick="onListButtonClick(this)">

                            <?php if ($usersList[$i]['role'] == 'admin') { ?>
                                <span class="navMenuIcon fas fa-user-astronaut"></span>
                            <?php } else { ?>
                                <span class="navMenuIcon fas fa-user-circle"></span>
                            <?php } ?>
                            <span class="navMenuItemText"><?=$usersList[$i]['name']?></span>
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