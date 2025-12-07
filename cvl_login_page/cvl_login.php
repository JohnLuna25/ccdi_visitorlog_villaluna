<?php
session_start();

// Temporary credentials
$correct_username = 'admin';
$correct_password = 'admin';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === $correct_username && $password === $correct_password) {
        // login success
        $_SESSION['user_id'] = 1; // dummy id
        $_SESSION['username'] = $username;

        // Correct redirection path
        header('Location: http://localhost/john_webdev/ccdi_visitorlog_villaluna/cvl_web_pages/cvl_visit_logs.php');
        exit;
    } else {
        $errors[] = 'Invalid username or password.';
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCDI Visitor Log</title>
    <link rel="stylesheet" href="cvl_login.css">
</head>
<body>
    <div class="login-title">
        <h1>CCDI Visitor Log</h1>

       <?php if ($errors): ?>
            <div id="login-error" style="color:red;">
                <?php foreach($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
        <?php endif; ?>

        <form class="login-form" action="" method="post" autocomplete="off" novalidate>
            <!-- Hidden fields to suppress Chrome password autocomplete -->
            <input type="text" name="fakeusernameremembered" style="display:none">
            <input type="password" name="fakepasswordremembered" style="display:none">

            <label id="login-username" for="username">Username:</label>
            <input type="text" id="username" name="username" required
                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

            <label id="login-password" for="password">Password:</label>
            <input type="password" id="password" name="password" required
                autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">

            <button type="submit" class="login">Login</button>
            <button type="reset" class="reset">Reset</button>
        </form>
    </div>

    <script>
        // Move focus from username to password when Enter is pressed
        document.getElementById('username').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
