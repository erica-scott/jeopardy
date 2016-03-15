<?php
$con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
mysql_select_db('jeopardy');

function startGame($players) {
  global $con;
  
  foreach ($players as $id => $player) {
    $query = sprintf("INSERT INTO current_game (player_id, player_name, score) VALUES ('%s', '%s', '%s')", $id, $player, 0);
    $res = mysql_query($query);
  }
}

function addToScore($player_id, $amount) {
  global $con;
  
  $query = sprintf("UPDATE current_game SET score = score + %s WHERE player_id = '%s'", intval($amount), $player_id);
  mysql_query($query);
}

function removeFromScore($player_id, $amount) {
  global $con;
  
  $query = sprintf("UPDATE current_game SET score = score - %s WHERE player_id = '%s'", intval($amount), $player_id);
  mysql_query($query);
}

function endGame() {
  global $con;
  
  $query = "SELECT * FROM current_game";
  $res = mysql_query($query);
  $winner_score = 0;
  $count = 0;
  while ($row = mysql_fetch_assoc($res)) {
    if ($row['score'] == $winner_score) {
      $count++;
    } else if ($row['score'] > $winner_score) {
      $winner = $row;
      $winner_score = $row['score'];
      $count = 0;
    }
  }
  if ($count == 0) {
    $query = sprintf("INSERT INTO statistics (winner_name, score, date) VALUES ('%s', '%s', NOW())", $winner['player_name'], $winner['score']);
  } else {
    $query = sprintf("INSERT INTO statistics (winner_name, score, date) VALUES ('TIE', '%s', NOW())", $winner_score);
  }
  $res = mysql_query($query);
  
  $query = "DELETE FROM current_game";
  $res = mysql_query($query);
}
?>