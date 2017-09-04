<?php
ini_set('display_errors',1);
//ini_set('sendmail_path','/usr/sbin/sendmail');
require_once('mail_plugin/MAIL5.php');

//var_dump($m);

$hostname_eventscon = "10.168.1.47";
$database_eventscon = "tktrushc_dbase";
$username_eventscon = "tktrushc_user";
$password_eventscon = "LP0q221p";
$eventscon = @mysql_pconnect($hostname_eventscon, $username_eventscon, $password_eventscon) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database_eventscon, $eventscon);
$query_orderRS = sprintf("SELECT * FROM ticket_orders WHERE oid=%s",$_SESSION['orderid']);
//echo $query_orderRS."<br>";
$orderRs = mysql_query($query_orderRS, $eventscon) or die(mysql_error());
$row_orderRs = mysql_fetch_assoc($orderRs);
//print_r($row_orderRs);echo "<br>";
$totalRows_orderRs = mysql_num_rows($orderRs);
//echo $query_orderRS;
mysql_select_db($database_eventscon, $eventscon);
$query_custRS = sprintf("SELECT * FROM customers WHERE cust_id =%s", $_SESSION['Customer']['cust_id']);
//echo $query_custRS."<br>";
$custRs = mysql_query($query_custRS, $eventscon) or die(mysql_error());
$row_custRs = mysql_fetch_assoc($custRs);
//print_r($row_custRs);echo "<br>";
$totalRows_custRs = mysql_num_rows($custRs);
//echo $query_custRS;
mysql_select_db($database_eventscon, $eventscon);
$query_eventRS = sprintf("SELECT tid, title FROM events WHERE tid = %s", $row_orderRs['tid']);
$eventRs = mysql_query($query_eventRS, $eventscon) or die(mysql_error());
$row_eventRs = mysql_fetch_assoc($eventRs);
//print_r($row_eventRs);echo "<br>";
$totalRows_eventRs = mysql_num_rows($eventRs);
//echo $query_eventRS;
mysql_select_db($database_eventscon, $eventscon);
$query_priceRS = sprintf("SELECT * FROM event_prices WHERE tid in (%s)", $row_orderRs['tid']);
$priceRs = mysql_query($query_priceRS, $eventscon) or die(mysql_error());
$totalRows_priceRs = mysql_num_rows($priceRs);
$zonetime = 3600*4;
$today = gmdate("l dS \of F Y h:i:s A", time() + $zonetime);
//$seat_type_arr=get_set_type();
$body = "Event Ticket Ordered\r\n";
$body .= "---------------------\r\n";
$body .= "Order Number: ".$row_orderRs['order_number']."\r\n";
$body .= "Customer Name: ".$row_custRs['lname']." ".$row_custRs['fname']."\r\n";
$body .= "Mobile: ".$row_custRs['mobile']."\r\n";
$body .= "Email: ".$row_custRs['email']."\r\n";
$body .= "City: ".$row_custRs['city']."\r\n";
$body .= "Country: ".$row_custRs['country']."\r\n\r\n";
$body .= "Address: ".$row_custRs['address']."\r\n\r\n";
$body .= "Event\r\n";
$body .= "------\r\n";
$body .= "Title: ".$row_eventRs['title']."\r\n";
$body .= "Order Date: ".$today."\r\n";
$body .= "Session Date: ".$row_orderRs['event_date']."\r\n";
if( $row_orderRs['tid'] != 161){
if($totalRows_priceRs>0)
{
 $ticket_arr=unserialize($row_orderRs['selected_seats']);
while($row_priceRs = mysql_fetch_assoc($priceRs)){
	if($ticket_arr['tickets'][$row_priceRs['pid']] || $ticket_arr['ctickets'][$row_priceRs['pid']]){
	$body .="\r\n";
	$body .= "------------------------------------\r\n";
	//$body .="Seat Type: ".$seat_type_arr[$row_priceRs['stand']]."\r\n";
	//$body .= "------------------------------------\r\n";
	$body .= "Ticket Adult Price: ".$row_priceRs['price']."\r\n";
	
	$body .= "Ticket Child Price: ".$row_priceRs['cprice']."\r\n";
	
	$body .= "Adult Tickets Ordered: ".(($ticket_arr['tickets'][$row_priceRs['pid']])?$ticket_arr['tickets'][$row_priceRs['pid']]:'0')."\r\n";
	
	$body .= "Child Tickets Ordered: ".(($ticket_arr['ctickets'][$row_priceRs['pid']])?$ticket_arr['ctickets'][$row_priceRs['pid']]:'0')."\r\n";
	}
}
}
}else{
    $body .="\r\n";
	$body .= "------------------------------------\r\n";
	$body .="Seat Type: General \r\n";
	$body .= "------------------------------------\r\n";
	$body .= "Ticket Adult Price: ".$row_orderRs['ticket_price']."\r\n";
	
	$body .= "Adult Tickets Ordered: ".$row_orderRs['tickets']."\r\n";
	
	
}
//$tt = (($row_priceRs['price']*$row_orderRs['tickets'])+($row_priceRs['cprice']*$row_orderRs['ctickets']));
$body .= "------------------------------------\r\n";
$body .="\r\n";
$body .= "Total Adult Tickets: ".$row_orderRs['tickets']."\r\n";
//$body .= "Total Child Tickets: ".$row_orderRs['ctickets']."\r\n";
$body .= "Delivery Charges: ".$row_orderRs['charges']."\r\n";
$body .= "Total Amount: ".$row_orderRs['ticket_price']."\r\n";


//echo $body;exit;
$to = $row_custRs['email'];
$subject = "Your electronic tickets and payment receipt for".$row_eventRs['title'];
$headers = "From: Ticket Rush <tickets@tktrush.com>\r\n";
$headers .= "Reply-To: Ticket Rush <tickets@tktrush.com>\r\n";
$headers .= "Cc: Ticket Rush <info@tktrush.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
//$file = dirname(__FILE__).'/vouchers/eventticket_'.$row_orderRs['order_number'].".pdf";
//$file_size = filesize($file);
//$handle = fopen($file, "r");
//$content = fread($handle, $file_size);
//fclose($handle);
$content = chunk_split(base64_encode($content));
$uid = md5(uniqid(time()));
//$name = basename($file);
$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
$headers .= "This is a multi-part message in MIME format.\r\n";
$headers .= "--".$uid."\r\n";
$headers .= "Content-type:text/plain; charset=iso-8859-1\r\n";
$headers .= "Content-Transfer-Encoding: 7bit\r\n";
$headers .= $body."\r\n";
$headers .= "--".$uid."\r\n";
$headers .= "Content-Type: application/pdf; name=\"eventticket_".$row_orderRs['order_number'].".pdf\"\r\n"; // use different content types here
$headers .= "Content-Transfer-Encoding: base64\r\n";
$headers .= "Content-Disposition: attachment; filename=\"eventticket_".$row_orderRs['order_number'].".pdf\"\r\n";
$headers .= $content."\r\n";
$headers .= "--".$uid."--";
//$mail_sent = mail( $to, $subject, $body,'');// $headers );
$m = new MAIL5; // initialize MAIL class
$m->From('tickets@tktrush.com'); // set from address
$m->AddTo($row_custRs['email']); // add to address
$m->Subject($subject); // set subject
$m->Text($body); // set text message
//$m->Attach(file_get_contents($file), FUNC5::mime_type($file), $name, null, null, 'attachment', MIME5::unique());
//// connect to MTA server 'smtp.hostname.net' port '25' with authentication: 'username'/'password'
//$c = $m->Connect('mail3.gridhost.co.uk', 25, 'tickets@tktrush.com', 'tickets@tktrush') or die(print_r($m->Result));
////var_dump($m);
//$m->Send($c);
?>