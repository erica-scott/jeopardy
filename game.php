<html>
<head>
  <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  <script>
    $(document).ready(function() {
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
        $('.players').each(function() {
          id = $(this).attr('id').split('_')[1];
          name = $(this).val();
          players[id] = name;
        });
        $.ajax({
          url: 'ajax/start_game.php',
          method: 'post',
          data: {players},
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
            window.location.replace('index.php');
          }
        });
      });
    });
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
  </style>
</head>
<body>
  <h1>Welcome to Erica & Sam's Jeopardy Game!</h1>
  <?php
  $con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
  mysql_select_db('jeopardy');
  
  $query = "SELECT * FROM current_game";
  $res = mysql_query($query);
  if ($res != FALSE && mysql_num_rows($res) > 0) { ?>
    <input type="button" id="final_jeopardy" value="Submit Final Jeopardy Scores">
    <input type="button" id="end_game" value="End Game"><br><br>
    <?php
    $query = "SELECT * FROM current_game";
    $res = mysql_query($query);
    $num_rows = mysql_num_rows($res);
    while ($row = mysql_fetch_assoc($res)) {
      $data[] = $row;
    }
    ?> <table width="100%">
      <tr class="header">
        <?php for($i = 0; $i < $num_rows; $i++) { ?>
          <td <?php if ($data[$i]['score'] < 0) { ?> style="color: red;" <?php } ?>><?php print $data[$i]['player_name'] . ' : ' . $data[$i]['score']; ?></td>
        <?php } ?>
      </tr>
      <tr>
        <?php for($i = 0; $i < $num_rows; $i++) { ?>
          <td>
          <table>
            <tr> 
              <td><input type="button" class="plus" id="200_<?php print $data[$i]['player_id']; ?>" value="+200"></td>
              <td><input type="button" class="minus" id="200_<?php print $data[$i]['player_id']; ?>" value="-200"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="400_<?php print $data[$i]['player_id']; ?>" value="+400"></td>
              <td><input type="button" class="minus" id="400_<?php print $data[$i]['player_id']; ?>" value="-400"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="600_<?php print $data[$i]['player_id']; ?>" value="+600"></td>
              <td><input type="button" class="minus" id="600_<?php print $data[$i]['player_id']; ?>" value="-600"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="800_<?php print $data[$i]['player_id']; ?>" value="+800"></td>
              <td><input type="button" class="minus" id="800_<?php print $data[$i]['player_id']; ?>" value="-800"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="1000_<?php print $data[$i]['player_id']; ?>" value="+1000"></td>
              <td><input type="button" class="minus" id="1000_<?php print $data[$i]['player_id']; ?>" value="-1000"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="1200_<?php print $data[$i]['player_id']; ?>" value="+1200"></td>
              <td><input type="button" class="minus" id="1200_<?php print $data[$i]['player_id']; ?>" value="-1200"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="1600_<?php print $data[$i]['player_id']; ?>" value="+1600"></td>
              <td><input type="button" class="minus" id="1600_<?php print $data[$i]['player_id']; ?>" value="-1600"></td>
            </tr>
            <tr> 
              <td><input type="button" class="plus" id="2000_<?php print $data[$i]['player_id']; ?>" value="+2000"></td>
              <td><input type="button" class="minus" id="2000_<?php print $data[$i]['player_id']; ?>" value="-2000"></td>
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
    $query = "SELECT * FROM game_stats";
    $res = mysql_query($query);
    $row = mysql_fetch_assoc($res);

    date_default_timezone_set('Canada/Pacific');

    $game_length = strtotime(date('Y-m-d H:i:s')) - strtotime($row['start_time']);
    $hours = floor($game_length/3600);
    $mins = floor(($game_length - ($hours*3600)) / 60);
    $secs = floor($game_length % 60);

    $game_length = $hours . "hrs : " . $mins . "mins : " . $secs . 'secs';

    print "<b><br>Game Stats: " . $row['number_players'] . ' players have been playing for ' . $game_length . '</b>';
    ?>
  <?php } else { ?>
    <input type="button" id="return" value="Return to Statistics"><br><br>
    <b>Input the names of the players below:</b><br>
    Player Name: <input class="players" type="text" id="player_0"><br>
    Player Name: <input class="players" type="text" id="player_1"><br>
    <div id="more_players"></div>
    <img id="add_player" src="images/plus.png" width="25px" height="25px">
    <input type="hidden" id="next_player" value=2>
    <br><br><input type="button" id="start_game" value="Start Game">
  <?php } ?>
</body>
</html>