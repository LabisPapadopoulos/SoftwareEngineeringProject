<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	$edit_page_url = "index.php?page=customersOrders&action=view&id=";
?>

<div class="container">
	<?php require("view/showMessage.php"); ?>
	<div class="information">
		<!-- Display customer details -->
		<div class="block" style="width: 48%;">
			<h3>Προβολή Πελάτη <?php echo $customer->fullname; ?></h3>
			<div class="block" >
				<span class="bold">Τηλ: </span><?php echo $customer->phone_number; ?>
			</div>
			<div class="block right" >
				<span class="bold">Email: </span><?php echo $customer->email; ?>
			</div>
			<br />
			<div class="block" >
				<span class="bold">Τοποθεσία: </span><?php echo $customer->location; ?>
			</div>
			<div class="block right" >
				<span class="bold">ΑΦΜ: </span><?php echo $customer->vat; ?>
			</div>
			<br />
			<a class="left" href="index.php?page=customersOrders&amp;action=new&amp;id=<?php echo $customer->id ?>"><button class="btn btn-primary">Καταχώρηση παραγγελίας</button></a>
			<a class="right" href="# <?php echo $customer->id ?>"><button class="btn btn-primary">Προβολή Στατιστικών</button></a>
			<?php if(have_access("admin")): ?>
			<br /><br /><a class="right" href="# <?php echo $customer->id ?>"><button class="btn btn-danger">Διαγραφή</button></a>
			<?php endif; ?>
		</div>
		<!-- Display pending orders lists -->
	    <div class="block" style="width: 50%;">
		    <div class="block" style="width: 60%;"><h3>Εκκρεμείς προμήθειες</h3></div>
	    	<div class="block right" style="width: 17%;"><a class="show-hide" href="#">+ Σύμπτυξη - Ανάπτυξη</a></div>
	    	<br /><br />
	    	<div class="pending_list">
			    <table id="table" class="table stable">
			    	<tr>
			    		<th>ID</th>
			    		<th>Καταχώρηση</th>
			    		<th>Αναμενόμενη παράδωση</th>
			            <th>Παράδωση</th>
			            <th>Περισσότερα</th>
			    	</tr>
			    	<?php foreach($pendingOrders as $row) { ?>
						<tr id="<?php echo $row->id ?>" class="click_able">
							<td><?php echo $row->id ?></td>
							<td><?php echo $row->order_date ?></td>
							<td><?php echo $row->expected_date ?></td>
							<td><?php echo $row->receipt_date ?></td>
							<td> 
								<a href="index.php?page=customersOrders&amp;action=view&amp;id=<?php echo $row->id ?>">
									<?php if($row->status == "completed") { ?>
										<button class="btn btn-success" type="button">✔</button> 
									<?php } else if($row->status == "cancelled") { ?>
										<button class="btn btn-danger" type="button">
											<i class="icon-remove icon-white"></i>
										</button>
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

	<!-- Discounts -->
	<h3>Εκπτώσεις πελάτη</h3>
    <table class="table table-hover">
      	<tr>
        	<th>Προϊόν</th>
        	<th>Βασική τιμή πώλησης</th>
        	<th>Έκπτωση πελάτη</th>
        	<th>Τιμή πελάτη</th>
      	</tr>
		<?php foreach($discounts->discounts as $key => $value ) { ?>
		<tr>
			<td><?php echo $discounts->products[$key]->name ?></td>
			<td><?php echo $discounts->products[$key]->sell_value ?>€</td>
			<td><?php echo $value * 100 ?>%</td>
			<td><?php echo $discounts->products[$key]->sell_value * (1 - $value) ?>€</td>
		</tr>
  	  <?php } ?>
	</table>
	<?php if( checkVar($User->type, "admin") ): ?>
		<a href="index.php?page=discount&amp;action=view&amp;id=<?php echo $customer->id ?>" class="btn btn-primary right">Επεξεργασία εκπτώσεων</a>
	<?php endif; ?>

</div>
