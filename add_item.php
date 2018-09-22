<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $ADD_ITEM_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Add Item";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();
$photoList = null;
$photoLen = 0;

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
        trace("add item page initialized");

        // Drag and Drop, File Upload Handling
        isAutomaticUpload = false;

        // force to use uploadedFileList
        useUploadListForTransfer = true;

        // create uploader
        this.initializeUpload();

        // Photo Slider
        this.initializePhotoSlider();
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        var dataItemID = $(menuItem).attr('data-menu-id');

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
        <form class="itemForm" method="post" action="api/add_db_item.php" autocomplete="on"
              enctype="multipart/form-data">
            <ul>
                <li>
                    <label for="name">Name</label>
                    <input type="text" name="name" maxlength="100"/>
                    <span>Enter name here</span>
                </li>
                <li>
                    <label for="age">Age</label>
                    <select id="age" name="age" class="itemSelect arrowIcon">
                        <optgroup label="Choose Age">
                        <?php
                            echo $forms->generateAgeSelectOptions($itemInfo['age']);
                        ?>
                        </optgroup>
                    </select>
                    <span>Age</span>
                </li>
                <li>
                    <label for="litter">Litter</label>
                    <input type="text" name="litter" maxlength="200"/>
                    <span>Enter your litter number</span>
                </li>
                <li>
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="itemSelect arrowIcon">
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
                    <select id="fixed" name="fixed" class="itemSelect arrowIcon">
                        <optgroup label="Choose Fixing">
                            <?php
                            echo $forms->generateFixedOptions($itemInfo['fixed']);
                            ?>
                        </optgroup>
                    </select>
                    <span>Is the dog fixed?</span>
                </li>
                <li class="slideshow">
                    <label for="uploadImage">Photos</label>
                    <div class="user-photo-slider">
                        <?php
                        for ($index = 0; $index < $photoLen; $index++) { ?>

                            <div class="item" data-item-id="<?=$photoList[$index]['id']?>">
                                <div class="inner-item">
                                    <div class="item-remove-photo" onclick="onRemovePhotoItem(this)">
                                        <span class="fas fa-times-circle"></span>
                                    </div>
                                </div>
                                <img src="<?=$uploadPath.$photoList[$index]['thumb_image_url'] ?>">
                            </div>

                        <?php } ?>
                    </div>
                </li>
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
                <li>
                    <label for="description">Description</label>
                    <textarea name="description"
                              onkeyup="adjust_textarea(this)"></textarea>
                    <span>Say something about the dog</span>
                </li>
                <li>
                    <label for="history">History</label>
                    <textarea name="history"
                              onkeyup="adjust_textarea(this)"></textarea>
                    <span>Some history regarding the dog</span>
                </li>
                <li class="li-nopadding">
                    <div class='itemEditButton'>
                        <button type="submit" class='itemButton'>
                            <span class="navMenuIcon fas fa-plus-circle"></span>
                            <span class="navMenuItemText">ADD</span>
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