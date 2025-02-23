<?php include 'dbconnect.php';
    $mail = $_POST['mail'];
    $token = bin2hex(random_bytes(50)); //unique token
    echo "   " . $token . "   ";
    $expiry = date('Y-m-d', strtotime('+1 day'));

    $confirmed = $db->prepare('SELECT CASE
                                   WHEN EXISTS (SELECT 1
                                                FROM login
                                                WHERE email = :mail AND confirmed = TRUE)
                                                    THEN TRUE
                                                    ELSE FALSE
                                                END AS confirmed_true');

    $confirmed->bindParam(':mail', $mail);
    $confirmed->execute();
    $resultconfirmed = $confirmed->fetch(PDO::FETCH_ASSOC);
    echo $resultconfirmed ? 'true' : 'false';

    if($resultconfirmed['confirmed_true']) {
        $stmt = $db->prepare('UPDATE LOGIN
                              SET token = :token, expiryDate = :expiry
                              WHERE email = :mail');

        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->execute();

//------------------------------------------------------------------------
        $resetLink = '127.0.0.1/website/reset_password.html?token=$token';
        $subject = 'Reset your Password for Tickify';
        $message = 'Click the following Link to reset your password: $resetlink';
        mail($mail, $subject, $message);
    }
?>