<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

<div class="container">
	<?php require_once('view/showMessage.php'); ?>
	<form class="form-horizontal" method="post" action="index.php?page=profile&amp;action=submit" 
	oninput="confirm.setCustomValidity(confirm.value != password.value ? 'Passwords do not match.' : '')">

		<div class="block">
			<!-- Fullname -->
			<div class="control-group">
				<label class="control-label" for="fullname">Όνομα</label>
				<div class="controls">
					<input type="text" name="fullname" id="fullname" value="<?php echo $User->fullname ?>">
				</div>
			</div>
	
			<!-- vat -->
			<div class="control-group">
				<label class="control-label" for="vat">ΑΦΜ (?)</label>
				<div class="controls">
					<input type="text" name="vat" id="vat" value="<?php echo $User->vat ?>">
				</div>
			</div>
	
			<!-- phone_number -->
			<div class="control-group">
				<label class="control-label" for="phone_number">Τηλέφωνο</label>
				<div class="controls">
					<input type="text" name="phone_number" id="phone_number" value="<?php echo $User->phone_number ?>">
				</div>
			</div>
		</div>
		<div class="block">
			<!-- email -->
			<div class="control-group">
				<label class="control-label" for="Email">Email</label>
				<div class="controls">
					<input type="text" name="email" id="email" value="<?php echo $User->email ?>">
				</div>
			</div>
	
			<!-- Password -->
			<div class="control-group">
				<label class="control-label" for="password">Κωδικός</label>
				<div class="controls">
					<input type="password" name="password" id="password" value="">
				</div>
			</div>
			
			<!-- Re-Password -->
			<div class="control-group">
				<label class="control-label" for="confirm">Επαλήθευση Κωδικού</label>
				<div class="controls">
					<input type="password" name="confirm" id="confirm" value="">
				</div>
			</div>
		</div>

		<div class="control-group right">
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
				 errorElement: "em",
				  rules: {
					password: confirm,
					password: {
						rangelength: [4, 16]
					},
					phone_number: {
						required: true,
						digits: true,
						rangelength: [8, 16]
					},
				    vat: {
					    required: true,
					    digits: true,
					    minlength: 9,
					    maxlength: 9
				    },
				    email: {
					    required: true,
					    email: true 
					},
					fullname: {
					    required: true,
					    minlength: 4
					}
				  },
				  messages: {
						password: {
							rangelength: "Παρακαλώ εισάγετε έναν κωδικό από 4-16 χαρακτήρες"
						},
					  phone_number: {
							required: "Παρακαλώ εισάγεται αριθμό τηλεφώνου",
							digits: "Παρακαλώ εισάγετε έναν σωστό αριθμό τηλεφώνου",
							rangelength: "Παρακαλώ εισάγετε έναν σωστό αριθμό τηλεφώνου"
						},
					    vat: {
						    required: "Παρακαλώ εισάγετε το ΑΦΜ",
						    digits: "Παρακαλώ εισάγετε ένα έγκυρο ΑΦΜ",
						    minlength: "Παρακαλώ εισάγετε ένα έγκυρο ΑΦΜ",
						    maxlength: "Παρακαλώ εισάγετε ένα έγκυρο ΑΦΜ"
					    },
					    email: {
						    required: "Παρακαλώ εισάγετε τη διεύθυνση email",
						    email: "Παρακαλώ εισάγετε μια έγκυρη διεύθυνση email"
						},
						fullname: {
						    required: "Παρακαλώ εισάγετε το όνομα",
						    minlength: "Παρακαλώ εισάγετε ένα αποδεκτό όνομα"
						}
				  }
				});
		});
	</script>
</div>
