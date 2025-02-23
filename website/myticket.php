<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/myticket.css">
    <title>My tickets</title>
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
                <li><h1 class="name">My Tickets</h1></a></li>
            </ul>
        </div>
        <div class="content">
            <?php
                include "dbconnect.php";
                if (isset($_SESSION["mail"])) {
                    $mail = $_SESSION["mail"];
                    try {
                        $stmt = $db->prepare("SELECT * FROM ticket WHERE accmail = :email");
                        $stmt->bindParam(":email",$mail);
                        $stmt->execute();
                        foreach($stmt as $row) {
                            echo"<div class='ticket'>";
                                echo "<div class='ticketnr'>";
                                echo "<strong>Ticket number:</strong> " . htmlspecialchars($row['num']) . "<br>";
                                echo "</div>";
                                echo "<div class='eventnr'>";
                                echo "<strong>Event number</strong> " . htmlspecialchars($row['enum']) . "<br>";
                                echo "</div>";
                                echo "<div class='types'>";
                                echo "<strong>Type:</strong> " . htmlspecialchars($row['types']) . "<br>";
                                echo "</div>";
                            echo "</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<p>Error fetching account data: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                } else {
                    echo "<a href='login.php' class='login'>Please log in</a>";
                }
            ?>
        </div>
    </div>
</body>
</html>