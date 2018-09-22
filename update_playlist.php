<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');
include ('api/playlist/class.playlist.php');

/* =====> Global Page Properties */
$currScreen = $UPDATE_PLAYLIST_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Update List";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();

$playlist = new PLAYLIST($DB_con, 'playlist');
$plEntries = null;
$itemInfo = null;
$itemName = '';
$removeLink = "";

// check if we are logged in *required
if (!$isUserLogged)
{
    $errorMessage = "user not logged in";
    //echo $errorMessage;
    header('Location: satos.php');
    exit;
}

if ( isset($_GET['id']) )
{
    $itemId = $_GET['id'];
    $resultData = $playlist->getAllPlaylistEntriesWithPromiseByID($itemId);

    if ($resultData["success"])
    {
        if (count($resultData["playlist"]) > 0)
        {
            $itemInfo = $resultData["playlist"][0];
            $itemName = $itemInfo['team_name'];
            $plEntries = $resultData["playlist"];
        }
    }
    else
    {
        // has no entries
        $resultData = $playlist->getAllPlaylistsByID($itemId);
        if (!$resultData["success"])
        {
            // highlight_string(json_encode($resultData));
        }
        else
        {
            $plEntries = null;
            $itemInfo = $resultData["playlist"][0];
            $itemName = $itemInfo['team_name'];
        }
    }

    $imgClassView = 'class="itemImageViewSrc"';
    if (!$isUserLogged)
    {
        $imgClassView = ' class="itemImageViewSrc" ';
    }
}

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Photo Slider Tools  -->
<script type="text/javascript" src="js/components/slideshow.js?t=<?=MD5(uniqid())?>"></script>

<!-- Add User Screen JS
<link type="text/javascript" src="js/screens/add_user_screen.js?t=<=MD5(uniqid())?>" />
-->

<!-- Application Start -->
<script type="text/javascript">

    // @Override called from app.js
    function appInitialized()
    {
        trace("update user page initialized");

        selectedItemId = parseInt('<?=$itemId ?>');
        selectedItemName = '<?=$itemName ?>';

        initializePlaylistUpdate();

        // Item Slider
        this.initializeItemSlider();
     }

    function initializePlaylistUpdate()
    {
        const $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );
        $form.on( 'submit', onSubmitUpdatePlaylist);
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        var dataItemID = $(menuItem).attr('data-menu-id');
        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'remove':
                removePlaylist();
                break;

            case 'update':
                onUpdatePlaylistSelect(menuItem);
                break;

            case 'playlists':
                goPlaylistHome();
                break;
        }
    }

    function onUpdatePlaylistSelect(navItem)
    {
        trace("onUpdatePlaylistSelect.navItem: ", navItem);

        $form = $('.itemForm');
        $form.trigger( 'submit' );
    }

    function onSubmitUpdatePlaylist(e)
    {
        e.preventDefault();

        trace("onSubmitUpdatePlaylist: ", currentItemList);

        // add new ids to current list
        var currentItemIds = [];
        for (var index = 0; index < currentItemList.length; index++)
        {
            trace("data-item-id: ", $(currentItemList[index]).attr('data-item-id'));

            currentItemIds.push($(currentItemList[index]).attr('data-item-id'));
        }


        var $form		= $('.itemForm');

        trace('currentItemIds:', currentItemIds);

        var ajaxData    = new FormData( $form[0] );
        ajaxData.append( 'addItemIds', currentItemIds );

        // check fields
        var i;
        for (i = 0; i < $form[0].length; i++)
        {
            if ($form[0][i].type == "submit"
                || $form[0][i].name == "" ) continue;

            trace("updatePlaylist: ", $form[0][i].name + ': ' + $form[0][i].value);

            ajaxData.append( $form[0][i].name, $form[0][i].value );
        }

        trace("ajaxData:", ajaxData);

        // ajax request
        $.ajax(
        {
            url: 			'api/playlist/update_db_playlist.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("updatePlaylist.complete");

            },
            success: function( data )
            {
                $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                if( !data.success )
                {
                    trace('updatePlaylist.error.data', data);
                    showAlert("Error", data.error, "error");
                }
                else
                {
                    trace('updatePlaylist.success.data', data);
                    showAlert("List Update",
                        "List info has been saved.",
                        "success",
                        reloadLocation);
                }
            },
            error: function(request, status, error)
            {
                trace('updatePlaylist.error', error);

                showAlert("Error", error.toString(), "error");
            }
        });
    }

    function validateInputField(input, name)
    {
        name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();

        if (input.val() < 5)
        {
            showAlert(name, name + " is missing or invalid", "error");

            getInputField(name);

            return false;
        }

        return true;
    }

    /*
        Removes current itemID data and photos from Server
     */
    function removePlaylist()
    {
        removePlaylistFromDatabase(selectedItemId, selectedItemName);
    }

    function removePlaylistFromDatabase(itemID, itemName)
    {
        trace("removePlaylistFromDatabase.itemID:", itemID + ' itemName: ' + itemName);

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
        .then(function(willDevare)
        {
            if (!willDevare) return;

            const ajaxData    = new FormData();
            ajaxData.append( "itemID", itemID );

            // ajax request
            $.ajax(
            {
                url: 			'api/playlist/remove_db_playlist.php',
                type:			'post',
                data: 			ajaxData,
                dataType:		'json',
                cache:			false,
                contentType:	false,
                processData:	false,
                complete: function()
                {
                    trace("removePlaylistFromDatabase.complete");
                },
                success: function( data )
                {
                    trace("removePlaylistFromDatabase.success.data: ", data);
                    if( !data.success )
                    {
                        showAlert("Error", data.error, "error");
                        updateInputFieldStatus(data.error, data.errorCode);
                    }
                    else
                    {
                        showAlert("Playlist Removed",
                            "Your data has been deleted.",
                            "success",
                            goPlaylistHome);
                    }
                },
                error: function(request, status, error)
                {
                    trace("removePlaylistFromDatabase.error", error);

                    showAlert("Error", error, "error");
                }
            });
        });
    }

    function goPlaylistHome()
    {
        window.location.replace("createlist_home.php?t=" + MD5(Date.now()));
    }

    function onAddItem()
    {
        createAddItemSlider();
    }

    function createAddItemSlider()
    {
        trace("createAddItemSlider");

        showLoader("Loading Items");

        var selectedItems = new Array();
        var items = $('.user-photo-slider .item');
        trace('items:', items);

        for (var i = 0; i < items.length; i++)
        {
            var id = $(items[i]).attr('data-item-id');
            trace('selected.id:', id);
            selectedItems.push(id);
        }

        const ajaxData    = new FormData();
        ajaxData.append( "selectedItems", selectedItems );

        // ajax request
        $.ajax({
            url: 			'api/get_db_items.php',
            type:			'post',
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("createAddItemSlider.complete");
                hideLoader();
            },
            success: function( data )
            {
                trace("createAddItemSlider.success.data: ", data);
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                    updateInputFieldStatus(data.error, data.errorCode);
                }
                else
                {
                    trace("Load Slider Popup");
                    createSliderSelectorElement(data.items);
                }
            },
            error: function(request, status, error)
            {
                trace("createAddItemSlider.error", error);

                showAlert("Error", error, "error");
            }
        });
    }

    function createSliderSelectorElement(items)
    {
        trace('createSliderSelectorElement:', items)

        currentSelectItemList = items;

        const slideshow = jQuery('<div/>', {
            id: 'slider-selector',
            class: 'slideshow'
        });

        const photoSlider = jQuery('<div/>', {
            class: 'user-item-selector'
        }).appendTo(slideshow);

        for (var i = 0; i < items.length; i++)
        {
            const dog_id = items[i]['dog_id'];
            const dog_name = items[i]['dog_name'];
            const dog_age = items[i]['dog_age'];
            const dog_gender = items[i]['dog_gender'];
            const dog_adopted = items[i]['dog_adopted'];
            const dog_fixed = items[i]['dog_fixed'];
            const thumb_image_url = items[i]['thumb_image_url'];

            const sliderItem = jQuery('<div/>', {
                class: 'item-select'
            });
            sliderItem.attr('data-item-id', dog_id);

            const innerItem = jQuery('<div/>', {
                class: 'inner-item'
            }).appendTo(sliderItem);

            const itemAdd = jQuery('<div/>', {
                class: 'item-add-item',
                onclick: 'onAddSelectItem(this)'
            }).appendTo(innerItem);
            itemAdd.attr('data-item-id', dog_id);
            itemAdd.attr('data-item-index', i);

            const itemSpan = jQuery('<span/>', {
                class: 'fas fa-plus-circle'
            }).appendTo(itemAdd);

            const itemNameLabel = jQuery('<div/>', {
                class: 'item-name-selector-label'
            }).appendTo(innerItem);

            const itemIcons = jQuery('<div/>', {
                class: 'item-icons'
            }).appendTo(itemNameLabel);

            const itemLabel = jQuery('<span/>', {
                class: 'item-label',
                text: dog_name
            }).appendTo(itemIcons);

            var dog_gender_icon = (dog_gender == 'male') ? "fa-mars" : "fa-venus";
            const itemIconsGender = jQuery('<span/>', {
                class: 'item-icon-fixed fas ' + dog_gender_icon
            }).appendTo(itemIcons);

            if (dog_fixed != 'intact')
            {
                const itemIconsFixed = jQuery('<span/>', {
                    class: 'item-icon-fixed fas fa-notes-medical'
                }).appendTo(itemIcons);
            }

            const itemAge = jQuery('<span/>', {
                class: 'item-age',
                text: generateAgeShortLabel(dog_age)
            }).appendTo(itemIcons);

            const itemImage = jQuery('<img/>', {
                class: 'itemImageSelectorSrc'
            }).appendTo(sliderItem);
            itemImage.attr('src', 'images/photos/' + thumb_image_url);

            trace('dog_id:', dog_id);
            trace('dog_name:', dog_name);
            trace('thumb_image_url:', thumb_image_url);

            photoSlider.append(sliderItem);
        }

        initializeSliderSelector(photoSlider);

        swal({
            title: "Add Item(s)",
            className: 'photo-selector',
            content: photoSlider.get(0),
            closeOnClickOutside: false,
            closeOnEsc: false,
            buttons: "DONE"
        })
        .then(function(value)
        {
            setAddedItemsToSlider();

        });
    }

    function generateAgeShortLabel(ageInMonths)
    {
        var ageLabel = ageInMonths;

        if (ageInMonths < 12)
        {
            ageLabel = (ageInMonths > 1) ? ageInMonths + " mo" : ageInMonths + " mo";
            if (ageInMonths == 0) ageLabel = "Newborn";

        }
        else
        {
            var monthValue = ageInMonths / 12;
            var monthLabel = monthValue + '';

            if (monthLabel.lastIndexOf(".") == -1)
            {
                monthValue = Math.floor(ageInMonths / 12);
                ageLabel = (monthValue > 1) ? monthLabel + " yrs" : monthLabel + " yr";
            }
        }

        return ageLabel;
    }

    function onAddSelectItem(item)
    {
        trace("onAddSelectItem.item:", item);

        const id = $(item).attr('data-item-id');
        const index = $(item).attr('data-item-index');

        // check if we have added the item
        if ( $(item).find('span').hasClass('fa-plus-circle') )
        {
            // add item
            currentAddedItemList[id] = currentSelectItemList[index];
            trace('currentAddedItemList:', currentAddedItemList);

            onAddPlaylistEntryItem(currentAddedItemList[id]);

            $(item).find('span').removeClass('fa-plus-circle');
            $(item).find('span').addClass('fa-check-circle');
        }
        else
        {

            // remove item
            delete currentAddedItemList[id];
            trace('currentAddedItemList:', currentAddedItemList);

            $(item).find('span').removeClass('fa-check-circle');
            $(item).find('span').addClass('fa-plus-circle');
        }
    }

    function setAddedItemsToSlider()
    {

    }

    function openLinkPlaylist(tid)
    {
        window.open('view.php?tid=' + tid, '_blank');
    }


</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <form class="itemForm" method="post" action="api/playlist/update_db_playlist.php" autocomplete="on"
              enctype="multipart/form-data">
            <input type="hidden" name="playlist_id" value="<?=$itemInfo['playlist_id']?>"/>
            <ul>
                <li class="slideshow">
                    <label for="uploadImage">Satos</label>
                    <div class="user-photo-slider">
                        <?php

                        $dogListLen = 0;
                        if ($plEntries != null) $dogListLen = count($plEntries);

                        for ($index = 0; $index < $dogListLen; $index++)
                        {
                            if ($plEntries[$index]['thumb_image_url'] == null)
                            {
                                $imageUrl = "images/nosato_thumb_pic.png";
                            }
                            else $imageUrl = "images/photos/" . $plEntries[$index]['thumb_image_url'];
                            ?>

                            <div class="item" data-item-id="<?=$plEntries[$index]['dog_id']?>">
                                <div class="inner-item">
                                    <div class="item-remove-photo" onclick="onRemovePlaylistEntryItem(this)">
                                        <span class="fas fa-times-circle"></span>
                                    </div>
                                    <div class="item-name-label">
                                        <div class="item-icons">
                                            <span class="item-label"><?=$plEntries[$index]['dog_name']?></span>

                                            <?php if ($plEntries[$index]['dog_gender'] == 'male') { ?>
                                                <span class="item-icon-gender fas fa-mars"></span>
                                            <?php } else { ?>
                                                <span class="item-icon-gender fas fa-venus"></span>
                                            <?php } ?>

                                            <?php if ($plEntries[$index]['dog_fixed'] != 'intact') { ?>
                                                <span class="item-icon-fixed fas fa-notes-medical"></span>
                                            <?php } ?>

                                            <?php $age = $forms->generateAgeShortLabel($plEntries[$index]['dog_age']); ?>
                                            <span class="item-age"><?=$age?></span>
                                        </div>
                                    </div>
                                </div>
                                <img <?=$imgClassView?> src="<?=$imageUrl?>">
                            </div>

                        <?php } ?>
                    </div>
                    <div class="item-divider"><a class='menu-line' href="#"></a></div>
                    <div class='itemEditButton'>
                        <button type="button" class='itemButton' onclick="onAddItem()">
                            <span class="navMenuIcon fas fa-plus-circle"></span>
                            <span class="navMenuItemText">ADD ITEM</span>
                        </button>
                    </div>
                </li>
                <li>
                    <label for="team_id">Team Name</label>
                    <select name="team_id" class="itemSelect arrowIcon">
                        <optgroup label="Choose Team">
        <?php

            $selectedTeamId = $itemInfo['team_id'];
            $teamIdField = 'team_id';
            $teams = $users->getUsersByRole('team');
            $divElement = '';
            if ($teams['success'])
            {
                $teamUsers = $teams['users'];
                for ($i = 0; $i < count($teamUsers); $i++) {
                    $teamID = $teamUsers[$i]['id'];
                    $label = $teamUsers[$i]['name'];
                    $selected = ($teamID == $selectedTeamId) ? "selected" : "";
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
                            <span class="navMenuIcon fas fa-check-circle"></span>
                            <span class="navMenuItemText">UPDATE</span>
                        </button>
                    </div>
                    <div class='itemPromiseButton'>
                        <button type="button" onclick="openLinkPlaylist('<?=$itemInfo['token']?>')" class='itemButton promise-selected'>
                            <span class="navMenuIcon fas fa-external-link-square-alt"></span>
                            <span class="navMenuItemText">VIEW LINK</span>
                        </button>
                    </div>
                    <div class='itemRemoveButton'>
                        <button type="button" onclick="removePlaylist()" class='itemButton error'>
                            <span class="navMenuIcon fas fa-trash-alt"></span>
                            <span class="navMenuItemText">REMOVE</span>
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