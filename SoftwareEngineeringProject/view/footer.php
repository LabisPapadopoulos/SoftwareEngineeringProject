<?php
	if(!defined("INDEX_CONTROLLER"))
		die("invalid request!");
	define("FOOTER_VIEW", true);
?>

<div id="footer" style="clear: both;">
	<p>Copyright &copy; 2013. - Designed by E-SC team</p>
</div>

<?php //TODO temporary here ?>

<?php if(checkVar($edit_page_url)):?>
<script type="text/javascript">
$(document).ready(function() {
	$(".click_able").click(
		function(event){
			var test = $(this).attr("id");
			window.location.href = "<?php echo $edit_page_url; ?>"+test; 
		}
	);
});
</script>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function() {
	 $("#table").tablesorter({ 
        // sort on the first column and second column, order asc 
        sortList: [[0,0],[1,0]] 
    });
	$(".show-hide").click(function () {
		$(".stable").toggle("slow");
			return false;
	});
	$(".chzn-select").chosen(); 
});
</script>

<!-- <script type="text/javascript"> 
 alert( "Hello World!" ); 
 selectValues = { "1": "rarirarou", "2": "afsdf" };

 	alert( $("#orderContents").length );
 	$("#orderContents").append( "<option>wheeeeeee</option>" );

</script> -->

</body>
</html>