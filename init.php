<?php

include("./admin/connect.php");

//routes

$tpl = "includes/templates/";  // Templates Directory
$lang = 'includes/languages/';
$func = 'includes/functions/';
$css = 'layout/css/';           // css dir
$js = 'layout/js/';             // j dir

// include the important files
include $lang . 'en.php';
include $func . 'functions.php';
include $tpl . 'header.php';



//
    include $tpl . 'navbar.php';


include $tpl . 'footer.php';
