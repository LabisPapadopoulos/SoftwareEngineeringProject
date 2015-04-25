<?php if(have_access("manager")): ?>
		<button class="btn btn-primary right" type="submit">Παραγγελία</button>
		<input type="hidden" name="hidden" value="1" />
		<?php if($_CONFIG['FORM_TOKENS']): ?>
		<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
		<?php endif; ?>
	</form>
	<div id="warning"></div>
	<script type="text/javascript">

		var selected = new Array();
		var supplier = null;
		var error = false;
		
		$("#supply-form").on('submit', check_suppliers);
		$("#supply-form input[type='checkbox']").change(function(){
			$('#warning').html($(''));

			if(this.checked)
				selected.push($(this).val());
			else
				selected.pop();

			if(selected.length == 1)
				supplier = null;
			
			var sup = $("#selector_id-" + $(this).val()).val();
			$("#supply-form tr").removeClass("alert-error");
			if(supplier == null)
			  supplier = sup;
			if(supplier != sup){
				$('#warning').html($('<div class="alert alert-block">'+
				   		 		'<a class=\"close\" data-dismiss=\"alert\">×</a>'+  
				   		  		'<h4 class=\"alert-heading\">Προσοχή!</h4>'+
				   		 		'Το προϊόν είνα από διαφορετικό προμηθευτή!</div>'));
   		 		
				$(this).parent().parent().addClass("alert-error");
				selected.pop();
				this.checked = false;
			}
			});
		
				function check_suppliers(){
					var selected = new Array();
					var supplier = null;
					var error = false;
	
					$('#warning').html($(''));
					
					$("input:checkbox:checked").each(function(){
						var val = $(this).val();
					    selected.push(val);
					    var sup = $("#selector_id-" + val).val();
					    if(supplier == null)
						    supplier = sup;
					    if(supplier != sup){
							$('#warning').html($('<div class="alert alert-block">'+
						    		 		'<a class=\"close\" data-dismiss=\"alert\">×</a>'+  
						    		  		'<h4 class=\"alert-heading\">Προσοχή!</h4>'+
						    		 		'Έχετε επιλέξει προϊόντα από διαφοτερικούς προμηθευτές!</div>'));
							error = true;
						}
					 });
					 if(error){
						return false;
					 } else {
						$('#supply-form').attr("action", "index.php?page=suppliersOrders&action=new&id=" + supplier);
						return true;
					}
				};

		</script>
<?php endif; ?> 
<div class="container">
	<?php if(have_access("manager")): ?>
		<form id="supply-form" method="post" action="index.php?page=suppliersOrders&amp;action=new&amp;id=">
	<?php endif; ?>
    <table id="table" class="table">
     <thead>
      <tr style="text-align:center;">
        <th style="text-align:center;">Κωδικός</th>
        <th style="text-align:center;">Προϊόν</th>
        <th style="text-align:center;">Ποσότητα Προϊόντων</th>
        <th style="text-align:center;">Δεσμευμένα Προϊόντα</th>
        <th style="text-align:center;">Διαθέσιμα</th>
        <th style="text-align:center;">Σε Παραγγελία</th>
        <?php if(have_access("manager")): ?>
        <th style='text-align:center;'>Επιλογή</th>
        <?php endif; ?>
      </tr>
      </thead>
      <tbody id="tbody_chosen" >
      <?php foreach($table as $row){
		$num = $row->total_quantity - $row->available_quantity;
        echo 	"<tr>
          		<td>{$row->id}</td>
				<td>{$row->name}</td>
          		<td> {$row->total_quantity}</td>
         		<td>{$num}</td>
          		<td>{$row->available_quantity}</td>
                <td>{$row->inOrder}</td>";
        if(have_access("manager"))
        	//TODO here we have to use the product's supplier ID
       		echo    "<td>
       					<input type=\"checkbox\" name=\"selector[]\" value=\"{$row->id}\" id=\"selector-{$row->id}\"/>
       					<input type=\"hidden\" name=\"selector_id[]\" id=\"selector_id-{$row->id}\" value=\"{$row->supplied_by}\" />
        			</td>";
        echo	"</tr>";
      } ?>
      </tbody>
    </table>
</div>