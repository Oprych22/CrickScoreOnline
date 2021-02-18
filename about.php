

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="crickscore.css" />

<title>CrickScore</title>
</head>

<?php
        $dbLocalHost = mysql_connect("localhost", "root", "")
                or die("Could not connect:" . mysql_error());
        
        mysql_select_db("cricketscorer", $dbLocalHost)
                or die("Could not find database: " . mysql_error());
        //echo"<h1>Connected to Database</h1>";
        
        $dbRecords = mysql_query("SELECT player.Player_Name, SUM(delivery.Runs) as Runs FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing where team_ID = 1 group by Player_ID", $dbLocalHost)
                or die("Problem reading table: " . mysql_error());
?>
<body>
  <div id="container">
      <div id="page">
  		
          <div id="header">
          	<div id="headerTitle"></div>
              <div id="headerSubText"></div>
              
          </div>
          <div id="bar">

          	<div class="navLink"><a href="index.php">Home</a></div>
             	<div class="navLink"><a href="about.php">About This Site</a></div>
              <div class="navLink"><a href="search.php">Search</a></div>
        </div>
          <div class="contentTitle"><h1>What is CrickScore?</h1></div>
          <div class="contentText">
          <?php
          
          ?>
            <p>&nbsp;</p>


            <p>CrickScore is the final year project being created as part of the BSc (Hons) Computer Science degree I'm undertaking at Plymouth University. 
              CrickScore is an android application that acts as a digital cricket scorecard. The information recorded can be sent to a server and then viewed on this website! </p>
            <p>&nbsp;</p>
          </div>
          <div class="contentTitle"><h1>The Author</h1></div>
          <div class="contentText">
            <p>Hi, my name is Michael Oprych.</p>
            <p>If you'd like any more information: <a href="mailto:michael.oprych@hotmail.co.uk">Email Me</a> </p>
            <p>&nbsp;</p>
          </div>
      </div>

        <div id="footer"></div>
      </div>
</body>
</html>
