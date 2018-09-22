<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/16/2018
 * Time: 8:55 PM
 */

// Initialize session
session_start();

// Screen constants
$HOME_SCREEN = 0;
$ADD_ITEM_SCREEN = 1;
$EDIT_ITEM_SCREEN = 2;
$VIEW_ITEM_SCREEN = 3;
$CREATE_LIST_SCREEN = 4;
$USERS_SCREEN = 5;
$ADD_USER_SCREEN = 6;
$UPDATE_USER_SCREEN = 7;
$PLAYLIST_SCREEN = 8;
$ADD_PLAYLIST_SCREEN = 9;
$UPDATE_PLAYLIST_SCREEN = 10;
$VIEW_SCREEN = 11;

include_once $ROOT_PATH . '/api/config/class.saved_data.php';

$savedData = new SAVEDATA();
$savedData->data['ROOT_PATH'] = $ROOT_PATH;
$savedData->data['APP_FULL_PATH'] = $APP_FULL_PATH;
$savedData->data['UPLOAD_FULL_PATH'] = $APP_FULL_PATH . "/images/photos/";
$savedData->data['UPLOAD_RELATIVE_PATH'] = "../images/photos/";
$savedData->data['UPLOAD_ABSOLUTE_PATH'] = __DIR__ . "/" . $savedData->data['UPLOAD_RELATIVE_PATH'];
$savedData->data['ADMIN_MODE'] = false;

$isUserLogged = 0;

// "Cookies are disabled.";
$GLOBALS['SAVEDATA'] = $savedData;

if (isset($_SESSION["user_logged"]))
{
    if ($_SESSION["user_logged"])
    {
        $GLOBALS['SAVEDATA']->data['ADMIN_MODE'] = true;
        $isUserLogged = 1;
        /*
         *  $_SESSION['user_logged'] = true;
            $_SESSION['user_id'] = $signin['userInfo']->id;
            $_SESSION['username'] = $signin['userInfo']->username;
         *
         */
    }
    else
    {
        $GLOBALS['SAVEDATA']->data['ADMIN_MODE'] = false;
        $isUserLogged = 0;
    }
}
?>