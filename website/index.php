<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/Mainpage.css">
</head>

<body>
    <div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div class="tickify">Tickify</div>
        <form class="search-bar" action="/searchresults.php" method="get">
            <input type="text" placeholder="Search.." class="search-input" name="search">
            <button type="submit" class="search-button">Search</button>
        </form>
        <div class="new-event">
            <form action="/newEvent.php" method="get">
                <button type="submit" class="newEvent-button">Add New Event</button>
            </form>
        </div>
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
            <img src="images/event picture1.jpg" alt="Event Picture" class="event-pic">
            <?php
                if (isset($_SESSION['message'])) {
                    echo '<div class="message">';
                    echo $_SESSION['message']; // Display the combined message
                    echo '</div>';
                    unset($_SESSION['message']); // Clear the message after displaying
                }
            ?>
        </div>
    </div>
</body>

</html>