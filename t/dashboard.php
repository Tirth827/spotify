<?php
session_start();

// Session timeout (optional)
$timeout_duration = 60 * 5; // 5 minutes

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout_duration) {
  session_unset();
  session_destroy();
  header("Location: login.php?timeout=true");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Spotify Dashboard</title>
  <style>
    body {
      background-color: #000;
      color: white;
      font-family: Arial;
      text-align: center;
      padding-top: 100px;
    }
    a {
      background-color: #1DB954;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 25px;
    }
  </style>
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> 🎧</h1>
  <p>You are successfully logged in.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
