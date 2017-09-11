<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
include_once("Medoo.php");
include_once('/dtcm_api/api_test.php');
include_once('/dtcm_api/dtcm_api.php');
ob_start();
use Medoo\Medoo;
@session_start();
$hostname_eventscon = "localhost";//"10.168.1.47";
$database_eventscon = "tktrushc_dbase";
$username_eventscon = "tktrushc_user";
$password_eventscon = "LP0q221p";
$eventscon = @mysql_pconnect($hostname_eventscon, $username_eventscon, $password_eventscon) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database_eventscon, $eventscon);
require_once(dirname(dirname(__FILE__)).'/config.php');

 
// Initialize
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => $database_eventscon,
    'server' => $hostname_eventscon,
    'username' => $username_eventscon,
    'password' => $password_eventscon
]);
global $database;

if($database->error())
	echo ">>";

function getEventDetails($event_id)
{
	global $database;
	$data=$database->query('select e.* from events e where e.tid=:tid',[':tid'=>$event_id])->fetchAll();
	if(!$database->error()||$database->error()[1]==null)
	{
		//var_dump($data);
		return $data[0];
	}
	else
		var_dump($database->error());
}

function getEventAvailabilities($event_id)
{
	global $database;
	global $dtcm_;
	$data=$database->query('select e.dtcm_approved,e.dtcm_code dtcm_code, p.*,s.* from events e,event_prices p,seats s where  e.tid=p.tid and p.stand=s.id and e.tid=:tid',[':tid'=>$event_id])->fetchAll();
	
	if(!$database->error()||$database->error()[1]==null)
	{
		if($data[0]['dtcm_approved']=='Yes' and $data[0]['dtcm_code']!='')
			return json_decode($dtcm_->get_performance_availabilities($data[0]['dtcm_code']),true);
		elseif($data[0]['dtcm_approved']=='No')
		{
			$PriceCategories=array();
			$i=1;
			foreach($data as $avail)
			{
				array_push($PriceCategories,array("Availability"=>array("SoldOut"=>false,"StatusCode"=>"Ok"),"PriceCategoryId"=>$avail["pid"],"PriceCategoryCode"=>$avail["pid"],"PriceCategoryName"=>$avail["seat_type"]));
				//$i++;
			}
			
			return array("PriceCategories"=>$PriceCategories);
		}
	}
	else
		return $database->error();
}

function getEventPrices($event_id)
{
	global $database;
	global $dtcm_;
	$data=$database->query('select e.dtcm_approved,e.dtcm_code dtcm_code, p.*,s.* from events e,event_prices p,seats s where  e.tid=p.tid and p.stand=s.id and e.tid=:tid',[':tid'=>$event_id])->fetchAll();
	if(!$database->error()||$database->error()[1]==null)
	{
		//var_dump($data[0]);
		if($data[0]['dtcm_approved']=='Yes' and $data[0]['dtcm_code']!='')
			return json_decode($dtcm_->get_performance_prices($data[0]['dtcm_code']),true);
		elseif($data[0]['dtcm_approved']=='No')
		{
			$PriceCategories=array();
			$PriceTypes=array();
			$Prices=array();
			foreach($data as $price)
			{
				array_push($PriceCategories,array('PriceCategoryId'=>$price['pid'],'PriceCategoryCode'=>$price['pid'],'PriceCategoryName'=>$price['seat_type']));
				
				array_push($PriceTypes,array('PriceTypeId'=>$price['pid'],'PriceTypeCode'=>'A','PriceTypeName'=>'ADULT','PriceTypeDescription'=>'Adult','AdmitCount'=>1));
				
				array_push($PriceTypes,array('PriceTypeId'=>'-'.$price['pid'],'PriceTypeCode'=>'C','PriceTypeName'=>'CHILD','PriceTypeDescription'=>'Child','AdmitCount'=>1));
				
				array_push($Prices,array('PriceId'=>$price['pid'],'PriceTypeId'=>$price['pid'],'PriceCategoryId'=>$price['pid'],'PriceCategoryCode'=>$price['pid'],'PriceTypeId'=>$price['pid'],'PriceTypeCode'=>'A','PriceNet'=>$price['price']*100));
				
				array_push($Prices,array('PriceId'=>"-".$price['pid'],'PriceTypeId'=>'-'.$price['pid'],'PriceCategoryId'=>'-'.$price['pid'],'PriceCategoryCode'=>$price['pid'],'PriceTypeId'=>'-'.$price['pid'],'PriceTypeCode'=>'C','PriceNet'=>$price['cprice']*100));
				
				$tmp= array('PriceCategories'=>$PriceCategories,'PriceTypes'=>$PriceTypes,'TicketPrices'=>array('Prices'=>$Prices));
				
				//var_dump($tmp);
			}
			//var_dump($PriceCategories);
			return $tmp;
		}
		
		
	}
	else
		 return $database->error();
}

function bookTicketForEvent($cust_id,$eventcode,$pid,$partner_id,$order_number,$transaction_code,$selected_seats,$charges,$ticket_price,$order_date,$event_date,$payment_type,$tickets,$ctickets,$basket_id,$extra_services)
{
	global $database;
	$database->insert("ticket_orders",array("cust_id"=>$cust_id,"tid"=>$eventcode,"pid"=>$pid,"partner_id"=>$partner_id,"order_number"=>$order_number,"payment_status"=>"unpaid","transaction_code"=>$transaction_code,"selected_seats"=>$selected_seats,"charges"=>$charges,"ticket_price"=>$ticket_price,"order_date"=>$order_date,"event_date"=>$event_date,"payment_type"=>$payment_type,"tickets"=>$tickets,"ctickets"=>$ctickets,"ccapproved"=>'No',"uniquecode"=>"","verifycode"=>"","downloaded"=>0,"basket_id"=>$basket_id,"extra_services"=>$extra_services,"customer_info"=>"","ccomp"=>"","zcomp"=>"","c_basket_id"=>""));
	if(!$database->error()||$database->error()[1]==null)
	{
		return $database->id();
	}
	else
		return $database->error();
}

function updatePaymentStatus($order_id,$status)
{
	global $database;
	$database->update("ticket_orders",["payment_status"=>$status,"ccapproved"=>'Yes'],["oid"=>$order_id]);
	if(!$database->error()||$database->error()[1]==null)
	{
		return true;
	}
	else
		return $database->error();
}

function updateDownload($order_id)
{
	global $database;
	$database->update("ticket_orders",["downloaded"=>1],["oid"=>$order_id]);
	if(!$database->error()||$database->error()[1]==null)
	{
		return true;
	}
	else
		return $database->error();
}

function updateOrder($order_id,$col,$data)
{
	global $database;
	$database->update("ticket_orders",[$col=>$data],["oid"=>$order_id]);
	if(!$database->error()||$database->error()[1]==null)
	{
		return true;
	}
	else
		return $database->error();
}

function getOrderDetails($order_id,$dtcm)
{
	if($dtcm=="Yes")
	{
		return json_decode($dtcm_->get_dtcm_order($order_id,true));
	}
	elseif($dtcm=="No")
	{
		global $database;
		$data=$database->query("select t.* from ticket_orders t where oid=:oid",["oid"=>$order_id])->fetchAll();
		//var_dump($data);
		if($data)
		$data=$data[0];
		$price_data=$database->query('select p.* from event_prices p where p.pid=:pid',[':pid'=>$data["pid"]])->fetchAll();
		//var_dump($price_data);
		if($price_data)
		$price_data=$price_data[0];
		$OrderLineItems=array();
		if($data&&$price_data){
		if($data["payment_status"]=="paid")
		{
			for($i=1;$i<=$data["tickets"];$i++)
			{
				array_push($OrderLineItems,array("Id"=>$i,"PriceCategoryCode"=>$data["pid"],"PriceTypeCode"=>"A","PriceTypeName"=>"Adult","Barcode"=>date('YmdH').$data["oid"].$i,"Price"=>array("Net"=>$price_data["price"]*100),"Seat"=>array("Section"=>"","Row"=>"","Seats"=>"","RzStr"=>"Manual")));
			}
			for($i=1;$i<=$data["ctickets"];$i++)
			{
				array_push($OrderLineItems,array("Id"=>$i,"PriceCategoryCode"=>"-".$data["pid"],"PriceTypeCode"=>"C","PriceTypeName"=>"Child","Barcode"=>date('YmdH').$data["oid"].$i,"Price"=>array("Net"=>$price_data["cprice"]*100),"Seat"=>array("Section"=>"","Row"=>"","Seats"=>"","RzStr"=>"Manual")));
			}
		}}
		if(!$database->error()||$database->error()[1]==null)
		{
		return array("Id"=>$order_id,"OrderItems"=>array(array("OrderLineItems"=>$OrderLineItems)));
		}
		else
		return $database->error();
		
	}
}
//getEventDetails("167");
//var_dump(getOrderDetails("946","No"));
?>