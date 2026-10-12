<?php
// login.php
// Purpose: Lets a logged-out user log in.
// JavaScript checks the typed email against the one saved in localStorage during registration.

// [START SESSION]
// Sessions let PHP remember whether a user is logged in across different pages.
session_start();

// [UNPROTECTED ROUTE GUARD]
// A logged-in user should not see the login page, so we send them to the home page.
if (isset($_SESSION['user_email'])) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

// [READ GET VALUE]
// register.php sends us here with "?registered=1" after a successful sign-up.
$justRegistered = isset($_GET['registered']);

// [HANDLE FORM SUBMISSION]
// We only process the form when it is sent with POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // [VALIDATE EMAIL ON THE SERVER]
    // JavaScript can be turned off by the user, so the server checks the input again.
    if ($email === '') {
        $errorMessage = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        // [LOG THE USER IN]
        // session_regenerate_id() gives the user a fresh session ID after login,
        // which protects against session hijacking (session fixation).
        session_regenerate_id(true);
        $_SESSION['user_email'] = $email;

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
</head>
<body>
    <h1>Log in</h1>

    <!-- [SUCCESS MESSAGE] -->
    <?php if ($justRegistered): ?>
        <p>Account created. You can now log in.</p>
    <?php endif; ?>

    <!-- [SERVER ERROR MESSAGE] -->
    <!-- htmlspecialchars() stops any HTML/JS inside the message from running (prevents XSS). -->
    <?php if ($errorMessage !== ''): ?>
        <p><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- [CLIENT ERROR MESSAGE] -->
    <!-- Hidden at first; JavaScript shows it when the email is wrong. -->
    <p id="client-error" hidden></p>

    <!-- [LOGIN FORM] -->
    <!-- "novalidate" turns off the browser's default popups so our own messages show instead. -->
    <form id="login-form" method="POST" action="login.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com">
        <button type="submit">Log in</button>
    </form>

    <p>No account yet? <a href="register.php">Register</a></p>

    <script>
        const loginForm = document.getElementById('login-form');
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

        loginForm.addEventListener('submit', function (event) {
            // Lowercase so it matches how register.php saved the email.
            const email = emailInput.value.trim().toLowerCase();

            // [GET SAVED EMAIL FROM LOCALSTORAGE]
            // This is the email saved during registration (null if nobody registered yet).
            const registeredEmail = localStorage.getItem('registeredEmail');

            // [VALIDATE EMAIL ON THE CLIENT]
            // Each check gives its own clear message.
            // preventDefault() stops the form from being sent when something is wrong.
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

            if (registeredEmail === null) {
                event.preventDefault();
                showError('No account found. Please register first.');
                return;
            }

            if (email !== registeredEmail) {
                event.preventDefault();
                showError('Incorrect email. Please use the email you registered with.');
                return;
            }

            // All checks passed, so the form submits normally to PHP.
        });
    </script>
</body>
</html>
