<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
ob_start();
session_start();
$hostname_eventscon = "10.168.1.47";
$database_eventscon = "tktrushc_dbase";
$username_eventscon = "tktrushc_user";
$password_eventscon = "LP0q221p";
$eventscon = mysql_pconnect($hostname_eventscon, $username_eventscon, $password_eventscon) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database_eventscon, $eventscon);
require_once(dirname(dirname(__FILE__)).'/config.php');
?>