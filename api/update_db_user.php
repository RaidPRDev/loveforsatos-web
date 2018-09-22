<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/15/2018
 * Time: 9:54 AM
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once 'config/dbconfig.php';

$isRoleAdmin = false;
$userInfo = array();

if (!isset($_POST['id']))
{
    sendError("Missing data");
}

if (!isset($_POST['role']))
{
    sendError("Missing data");
}
else $isRoleAdmin = ($_POST['role'] == 'admin') ? true : false;

if (!isset($_POST['name']))
{
    sendError("Missing name");
}

if (!isset($_POST['email']))
{
    sendError("Missing email");
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
{
    sendErrorWithCode('Invalid email format', "email");
}

if (!isset($_POST['username']) && $isRoleAdmin)
{
    sendError("Missing username");
}
else
{
    if ($isRoleAdmin)
    {
        $userInfo['username'] = $_POST['username'];
    }
    else $userInfo['username'] = MD5(uniqid());
}

if (!isset($_POST['password']) && $isRoleAdmin)
{
    sendError("Missing password");
}
else
{
    if ($isRoleAdmin) $userInfo['password'] = $_POST['password'];
    else $userInfo['password'] = MD5(uniqid());
}

$userInfo['id'] = $_POST['id'];
$userInfo['role'] = $_POST['role'];
$userInfo['name'] = $_POST['name'];
$userInfo['email'] = $_POST['email'];
$userInfo['updated'] = date("Y-m-d H:i:s");

$register = $users->update($userInfo);
if ($register['success'])
{
    $data = array(
        "success" => true,
        "error" => "",
        "errorCode" => 0
    );

    // silently quit and send data
    die(json_encode($data));

}
else
{
    sendErrorWithCode("Could not register", $register['error']);
}

// ==================================================================

// convenience functions
function sendSuccess()
{
    $data = array(
        "success" => true,
        "error" => ''
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendSuccessWithID($itemID)
{
    $data = array(
        "success" => true,
        "error" => '',
        "itemID" => $itemID
    );

    // silently quit and send data
    die(json_encode($data));
}

function sendErrorWithCode($msg, $error)
{
    $data = array(
        "success" => false,
        "error" => $msg,
        "errorCode" => $error
    );

    // silently quit and send data

    die(json_encode($data));
}

function sendError($msg)
{
    $data = array(
        "success" => false,
        "error" => $msg
    );

    // silently quit and send data
    die(json_encode($data));
}

?>