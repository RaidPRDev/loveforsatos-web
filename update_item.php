<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $EDIT_ITEM_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = ($isUserLogged) ? "Update Item" : "View Item";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();
$itemId = -1;                   // item id
$itemName = '';                 // item name
$itemInfo = null;               // item details
$isIDNotValid = false;          // id flag
$removeLink = "";               // remove button
$uploadPath = $GLOBALS['SAVEDATA']->data['UPLOAD_FULL_PATH'];
$photoList = null;
$photoLen = 0;

if ( isset($_GET['id']) )
{
    // check if id exists
    $itemId = $_GET['id'];
    $resultData = $dogs->fetchItemById($itemId);

    if ($resultData["success"])
    {
        $itemInfo = $resultData["item"];
        $itemName = $itemInfo['name'];

        $removeLink = "onclick='removeItem(\"".$itemInfo["id"]."\", \"".$itemInfo["name"]."\")'";
        $photoList = $resultData['photos']['items'];
        $photoLen = count($photoList);
    }
    else
    {
        $isIDNotValid = true;
        $errorMessage = "Unable to retrieve team info.";
    }
}

// if user not logged, disable input fields
$disableInput = '';
$imgClassView = 'class="itemImageViewSrc"';
if (!$isUserLogged)
{
    $disableInput = 'disabled style="color:var(--primary-font-color); ';
    $disableInput .= '-webkit-text-fill-color:var(--primary-font-color);" ';

    $imgClassView = ' class="itemImageViewSrc" ';
}

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Photo Slider Tools  -->
<script type="text/javascript" src="js/components/slideshow.js?t=<?=MD5(uniqid())?>"></script>

<!-- Application Start -->
<script type="text/javascript">

    // @Override called from app.js
    function appInitialized()
    {
        trace("update item page initialized");

        selectedItemId = parseInt('<?=$itemId ?>');
        selectedItemName = '<?=$itemName ?>';

        // check if the item id is not valid
        var isIDNotValid = "<?php echo $isIDNotValid ?>";
        if (isIDNotValid == "1")
        {
            showAlert("Item Not Found", "We were not able to open this ID", "error", goHome);
            return;
        }

        // disable auto upload
        isAutomaticUpload = false;

        // force to use uploadedFileList
        useUploadListForTransfer = true;

        // create uploader
        this.initializeUpload();

        // Photo Slider
        this.initializePhotoSlider();
    }

    /*
        Removes current itemID data and photos from Server
     */
    function removeItem(id, name)
    {
        removeFromDatabase(id, name);
    }

    function removeFromDatabase(itemID, itemName)
    {
        trace("removeFromDatabase.itemID:", itemID + ' itemName: ' + itemName);

        swal({
            title: "Remove " + itemName + "?",
            text: "You will not be able to recover this data!",
            icon: "warning",
            closeOnClickOutside: false,
            closeOnEsc: false,
            buttons: ["CANCEL","REMOVE"],
            dangerMode: true,
            className: ""
        })
        .then(function(willDelete)
        {
            if (!willDelete) return;

            const ajaxData    = new FormData();
            ajaxData.append( "itemID", itemID );

            // ajax request
            $.ajax(
            {
                url: 			'api/remove_db_item.php',
                type:			'post',
                data: 			ajaxData,
                dataType:		'json',
                cache:			false,
                contentType:	false,
                processData:	false,
                complete: function()
                {
                    trace("removeFromDatabase.complete");
                },
                success: function( data )
                {
                    trace("removeFromDatabase.success.data: ", data);
                    if( !data.success )
                    {
                        showAlert("Error", data.error, "error");
                        updateInputFieldStatus(data.error, data.errorCode);
                    }
                    else
                    {
                        showAlert("Item Removed",
                            "Your data has been deleted",
                            "success",
                            goHome);
                    }
                },
                error: function(request, status, error)
                {
                    trace("removeItemFromDatabase.error", error);

                    showAlert("Error", error, "error");
                }
            });
        });
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        const dataItemID = $(menuItem).attr('data-menu-id');

        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'home':
                goHome();
                break;
        }
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <form class="itemForm" method="post" action="api/update_db_item.php" autocomplete="on"
              enctype="multipart/form-data">
            <input type="hidden" name="itemID" value="<?=$itemInfo['id'];?>"/>
            <ul>
                <?php
                // check if its promised
                if ($itemInfo["adopted"] == "yes")
                {
                    // get team id and name
                    $team_id = $itemInfo["adopted_by"];
                    $team_user = $users->getUser($team_id);
                    $team_name = $team_user['userInfo']['name'];
                }
                ?>
                <?php if ($itemInfo["adopted"] == "yes") { ?>
                    <li class="item-promised-list">
                        <label for="name"><span class="fas fa-star"></span>&nbsp;Promised By</label>
                        <div class="item-edit-label"><?=$team_name?></div>
                        <span></span>
                    </li>
                <?php } ?>
                <li class="slideshow">
                    <label for="uploadImage">Photos</label>
                    <div class="user-photo-slider">
                        <?php
                        for ($index = 0; $index < $photoLen; $index++) {
                            ?>
                            <div class="item" data-item-id="<?=$photoList[$index]['id']?>">

                                <div class="inner-item">
                                    <?php if ($isUserLogged) { ?>
                                        <div class="item-remove-photo" onclick="onRemovePhotoItem(this)">
                                            <span class="fas fa-times-circle"></span>
                                        </div>
                                    <?php } ?>
                                    <?php if ($itemInfo["adopted"] == "yes" && $index == 0) { ?>
                                        <div class="item-promise-photo">
                                            <span class="fas fa-star"></span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <img <?=$imgClassView?> src="<?=$uploadPath.$photoList[$index]['full_image_url'] ?>">
                            </div>

                        <?php } ?>
                    </div>
                </li>
                <?php
                    if ($isUserLogged) {
                ?>
                <li>
                    <label for="uploadImage">Upload</label>
                    <div class="box">
                        <div class="box__input">
                            <svg class="box__icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"></path></svg>
                            <input class="box__file" type="file" name="files[]" id="file" data-multiple-caption="{count} files selected" multiple />
                            <label class="inputLabel" for="file"><strong>Choose photo(s)</strong><span class="box__dragndrop"> or drag it here</span></label>
                            <button class="box__button" type="submit">Upload</button>
                        </div>
                        <div class="box__uploading">Uploading&hellip;</div>
                        <div class="box__success">Done!</div>
                        <div class="box__error">Error! <span></span>.</div>
                    </div>
                </li>
                <?php } ?>
                <li>
                    <label for="name">Name</label>
                    <input <?=$disableInput?> type="text" name="name"
                           maxlength="100" value="<?=$itemInfo['name'];?>"/>
                    <span>Enter name here</span>
                </li>
                <li>
                    <label for="age">Age</label>
                    <select <?=$disableInput?> id="age" name="age" class="itemSelect arrowIcon">
                        <optgroup label="Choose Age">
                        <?php
                            echo $forms->generateAgeSelectOptions($itemInfo['age']);
                        ?>
                        </optgroup>
                    </select>
                    <span>How old is the dog?</span>
                </li>
                <li>
                    <label for="litter">Litter</label>
                    <input <?=$disableInput?> type="text" name="litter" maxlength="100" value="<?=$itemInfo['litter'];?>"/>
                    <span>Enter your litter number</span>
                </li>
                <li>
                    <label for="gender">Gender</label>
                    <select <?=$disableInput?> id="gender" name="gender" class="itemSelect arrowIcon">
                        <optgroup label="Choose Gender">
                        <?php
                            echo $forms->generateGenderOptions($itemInfo['gender']);
                        ?>
                        </optgroup>
                    </select>
                    <span>Male or Female?</span>
                </li>
                <li>
                    <label for="fixed">Fixed</label>
                    <select <?=$disableInput?> id="fixed" name="fixed" class="itemSelect arrowIcon">
                        <optgroup label="Choose Fixing">
                            <?php
                            echo $forms->generateFixedOptions($itemInfo['fixed']);
                            ?>
                        </optgroup>
                    </select>
                    <span>Is the dog fixed?</span>
                </li>
                <li>
                    <label for="description">Description</label>
                    <textarea <?=$disableInput?> name="description"
                              onkeyup="adjust_textarea(this)"><?=trim($itemInfo['description']);?></textarea>
                    <span>Say something about the dog</span>
                </li>
                <li>
                    <label for="history">History</label>
                    <textarea <?=$disableInput?> name="history"
                              onkeyup="adjust_textarea(this)"><?=trim($itemInfo['history']);?></textarea>
                    <span>Any comments on the dog's history</span>
                </li>
                <?php if ($isUserLogged) { ?>
                <li class="li-nopadding">
                    <div class='itemEditButton'>
                        <button type="submit" class='itemButton'>
                            <span class="navMenuIcon fas fa-check-circle"></span>
                            <span class="navMenuItemText">UPDATE</span>
                        </button>
                    </div>

                    <div class='itemEditButton'>
                        <button type="button" <?=$removeLink?>  class='itemButton error'>
                            <span class="navMenuIcon fas fa-trash-alt"></span>
                            <span class="navMenuItemText">REMOVE</span>
                        </button>
                    </div>
                </li>
                <?php } else { ?>
                <li class="li-nopadding"></li>
                <?php } ?>
            </ul>
        </form>
        <!-- End of Form -->
    </div>
</div>
<!-- Page Content End -->

<!-- Page Container End -->
<?php include ('layout/page_end_block.php'); ?>