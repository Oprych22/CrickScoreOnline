

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="crickscore.css" />

<title>CrickScore</title>
</head>


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
        <div class="contentTitle"><h1>Match Results</h1></div>
        <div class="contentText">

            <?php
        

          $matchID = $_GET['matchID'];

          
          switch($_SERVER['REQUEST_METHOD'])
          {
            case 'GET':
            
            {
                  $dbLocalHost = mysql_connect("localhost","root",""); 

    
                  mysql_select_db("CricketScorer",  $dbLocalHost)
                   or die("Could not find database: " . mysql_error()); 
                  
                  $getMatchInfo = mysql_query("SELECT home_team, away_team FROM Match_day Where match_ID = " . $matchID, $dbLocalHost); 

                  while($rowHigher = mysql_fetch_array($getMatchInfo))
                  {


                    // HOME TEAM 
                    //GET NAMES AND PLAYER IDs
                    $dbRecords = mysql_query("SELECT player.Player_Name, player.Player_ID, SUM(delivery.Runs) as Runs, COUNT(delivery.Runs) as Balls
                      FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing where team_ID = ". $rowHigher['home_team']  . " group by Player_ID", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());






                 
                  
                  //echo $homeTeamRuns;

                  $homeTeamNames = mysql_query("SELECT team_name  FROM Team 
                                  where team_ID = ". $rowHigher['home_team'], $dbLocalHost);
                  $homeTeamRuns = mysql_query("SELECT SUM(delivery.Runs) as Runs FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                  where team_ID = ". $rowHigher['home_team'] . " and match_ID = " . $matchID, $dbLocalHost);
                  // GET WICKET NUMBER

                  $homeWicketNumber = mysql_query("SELECT  SUM(delivery.wicket) AS wickets  FROM delivery INNER JOIN PLAYER  ON player.player_id=delivery.player_Facing where team_ID = ". $rowHigher['home_team'] . " and match_ID = " . $matchID . " AND delivery.wicket > 0 ", $dbLocalHost)
                    or die("Problem reading table: " . mysql_error());
                  $numHomeWickets=mysql_num_rows($homeWicketNumber);
                  if ($numHomeWickets > 0)
                  {
                    while($rowWickets = mysql_fetch_array($homeWicketNumber))
                      {
                        global $totalWicketsHome;
                        $totalWicketsHome = $rowWickets['wickets'];

                      }
                  }
                  else
                  {
                      global $totalWicketsHome;
                        $totalWicketsHome = 0;
                  }


                  echo "<tr class=\"head\">";

                  while($rowNames = mysql_fetch_array($homeTeamNames))
                  {
                    global $homeTeamName;
                    $homeTeamName = $rowNames['team_name'];
                    echo "<p>" . $rowNames['team_name'];
                  }

                  echo " - ";
                  while($row = mysql_fetch_array($homeTeamRuns))
                  {
                    $homeExtras = mysql_query("SELECT SUM(delivery.extras) as extras, delivery.extra_type from DELIVERY INNER JOIN player ON player.player_id=delivery.player_Facing where team_ID = ". $rowHigher['home_team'] . " and match_ID = " . $matchID . " AND delivery.extras > 0 ", $dbLocalHost)
                    or die("Problem reading table: " . mysql_error());
                    $numExtras=mysql_num_rows($homeExtras);
                    if ($numExtras > 0)
                    {
                      while($rowExtras = mysql_fetch_array($homeExtras))
                      {
                        global $totalRunsHome;
                        $totalRunsHome = $row['Runs'] +  $rowExtras['extras'];
                        echo $totalRunsHome . "-" . $totalWicketsHome ;
                      }
                    }
                    else
                    {
                      global $totalRunsHome;
                        $totalRunsHome = $row['Runs'];
                      echo $totalRunsHome . "-" . $totalWicketsHome;

                    }
                  }

                  echo "</br>";
                  $awayTeamNames = mysql_query("SELECT team_name  FROM Team 
                                  where team_ID = ". $rowHigher['away_team'], $dbLocalHost);
                  $awayTeamRuns = mysql_query("SELECT SUM(delivery.Runs) as Runs FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                  where team_ID = ". $rowHigher['away_team'] . " and match_ID = " . $matchID, $dbLocalHost);
                  // GET WICKET NUMBER

                  $awayWicketNumber = mysql_query("SELECT  SUM(delivery.wicket) AS wickets  FROM delivery INNER JOIN PLAYER  ON player.player_id=delivery.player_Facing where team_ID = ". $rowHigher['away_team'] . " and match_ID = " . $matchID . " AND delivery.wicket > 0 ", $dbLocalHost)
                    or die("Problem reading table: " . mysql_error());
                  $numAwayWickets=mysql_num_rows($awayWicketNumber);
                  if ($numAwayWickets > 0)
                  {
                    while($rowWickets = mysql_fetch_array($awayWicketNumber))
                      {
                        if ($rowWickets['wickets'] < 1)
                        {
                          global $totalWicketsAway;
                          $totalWicketsAway = 0;
                          break;
                          

                        }
                        global $totalWicketsAway;
                        $totalWicketsAway = $rowWickets['wickets'];

                      }
                  }
                  else
                  {
                      global $totalWicketsAway;
                      $totalWicketsAway = 0;
                  }


                  echo "<tr class=\"head\">";

                  while($rowNames = mysql_fetch_array($awayTeamNames))
                  {
                    global $awayTeamName;
                    $awayTeamName = $rowNames['team_name'];
                    echo "<p>" . $rowNames['team_name'];
                  }

                  echo " - ";
                  while($row = mysql_fetch_array($awayTeamRuns))
                  {
                    $awayExtras = mysql_query("SELECT SUM(delivery.extras) as extras, delivery.extra_type from DELIVERY INNER JOIN player ON player.player_id=delivery.player_Facing where team_ID = ". $rowHigher['away_team'] . " and match_ID = " . $matchID . " AND delivery.extras > 0 ", $dbLocalHost)
                    or die("Problem reading table: " . mysql_error());
                    $numExtras=mysql_num_rows($awayExtras);
                    if ($numExtras > 0)
                    {
                      while($rowExtras = mysql_fetch_array($awayExtras))
                      {
                        global $totalRunsAway;
                        $totalRunsAway = $row['Runs'] +  $rowExtras['extras'];
                        echo $totalRunsAway . "-" . $totalWicketsAway ;
                      }
                    }
                    else
                    {
                      global $totalRunsAway;
                        $totalRunsAway = $row['Runs'];
                        if ($totalRunsAway == 0 && $totalWicketsAway == 0)
                        {
                            echo "Yet to Bat";
                        }
                        else
                        {
                          echo $totalRunsAway . "-" . $totalWicketsAway;
                        }

                    }
                  }

                  // CURRENT SCORE 
                  $currentOver = mysql_query("SELECT MAX(Number) AS currentOver FROM over where team_bowling = ". $rowHigher['away_team'] . " and match_ID = " . $matchID , $dbLocalHost)
                   or die("Problem reading table: " . mysql_error());

                  echo "</br>";
                  if ($totalRunsAway > $totalRunsHome)
                  {
                    $wicketsWonBy = 10 - $totalWicketsAway;
                    echo $awayTeamName . " won by " .  $wicketsWonBy . " wickets";
                  }
                  else if ($totalRunsAway < $totalRunsHome && $numAwayWickets == 10) 
                  {
                    $runsWonBy = $totalRunsHome - $totalRunsAway;
                    echo $homeTeamName . " won by " .  $runsWonBy . " runs";
                  }
                  else
                  {
                    echo "In Play </br>";
                    while($rowCurrentOver = mysql_fetch_array($currentOver))
                    {
                       echo "Current Over - " . $rowCurrentOver['currentOver'];
                     }

                  }



                   $i = 0;


                  echo "<table>";
                  $homeTeamNames = mysql_query("SELECT team_name  FROM Team 
                                  where team_ID = ". $rowHigher['home_team'], $dbLocalHost);
                  echo "<tr class=\"head\">";
                  while($rowNames = mysql_fetch_array($homeTeamNames))
                  {
                    echo "<td>". $rowNames['team_name'] . "</td>";
                  }
                  echo "<td></td>";
                  echo "<td>R</td>";
                  echo "<td>B</td>";
                  echo "<td>4s</td>";
                  echo "<td>6s</td>";
                  echo "</tr>";
                   while($row = mysql_fetch_array($dbRecords))
                  {
                    $playerID = $row['Player_ID'];
                    $i++;
                    echo "<tr class=\"d".($i & 1)."\">";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $row['Player_Name'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $wicketTaken = mysql_query("SELECT * FROM delivery INNER JOIN OVER ON delivery.over_number=over.over_ID 
                      INNER JOIN player ON over.player_bowling = player.player_id where delivery.wicket=1 AND delivery.player_dismissed = " . $playerID . " AND over.match_id = " . $matchID, $dbLocalHost);
                    $numWicket=mysql_num_rows($wicketTaken);
                     while($wicketRow = mysql_fetch_array($wicketTaken))
                    {
                       

                      switch ($wicketRow['Wicket_Type'])
                      {
                        case "Bowled":

                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp B " . $wicketRow['Player_Name']. " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        break;

                        case "Stumped":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] . " AND delivery.match_id =  ". $matchID, $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp  B " . $wicketRow['Player_Name'] .  "   st  " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;

                        case "LBW":

                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp LBW " . $wicketRow['Player_Name']. " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        break;

                        case "Run Out":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] . " AND delivery.match_id =  ". $matchID, $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp ro " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;

                        case "Caught":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] , $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp  B " . $wicketRow['Player_Name'] .  "  c  " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;
                      }

                      
                      
                    }
                    if ($numWicket == 0 &&  $row['Balls'] == 0)
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }
                    else if ($numWicket == 0) 
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Not Out &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }

                    $legByes = mysql_query("SELECT player.player_name, delivery.extras
                      FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing where player.player_ID = " . $playerID  ." AND delivery.extra_type = \"Leg-Bye(s)\" AND delivery.match_id =  ". $matchID  ."  group by Player_ID ", $dbLocalHost);;
                     // echo $legByes;
                    $numLegByes=mysql_num_rows($legByes);
                    if ($numLegByes > 0)
                    {
                      while($legByesCalc = mysql_fetch_array($legByes))
                        {
                          $total = $row['Runs'] + $legByesCalc['extras'];
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $total . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                      
                    }
                    else if ($row['Runs'] > 0 )
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $row['Runs'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                      
                    }
                    else
                    {
                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . "0" . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td>";
                    }

                    if ($row['Balls'] > 0)
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $row['Balls'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }
                    else 
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td>";
                    }


                    $foursScored = mysql_query("SELECT player.player_ID, COUNT(delivery.RUNS) AS 4s   
                                                FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                                where team_ID = ". $rowHigher['home_team'] . " and delivery.RUNS = 4 and player_Name = \"". $row['Player_Name'] .  "\"", $dbLocalHost);
                     while($rowFours = mysql_fetch_array($foursScored))
                    {

                        if ($rowFours['4s'] > 0)
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $rowFours['4s'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";

                        }
                        else
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                    }

                    $sixesScored = mysql_query("SELECT player.player_ID, COUNT(delivery.RUNS) AS 6s   
                                                FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                                where team_ID = ". $rowHigher['home_team'] . " and delivery.RUNS = 6 and player_Name = \"". $row['Player_Name'] .  "\"", $dbLocalHost);
                     while($rowSixes = mysql_fetch_array($sixesScored))
                    {

                        if ($rowSixes['6s'] > 0)
                        { 
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $rowSixes['6s'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";

                        }
                        else
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                    }


                     echo "</tr>";       

                 }
                 echo "<tr >";
                 echo "<td></td>";
                 echo "<td></td>";
                  
                    echo "<td class=\"black\"> " . $totalRunsHome . "</td>";
                  echo "</tr>";
                 echo "</table>";

                 // BOWLING

                 echo "<table>";
                  echo "<tr class=\"head\">";
                  echo "<td></td>";
                  echo "<td>O</td>";
                  echo "<td>M</td>";
                  echo "<td>R</td>";
                  echo "<td>W</td>";

                  $awayBowlingFigures = mysql_query("SELECT player.Player_Name, SUM(delivery.runs) AS Runs,  SUM(delivery.extras) AS Extras, COUNT(DISTINCT over.over_id ) AS Overs, SUM(delivery.wicket) FROM player 
                    INNER JOIN over ON over.player_bowling = player.player_id INNER JOIN delivery ON delivery.over_number = over.over_id  where team_ID = ". $rowHigher['away_team'] . " and delivery.match_ID = " . $matchID . " GROUP BY player.player_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());


                  $i = 0;

                  while($rowBowl = mysql_fetch_array($awayBowlingFigures))
                  {
                    $i++;
                    echo "<tr class=\"d".($i & 1)."\">";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $rowBowl['Player_Name'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                    $totalRunsBowlerAway = $rowBowl['Runs'] + $rowBowl['Extras'];
                    
                    
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $rowBowl['Overs'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $awayBowlingFiguresMaidens = mysql_query("SELECT player.player_name, SUM(delivery.runs) AS Runs, SUM(delivery.extras) as Extras FROM Over 
                      INNER JOIN player ON over.player_bowling=player.player_ID INNER JOIN delivery ON over.over_id=delivery.over_number  where team_ID = ". 
                      $rowHigher['away_team'] . " and delivery.match_ID = " . $matchID . " and player.player_name = \"" . $rowBowl['Player_Name']  . "\" GROUP BY over_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  global $howManyMaidens;
                  $howManyMaidens = 0;
                   while($rowBowlMaidens = mysql_fetch_array($awayBowlingFiguresMaidens))
                  {
                    
                    if ($rowBowlMaidens['Runs'] == 0 && $rowBowlMaidens['Extras'] == 0)
                    {
                        $howManyMaidens++;
                    }
                  }
                    echo "<td> &nbsp &nbsp &nbsp &nbsp ". $howManyMaidens . " &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $totalRunsBowlerAway . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $awayBowlingFiguresWickets = mysql_query("SELECT player.player_name, SUM(delivery.wicket) AS Wickets FROM Over INNER JOIN player ON over.player_bowling=player.player_ID 
                      INNER JOIN delivery ON over.over_id=delivery.over_number  WHERE delivery.wicket_type <> \"RUN OUT\"  AND team_ID = ". 
                      $rowHigher['away_team'] . " and delivery.match_ID = " . $matchID . " and player.player_name = \"" . $rowBowl['Player_Name']  . "\" GROUP BY over_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  $numWickets=mysql_num_rows($awayBowlingFiguresWickets);
                  if ($numWickets == 0)
                  {
                       echo "<td> &nbsp &nbsp &nbsp &nbsp 0 &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                  }

                  while($rowBowlWickets = mysql_fetch_array($awayBowlingFiguresWickets))
                  {
                   
                      echo "<td> &nbsp &nbsp &nbsp &nbsp "  . $rowBowlWickets['Wickets']  . "  &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                  }

                    echo "</tr>";
                  }


                  echo "</table>";

                  $awayExtrasTotal = mysql_query("SELECT delivery.extras, delivery.extra_type FROM Over INNER JOIN player ON over.player_bowling=player.player_ID INNER JOIN 
                    delivery ON over.over_id=delivery.over_number WHERE extra_type <> \"NULL\"   AND team_ID = ". 
                      $rowHigher['away_team'] . " and delivery.match_ID = " . $matchID . " GROUP BY delivery.extra_type", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  global $awayLegByes;
                  global $awayByes;
                  global $awayWides;
                  global $awayNoBalls;
                  $awayLegByes = 0;
                  $awayByes = 0;
                  $awayWides = 0;
                  $awayNoBalls = 0;

                   while($rowBowlExtras = mysql_fetch_array($awayExtrasTotal))
                  {
                    if ($rowBowlExtras['extra_type'] == "No Ball")
                    {
                      $awayNoBalls += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Wide")
                    {
                      $awayWides += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Leg-Bye(s)")
                    {
                      $awayLegByes += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Bye(s)")
                    {
                      $awayByes += $rowBowlExtras['extras'];
                    }
                      
                  }
                  echo "<p>Extras: lb ". $awayLegByes . ", b " . $awayByes . ", nb " . $awayNoBalls . ", w " . $awayWides . "</p>";


                // AWAY TEAM

                echo "</br>";
                echo "</br>";
                echo "</br>";
                   $dbRecords = mysql_query("SELECT player.Player_Name, player.Player_ID, SUM(delivery.Runs) as Runs, COUNT(delivery.Runs) as Balls
                      FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing where team_ID = ". $rowHigher['away_team']  . " group by Player_ID", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());






                  $i = 0;
                  
                  

                  $awayTeamNames = mysql_query("SELECT team_name  FROM Team 
                                  where team_ID = ". $rowHigher['home_team'], $dbLocalHost);
                  $awayTeamRuns = mysql_query("SELECT SUM(delivery.Runs) as Runs FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                  where team_ID = ". $rowHigher['away_team'] . " and match_ID = " . $matchID, $dbLocalHost);
                  // GET WICKET NUMBER


                  echo "<tr class=\"head\">";

                





                  echo "<table>";
                  $awayTeamNames = mysql_query("SELECT team_name  FROM Team 
                                  where team_ID = ". $rowHigher['away_team'], $dbLocalHost);
                  echo "<tr class=\"head\">";
                  while($rowNames = mysql_fetch_array($awayTeamNames))
                  {
                    echo "<td>". $rowNames['team_name'] . "</td>";
                  }
                  echo "<td></td>";
                  echo "<td>R</td>";
                  echo "<td>B</td>";
                  echo "<td>4s</td>";
                  echo "<td>6s</td>";
                  echo "</tr>";
                   while($row = mysql_fetch_array($dbRecords))
                  {
                    $playerID = $row['Player_ID'];
                    $i++;
                    echo "<tr class=\"d".($i & 1)."\">";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $row['Player_Name'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $wicketTaken = mysql_query("SELECT * FROM delivery INNER JOIN OVER ON delivery.over_number=over.over_ID 
                      INNER JOIN player ON over.player_bowling = player.player_id where delivery.wicket=1 AND delivery.player_dismissed = " . $playerID . " AND over.match_id = " . $matchID, $dbLocalHost);
                    $numWicket=mysql_num_rows($wicketTaken);
                     while($wicketRow = mysql_fetch_array($wicketTaken))
                    {
                       

                      switch ($wicketRow['Wicket_Type'])
                      {
                        case "Bowled":

                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp B " . $wicketRow['Player_Name']. " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        break;

                        case "Stumped":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] . " AND delivery.match_id =  ". $matchID, $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp  B " . $wicketRow['Player_Name'] .  "   st  " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;

                        case "LBW":

                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp LBW " . $wicketRow['Player_Name']. " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        break;

                        case "Run Out":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] . " AND delivery.match_id =  ". $matchID, $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp ro " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;

                        case "Caught":
                        $wicketAssist = mysql_query("SELECT * FROM delivery INNER JOIN PLAYER ON delivery.PLAYER_ASSIST=Player.Player_ID 
                          where delivery.wicket=1 AND delivery.player_dismissed = " . $row['Player_ID'] , $dbLocalHost);
                        while($wicketAssistRow = mysql_fetch_array($wicketAssist))
                        {
                          echo "<td> &nbsp  B " . $wicketRow['Player_Name'] .  "  c  " . $wicketAssistRow['Player_Name'] . "  &nbsp </td>";
                        }
                        break;
                      }

                      
                      
                    }
                    if ($numWicket == 0 &&  $row['Balls'] == 0)
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }
                    else if ($numWicket == 0) 
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Not Out &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }

                    $legByes = mysql_query("SELECT player.player_name, delivery.extras
                      FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing where player.player_ID = " . $playerID  ." AND delivery.extra_type = \"Leg-Bye(s)\" AND delivery.match_id =  ". $matchID  ."  group by Player_ID ", $dbLocalHost);;
                     // echo $legByes;
                    $numLegByes=mysql_num_rows($legByes);
                    if ($numLegByes > 0)
                    {
                      while($legByesCalc = mysql_fetch_array($legByes))
                        {
                          $total = $row['Runs'] + $legByesCalc['extras'];
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $total . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                      
                    }
                    else if ($row['Runs'] > 0 )
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $row['Runs'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                      
                    }
                    else
                    {
                        echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . "0" . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td>";
                    }

                    if ($row['Balls'] > 0)
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $row['Balls'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                    }
                    else 
                    {
                      echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</td>";
                    }


                    $foursScored = mysql_query("SELECT player.player_ID, COUNT(delivery.RUNS) AS 4s   
                                                FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                                where team_ID = ". $rowHigher['away_team'] . " and delivery.RUNS = 4 and player_Name = \"". $row['Player_Name'] .  "\"", $dbLocalHost);
                     while($rowFours = mysql_fetch_array($foursScored))
                    {

                        if ($rowFours['4s'] > 0)
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $rowFours['4s'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";

                        }
                        else
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                    }

                    $sixesScored = mysql_query("SELECT player.player_ID, COUNT(delivery.RUNS) AS 6s   
                                                FROM player LEFT JOIN delivery On player.Player_ID=delivery.Player_Facing 
                                                where team_ID = ". $rowHigher['away_team'] . " and delivery.RUNS = 6 and player_Name = \"". $row['Player_Name'] .  "\"", $dbLocalHost);
                     while($rowSixes = mysql_fetch_array($sixesScored))
                    {

                        if ($rowSixes['6s'] > 0)
                        { 
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp" . $rowSixes['6s'] . " &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";

                        }
                        else
                        {
                          echo "<td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 0 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp </td>";
                        }
                    }


                     echo "</tr>";       

                 }
                 echo "<tr >";
                 echo "<td></td>";
                 echo "<td></td>";
                  
                    echo "<td class=\"black\"> " . $totalRunsAway . "</td>";
                  echo "</tr>";
                 echo "</table>";

                 // BOWLING

                 echo "<table>";
                  echo "<tr class=\"head\">";
                  echo "<td></td>";
                  echo "<td>O</td>";
                  echo "<td>M</td>";
                  echo "<td>R</td>";
                  echo "<td>W</td>";

                   $homeBowlingFigures = mysql_query("SELECT player.Player_Name, SUM(delivery.runs) AS Runs,  SUM(delivery.extras) AS Extras, COUNT(DISTINCT over.over_id ) AS Overs, SUM(delivery.wicket) FROM player 
                    INNER JOIN over ON over.player_bowling = player.player_id INNER JOIN delivery ON delivery.over_number = over.over_id where team_ID = ". $rowHigher['home_team'] . " and delivery.match_ID = " . $matchID . " GROUP BY player.player_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());


                  $i = 0;

                  while($rowBowl = mysql_fetch_array($homeBowlingFigures))
                  {
                    $i++;
                    echo "<tr class=\"d".($i & 1)."\">";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $rowBowl['Player_Name'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                    $totalRunsBowlerHome = $rowBowl['Runs'] + $rowBowl['Extras'];
                    
                    
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $rowBowl['Overs'] . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $homeBowlingFiguresMaidens = mysql_query("SELECT player.player_name, SUM(delivery.runs) AS Runs, SUM(delivery.extras) as Extras FROM Over 
                      INNER JOIN player ON over.player_bowling=player.player_ID INNER JOIN delivery ON over.over_id=delivery.over_number  where team_ID = ". 
                      $rowHigher['away_team'] . " and delivery.match_ID = " . $matchID . " and player.player_name = \"" . $rowBowl['Player_Name']  . "\" GROUP BY over_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  global $howManyMaidens;
                  $howManyMaidens = 0;
                   while($rowBowlMaidens = mysql_fetch_array($homeBowlingFiguresMaidens))
                  {
                    
                    if ($rowBowlMaidens['Runs'] == 0 && $rowBowlMaidens['Extras'] == 0)
                    {
                        $howManyMaidens++;
                    }
                  }
                    echo "<td> &nbsp &nbsp &nbsp &nbsp ". $howManyMaidens . " &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                    echo "<td> &nbsp &nbsp &nbsp &nbsp" . $totalRunsBowlerAway . "&nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                    $homeBowlingFiguresWickets = mysql_query("SELECT player.player_name, SUM(delivery.wicket) AS Wickets FROM Over INNER JOIN player ON over.player_bowling=player.player_ID 
                      INNER JOIN delivery ON over.over_id=delivery.over_number  WHERE delivery.wicket_type <> \"RUN OUT\"  AND team_ID = ". 
                      $rowHigher['home_team'] . " and delivery.match_ID = " . $matchID . " and player.player_name = \"" . $rowBowl['Player_Name']  . "\" GROUP BY over_id", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  $numWickets=mysql_num_rows($homeBowlingFiguresWickets);
                  if ($numWickets == 0)
                  {
                       echo "<td> &nbsp &nbsp &nbsp &nbsp 0 &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";

                  }

                  while($rowBowlWickets = mysql_fetch_array($homeBowlingFiguresWickets))
                  {
                   
                      echo "<td> &nbsp &nbsp &nbsp &nbsp "  . $rowBowlWickets['Wickets']  . "  &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  </td>";
                  }

                    echo "</tr>";
                  }


                  echo "</table>";

                  $homeExtrasTotal = mysql_query("SELECT delivery.extras, delivery.extra_type FROM Over INNER JOIN player ON over.player_bowling=player.player_ID INNER JOIN 
                    delivery ON over.over_id=delivery.over_number WHERE extra_type <> \"NULL\"   AND team_ID = ". 
                      $rowHigher['home_team'] . " and delivery.match_ID = " . $matchID . " GROUP BY delivery.extra_type", $dbLocalHost)
                  or die("Problem reading table: " . mysql_error());

                  global $awayLegByes;
                  global $awayByes;
                  global $awayWides;
                  global $awayNoBalls;
                  $awayLegByes = 0;
                  $awayByes = 0;
                  $awayWides = 0;
                  $awayNoBalls = 0;

                   while($rowBowlExtras = mysql_fetch_array($awayExtrasTotal))
                  {
                    if ($rowBowlExtras['extra_type'] == "No Ball")
                    {
                      $awayNoBalls += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Wide")
                    {
                      $awayWides += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Leg-Bye(s)")
                    {
                      $awayLegByes += $rowBowlExtras['extras'];
                    }
                     if ($rowBowlExtras['extra_type'] == "Bye(s)")
                    {
                      $awayByes += $rowBowlExtras['extras'];
                    }
                      
                  }
                  echo "<p>Extras: lb ". $awayLegByes . ", b " . $awayByes . ", nb " . $awayNoBalls . ", w " . $awayWides . "</p>";


               }


             }

          }

        ?>
          <p>&nbsp;</p>


          
          <p>&nbsp;</p>
        </div>
    </div>
        <div id="footer"></div>
  </div>
</body>
</html>
