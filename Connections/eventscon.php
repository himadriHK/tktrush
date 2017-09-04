<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
include_once("Medoo.php");
include_once('../dtcm_api/api_test.php');
include_once('../dtcm_api/dtcm_api.php');
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
	if(!$database->error()||$database->error()[0]=="00000")
	{
		//var_dump($data);
		return $data;
	}
	else
		var_dump($database->error());
}

function getEventPrices($event_id)
{
	global $database;
	global $dtcm_;
	$data=$database->query('select e.dtcm_approved,e.dtcm_code, p.*,s.* from events e,event_prices p,seats s where  e.tid=p.tid and p.stand=s.id and e.tid=:tid',[':tid'=>$event_id])->fetchAll();
	if(!$database->error()||$database->error()[0]=="00000")
	{
		var_dump($data[0]);
		if($data[0]['dtcm_approved']=='Yes' and $data[0]['dtcm_code']!='')
			return json_decode($dtcm_->get_performance_prices($data['dtcm_code']),true);
		elseif($data[0]['dtcm_approved']=='No')
		{
			$PriceCategories=array();
			$PriceTypes=array();
			$Prices=array();
			foreach($data as $price)
			{
				array_push($PriceCategories,array('PriceCategoryId'=>$price['id'],'PriceCategoryCode'=>$price['id'],'PriceCategoryName'=>$price['seat_type']));
				
				array_push($PriceTypes,array('PriceTypeId'=>$price['pid'],'PriceTypeCode'=>'A','PriceTypeName'=>'ADULT','PriceTypeDescription'=>'Adult','AdmitCount'=>1));
				
				array_push($PriceTypes,array('PriceTypeId'=>'-'.$price['pid'],'PriceTypeCode'=>'C','PriceTypeName'=>'CHILD','PriceTypeDescription'=>'Child','AdmitCount'=>1));
				
				array_push($Prices,array('PriceTypeId'=>$price['pid'],'PriceCategoryId'=>$price['pid'],'PriceCategoryCode'=>$price['id'],'PriceTypeId'=>$price['pid'],'PriceTypeCode'=>'A','PriceNet'=>$price['price']*100));
				
				array_push($Prices,array('PriceTypeId'=>'-'.$price['pid'],'PriceCategoryId'=>'-'.$price['pid'],'PriceCategoryCode'=>$price['id'],'PriceTypeId'=>'-'.$price['pid'],'PriceTypeCode'=>'C','PriceNet'=>$price['cprice']*100));
				
				$tmp= array('PriceCategories'=>$PriceCategories,'PriceTypes'=>$PriceTypes,'TicketPrices'=>array('Prices'=>$Prices));
				
				var_dump($tmp);
			}
			//var_dump($PriceCategories);
		}
		
		//return $data;
	}
	else
		var_dump($database->error());
}

//getEventDetails("167");
getEventPrices("167");
?>