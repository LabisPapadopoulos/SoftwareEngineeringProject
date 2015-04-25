<?php 
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
//TODO place the variable initialization in the right place *controller 
$form_title = "Επεξεργασία Προμήθειας";
if(have_access("storekeeper") && $order->state != 'completed'){
	$editable = true;
} else {
	$editable = false;
}  ?>

<div class="container">
	<div class="information">
		<div class="block" style="width: 60%;">
			<h3>Προμήθεια <?php echo $order->id; ?></h3>
			<!-- Display supplier details -->
			<div class="block">
				<span class="bold">Καταχώρηση: </span><?php echo $order->order_date; ?>
			</div>
			
			<div class="block right">
				<span class="bold">Αναμενόμενη παραλαβή: </span><?php echo $order->expected_date; ?>
				<input type="hidden" name="date" value="<?php echo $order->expected_date; ?>" form="edit-form" />
			</div>
			<br />
			<div class="block">
				<span class="bold">Παραλαβή: </span><?php echo $order->receipt_date; ?>
			</div>
			<div class="block right" >
				<span class="bold">Κατάσταση: </span>
				<?php if($order->state == "completed") { ?>
					<button class="btn btn-success" type="button">✔</button> 
				<?php } else if($order->state == "cancelled" ) { ?>
					<button class="btn btn-danger" type="button">
						<i class="icon-remove icon-white"></i>
					</button>
				<?php } else { ?>
					<button class="btn btn-inverse" type="button">
						<i class="icon-time icon-white"></i>
					</button>
				<?php } ?>


			</div><br />
			<?php if($editable): ?>
			<div class="block" style="width: 40%;">
				<form action="index.php?page=suppliersOrders&amp;action=edit&amp;id=<?php echo $supplier->id; ?>&amp;order=<?php echo $order->id; ?>" method="post" id="edit-form">
					<input type="hidden" name="hidden" value="2" />
					<button class="btn btn-secondary" type="submit">Επεξεργασία</button>
				</form>
			</div>
			<div class="block" style="width: 40%;">
				<?php if($_CONFIG['FORM_TOKENS']): ?>
					<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
				<?php endif; ?>	
				<a href="index.php?page=suppliersOrders&amp;action=delete&amp;order=<?php echo $_GET['id']; ?>" onclick="return confirm('Are you sure you want to delete this order?')" class="btn btn-danger">Διαγραφή</a>
			</div>
			<?php endif; ?>
		</div>
		<div class="block right" style="width: 35%;">
			<h3>Προμηθευτής <a href="?page=suppliers&id=<?php echo $supplier->id; ?>" target="_blank" ><?php echo $supplier->fullname; ?></a></h3>
			<!-- Display supplier details -->
			<div class="block" >
				<span class="bold">Τηλ: </span><?php echo $supplier->phone_number; ?>
			</div>
			<div class="block right" >
				<span class="bold">Email: </span><?php echo $supplier->email; ?>
			</div>
			<br />
			<div class="block " >
				<span class="bold">ΑΦΜ: </span><?php echo $supplier->vat; ?>
			</div>
		</div>
	</div>

	<?php if(have_access("storekeeper")): ?>
	<form action="index.php?page=suppliersOrders&amp;action=receive&amp;id=<?php echo $_GET['id']; ?>&amp;do=done" method="post">
	<?php endif; ?>

	<!-- Display order products -->
	<h3>Προϊόντα</h3>
	<table id="table" class="table table-hover">
		<tr>
			<th>Κωδικός</th>
			<th>Όνομα</th>
			<th>Ποσότητα παραγγελίας</th>
			<th>Διαθέσιμη Ποσότητα</th>
			<th>Ποσότητα παραλαβής</th>
		</tr>
		<?php foreach($products->product as $product) { ?>
			<tr>
				<td>
					<input type="hidden" name="items[]" value="<?php echo $product ?>" /><?php echo $product ?>
					<!-- values to edit the order -->
					<input type="hidden" name="selector[]" value="<?php echo $product; ?>" form="edit-form" />
					<input type="hidden" name="products_quantity[]" value="<?php echo  $products->quantity[$product]; ?>" form="edit-form" />
				</td>
				<td><?php echo $products->product_name[$product] ?></td>
				<td><input type="hidden" name="desired[]" value="<?php echo $products->quantity[$product] ?>" />
					<?php echo $products->quantity[$product] ?></td>
				<td><?php echo $products->available_quantity[$product] ?></td>
				<td><input name="receipt[]" autocomplete="off" type="text" value="<?php echo $editable ? $products->quantity[$product] : 
											$products->receipt_quantity[$product] ?>" <?php echo $editable ? "" : "disabled=\"disabled\""; ?>/></td>
			</tr>
			
		<?php } ?>	
	</table>
	<?php if($editable): ?>
		<button class="btn btn-primary right" type="submit">Παραλαβή</button>
	<?php endif; ?>
	</form>
</div>