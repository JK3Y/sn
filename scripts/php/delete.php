<?php
session_start();
header("Content-type: application/json");
$postid = $_POST['postid'];
$userid = $_SESSION['id'];
require_once 'classes.php';
$data = array();

$deleteCheck = PostModel::deletePost($userid, $postid);
if ($deleteCheck) {
	$data['success'] = true;
}
else {
	$data['success'] = false;
}
echo json_encode($data);
?>