<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>
<div class="form-filter">
	<form method="GET" >
		<input type="hidden" name="page" value="<?php echo $_GET["page"] ?>" >
		<input type="hidden" name="action" value="<?php echo $_GET["action"] ?>" >
	
		<div id="from-date">
			<label class="inline" for="start">Από: </label>
			<input type="text" name="start" id="start" class="datepicker" value="<?php echo $start ?>" >
		</div>
		<div id="to-date">
			<label class="inline" for="end">Έως: </label>
			<input type="text" name="end" id="end" class="datepicker" value="<?php echo $end ?>" >
		</div>
		<div id="submit-date">
			<button class="btn btn-primary right" type="submit" >Φιλτράρισμα</button>
		</div>
	
		<?php if( have_access("admin") ) { ?>
			<div class="right">
				<select name="id" id="sellerSelect" data-placeholder="Διάλεξε πωλητή" class="chzn-select" style="width:300px;" tabindex="2">
		    		<option value="">Όλοι</option>
		    		<?php foreach($sellers as $row) {
			    		echo "<option value=\"$row->id\">$row->fullname</option>";
		    		} ?>
				</select>
			</div>
		<?php } else { ?>
			<input name="id" type="hidden" value="<?php $User->id ?>" />
		<?php } ?>
	</form>
</div>
<div class="container">
	<table id="historyTable" class="table zebra-striped">
		 <thead>
     	 <tr>
			<th>ID</th>
			<th>Πωλητής</th>
			<th>Πελάτης</th>
			<th>Καταχώρηση</th>
			<th>Αναμενόμενη παράδωση</th>
			<th>Παράδωση</th>
			<th>Περισσότερα</th>
		</tr>
		</thead>
		<tbody id="tbody_chosen">
		<?php foreach($orders as $row) { ?>
			<tr id="<?php echo $row->id ?>" class="click_able">
				<td><?php echo $row->id ?></td>
				<td><?php echo $row->seller_name ?></td>
				<td><?php echo $row->customer_name;?></td>
				<td><?php echo $row->order_date ?></td>
				<td><?php echo $row->expected_date ?></td>
				<td><?php echo $row->receipt_date ?></td>
				<td>
					<a href="<?php echo htmlspecialchars($edit_page_url).$row->id; ?>">
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
				</a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function() { 
    
    /* Set chosen to show currently selected seller as default value */
	<?php if(checkArr($_GET, "id")) { ?>
		$("#sellerSelect").val(<?php echo $sellerID ?>);
	<?php } ?>

	/* Activate chosen */
	$(".chzn-select").chosen();

    /* Activate datepicker */
	$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
	$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );


	/* Sort the first column desc with the tablesorter plugin */
    $("#historyTable").tablesorter({ 
        sortList: [[3,1]] 
    });

}); </script>
