<?php
$con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
mysql_select_db('jeopardy', $con);
date_default_timezone_set('Canada/Pacific');

function startGame($players, $mobile_game) {
  global $con;
  
  foreach ($players as $id => $player) {
    $query = sprintf("INSERT INTO current_game (player_id, player_name, score) VALUES ('%s', '%s', '%s')", $id, $player, 0);
    $res = mysql_query($query, $con);
  }

  $query = sprintf("INSERT INTO game_stats (start_time, number_players, mobile_game) VALUES (NOW(), '%s', '%s')", count($players), $mobile_game);
  $res = mysql_query($query);
}

function addToScore($player_id, $amount) {
  global $con;
  
  $query = sprintf("UPDATE current_game SET score = score + %s WHERE player_id = '%s'", intval($amount), $player_id);
  mysql_query($query);

  $query = "UPDATE game_stats SET rung_in = '0'";
  $res = mysql_query($query);
  releaseButtons();
}

function removeFromScore($player_id, $amount) {
  global $con;
  
  $query = sprintf("UPDATE current_game SET score = score - %s WHERE player_id = '%s'", intval($amount), $player_id);
  mysql_query($query);

  $query = "UPDATE game_stats SET rung_in = '0'";
  $res = mysql_query($query);
  releaseButtons();
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
  while ($row = mysql_fetch_assoc($res)) {
    if ($row['score'] == $winner_score) {
      if ($winner_name != '') {
        $winner_name = $winner_name . ' & ' . $row['player_name'];
      } else {
        $winner_name = $row['player_name'];
      }
    } else if ($row['score'] > $winner_score) {
      $winner_name = $row['player_name'];
      $winner_score = $row['score'];
    }
  }
  
  $query = sprintf("INSERT INTO statistics (winner_name, score, date, game_length, num_players) VALUES ('%s', '%s', NOW(), '%s', '%s')", $winner_name, $winner_score, $game_length, $num_players);
  $res = mysql_query($query);
  
  $query = "DELETE FROM current_game";
  $res = mysql_query($query);

  $query = "DELETE FROM game_stats";
  $res = mysql_query($query);
}

function login($username, $password) {
  global $con;

  $query = sprintf("SELECT * FROM admins WHERE username = '%s'", $username);
  $res = mysql_query($query);
  if ($row = mysql_fetch_assoc($res)) {
    if ($row['password'] == $password) {
      return true;
    } else {
      return false;
    }
  } else {  
    return false;
  }
}

function clearStatistics() {
  global $con;

  $query = "DELETE FROM statistics";
  $res = mysql_query($query);
}

function checkIn($user_id) {
  global $con;

  $query = sprintf("UPDATE current_game SET checked_in = 1 WHERE player_id = '%s'", $user_id);
  $res = mysql_query($query);
}

function ringIn($user_id) {
  global $con;

  $query = "SELECT * FROM current_game";
  $res = mysql_query($query);
  while ($row = mysql_fetch_assoc($res)) {
    if ($row['rung_in'] == 1) {
      return;
    }
  }

  $query = sprintf("UPDATE current_game SET rung_in = '1' WHERE player_id = '%s'", $user_id);
  $res = mysql_query($query);
}

function rungIn() {
  global $con;

  $query = "SELECT * FROM current_game";
  $res = mysql_query($query);
  while($row = mysql_fetch_assoc($res)) {
    if ($row['rung_in'] == 1) {
      return 1 . '_' . $row['player_id'];
    }
  }
  return 0 . '_none' ;
}

function releaseButtons() {
  global $con;

  $query = "UPDATE current_game SET rung_in = '0'";
  $res = mysql_query($query);
}

function cancelGame() {
  global $con;

  $query = "DELETE FROM current_game";
  $res = mysql_query($query);

  $query = "DELETE FROM game_stats";
  $res = mysql_query($query);
}

?>