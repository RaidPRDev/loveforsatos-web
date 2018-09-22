<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $HOME_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Love For Satos";

/* =====> PHP/mySQL Application Start */

$forms = new FORMS();

// setup filters
$sortIndex = $_COOKIE['filter_sort_index'];
$showIndex = $_COOKIE['filter_show_index'];

// check if we have any cookies set
if (!is_null($showIndex))
{
    $showList = explode(",", $showIndex);
    $showListIndex = $showList[0];
    $showListName = $showList[1];
}
else $showListName = "name";    // default filter

if (!is_null($sortIndex)) $sortOrder = ($sortIndex == 0) ? "ASC" : "DESC";
else $sortOrder = "ASC";    // default order

$statement = "ORDER BY ";
$statement .= $showListName;

// get all items
$allDogsList = $dogs->fetchAllItems($statement, $sortOrder);

if (!$allDogsList)
{
    $response["success"] = false;
    $response["error"] = "ERROR: Please check server.";
}

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Application Start -->
<script type="text/javascript">
    // @Override called from app.js
    function appInitialized()
    {
        trace("home initialized");

        // setup filters
        var sortIndex = parseInt(getCookie(SETTINGS_SORT_LABEL));
        var showIndex = getCookie(SETTINGS_SHOW_LABEL);
        var showList = null;
        var showListIndex = NaN;

        // check if cookiers exist, if not set defaults
        if (sortIndex == null || isNaN(sortIndex)) sortIndex = 0;
        if (showIndex == "" || showIndex == null) showIndex = '0,id';

        // parse showIndex
        var showList = showIndex.split(",");
        var showListIndex = parseInt(showList[0]);

        // set default order and show filters
        setDropSelectionMenuIndex(SETTINGS, SETTINGS_SORT_INDEX, sortIndex);
        setDropSelectionMenuIndex(SETTINGS, SETTINGS_SHOW_INDEX, showListIndex);
    }

    function onEditButtonClick(id)
    {
        window.location.replace("update_item.php?id=" + id);
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel" data-id="<?=$showListName?>">
    <div class="innerItemPanel inner-padding">

        <?php
        $list = $allDogsList["items"];
        $listLen = count($list);
        $noSatoPic = "images/nosato_pic.png";

        if ($listLen > 0)
        {
            for($i = 0; $i < $listLen; $i++)
            {
                // get photos
                if (!is_null($list[$i]["photos"]))
                {
                    $photoCount = count($list[$i]["photos"]);
                    $imgPath = 'images/photos/';
                    if ($photoCount > 0)
                        $satoPic = $imgPath.$list[$i]["photos"][0]['full_image_url'];
                    else
                        $satoPic = $noSatoPic;
                }
                else $satoPic = $noSatoPic;

                $id = $list[$i]["id"];
                $name = ucwords($list[$i]["name"]);
                $age = $forms->generateAgeLabel($list[$i]["age"]);
                $gender = ucwords($list[$i]["gender"]);
                $fixed = ucwords($list[$i]["fixed"]);
                $buttonLabel = ($isUserLogged) ? 'UPDATE' : 'VIEW';
                $editBtnParams = $id;
                ?>
                <div class='itemBox'>
                    <div class='itemPhoto'>
                        <?php
                        // check if we have promised, show icon
                        if ($list[$i]["adopted"] == "yes") { ?>
                            <div class="inner-item">
                                <div class="item-promise-photo">
                                    <span class="fas fa-star"></span>
                                </div>
                            </div>
                        <?php } ?>
                        <img class='itemImageSrc' src='<?=$satoPic?>'>
                    </div>
                    <div class='itemName'><?=$name?></div>
                    <div class='itemAge'>Age: <?=$age?></div>
                    <div class='itemGender'>Gender: <?=$gender?></div>
                    <div class='itemFixed'>Fixed: <?=$fixed?></div>

                    <div class='itemEditButton'>
                        <button class='itemButton'
                                onclick='onEditButtonClick(<?=$editBtnParams?>)'>
                            <span class="fas fa-edit"></span>
                            &nbsp;&nbsp;<?=$buttonLabel?>
                        </button>
                    </div>
                </div>
                <?php
            }
        }
        else
        {
            ?>
            <div class='itemBox'>
                <div class='itemPhoto'>
                    <img class='itemImageSrc' src='<?=$noSatoPic?>'>
                </div>
                <div class='itemName'>No items found</div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<!-- Page Content End -->

<!-- Page Content End and Footer -->
<?php include ('layout/page_end_block.php'); ?>