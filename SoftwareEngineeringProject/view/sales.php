<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>
<div class="navbar-form">
	<h3>Αριθμός πωλήσεων</h3>
</div>
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
		<div class="right">
			<select name="sellerID" id="sellerSelect" data-placeholder="Διάλεξε πωλητή" class="chzn-select" style="width:300px;" tabindex="2">
		    	<option value="">Όλοι</option>
		    	<?php foreach($sellers as $row){
			    	echo "<option value=\"$row->id\">$row->fullname</option>";
		    	} ?>
			</select>
		</div>
	</form>
</div>
<div class="container">

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>

	<h3>Έσοδα</h3>
		<div class="demo-container">
			<div id="placeholder2" class="demo-placeholder"></div>
		</div>

	<h3>Στατιστικά για το διάστημα <?= $start ?> - <?= $end ?></h3>

<table id="table" class="table">
       <thead>
        <tr>
	        <th>Πωλητής</th>
	        <th>Πωλήσεις</th>
        </tr>
      </thead>
	  <tbody id="tbody_chosen">
      <?php 
		foreach($seller_stat as $key => $stat){

        	echo 	"<tr>
          			<td>{$stat['seller']->fullname}</td>
					<td>{$stat['orders']}</td>
         			</tr>";
    
      	} ?>
      </tbody>
	  
	 </table>

</div>
<script type="text/javascript">

	$(function() {
		/* Set chosen to show currently selected seller as default value */
		<?php if(checkArr($_GET, "sellerID")) { ?>
			$("#sellerSelect").val(<?php echo $sellerID ?>);
		<?php } ?>


		/* Activate chosen */
		$(".chzn-select").chosen();

		/* Activate datepicker */
		$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
		$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );

		var stats = [];
		var incomeStats = [];

		<?php 
			/* Create an interval between the dates */
			$beginDate = new DateTime($start);
			$endDate = new DateTime($end);

			/* Increment by one day so the endDate is included.. */
			$endDate->modify("+1 day");

			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($beginDate, $interval, $endDate);

			/* For each day betewen start and end... */
			foreach ( $period as $dt ) {
				$day = $dt->format("Y-m-d");
				// $dayFlot = $dt->getTimestamp() * 1000 + 8500000;
				echo "dayFlot = new Date(\"$day\").getTime();";

				if( checkArr($stats, $day ) )
					echo "stats.push([dayFlot, $stats[$day]]);"; 
				else
					echo "stats.push([dayFlot, 0]);"; 

				if( checkArr($incomeStats, $day ) )
					echo "incomeStats.push([dayFlot, $incomeStats[$day]]);";
				else
					echo "incomeStats.push([dayFlot, 0]);";

			}
		?>

		/* Number of purchases graph */
		var plot = $.plot("#placeholder", [
			{ 
				<?php if(!checkVar($sellerID)) { ?>
					data: stats, label: "αριθμός όλων των πωλήσεων", color: "#5EBB5E"
				<?php } else { ?>
					data: stats, label: "αριθμός πωλήσεων από <?php echo $sellers[$sellerID]->fullname ?>", color: "#0067CC"
				<?php } ?>
			}
		], {
			series: {
				lines: {
					show: true
				},
				points: {
					show: true
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			xaxis: {
				mode: "time",
				minTickSize: [1, "day"],
				min: (new Date("<?php echo $start ?>")),
				max: (new Date("<?php echo $end ?>")),
			},
			yaxis: {
				tickDecimals: 0,
				min: 0,
				max: <?php if(checkVar($stats)) echo max($stats)*1.2; else echo 2; ?>
			}
		});

		/* Income graph */
		var plot = $.plot("#placeholder2", [
			{ 
				<?php if(!checkVar($sellerID)) { ?>
					data: incomeStats, label: "έσοδα όλων των πωλητών", color: "#5EBB5E"
				<?php } else { ?>
					data: incomeStats, label: "έσοδα από <?php echo $sellers[$sellerID]->fullname ?>", color: "#0067CC"
				<?php } ?>
			}
		], {
			series: {
				lines: {
					show: true
				},
				points: {
					show: true
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			xaxis: {
				mode: "time",
				minTickSize: [1, "day"],
				min: (new Date("<?php echo $start ?>")),
				max: (new Date("<?php echo $end ?>")),
			},
			yaxis: {
				tickDecimals: 0,
				min: 0,
				max: <?php if(checkVar($incomeStats)) echo max($incomeStats)*1.2; else echo 10; ?>
			}
		});


		function showTooltip(x, y, contents) {
			$("<div id='tooltip'>" + contents + "</div>").css({
				position: "absolute",
				display: "none",
				top: y + 5,
				left: x + 5,
				border: "1px solid #fdd",
				padding: "2px",
				"background-color": "#fee",
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}

		var previousPoint = null;
		$("#placeholder").bind("plothover", function (event, pos, item) {

			if (item) {
				if (previousPoint != item.dataIndex) {

					previousPoint = item.dataIndex;

					$("#tooltip").remove();
					var x = item.datapoint[0].toFixed(2),
					y = Math.floor(item.datapoint[1].toFixed(2));

					date = new Date(parseInt(x));

					showTooltip(item.pageX, item.pageY,
						"αριθμός πωλήσεων " + " στις " + $.datepicker.formatDate('yy-mm-dd', date) + " = " + y);
				}
			} else {
					$("#tooltip").remove();
					previousPoint = null;            
			}
		});

		$("#placeholder2").bind("plothover", function (event, pos, item) {

			if (item) {
				if (previousPoint != item.dataIndex) {

					previousPoint = item.dataIndex;

					$("#tooltip").remove();
					var x = item.datapoint[0].toFixed(2),
					y = Math.floor(item.datapoint[1].toFixed(2));

					date = new Date(parseInt(x));

					showTooltip(item.pageX, item.pageY,
						"έσοδα " + " στις " + $.datepicker.formatDate('yy-mm-dd', date) + " = " + y + "€");
				}
			} else {
					$("#tooltip").remove();
					previousPoint = null;            
			}
		});


	});

	</script>
