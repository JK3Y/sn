<?php
session_start();
if (isset($_SESSION['id'])) {
	header('location: myhome.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>SN!</title>
        <link rel="stylesheet" href="scripts/style.css" type="text/css" />
        <link rel="stylesheet" href="scripts/jquery/ui/jquery-ui.css">
    </head>
    <body>
    	<?php include('headers/header.php'); ?>
		<div id="content">
			<div class="wrapper">
				<img src="src/large.jpg">
                <div class="panel right">

                	<div class="ui-widget">
						<div class="ui-state-error ui-corner-all" style="display: none;"  align="left" id="errorContainer">
							<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .8em; margin-top: 0;"></span><strong>Error!</strong><ul id="errorList"></ul></p>
						</div>
					</div>

                	<div class="tabs">
                		<ul>
                            <li><a href="#login-tab">Login</a></li>
                			<li><a href="#register-tab">Register</a></li>
                		</ul>

                		<div id="regConfirm"  style="display:none;">
                			<h1>Successfully registered!</h1>
                		</div>
                        
                        <div id="login-tab">
                            <h1>Login to SN!</h1>
                            <form method="POST" id="loginForm">
                                <input type="text" placeholder="Username" name="username" id="unlogin">
                                <input type="password" placeholder="Password" name="password" id="passlogin">
                                <input type="submit" value="Login" id="btnLogin">
                            </form>
                        </div>

                		<div id="register-tab">
		                	<h1>New to SN!?</h1>
							<form method="POST" id="registerForm">
							    <input name="username" 	type="text" 	placeholder="Username" id="username">
							    <input name="email" 	type="text" 	placeholder="Email" id="email">
							    <input name="password" 	type="password" placeholder="Password" id="password">
							    <input name="confirmpass" type="password" placeholder="Confirm Password" id="confirmpass">
							    <input type="submit" 	value="Create Account" id="btnRegister">
							</form>
						</div>

                	</div>

				</div>
			</div>
		</div>
        <?php include_once('headers/footer.php'); ?>
        <script src="scripts/jquery/jquery.js"></script>
        <script src="scripts/jquery/ui/jquery-ui.js"></script>
        <script src="scripts/jquery/validate.js"></script>
        <script src="scripts/js/validation.js"></script>
        <script>
        	$(function() {
        		$('.tabs').tabs({
                    activate: function(event, ui) {
                        var active = $('.tabs').tabs('option', 'active');

                        if ($('.tabs ul>li a').eq(active).attr("href") == '#login-tab') {
                            $('#errorContainer').hide();
                        }
                        else if ($('.tabs ul>li a').eq(active).attr("href") == '#register-tab') {
                            if (($('#errorList').has("li").length > 0)) {
                                $('#errorContainer').show();
                            }
                            else {
                                $('#errorContainer').hide();
                            }
                        }
                        
                    }
                });
        	});   
        </script>
	</body>