<?php 
include("functions.php"); 
include("config.php");
include($_SERVER['DOCUMENT_ROOT'].'/dtcm_api/api_test.php');
include($_SERVER['DOCUMENT_ROOT'].'/dtcm_api/dtcm_api.php');
ini_set('display_errors',1);
define('DISPLAY_XPM4_ERRORS', true);
//require_once('model_function.php');
session_regenerate_id();
@session_start();
//echo "here should be sesion data \n";

$data = array(
				'ivp_method'	=> "check",
				'ivp_store'	=> '12778' ,
				'order_ref'	=> @$_SESSION['payment_order_ref'],
				'ivp_authkey'	=> 'tp3L^9CLZh@QcfTM',
				);
$payload=array('headers'=>array(),'body'=>http_build_query($data));
//echo "test";

if(!isset($_SESSION['payment_order_ref']))
{
	session_regenerate_id();
	@session_start();
	//header("Location: /index.php");
	exit();
}
if(isset($_SESSION['Customer']['type'])&&$_SESSION['Customer']['type']=='partner')
{
	echo "Here----";
	$gatewayUrl=array("order"=>array("transaction"=>array("status"=>"A","ref"=>$_SESSION['orderid'].$_SESSION['orderid']),"amount"=>$_SESSION['total'],));
}
else
$gatewayUrl=json_decode(wp_remote_post('https://secure.telr.com/gateway/order.json',$payload),true);
//var_dump($gatewayUrl);
//var_dump($_SESSION);
//$_SESSION['event_id']=163;
//$_SESSION['orderid']=181;
function multidimensional_search($parents, $searched) {
									if (empty($searched) || empty($parents)) {
										return false;
									}
									foreach ($parents as $key => $value) {
										$exists = true;
										foreach ($searched as $skey => $svalue) {
											$exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
										}
										if($exists){ return $key; }
									}
									return false;
								}
if($gatewayUrl['order']['transaction']['status']=='A'||$gatewayUrl['order']['transaction']['status']=='H')
{
updatePaymentStatus($_SESSION['orderid'],'paid');
if($_SESSION['dtcm_event']=='Yes')	{
$purchaseTicket=json_decode($dtcm_->purchase_basket_dtcm(array($_SESSION['dtcm_order_id'],($gatewayUrl['order']['amount']-$_SESSION['extra_service_fees'])*100)),true);
if($purchaseTicket=='NULL'||!$purchaseTicket) 
{
	session_regenerate_id();
	@session_start();
	echo "here -- -- ";
	//header("Location: /index.php");
	exit();
}
$orderDetails=getOrderDetails($purchaseTicket['OrderId'],'Yes');
}
elseif($_SESSION['dtcm_event']=='No'){
$orderDetails=getOrderDetails($_SESSION['orderid'],'No');//json_decode($dtcm_->get_dtcm_order($purchaseTicket['OrderId']),true);
$_SESSION['purchased_order_id']=$_SESSION['orderid'];}
$perf_prices=getEventPrices($_SESSION['eid']);//json_decode($dtcm_->get_performance_prices($_SESSION['perf_code']),true);
//$_SESSION['purchased_order_id']='20170813,131';
//$orderDetails=json_decode($dtcm_->get_dtcm_order('20170813,131'),true);
//var_dump($purchaseTicket);
//var_dump($orderDetails);
$price_types=$perf_prices['PriceTypes'];
$ticket_prices=$perf_prices['TicketPrices']['Prices'];
$query_eventRS = sprintf("SELECT * FROM events WHERE tid = %s", $_SESSION['eid']);
//file_put_contents('inside_equery.txt',$query_eventRS);
$eventRs = mysql_query($query_eventRS, $eventscon);
//file_put_contents('inside_equery_ex.txt',$eventRS);
$row_eventRs_ = mysql_fetch_assoc($eventRs);
//var_dump($row_eventRs_);
$order_query= sprintf("select * from ticket_orders WHERE oid = %s",$_SESSION['orderid']);
$order_result = mysql_query($order_query, $eventscon);
$order=mysql_fetch_assoc($order_result);
$query_categoryRS = sprintf("SELECT * FROM category WHERE id = %s", $row_eventRs_['category']);
$categoryRs = mysql_query($query_categoryRS, $eventscon);
$row_categoryRs = mysql_fetch_assoc($categoryRs);
$query_priceRS = sprintf("SELECT * FROM event_prices WHERE tid in (%s)", $_SESSION['eid']);
$priceRs = mysql_query($query_priceRS, $eventscon);
$totalRows_priceRs = mysql_num_rows($priceRs);
$sponsor_logos='';
if($row_eventRs_['sponsor_logo1']!='')
$sponsor_logos.='<img src="http://www.tktrush.com/data/'.$row_eventRs_['sponsor_logo1'].'" alt="" width="70" height="72" style="margin-left:2px;" />';
if($row_eventRs_['sponsor_logo2']!='')
$sponsor_logos.='<img src="http://www.tktrush.com/data/'.$row_eventRs_['sponsor_logo2'].'" alt="" width="70" height="72" style="margin-left:2px;" />';
if($row_eventRs_['sponsor_logo3']!='')
$sponsor_logos.='<img src="http://www.tktrush.com/data/'.$row_eventRs_['sponsor_logo3'].'" alt="" width="70" height="72" style="margin-left:2px;" />';
if($row_eventRs_['sponsor_logo4']!='')
$sponsor_logos.='<img src="http://www.tktrush.com/data/'.$row_eventRs_['sponsor_logo4'].'" alt="" width="70" height="72" style="margin-left:2px;" />';
$tickets_data='';
$ticket_arr=unserialize($order['selected_seats']);
$msg = "You have successfully ordered your ticket. Please scroll down and download the tickets";
//include('header.php');
?>
<?php 		$order_query= sprintf("select * from ticket_orders WHERE oid = %s",$_SESSION['orderid']);
	$order_result = mysql_query($order_query, $eventscon);
	$order=mysql_fetch_assoc($order_result);
        if(empty($order['basket_id'])){
            echo 'Something went wrong, please report to admin.';
        }else {
            echo $msg;
        } 
		?>
		</p></td>
        <td width="10">&nbsp;</td>
      </tr>
     
    </table></td>
  </tr>
</table>
</body>
</html>
<?php
while($row_priceRs = mysql_fetch_assoc($priceRs)){
	if($ticket_arr['tickets'][$row_priceRs['pid']] || $ticket_arr['ctickets'][$row_priceRs['pid']]){
		$tickets_data.="<tr>";
	$tickets_data .= '<td style="padding: 5px 10px;">';
	$tickets_data .="Seat Type: ".$seat_type_arr[$row_priceRs['stand']]."</td>";
	$tickets_data .= '<td style="padding: 5px 10px;">';
	   if($ticket_arr['tickets'][$row_priceRs['pid']]){
	   $tickets_data .= "Adult Tickets: ".$ticket_arr['tickets'][$row_priceRs['pid']]." ";
	   }
	if($ticket_arr['ctickets'][$row_priceRs['pid']]){
	$tickets_data .= "ELAB Tickets: ".$ticket_arr['ctickets'][$row_priceRs['pid']];
	}
	$tickets_data .= "</td></tr>";
	}
	}
include_once $_SERVER['DOCUMENT_ROOT']."/dompdf/dompdf_config.inc.php";
include('sendmail.php');
global $m;
ob_start();
include('template_ticket.html');
$html_raw = ob_get_clean();
$html_parsed=$html_raw;
$seats_str='';
//var_dump($price_types);
$count=count($orderDetails['OrderItems'][0]['OrderLineItems']);
$ticket_array=array();
global $database;
$event_=$database->query("select * from events where tid=:tid",[":tid"=>$order["tid"]])->fetchAll();
$event_=$event_[0];
$gatesopen=date('jS \of F Y h:i:s A',strtotime(substr($order['event_date'],0,10).' '.$event_["doors_open"]));

foreach ($orderDetails['OrderItems'][0]['OrderLineItems'] as $item) { 
//var_dump($item);
//var_dump(multidimensional_search($price_types,array('PriceTypeCode'=>$item['PriceTypeCode'])));
$seats_str=$item['Seat']['Section']."/".$item['Seat']['Row']."/".$item['Seat']['Seats'];
$barcode=$item['Barcode'];
$database->insert("order_details",array("orderid"=>$_SESSION['orderid'],"category"=>$_SESSION['catname'],"subcategory"=>$price_types[multidimensional_search($price_types,array('PriceTypeCode'=>$item['PriceTypeCode']))]['PriceTypeDescription'],"price"=>$ticket_prices[multidimensional_search($ticket_prices,array('PriceCategoryCode'=>$item['PriceCategoryCode'],'PriceTypeCode'=>$item['PriceTypeCode']))]['PriceNet']/100));
$replace_arr = array(
'%%eventdate%%'=>date('Y-m-d',@strtotime($order['event_date'])),
'%%gatesopen%%'=>$gatesopen,
'%%orderno%%'=>$_SESSION['orderid'],
//'%%PurchaseDate%%'=>date('Y-m-d',strtotime($order['order_date'])),
'%%transactionref%%'=>$gatewayUrl['order']['transaction']['ref'],
'%%barcode_%%'=>$item['Barcode'],
'%%eventname%%'=>$row_eventRs_['title'],
'%%location%%'=>$row_eventRs_['venue'],
//'%%AgeLimit%%'=>$row_eventRs_['age_limit'],
'%%price%%'=>$ticket_prices[multidimensional_search($ticket_prices,array('PriceCategoryCode'=>$item['PriceCategoryCode'],'PriceTypeCode'=>$item['PriceTypeCode']))]['PriceNet']/100,
'%%category%%'=>$_SESSION['catname']."/".$price_types[multidimensional_search($price_types,array('PriceTypeCode'=>$item['PriceTypeCode']))]['PriceTypeDescription'],
//'%%ServiceCharge%%'=>$row_eventRs_['service_charge'],
'%%seat%%'=>$seats_str,
'%%barcode%%'=>$domain."barcode.php?code=".$item['Barcode'],
//'%%CCCharge%%'=>(($order['ticket_price']-2)-(( ($order['ticket_price']-2) / (5+100) )*100)),
//'%%TicketNumber%%'=>$order['order_number'],
//'%%TotalAmount%%'=>$gatewayUrl['order']['amount'],
'%%name%%'=>$_SESSION['Customer']['lname']." ".$_SESSION['Customer']['fname'],
//'%%code%%'=>$order['order_number'],
'%%eventimg%%'=>$domain.'data/'.$row_eventRs_['pic'],
'%%count%%'=>$item['Id']." of ".$count,
'%%date%%'=>date('Y-m-d'),
'%%total%%'=>$gatewayUrl['order']['amount'],
//'%%Tickets%%'=>$tickets_data,
//'%%EventSponser%%'=>$sponsor_logos,
'%%voucheradd%%'=>(($row_eventRs_['voucher_advert1']!='')?$domain.'data/'.$row_eventRs_['voucher_advert1']:$domain.'data/'.'images/controed1.png'),
//'%%VoucherAdvert2%%'=>(($row_eventRs_['voucher_advert2']!='')?'http://www.tktrush.com/data/'.$row_eventRs_['voucher_advert2']:'http://www.tktrush.com/images/controed2.png'),
);
//var_dump($row_eventRs_['pic']);
	//file_put_contents("test5.json",json_encode($replace_arr));
    foreach($replace_arr as $key => $val){
		//var_dump($key);
		//var_dump($val);
    	$html_parsed = str_replace($key,$val,$html_parsed);
    }
//echo "<br>".$html_parsed."<br>"; 
$dompdf = new DOMPDF();
$dompdf->load_html($html_parsed);
//$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$pdf=$dompdf->output();
$file_location = dirname(__FILE__)."/vouchers/eventticket_".$item['Barcode'].".pdf";
       //file_put_contents($file_location,$pdf);
$file = fopen($file_location,"w");
$ticket_array=$ticket_array+array($item['Barcode']=>$html_parsed);
if(fwrite($file,$pdf)){
//echo "<center><a href=\""."/vouchers/eventticket_".$item['Barcode'].".pdf"."\" target=\"_blank\">Download Ticket</a></center>";
fclose($file);
}
$html_parsed=$html_raw;
$file=$file_location;
//
//$m->Attach(file_get_contents($file_location), FUNC5::mime_type($file_location), basename($file_location), null, null, 'attachment', MIME5::unique());
// connect to MTA server 'smtp.hostname.net' port '25' with authentication: 'username'/'password'
}
ob_start();
include('template_mail.html');
$html_parsed = ob_get_clean();
$replace_arr['%%count%%']=$count;
foreach($replace_arr as $key => $val){
		//var_dump($key);
		//var_dump($val);
    	$html_parsed = str_replace($key,$val,$html_parsed);
    }
//$m->Html($html_parsed,'utf-8','base64');
//$c=false;
//$c = $m->Connect('mail3.gridhost.co.uk', 25, 'tickets@tktrush.com', 'tickets@tktrush');
//if($c)
//$m->Send($c);
}
//if($_GET['vpc_Message']=="Approved"){
//} else { $msg ="Your ticket could not be ordered. Please try again or call customer support.";}
//var_dump($_SESSION);
if(isset($_SESSION['dtcm_id'])) 
unset($_SESSION['dtcm_id']);

if(isset($_SESSION['dtcm_order_id']))
unset($_SESSION['dtcm_order_id']);

if(isset($_SESSION['eid']))
unset($_SESSION['eid']);
//unset($_SESSION['orderid']);
if(isset($_SESSION['orderid']))
$_SESSION['orderid']=$_SESSION['orderid']."paid";

$_SESSION['tickets_print']=$ticket_array;
if(isset($_SESSION['payment_order_ref']))
unset($_SESSION['payment_order_ref']);

if(isset($_SESSION['purchased_order_id']))
unset($_SESSION['purchased_order_id']);

session_regenerate_id();
@session_start();
var_dump($_SESSION);
//echo "<html>\n<script>window.close();</script>\n</html>";
?>