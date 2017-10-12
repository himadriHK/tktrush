<<<<<<< HEAD
<?php
ini_set('display_errors', 1);
include("Medoo.php");
use Medoo\Medoo;

$config = array();
$config['SITE_NAME'] = "Ticket Master";
$config['ADMIN_RESULTS_PER_PAGE'] = 10;
$DBTYPE = 'mysql';
$DBHOST = "localhost";//'10.168.1.47';
$DBNAME = 'tktrushc_dbase';
$DBUSER = 'tktrushc_user';
$DBPASSWORD = 'LP0q221p';

$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => $DBNAME,
    'server' => $DBHOST,
    'username' => $DBUSER,
    'password' => $DBPASSWORD
]);
global $database;

$conn = mysqli_connect($DBHOST,$DBUSER,$DBPASSWORD,$DBNAME);
if(mysqli_connect_errno()){
	echo "Could not connect to mysql! Please check your database settings! " . mysqli_connect_error();
}
$config['BASE_DIR'] = dirname(dirname(__FILE__));
require_once('include/security.php');
require_once(dirname($config['BASE_DIR']). '/config.php');
session_start();
=======
<?php
ini_set('display_errors', 1);
include("Medoo.php");
use Medoo\Medoo;

$config = array();
$config['SITE_NAME'] = "Ticket Master";
$config['ADMIN_RESULTS_PER_PAGE'] = 10;
$DBTYPE = 'mysql';
$DBHOST = "localhost";//'10.168.1.47';
$DBNAME = 'tktrushc_dbase';
$DBUSER = 'tktrushc_user';
$DBPASSWORD = 'LP0q221p';

$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => $DBNAME,
    'server' => $DBHOST,
    'username' => $DBUSER,
    'password' => $DBPASSWORD
]);
global $database;

$conn = mysqli_connect($DBHOST,$DBUSER,$DBPASSWORD,$DBNAME);
if(mysqli_connect_errno()){
	echo "Could not connect to mysql! Please check your database settings! " . mysqli_connect_error();
}
$config['BASE_DIR'] = dirname(dirname(__FILE__));
require_once('include/security.php');
require_once(dirname($config['BASE_DIR']). '/config.php');
session_start();
>>>>>>> refs/remotes/origin/master
?>