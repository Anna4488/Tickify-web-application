<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="styles/newEvent.css">
</head>

<body>
    <div class="go-back">
        <a href="index.php" class="back" >Go back</a>
    </div>
    <?php
        //php to check if user logged in

        include 'dbconnect.php';
        session_start();

        if (isset($_SESSION["mail"])) {
            $mail = $_SESSION["mail"];
             echo '<form id="eventForm" action="newEvent.php" method="post">';
                echo '<h2>Create Event</h2>';
                echo '<label for="eventName">Event Name:</label>';
                echo '<input type="text" class="answer", id="eventName" name="eventName" required>';

                echo '<label for="eventDate">Event Date:</label>';
                echo '<input type="date" class="answer", id="eventDate" name="eventDate" required>';

                echo '<label for="duration">Duration:</label>';
                echo '<input type="number" class="answer", id="duration" name="duration" required>';

                echo '<label for="eventPegi">PEGI:</label>';
                echo '<input type="number" class="answer", id="eventPegi" name="eventPegi" required>';

                /*
                <select id="eventPegi" name="eventPegi" required>
                    <option value="3">3</option>
                    <option value="7">7</option>
                    <option value="12">12</option>
                    <option value="16">16</option>
                    <option value="18">18</option>
                </select>
               */

               echo '<label for="eventCapacity">Capacity:</label>';
               echo '<input type="number" class="answer", id="eventCapacity" name="eventCapacity" min="1" required>';

               echo '<h2>Location</h2>';

               echo '<label for="ecountry">Country:</label>';
               echo '<input type="text" class="answer", id="ecountry" name="ecountry" required>';

               echo '<label for="ecity">City:</label>';
               echo '<input type="text", class="answer", id="ecity", name="ecity" required>';

               echo '<label for="ezip">Zipcode:</label>';
               echo '<input type="text", class="answer", id="ezip", name="ezip" required>';

               echo '<label for="estreet">Street:</label>';
               echo '<input type="text", class="answer", id="estreet", name="estreet" required>';
                                
               echo '<label for="enumber">Number:</label>';
               echo '<input type="number", class="answer", id="enumber", name="enumber" required>';

               echo '<label for="descriptions">Description:</label>';
               echo '<textarea class="answer", id="descriptions" name="descriptions" rows="3" required></textarea>';

                /*
                <label for="eventBackground">Upload Event Background:</label>
                <input type="file" id="eventBackground" name="eventBackground" accept="image/*" required>
                we don't have a way to store this data*/

                /*
                echo '<h2>Tickets data</h2>';

                echo '<label for="ttype">Ticket Types:</label>';
                echo '<input type="text" id="ttype" name="ttype" required>';
                
                echo '<label for="tprice">Ticket Price:</label>';
                echo '<input type="number" id="tprice" name="tprice" step="0.01" min="0" required>';

                echo '<label for="tquantity">Ticket Quantity (per type):</label>';
                echo '<input type="number" id="tquantity" name="tquantity"  min="0" required>';
                cant make it work like this too complicated*/

                echo '<h2>Ticket Types and prices:</h2>';

                echo '<p>The website operates with the following tickets and prices:</p>';
                echo '<ul>';
                    echo '<li>Regular ticket: €50 </li>';
                    echo '<li>VIP ticket: €100</li>';
                    echo '<li>Student ticket: €25</li>';
                echo '</ul>';
                echo '<p>If you would like this changed for your event please contact us through this link:</p>';
                echo '<a href="contact.php" class="contact"> Contuct us here!</a>';

                echo '<button type="submit" , name="confirm">Confirm</button>';
                echo '</form>';
        } else {
            echo '<a href="login.php" class="login"> Please log in here!</a>';
        }
    ?>            
</body>

</html>

<?php
    //php for adding event and ticket

    if(isset($_POST['confirm'])) {
        $eventName = $_POST['eventName'];
        $eventDate = $_POST['eventDate'];
        $eventPegi = $_POST['eventPegi'];
        $eventCapacity = $_POST['eventCapacity'];
        $duration = $_POST['duration'];
        $ecountry = $_POST['ecountry'];
        $ecity = $_POST['ecity'];
        $ezip = $_POST['ezip'];
        $estreet = $_POST['estreet'];
        $enumber = $_POST['enumber'];
        $descriptions = $_POST['descriptions'];
         
        //note for anna: maybe make this a trigger?
        $eventExists = $db->prepare('SELECT COUNT(*)
                                    FROM events
                                    WHERE names = :eventName');
        $eventExists->bindParam(':eventName',$eventName);
        $eventExists->execute();
        $eventCount = $eventExists->fetchColumn();

        if ($eventCount == 0) {
            try {
                //Adding data to event table + the email
                $insertintoEvent = $db->prepare('INSERT INTO events(dates, names, descriptions, pegi, capacity, duration, accmail)
                                                 VALUES(:eventDate, :eventName, :descriptions, :eventPegi, :eventCapacity, :duration, :mail)');
                
                $insertintoEvent->bindParam(':eventDate', $eventDate);
                $insertintoEvent->bindParam(':eventName', $eventName);
                $insertintoEvent->bindParam(':descriptions', $descriptions);
                $insertintoEvent->bindParam(':eventPegi', $eventPegi);
                $insertintoEvent->bindParam(':eventCapacity', $eventCapacity);
                $insertintoEvent->bindParam(':duration', $duration);
                $insertintoEvent->bindParam(':mail', $mail);

                $insertintoEvent->execute();
                echo '<p>Event was added to your account</p>';

                //Adding data to eventaddress table + enum
                $enum = $db->lastInsertId(); //gets the previously added events id number bc its automatic
                
                $insertintoEventaddress = $db->prepare('INSERT INTO eventaddress(country, city, zipcode, street, num, enum)
                                                        VALUES(:ecountry, :ecity, :ezip, :estreet, :enumber, :enum)');
                $insertintoEventaddress->bindParam(':ecountry',$ecountry);
                $insertintoEventaddress->bindParam(':ecity',$ecity);
                $insertintoEventaddress->bindParam(':ezip',$ezip);
                $insertintoEventaddress->bindParam(':estreet',$estreet);
                $insertintoEventaddress->bindParam(':enumber',$enumber);
                $insertintoEventaddress->bindParam(':enum',$enum);

                $insertintoEventaddress->execute();
                echo '<p>Location was added succesfully</p>';

            } catch (PDOException $e) {
                echo '<p>Error inserting data into event' . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo '<p>Event already exists</p>';
        }
    }
?>
