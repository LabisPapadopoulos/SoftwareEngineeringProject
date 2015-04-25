			<div class="navbar-form" id="search">
				<h3 id="head3" class="block" ><?php echo isset($head3) ? $head3 : ""; ?></h3>
            	<input id="search-input" class="span3" name="anazitish" type="text" placeholder="Αναζήτηση" style="margin: 0px;">
            </div>
            <script type="text/javascript">
            $(document).ready(function() {
                $("#search-input").keyup(function(){
                    $('#tbody_chosen tr').hide();
                    var arr = $("#tbody_chosen tr");
                    arr.each(function(){
                        arr = arr.filter(function(){ return $(this).text().toUpperCase().search($('#search-input').val().toUpperCase().replace(/\s+/g, " ").trim()) >= 0; });
                    });
                    arr.show();	
            	});
            });
            </script>