<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/9/2018
 * Time: 4:16 AM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<body>
<div class="mainContainer">
    <section>
        <div class="itemContainer" >
            <div class="innerItemContainer">
<?php

// =====> screen constants -> config/settings.php

/* =====> Select Navigation Bar */
$navigation_header_bar;

switch ($currScreen)
{
    case $HOME_SCREEN:
        $navigation_header_bar = 'home_navigation_header.php';
        break;

    case $VIEW_SCREEN:
        $navigation_header_bar = 'view_navigation_header.php';
        break;

    case $ADD_ITEM_SCREEN:
        $navigation_header_bar = 'additem_navigation_header.php';
        break;

    case $EDIT_ITEM_SCREEN:
        $navigation_header_bar = 'edititem_navigation_header.php';
        break;

    case $VIEW_ITEM_SCREEN:
        $navigation_header_bar = 'viewitem_navigation_header.php';
        break;

    case $CREATE_LIST_SCREEN:
        $navigation_header_bar = 'home_navigation_header.php';
        break;

    case $USERS_SCREEN:
        $navigation_header_bar = 'users_navigation_header.php';
        break;

    case $ADD_USER_SCREEN:
        $navigation_header_bar = 'adduser_navigation_header.php';
        break;

    case $UPDATE_USER_SCREEN:
        $navigation_header_bar = 'updateuser_navigation_header.php';
        break;

    case $PLAYLIST_SCREEN:
        $navigation_header_bar = 'createlist_navigation_header.php';
        break;

    case $ADD_PLAYLIST_SCREEN:
        $navigation_header_bar = 'addlist_navigation_header.php';
        break;

    case $UPDATE_PLAYLIST_SCREEN:
        $navigation_header_bar = 'updatelist_navigation_header.php';
        break;
}
/* =====> Navigation Bar */
include('pageheaders/' . $navigation_header_bar);

/* =====> User JS Connections, Status */
include('page_user_script_start.php');

?>