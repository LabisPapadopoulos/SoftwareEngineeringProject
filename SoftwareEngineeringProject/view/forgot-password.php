<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("LOGIN_VIEW", true);
?>

<!-- This is a stand-alone view, header.php and footer.php should not be included before this. -->

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<!-- Bootstrap CSS -->
	<link href="view/css/bootstrap.min.css" rel="stylesheet" media="screen">

	<!-- JS -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script type="text/javascript" src="view/js/bootstrap.min.js" ></script>


	<!-- Custom CSS files -->
	<link href="view/css/login.css" rel="stylesheet" media="screen">

	<!-- Force SSL? -->
	<?php if(checkArr($CONFIG, 'force_ssl', true)): ?>
		<script type="text/javascript">
		if (window.location.protocol != "https:")
		    window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
		</script>
	<?php endif; ?>

</head>

<body class="login">
	<div class="container">
		<!-- login page -->
		<div class="content">
			<h3>Ξεχάσατε τον κωδικό σας;</h3>
			<p>Εισάγετε παρακάτο το email σας για να επαναφέρετε τον κωδικό σας</p>
		</div>

		<?php require("view/showMessage.php"); ?>
		
		<form class="form-signin" method="post" action="index.php?page=login&amp;action=forgot-password&amp;redirect=<?php echo checkArr($_GET, 'redirect'); ?>">
			<input type="email" name="email" class="input-block-level" placeholder="email" />
			
			<input type="hidden" name="form-submit" value="forgot" />
			<a class="btn btn-large btn-primary" href="?page=login&amp;redirect=<?php echo checkArr($_GET, 'redirect'); ?>">Πίσω</a>
			<button class="btn btn-large btn-primary" type="submit" >Υποβολή</button>
		
			<?php if($_CONFIG['FORM_TOKENS']): ?>
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
			<?php endif;?>
		</form>

	</div> <!-- /container -->

</body>
</html>