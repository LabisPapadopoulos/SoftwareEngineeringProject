<?php 
	if(!defined("MAIN_CONTROLLER"))
		die("invalid request!");
	define("MAIN_VIEW", true);
?>
<a href="<?php echo $add_new_url; ?>" class="right" >
	<button class="btn btn-success add_new" type="button"><i class="icon-plus-sign icon-white"></i> Προσθήκη</button>
</a>
<div class="container">
	<table id="table" class="table">
       <thead>
        <tr>
	        <th>Κωδικός</th>
	        <th>Προϊόν</th>
            <th>Μόναδα μέτρησης</th>
	        <th>Τιμή Αγοράς</th>
	        <th>Τιμή πώλησης</th>
	        <th>Επεξεργασία</th>
        </tr>
      </thead>
      <tbody id="tbody_chosen" style="text-align: center;" >
      <?php $edit_url = htmlspecialchars($edit_page_url); 
      foreach($pricelist as $product){ ?>
          
          <tr id="<?= $product->id ?>" class="click_able">
              <td><?= $product->id ?></td>
              <td><?= $product->name ?></td>
              <td><?= $product->metric_units ?></td>
              <td><?= $product->market_value ?> &#8364;</td>
              <td><?= $product->sell_value ?> &#8364;</td>
              <td><a href="<?= $edit_url . $product->id ?>" >
                    <button class="btn btn-inverse" type="button">
                      <i class="icon-pencil icon-white"></i>
                    </button>
                  </a>
              </td>

      <?php } ?>
      </tbody>
    </table>
</div>