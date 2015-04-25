<?php 
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true);

?>
<div class="container">
	<form class="form-horizontal" action="<?php echo $edit ? "index.php?page=pricelist&amp;action=edit_done" : "index.php?page=pricelist&amp;action=add_done"; ?>" method="post">
	<div class="block">
		<div class="control-group">
			<label class="control-label" for="ID">ID</label>
			<div class="controls">
				<input type="text" name="ID" id="ID" readonly value="<?php echo $edit ? $product->id : "N/A"; ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="name">Όνομα</label>
			<div class="controls">
				<input type="text" name="name" id="mane" value="<?php echo $edit ? $product->name : ""; ?>" <?php echo $edit ? "readonly" : ""; ?> required>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description">Περιγραφή</label>
			<div class="controls">
				<input type="text" name="description" id="description" value="<?php echo $edit ? $product->description : ""; ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="supplier">Προμηθευτής</label>
			<div class="controls">
				<select name="supplier" id="supplier" data-placeholder="Διάλεξε.." class="chzn-select">
					<?php 
					//TODO should I get only active?
					$suppliers = ModelSuppliers::get_suppliers();
					
					foreach($suppliers as $supplier){
						$selected = false;
						if(($edit && $product->supplier == $supplier->fullname)
								|| (checkArr($_GET, "supplier") && $_GET['supplier'] == $supplier->fullname))
							$selected = true;				

						echo '<option value="'. $supplier->fullname .'"';
						echo $selected ? ' selected="selected"' : '';
						echo '>'. $supplier->fullname .'</option>
						';
					}
					?>
					
				</select>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="metric">Μονάδα Μέτρησης</label>
			<div class="controls">
				<input type="text" name="metric" id="metric" value="<?php echo $edit ? $product->metric_units : ""; ?>" required>
			</div>
		</div>
	</div>
	<div class="block">
		
		<div class="control-group">
			<label class="control-label" for="market_value">Τιμή Αγοράς</label>
			<div class="controls">
				<input type="text" name="market_value" id="market_value" value="<?php echo $edit ? $product->market_value : ""; ?>" required>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="sell_value">Τιμή Πώλησης</label>
			<div class="controls">
				<input type="text" name="sell_value" id="sell_value" value="<?php echo $edit ? $product->sell_value : ""; ?>" required>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="quantity">Φυσικό απόθεμα</label>
			<div class="controls">
				<input type="number" name="quantity" id="quantity" value="<?php echo $edit ? $product->total_quantity : "0"; ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="quantity">Διαθέσιμο φυσικό απόθεμα</label>
			<div class="controls">
				<input type="number" name="available_quantity" id="available_quantity" value="<?php echo $edit ? $product->available_quantity : "0"; ?>">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="quantity">Οριακή ποσότητα</label>
			<div class="controls">
				<input type="number" name="limit" id="limit" value="<?php echo $edit ? $product->limit : "0"; ?>">
			</div>
		</div>

		<button class="btn btn-info" type="submit">Αποθήκευση</button>
		<?php if($_CONFIG['FORM_TOKENS']): ?>
  		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
  		<?php endif; ?>
  	</div>
	</form>
	<script type="text/javascript">
	//here something's whrong with css
		$(document).ready(function(){
			$("form").validate({
				 errorClass: "alert-error",
				 validClass: "alert-success",
				  rules: {
				    name: "required",
				    metric: "required",
				    market_value: {
					    required: true,
					    number: true 
					},
				    sell_value: {
					    required: true,
					    number: true 
					}
				  },
				  messages: {
				    name: "Παρακαλώ εισάγετε όνομα",
				    metric: "Παρακαλώ εισάγετε μονάδα μέτρησης",
				  	market_value: {
					    required: "Παρακαλώ εισάγετε Τιμή Αγοράς",
					    number: "Παρακαλώ εισάγετε έναν έγκυρο αριθμό στην Τιμή Αγοράς" 
					},
					sell_value: {
					    required: "Παρακαλώ εισάγετε Τιμή Πώλησης",
					    number: "Παρακαλώ βάλτε έναν έγκυρο αριθμό στην Τιμή Πώλησης" 
					},
				  }
				});
		});
	</script>
</div>