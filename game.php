<html>
<head>
  <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  <script>
    $(document).ready(function() {
      $.ajax({
        url: 'ajax/rung_in.php',
        success: function(data) {
          var rung_in = data.split('_')[0];
          var user_id = data.split('_')[1];
          if (rung_in == 1) {
            $('.ring_in').attr("disabled", true);
            $('.ring_in').css('background-color', 'red');

            var cookie = $('#cookie').val();
            if (user_id == cookie) {
              $('#rung_in_display').show();
            } else {
              $('#rung_in_display').hide();
            }
          }
        }
      });

      $('#return').click(function() {
        window.location.replace('index.php');
      });
      
      $('#add_player').click(function() {
        var i = parseInt($('#next_player').val());
        var html = 'Player Name: <input class="players" type="text" id="player_' + i + '"><br>';
        $('#more_players').append(html);
        $('#next_player').val(i+1);
      });
      
      $('#start_game').click(function() {
        var id;
        var name;
        var players = new Array();
        var mobile_game = 0;
        if ($('#mobile_game').is(":checked")) {
          mobile_game = 1;
        }
        $('.players').each(function() {
          id = $(this).attr('id').split('_')[1];
          name = $(this).val();
          players[id] = name;
        });
        $.ajax({
          url: 'ajax/start_game.php',
          method: 'post',
          data: {players, mobile_game},
          success: function() {
            window.location.reload();
          }
        });
      });
      
      $('.plus').click(function() {
        var amount = $(this).attr('id').split('_')[0];
        var id = $(this).attr('id').split('_')[1];
        $.ajax({
          url: 'ajax/add_to_score.php',
          method: 'post',
          data: {amount, id},
          success: function() {
            window.location.reload();
          }
        });
      });
      
      $('.minus').click(function() {
        var amount = $(this).attr('id').split('_')[0];
        var id = $(this).attr('id').split('_')[1];
        $.ajax({
          url: 'ajax/remove_from_score.php',
          method: 'post',
          data: {amount, id},
          success: function() {
            window.location.reload();
          }
        });
      });
      
      $('#final_jeopardy').click(function() {
          var data = [];
          var i = 0;
          $('.bet').each(function() {
            var id = $(this).attr('id').split('_')[1];
            var amount = $(this).val();
            var plus_minus = $("input[name=correct_" + id + "]:checked").val();
            if (plus_minus == 'no') {
              $.ajax({
                url: 'ajax/remove_from_score.php',
                method: 'post',
                data: {amount, id},
                success: function() {
                  window.location.reload();
                }
              });
            } else {
              $.ajax({
                url: 'ajax/add_to_score.php',
                method: 'post',
                data: {amount, id},
                success: function() {
                  window.location.reload();
                }
              });
            }
          });
      });
      
      $('#end_game').click(function() {
        $.ajax({
          url: 'ajax/end_game.php',
          method: 'post',
          success: function() {
            createCookie('user_id', 'false', 1);
            window.location.replace('index.php');
          }
        });
      });

      $('#player_check_in').click(function() {
        var user_id = $("input:radio[name=player_id_check]").val();
        $.ajax({
          url: 'ajax/check_in.php',
          method: 'post',
          data: {user_id},
          success: function() {
            createCookie('user_id', user_id, 3600);
            window.location.reload();
          }
        });
      });

      $('.ring_in').click(function() {
        var user_id = $('#cookie').val();
        $('#rung_in').val(1);
        $(this).attr("disabled", true);
        $(this).css('background-color', 'red');
        $.ajax({
          url: 'ajax/ring_in.php',
          method: 'post',
          data: {user_id},
          success: function() {
            window.location.reload();
          }
        });
      });

      $('#cancel_game').click(function() {
        $.ajax({
          url: 'ajax/cancel_game.php',
          success: function() {
            window.location.reload();
          }
        });
      });
    });

    function createCookie(name, value, minutes) {
      if (minutes) {
        var date = new Date();
        date.setTime(date.getTime()+(minutes*60*1000));
        var expires = "; expires=" + date.toGMTString();
      } else {
        var expires = "";
      }
      document.cookie = name + "=" + value + expires + "; path=/";
    }
  </script>
  <style>
    .header td {
      font-weight: bold;
    }
    .plus {
      width: 100px;
      background-color: green;
    }
    .minus {
      width: 100px;
      background-color: red;
    }
    .bet {
      width: 100px;
    }
    .ring_in {
      width: 500px; 
      height: 500px; 
      margin: auto; 
      display: block;
      background-color: green;
    }
  </style>
</head>
<body>
  <?php date_default_timezone_set('Canada/Pacific');?>
  <h1>Welcome to Erica & Sam's Jeopardy Game!</h1>
  <?php
  $con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
  mysql_select_db('jeopardy');
  
  $query = "SELECT * FROM current_game";
  $res = mysql_query($query);

  $checked_in = 0;
  $rung_in = 0;
  while ($row = mysql_fetch_assoc($res)) {
    $data[] = $row;
    if ($row['checked_in'] == 1) {
      $checked_in++;
    }
    if ($row['rung_in'] == 1) {
      $rung_in = 1;
      $rung_in_id = $row['player_id'];
    }
  }

  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if (preg_match('/iPhone|BlackBerry/', $user_agent)) {
    $query = "SELECT * FROM game_stats";
    $res = mysql_query($query);
    $game_stat_row = mysql_fetch_assoc($res);
    if (mysql_num_rows($res) > 0) { 
      if ($game_stat_row['mobile_game'] == 1) {
        if ($checked_in == count($data)) {
          $user_id = $_COOKIE['user_id'];
          $query = sprintf("SELECT * FROM current_game WHERE player_id = '%s'", $user_id);
          $user_res = mysql_query($query);
          $row = mysql_fetch_assoc($user_res);
          ?>
          <div id="rung_in_display" style="display:none;"><h2>You were the first to ring in!</h2></div>
          <?php
          print '<h2>' . $row['player_name'] . ', your current score is: ' . $row['score'] . ' (Please refresh the page to update this)</h2><br>';
          ?> 
          <input type="hidden" id="rung_in" value="<?php print $game_stat_row['rung_in']; ?>">
          <input type="hidden" id="cookie" value="<?php print $_COOKIE['user_id']; ?>">
          <input class="ring_in" type="button" id="ringin_<?php print $row['player_id']; ?>"><?php
          $query = "SELECT * FROM game_stats";
          $res = mysql_query($query);
          $row = mysql_fetch_assoc($res);

          $game_length = strtotime(date('Y-m-d H:i:s')) - strtotime($row['start_time']);
          $hours = floor($game_length/3600);
          $mins = floor(($game_length - ($hours*3600)) / 60);
          $secs = floor($game_length % 60);

          $game_length = $hours . "hrs : " . $mins . "mins : " . $secs . 'secs';

          print "<br><br><h2><b>Game Stats: " . $row['number_players'] . ' players have been playing for ' . $game_length . '</b></h2>';
        } else {
          if (isset($_COOKIE['user_id'])) {
            print "Please wait for the rest of the players to check in!";
          } else {
            print "Please choose your name and check in to the game:<br><br>";
            foreach ($data as $row) { ?>
              <?php if ($row['checked_in'] == 0) { ?>
                <input type="radio" name="player_id_check" value="<?php print $row['player_id']; ?>"><?php print $row['player_name']; ?><br><br><br>
              <?php } ?>
            <?php }
            ?> <input type="button" id="player_check_in" value="Check In"> <?php
          }
        }
      } else {
        print "There is a non-mobile game going on right now. Please check back later!";
      }  
    } else {
      ?> 
      <script>
        createCookie('user_id', 'false', 1);
      </script>
      <?php
      print 'No games have been started yet! Please go online on a computer to start a game.<br/>';
      $query = "SELECT * FROM statistics ORDER BY date DESC LIMIT 1";
      $res = mysql_query($query);
      $row = mysql_fetch_assoc($res);
      if ($row) {
        print 'The last winner was ' . $row['winner_name'] . ' with ' . $row['score'] . ' points.';
      }
    }
  } else {
    if ($res != FALSE && mysql_num_rows($res) > 0) { 
      $query = "SELECT * FROM game_stats";
      $res = mysql_query($query);
      $game_stat_row = mysql_fetch_assoc($res);
      if ($checked_in == count($data) || $game_stat_row['mobile_game'] == 0) { ?>
        <input type="button" id="final_jeopardy" value="Submit Final Jeopardy Scores">
        <input type="button" id="end_game" value="End Game">
        <input type="button" id="cancel_game" value="Cancel Game"><br><br>
        <table width="100%">
          <tr class="header">
            <?php for($i = 0; $i < count($data); $i++) { ?>
              <td <?php if ($data[$i]['score'] < 0) { ?> style="color: red;" <?php } ?>><?php print $data[$i]['player_name'] . ' : ' . $data[$i]['score']; ?></td>
            <?php } ?>
          </tr>
          <tr>
            <?php for($i = 0; $i < count($data); $i++) { ?>
              <?php 
              $disabled = false;
              if ($game_stat_row['mobile_game'] == 1 && (($rung_in == 1 && isset($rung_in_id) && $rung_in_id != $data[$i]['player_id']) || ($rung_in == 0))) {
                $disabled = true;
              } ?> 
              <td>
              <table>
                <tr> 
                  <td><input type="button" class="plus" id="200_<?php print $data[$i]['player_id']; ?>" value="+200" <?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="200_<?php print $data[$i]['player_id']; ?>" value="-200" <?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="400_<?php print $data[$i]['player_id']; ?>" value="+400"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="400_<?php print $data[$i]['player_id']; ?>" value="-400"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="600_<?php print $data[$i]['player_id']; ?>" value="+600"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="600_<?php print $data[$i]['player_id']; ?>" value="-600"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="800_<?php print $data[$i]['player_id']; ?>" value="+800"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="800_<?php print $data[$i]['player_id']; ?>" value="-800"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="1000_<?php print $data[$i]['player_id']; ?>" value="+1000"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="1000_<?php print $data[$i]['player_id']; ?>" value="-1000"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="1200_<?php print $data[$i]['player_id']; ?>" value="+1200"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="1200_<?php print $data[$i]['player_id']; ?>" value="-1200"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="1600_<?php print $data[$i]['player_id']; ?>" value="+1600"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="1600_<?php print $data[$i]['player_id']; ?>" value="-1600"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr> 
                  <td><input type="button" class="plus" id="2000_<?php print $data[$i]['player_id']; ?>" value="+2000"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                  <td><input type="button" class="minus" id="2000_<?php print $data[$i]['player_id']; ?>" value="-2000"<?php if($disabled) { ?> disabled <?php } ?> ></td>
                </tr>
                <tr>
                  <td>Final Jeopardy:</td>
                  <td><input type="text" class="bet" id="bet_<?php print $data[$i]['player_id']; ?>"></td>
                </tr>
                <tr>
                  <td>Correct Answer?</td>
                  <td>
                    Yes<input class="correct_answer" type="radio" name="correct_<?php print $data[$i]['player_id']; ?>" value="yes">
                    No<input class="correct_answer" type="radio" name="correct_<?php print $data[$i]['player_id']; ?>" value="no">
                  </td>
                </tr>
              </table>
              </td>
            <?php } ?>
          </tr>
        </table>
        <?php
        date_default_timezone_set('Canada/Pacific');

        $query = "SELECT * FROM game_stats";
        $res = mysql_query($query);
        $row = mysql_fetch_assoc($res);

        $game_length = strtotime(date('Y-m-d H:i:s')) - strtotime($row['start_time']);
        $hours = floor($game_length/3600);
        $mins = floor(($game_length - ($hours*3600)) / 60);
        $secs = floor($game_length % 60);

        $game_length = $hours . "hrs : " . $mins . "mins : " . $secs . 'secs';
        print "<b><br>Game Stats: " . $row['number_players'] . ' players have been playing for ' . $game_length . '</b>';
        ?>
      <?php } else { ?>
        <input type="button" id="cancel_game" value="Cancel Game">
        We are just waiting for all members to check in!
      <?php } ?>
    <?php } else { ?>
      <input type="button" id="return" value="Return to Statistics"><br><br>
      <b>Input the names of the players below:</b><br>
      Player Name: <input class="players" type="text" id="player_0"><br>
      Player Name: <input class="players" type="text" id="player_1"><br>
      <div id="more_players"></div>
      <img id="add_player" src="images/plus.png" width="25px" height="25px"><br>
      <input type="hidden" id="next_player" value=2>
      <input type="checkbox" id="mobile_game">Check this box if you would like to play this game on your mobile phones.
      <br><br><input type="button" id="start_game" value="Start Game">
    <?php } ?>
  <?php } ?>
</body>
</html>