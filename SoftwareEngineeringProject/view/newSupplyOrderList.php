<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>
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
	    <?php foreach($suppliers as $row) { ?>
		<tr id="<?php echo $row->id ?>" class="click_able">
			<td><?php echo $row->id ?></td>
			<td><?php echo $row->fullname ?></td>
			<td><?php echo $row->vat ?></td>
			<td><?php echo $row->location ?></td>
			<td><?php echo $row->phone_number ?></td>
			<td><?php echo $row->email ?></td>
			<td>
				<a href="<?php echo htmlspecialchars($edit_page_url).$row->id; ?>">
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