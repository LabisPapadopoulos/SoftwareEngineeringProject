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
			<th>Username</th>
			<th>Όνομα</th>
			<th>Τύπος</th>
			<th>Επεξεργασία</th>
		</tr>
      </thead>
	  <tbody id="tbody_chosen">
		<?php foreach($users as $row) { ?>
			<tr id="<?php echo $row->id ?>" class="click_able">
				<td><?php echo $row->id ?></td>
				<td><?php echo $row->username ?></td>
				<td><?php echo $row->fullname ?></td>
				<td>
					<?php if($row->type == "admin")
							echo "Αφεντικό";
						  else if($row->type == "seller")
						    echo "Πωλητής";
						  else if($row->type == "manager")
						    echo "Κουμανταδόρος";
						  else if($row->type == "storekeeper")
						    echo "Αποθηκάριος";
						  else
						    echo "??????";
					?>
				</td>
				<td>
					<a href="<?php echo htmlspecialchars($edit_page_url).$row->id; ?>">
					 	<button class="btn btn-inverse" type="button">
                        	<i class="icon-pencil icon-white"></i>
                     	</button>
                 	</a>
				</td>
			</tr>
		<?php } ?>
	  </tbody>
	</table>
</div>