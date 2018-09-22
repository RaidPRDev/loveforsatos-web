<?php
$serverUrl = $_SERVER['DOCUMENT_ROOT'] . '/clients/satos';

include_once $serverUrl . '/api/config/dbconfig.php';
include_once $serverUrl . '/api/config/settings.php';

header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Accept, X-Access-Token, X-Application-Name, X-Request-Sent-Time');

// session_cache_limiter('nocache');
// $cache_limiter = session_cache_limiter();

/*spl_autoload_register('autoloader');
function autoloader($classname) {
    include_once 'path/to/class.files/' . $classname . '.php';
}*/
?>