<?php
session_start();
include('header.php');
//var_dump($_SESSION);
if(!isset($_SESSION['tickets_print']))
{
	session_regenerate_id();
	header("Location: /index.php");
	exit();
}
?>
<body>
</br>
</br>
<table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="45" background="images/fasel-middle.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="10">&nbsp;</td>
        <td width="36" height="45">&nbsp;</td>
        <td width="15">&nbsp;</td>
        <td class="eventHeader" style="color:#eee;">Event Ticket Ordered </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="500" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="3" background="images/g-dot.jpg"><img src="images/g-dot.jpg" width="7" height="7" /></td>
        </tr>
      <tr>
        <td width="10" >&nbsp;</td>
        <td align="center" valign="top"><p>&nbsp;</p>
            <?php
            $name = '';
            if(isset($_SESSION['Customer']['lname'])){
                $name = $_SESSION['Customer']['lname']." ".$_SESSION['Customer']['fname'];
            }
            if(isset($_SESSION['PP_UserId'])){
                $name = $_SESSION['PP_Username'];
            }
            ?>
          <p>Dear <?php echo $name ?>,<br />
		  <p>You have successfully ordered your ticket. Please scroll down and download the tickets</p>
		</p></td>
        <td width="10">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3" background="images/g-dot.jpg"><img src="images/g-dot.jpg" width="7" height="7" /></td>
        </tr>
    </table></td>
  </tr>
</table>
<?php
foreach($_SESSION['tickets_print'] as $name=>$html_code)
{
	echo "</br>".$html_code;
	echo "<center><a href=\""."/vouchers/eventticket_".$name.".pdf"."\" target=\"_blank\">Download Ticket</a></center>";
}
unset($_SESSION['tickets_print']);
session_regenerate_id();
?>