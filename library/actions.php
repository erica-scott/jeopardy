<?php
$con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
mysql_select_db('jeopardy', $con);
date_default_timezone_set('Canada/Pacific');

function startGame($players) {
  global $con;
  
  foreach ($players as $id => $player) {
    $query = sprintf("INSERT INTO current_game (player_id, player_name, score) VALUES ('%s', '%s', '%s')", $id, $player, 0);
    $res = mysql_query($query, $con);
  }

  $query = sprintf("INSERT INTO game_stats (start_time, number_players) VALUES (NOW(), '%s')", count($players));
  $res = mysql_query($query);
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

  $query = "SELECT * FROM game_stats";
  $res = mysql_query($query);
  $row = mysql_fetch_assoc($res);
  $start_time = $row['start_time'];
  $num_players = $row['number_players'];

  $game_length = strtotime(date('Y-m-d H:i:s')) - strtotime($start_time);
  $hours = floor($game_length/3600);
  $mins = floor(($game_length - ($hours*3600)) / 60);
  $secs = floor($game_length % 60);

  $game_length = $hours . "hrs : " . $mins . "mins : " . $secs . 'secs';
  
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
    $query = sprintf("INSERT INTO statistics (winner_name, score, date, game_length, num_players) VALUES ('%s', '%s', NOW(), '%s', '%s')", $winner['player_name'], $winner['score'], $game_length, $num_players);
  } else {
    $query = sprintf("INSERT INTO statistics (winner_name, score, date, game_length, num_players) VALUES ('TIE', '%s', NOW(), '%s', '%s')", $winner_score, $game_length, $num_players);
  }
  $res = mysql_query($query);
  
  $query = "DELETE FROM current_game";
  $res = mysql_query($query);

  $query = "DELETE FROM game_stats";
  $res = mysql_query($query);
}
?>