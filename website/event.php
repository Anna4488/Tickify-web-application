<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/searchresults.css">
    <link rel="stylesheet" href="styles/event.css">
</head>
<body>
<div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div><a href="index.php" class="tickify">Tickify</a></div>
        <form class="search-bar" action="/searchresults.php" method="get">
            <input type="text" placeholder="Search.." class="search-input" name="search">
            <button type="submit" class="search-button">Search</button>
        </form>
        <div class="button">
            <form action="/login.php" method="get">
                <?php
                    session_start();
                    if (isset($_SESSION['mail'])) {
                        echo '<label for="loggedin" class="login-button">Logged in as ' . htmlspecialchars($_SESSION['mail']) . '</label>';
                    }
                    else {
                        echo '<button type="submit" class="login-button">Log in</button>';
                    }
                ?>
            </form>
        </div>
    </div>
    <div class="main-content">
        <div class="sidebar">
        <ul class="no-dots">
                <li><a href="account.php"><h1>Account</h1></a></li> 
                <li><a href="contact.php"><h1>Contact Us</h1></a></li>
                <li><a href="myticket.php"><h1>My Tickets</h1></a></li>
                <!--<li><?php
                    //echo "<a href='buyticket.php?event=" . urlencode($_GET['event']) . "'><h1>Buy Ticket</h1></a>" //Link to the buy ticket site
                    ?>
                    </li>
                moved to somewhere else-->
            </ul>
        </div>
        <div class="content">
        <?php
            include "dbconnect.php";
            $x = $_GET['event'];
            try { 
                // SQL query to select data from your table 
                $stmt = $db->prepare("SELECT names, dates, pegi, capacity , duration, descriptions
                                        FROM events 
                                        where enum = $x");
               
                $stmt->execute(); // Fetch all the rows as an associative array 
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC); //checks if the results array has data in it
                if (count($result) > 0) { 
                    foreach($result as $row)
                    //echo "<div class='container'>";
                        echo "<div class='data'>";
                            echo "<div class='name'>";
                                echo "<div class='title'>";
                                echo "<p>" . htmlspecialchars($row['names']) . "</p>";
                                echo "</div>";

                                echo "<div class='text'>";
                                echo "<p>Start date:<br>" . htmlspecialchars($row['dates']) . "</p>";
                                echo "</div>";

                                //echo "<div class='text'>";
                                //echo "<p>Capacity: " . htmlspecialchars($row['capacity']) . "</p>";
                                //echo "</div>";

                                echo "<div class='text'>";
                                echo "<p>" . htmlspecialchars($row['duration']) . " days long</p>";
                                echo "</div>";

                            echo "</div>";

                            echo "<div class='tickets'>";
                                echo "<div class=''>";
                                echo "<p class='pegi'>Pegi " . htmlspecialchars($row['pegi']) . "</p>";
                                echo "</div>";
                            echo '</div>'; //class="tickets"
                        echo '</div>'; //class="data"

                        echo '<div class="desc">';
                        echo "<texterae>" . htmlspecialchars($row['descriptions']) . "</texterae>";
                        echo '</div>';
                            
                        //x is enum from the beginning, run the sql than i get the column with the count which is a number
                        $bought = $db->prepare("SELECT count(*) FROM ticket WHERE enum = :enum");
                        $bought->bindParam(':enum', $x);
                        $bought->execute();
                        $countnum = $bought->fetchColumn();
                        
                        //if the counted tickets are less than capacity
                        if ($countnum < $row['capacity']) {
                            echo "<div class='yes'>";
                            echo "<a href='buyticket.php?event=" . urlencode($_GET['event']) . "'><p>Tickets are available!</p></a>";
                            echo '</div>';


                    } else {
                        echo "<div class='no'>";
                        echo 'Tickets are no longer sold for this event :(';
                        echo '</div>';
                    }

                        

                        //add description

                        //add buy ticket end of this
                        
                    //echo "</div>";

                    /*original code did no touch it
                    //draws the table
                        echo "<table border='1'><tr><th>date</th><th>Name</th><th>Pegi</th><th>Capacity</th><th>Duration</th></tr>"; // Output data of each row 
                        foreach ($result as $row) { 
                            echo "<tr><td>".$row['dates']."</td><td>".$row['names']."</td><td>".$row['pegi']."</td><td>".$row['capacity']."</td><td>".$row['duration']."</td></tr>"; } echo "</table>"; 
                    */
                    } else { 
                        echo "0 results"; 
                    } 
                    

                } 
                catch(PDOException $e) { 
                    //error massage in case conection failed
                    echo "Connection failed: " . $e->getMessage(); 
                } 
            $db = null; 
            ?>
            
            <?php
            /*
                include "dbconnect.php";
                $x = $_GET['event'];
                try {  
                        $stmt = $db->prepare("SELECT descriptions FROM events WHERE enum = $x"); 
                        $stmt->execute();  
                        $results = $stmt->fetchAll(); //checks if the results array has data in it
                        if ($results) { 
                            // show the results 
                            foreach ($results as $row) { 
                                echo $row['descriptions'] . "<br>"; 
                            } 
                        } else { 
                            echo "Keine Ereignisse gefunden."; 
                        } 
                    } catch (\PDOException $e) { 
                        //error massage in case conection failed
                        echo "Fehler: " . $e->getMessage(); 
                    }
                        */
            ?>
        </div>
    </div>
</body>
</html>