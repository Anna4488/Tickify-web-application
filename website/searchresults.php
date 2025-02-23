<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/searchresults.css">
</head>
<body>
<div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div><a href="index.php" class="tickify">Tickify</a></div>
        <form class="search-bar" action="/searchresults.php" method="get">
            <input type="text" name="search" placeholder="Search.." class="search-input">
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
            </ul>
        </div>
        <div class="content">
            <?php
            include "dbconnect.php";

            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

            if (!empty($searchTerm)) {
                $sqlSelect = "SELECT * FROM events WHERE LOWER(names) LIKE LOWER(:searchTerm)";

                $stmt = $db->prepare($sqlSelect);

                $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');

                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    foreach ($stmt as $row){
                        echo "<a href='event.php?event=" . urlencode($row['enum']) . "'>";
                        echo "<div class='event-container'><div class='event-text'><b>"
                             . htmlspecialchars($row['enum']) . " <br> " . htmlspecialchars($row['names']) . 
                             "</b></div></div>";
                        echo "</a><br>";
                    }
                } else {
                    echo "No events found.";
                }
            } else {
                echo "Please enter a search term.";
            }
            
            ?>
        </div>
    </div>
</body>
</html>