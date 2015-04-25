<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true); 
?>

<div class="container">
	<form action="<?php echo $form_action; ?>" method="post">
	<div class="block left_new_order">
		<h3><?php echo $form_title; ?></h3>
		<!-- Display supplier details -->
		<div class="block" >
			<span class="bold">Προμ: </span>
			<a href="?page=suppliers&id=<?php echo $supplier->id; ?>" target="_blank" ><?php echo $supplier->fullname; ?></a>
			
		</div>
		<div class="block right" >
			<span class="bold">Τηλ: </span><?php echo $supplier->phone_number; ?>
		</div>
		<br />
		<div class="block" >
			<span class="bold">ΑΦΜ: </span><?php echo $supplier->vat; ?>
		</div>
		
		<div class="block right" >
			<span class="bold">Email: </span><?php echo $supplier->email; ?>
		</div>

		<!-- Expected date -->
		<div style="text-align: left; margin: 20px 0 5px 0;">
			<h5 style="display: inline-block;">Ημερ. Παραλαβής: </h5>
		 	<input name="expectedDate" class="datepicker" type="text" placeholder="YYYY-MM-DD" style="width: 120px; margin: 0 0 0 30px; text-align: right;"/>
		</div>
		
		<div style="text-align: left;">	
			<h5>Προσθήκη προϊόντων</h5>
			<select id="productsSelect" data-placeholder="Διάλεξε προϊόν" class="chzn-select" style="width:300px;" tabindex="2">
		    	<option value=""></option> 
		    	<?php /* Load all products into chosen */
		    		foreach ($products as $row) {
		    			echo "<option id=\"chosenID" . $row->id . "\" value=\"" . $row->id . "\">" . $row->id . ". " . $row->name . "</option>";
					} 
				?>
		    </select>
	    </div>
	</div>
	
	<div class="block right_new_order">
		<table id="itemsToBeOrdered" class="table table-hover scrollable_table">
			<thead><tr>
				<th>Κωδικός</th>
				<th>Προϊόν</th>
				<th>Διαθέσιμο</th>
				<th>Φυσικό</th>
				<th>Δεσμευμένο</th>
				<th>Ποσότητα</th>
				<th>Διαγραφή</th>
			</tr>
			</thead>
			<tbody>
			<?php  
			if(checkArr($_POST, "hidden")){

				$i = 0;
				foreach($_POST['selector'] as $item){
					$rev = $products[$item]->total_quantity - $products[$item]->available_quantity;
					
					$quantity = ($_POST['hidden'] == 2) ? $_POST['products_quantity'][$i ++] : 1;
					
					echo "	<tr id=\"productID$item\">
								<td>$item</td>
								<td class=\"product\">{$products[$item]->name}</td>
								<td>{$products[$item]->available_quantity}</td>
								<td>{$products[$item]->total_quantity}</td>
								<td>$rev</td>
								<td>
									<input type=\"number\" class=\"quantity\" name=\"products_quantity[]\" value=\"$quantity\" />
									<input type=\"hidden\" name=\"products_id[]\" value=\"$item\" />
								</td>
								<td>
									<div class=\"centering\">
								  	<button onClick=\"removeID($item)\" class=\"btn btn-mini btn-warning\" type=\"button\"> ✗ </button>
									</div>
								</td>
							</tr>";
					
				}
			} ?>
			</tbody>
		</table>
		<button class="btn btn-primary right" type="submit"><?php echo $form_button; ?></button>
		<div id="error" class="alert alert-error" style="display: none; margin-top: 60px;"></div>
	</div>
	
	
	<!-- <h4>Περιεχόμενα παραγγελίας</h4>
	<select id="orderContents" multiple="multiple">	
	</select>
	 -->	
	 <?php if($_CONFIG['FORM_TOKENS']): ?>
	 <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
	 <?php endif; ?>
</form>
</div>
	<script type="text/javascript">
		/* Activate chosen */	
		$(".chzn-select").chosen();

		/* Activate datepicker */
		$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd", minDate: 0 });
		$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );

		$( ".datepicker" ).datepicker();

<?php if(checkArr($_POST, "hidden", 2) && checkArr($_POST, "date")){ ?>
	$(".datepicker").datepicker( "setDate", "<?php echo $_POST["date"]; ?>" );
<?php } ?>

		/* Pass $products PHP var into a products JS var :D */
  		var products = <?php echo json_encode($products); ?>;

  		/* Items that are currently in the order table */
  		var inOrder = {};

  		/* Add items from POST data into inOrder */
  		<?php if(checkArr($_POST, "hidden")){
					foreach($_POST['selector'] as $item) { ?>
						inOrder[<?php echo $item ?>] = true;
			<?php   }
				} ?>

  		function removeID(id) {
  			inOrder[id] = false;
  			$("#productID" + id).remove();
  			// $("#chosenID" + id).show();
  			// $("#productsSelect").trigger("liszt:updated");
  		}


		/* Define what happens when an item in chosen is selected */
		$("#productsSelect").chosen().change(function() {			
			var id = $(this).val();
			$("#error").hide();

			/* Unselect option */
			$(this).val("");
			$(this).trigger("liszt:updated");

			if(inOrder[id] == true)
				return;
			inOrder[id] = true;
			var reserved = products[id].total_quantity - products[id].available_quantity;
			$("#itemsToBeOrdered > tbody:first").append( "<tr id=\"productID"+id + "\" ><td>" + id + "</td> " +
															  "<td class=\"product\">" + products[id].name + "</td>" +
															  "<td>" + products[id].available_quantity + "</td>" +
															  "<td>" + products[id].total_quantity + "</td>" +
															  "<td>" + reserved + "</td>" +
				"<td> 	<input type=\"number\" class=\"quantity\" name=\"products_quantity[]\" value=\"1\" /> " +
				"		<input type=\"hidden\" name=\"products_id[]\" value=\""+id+"\" /> 						</td> " + 
				"<td> <div class=\"centering\"><button onClick=\"removeID("+id+")\" class=\"btn btn-mini btn-warning\" type=\"button\"> ✗ </button> </div> </td> " 
				+ "</tr>" );



			// $("#productsSelect.option").prop('selected', false);
   //  		$("#productsSelect").trigger('liszt:updated');

			// $("#chosenID" + id).hide();
			// $(this).trigger("liszt:updated");


			// $(this).find('option:contains("All")').remove();
			// $(this).trigger("liszt:updated");
			// $(this).next().removeAttr("selected");


			//$('#' + $(this).val()).show();
		});

		function removeProduct(id) {

		}
		$("form").submit(function(){
			$("#error").hide();
			
			if($("#itemsToBeOrdered tbody tr").size() <= 0){
				$("#error").html("Παρακαλώ εισάγετε προϊόντα για παραγγελία");
				$("#error").show("slow");
				return false;
			}
		});
	</script>

