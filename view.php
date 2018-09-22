<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/playlist/class.playlist.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $VIEW_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Love For Satos";
$pageSubTitle = "Hello";

/* =====> PHP/mySQL Application Start */
// setup filters
$sortIndex = null;
if (isset($_COOKIE['filter_sort_index']))
    $sortIndex = $_COOKIE['filter_sort_index'];

$showIndex = null;
if (isset($_COOKIE['filter_show_index']))
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

$forms = new FORMS();

$allDogsList = array();
$dogList = array();
$playlistInfo = array();
$team_id = -1;

if ( isset($_GET['tid']) )
{
    // validate playlist token
    $playlist = new PLAYLIST($DB_con, 'playlist');

    $resultData = $playlist->validateToken($_GET['tid']);

    if ($resultData["success"])
    {
        $_SESSION['token'] = $_GET['tid'];
        //highlight_string(json_encode($resultData));
        //echo '<br><br>';

        $playlistInfo = $resultData['playlist'];
        $resultData = $playlist->getAllPlaylistEntriesWithPromiseByID($playlistInfo['id']);
        if ($resultData["success"])
        {
            //highlight_string(json_encode($resultData));
            //echo '<br><br>';

            $team_id = $resultData["playlist"][0]['team_id'];
            $pageSubTitle = $resultData["playlist"][0]['team_name'];
            if (!empty($resultData["playlist"][0]['description']))
            {
                $pageSubTitle .= ' ( ' . $resultData["playlist"][0]['description'] . ' )';
            }

            if (count($resultData["playlist"]) > 0)
            {
                for ($index = 0; $index < count($resultData["playlist"]); $index++)
                {
                    $dogList[] = $resultData["playlist"][$index]['dog_id'];
                }

                //highlight_string(json_encode($dogList));
                //echo '<br><br>';

                $resultData = $dogs->fetchAllItemsInIds($dogList, $statement, $sortOrder);
                if ($resultData["success"])
                {
                    //highlight_string(json_encode($resultData));
                    //echo '<br><br>';

                    $allDogsList = $resultData['items'];

                    // highlight_string(json_encode($dogList));
                    //echo '<br><br>';
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

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Promise Item  -->
<script type="text/javascript" src="js/components/promise.js?t=<?=MD5(uniqid())?>"></script>

<!-- Application Start -->
<script type="text/javascript">
    // @Override called from app.js

    function appInitialized()
    {
        trace("home initialized");

        viewToken = '<?=$_SESSION['token']?>';

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
        setDropSelectionMenuIndex(VIEW_SETTINGS, SETTINGS_SORT_INDEX, sortIndex);
        setDropSelectionMenuIndex(VIEW_SETTINGS, SETTINGS_SHOW_INDEX, showListIndex);
    }

    function onViewButtonClick(id, playID)
    {
        window.location.replace("view_item.php?id=" + id + "&pid=" + playID + "&tid=" + viewToken);
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel" data-id="<?=$showListName?>">
    <div class="innerItemPanel inner-padding">

        <?php
        $list = $allDogsList;
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

                $name = ucwords($list[$i]["name"]);
                $age = $forms->generateAgeLabel($list[$i]["age"]);
                $gender = ucwords($list[$i]["gender"]);
                $fixed = ucwords($list[$i]["fixed"]);
                $dog_id = $allDogsList[$i]['id'];
                $viewBtnParams = $dog_id . ", " . $playlistInfo['id'];
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
                                onclick='onViewButtonClick(<?=$viewBtnParams?>)'>
                            <span class="fas fa-id-card"></span>
                            &nbsp;&nbsp;VIEW
                        </button>
                    </div>
                    <?=$forms->generatePromiseButton($allDogsList[$i], $playlistInfo)?>
                </div>
                <?php
            }
        }
        else    // there are no items found
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