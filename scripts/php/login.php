<?php
session_start();
header("Content-type: application/json");
require_once 'classes.php';
$data = array();

// VALIDATION
if (!empty($_POST['username'])) {
	$username = trim($_POST['username']);
}
else {
	$username = FALSE;
}
if (!empty($_POST['password'])) {
	$password = trim($_POST['password']);
}
else {
	$password = FALSE;
}

// If validation passes
if ($username && $password) {
	// check username/pass against database
	$validCheck = UserModel::checkLoginCredentials($username, $password);
	if ($validCheck) {
		// IF USERNAME AND PASSWORD ARE VALID LOGIN
		$data['success'] = true;
	}
	else {
		$data['success'] = false;
		$data['errors'] = "The username and password combination do not match our records. Please try again.";
	}
}

echo json_encode($data);
?>