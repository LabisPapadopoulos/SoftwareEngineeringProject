<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("HEADER_VIEW", true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $title ?></title>

	<!-- Bootstrap CSS -->
	<link href="view/css/bootstrap.css" rel="stylesheet" media="screen">

	<!-- Chosen CSS -->
	<link href="view/css/chosen.css" rel="stylesheet" media="screen">

	<!-- Custom CSS files -->
	<link href="view/css/styles.css" rel="stylesheet" media="screen">	

	<!-- Force SSL? -->
	<?php if(checkArr($CONFIG, 'force_ssl', true)): ?>
		<script type="text/javascript">
		if (window.location.protocol != "https:")
		    window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
		</script>
	<?php endif; ?>
	
	<!-- jquery, bootstrap and chosen js -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/i18n/jquery.ui.datepicker-el.min.js"></script>
	<script type="text/javascript" src="view/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="view/js/jquery.ui.datepicker.validation.js"></script>
	
	

	<script type="text/javascript" src="view/js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="view/js/chosen.jquery.js" ></script>

	<!-- jquery theme -->
	<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/pepper-grinder/jquery-ui.css" rel="stylesheet" media="screen">
	
	<!-- Calendar -->
	<!-- <link href="view/themes/rocket/jquery-wijmo.css" rel="stylesheet" type="text/css" /> -->

	<!-- Tablesorter plugin -->
	<script type="text/javascript" src="view/js/jquery.tablesorter.min.js"></script>

	<!-- Flot graphing plugin -->
	<script language="javascript" type="text/javascript" src="view/js/flot/jquery.flot.min.js"></script>
	<script language="javascript" type="text/javascript" src="view/js/flot/jquery.flot.time.min.js"></script>
	

	<link href="view/themes/wijmo/jquery.wijmo.wijcalendar.css" rel="stylesheet" type="text/css"/>	
	<script src="view/external/globalize.min.js" type="text/javascript"></script>
	<script src="view/wijmo/jquery.wijmo.wijpopup.js" type="text/javascript"></script>
	<script src="view/wijmo/jquery.wijmo.wijcalendar.js" type="text/javascript"></script>
	
	
	<script type="text/javascript">
		$(function () {
			$("#calendar1").wijcalendar(
				{ easing: "easeOutExpo" }
			);
		});
	</script>
	
	<?php 
	if(checkArr($redirect, "url") != null){
		echo "<script type=\"text/javascript\">setTimeout(function(){ window.location = '{$redirect['url']}'}, ";
		echo checkArr($redirect, "time") == null ? $CONFIG['default_redirect_time'] : $redirect['time']*1000;
		echo ")</script>";
		}	?>
</head>

<body class="container">
	<div id="wrapper">
        <div>
            <div style="padding-left: 90px;"><a href="index.php"><img src="" id="logo"/></a></div>
            <div id="controlPanel">
            	<p>Καλώς ορίσατε, <?php echo $User==null ? "Επισκέπτη" : $User->username; ?></p></br>
            	<p><a href="index.php?page=profile"><button class="btn">Προφίλ χρήστη</button></a><a href="index.php?page=logout"> <button class="btn">Αποσύνδεση</button></a></p>
            </div>
        </div>
        <?php 
        	require_once('view/menu.php');
       	?>
