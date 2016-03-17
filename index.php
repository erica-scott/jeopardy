<html>
<head>
  <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  <script>
    $(document).ready(function() {
      $('#new_game').click(function() {
        window.location.replace('game.php');
      });

      $('#dialog-login').dialog({
        autoOpen: false,
        buttons: {
          Login: function() {
            var username = $('#username').val();
            var password = $('#password').val();
            $.ajax({
              url: 'ajax/check_login.php',
              method: 'post',
              data: {username, password},
              success: function(data) {
                if (data == 1) {
                  createCookie('username', username, 60);
                  $('#dialog-login').dialog("close");
                  window.location.reload();
                } else {
                  $('#dialog-login').append('<br>Your username or password was incorrect.');
                }
              }
            });
          },
          Cancel: function() {
            $(this).dialog("close");
          }
        }
      });

      $('#login').click(function() {
        $.ajax({
          url: 'ajax/login_form.php',
          success: function(data) {
            $('#dialog-login').html(data);
            $('#dialog-login').dialog('open');
          }
        });
      });

      $('#logout').click(function() {
        createCookie('username', 'false', 1);
        window.location.reload();
      });

      $('#clear_statistics').click(function() {
        $.ajax({
          url: 'ajax/clear_statistics.php',
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
      text-align: center;
    }
    .data td {
      text-align: center;
    }
  </style>
</head>
<body>
<h1>Welcome to Erica & Sam's Jeopardy Game!</h1>
<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/iPhone|BlackBerry/', $user_agent)) {
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . 'game.php');
} else { ?>
  <input type="button" id="new_game" value="Start New Game">
  <?php if(isset($_COOKIE['username']) && $_COOKIE['username'] != 'false') : ?>
    <input type="button" id="clear_statistics" value="Clear Statistics">
    <input type="button" id="logout" value="Logout">
  <?php else: ?>
    <input type="button" id="login" value="Login">
  <?php endif; ?>
  <br><br>
  <?php
  $con = mysql_connect('localhost', 'escott', 'Silas2727_') or die('Could not connect: ' . mysql_error());
  mysql_select_db('jeopardy');
  $query = "SELECT * FROM statistics ORDER BY score";
  $stat_res = mysql_query($query);
  if ($stat_res != FALSE) { ?>
    <table border=1 width=60% style="border-collapse:collapse;">
      <tr class="header">
        <td width="30%">Name</td>
        <td width="15%">Score</td>
        <td width="25%">Date</td>
        <td width="25%">Game Length</td>
        <td width="5%">Num Players</td>
      </tr>
      <?php while ($row = mysql_fetch_assoc($stat_res)) { ?>
        <tr class="data">
          <td><?php print $row['winner_name']; ?></td>
          <td><?php print $row['score']; ?></td>
          <td><?php print $row['date']; ?></td>
          <td><?php print $row['game_length']; ?></td>
          <td><?php print $row['num_players']; ?></td>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
  <div id="dialog-login" title="Login"></div>
<?php } ?>
</body>
</html>