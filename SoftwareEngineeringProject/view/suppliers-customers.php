<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>
<a href="<?php echo $add_new_url; ?>" class="right" >
	<button class="btn btn-success add_new" type="button"><i class="icon-plus-sign icon-white"></i>Προσθήκη</button>
</a>
<div class="container">
	<table id="table" class="table">
	 <thead>
		<tr>
			<th>ID</th>
			<th>Όνομα</th>
			<th>ΑΦΜ</th>
			<th>Περιοχή</th>
			<th>Τηλέφωνο</th>
			<th>Email</th>
			<th>Περισσότερα</th>
		</tr>
     </thead>
	 <tbody id="tbody_chosen">
	  <?php foreach($persons as $row) { ?>
		<tr id="<?php echo $row->id; ?>" class="click_able">
			<td><?php echo $row->id ?></td>
			<td><?php echo $row->fullname ?></td>
			<td><?php echo $row->vat ?></td>
			<td><?php echo $row->location ?></td>
			<td><?php echo $row->phone_number ?></td>
			<td><?php echo $row->email ?></td>
			<td>
				<a href="<?php echo htmlspecialchars($edit_page_url).$row->id; ?>">
    				<button class="btn btn-info">
						<i class="icon-chevron-right icon-white"></i>
    				</button>
				</a>
  			</td>
		</tr>
		<?php } ?>
	 </tbody>
	</table>
</div>
