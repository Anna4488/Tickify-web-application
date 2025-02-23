<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/account.css">
    <title>Account</title>
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
                <li><h1 class="name">Account</h1></a></li>
                <li><a href="contact.php"><h1>Contact Us</h1></a></li>
                <li><a href="myticket.php"><h1>My Tickets</h1></a></li>
            </ul>
        </div>
        <div class="content">
                <?php
                include "dbconnect.php";
                if (isset($_SESSION["mail"])) {
                    $mail = $_SESSION["mail"];
                    try {
                        $accountdata = $db->prepare("SELECT * FROM account WHERE email = :mail");
                        $accountdata->bindParam(":mail",$mail);
                        $accountdata->execute();

//--------------------------------------------------------------------------------------------------------------------------------------------------------  

                        //Account data from account table
                        foreach($accountdata as $row) {
                            echo "<div class='big'>";
                                echo "<div class='data'>";
                                    echo "<h1 class='title'>Account data:</h1>";

                                    echo "<div class='email'>";
                                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='fname'>";
                                    echo "<p>First name: " . htmlspecialchars($row['firstname']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='lname'>";
                                    echo "<p>Last name: " . htmlspecialchars($row['lastname']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='birthday'>";
                                    echo "<p>Birthday: " . htmlspecialchars($row['birthdate']) . "</p>";
                                    echo "</div>";
                        }
//--------------------------------------------------------------------------------------------------------------------------------------------------------  

                        //Location data from accountaddress table
                        $locationdata = $db->prepare("SELECT * FROM accountaddress where accmail = :mail");
                        $locationdata->bindParam(":mail",$mail);
                        $locationdata->execute();
                        foreach($locationdata as $row) {
                                    echo "<h1 class='title'>Address data:</h1>";

                                    echo "<div class='text'>";
                                    echo "<p>Country: " . htmlspecialchars($row['country']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='text'>";
                                    echo "<p>City: " . htmlspecialchars($row['city']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='text'>";
                                    echo "<p>Zip: " . htmlspecialchars($row['zipcode']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='text'>";
                                    echo "<p>Street: " . htmlspecialchars($row['street']) . "</p>";
                                    echo "</div>";

                                    echo "<div class='text'>";
                                    echo "<p>Housenummber: " . htmlspecialchars($row['housenum']) . "</p>";
                                    echo "</div>";
                        }

//--------------------------------------------------------------------------------------------------------------------------------------------------------  

                                    //Logging out
                                    echo '<div class="button">';
                                            echo '<form action="account.php" method="post">';
                                                echo '<button type="submit" name="logout" class="login-button">Log out</button>';
                                            echo '</form>';   
                                    echo '</div>';

                                    if (isset($_POST["logout"])) {
                                        session_destroy();
                                        header("Location: index.php");
                                    }
                                echo "</div>";

//--------------------------------------------------------------------------------------------------------------------------------------------------------  

                                //Delete account button
                                echo '<div class="delete">';
                                    echo '<form action="account.php" method="post">';
                                        echo '<button type="submit" name="delete">Delete account</button>';
                                    echo '</form>';
                                echo '</div>';

                                if (isset($_POST["delete"])) {
                                    try {
                                        //checks if account has tickets
                                        $checkticket = $db -> prepare("SELECT COUNT(*) FROM ticket WHERE accmail = :mail");
                                        $checkticket->bindParam(":mail", $mail);
                                        $checkticket->execute();
                                        $ticketexist = $checkticket->fetchColumn();
                                        
                                        //checks if account has events
                                        $checktevent = $db -> prepare("SELECT COUNT(*) FROM events WHERE accmail = :mail");
                                        $checktevent->bindParam(":mail", $mail);
                                        $checktevent->execute();
                                        $eventexist = $checktevent->fetchColumn();

                                        //only allows deletion if no ticket or event
                                        if ($ticketexist > 0 || $eventexist > 0) {
                                            echo "<p>Error: Cannot delete account as there are associated tickets or events.</p>";
                                        } else {
                                            //delete data from accountaddress table
                                            $deletedataaddress = $db->prepare("DELETE FROM accountaddress WHERE accmail = :mail");
                                            $deletedataaddress->bindParam(":mail", $mail);
                                            if ($deletedataaddress->execute()) {
                                                echo "<p>Address data deleted successfully.</p>";
                                                
                                            } else {
                                                echo "<p>Error deleting address. Please try again.</p>";
                                            }

                                            //delete data from account table
                                            $deletedataaccount = $db->prepare("DELETE FROM account WHERE email = :mail");
                                            $deletedataaccount->bindParam(':mail', $mail);

                                            if ($deletedataaccount->execute()) {
                                                echo "<p>Account deleted successfully.</p>";
                                                session_unset();
                                                session_destroy();
                                                header("Location: index.php");
                                                exit();
                                            } else {
                                                echo "<p>Error deleting account. Please try again.</p>";
                                            }
                                        }

//--------------------------------------------------------------------------------------------------------------------------------------------------------  

                                    //Error handling
                                    } catch (PDOException $e) {
                                        echo "<p>Error deleting account: " . htmlspecialchars($e->getMessage()) . "</p>";
                                    }
                                }
                                
                            echo "</div>";
                        
                    } catch (PDOException $e) {
                        echo "<p>Error fetching account data: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                } else {
                    echo "<a href='login.php' class='login'> Please log in</a>";
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>