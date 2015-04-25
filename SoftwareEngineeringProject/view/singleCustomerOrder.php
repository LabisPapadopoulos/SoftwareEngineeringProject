<?php 
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");

if(have_access("seller") && $order->status != "completed"){
	$editable = true;
} else {
	$editable = false;
}  ?>

<script type="text/javascript">
$(document).ready(function(){
	$(".show_hide").show();
	
	$('.show_hide').click(function(){
		$(".slidingDiv").slideToggle();
	});
});
</script>

<div class="container">
	<div class="information">
		<div class="block" style="width: 60%;">
			<h3>Παραγγελία <?php echo $order->id; ?></h3>
			<!-- Display supplier details -->
			<div class="block">
				<span class="bold">Καταχώρηση: </span><?php echo $order->order_date; ?>
			</div>
			
			<div class="block right">
				<span class="bold">Αναμενόμενη ημερομηνία: </span><?php echo $order->expected_date; ?>
				<input type="hidden" name="date" form="edit-form" value="<?php echo $order->expected_date; ?>" />
			</div>
			<br />
			<div class="block">
				<span class="bold">Ημερομηνία αποστολής: </span><?php echo $order->receipt_date; ?>
			</div>
			<div class="block right" >
				<span class="bold">Κατάσταση: </span>
				<?php if($order->status == "completed") { ?>
					<button class="btn btn-success" type="button">✔</button> 
				<?php } else if($order->status == "cancelled") { ?>
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
				<form action="index.php?page=customersOrders&amp;action=edit&amp;order=<?php echo $_GET['id']; ?>&amp;id=<?php echo $order->customer->id; ?>" method="post" id="edit-form">
		            <input type="hidden" name="hidden" value="2" />
		            <button class="btn btn-secondary" type="submit">Επεξεργασία</button>
		        </form>
			</div>
			<div class="block" style="width: 40%;">
				<?php if($_CONFIG['FORM_TOKENS']): ?>
					<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
				<?php endif; ?>	
				<a href="index.php?page=customersOrders&amp;action=delete&amp;order=<?php echo $_GET['id']; ?>" onclick="return confirm('Are you sure you want to delete this order?')" class="btn btn-danger">Διαγραφή</a>
			</div>
			<?php endif; ?>
		</div>
		<div class="block right" style="width: 35%;">
			<h3>Πελάτης <a href="?page=customers&id=<?php echo $order->customer->id; ?>" target="_blank" ><?php echo $order->customer->fullname; ?></a></h3>
			<!-- Display supplier details -->
			<div class="block" >
				<span class="bold">Τηλ: </span><?php echo $order->customer->phone_number; ?>
			</div>
			<div class="block right" >
				<span class="bold">Email: </span><?php echo $order->customer->email; ?>
			</div>
			<br />
			<div class="block" >
				<span class="bold">Τοποθεσία: </span><?php echo $order->customer->location; ?>
			</div>
			<div class="block right" >
				<span class="bold">ΑΦΜ: </span><?php echo $order->customer->vat; ?>
			</div>
		</div>
	</div>

	<?php if(have_access("storekeeper")): ?>
		<form action="index.php?page=customersOrders&amp;action=send&amp;id=<?php echo $_GET['id']; ?>&amp;do=done" method="post">
	<?php endif; ?>
	<!-- Display order products -->
	<h3>Προϊόντα</h3>

	<table id="table" class="table table-hover">
		<thead>
		<tr>
			<th>Κωδικός</th>
			<th>Όνομα</th>
			<?php if($order->status != 'completed'): ?>
				<th>Ποσότητα παραγγελίας</th>
				<th>Δεσμευμένη ποσότητα</th>
				<th>Φυσικό απόθεμα</th>
				<th>Διαθέσιμο απόθεμα</th>
			<?php endif ?>
			<?php if($order->status == 'completed'): ?>
				<th>Αρχική Ποσότητα</th>
				<th>Τελική Πσότητα</th>
			<?php endif ?>
			<th>Αποσταλμένη ποσότητα</th>
		</tr>
		</thead>
		<tbody id="tbody_chosen">
		<?php foreach($order->products as $row) { ?>
			<tr>
				<td>
					<input type="hidden" name="items[]" value="<?php echo $row->id ?>" /><?php echo $row->id ?>
					<input type="hidden" name="selector[]" form="edit-form" value="<?php echo $row->id; ?>" />
				</td>
				<td><?php echo $row->name ?></td>
				
				<?php if($order->status != 'completed'): ?>
					<td>
						<?php echo $row->quantity+$row->wish_quantity; ?>
						<input type="hidden" name="products_quantity[]" value="<?php echo $row->quantity+$row->wish_quantity ?>" form="edit-form" />
					</td>
					<td><?php echo $row->quantity ?></td>
					<td><?php echo $row->total_quantity ?></td>
					<td><?php echo $row->available_quantity ?></td>
					<td><input autocomplete="off" name="receipt[]" type="text" value="<?php echo checkVar($row->completed_quantity) ? $row->completed_quantity : $row->quantity; ?>" <?php echo have_access("storekeeper") ? "" : "disabled=\"disabled\""?>/></td>
				<?php endif ?>
				
				<?php if($order->status == 'completed'): ?>
					<td><?php echo $row->start_quantity ?></td>
					<td><?php echo $row->quantity+$row->wish_quantity; ?></td>
					<td><?php echo $row->completed_quantity; ?></td>
				<?php endif ?>
				
			</tr>
		<?php } ?>
		</tbody>
	</table>
	
	<a href="#" id="show_hide" class="show_hide">Παρατηρήσεις Παραγγελίας</a><br />
		<div class="slidingDiv">
			<textarea name="comments" id="comments" cols="30" rows="6" placeholder="Σχολιάστε τη παραγγελία" ><?php 
							if(!empty($order->comment)){ echo $order->comment;} ?> </textarea>
		</div>

	<?php if(have_access("storekeeper") && $order->status == "incompleted"){ ?>
		<button class="btn btn-primary right" type="submit">Αποστολή</button>
		<?php if($_CONFIG['FORM_TOKENS']){ ?>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
		<?php } ?>
		</form>
	<?php } ?>
</div>