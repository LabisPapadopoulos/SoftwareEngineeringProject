<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	
?>
<div class="container">
	<form class="form-horizontal" method="post" action="<?php echo $action_url; ?>"
	oninput="confirm.setCustomValidity(confirm.value != password.value ? 'Passwords do not match.' : '')">
	<div class="block">	
		<div class="control-group">
			<label class="control-label" for="username">Username</label>
			<div class="controls">
				<input type="text" name="username" id="username" value="<?php echo $target_user->username ?>" <?php echo ($target_user->username != '') ? "readonly" : ""; ?> required>
			</div>
		</div>

		<!-- email -->
		<div class="control-group">
			<label class="control-label" for="Email">Email</label>
			<div class="controls">
				<input type="text" name="email" id="email" value="<?php echo $target_user->email ?>" <?php echo ($target_user->username != '') ? "readonly" : ""; ?> required>
			</div>
		</div>

		<!-- Fullname -->
		<div class="control-group">
			<label class="control-label" for="fullname">Oνοματεπώνυμο</label>
			<div class="controls">
				<input type="text" name="fullname" id="fullname" value="<?php echo $target_user->fullname ?>" <?php echo ($target_user->username != '') ? "readonly" : ""; ?> required>
			</div>
		</div>

		<!-- vat -->
		<div class="control-group">
			<label class="control-label" for="vat">ΑΦΜ</label>
			<div class="controls">
				<input type="text" name="vat" id="vat" value="<?php echo $target_user->vat ?>" <?php echo ($target_user->username != '') ? "readonly" : ""; ?> required>
			</div>
		</div>
	</div>
	<div class="block">
		<!-- phone_number -->
		<div class="control-group">
			<label class="control-label" for="phone_number">Τηλέφωνο</label>
			<div class="controls">
				<input type="text" name="phone_number" id="phone_number" value="<?php echo $target_user->phone_number ?>" <?php echo ($target_user->username != '') ? "readonly" : ""; ?> required>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="type">Δικαιώματα</label>
			<div class="controls">
				<select name="type" id="type">
					<option value="admin">admin</option>
					<option  value="seller">seller</option>
					<option  value="manager">manager</option>
					<option  value="storekeeper">storekeeper</option>
				</select>
			</div>
		</div>

		<?php if($target_user->username != null): ?>
		<div class="control-group">
			<label class="control-label" for="deleted">Διεγραμμένος</label>
			<div class="controls">
				<input type="checkbox" name="deleted" id="deleted" <?php echo ($target_user->status == "deleted") ? "checked=\"checked\"" : ""; ?> value="1">
			</div>
		</div>
		<?php endif; ?>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn">Υποβολή</button>
			</div>
		</div>
		<?php if($_CONFIG['FORM_TOKENS']): ?>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
		<?php endif; ?>
	</div>
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			$("form").validate({
				 errorClass: "alert-error",
				 validClass: "alert-success",
				  rules: {
				    username: {
					    required: true,
					    rangelength: [4, 16]
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
					    required: "Παρακαλώ εισάγετε το όνομα χρήστη",
					    rangelength: "Παρακαλώ εισάγετε ένα όνομα χρήστη από 4 εώς 16 χαρακτήρες"
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