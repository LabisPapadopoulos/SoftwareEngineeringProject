<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
?>
<?php //TODO add the $edit_page_url ?>
<div class="container">
	<table id="table" class="table">
		<thead>
		<tr>
			<th>ID</th>
			<th>Όνομα</th>
			<th>VAT</th>
			<th>Περιοχή</th>
			<th>Τηλέφωνο</th>
			<th>Email</th>
			<th>Νέα</th>
		</tr>
		</thead>
		<tbody id="tbody_chosen">
		<?php foreach($customers as $row) { ?>
			<tr id="<?php echo $row->id ?>" class="click_able">
				<td><?php echo $row->id ?></td>
				<td><?php echo $row->fullname ?></td>
				<td><?php echo $row->vat ?></td>
				<td><?php echo $row->location ?></td>
				<td><?php echo $row->phone_number ?></td>
				<td><?php echo $row->email ?></td>
				<td>
					<a href="index.php?page=customersOrders&amp;action=new&amp;id=<?php echo $row->id ?>">
						<button class="btn btn-success">
							<i class="icon-plus icon-white"></i>
						</button>
					</a>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$(".click_able").click(
		function(event){
			var test = $(this).attr("id");
			window.location.href = "index.php?page=customersOrders&action=new&id="+test; 
		}
	);
});
</script>