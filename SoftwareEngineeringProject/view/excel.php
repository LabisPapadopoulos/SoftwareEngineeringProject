<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

<div class="container">

	<h3>Συγκεντρωτικά στοιχεία επιχείρησης σε Excel</h3>

	<div class="form-filter">
		<form method="get" >

		<input type="hidden" name="page" value="<?php echo $_GET["page"] ?>" >
		<input type="hidden" name="action" value="download" >

		<div id="from-date">
			<label class="inline" for="start">Από: </label>
			<input type="text" name="start" id="start" class="datepicker" value="<?php echo $start ?>" >
		</div>
		<div id="to-date">
			<label class="inline" for="end">Έως: </label>
			<input type="text" name="end" id="end" class="datepicker" value="<?php echo $end ?>" >
		</div>
		<div id="submit-date">
			<button class="btn btn-primary right" type="submit" >Κατέβασμα</button>
		</div>

	</form>
</div>
</div>


<script type="text/javascript">

	$(function() {

		/* Activate datepicker */
		$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
		$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );
	});

</script>
