<?php
header("Content-type: application/json");
require_once 'classes.php';
$data = array();
$errors = array();
$email = NULL;
$username = NULL;
$password = NULL;

// VALIDATION
if (!empty($_POST['username'])) {
	$username = trim($_POST['username']);
}
else {
	$username = FALSE;
}
if (!empty($_POST['email'])) {
	$email = trim($_POST['email']);
}
else {
	$email = FALSE;
}
if (!empty($_POST['password'])) {
	if (!empty($_POST['confirmpass'])){
		if ($_POST['password'] == $_POST['confirmpass']) {
            $password = trim($_POST['password']);
        }
        else {
        	$password = FALSE;
        }
	}
}
else {
    $password = FALSE;
}

 // IF VALIDATION PASSES
if ($username && $email && $password) {
	// test database for registered email
	// if it's successful insert the new user to database
	$result = UserModel::checkIfEmailExists($email);
	if (empty($result)) {
		// check if username already exists
		$result = UserModel::checkIfUsernameExists($username);
		if (empty($result)) {
			$result = UserModel::createNewUser($username, $password, $email);
			if ($result) {
				$data['success'] = true;
				$data['message'] = 'Success!';
			}
			else {
				$data['success'] = false;
				$data['errors'] = "Registration unsuccessful. Please try again.";
			}
		}
		else {
			$data['errors'] = "Username is already taken. :(";
		}
	}
	else {
		$data['errors'] = "Email has already been registered. :(";
	}
}

echo json_encode($data);
?>