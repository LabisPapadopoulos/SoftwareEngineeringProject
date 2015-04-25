<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

<div class="container">
	<h3>Προσθήκη νέου πελάτη</h3>
	<form class="form-horizontal" method="post" action="index.php?page=customers&amp;action=submit">

		<!-- fullname -->
		<div class="control-group">
			<label class="control-label" for="fullname">Όνομα</label>
			<div class="controls">
				<input type="text" id="fullname" name="fullname" value="" required>
			</div>
		</div>

		<!-- vat -->
		<div class="control-group">
			<label class="control-label" for="vat">ΑΦΜ</label>
			<div class="controls">
				<input type="number" id="vat" name="vat" value="" required>
			</div>
		</div>

		<!-- location -->
		<div class="control-group">
			<label class="control-label" for="location">Περιοχή</label>
			<div class="controls">
				<input type="text" id="location" name="location" value="" required>
			</div>
		</div>

		<!-- phone_number -->
		<div class="control-group">
			<label class="control-label" for="phone_number">Τηλέφωνο</label>
			<div class="controls">
				<input type="text" id="phone_number" name="phone_number" value="" required>
			</div>
		</div>

		<!-- email -->
		<div class="control-group">
			<label class="control-label" for="email">Email</label>
			<div class="controls">
				<input type="text" id="email" name="email" value="" required>
			</div>
		</div>



		<!-- Submit button -->
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn">Αποθήκευση</button>
			</div>
		</div>
		<?php if($_CONFIG['FORM_TOKENS']): ?>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
		<?php endif; ?>
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			$("form").validate({
				 errorClass: "alert-error",
				 validClass: "alert-success",
				  rules: {
					location: {
					    required: true,
					    rangelength: [4, 25]
					}, email: {
					    required: true,
					    email: true 
					},fullname: {
						required: true,
						minlength: 4
					},vat: {
						required: true,
						minlength: 9,
						maxlength: 9,
						digits: true
					},phone_number: {
						required: true,
						digits: true,
						rangelength: [7, 14]
					}
				  },
				  messages: {
				 	username: {
					    required: "Παρακαλώ εισάγετε μια διεύθυνση",
					    rangelength: "Παρακαλώ εισάγετε μια διεύθυνση από 4 εώς 25 χαρακτήρες"
					}, email: {
					    required: "Παρακαλώ εισάγετε τη διεύθυνση email",
					    email: "Παρακαλώ εισάγετε μια έγκυρη διεύθυνση email"
					},fullname: {
						required: "Παρακαλώ εισάγετε το όνομα",
						minlength: "Παρακαλώ εισάγετε ένα σωστό όνομα"
					},vat: {
						required: "Παρακαλώ εισάγετε το ΑΦΜ",
						minlength: "Παρακαλώ εισάγετε σωστό ΑΦΜ",
						maxlength: "Παρακαλώ εισάγετε σωστό ΑΦΜ",
						digits: "Παρακαλώ εισάγετε ένα έγκυρο ΑΦΜ"
					},phone_number: {
						required: "Παρακαλώ εισάγετε τον αριθμό τηλεφώνου",
						digits: "Παρακαλώ εισάγετε έναν σωστό αριθμό τηλεφώνου",
						rangelength: "Παρακαλώ εισάγετε έναν έγκυρο αριθμό τηλεφώνου"
					}
				  }
				});
		});
	</script>

</div>