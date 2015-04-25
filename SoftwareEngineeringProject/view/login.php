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
			<h3>Καλωσορίσατε στην εφαρμογή διαχείρισης</h3>
			<p>Μπορείτε να συνδεθείτε στο σύστημα εισάγοντας τα στοιχεία του λογαριασμού σας</p>
		</div>

		<?php require("view/showMessage.php"); ?>

		<form class="form-signin" method="post" action="index.php?page=login&amp;action=submit&amp;redirect=<?php echo base64_encode(serialize($_SERVER['QUERY_STRING'])); ?>">
			<h2 class="form-signin-heading">Είσοδος</h2>
			<input type="text" name="username" class="input-block-level" placeholder="όνομα χρήστη" />
			<input type="password" name="password" class="input-block-level" placeholder="συνθηματικό" />
			<label class="checkbox">
				<input type="checkbox" value="1" name="keep_login"> Να παραμείνεις συνδεδεμένος
			</label>
			<p><a href="?page=login&amp;action=forgot-password&ampredirect=<?php echo base64_encode(serialize($_SERVER['QUERY_STRING'])); ?>">Ξέχασες τον κωδικό σου;</a></p>
			<input type="hidden" name="form-submit" value="login" />
			<button class="btn btn-large btn-primary" type="submit" >Είσοδος</button>
			<?php if($_CONFIG['FORM_TOKENS']): ?>
			<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
			<?php endif;?>
		</form>

	</div> <!-- /container -->

</body>
</html>


