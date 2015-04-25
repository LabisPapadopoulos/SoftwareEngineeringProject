<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	$edit_page_url = "index.php?page=suppliersOrders&action=view&id=";
?>
<div class="container">
	<?php require("view/showMessage.php"); ?>
	<div class="information">
		<!-- Display supplier details -->
		<div class="block" style="width: 48%;">
			<h3>Προβολή Προμηθευτή <?php echo $supplier->fullname; ?></h3>
			<div class="block" >
				<span class="bold">Τηλ: </span><?php echo $supplier->phone_number; ?>
			</div>
			<div class="block right" >
				<span class="bold">Email: </span><?php echo $supplier->email; ?>
			</div>
			<br />
			<div class="block" >
				<span class="bold">Τοποθεσία: </span><?php echo $supplier->location; ?>
			</div>
			<div class="block right" >
				<span class="bold">ΑΦΜ: </span><?php echo $supplier->vat; ?>
			</div>
			<br />
			<a class="left" href="index.php?page=suppliersOrders&amp;action=new&amp;id=<?php echo $supplier->id ?>"><button class="btn btn-primary">Καταχώρηση προμήθειας</button></a>
			<?php if(have_access("admin")): ?>
				<a class="right" href="index.php?page=pricelist&amp;action=add_item&amp;supplier=<?php echo $supplier->fullname; ?>"><button class="btn btn-primary">Προσθήκη Προϊόντος</button></a>
				<br /><br />
				<a class="left" href="index.php?page=suppliers&amp;delete=<?php echo $supplier->fullname; ?>" id="delete-button"><button class="btn btn-danger">Διαγραφή</button></a>
				<a class="right" href="# <?php echo $supplier->id ?>"><button class="btn btn-primary">Προβολή Στατιστικών</button></a>
			<?php endif; ?>
		</div>
		<!-- Display pending orders lists -->
		<div class="block" style="width: 50%;">
		    <div class="block" style="width: 60%;"><h3>Εκκρεμείς προμήθειες</h3></div>
	    	<div class="block right" style="width: 17%;"><a class="show-hide" href="#">+ Σύμπτυξη - Ανάπτυξη</a></div>
	    	<br /><br />
	    	<div class="pending_list">
			    <table class="table stable" id="orders-table">
			    	<tr>
			    		<th>ID</th>
			    		<th>Καταχώρηση</th>
			    		<th>Αναμενόμενη παραλαβή</th>
			            <th>Παραλαβή</th>
			            <th>Περισσότερα</th>
			    	</tr>
			    	<?php foreach($pendingOrders as $row) { ?>
						<tr id="<?php echo $row->id ?>" class="click_able">
							<td><?php echo $row->id ?></td>
							<td><?php echo $row->order_date ?></td>
							<td><?php echo $row->expected_date ?></td>
							<td><?php echo $row->receipt_date ?></td>
							<td> 
								<a href="index.php?page=suppliersOrders&amp;action=view&amp;id=<?php echo $row->id ?>">
									<?php if($row->state == "completed") { ?>
										<button class="btn btn-success" type="button">✔</button> 
									<?php } else { ?>
			                            <button class="btn btn-inverse" type="button">
											<i class="icon-time icon-white"></i>
										</button>
									<?php } ?>
								</a>
							</td>
						</tr>
			    	<?php } ?>
			    </table>
			</div>
	    </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#delete-button').click(function(){ 
				if(confirm("Θέλετε σίγουρα να διαγράψετε τον προμηθευτή;")) 
					return true;
				return false;
			});    
		});
	</script>