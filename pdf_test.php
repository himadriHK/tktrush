<?php
include_once "dompdf/dompdf_config.inc.php";
ob_start();
include('template_ticket.html');
$html_raw = ob_get_clean();
$dompdf = new DOMPDF();
$dompdf->load_html($html_raw);
//$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$pdf=$dompdf->output();
$file_location = dirname(__FILE__)."/vouchers/test4.pdf";
$file = fopen($file_location,"w");
if(fwrite($file,$pdf)){
echo "<html><body><center><a href=\"/vouchers/test4.pdf\" target=\"_blank\">Download Ticket</a></center></body></html>";
fclose($file);
}
?>