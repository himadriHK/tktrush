<?php
require("access.php");
include("../config.php"); 

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

if(!isset($_REQUEST['e'])){
	header('Location: popup.php');exit;		
}
require("connection1.php");
	$id= mysql_connect($server,$login,$password);
	mysql_select_db($base,$id);
	
$query_guidecatRs = "SELECT * FROM `popup` where id = ".$_REQUEST['e'];
$guidecatRs = mysql_query($query_guidecatRs, $id) or die(mysql_error());
while ($row = mysql_fetch_array($guidecatRs, MYSQL_ASSOC)) {
	$popup[] = $row;
}
if(count($popup)!==1){
	header('Location: popup.php');exit;		
}else{
	$popup = reset($popup);	
}

if(isset($_POST['submit'])){
	
	$file_path=$_SERVER['DOCUMENT_ROOT'];
	
	if($_FILES['source']['name']!="" && $_POST['type']==1){

		$img_name= time().$_FILES['source']['name'];
		$img_path="/popup/".$img_name;
		$tmp_path=$_FILES['source']['tmp_name'];
		if($popup['source'])
			unlink($file_path.'/popup/'.$popup['source']);
		move_uploaded_file($tmp_path,$file_path.$img_path);
		
	}else if($_POST['type']==2){
		if($popup['source'] && $popup['type']==1 && file_exists($file_path.'/popup/'.$popup['source']))
			unlink($file_path.'/popup/'.$popup['source']);
			
		$img_name = $_POST['video_url']; 
	}
		
	if(!$img_name && $_POST['type']==1)
		$img_name = $popup['source']; 	
	
	
	
	$insertSQL = sprintf("UPDATE popup SET title = %s, source = %s, status = %s, type =%s,cat_id =%s,place_type =%s where id = ".$_REQUEST['e'],
GetSQLValueString($_POST['title'], "text"),
GetSQLValueString($img_name, "text"),
GetSQLValueString($_POST['status'], "int"),
GetSQLValueString($_POST['type'], "int"),
GetSQLValueString($_POST['cat_id'], "int"),
GetSQLValueString($_POST['place_type'], "int"));
$Result1 = mysql_query($insertSQL, $id) or die(mysql_error());
	header('Location: popup.php');exit;	

}


?>
<html>
<head>
<title>
<?require"title.php";?>
</title>
<link href="events.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="../js/jquery-1.8.2.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php require("head.php"); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="200" valign="top" bgcolor=<?echo"$contentbg";?>><?php require("contents.php"); ?></td>
    <td width="1" valign="top" background="../images/up-dot.gif"><img src="../images/up-dot.gif" width="1" height="3"> <img src="../images/up-dot.gif" width="1" height="3"></td>
    <td align="center" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif" color="#CC3300"> <b>Create Pop-up</b></font><br>
      <table border="0" cellspacing="3" cellpadding="1">
        <tr> </tr>
      </table>
      <br>
      <form method="post" action="#" enctype="multipart/form-data">
        <table width="98%" border="1" cellspacing="1" cellpadding="0" bordercolor="#F3F3F3">
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC">Title:</td>
            <td bgcolor="#EAEAFF"><input type="text" class="formField" name="title" size="40" value="<?php echo $popup['title']; ?>" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC">Type:</td>
            <td bgcolor="#EAEAFF"><input  name="type" type="radio" class="formField" value="1" <?php  echo ($popup['type']==1)?'checked="checked"':''; ?>  /><label>Image</label><input type="radio" class="formField" value="2"  name="type" <?php  echo ($popup['type']==2)?'checked="checked"':''; ?> /><label>Video</label></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC">Source:</td>
            <td bgcolor="#EAEAFF">
            <input type="file" class="formField" name="source"  id="image_source"  <?php echo ($popup['type']==2)?'style="display:none;"':''; ?> />
            <input type="text" class="formField" name="video_url" size="100" id="video_url" <?php echo ($popup['type']==1)?'style="display:none;"':''; ?> value="<?php echo $popup['source']; ?>" />
            
            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC">Status:</td>
            <td bgcolor="#EAEAFF"><select name="status">
            	<option value="1" <?php  echo ($popup['status']==1)?'selected="selected"':''; ?>>Publish</option>
                <option value="0"  <?php  echo ($popup['status']==0)?'selected="selected"':''; ?>>Unpublish</option>	
            </select>
            </td>
          </tr>
          
          
             
           <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC">Page Type:</td>
            <td bgcolor="#EAEAFF"><select name="place_type" id="place_type">
            	<option value="1" <?php  echo ($popup['place_type']==1)?'selected="selected"':''; ?>>Home Page</option>
                <option value="2" <?php  echo ($popup['place_type']==2)?'selected="selected"':''; ?>>Category Page</option>	
            </select>
            </td>
          </tr>
          
          
            <tr valign="baseline" <?php echo ($popup['place_type']==2)?'':'style="display:none"'; ?> id="place_cat_list">
            <td align="right" nowrap bgcolor="#CCCCCC">Category:</td>
            <td bgcolor="#EAEAFF">
            	<?php
					$sql = "select * FROM category";
					$category_query = mysql_query($sql, $id) or die(mysql_error());
				
				?>
            <select name="cat_id" id="cat_id">
            	<option value="" >Choose Category</option>
            	<?php 
				while ($category = mysql_fetch_assoc($category_query)) {  
					if($popup['cat_id'] == $category['id']){ ?>
                        <option value="<?php echo $category['id']?>" selected="selected"><?php echo $category['name']?></option>
				<?php 	}else{
				?>
				<option value="<?php echo $category['id']?>" ><?php echo $category['name']?></option>
				<?php
					}
				} 
				?>
            </select>
            </td>
          </tr>
          
          
          <tr valign="baseline">
            <td align="right" nowrap bgcolor="#CCCCCC"></td>
            <td bgcolor="#EAEAFF">
            	<input type="submit" name="submit" value="Save" />
            </td>
          </tr> 
          
        </table>
      </form></td>
  </tr>
</table>
<script type="text/javascript">
$('#place_type').on('change',function(){
	if($(this).val()==2){
		$('#cat_id').val('');
		$('#place_cat_list').show();	
	}else{
		$('#cat_id').val('');
		$('#place_cat_list').hide();	
	}
});
$('input[name=type]').on('click',function(){
	var $this = $('input[name=type]:checked');
		if($this.val()==1){
			$('#video_url').hide();
			$('#image_source').show();
		}else if($this.val()==2){
			$('#image_source').hide();
			$('#video_url').show();
		}
});
</script>
</body>
</html>