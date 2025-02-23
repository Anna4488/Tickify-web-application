<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/AdminPage.css">
    <title>Admin Page</title>
</head>
<body>
    <div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div class="tickify">Tickify</div>
    </div>
    <div class="main-content">
        
        <div class="content">
                <?php
                include "dbconnect.php";
                session_start();
                if (isset($_SESSION["mail"])) {
                    $mail = $_SESSION["mail"];
                    try {
                        $stmt = $db->prepare("SELECT * FROM account WHERE email = :email");
                        $stmt->bindParam(":email",$mail);
                        $stmt->execute();
                        foreach($stmt as $row) {
                            echo "<div class='data'>";
                                echo "<div class='email'>";
                                echo "<strong>Email:</strong> " . htmlspecialchars($row['email']) . "<br>";
                                echo "</div>";
                                echo "<div class='fname'>";
                                echo "<strong>First name:</strong> " . htmlspecialchars($row['firstname']) . "<br>";
                                echo "</div>";
                                echo "<div class='lname'>";
                                echo "<strong>Last name:</strong> " . htmlspecialchars($row['lastname']) . "<br>";
                                echo "</div>";
                                echo "<div class='birthday'>";
                                echo "<strong>Birthday:</strong> " . htmlspecialchars($row['birthdate']) . "<br>";
                                echo "</div>";
                            echo "</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<p>Error fetching account data: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                } else {
                    echo "<p>Error: Mail is not set in the session. Please log in.</p>";
                    echo "<a href='login.php'>Log in</a>";
                }

                $accountMessage = "";
                $eventMessage = "";

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account_email'])) {
                    $emailToDelete = filter_var($_POST['delete_account_email'], FILTER_SANITIZE_EMAIL);
                    if(!filter_var($emailToDelete, FILTER_VALIDATE_EMAIL)) {
                        $accountMessage = "<p>Invalid email address.</p>";
                    } else {
                        try {
                            /*$stmt = $db->prepare("DELETE FROM account WHERE email = :email");
                            $stmt->bindParam(':email', $emailToDelete);
                            if ($stmt->execute()) {
                                if ($stmt->rowCount() > 0) {
                                    $accountMessage = "<p>Account with email" . htmlspecialchars($emailToDelete) . "has been deleted.</p>";
                                } else {
                                    $accountMessage = "<p>No account found with email $emailToDelete.</p>";
                                }
                            } else {
                                $accountMessage = "<p>Failed to delete account. Please try again.</p>";
                            }*/

                            //checks if account has tickets
                            $checkticket = $db -> prepare("SELECT COUNT(*) FROM ticket WHERE accmail = :email");
                            $checkticket->bindParam(":email", $emailToDelete);
                            $checkticket->execute();
                            $ticketexist = $checkticket->fetchColumn();
                            
                            //checks if account has events
                            $checktevent = $db -> prepare("SELECT COUNT(*) FROM events WHERE accmail = :email");
                            $checktevent->bindParam(":email", $emailToDelete);
                            $checktevent->execute();
                            $eventexist = $checktevent->fetchColumn();

                            //only allows deletion if no ticket or event
                            if ($ticketexist > 0 || $eventexist > 0) {
                                echo "<p>Error: Cannot delete account as there are associated tickets or events.</p>";
                            } else {
                                //delete data from accountaddress table
                                $deletedataaddress = $db->prepare("DELETE FROM accountaddress WHERE accmail = :email");
                                $deletedataaddress->bindParam(":email", $emailToDelete);
                                if ($deletedataaddress->execute()) {
                                    echo "<p>Address data deleted successfully.</p>";
                                    
                                } else {
                                    echo "<p>Error deleting address. Please try again.</p>";
                                }

                                //delete data from account table
                                $deletedataaccount = $db->prepare("DELETE FROM account WHERE email = :email");
                                $deletedataaccount->bindParam(':email', $emailToDelete);

                                if ($deletedataaccount->execute()) {
                                    echo "<p>Account deleted successfully.</p>";
                                }
                            }

                        } catch (PDOException $e) {
                            $accountMessage = "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                        }
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_number'])) {
                    $eventNumberToDelete = filter_var($_POST['delete_event_number'], FILTER_SANITIZE_NUMBER_INT);
                    if(!is_numeric($eventNumberToDelete)) {
                        $eventMessage = "<p>Invalid event number.</p>";
                    } else {
                        try {
                            $stmt = $db->prepare("DELETE FROM events WHERE enum = :event_number");
                            $stmt->bindParam(':event_number', $eventNumberToDelete);
                            if ($stmt->execute()) {
                                if ($stmt->rowCount() > 0) {
                                    $eventMessage = "<p>Event with number" . htmlspecialchars($eventNumberToDelete) . "has been deleted.</p>";
                                } else {
                                    $eventMessage = "<p>No event found with number $eventNumberToDelete.</p>";
                                }
                            } else {
                                $eventMessage = "<p>Failed to delete event. Please try again.</p>";
                            }
                        } catch (PDOException $e) {
                            $eventMessage = "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                        }
                    }
                }
                ?>
            
                <div class="delete-account">
                    <h3>Delete Account</h3>
                    <form method="POST">
                        <input type="email" name="delete_account_email" placeholder="Enter Email" required>
                        <button type="submit">Delete Account</button>
                    </form>
                    <?= $accountMessage ?>
                </div>
                <br>
                <div class="delete-event">
                    <h3>Delete Event</h3>
                    <form method="POST">
                        <input type="number" name="delete_event_number" placeholder="Enter Event" required>
                        <button type="submit">Delete Event</button>
                    </form>
                    <?= $eventMessage ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>