<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

date_default_timezone_set('US/Eastern');

$isDebug = (isset($_GET['d'])) ? true : false;
$ROOT_PATH = $_SERVER['DOCUMENT_ROOT'];
$SERVER_NAME = $_SERVER['SERVER_NAME'];

$isLocal = (strrpos($SERVER_NAME, "localhost") === false) ? false : true;

if ($isLocal)	// Local Mode
{
	$APP_PATH = "/apps/escaperoom";
	$ROOT_PATH .= $APP_PATH;
	
	$DB_host = "";
	$DB_user = "";
	$DB_pass = "";
	$DB_name = "";
}
else
{
	$isAmazon = (strrpos($SERVER_NAME, "amazonaws") === false) ? false : true;
	$isRaid = (strrpos($SERVER_NAME, "raidpr") === false) ? false : true;
	
	if ($isAmazon)
	{
		$APP_PATH = "/";
		$ROOT_PATH .= $APP_PATH;

		$DB_host = "";
		$DB_user = "";
		$DB_pass = "";
		$DB_name = "";
	}
	else if ($isRaid)
	{
		$APP_PATH = "/clients/satos";
		$ROOT_PATH .= $APP_PATH;

		$DB_host = "";
		$DB_user = "";
		$DB_pass = "";
		$DB_name = "";
	}
	else
	{
		$APP_PATH = "/";
		$ROOT_PATH .= $APP_PATH;

		$DB_host = "";
		$DB_user = "";
		$DB_pass = "";
		$DB_name = "";
	}
}

$APP_FULL_PATH = "http://" . $SERVER_NAME . $APP_PATH;

if ($isDebug)
{
	highlight_string("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n");
	highlight_string("SERVER_NAME: " . $SERVER_NAME . "\n\n");
}

include_once $ROOT_PATH . '/api/class.users.php';
include_once $ROOT_PATH . '/api/class.dogs.php';

try
{
	$DB_con = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
	$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Users Object
    $users = new USERS($DB_con, 'users');

	// Create Dogs Object
	$dogs = new DOGS($DB_con);
}
catch(PDOException $e)
{
	$serverResponse["success"] = false; 
	$serverResponse["error"] = $e->getMessage(); 
	
	if ($isDebug) highlight_string( var_export($serverResponse, true));
	else echo json_encode($serverResponse);
	
	exit;
}
?>