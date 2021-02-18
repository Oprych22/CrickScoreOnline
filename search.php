

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="crickscore.css" />

<title>CrickScore</title>
<link rel="stylesheet" href="Javascript/development-bundle/themes/base/jquery.ui.all.css">
  <script src="Javascript/js/jquery-1.7.1.min.js"></script>
  <script src="Javascript/development-bundle/ui/jquery.ui.core.js"></script>
  <script src="Javascript/development-bundle/ui/jquery.ui.widget.js"></script>
  <script src="Javascript/development-bundle/ui/jquery.ui.datepicker.js"></script>

  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
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
        <div class="contentTitle"><h1>Search For a Match</h1></div>
        <div class="contentText">


          <?php
            if(isset($_GET['searched']) || isset($_GET['date']))
          {
            ?>
            
          <form class="f1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get"> Team Name
          <input type="text" name="searched" value="<?php echo $_GET['searched']; ?>" /> 
        </br>
        Date
          <input type="text" name ="date" id="datepicker" value="<?php echo $_GET['date']; ?>" />
        </br>
          <input type="submit" name="submit" value="Search" />
   
          </form>
          <?php
        }
        else 
        {
            ?>
            <form class="f1" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get"> Team Name
           <input type="text" name="searched"  /> 
           </br>
           Date
           <input type="text" name ="date" id="datepicker" />
           </br>
            <input type="submit" name="submit" value="Search" />
          

          

            <?php
        }

        
         
          
          if(isset($_GET['submit']) && ($_GET['searched'] != "" || isset($_GET['date'])))
          {
              
                    $var = $_GET['searched'] ;
                    if (isset($_GET['date']) )
                    {
                      $newDate = date("Y-d-m", strtotime($_GET['date']));
                    }
                    else
                    {
                      $newDate = "";
                    }

                    if ($newDate == "1970-01-01")
                    {
                      $newDate = "";
                    }
                    $trimmed = trim($var); 

                  // rows to return
                  $limit=10; 
                  ;
               
                  if ($trimmed != "" && $newDate == "")
                  {
                      $query = "SELECT * FROM Match_day LEFT JOIN TEAM On Match_day.Home_Team=TEAM.TEAM_ID OR Match_day.Away_Team=TEAM.TEAM_ID  WHERE TEAM_NAME LIKE \"%".$trimmed."%\""; 
                  //echo $query;


                  }
                  else if  ($trimmed == "" && $newDate != "")
                  {
                      $query = "SELECT * FROM Match_day   WHERE Date = " . "\"" . $newDate . "\""; 
                  //echo $query;


                  }
                  else
                  {
                         $query = "SELECT * FROM Match_day LEFT JOIN TEAM On Match_day.Home_Team=TEAM.TEAM_ID OR Match_day.Away_Team=TEAM.TEAM_ID  WHERE TEAM_NAME LIKE \"%".$trimmed."%\" AND  Date = " . "\"" . $newDate ."\""; 
                      //echo $query;
                       

                  }

                  //echo "<p>" . $query . "</p>";
                  if (!isset($var) && $newDate = "")
                    {
                      echo "<p>We dont seem to have a search parameter!</p>";
                      exit;
                    }
              
                  $dbLocalHost = mysql_connect("localhost","root",""); 

    
                  mysql_select_db("CricketScorer",  $dbLocalHost)
                   or die("Could not find database: " . mysql_error()); 

                  $numresults=mysql_query($query, $dbLocalHost);
                       $numrows=mysql_num_rows($numresults);
                   
                  
                  

                  

                  if ($numrows == 0)
                    {
                    echo "<p>Results</p>";
                    echo "<p>Sorry, your search: &quot;" . $trimmed . "  " . $newDate. "&quot; returned zero results</p>";

                  // google
                   echo "<p><a href=\"http://www.google.com/search?q=" 
                    . $trimmed . "\" target=\"_blank\" title=\"Look up 
                    " . $trimmed . " on Google\">Click here</a> to try the 
                    search on google</p>";
                    }

                    if (empty($s)) 
                    {
                      $s=0;
                    }

                    $query .= " limit $s,$limit";
                    $result = mysql_query($query) or die("Couldn't execute query");

  
                  //echo "<p>You searched for: &quot;" . $var . "&quot;</p>";

                  echo "<p>Results</p>";
                  $count = 1 + $s ;

                  echo "<table>";


                  echo "<tr>";
                  echo "</tr>";
                  // now you can display the results returned
                    while ($row = mysql_fetch_array($result)) 
                    {
                      $homeTeam = mysql_query("SELECT Team_Name FROM team WHERE TEAM_ID = " . $row["Home_Team"], $dbLocalHost) ; 
                      $awayTeam = mysql_query("SELECT Team_Name FROM team WHERE TEAM_ID = " . $row["Away_Team"], $dbLocalHost) ; 

                       echo "<tr class=\"d".($count & 1)."\">";
                      echo "<td>" .  mysql_result($homeTeam, 0) .  "</td>" ;
                      echo "<td>" .  mysql_result($awayTeam, 0) .  "</td>" ;
                      echo "<td>" .  $row["Venue"] .  "</td>" ;
                      echo "<td>" .  $row["Match_Length"] .  "</td>" ;
                      echo "<td>" .  $row["Date"] .  "</td>" ;
                      echo "<td>" .  $row["Umpires"] .  "</td>" ;
                      echo"<td>" .  "<FORM action=\"matchinfo.php\" method=\"get\">
                                    <button TYPE=\"submit\"  NAME=\"matchID\" VALUE=\" " .  $row["Match_ID"] . " \">Match Link</button>
                                    </FORM>" .  "</td>" ;

                    echo "</tr>";
                      $count++ ;
                    }
                    echo "</table>";
                   $currPage = (($s/$limit) + 1);

                  //break before paging
                    echo "<br />";

                    // next we need to do the links to other results
                    if ($s>=1) { // bypass PREV link if s is 0
                    $prevs=($s-$limit);
                    print "&nbsp;<a href=\"$PHP_SELF?s=$prevs&q=$var\">&lt;&lt; 
                    Prev 10</a>&nbsp&nbsp;";
                    } 


            } 
            else if ((isset($_GET['submit']) && ($_GET['searched'] == "" || ($_GET['date'] == ""))))
            {
              echo "oh";
                echo "<p>We dont seem to have a search parameter!</p>";
            }
            else
            {
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
