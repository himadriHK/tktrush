<?php 
@session_start();
header('Cache-Control: no-cache');
header('Pragma: no-cache');
//if( !isset($_SESSION["softix_token"]) ){
//require_once 'softix-token.php';
//}
//include('Connections/eventscon.php'); 
//require_once('dtcm_api.php');
include("functions.php"); 
include("config.php"); 
require_once('model_function.php');
if(empty($_SESSION['Customer']) && empty($_SESSION['PP_UserId']))
{
	$_SESSION['referer']=$_SERVER['REQUEST_URI'];
	header("location: signin.php");exit;
}
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
$theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
switch ($theType) {
case "text":
$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
break;    
case "long":
case "int":
$theValue = ($theValue != "") ? intval($theValue) : "0";
break;
case "double":
$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
break;
case "date":
$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
break;
case "defined":
$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
break;
}
return $theValue;
}
function getDtcmPriceVal($id,$ticket_prices){
	foreach ($ticket_prices['TicketPrices']['Prices'] as $price){
		//var_dump($price);
		if($price['PriceCategoryCode'].$price['PriceTypeCode']==$id) {
			return $price;
		}
	}
	return '';
}
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
if(isset($_SESSION['Customer']['cust_id']) && !isset($_SESSION['Customer']['type']) && @$_SESSION['Customer']['type']!='partner'){
	//echo "HEREEEEEEEEEEE";
    $cust_sql = "UPDATE customers set city='" . $_POST['city'] . "', mobile='" . $_POST['mobile'] . "', fname='" . $_POST['fname'] . "', lname='" . $_POST['lname'] . "', address='" . $_POST['address'] . "' where cust_id='" . $_SESSION['Customer']['cust_id'] . "'";
    mysql_query($cust_sql);
    $cust_sql = "select * from customers where cust_id=" . $_SESSION['Customer']['cust_id'];
    $restult = mysql_query($cust_sql);
    $customers = mysql_fetch_assoc($restult);
    unset($_SESSION['Customer']);
    $_SESSION['Customer'] = $customers;
}
##End of update customer data
$eventid = $_POST['eid'];
//$query_eventRs = sprintf("SELECT events.* FROM events WHERE events.tid=%s", $eventid);
//
//$eventRs = mysql_query($query_eventRs, $eventscon) or die(mysql_error());
$eid=filter_var($_POST['eid'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$row_eventRs=getEventDetails($eid);
$_SESSION['custid']=$_SESSION['Customer']['cust_id'];
//if($row_eventRs['voucher_image']!='')
//{
//$uniquecode=uniqid();
//$verifycode=getRandomCode();
//}
//else
//{
//	$uniquecode='';
//	$verifycode='';
//}
$pids=array();
$tot_tickets=0;
$tot_ctickets=0;
$total_price=0;
if(true){//$row_eventRs['dtcm_approved']=='Yes' && $row_eventRs['dtcm_code']!='' ) {
	$eventcode = $row_eventRs['dtcm_code'];
	$_SESSION['dtcm_event']=$row_eventRs['dtcm_approved'];
	$_SESSION['dtcm_code']=$row_eventRs['dtcm_code'];
	$_SESSION['eid']=$eid;
$prices = getEventPrices($eid);
$_SESSION['perf_code']=$eventcode;
$ticket_prices = $prices;
}
$basket_info=array();
$b=1;
$pcats=getEventPrices($eid);
//var_dump($_POST);
foreach($_POST['tickets'] as $key=>$val)
	{
		$ticket_type=substr($key,-1);
		if($ticket_type=='A')
		$tot_tickets=$tot_tickets+$val;
		if($ticket_type=='C')
		$tot_ctickets=$tot_ctickets+$val;
		if($val){
			$pids[$key]=substr($key,0,-1);
			$price_values = getDtcmPriceVal($key,$ticket_prices);
			//var_dump($ticket_prices['TicketPrices']['Prices']);
			//var_dump($pids);
            $total_price=$total_price+($price_values['PriceNet']/100*$val);
		}
	}
	if($_POST['extra_services'])
	{
		global $database;
		$service_price=$database->query("select price from event_services where id=:id",[":id"=>$_POST['extra_services']])->fetchAll();
		//var_dump($service_price);
		$total_price=$total_price+$service_price[0]["price"];
		$_SESSION['extra_service_fees']=$service_price[0]["price"];
	}
	//$total_price=$total_price+($total_price*0.05)+2;
$pids=implode(",",$pids);
//var_dump($pids);
//var_dump($total_price);
//var_dump($_POST);
if(isset($_POST['category'])){
$_SESSION['catname']=$_POST['catname'.$_POST['category']];
$pcats=$_POST['category'];
$prices=$_POST['price'];
$tickets=$_POST['tickets'];
 $fee_obj = new stdClass();
 $blank_obj=new stdClass();
 $fee_obj->Type = "5";
 $fee_obj->Code = 'W';
	//foreach($pcats as $catcode => $cat){
		$basket_info['channel']='W';
		$basket_info['seller']=$dtcm_->seller_code;
		$basket_info['performancecode']=$eventcode;
		$basket_info['area']='@'.$pcats;
		$basket_info['autoReduce']=false;
		$basket_info['holdcode']='';
		foreach ($prices as $pricecode=>$cost){
		if($pricecode[0]!=$pcats) continue;
		if($_POST['tickets'][$pricecode]==0) continue;
			$basket_info['demand'][]=array(
								'priceTypeCode'=>substr($pricecode,1),
								'Admits'=>$_POST['tickets'][$pricecode],
								'quantity'=>$_POST['tickets'][$pricecode],
								'offerCode'=>'',
								'qualifierCode'=>'',
								'entitlement'=>'',
								'Customer'=>$blank_obj);
		}
		$basket_info['Fees']=array($fee_obj);
	//}
}
//var_dump($pcats);
if(!empty($basket_info)){
	$args=array($basket_info,'');
	//var_dump($args[0]['demand']);
	if($_SESSION['dtcm_event']=='Yes'){
	$basket_details = json_decode($dtcm_->post_demands_to_dtcm($args),true);
	//var_dump(print_r($basket_details));
	$basket_id = $basket_details['Id'];
	$_SESSION['dtcm_order_id']=$basket_id;
	$order_id=bookTicketForEvent($_SESSION['custid'],$eid,$pids,"",-1,"","",0,$total_price,date('Y-m-d'),$row_eventRs['date_start'],'cc',$tot_tickets,$tot_ctickets,$basket_id,$_POST['extra_services']);
	$_SESSION['orderid']=$order_id;
	$_SESSION['total']=$total_price;
	
	}
	elseif($_SESSION['dtcm_event']=='No')
	{
		$order_id=bookTicketForEvent($_SESSION['custid'],$eid,$pids,"",-1,"","",0,$total_price,date('Y-m-d'),$row_eventRs['date_start'],'cc',$tot_tickets,$tot_ctickets,"-1",$_POST['extra_services']);
		$_SESSION['orderid']=$order_id;
		$_SESSION['total']=$total_price;
		//bookTicketForEvent($cust_id,$eventcode,$pid,$partner_id,$order_number,$transaction_code,$selected_seats,$charges,$ticket_price,$order_date,$event_date,$payment_type,$tickets,$ctickets,$basket_id,$extra_services)
	}
}
}
//} else {
//if($_POST['tickets'])
//	foreach($_POST['tickets'] as $key=>$val)
//	{
//		$tot_tickets=$tot_tickets+$val;
//		if($val){
//			$pids[$key]=$key;
//			$sql="select * from event_prices where pid=$key";
//			$price_query = mysql_query($sql, $eventscon) or die(mysql_error());
//			$prive_values = mysql_fetch_assoc($price_query);
//			$total_price=$total_price+($prive_values['price']*$val);
//		}
//	}
////if($_POST['eid'] != 161){
//	foreach($_POST['ctickets'] as $key=>$val)
//	{
//		$tot_ctickets=$tot_ctickets+$val;
//		if($val){
//			$pids[$key]=$key;
//			$sql="select * from event_prices where pid=$key";
//			$price_query = mysql_query($sql, $eventscon) or die(mysql_error());
//			$prive_values = mysql_fetch_assoc($price_query);
//                        if($_POST['eid'] == 161){
//$total_price = $total_price+($ano_price*$val);
//                        }else{
//			$total_price=$total_price+($prive_values['cprice']*$val);
//                        }
//		}
//	}
////}
//$total_price=$total_price+$_POST['charges']+($total_price*0.05)+2;
//$pids=implode(",",$pids);
//$seat_arr['tickets']=$_POST['tickets'];
//$seat_arr['ctickets']=$_POST['ctickets'];
//$selected_seats=serialize($seat_arr);
//$sessiondate = $_POST["sessiondate"];
//$order_number=mt_rand(100000, 999999);
//if($sessiondate=="Ongoing"){ $sessiondate = date("Y-m-d"); }
//	$ccomp = $_POST['comp_tickets'];
//	$zcomp = $_POST['compz_tickets'];
//$insertSQL = sprintf("INSERT INTO ticket_orders (order_number,cust_id, tid,selected_seats, pid, ticket_price,charges,order_date, event_date, payment_type, tickets, ctickets,uniquecode,verifycode,ccomp,zcomp) VALUES (%s,%s, %s,%s, %s, %s, %s, %s, %s, %s, %s, %s,%s,%s,%s,%s)",
//GetSQLValueString($order_number, "text"),
//GetSQLValueString($_SESSION['custid'], "int"),
//GetSQLValueString($_POST['eid'], "int"),
//GetSQLValueString($selected_seats, "text"),
//GetSQLValueString($pids, "text"),
//GetSQLValueString($total_price, "text"),
//GetSQLValueString($_POST['charges'], "text"),
//GetSQLValueString(date("Y-m-d"), "date"),
//GetSQLValueString($sessiondate, "date"),
//GetSQLValueString($_POST['paytype'], "text"),
//GetSQLValueString($tot_tickets, "int"),
//GetSQLValueString($tot_ctickets, "int"),
//GetSQLValueString($uniquecode, "text"),
//GetSQLValueString($verifycode, "text"),
//GetSQLValueString($ccomp, "text"),
//GetSQLValueString($zcomp, "text"));
//$Result1 = mysql_query($insertSQL, $eventscon);
//if($Result1){
//$_SESSION['orderid'] = mysql_insert_id();
//}
//}