<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/Mainpage.css">
    <link rel="stylesheet" href="styles/payment.css">
    <title>Payment</title>
    <script>
        function validatePayment() {
            const cardNumber = document.getElementById("card-number").value;
            const expirationDate = document.getElementById("expiration-date").value;
            const cvc = document.getElementById("cvc").value;

            // Regex for validation
            const cardNumberRegex = /^\d{16}$/; // 16-digit card number
            const expirationDateRegex = /^(0[1-9]|1[0-2])\/\d{2}$/; // MM/YY format
            const cvcRegex = /^\d{3}$/; // 3-digit CVC

            if (!cardNumberRegex.test(cardNumber)) {
                alert("Invalid card number. Please enter a 16-digit number.");
                return false;
            }
            if (!expirationDateRegex.test(expirationDate)) {
                alert("Invalid expiration date. Please use MM/YY format.");
                return false;
            }
            if (!cvcRegex.test(cvc)) {
                alert("Invalid CVC. Please enter a 3-digit number.");
                return false;
            }

            // Show success message
            alert("Successful transaction!");
            return true;
        }
    </script>
</head>

<body>
    <?php
        // Fetch ticket data from POST request
        $totalTickets = $_POST['totalQuantity'] ?? 0;
        $totalCost = $_POST['totalCost'] ?? 0.00;
    ?>

    <form class="payment-form" onsubmit="return validatePayment();">
        <div class="form-row">
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="cardNumber" placeholder="1234 5678 9101 1121">
        </div>

        <div class="form-row">
            <label for="expiration-date">Expiration Date (MM/YY):</label>
            <input type="text" id="expiration-date" name="expirationDate" placeholder="MM/YY">
        </div>

        <div class="form-row">
            <label for="cvc">CVC:</label>
            <input type="text" id="cvc" name="cvc" placeholder="123">
        </div>

        <div class="form-row">
            <div id="ticket-info">
                <p>Number of tickets: <?= htmlspecialchars($totalTickets) ?></p>
                <p>Total Cost: €<?= htmlspecialchars(number_format($totalCost, 2)) ?></p>
            </div>
        </div>

        <div class="form-row buttons">
            <button type="button" onclick="window.history.back()">Back</button>
            <button type="submit">Submit Payment</button>
        </div>
    </form>
</body>

</html>
