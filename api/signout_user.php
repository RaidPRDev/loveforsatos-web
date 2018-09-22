<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/15/2018
 * Time: 9:54 AM
 */

include_once 'config/dbconfig.php';

session_start();

if (isset($_POST['signing_out']))
{
   if ($_POST['signing_out'] = 1)
    {
        session_destroy();

        echo json_encode(array("success"=>true));
    }
    else
    {
        echo json_encode(array("success"=>false, "error"=>"Missing data"));
    }
}
else
{
    echo json_encode(array("success"=>false, "error"=>"Missing data"));
}
?>