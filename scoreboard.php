<html>
<head>
	<meta http-equiv="refresh" content="15" />
  <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  <script>
    $(document).ready(function() {
    });
  </script>
  <style>
  	td {
  		font-size: 100px;
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
  if (mysql_num_rows($res) > 0) {
  	?> <table width="100%"> <?php
  	while ($row = mysql_fetch_assoc($res)) { ?>
  		<tr>
  			<td width="50%"><?php print $row['player_name']; ?></td>
  			<td width="50%" <?php if (intval($row['score']) < 0) { ?> style="color: red;" <?php } ?> ><?php print $row['score']; ?></td>
  		</tr>
  	<?php }
  	?> </table> <?php
  } else {
  	print 'No games in progress right now!<br/>';
  	$query = "SELECT * FROM statistics ORDER BY date DESC LIMIT 1";
  	$res = mysql_query($query);
  	$row = mysql_fetch_assoc($res);
  	if ($row) {
      print 'The last winner was ' . $row['winner_name'] . ' with ' . $row['score'] . ' points.';
    }
  }
  ?>
</body>