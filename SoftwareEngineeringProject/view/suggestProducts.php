<?php 
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true);
?>

<div class="container">

    <table id="table" class="table">
     <thead>
      <tr style="text-align:center;">
        <th style="text-align:center;">Κωδικός</th>
        <th style="text-align:center;">Προϊόν</th>
        <th style="text-align:center;">Προμηθευτής</th>
        <th style="text-align:center;">Οριακή Ποσότητα</th>
        <th style="text-align:center;">Διαθέσιμο Απόθεμα</th>
        <th style="text-align:center;">Ποσότητα Επιθυμίας</th>
      </tr>
      </thead>
      <tbody id="tbody_chosen" >
      <?php foreach($products as $product){
        echo 	"<tr>
          		<td>{$product->id}</td>
				<td>{$product->name}</td>
          		<td>{$product->supplier}</td>
         		<td>{$product->limit}</td>
          		<td>{$product->available_quantity}</td>
                <td>{$product->wish_quantity}</td>";
      } ?>
      </tbody>
    </table>
</div>

