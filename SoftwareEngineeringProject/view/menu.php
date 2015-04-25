        <div class="navbar">
			<div class="navbar-inner">
    			<ul class="nav">
    				<li><a href="index.php" title="Αρχική">Αρχική</a></li>
					<?php 
					if(checkVar($User->type, "manager")):
					//Manager menu bar
					?>
                    <li><a class="active" href="index.php?page=suppliersOrders&amp;action=new" title="Νέα παραγγελία προς τους προμηθευτές">Νέα Προμήθεια</a></li>
                    <li><a href="index.php?page=warehouse" title="Προβολή αποθεμάτων">Αποθέματα</a></li>
                    <li><a href="index.php?page=suppliersOrders&amp;action=history" title="Ιστορικό παραγγελιών προς τους προμηθευτές">Ιστορικό Προμηθειών</a></li>
                    <li><a href="index.php?page=suppliers" title="προβολή όλων των προμηθευτών">Προμηθευτές</a></li>
					<li><a href="index.php?page=warehouse&amp;action=suggest" title="Πρόταση Προμήθειας">Πρόταση Προμήθειας</a></li>
                    <?php 
                    elseif(checkVar($User->type, "seller")): 
                    //seller menu
                    ?>
                    <li><a href="index.php?page=customersOrders&amp;action=new" title="Νέα παραγγελία πελάτη">Νέα Παραγγελία</a></li>
                    <li><a href="index.php?page=customersOrders&amp;action=history&id=<?php echo $User->id; ?>" title="Ιστορικό παραγγελιών">Ιστορικό παραγγελιών</a></li>
                    <li><a href="index.php?page=customers" title="Πελάτες">Πελάτες</a></li>
                    <li><a href="index.php?page=warehouse" title="Αποθέματα">Αποθέματα</a></li>
                    
                    <?php 
                    elseif(checkVar($User->type, "storekeeper")):
                    //storekeeper menu
                    ?>
                    <li><a href="index.php?page=warehouse&amp;action=view" title="Αποθέματα">Αποθέματα</a></li>
                    <li><a href="index.php?page=suppliersOrders&amp;action=receive" title="Παραλαβή νέου αποθέματος">Παραλαβή</a></li>
                    <li><a href="index.php?page=customersOrders&amp;action=send" title="Αποστολή παραγγελιών">Αποστολή</a></li>  
                    <?php 
                    else:
                    //admin menu
                    ?>   
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Λειτουργίες Κουμανταδόρου">Κουμανταδόρος<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    	<li><a class="active" href="index.php?page=suppliersOrders&amp;action=new" title="Νέα παραγγελία προς τους προμηθευτές">Νέα Προμήθεια</a></li>
	                    <li><a href="index.php?page=warehouse" title="Προβολή αποθεμάτων">Αποθέματα</a></li>
	                    <li><a href="index.php?page=suppliersOrders&amp;action=history" title="Ιστορικό παραγγελιών προς τους προμηθευτές">Ιστορικό Προμηθειών</a></li>
	                    <li><a href="index.php?page=suppliers" title="προβολή όλων των προμηθευτών">Προμηθευτές</a></li>
						<li><a href="index.php?page=warehouse&amp;action=suggest" title="Πρόταση Προμήθειας">Πρόταση Προμήθειας</a></li>
                    </ul></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Λειτουργίες Αποθηκάριου">Αποθηκάριος<b class="caret"></b></a>
                    <ul class="dropdown-menu">
	                    <li><a href="index.php?page=warehouse" title="Αποθέματα">Αποθέματα</a></li>
	                    <li><a href="index.php?page=suppliersOrders&amp;action=receive" title="Παραλαβή νέου αποθέματος">Παραλαβή</a></li>
	                    <li><a href="index.php?page=customersOrders&amp;action=send" title="Αποστολή παραγγελιών">Αποστολή</a></li>
                    </ul></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Λειτουργίες Πωλητή">Πωλητής<b class="caret"></b></a>
                    <ul class="dropdown-menu">
	                    <li><a href="index.php?page=customersOrders&amp;action=new" title="Νέα παραγγελία πελάτη">Νέα Παραγγελία</a></li>
	                    <li><a href="index.php?page=customersOrders&amp;action=history" title="Ιστορικό παραγγελιών">Ιστορικό παραγγελιών</a></li>
	                    <li><a href="index.php?page=customers" title="Πελάτες">Πελάτες</a></li>
	                    <li><a href="index.php?page=warehouse" title="Αποθέματα">Αποθέματα</a></li>
                    </ul></li>                    
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Χρήστες">Χρήστες<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    	<li><a href="index.php?page=users&amp;action=all">Προβολή</a></li>
                    	<li><a href="index.php?page=users&amp;action=add">Προσθήκη</a></li>
                    </ul></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Στατιστικά">Στατιστικά<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    	<li><a href="index.php?page=stats&amp;action=profit">Κέρδη</a></li>
                    	<li><a href="index.php?page=stats&amp;action=sales">Πωλήσεις</a></li>
                         <li><a href="index.php?page=stats&amp;action=excel">Συνολικά</a></li>
                    </ul></li>
                    <li><a href="index.php?page=pricelist" title="Τιμοκατάλογος">Τιμοκατάλογος</a></li>
                    <?php 
                    endif;
                    ?>   
   				</ul>
  			</div>
		</div>