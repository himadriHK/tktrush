<?php
//if(!isset($_SESSION['orderid']))
//	exit();
session_start();
//$ctr=40;
//$tmp=$_SESSION['orderid'];
//var_dump($_SESSION);
//while($ctr!=0)
//{
//	sleep(5);
//	echo "sess ".$_SESSION['orderid'];
//	if(strpos($_SESSION['orderid'],"paid"))
//		break;
//	$ctr=$ctr-1;
//}

if(strpos($_SESSION['orderid'],"paid"))
{
	echo "OK";
	unset($_SESSION['orderid']);
}
else
{
	echo "FAIL"; //test
}
	
//if($ctr==0)
//{
//echo "FAIL";
////var_dump($_SESSION);
//}
//else
//{
//echo "OK";
////unset($_SESSION['orderid']);
//}
?>