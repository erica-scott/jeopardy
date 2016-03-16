<?php
include('../library/actions.php');
$username = $_POST['username'];
$password = $_POST['password'];

$res = login($username, $password);
if ($res == true) {
	print 1;
} else {
	print 0;
}
?>