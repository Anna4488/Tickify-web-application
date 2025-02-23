<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/buyticket.css">
    <title>Buy ticket</title>
</head>
<body>
    <div class="header">
        <img src="images/logo.png" alt="Logo" class="logo">
        <div class="tickify">Tickify</div>
    </div>
    <div class="main-content">
        <?php
        include "dbconnect.php";
        session_start();
        if (isset($_SESSION["mail"])) {
            $mail = $_SESSION["mail"];
            try {
                $enum=$_GET["event"];
                $geteventdata = $db->prepare("SELECT * FROM events WHERE enum = :enum");
                $geteventdata->bindParam(":enum", $enum);
                $geteventdata->execute();

                foreach($geteventdata as $row) {
                    echo "<div class='name'>";
                    echo "<p> " . htmlspecialchars($row['names']) . "</p>";
                    echo "</div>";
                }
                echo '<form action="buyticket.php?event=' . urlencode($_GET['event']) . '" method="post">';
                    echo '<label for="regular">Regular ticket: 50€</label>';
                    echo '<input type="number" id="regular" name="regular" step="1" min="0" max="10" value="0">';
                    
                    echo '<label for="vip">VIP ticket: 100€</label>';
                    echo '<input type="number" id="vip" name="vip" step="1" min="0" max="10" value="0">';

                    echo '<label for="student">Student ticket: 25€</label>';
                    echo '<input type="number" id="student" name="student" step="1" min="0" max="10" value="0">';

                    echo '<button type="submit"  name="confirm">Confirm</button>';

                    echo "<a href='event.php?event=" . urlencode($row['enum']) . "'>";
                    echo "<div class='go-back'>";
                    echo "<p>Go back</p>";
                    echo "</div>";
                echo '</form>';
                    // to calculate the price before submitting the form requires javascript :(
                if(isset($_POST['confirm'])) {;
                    $regular = $_POST["regular"];
                    $vip = $_POST["vip"];
                    $student = $_POST["student"];
                    $message = "";
                    $rnum = 0;
                    $vnum = 0;
                    $snum = 0;
                    for ($i = 0; $i < $regular;$i++) {
                        $insertintotickets = $db ->prepare("INSERT INTO ticket(enum, types, price, accmail)
                                                            VALUES(:enum, 'regular', 50, :mail)");
                        $insertintotickets->bindParam(':enum', $enum);
                        $insertintotickets->bindParam(':mail', $mail);
                        $insertintotickets->execute();
                        $rnum +=1;
                    }
                    $message .= "$rnum Regular tickets purchased successfully.<br>";

                    for ($i = 0; $i < $vip;$i++) {
                        $insertintotickets = $db ->prepare("INSERT INTO ticket(enum, types, price, accmail)
                                                            VALUES(:enum, 'VIP', 100, :mail)");
                        $insertintotickets->bindParam(':enum', $enum);
                        $insertintotickets->bindParam(':mail', $mail);
                        $insertintotickets->execute();
                        $vnum +=1;
                    }
                    $message .= "$vnum VIP tickets purchased successfully.<br>";

                    for ($i = 0; $i < $student;$i++) {
                        $insertintotickets = $db ->prepare("INSERT INTO ticket(enum, types, price, accmail)
                                                            VALUES(:enum, 'student', 50, :mail)");
                        $insertintotickets->bindParam(':enum', $enum);
                        $insertintotickets->bindParam(':mail', $mail);
                        $insertintotickets->execute();
                        $snum +=1;
                    }
                    $message .= "$snum Student tickets purchased successfully.<br>";

                    $_SESSION['message'] = $message;
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['message'] = "Couldn't buy tickets";
                }
            } catch (PDOException $e) {
                $message .= "Ticket purchase limit exceeded for this account (max 10 tickets per event).";
                $_SESSION['message'] = $message;
                header("Location: index.php");
                echo "<p>bad bad " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<a href='login.php' class='login'> Please log in</a>";
        }
        ?>
    </div>
</body>
</html>