<?php require_once('../Connections/eventscon.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
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
 mysql_select_db($database_eventscon, $eventscon);
 if($_POST['id']){
	 $val=1;
	 $id=$_POST['id'];
	 $sql="UPDATE cities set default_city='0' where 1";
	 mysql_query($sql,$eventscon);
	$sql="UPDATE cities set default_city='$val' where id='$id'";
	mysql_query($sql,$eventscon);
	echo "success";exit;
}
if ((isset($_GET["act"])) && ($_GET["act"] == "del")) {
  $updateSQL = sprintf("delete from cities WHERE id=%s",
                       GetSQLValueString($_GET['cityid'], "int"));

  $Result1 = mysql_query($updateSQL, $eventscon) or die(mysql_error());
}

mysql_select_db($database_eventscon, $eventscon);
$query_shiplist = "SELECT * FROM cities ORDER BY name ASC";
$shiplist = mysql_query($query_shiplist, $eventscon) or die(mysql_error());
$row_shiplist = mysql_fetch_assoc($shiplist);
$totalRows_shiplist = mysql_num_rows($shiplist);


?>
<?php require("access.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ticket Master</title>
<link href="events.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.headeradmin {color: #C31600}
-->
</style>
<script src="../js/jquery-1.8.2.js" type="text/javascript"></script>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php require("head.php"); ?></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  
  <tr>
    <td width="200" valign="top"><?php require("contents.php"); ?></td>
    <td width="1" valign="top" background="../images/up-dot.gif"><img src="../images/up-dot.gif" width="1" height="3" /></td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top"><table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td height="35" style="width:241px;" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">VIEW CITIES</span></td>
            
              <td height="35" style="width:417px;" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">Country</span></td>
			  <td height="35" style="width:352px;" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">Default city</span></td>
               <td height="35" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">Actions</span></td>
            </tr>
            <tr>
              <td background="../images/w-dot.gif"><img src="../images/w-dot.gif" width="3" height="1" /></td>
            </tr>
          </table>
            <table width="100%" border="0" cellspacing="1" cellpadding="3">
            
              <?php 
              
              while($row_shiplist = mysql_fetch_assoc($shiplist)) { 
              $sql = "SELECT * FROM shippingrates where countryid=".$row_shiplist['cid'];
              $sqlq= mysql_query($sql, $eventscon) or die(mysql_error());
                  $country= mysql_fetch_assoc($sqlq);
                ?>
                <tr>
                  <td bgcolor="#E9E9E9"><a href="" class="eventText"><?php echo $row_shiplist['name']; ?></a></td>
                  <td bgcolor="#E9E9E9"><a href="" class="eventText"><?php echo $country['name']; ?></a></td>
                  <td bgcolor="#E9E9E9"><a href="" class="eventText"><a href="#" onclick="default_city('<?php echo $row_shiplist['id'];?>');"><?php echo ($row_shiplist['default_city'])?'Default city':'Make it default city';?></a></td>
                  <td width="60" bgcolor="#E9E9E9"><div align="center"><a href="manage_city.php?cityid=<?php echo $row_shiplist['id']; ?>" class="headeradmin">Edit</a></div></td>
                  <td width="60" bgcolor="#E9E9E9"><div align="center"><a href="view_city.php?cityid=<?php echo $row_shiplist['id']; ?>&amp;act=del" onclick="return confirm('Are you sure you want to delete?')" class="headeradmin">Delete</a></div></td>
                </tr>
                <?php } ?>
        </table>          </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($shiplist);
?>
<script>
function default_city(id)
{
	$.ajax({
		type:'POST',
		url:"view_city.php",
		data:"id="+id,
		success:function(msg){
			window.location.reload(true);
		}
		
	});
}
</script>