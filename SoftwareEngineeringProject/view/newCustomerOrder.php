<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true);
?>

<script type="text/javascript">
$(document).ready(function(){
	if($('#comment_value').val() == 'undifined')
		$(".slidingDiv").hide();
	else
		$(".slidingDiv").show();
	
	$(".show_hide").show();
	
	$('.show_hide').click(function(){
		$(".slidingDiv").slideToggle();
	});
});
</script>

<div class="container">
	<form action="<?php echo $form_action; ?>" method="post">
	
	<div class="block left_new_order">
		<h3><?php echo $form_title; ?></h3>
		<!-- Display supplier details -->
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

		<!-- Expected date -->
		<div style="text-align: left; margin: 20px 0 5px 0;">
			<h5 style="display: inline-block;">Ημερ. Παράδωσης: </h5>
		 	<input id="expectedDate" name="expectedDate" class="datepicker" type="text" placeholder="YYYY-MM-DD" style="width: 120px; margin: 0 0 0 25px; text-align: right;" required/>
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
		<table id="itemsToBeOrdered" class="table table-hover scrollable_table">
			<thead><tr>
				<th>Κωδικός</th>
				<th>Προϊόν</th>
				<th>Διαθέσιμο</th>
				<th>ΣεΠαραγγελία</th>
				<th>Ποσότητα</th>
				<th>Τιμή</th>
				<th>Διαγραφή</th>
			</tr></thead>
			<tbody>
			<?php if(checkArr($_POST, "hidden")){
				$i = 0;
				foreach($_POST['selector'] as $item){
					$rev = $products[$item]->total_quantity - $products[$item]->available_quantity;
					
					if(array_key_exists($item, $discounts->products))
						$price = $products[$item]->sell_value - $discounts->discounts[$item]*$products[$item]->sell_value;
					else 
						$price = $products[$item]->sell_value;
					
					$quantity = ($_POST['hidden'] == 2) ? $_POST['products_quantity'][$i ++] : 1;
					
					echo "	<tr id=\"productID$item\">
								<td><input type=\"hidden\" class=\"product_id\" name=\"products_id[]\" value=\"$item\" />$item</td>
								<td class=\"product\">{$products[$item]->name}</td>
								<td class=\"available_quantity\">{$products[$item]->available_quantity}</td>
								<td class=\"inOrder\">{$products[$item]->inOrder}</td>
								<td><input type=\"number\" class=\"quantity\" onKeyup=\"updateVisualData();calculateSum();\" onChange=\"updateVisualData();calculateSum();\" name=\"products_quantity[]\" value=\"$quantity\" oninput=\" calculateSum(); \" /></td>
								<td class=\"price\">". round($price, 2) ."</td>
								<td><button onClick=\"removeID($item);\" class=\"btn btn-mini btn-warning\" type=\"button\"> ✗ </button></td>
							</tr>"; 
				}
			} ?>
			</tbody>
		</table>
		
		<a href="#" id="show_hide" class="show_hide">Παρατηρήσεις Παραγγελίας</a><br />
		<div class="slidingDiv">
			<textarea name="comments" id="comments" cols="30" rows="6" placeholder="Σχολιάστε τη παραγγελία" ><?php 
							if(!empty($comment)){ echo $comment;} ?> </textarea>
		</div>
		<input id="comment_value" type="hidden" value=<?php if(!empty($comment)){ echo $comment;}else{ echo 'undifined';} ?> />
		
		<div>
			Σύνολο: <span id="total_cost">0</span>&#8364;
			<button class="btn btn-primary right" type="submit" id="submit"><?php echo $form_button; ?></button>
			<a href="#" class="btn btn-secondary right" id="update">Ενημέρωση Δεδομένων</a>
			<div id="error" class="alert alert-error" style="margin-top: 20px;display: none;"></div>
			<div id="warning" class="alert" style="margin-top: 20px;display: none;"></div>
			<div id="loadingDiv" class="alert" style="margin-top: 20px;display: none;"><img src="view/img/loading.gif" style="height: 1.5em;" alr="Loading..." />Γίνεται φόρτωση...</div>
		</div>
	</div>
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
		<?php }else{ ?>
		 	$(".datepicker").datepicker( "setDate", "<?php echo date("Y-m-d"); ?>" );
		 <?php } ?>
	
		/* Pass $products PHP var into a products JS var :D */
  		var products = <?php echo json_encode($products); ?>;
  		var discount = <?php echo json_encode($discounts->discounts); ?>;
		var intervalMS = 180000, interval;		
		
  		/* Items that are currently in the order table */
  		var inOrder = {};

  		/* Add items from POST data into inOrder */
  		<?php if(checkArr($_POST, "hidden")){
					foreach($_POST['selector'] as $item){  ?>
						inOrder[<?php echo $item ?>] = true;
		<?php } } ?>
	
  		function removeID(id) {
  			inOrder[id] = false;
  			$("#productID" + id).remove();
  			calculateSum();
  			updateVisualData();
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
			<?php //TODO 3)kai edw! ?>
			var reserved = products[id].total_quantity - products[id].available_quantity;
			$("#itemsToBeOrdered > tbody:first").append( "<tr id=\"productID"+id + "\" ><td>" + id + "</td> " +
															  "<td class=\"product\">" + products[id].name + "</td>" +
															  "<td class=\"available_quantity\">" + products[id].available_quantity + "</td>" +
															  "<td class=\"inOrder\">" + products[id].inOrder + "</td>" +
				"  <input type=\"hidden\" class=\"product_id\" name=\"products_id[]\" value=\""+id+"\" /> " + 
				"<td> <input type=\"number\" class=\"quantity\" onKeyup=\"updateVisualData();calculateSum();\" onChange=\"updateVisualData();calculateSum();\" name=\"products_quantity[]\" value=\"1\" oninput=\" calculateSum(); \"  /> </td> " + 
				"<td class=\"price\">"+ Math.round(print_price(id)*100)/100 +"</td>"+
				"<td> <button onClick=\"removeID("+id+");\" class=\"btn btn-mini btn-warning\" type=\"button\"> ✗ </button> </td> " 
				+ "</tr>" );

			calculateSum();
			validateData();
		});
		
		function print_price(id){
			if(discount.hasOwnProperty(id))  
				return products[id].sell_value * (1 - discount[id]); 
			else 
				return products[id].sell_value;
		}

		function calculateSum(){
			var SUM = 0.0;
			$("#itemsToBeOrdered tbody tr").each(function(){ 
				var fl, qua;
				if(!isNaN(fl = parseFloat($(this).find(".price").text())) && !isNaN(qua = parseFloat($(this).find(".quantity").val())))
					SUM += fl * qua;
			});
			$("#total_cost").html(Math.round(SUM*100)/100);
		}

		function updateProductData(){
			//clear the interval to rerun again in 3 mins
			window.clearInterval(interval);
			
			$.get('index.php?method=ajax&action=getProductsDetails&date='+$(".datepicker").val(), function(data){ 
				products = $.parseJSON(data);
				
				//remove all select items
				$("#productsSelect option").remove();

				$("#productsSelect").append($("<option>").text("").attr("value", ""));
				
				$.each($.parseJSON(data), function(i, val){ 
					$("#productsSelect").append($("<option>").text(val.name).attr("value", val.id));
				});
		        
				$("#itemsToBeOrdered tbody .product_id").each(function(){
						var id = $(this).val();
						
						$("#productID"+id+" .price").html(Math.round(print_price(id)*100)/100);
						$("#productID"+id+" .available_quantity").html(products[id].available_quantity);
						$("#productID"+id+" .inOrder").html(products[id].inOrder);
					});
				$(".chzn-select").trigger("liszt:updated");

				updateVisualData();
			});
			interval = window.setInterval(updateProductData, intervalMS);
		}

		function updateVisualData(){
			var error = false;
			var warning = "";
		
			$("#itemsToBeOrdered tbody tr").each(function(){
				$("#itemsToBeOrdered tbody .quantity").each(function(){
					$(this).parent().parent().removeClass("alert-error");
					
					if($(this).val() <= 0){
						$(this).parent().parent().addClass("alert-error");
						error = true;
					}
				});
				
				$(this).removeClass("alert");
				if(parseFloat($(this).find(".available_quantity").html()) + parseFloat($(this).find(".inOrder").html()) < parseFloat($(this).find(".quantity").val())){
					$(this).addClass("alert");
					warning += "-η ποσότητα στο προϊόν "+$(this).find(".product").html()+" δεν είναι διαθέσιμη (λείπουν "+(parseFloat($(this).find(".quantity").val()) - (parseFloat($(this).find(".available_quantity").html()) + parseFloat($(this).find(".inOrder").html())))+")<br/>";
				}
			});

			$("#warning").hide();
			if(warning.length > 0){
				$("#warning").html(warning);
				$("#warning").show("slow");
			}
				
			if(!validateData())
				error = true;
			return error;
		}

		// Expect input as yyyy-mm-dd
		 function isValidDate(inp){
		    try{
		        var d=inp.split(/\D+/);
		        d[0]*=1;
		        d[1]-=1;
		        d[2]*=1;
		        
		        var D=new Date(d[0],d[1],d[2]);
		        
		        if(D.getFullYear()== d[0] && D.getMonth()== d[1] && D.getDate()== d[2]) return D;
		        else throw new Error('The date specified ('+inp+') does not exist');
		    }
		    catch(er){
		        inp='';
		        return false;
		    }
		}

		function validateData(){
			var ret = true;
			var errors = "";

			$("#error").hide("slow");
			
			if($("#itemsToBeOrdered tbody tr").size() <= 0){
				errors += "-Θα πρέπει να εισάγετε προϊόντα πρώτα..<br/>";
				ret = false;
			}

			if(!isValidDate($("#expectedDate").val())){
				errors += "-Θα πρέπει να εισάγετε μια επιτρεπτή ημερομηνία..<br/>";
				ret = false;
			}

			$("#itemsToBeOrdered tbody .quantity").each(function(){
				$(this).parent().parent().removeClass("alert-error");
				
				if($(this).val() <= 0){
					$(this).parent().parent().addClass("alert-error");
					errors += "-Έχετε μη επιτρεπτή ποσότητα στο προϊόν με όνομα "+$(this).parent().parent().find(".product").html()+"<br/>";
					ret = false;
				}
			});
		
			if(errors.length > 0){
				$("#error").html(errors);
				$("#error").show("slow");
			}

			return ret;
		}

		$('#loadingDiv')
		    .hide()  // hide it initially
		    .ajaxStart(function(){$(this).show("slow");})
		    .ajaxStop(function(){$(this).hide("slow");});

		//update data before submit
		$("form").submit(function(){ 
			if(updateVisualData())
				return false;
			return true; 
		});

		$('#expectedDate').change(updateProductData);

		//submit form with double click
		$("#submit").dblclick(function(){
			$("form").submit();
		});

		$("#update").click(updateProductData);
		
		//update data every 3 min
		interval = window.setInterval(updateProductData, intervalMS);
		calculateSum();
		
		$("form").validate({
		   rules: { expectedDate: { required: true, date: true }}
		});
	</script>
