<?php
// register.php
// Purpose: Lets a logged-out user create an account.
// The email is saved in the browser's localStorage so login.php can check it later.

// [START SESSION]
// Sessions let PHP remember whether a user is logged in across different pages.
session_start();

// [UNPROTECTED ROUTE GUARD]
// A logged-in user has no reason to register again, so we send them to the home page.
if (isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

// [HANDLE FORM SUBMISSION]
// We only process the form when it is sent with POST,
// because POST is the correct method for submitting form data.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // [VALIDATE EMAIL ON THE SERVER]
    // JavaScript can be turned off by the user, so the server checks the input again.
    if ($email === '') {
        $errorMessage = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        // [REDIRECT TO LOGIN]
        // Registration is done, so send the user to the login page.
        // "?registered=1" is a GET value that tells login.php to show a success message.
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Create an account</h1>

    <!-- [SERVER ERROR MESSAGE] -->
    <!-- htmlspecialchars() stops any HTML/JS inside the message from running (prevents XSS). -->
    <?php if ($errorMessage !== ''): ?>
        <p><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- [CLIENT ERROR MESSAGE] -->
    <!-- Hidden at first; JavaScript shows it when the input is wrong. -->
    <p id="client-error" hidden></p>

    <!-- [REGISTER FORM] -->
    <!-- "novalidate" turns off the browser's default popups so our own messages show instead. -->
    <form id="register-form" method="POST" action="register.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com">
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>

    <script>
        const registerForm = document.getElementById('register-form');
        const emailInput = document.getElementById('email');
        const clientError = document.getElementById('client-error');

        // A simple pattern for "something@something.something".
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // [SHOW CLIENT ERROR]
        // Puts the error text on the page so the user knows what to fix.
        function showError(message) {
            clientError.textContent = message;
            clientError.hidden = false;
        }

        registerForm.addEventListener('submit', function (event) {
            // Lowercase so "Juan@Mail.com" and "juan@mail.com" count as the same email.
            const email = emailInput.value.trim().toLowerCase();

            // [VALIDATE EMAIL ON THE CLIENT]
            // preventDefault() stops the form from being sent when the input is wrong.
            if (email === '') {
                event.preventDefault();
                showError('Email is required.');
                return;
            }

            if (!emailPattern.test(email)) {
                event.preventDefault();
                showError('Please enter a valid email address.');
                return;
            }

            // [SAVE EMAIL TO LOCALSTORAGE]
            // The email is stored in the browser so login.php can compare against it later.
            // After this, the form submits normally to PHP.
            localStorage.setItem('registeredEmail', email);
        });
    </script>
</body>
</html>
