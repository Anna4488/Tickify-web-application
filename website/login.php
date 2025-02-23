<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/password.css">
    <title>Login</title>
</head>
<body>
    <div class="containerV">
        <div class="h1">
            <h1>Login</h1>
        </div>
        <div class="login-background">
            <div class="flex-container">
                    <p style="color:red;"><?php if($_GET != NULL) {   
                                                    if($_GET['message'] != NULL) { 
                                                        if(strlen($_GET['message']) >0) {
                                                            echo $_GET['message'];
                                                            }
                                                        }
                                                    }?></p>
                    <form action="login.php" method="post">
                        <label for="mail">E-mail:</label>
                        <input class="input-field", type="email", name="mail", value="" required>
                        <label for="userPassword">Password:</label>
                        <input class="input-field", type="password", name="userPassword", id="userPassword", value="" require><br>
                        <input class="submit-button", type="submit", value="Log in", name="submit">
                    </form>
                    <div class="containerH">
                        <div>
                            <a href="./forgot_password.html", target="_self" , class="link">forgot your password?</a>
                        </div>
                        <div>
                            <a href="./register.php", target="_self" , class="link">register</a>
                        </div>
                        
                    </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>

<?php include 'dbconnect.php';
    session_start();
    if(isset($_POST['submit'])) {
        $mail = $_POST['mail'];
        $userPassword = $_POST['userPassword'];


        $confirmed = $db->prepare('SELECT COUNT(*) 
                                   FROM account 
                                   WHERE email = :mail');

        $confirmed->bindParam(':mail', $mail);
        $confirmed->execute();
        $resultconfirmed = $confirmed->fetchColumn();

        echo $resultconfirmed . " ";
        if($resultconfirmed = 1) {

            echo $mail . " ";
            echo $userPassword . "<br>";
        
            $stmt = $db->prepare('SELECT userPassword
                                  FROM account
                                  WHERE email = :mail');
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $superPassword = $stmt->fetchColumn();
            echo $superPassword . " ";
            if(password_verify($userPassword, $superPassword)) {
                echo 'password true';
                $_SESSION['mail'] = $mail;
                $restr = $db->prepare('SELECT restriction
                                       FROM userRole
                                       WHERE accMail = :mail');
                $restr->bindParam(':mail', $mail);
                $restr->execute();
                $resultrestr = $restr->fetchColumn();
                echo $resultrestr;
                if($resultrestr == 'admin') {
                    header('Location: ./adminpage.php');
                    exit();
                } if($resultrestr == 'customer') {
                    header('Location: ./index.php');
                    exit();
                } else {
                    echo 'restriction alert';
                    $message="your restriction is not set correctly! Please contact an admin for this matter";
                    header("Location: ./login.php?message=$message");
                    exit();
                }
            } else {
                echo'password false';
                $message="your password or email is incorrect";
                header("Location: ./login.php?message=$message");
                exit();
            }
        } else {
            echo 'Email does not exists or is not validated yet!';
            $message="your email does not exist";
            header("Location: ./login.php?message=$message");
            exit();
        }
    }
?>