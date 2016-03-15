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
    });
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
<input type="button" id="new_game" value="Start New Game"><br><br>
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
</body>
</html>