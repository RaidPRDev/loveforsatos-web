<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/playlist/class.playlist.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $VIEW_ITEM_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "View Item";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();
$playlist = new PLAYLIST($DB_con, 'playlist');
$playlist_id = -1;              // playlist id
$dog_id = -1;                   // dog id
$itemId = -1;                   // item id
$itemName = '';                 // item name
$itemInfo = null;               // item details
$isIDNotValid = false;          // id flag
$removeLink = "";               // remove button
$uploadPath = $GLOBALS['SAVEDATA']->data['UPLOAD_FULL_PATH'];
$photoList = null;
$photoLen = 0;
$playlistInfo = array();

if (is_null($_SESSION['token']))
{
   // $isIDNotValid = true;
}
else
{
    if ( isset($_GET['id']) && isset($_GET['pid']) )
    {
        // check if id exists
        $itemId = $_GET['id'];
        $playlist_id = $_GET['pid'];
        $resultData = $dogs->fetchItemById($itemId);

        if ($resultData["success"])
        {
            $itemInfo = $resultData["item"];
            $dog_id = $itemInfo["id"];
            $itemName = $itemInfo['name'];
            $photoList = $resultData['photos']['items'];
            $photoLen = count($photoList);

            $resultData = $playlist->getPlaylistByID($playlist_id);
            if ($resultData["success"])
            {
                $playlistInfo = $resultData['playlist'];
            }
        }
        else
        {
            $isIDNotValid = true;
            $errorMessage = "Unable to retrieve info.";
        }
    }
    else
    {
        $isIDNotValid = true;
        $errorMessage = "Unable to retrieve info.";
    }
}



/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Photo Slider Tools  -->
<script type="text/javascript" src="js/components/slideshow.js?t=<?=MD5(uniqid())?>"></script>

<!-- Promise Item  -->
<script type="text/javascript" src="js/components/promise.js?t=<?=MD5(uniqid())?>"></script>

<!-- Application Start -->
<script type="text/javascript">

    // @Override called from app.js
    function appInitialized()
    {
        trace("update item page initialized");

        viewToken = '<?=$_SESSION['token']?>';

        if (viewToken == '')
        {
            window.location.replace("view.php");
        }

        // check if the item id is not valid
        var isIDNotValid = "<?php echo $isIDNotValid ?>";
        if (isIDNotValid == "1")
        {
            showAlert("Item Not Found", "We were not able to open this ID", "error");
            return;
        }

        // Photo Slider
        this.initializePhotoSlider();
    }

    function menuSelect(menuItem)
    {
        trace("ViewItem.menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        var dataItemID = $(menuItem).attr('data-menu-id');

        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'home':
                window.location.replace("view.php?tid=" + viewToken);
                break;
        }
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <div class="itemForm itemView">
            <ul>
                <li class="slideshow">
                    <label for="uploadImage">Photos</label>
                    <div class="user-photo-slider">
                        <?php
                        for ($index = 0; $index < $photoLen; $index++) { ?>

                            <div class="item" data-item-id="<?=$photoList[$index]['id']?>">
                                <?php
                                // check if we have promised, show icon
                                if ($itemInfo["adopted"] == "yes" && $index == 0) { ?>
                                    <div class="inner-item">
                                        <div class="item-promise-photo">
                                            <span class="fas fa-star"></span>
                                        </div>
                                    </div>
                                <?php } ?>
                                <img class="itemImageViewSrc" src="<?=$uploadPath.$photoList[$index]['full_image_url'] ?>">
                            </div>

                        <?php } ?>
                    </div>
                </li>
                <li>
                    <label for="name">Name</label>
                    <div class="item-view-label"><?=ucwords($itemInfo['name'])?></div>
                    <span></span>
                </li>
                <li>
                    <label for="age">Age</label>
                    <div class="item-view-label"><?=$forms->generateAgeLabel($itemInfo['age'])?></div>
                    <span></span>
                </li>
                <li>
                    <label for="litter">Litter</label>
                    <div class="item-view-label"><?=$itemInfo['litter']?></div>
                    <span></span>
                </li>
                <li>
                    <label for="gender">Gender</label>
                    <div class="item-view-label"><?=ucwords($itemInfo['gender'])?></div>
                    <span></span>
                </li>
                <li>
                    <label for="fixed">Fixed</label>
                    <div class="item-view-label"><?=ucwords($itemInfo['fixed'])?></div>
                    <span></span>
                </li>
                <li>
                    <label for="description">Description</label>
                    <div class="item-view-label"><?=$forms->checkFieldIsEmpty($itemInfo['description'])?></div>
                    <span></span>
                </li>
                <li class="listItem">
                    <label for="history">History</label>
                    <div class="item-view-label"><?=$forms->checkFieldIsEmpty($itemInfo['history'])?></div>
                    <span></span>
                </li>
                <li class="li-nopadding">
                    <?=$forms->generatePromiseButton($itemInfo, $playlistInfo);?>
                </li>
            </ul>
        </div>
        <!-- End of Form -->
    </div>
</div>
<!-- Page Content End -->

<!-- Page Container End -->
<?php include ('layout/page_end_block.php'); ?>