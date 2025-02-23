<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/password.css">
    <link rel="stylesheet" href="styles/register.css">
    <title>Login</title>
</head>
<body>
    <div class="containerV">
        <div class="h1">
            <h1>Register</h1>
        </div>
        <div class="login-background">
            <div class="flex-container">
                    <form action="register.php" method="post">
                       <!--<label for="registerAs">Register as:</label>
                        <select name="userRole" id="userRole" required>
                            <option value="customer">Customer</option>
                            <option value="organizer">Event Organizer</option>
                        </select> -->
                        
                        <label for="mail">E-mail:</label>
                        <input class="input-field", type="email", name="mail", id="mail", value="" required>

                        <label for="userPassword">Password:</label>
                        <input class="input-field", type="password", name="userPassword", id="userPassword", value="" require>

                        <label for="userPassword">Repeat Password:</label>
                        <input class="input-field", type="password", name="userPasswordRepeat", id="userPasswordRepeat", value="" require>
                        
                        <label for="firstName">first Name:</label>
                        <input class="input-field", type="text", name="firstName", id="firstName", value="" required>
                        
                        <label for="lastName">last Name:</label>
                        <input class="input-field", type="text", name="lastName", id="lastName", value="" required>

                        <label for="age">Birthday:</label>
                        <input class="input-field", type="date", name="birthday", id="birthday", value="" required>

                        <label for="address">Country:</label>
                        <input class="input-field", type="text", name="country", id="country", value="" required>

                        <label for="address">City:</label>
                        <input class="input-field", type="text", name="city", id="city", value="" required>

                        <label for="address">Zipcode:</label>
                        <input class="input-field", type="text", name="zip", id="zip", value="" required>

                        <label for="address">Street:</label>
                        <input class="input-field", type="text", name="street", id="street", value="" required>
                        
                        <label for="address">Number:</label>
                        <input class="input-field", type="text", name="number", id="number", value="" required>

                        <br>
                        <input class="submit-button", type="submit", value="Register", name="submit">
                    </form>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>

<?php include 'dbconnect.php';
    if(isset($_POST['submit'])) {
        $userRole = $_POST['userRole'];
        $mail = $_POST['mail'];
        $password = $_POST['userPassword'];
        $passwordRepeat = $_POST['userPasswordRepeat'];
        $fName = $_POST['firstName'];
        $lName = $_POST['lastName'];
        $birthDay = $_POST['birthday'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $zip = $_POST['zip'];
        $street = $_POST['street'];
        $number = $_POST['number'];

        echo $mail . ", " . $password . ", ";

        if($password == $passwordRepeat) {
            $hashedpsw = password_hash($password, PASSWORD_DEFAULT);
            echo $hashedpsw . ", ";
            $accountExists = $db->prepare('SELECT COUNT(*)
                                           FROM account
                                           WHERE email = :mail');
            $accountExists->bindParam(':mail', $mail);
            $accountExists->execute();
            $accCount = $accountExists->fetchColumn();
            echo $accCount . '<br>'; 
            if($accCount == 0) {
                try{
    //----------------------------------------------------------------------------------------------------------------
                    $insertToLogin = $db->prepare('INSERT INTO account(email, userPassword, firstName, lastName, birthdate)
                                                   VALUES(:mail, :userPassword, :firstName, :lastName, :age)');
                    $insertToLogin->bindParam(':mail', $mail);
                    $insertToLogin->bindParam(':userPassword', $hashedpsw);
                    $insertToLogin->bindParam(':firstName', $fName);
                    $insertToLogin->bindParam(':lastName', $lName);
                    $insertToLogin->bindParam(':age', $birthDay);
                    $insertToLogin->execute();
                    echo 'inserted to account<br>';
    //----------------------------------------------------------------------------------------------------------------
                    $insertToAddress = $db->prepare('INSERT INTO accountaddress(country, city, zipcode, street, housenum, accMail)
                                                     VALUES(:country, :city, :zipcode, :street, :num, :mail)');
                    $insertToAddress->bindParam(':country', $country);
                    $insertToAddress->bindParam(':city', $city);
                    $insertToAddress->bindParam(':zipcode', $zip);
                    $insertToAddress->bindParam(':street', $street);
                    $insertToAddress->bindParam(':num', $number);
                    $insertToAddress->bindParam(':mail', $mail);
                    $insertToAddress->execute();
                    echo 'inserted to address';
    //----------------------------------------------------------------------------------------------------------------
                    $insertToUserRole = $db->prepare('INSERT INTO userRole(restriction, accMail)
                                                    VALUES("customer", :mail)');
                    $insertToUserRole->bindParam(':mail', $mail);
                    $insertToUserRole->execute();
                    echo 'inserted to userRole';
                    $message = 'registration succesfully :)';
                    header("Location: ./login.php?message=$message");
                    exit();
    //----------------------------------------------------------------------------------------------------------------
                } catch(PDOException $e) {
                    $message = 'whe had an issue with your registration :(';
                    header("Location: ./register.php?message=$message");
                    exit();
                    echo 'there were complications creating your account: ' . $e->getMessage();
                }
        } else {
                echo 'email already exists';
                $message = 'this email already exists';
                header("Location: ./login.php?message=$message");
                exit();
            }
        }
    }
?>