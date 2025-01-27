<?php
	
	session_start();
	
	function logged_in() {
		return isset($_SESSION['D']);
        
	}
	
	function confirm_logged_in() {
		if (!logged_in()) {
?>
			<script type="text/javascript">
				window.location = "login.php";
			</script>
<?php
		}
	}
?>