<?php if(isset($_SESSION['splash']) && $_SESSION['splash'] != '') { 
	//valid splash types are success, warning, info, error
	?>
	<div class="alert alert-<?php echo isset($_SESSION['splash_type']) ? $_SESSION['splash_type'] : 'success'; ?>">
	<button class="close" data-dismiss="alert"></button>
	<?php
		echo $_SESSION['splash'];
		$_SESSION['splash'] = ''; 
	?>
	</div>
<?php } ?>