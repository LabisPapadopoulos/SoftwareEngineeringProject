<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

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

<?php }
	if(checkVar($warningMsg)) { ?>

	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $warningMsg ?>
	</div>


<?php } ?>
