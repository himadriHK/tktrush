<?php
include($_SERVER['DOCUMENT_ROOT'].'/dtcm_api/api_test.php');
include($_SERVER['DOCUMENT_ROOT'].'/dtcm_api/dtcm_api.php');
include("orderentry.php");
//test test

if(!isset($_SESSION['orderid']))
{
	header("Location: /index.php",TRUE,302);
	exit();
}

//if($_POST["paytype"]=="spot"){
//mysql_select_db($database_eventscon, $eventscon);
//$query_update = sprintf("UPDATE ticket_orders SET ccapproved = 'Yes' WHERE oid = %s", $_SESSION['orderid']);
//$update = mysql_query($query_update, $eventscon) or die(mysql_error());
//$orderid=$_SESSION['orderid'];
//$custid=$_SESSION['custid'];
////include("sendmail.php");
//	header("Location:events_confirm.php");
//	exit();
//}
//// functionality to update payment values for partner login
//if($_POST["paytype"]=="pt_spot" ){
//    $customer_data = array();
//    if(isset($_POST['email'])){
//        $customer_data['fname'] = $_POST['fname'];
//        $customer_data['lname'] = $_POST['lname'];
//        $customer_data['address'] = $_POST['address'];
//        $customer_data['mobile'] = $_POST['address'];
//        $customer_data['email'] = $_POST['email'];
//        $customer_data['city'] = $_POST['city'];
//        $customer_data['country'] = $_POST['country'];
//    }
//    mysql_select_db($database_eventscon, $eventscon);
//    $query_update = sprintf("UPDATE ticket_orders SET partner_id = ".$_SESSION['PP_UserId'].",ccapproved = 'Yes', payment_status = 'paid',customer_info = '".serialize($customer_data)."' WHERE oid = %s", $_SESSION['orderid']);
//    $update = mysql_query($query_update, $eventscon) or die(mysql_error());
//    $orderid=$_SESSION['orderid'];
//    $custid=$_SESSION['custid'];
//    //if(isset($_SESSION['PP_UserId'])){
//    //    header("Location:download_tickets.php");
//    //} else {
//    //    header("Location:events_confirm.php");
//    //}
//    exit();
//}
//End of partner functionality
##Hashing algorithm
function SignData($post_data,$secretKey,$fieldList) 
{
	$signatureParams = explode(',', $fieldList);
	$signatureString = $secretKey;
	foreach ($signatureParams as $param) {
		if (array_key_exists($param, $post_data)) {
			$signatureString .= ':' . trim($post_data[$param]);
		} else {
			$signatureString .= ':';
		}
	}
	return sha1($signatureString);
}
##End of hashing algorithm
//$gatewayUrl = "https://secure.innovatepayments.com/gateway/index.html";
//$secret_key='zVRZ$h77s';
//$store_id='12778';
//$test=1; //  0 for live
//$post_data = array(
//  'ivp_store' => $store_id,
//  'ivp_amount'     => $total_price,
//  'ivp_currency'  => 'AED',
//  'ivp_test' => $test,
//  'ivp_timestamp' => time(),
//  'ivp_cart' => $_SESSION['orderid'].'-'.$_SESSION['custid'],
//  'ivp_desc' => trim($row_eventRs['title']),
//  'ivp_extra' => 'return',
//  'return_cb_auth'=>$domain.'auth_success.php',
//  'return_cb_decl'=>$domain.'auth_cancel.php',
//  'return_cb_can'=>$domain.'auth_cancel.php',
//  'return_auth'=>$domain.'events_confirm.php',
//  'return_decl'=>$domain.'events_cancelled.php',
//  'return_can'=>$domain.'events_cancelled.php',
//  );
//
//var_dump($post_data);
//
//  $post_data['ivp_signature']=SignData($post_data,$secret_key,
//'ivp_store,ivp_amount,ivp_currency,ivp_test,ivp_timestamp,ivp_cart,ivp_desc,ivp_extra');
//$post_data['return_signature']=SignData($post_data,$secret_key,
//'return_cb_auth,return_cb_decl,return_cb_can,return_auth,return_decl,return_can,ivp_signature');
//var_dump($_POST);
//var_dump($_GET);
//var_dump($_SESSION);
$data = array(
				'ivp_method'	=> 'create',
				'ivp_source'	=> 'tktrush.com',
				'ivp_store'	=> '12778' ,
        'ivp_authkey'=>'tp3L^9CLZh@QcfTM',
				'ivp_cart'	=> $_SESSION['orderid'].'_'.$_SESSION['custid'].'_'.date('Ymd'),
				'ivp_test'	=> 1,
				'ivp_amount'	=> (int)$_SESSION['total'],
				'ivp_currency'	=> 'AED',
				'ivp_desc'	=> trim($row_eventRs['title']),
				'return_auth'	=> $domain.'test_session.php',
				'return_can'	=> $domain.'events_cancelled.php',
				'return_decl'	=> $domain.'events_cancelled.php',
				'bill_fname'	=> $_SESSION['Customer']['fname'],
				'bill_sname'	=> $_SESSION['Customer']['lname'],
        'bill_addr1'	=> $_SESSION['Customer']['address'],
        'bill_city'	=> $_SESSION['Customer']['city'],
        'bill_email'	=> $_SESSION['Customer']['email']
				);
//$data['ivp_signature']=SignData($data,'zVRZ$h77s','ivp_store,ivp_cart,ivp_test,ivp_amount,ivp_currency,ivp_desc,ivp_extra');
//$data['return_signature']=SignData($data,'zVRZ$h77s','return_auth,return_can,return_decl,ivp_signature');
//include("/dtcm_api/dtcm_api.php");
$payload=array('headers'=>array(),'body'=>http_build_query($data));
//var_dump(http_build_query($data));
//$gatewayUrl=json_decode(wp_remote_post('https://secure.telr.com/gateway/order.json',$payload),true);
var_dump($_SESSION);
@session_start();
if(isset($_SESSION['Customer']['type'])&&$_SESSION['Customer']['type']=='partner')
	$_SESSION['payment_order_ref']=$_SESSION['orderid'].$_SESSION['orderid'];
else
{
$gatewayUrl=json_decode(wp_remote_post('https://secure.telr.com/gateway/order.json',$payload),true);
$_SESSION['payment_order_ref']=$gatewayUrl['order']['ref'];

}

header('Cache-Control: no-cache');
header('Pragma: no-cache');
if(isset($_SESSION['Customer']['type'])&&$_SESSION['Customer']['type']=='partner')
{
	echo "Here---------";
	header('Location: /test_session.php',TRUE,302);
}
else
header('Location: '.$gatewayUrl['order']['url'],TRUE,302);
    //echo "<html>\n";
    //echo "<head><title>Processing Payment...</title>";
    //echo "<meta http-equiv=\"content-type\" content=\"text/html charset=UTF-8\" />";
    ////echo "<script>location.href=\"".$gatewayUrl['order']['url']."\"</script>";
    //echo "</head>\n";
    //echo "<body>\n";
    ////echo "<p style=\"text-align:center;width:100%;\"><img src=\"/images/innovate_logo.gif\" /></p><p style=\"text-align:center;width:100%;\">";
    ////echo " </p>\n";
    ////echo "<p style=\"width:100%;text-align:center;\"><br/><img src=\"/images/loading42.gif\" alt=\"Please wait..\"</p>";
    ////echo "<form method=\"GET\" name=\"gateway_form\" ";
    ////echo "action=\"" . $gatewayUrl['order']['url'] . "\">\n";
    //echo '<iframe src="'.$gatewayUrl['order']['url'].'"></iframe>';
    ////foreach ($post_data as $k => $v) {
	////ho "<input type=\"hidden\" name=\"" . $k . "\" value=\"" . htmlspecialchars($v) . "\">";
	////
    ////
    ////echo "<p style=\"text-align:center;\"><br/>";
    ////echo "If it is not automatically redirected";
    ////echo "...";
    ////echo "<br/><br/>\n";
    ////echo "<input type=\"submit\" value=\"Click Here\"></p>\n";
    //
    //echo "</form>\n";
    //echo "</body></html>\n";
		exit();