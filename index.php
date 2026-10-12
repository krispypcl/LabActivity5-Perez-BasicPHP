<?php
// index.php
// Purpose: The protected home page. Only logged-in users can see it.
// It also handles logging out.

// [START SESSION]
// Sessions let PHP remember whether a user is logged in across different pages.
session_start();

// [PROTECTED ROUTE GUARD]
// If there is no logged-in user in the session, send them to the login page.
if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

// [HANDLE LOGOUT]
// Logging out changes the user's state, so it is done with POST instead of a plain link (GET).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Clear all session data, then destroy the session itself.
    session_unset();
    session_destroy();

    header('Location: login.php');
    exit;
}

$userEmail = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Welcome!</h1>

    <!-- [SHOW LOGGED-IN USER] -->
    <!-- htmlspecialchars() makes sure the email is shown as plain text (prevents XSS). -->
    <p>You are logged in as <strong><?= htmlspecialchars($userEmail) ?></strong>.</p>

    <!-- [LOGOUT FORM] -->
    <form method="POST" action="index.php">
        <button type="submit" name="logout">Log out</button>
    </form>
</body>
</html>
