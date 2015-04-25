<?php

	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

	require_once('view/header.php');

?>
	<div class="container">
		<p>Οππς... Παρουσιάστηκε κάποιο σφάλμα!</p>
		<?php if(isset($ex) && $ex->getMessage()) { ?>
		<p class="text-error"><?php echo $ex->getMessage(); ?></p>
<?php } ?>
		<p class="text-warning">Αν συνεχίζεται αυτό επικοινωνήστε με τον διαχειριστή της εφαρμογής..</p>
		
	</div>
<?php 
	
	require_once('view/footer.php');
	
	exit();
?>