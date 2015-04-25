<?php
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true);
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
	</form>
</div>
<div class="container">
	<table id="historyTable" class="table table-bordered centered">
		<thead>
			<tr>
				<th>ID</th>
				<th>Προμηθευτής</th>
				<th>Καταχώρηση</th>
				<th>Αναμενόμενη παραλαβή</th>
				<?php if(!checkArr($_GET,"action","receive")){ ?>
					<th>Παραλαβή</th>
				<?php } ?>
				<th>Περισσότερα</th>
			</tr>
		</thead>
		<tbody id="tbody_chosen">
		 <?php foreach($orders as $row) { ?>
			<tr id="<?php echo $row->id ?>" class="click_able">
				<td><?php echo $row->id ?></td>
				<td><b><a href="index.php?page=suppliers&id=<?php echo $row->supplier; ?>"><?php echo $row->supplier_name ?></a></b></td>
				<td><?php echo $row->order_date ?></td>
				<td><?php echo $row->expected_date ?></td>
				<?php if(!checkArr($_GET,"action","receive")){ ?>
					<td><?php echo $row->receipt_date ?></td>
				<?php } ?>
				<td>
					<a href="<?php echo htmlspecialchars($edit_page_url).$row->id; ?>">
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
		</tbody>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function() { 
    /* Here sort the date column desc with the tablesorter plugin */
    $("#historyTable").tablesorter({ 
        sortList: [[2,1]] 
    }); 

	/* Activate datepicker */
	$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
	$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );

}); </script>
