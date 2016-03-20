<?php
require_once 'scripts/php/classes.php';
if (isset($_POST['btn-Logout'])) {
	User::logoutUser();
}
else if (isset($_POST['btn-Acct'])) {
	header('location: account.php');
	exit();
}
?>
<header>
    <div class="wrapper">
		<h1><a href="index.php">SN!</a></h1>
		<span>Social Network</span>
	    <form class="headerform" method="POST">
	        <input type="submit" value="Edit Account" name="btn-Acct">
	        <input type="submit" value="Logout" name="btn-Logout">
	    </form>
	</div>
</header>