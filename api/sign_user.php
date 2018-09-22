<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/15/2018
 * Time: 9:54 AM
 */

include_once 'config/dbconfig.php';

if (isset($_POST['username']) && isset($_POST['password']))
{
    $userInfo['username'] = strtolower(trim($_POST['username']));
    $userInfo['password'] = trim($_POST['password']);

    if (isset($_POST['pin']))
        $userInfo['pin'] = trim($_POST['pin']);

    $signin = $users->signIn($userInfo);

    //unset($signin['userInfo']['password']);
    //$signin['userInfo']['password'] = null;

    // echo "password: " . $signin['userInfo']['password'];

    if ($signin['success'])
    {
        session_destroy();
        session_start();

        $_SESSION['user_logged'] = true;
        $_SESSION['user_id'] = $signin['userInfo']['id'];
        $_SESSION['username'] = $signin['userInfo']['username'];

        echo json_encode($signin);
    }
    else
    {
        echo json_encode($signin);
    }
}
else
{
    echo json_encode(array(
        "success"=>false,
        "username"=>$_POST['username'],
        "password"=>$_POST['password'],
        "error"=>"Missing data"
    ));
}
?>