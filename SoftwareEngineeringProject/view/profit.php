<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>
<div class="navbar-form">
	<h3>Έσοδα - Έξοδα στο διάστημα <br /><?= $start ?> έως <?= $end ?></h3>
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
	</form>
</div>
<div class="container">
	<table class="table">
		<tr>
			<th>Έσοδα</th>
			<th>Έξοδα</th>
			<th>Κέρδη</th>
		</tr>

		<tr>
			<td><?= max($incomeStats); ?>€</td>
			<td><?= max($supplyCosts); ?>€</td>
			<td><?= max($incomeStats) - max($supplyCosts); ?>€</td>
		</tr>
	</table>

	<div class="demo-container">
		<div id="placeholder" class="demo-placeholder"></div>
	</div>


	
<script type="text/javascript">

	$(function() {

		/* Convenience function that returns the last item of an array */
		Array.prototype.last = function() {
    		return this[this.length-1];
		}	

		/* Activate datepicker */
		$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
		$.datepicker.setDefaults( $.datepicker.regional[ "el" ] );
		$( ".datepicker" ).datepicker();

		var expenditures = [];
		var income = [];
		var total = [];

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

				if( checkArr($supplyCosts, $day ) )
					echo "expenditures.push([dayFlot, -$supplyCosts[$day]]);"; 
				else
					echo "expenditures.push([dayFlot, 0]);";

				if( checkArr($incomeStats, $day ) )
					echo "income.push([dayFlot, $incomeStats[$day]]);";
				else
					echo "income.push([dayFlot, 0]);";
			}
		?>

		var plot = $.plot("#placeholder", [
			{ 
					data: expenditures, 
					label: "κόστος προμηθειών", 
					color: "#BD6F6F"
			},
			{
					data: income, 
					label: "έσοδα από πωλήσεις", 
					color: "#6FBD6F"
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
				min: <?php if(checkVar($supplyCosts)) echo "-" . max($supplyCosts)*1.2; else echo -2; ?>,
				max: <?php if(checkVar($incomeStats)) echo max($incomeStats)*1.2; else echo 2; ?>
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
						item.series.label + " στις " + $.datepicker.formatDate('yy-mm-dd', date) + " = " + y);
				}
			} else {
					$("#tooltip").remove();
					previousPoint = null;            
			}
		});




	});


</script>
</div>
