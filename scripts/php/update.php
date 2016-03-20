<?php
header("Content-type: application/json");
require_once 'classes.php';
$data = array();
$errors = array();
$updates = [
			"email" => NULL,
			"newpassword" => NULL,
			"currentpassword" => NULL
			];

$updates['userid'] = $_POST['userid'];


if (!empty($_POST['email'])) {
	$updates['email'] = trim($_POST['email']);
}
else {
	$updates['email'] = NULL;
}

if (!empty($_POST['newpassword']) && !empty($_POST['newpassword2'])) {
	if ($_POST['newpassword'] == $_POST['newpassword2']) {
		$updates['newpassword'] = trim($_POST['newpassword']);
	}
}
else {
		$updates['newpassword'] = NULL;
	}

if (!empty($_POST['currentpass'])) {
	$updates['currentpassword'] = trim($_POST['currentpass']);
}
else {
	$updates['currentpassword'] = NULL;
}

// VALIDATION COMPLETE.
if (!empty($updates)) {
	$currentPassCheck = UserModel::checkCurrentPassword($updates['userid'], $updates['currentpassword']);

	// IF CURRENT PASSWORD IS A MATCH
	if ($currentPassCheck) {
		$updateCheck = UserModel::updateAccount($updates);
		if ($updateCheck) {
			// echo '<script>alert("Update was successful!")</script>';
			$data['success'] = true;
			$data['message'] = 'Success!';
		}
		else {
			// echo '<script>alert("Whoops! Something went wrong. Please try again later.")</script>';
			$data['success'] = false;
			$data['errors'] = "Update could not be completed. Try again.";
		}
	}
	else {
		$data['errors'] = "Current password is invalid. Try again.";
	} 
}
echo json_encode($data);
?>


