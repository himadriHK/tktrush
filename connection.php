<?php

session_start();

if($lan)

$lang = $lan;

@mysql_connect("localhost","root","root");

//@mysql_select_db("dubaideal_com");

@mysql_select_db("tktrushc_dbase");

if($lang == "en"){

include_once("languages/english.php");

define('SITE_LANGUAGE', '1');



}

else 

if($lang == "ar"){

define('SITE_LANGUAGE', '2');

include_once("languages/arabic.php");



}

else{

define('SITE_LANGUAGE', '1');

include_once("languages/english.php");

$lang="en";

}

session_register('lang');?>

<html dir="<?php echo HTMLVAR?>">
