<?php
include('../library/actions.php');
$id = $_POST['id'];
$amount = $_POST['amount'];

addToScore($id, $amount);
?>