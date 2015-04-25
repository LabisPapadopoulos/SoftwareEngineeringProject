<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
?>

<div class="container">
	<form action="?page=discount&amp;action=submit&amp;id=<?php echo $customer->id ?>" method="post">
	
	<div class="block left_new_order">
		<h3>Εκπτώσεις</h3>
		<!-- Display customer details -->
		<div class="block" >
			<span class="bold">Πελάτης: </span>
			<a href="?page=customers&id=<?php echo $customer->id; ?>" target="_blank" ><?php echo $customer->fullname; ?></a>
		</div>
		<div class="block right" >
			<span class="bold">ΑΦΜ: </span><?php echo $customer->vat; ?>
		</div>
		<br />
		<div class="block" >
			<span class="bold">Περιοχή: </span><?php echo $customer->location; ?>
		</div>
		<div class="block" >
			<span class="bold">Τηλ: </span><?php echo $customer->phone_number; ?>
		</div>
		<div class="block right" >
			<span class="bold">Email: </span><?php echo $customer->email; ?>
		</div>

		<div style="text-align: left;">	
			<h5>Προσθήκη προϊόντων</h5>
			<select id="productsSelect" data-placeholder="Διάλεξε προϊόν" class="chzn-select" style="width:300px;" tabindex="2">
    			<option value=""></option>
    			<?php /* Load all products into chosen */
    			foreach ($products as $row) {
    				echo "<option id=\"chosenID" . $row->id . "\" value=\"" . $row->id . "\">" . $row->id . ". " . $row->name . "</option>";
				} ?>
    		</select>
    	</div>
	</div>

	<div class="block right_new_order">
		<table id="itemsToBeOrdered" class="table table-hover scrollable_table centered">
			<thead>  <tr> 
				<th>Κωδικός</th>
				<th>Προϊόν</th>
				<th>Βασική τιμή</th>
				<th>Έκπτωση(%)</th>
				<th>Τιμή πελάτη</th>
				<th>Διαγραφή</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($discounts->discounts as $key => $value ){ 
						$jsOnChange = "updateFinalCost($key, ".$products[$key]->sell_value.", value)";
				?>
					
					<tr id="productID<?php echo $key; ?>">
						<input type="hidden" name="products[]" value="<?php echo $key; ?>" /> 
						<td><?php echo $key; ?></td>
						<td><?php echo $discounts->products[$key]->name; ?></td>
						<td><?php echo $products[$key]->sell_value; ?>€</td>
						<td><input class="quantity" type="number" min="0" max="100" step="0.1" name="discounts[]" value="<?php echo $value * 100; ?>" onChange="<?php echo $jsOnChange; ?>" onKeyUp="<?php echo $jsOnChange; ?>"></td>
						<td> <div id="finalCost<?php echo $key; ?>"></div> </td>
						<td><button onClick="removeID(<?php echo $key; ?>)" class="btn btn-mini btn-warning" type="button"> ✗ </button></td>
					</tr>

			<?php } ?>
			</tbody>
		</table>
		<button class="btn btn-primary right" type="submit">Καταχώρηση</button>
	</div>
	<?php if($_CONFIG['FORM_TOKENS']): ?>
	<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
	<?php endif; ?>
	</form>
</div>
	<script type="text/javascript">
		/* Activate chosen */	
		$(".chzn-select").chosen();

		/* Makes sure the final costs are being displayed from the start */
		$(".quantity").change();

		/* Pass $products PHP var into a products JS var :D */
  		var products = <?php echo json_encode($products); ?>;

  		/* Items that are currently in the order table */
  		var inOrder = {};

  		/* Add items from current discounts into inOrder and load their finalCost*/
  		<?php foreach( $discounts->discounts as $key => $value ) { ?>
						inOrder[<?= $key ?>] = true;
		<?php } ?>
  		function removeID(id) {
  			inOrder[id] = false;
  			$("#productID" + id).remove();
  			// $("#chosenID" + id).show();
  			// $("#productsSelect").trigger("liszt:updated");
  		}

  		function updateFinalCost(id, baseCost, discount) {
  			var finalCost = baseCost * (1-(discount/100));
			
			$result = (finalCost.toFixed(4)).replace(/0*$/, '').replace(/\.$/, '');
			if($result.charAt($result.length-2) == '.'){
				$result+='0';
			}
			$result+=" €";
			$("#finalCost" + id).html($result);
  			//$("#finalCost" + id).html((finalCost.toFixed(4)).replace(/0*$/, '').replace(/\.$/, '') + " €");
  		}


		/* Define what happens when an item in chosen is selected */
		$("#productsSelect").chosen().change(function() {			
			var id = $(this).val();

			/* Unselect option */
			$(this).val("");
			$(this).trigger("liszt:updated");

			if(inOrder[id] == true)
				return;
			inOrder[id] = true;

			jsOnChange = 'updateFinalCost('+id+','+products[id].sell_value+', value)';

			$("#itemsToBeOrdered > tbody:first").append( 

						'<tr id="productID'+id+'">' +
							'<input type="hidden" name="products[]" value="'+id+'" />' + 
							'<td>' + id + '</td>' +
							'<td>' + products[id].name + '</td>' +
							'<td>' + products[id].sell_value + '€</td>' +
							'<td><input class="quantity" type="number" min="0" max="100" step="0.1" name="discounts[]" value="0.0" onChange="'+jsOnChange+'" onKeyUp="'+jsOnChange+'"></td>' +
							'<td><div id="finalCost'+id+'"></div></td>' +
							'<td><button onClick="removeID('+id+')" class="btn btn-mini btn-warning" type="button"> ✗ </button> ' + 

						'</tr>'
			);

			updateFinalCost(id, products[id].sell_value, 0);
		});

		function removeProduct(id) {
		}
	</script>



