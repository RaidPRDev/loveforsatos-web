<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/9/2018
 * Time: 4:16 AM
 */

$pageKeywords = "satos, dogs, rescue";
$pageAuthor = "Rafael Emmanuelli";
$pageFavIcon = "../favicon.ico";
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <title><?=$pageTitle?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="description" content="<?=$pageDescription?>" />
    <meta name="keywords" content="<?=$pageKeywords?>" />
    <meta name="author" content="<?=$pageAuthor?>" />
    <link rel="shortcut icon" href="<?=$pageFavIcon?>">

    <!-- Roboto Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="css/fontawesome.solid.min.css" />
    <link rel="stylesheet" type="text/css" href="css/fontawesome.min.css" />

    <!-- Sweet Alert Styles -->
    <link rel="stylesheet" type="text/css" href="css/sweetalert.css" />
    <script type="text/javascript" src="js/lib/sweetalert.js"></script>

    <!-- Page Layout Styles -->
    <link rel="stylesheet" type="text/css" href="css/layout.css?t=<?=MD5(uniqid())?>" />

    <!-- Form Styles -->
    <link rel="stylesheet" type="text/css" href="css/forms.css?t=<?=MD5(uniqid())?>" />

    <!-- Form File Input Styles -->
    <link rel="stylesheet" type="text/css" href="css/input.css?t=<?=MD5(uniqid())?>" />

    <!-- JQuery 3.3.1 -->
    <script type="text/javascript" src="js/lib/jquery-3.3.1.js"></script>

    <!-- Load Image -->
    <script type="text/javascript" src="js/components/loadimage/load-image.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-scale.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-meta.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-fetch.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-exif.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-exif-map.js"></script>
    <script type="text/javascript" src="js/components/loadimage/load-image-orientation.js"></script>

    <!-- Slick -->
    <link rel="stylesheet" type="text/css" href="js/components/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="js/components/slick/slick-theme.css"/>
    <link rel="stylesheet" type="text/css" href="css/slick-satos-theme.css?t=<?=MD5(uniqid())?>"/>
    <script type="text/javascript" src="js/components/slick/slick.min.js"></script>

    <!-- App -->
    <script type="text/javascript" src="js/app.js?t=<?=MD5(uniqid())?>"></script>
    <script type="text/javascript" src="js/components/uploadtools.js?t=<?=MD5(uniqid())?>"></script>
    <script type="text/javascript" src="js/components/dropdown.js?t=<?=MD5(uniqid())?>"></script>
    <script type="text/javascript" src="js/navigation.js?t=<?=MD5(uniqid())?>"></script>
</head>
