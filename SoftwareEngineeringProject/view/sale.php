<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
?>

<div class="container">
	<table id="table" class="table table-hover">
       <thead>
        <tr>
	        <th>Στήλη 1</th>
	        <th>Στήλη 2</th>	                 
        </tr>
      </thead>
	  <tbody id="tbody_chosen">
      <?php 
	  
				$arr = array(1, 2, 3);
				foreach($arr as &$value){
        echo 	"<tr>
          		<td>Kati</td>
				<td>Kati</td>
          		
              
         		</tr>";
    
      } ?>
      </tbody>
	  
	 </table>
	

</div>
