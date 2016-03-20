<?php
session_start();
header("Content-type: application/json");
require_once 'classes.php';
$userid = $_SESSION['id'];
$followingid = $_POST['userid'];
$data = array();

$followCheck = UserModel::unfollowUser($userid, $followingid);

if ($followCheck) {
	$data['success'] = true;
}
else {
	$data['success'] = false;
}
echo json_encode($data);
?>