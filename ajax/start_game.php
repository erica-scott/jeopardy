<?php
include('../library/actions.php');
$players = $_POST['players'];
$mobile_game = $_POST['mobile_game'];
startGame($players, $mobile_game);
?>