<?php require_once('Connections/eventscon.php');?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"  />
<?php
if(isset($_GET['eid']) && $_GET['eid']>0) {
	mysql_select_db($database_eventscon, $eventscon);
	$fb_query_eventRs = sprintf("SELECT events.* FROM events WHERE events.tid=%s ", $_GET['eid']);
	$fb_eventRs = mysql_query($fb_query_eventRs, $eventscon) or die(mysql_error());
	$fb_row_eventRs = mysql_fetch_assoc($fb_eventRs);
	?>
<meta property="og:title"
	content="<?php echo $fb_row_eventRs['title']; ?>" />
<meta property="og:description"
	content="<?php echo strip_tags($fb_row_eventRs['desc']); ?>" />
<meta property="og:image"
	content="http://www.tktrush.com/data/<?php echo $fb_row_eventRs['pic']; ?>" />
	<?php
}
?>
<title>Welcome</title>
<link href="style.css" rel="stylesheet" />
<link href="bootstrap.css" rel="stylesheet" />
<style type="text/css">
.menu_tab ul ul {
	display: none;
}
.menu_tab ul li:hover>ul {
	display: block;
}
.menu_tab ul {
	display: inline-table;
	list-style: none outside none;
	position: relative;
}
.menu_tab ul:after {
	content: "";
	clear: both;
	display: block;
}
.menu_tab ul li {
	float: left;
}
.menu_tab ul li:hover a {
	color: #fff;
}
.menu_tab ul li a {
	display: block;
	text-decoration: none;
}
.menu_tab ul ul {
	background: #008dce );
	border-radius: 0px;
	padding: 10px;
	position: absolute;
	top: 100%;
}
.menu_tab ul ul li {
	float: none;
	text-align: left;
	border-bottom: 1px solid rgba(0, 0, 0, 0.25);
	position: relative;
}
.menu_tab ul ul li a {
	padding: 10px 20px;
	color: #fff;
	font-size: 12px;
	width: 150px !important;
	text-transform: capitalize;
}
.menu_tab ul ul li a:hover {
	background: rgba(0, 0, 0, 0.25);
}
.menu_tab ul ul ul {
	position: absolute;
	left: 100%;
	top: 0;
}
</style>
<link href="css/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/skin.css">
<!--Added by ppatel on 25june2014-->
<script src="js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="js/bootstrap.js" type="text/javascript"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/countdown_zone.js" type="text/javascript"></script>
<script src="js/multitimer.js" type="text/javascript"></script>
<script language="JavaScript" src="mm_menu.js"></script>
<script type="text/javascript">
$('#nav a')
	.css( {backgroundPosition: "0 0"} )
	.mouseover(function(){
		$(this).stop().animate(
			{backgroundPosition:"(0 -250px)"}, 
			{duration:500})
		})
	.mouseout(function(){
		$(this).stop().animate(
			{backgroundPosition:"(0 0)"}, 
			{duration:500})
		})
</script>
<script>
function openVideoPlayer(videoName)
{
 if(videoName !="")
 {
  file="eventVideo/"+videoName;
 }
 window.open('playVideo.php?fileName='+file+'&from=video','popup','width=450,height=350,scrollbars=yes,resizable=yes,toolbar=no,status=no,left=50,top=0');
}
function openAudioPlayer(audioName)
{
 if(audioName!="")
 {
  file="eventAudio/"+audioName;
 }
 window.open('playVideo.php?fileName='+file+'&from=audio','popup','width=450,height=350,scrollbars=yes,resizable=yes,toolbar=no,status=no,left=50,top=0');
}
</script>
<?php
$default_city_id=0;
mysql_select_db($database_eventscon, $eventscon);
$query_guidecatRs = "SELECT * FROM `guide_cat` ORDER BY name ASC";
$guidecatRs = mysql_query($query_guidecatRs, $eventscon) or die(mysql_error());
$sql= "SELECT * FROM `banners`";
$query= mysql_query($sql, $eventscon) or die(mysql_error());
$images = mysql_fetch_array($query);
$sql= "SELECT * FROM `banner2`";
$query= mysql_query($sql, $eventscon) or die(mysql_error());
$img= mysql_fetch_array($query);
$sql= "SELECT * FROM `setting`";
$query= mysql_query($sql, $eventscon) or die(mysql_error());
$config= mysql_fetch_array($query);
define('APIKEY',$config['fbappid']);
define('SECRET',$config['fbskey']);
define( 'FBTOKEN', 'Y3CIB3' );
?>
<?php
$cur_catid = "";
$i=0;$k=1;
//echo $query_guidecatRs;
$javafunc2 = '';
$javafunc = '
<script language="JavaScript">
<!--
function mmLoadMenus() {
  if (window.mm_menu_1007235033_0) return;
'; 
//mysql_data_seek($guidecatRs, 0);
while($row_guidecatRs = mysql_fetch_array($guidecatRs)){
	$cat_name = '';
	$guidecat = preg_replace('/\s+/', '&nbsp;', $row_guidecatRs['name']);
	//echo $guidecat;
	//mysql_select_db($database_eventscon, $eventscon);
	$query_sub_guidecatRs = "SELECT * FROM guide_sub_cat where catid=".$row_guidecatRs['catid']." ORDER BY name ASC";
	//echo $query_sub_guidecatRs;
	if($guidesub_catRs = mysql_query($query_sub_guidecatRs)){
		while($row_guidesub_catRs = mysql_fetch_array($guidesub_catRs)){
			//$cat_name = '';
			if($row_guidesub_catRs['catid']!=$cur_catid) {
				$i++;
				$javafunc .= 'window.mm_menu_1007235033_0_'.$i.' = new Menu("'.$guidecat.'",180,25,"Arial, Helvetica, sans-serif",10,"#FEFEFE","#FEFEFE","#333333","#666666","left","middle",3,0,1000,-5,7,true,true,true,0,false,false);  ';
				$cat_name = 'mm_menu_1007235033_0_'.$i;
			} // end if catid check
			$guidesub_cat = preg_replace('/\s+/', '&nbsp;', $row_guidesub_catRs['name']);
			$javafunc .= 'mm_menu_1007235033_0_'.$i.'.addMenuItem("'.$guidesub_cat.'","location=\'guide.php?category='.$row_guidecatRs['catid'].'&subcat='.$row_guidesub_catRs['subcatid'].'\'");
  ';
			if($row_guidesub_catRs['catid']!=$cur_catid) {
				$javafunc .= 'mm_menu_1007235033_0_'.$i.'.fontWeight="bold";
';
				$javafunc .= 'mm_menu_1007235033_0_'.$i.'.hideOnMouseOut=true;
   mm_menu_1007235033_0_'.$i.'.bgColor=\'#000000\';
   mm_menu_1007235033_0_'.$i.'.menuBorder=1;
   mm_menu_1007235033_0_'.$i.'.menuLiteBgColor=\'#000000\';
   mm_menu_1007235033_0_'.$i.'.menuBorderBgColor=\'#333333\';
   ';
			} // end if catid check
			?>
			<?php
			$cur_catid = $row_guidesub_catRs['catid'];
		}// end sub_cat while
	} ?>
	<?php if($k==1){
		$javafuncmain = 'window.mm_menu_1007235033_0 = new Menu("root",180,25,"Arial, Helvetica, sans-serif",10,"#FEFEFE","#FEFEFE","#333333","#666666","left","middle",3,0,1000,-5,7,true,true,true,0,false,false);  ';
		$k=0;} ?>
		<?php
		//$guidecat = str_replace('&nbsp;', ' ',$row_guidecatRs['name']);
		$guidecat = preg_replace('/\s+/', '&nbsp;', $row_guidecatRs['name']);
		if($cat_name!=""){$guidecat = $cat_name;
		$javafunc2 .= 'mm_menu_1007235033_0.addMenuItem('.$guidecat.',"location=\'guide.php?category='.$row_guidecatRs['catid'].'\'");  ';
		} else {
			$javafunc2 .= 'mm_menu_1007235033_0.addMenuItem("'.$guidecat.'","location=\'guide.php?category='.$row_guidecatRs['catid'].'\'");  ';
		}
}// end cat while
echo $javafunc;
echo '	';
echo $javafuncmain;
echo '	';
echo $javafunc2;
?>
mm_menu_1007235033_0.fontWeight="bold";
mm_menu_1007235033_0.hideOnMouseOut=true;
mm_menu_1007235033_0.childMenuIcon="img/arrow_menu.png";
mm_menu_1007235033_0.bgColor='#000000';
mm_menu_1007235033_0.menuBorder=1;
mm_menu_1007235033_0.menuLiteBgColor='#000000';
mm_menu_1007235033_0.menuBorderBgColor='#333333';
mm_menu_1007235033_0.writeMenus(); } // mmLoadMenus() //-->
</script>
<script language="JavaScript1.2">mmLoadMenus();</script>
<script type="text/javascript">
$(document).ready(function(){
	$(".slide_button, .hideSlideBox").click(function(){
		$("#slide_panel").slideToggle();
		$(this).toggleClass("#slide_panel_hide"); return false;
	});
});
$(window).load(function(){
var menu = $('#loginblock'), but = $('#login a, #login img');
$(document).on('click','*', function(evt) {
    evt.stopPropagation();
    if ($(this).is(but))
        menu.toggle();
    else if (!$(this).closest(menu).length)
        menu.hide();
});
});
</script>
</head>
<body>




<?php
$bg_images=$images['b_image'];
if(isset($_GET['eid'])){
	mysql_select_db($database_eventscon, $eventscon);
	$query_bgeventRs = sprintf("SELECT events.popup_pic FROM events WHERE events.tid=%s", $_GET['eid']);
	$bgeventRs = mysql_query($query_bgeventRs, $eventscon) or die(mysql_error());
	$row_bgeventRs = mysql_fetch_assoc($bgeventRs);
	if($row_bgeventRs['popup_pic'] && file_exists($_SERVER['DOCUMENT_ROOT'].'/data/'.$row_bgeventRs['popup_pic']))
	$bg_images="data/".$row_bgeventRs['popup_pic'];
}
if(isset($_REQUEST['cat']) && $_REQUEST['cat']){
	$get_cat_query = "Select * from category where ename = '".$_REQUEST['cat']."'";
	$get_cat = mysql_query($get_cat_query,$eventscon);
	while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
		$im_cat[] = $row;
	}
	if(count($im_cat)==1){
		$im_cat = reset($im_cat);
		$popup_query = "SELECT * FROM `popup` where status=1 and place_type=2 and cat_id=".$im_cat['id']." ORDER BY id desc LIMIT 1";	
	}else{
		$popup_query ='';
	}
	
}else{
 $popup_query = "SELECT * FROM `popup` where status=1 and place_type=1 ORDER BY id desc LIMIT 1";
}
if($popup_query){
	$guidecatRs = mysql_query($popup_query, $eventscon) or die(mysql_error());
	while ($row = mysql_fetch_array($guidecatRs, MYSQL_ASSOC)) {
		$popup[] = $row;
	}
	if(count($popup)==1){
			$popup = reset($popup);	
	}else{
		$popup = array();	
	}
}else{
	$popup_query = array();
}
if($popup){
?>
<script type="text/javascript">
$(function(){
	if(!localStorage.skipIMPopup)
		$('#im_popup').modal('show');
	$('a#im_popup_skip').click(function(){
		localStorage.skipIMPopup = 1;
		$('#im_popup').modal('hide');
	});	
})
</script>
<div id="im_popup" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo $popup['title']; ?></h3>
  </div>
  <div class="modal-body">
    <?php if($popup['type']==1){ ?>
		<img src="popup/<?php echo $popup['source']; ?>" alt="<?php echo $popup['title']; ?>" >
	 <?php }else if($popup['type']==2){ ?>
		<iframe width="520" height="315" src="<?php echo $popup['source']; ?>" frameborder="0" allowfullscreen></iframe>
		
	<?php	}?>
    <a href="javascript:void(0)" id="im_popup_skip" style="float: right;margin: 3%;text-decoration: underline;">Skip</a>
  </div>
  
</div>
<?php } ?>

	<div style="background:url(<?php echo $bg_images; ?>)  repeat; position:fixed; width:100%; height:100%;background-size: cover;"></div>
	<div class="wrapper" style="position: relative;">
		<!--<div style="position:absolute; left:42px; top:35px;"><img src="img/slogan.gif" style="width:140px;"></div>-->
		<div class="main_header">
			<div class="city_divide">
				<div class="container">
					<div id="slide_panel" style="display: none;">
						<div class="hideSlideBox #slide_panel_hide">
							<a id="slide_panel_hide" href="#">Hide</a>
						</div>
						<ul class="countryList">
						<?php
						$query_countryRs = "SELECT * FROM shippingrates ORDER BY countryid ASC";
						$countryRs = mysql_query($query_countryRs, $eventscon) or die(mysql_error());
						while($row_countryRs = mysql_fetch_assoc($countryRs))
						{
							$country_id=$row_countryRs['countryid'];
							$sql="select * from cities where cid='$country_id'";
							$result=mysql_query($sql,$eventscon) or die(mysql_error());
							if(mysql_num_rows($result)){
								echo '<li>';
								echo '<span>'.$row_countryRs['name'].'</span>';
								echo '<ul class="cityList"> ';
								while($cities=mysql_fetch_assoc($result)){
									if($_COOKIE['city_id'])
									$default_city_id= $_COOKIE['city_id'];
									else if($cities['default_city']){
										$default_city_id=$cities['id'];
										setcookie('city_id',$default_city_id,time()+60*60*24*365);
									}
									if($cities['id']==$default_city_id)$city_name=$cities['name']; ?>
							<li><a href="cities.php?city=<?php echo $cities['id'];?>"><?php echo $cities['name'];?>
							</a></li>
							<?php }echo '</ul>';
							echo '</li>'; }
						} ?>
						</ul>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="header">
					<div class="logo">
						<a href="#"><img src="img/logo.png" /> </a>
					</div>
					<div class="head_top">
						<div class="city slide_button">
							<a href="#"><?php echo ($city_name!='')?$city_name:'City';?> </a>
							<img src="img/choose_city.png" class="choose_city" />
						</div>
					</div>
					<div class="logins">
					<?php if(empty($_SESSION['Customer'])){?>
					<?php require_once('facebookcall.php');?>
						<!-- <img src="img/fb_login.png" class="fblogin" /> -->
						<div class="topnav">
							<span id="login"><a href="#">Sign In <img src="img/navarrow.png" />
							</a> </span>
							<div class="signinner" id="loginblock">
								<form action="signin.php" method="post">
									<ul>
										<li><label>Email</label> <input type="text" name="email"
											value="" />
										</li>
										<li><label>Password</label> <input type="password"
											name="password" value="" />
										</li>
									</ul>
									<a href="forgot_password.php"
										style="float: left; padding: 5px 0; margin-right: 5px; font-size: 12px;">Forgot
										Pasword?</a> <input type="submit" name="" value="SUBMIT" />
								</form>
							</div>
							<a href="signup.php">Register</a>
						</div>
						<?php }else{?>
						<div class="topnav">
							<span
								style="color: #fff; float: left; margin-top: 6px; margin-right: 4px;">Welcome&nbsp;<?php echo $_SESSION['Customer']['fname'];?>
							</span> <a href="account.php">My Account</a> <a
								href="signout.php">Sign Out</a>
						</div>
						<?php }?>
					</div>
					<div class="menu_tab">
						<ul class="main-nav">
							<li><a href="index.php">Home</a></li>
							<li><a href="out_and_about.php">Out & About</a></li>
							<li><a href="hotel_booking.php">Hotel Booking</a></li>
							<!--<li><a href="guest_list.php">Guest List </a></li>-->
							<li><a href="guide.php" id="image1"
								onmouseover="MM_showMenu(window.mm_menu_1007235033_0,-23,11,null,'image1')"
								onmouseout="MM_startTimeout();">Guide</a>
								<ul>
									<li><a href="#">Bars/Lounge</a>
										<ul>
											<li><a href="guide.php?category=30&subcat=37">Abu Dhabi</a></li>
											<li><a href="guide.php?category=30&subcat=36">Dubai</a></li>
										</ul>
									</li>
									<li><a href="#">Night Clubs</a>
										<ul>
											<li><a href="guide.php?category=30&subcat=37">Abu Dhabi</a></li>
											<li><a href="guide.php?category=30&subcat=36">Dubai</a></li>
										</ul>
									</li>
									<li><a href="#">Planning an Event?</a>
										<ul>
											<li><a href="guide.php?category=29&subcat=27">Fencing &
													Barthrooms</a></li>
											<li><a href="guide.php?category=29&subcat=25">Limo Service</a>
											</li>
											<li><a href="guide.php?category=29&subcat=31">Magazines</a></li>
											<li><a href="guide.php?category=29&subcat=33">Party Store &
													Supplies</a></li>
											<li><a href="guide.php?category=29&subcat=26">Printing Shops</a>
											</li>
											<li><a href="guide.php?category=29&subcat=30">Radio Stations</a>
											</li>
											<li><a href="guide.php?category=29&subcat=29">Securities
													Companies</a></li>
											<li><a href="guide.php?category=29&subcat=32">SMS & Email
													Marketing</a></li>
											<li><a href="guide.php?category=29&subcat=24">Sound, State &
													Lights</a></li>
											<li><a href="guide.php?category=29&subcat=28">Wristbands</a>
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li><a href="outlets.php">Outlets </a></li>
							<!-- <li><a href="profile.php">About Us</a></li> -->
							<li><a href="sell_with_us.php">Sell With Us</a></li>
							<li><a href="contact.php">Contact us</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>