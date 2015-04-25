<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="1; url=<?php echo $redirectUrl ?>">
    <script type="text/javascript">
        window.location.href = "<?php echo $redirectUrl ?>"
    </script>
    <title>Page Redirection</title>
</head>
<body>

    <?php if(checkVar($successMsg)) { ?>

				<div class="alert alert-success">
  					<button type="button" class="close" data-dismiss="alert">&times;</button>
  					<?php echo $successMsg ?>
				</div>

        <?php }
        	if(checkVar($failureMsg)) { ?>

        		<div class="alert alert-error">
  					<button type="button" class="close" data-dismiss="alert">&times;</button>
  					<?php echo $failureMsg ?>
				</div>

        <?php } ?>

	Αν δεν μεταφερθείτε αυτόματα, ακολουθείστε το <a href="<?php echo $redirectUrl ?>">σύνδεσμο</a>

<?php 
if(!defined("FOOTER_VIEW"))
	require_once('view/footer.php');
?>