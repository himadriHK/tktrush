<?php require("access.php"); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO event_prices (tid, price, cprice, currency, stand, tickets, ctickets) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['tid'], "int"),
                       GetSQLValueString($_POST['price'], "double"),
					   GetSQLValueString($_POST['cprice'], "double"),
                       GetSQLValueString($_POST['currency'], "text"),
                       GetSQLValueString($_POST['stand'], "text"),
                       GetSQLValueString($_POST['tickets'], "int"),
                       GetSQLValueString($_POST['ctickets'], "int"));

  mysql_select_db($database_eventscon, $eventscon);
  $Result1 = mysql_query($insertSQL, $eventscon) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

require("upload.php");

$picture = "";
$xpic = "picture";
 if (($_FILES[$xpic]['name']<>"none") and ($_FILES[$xpic]['name']<>"")){
 	if ($_POST["hot"]=="Yes"){
		uploadimg($xpic, "N", "N", 500, 350, 470, 165);
	} else {
		uploadimg($xpic, "N", "N", 500, 350, 125, 100);	
	}
$picture = $_FILES[$xpic]['name'];
}

$locmap = "";
$xpic = "locmap";
 if (($_FILES[$xpic]['name']<>"none") and ($_FILES[$xpic]['name']<>"")){
uploadimg($xpic, "N", "N", 500, 350, 0,0);
$locmap = $_FILES[$xpic]['name'];
}

$floorplan = "";
$xpic = "floorplan";
 if (($_FILES[$xpic]['name']<>"none") and ($_FILES[$xpic]['name']<>"")){
uploadimg($xpic, "N", "N", 500, 350,0,0);
$floorplan = $_FILES[$xpic]['name'];
}

include("../functions.php");


if ($_POST['date_start']==""){
	$datestart = "";
} else { $datestart = getpdate($_POST['date_start']); }


if ($_POST['date_end']==""){
	$dateend = "";
} else {$dateend = getpdate($_POST['date_end']);}


if ($_POST['sale_date']==""){
	$saledate = "2007-10-01";
} else {$saledate = getpdate($_POST['sale_date']);}

$eventdesc = str_replace("\n", "<br>", $_POST['desc']);

$insertSQL = sprintf("INSERT INTO events (promoter, country, title, `desc`, thumb, pic, date_start, date_end, venue, dress, age_limit, restaurant, rest_room, hot, loc_map, floorplan, ongoing, session_hour, doors_open, sale_date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['promoter'], "int"),
                       GetSQLValueString($_POST['country'], "int"),
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($eventdesc, "text"),
                       GetSQLValueString("t_".$picture, "text"),
                       GetSQLValueString($picture, "text"),
                       GetSQLValueString($datestart, "date"),
                       GetSQLValueString($dateend, "date"),
                       GetSQLValueString($_POST['venue'], "text"),
                       GetSQLValueString($_POST['dress'], "text"),
                       GetSQLValueString($_POST['age_limit'], "text"),
                       GetSQLValueString(isset($_POST['restaurant']) ? "true" : "", "defined","'Yes'","'No'"),
                       GetSQLValueString(isset($_POST['rest_room']) ? "true" : "", "defined","'Yes'","'No'"),
                       GetSQLValueString(isset($_POST['hot']) ? "true" : "", "defined","'Yes'","'No'"),
                       GetSQLValueString($locmap, "text"),
                       GetSQLValueString($floorplan, "text"),
                       GetSQLValueString(isset($_POST['ongoing']) ? "true" : "", "defined","'Yes'","'No'"),
                       GetSQLValueString($_POST['session_hour'].".00", "date"),
                       GetSQLValueString($_POST['doors_open'].".00", "date"),
                       GetSQLValueString($saledate, "date"));
//echo $insertSQL."<br><br>";
  mysql_select_db($database_eventscon, $eventscon);
  $Result1 = mysql_query($insertSQL, $eventscon) or die(mysql_error());
}

mysql_select_db($database_eventscon, $eventscon);
$query_promotersRs = "SELECT spid, name FROM promoters ORDER BY name ASC";
$promotersRs = mysql_query($query_promotersRs, $eventscon) or die(mysql_error());
$row_promotersRs = mysql_fetch_assoc($promotersRs);
$totalRows_promotersRs = mysql_num_rows($promotersRs);

mysql_select_db($database_eventscon, $eventscon);
$query_countryRs = "SELECT * FROM countries ORDER BY country ASC";
$countryRs = mysql_query($query_countryRs, $eventscon) or die(mysql_error());
$row_countryRs = mysql_fetch_assoc($countryRs);
$totalRows_countryRs = mysql_num_rows($countryRs);

mysql_select_db($database_eventscon, $eventscon);
$query_eventRs = "SELECT tid, title FROM events ORDER BY title ASC";
$eventRs = mysql_query($query_eventRs, $eventscon) or die(mysql_error());
$row_eventRs = mysql_fetch_assoc($eventRs);
$totalRows_eventRs = mysql_num_rows($eventRs);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ticket Master</title>
<link href="events.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="datepicker.js"></script>
<style type="text/css">
<!--
.headeradmin {color: #C31600}
-->
</style>
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
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="60%"><form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1">
              <table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td height="35" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">ADD EVENT</span></td>
                </tr>
                <tr>
                  <td background="../images/w-dot.gif"><img src="../images/w-dot.gif" width="3" height="1" /></td>
                </tr>
              </table>
              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" class="eventText">
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Promoter:</td>
                  <td bgcolor="#EAEAFF"><select name="promoter" class="formField" style="width:150px">
                      <?php 
do {  
?>
                      <option value="<?php echo $row_promotersRs['spid']?>" ><?php echo $row_promotersRs['name']?></option>
                      <?php
} while ($row_promotersRs = mysql_fetch_assoc($promotersRs));
?>
                    </select>                  </td>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Country:</td>
                  <td bgcolor="#EAEAFF"><select name="country" class="formField" style="width:150px">
                      <?php 
do {  
?>
                      <option value="<?php echo $row_countryRs['cid']?>" ><?php echo $row_countryRs['country']?></option>
                      <?php
} while ($row_countryRs = mysql_fetch_assoc($countryRs));
?>
                    </select>                  </td>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Title:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="title" value="" size="32" class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" valign="top" nowrap bgcolor="#CCCCCC">Description:</td>
                  <td bgcolor="#EAEAFF"><textarea name="desc" cols="50" rows="5" class="formField"></textarea>                  </td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Picture:</td>
                  <td bgcolor="#EAEAFF"><input name="picture" type="file" class="formField" id="picture" />
                    <a href="administer/data/<?php echo $row_eventsRs['pic']; ?>" target="_blank"></a></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Date start:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="date_start" value="" size="32" class="formField" readonly="readonly">
                    <a href="javascript:NewCal('date_start','mmddyyyy')"><img src="../images/cal.gif" width="16" height="16" border="0" alt="Pick a date" onmouseover="this.style.cursor = 'hand';" onmouseout="this.style.cursor = 'default';"></a></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Date end:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="date_end" value="" size="32" class="formField">
                    <a href="javascript:NewCal('date_end','mmddyyyy')"><img src="../images/cal.gif" width="16" height="16" border="0" alt="Pick a date" onmouseover="this.style.cursor = 'hand';" onmouseout="this.style.cursor = 'default';"></a></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Venue:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="venue" value="" size="32" class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Dress:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="dress" value="" size="32" class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Age limit:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="age_limit" value="" size="32" class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Restaurant:</td>
                  <td bgcolor="#EAEAFF"><input type="checkbox" name="restaurant" value="Yes"  class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Rest room:</td>
                  <td bgcolor="#EAEAFF"><input type="checkbox" name="rest_room" value="Yes"  class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Hot:</td>
                  <td bgcolor="#EAEAFF"><input type="checkbox" name="hot" value="Yes"  class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Location map:</td>
                  <td bgcolor="#EAEAFF"><input name="locmap" type="file" class="formField" id="locmap" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Floor plan:</td>
                  <td bgcolor="#EAEAFF"><input name="floorplan" type="file" class="formField" id="floorplan" />
                    <a href="administer/data/<?php echo $row_eventsRs['pic']; ?>" target="_blank"></a> <a href="administer/data/<?php echo $row_eventsRs['pic']; ?>" target="_blank"></a></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Ongoing:</td>
                  <td bgcolor="#EAEAFF"><input type="checkbox" name="ongoing" value="Yes"  class="formField"></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Session hour:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="session_hour" value="" size="32" class="formField">
                    HH:MM </td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Doors open:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="doors_open" value="" size="32" class="formField">
                    HH:MM </td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Start Sale date:</td>
                  <td bgcolor="#EAEAFF"><input type="text" name="sale_date" value="" size="32" class="formField" readonly="readonly">
                    <a href="javascript:NewCal('sale_date','mmddyyyy')"><img src="../images/cal.gif" width="16" height="16" border="0" alt="Pick a date" onmouseover="this.style.cursor = 'hand';" onmouseout="this.style.cursor = 'default';" /></a></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;</td>
                  <td bgcolor="#EAEAFF"><input type="submit" value="Insert record" class="formField"></td>
                </tr>
              </table>
              <input type="hidden" name="MM_insert" value="form1" class="formField">
            </form>
            <p>&nbsp;</p></td>
          <td width="1" valign="top" background="../images/up-dot.gif"><img src="../images/up-dot.gif" width="1" height="3" /></td>
          <td width="40%" valign="top"><form method="post" name="form2" action="<?php echo $editFormAction; ?>">
              <table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td height="35" bgcolor="#C31600"><span class="headeradmin">-</span><span class="eventHeader">ADD EVENT PRICE </span></td>
                </tr>
                <tr>
                  <td background="../images/w-dot.gif"><img src="../images/w-dot.gif" width="3" height="1" /></td>
                </tr>
              </table>
              <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" class="eventText">
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Event:</td>
                  <td bgcolor="#EAEAFF"><select name="tid" class="formField" style="width:100px">
                      <?php 
do {  
?>
                      <option value="<?php echo $row_eventRs['tid']?>" ><?php echo $row_eventRs['title']?></option>
                      <?php
} while ($row_eventRs = mysql_fetch_assoc($eventRs));
?>
                    </select>                  </td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Adult Price:</td>
                  <td bgcolor="#EAEAFF"><input name="price" type="text" class="formField" value="" size="15"></td>
                  <td bgcolor="#EAEAFF">No or Tickets:
<input name="tickets" type="text" id="tickets" size="15" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" bgcolor="#CCCCCC">Child Price:</td>
                  <td bgcolor="#EAEAFF"><input name="cprice" type="text" class="formField" id="cprice" value="" size="15" /></td>
                  <td bgcolor="#EAEAFF">No or Tickets: 
                  <input name="ctickets" type="text" id="ctickets" size="15" /></td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap="nowrap" bgcolor="#CCCCCC">&nbsp;</td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Currency:</td>
                  <td bgcolor="#EAEAFF"><input name="currency" type="text" class="formField" value="" size="15"></td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">Stand:</td>
                  <td bgcolor="#EAEAFF"><input name="stand" type="text" class="formField" value="" size="15"></td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                </tr>
                <tr valign="baseline">
                  <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;</td>
                  <td bgcolor="#EAEAFF"><input type="submit" value="Add Price"></td>
                  <td bgcolor="#EAEAFF">&nbsp;</td>
                </tr>
              </table>
              <input type="hidden" name="MM_insert" value="form2">
          </form>
            </td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($promotersRs);

mysql_free_result($countryRs);

mysql_free_result($eventRs);
?>
