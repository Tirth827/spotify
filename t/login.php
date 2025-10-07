<?php
session_start();

// --- DATABASE CONNECTION ---
$servername = "localhost";
$username = "root"; // XAMPP default
$password = "";     // empty by default
$dbname = "app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

$message = "";

// --- FORM SUBMIT HANDLER ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $pass = $_POST['password'];

  // Check credentials
  $stmt = $conn->prepare("SELECT * FROM data WHERE email = ? AND pass = ?");
  $stmt->bind_param("ss", $email, $pass);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $_SESSION['user'] = $email;
    $_SESSION['login_time'] = time(); // For session timeout
    header("Location: dashboard.php");
    exit;
  } else {
    $message = "❌ Invalid email or password!";
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Spotify Login</title>
  <style>
    body {
      background-color: #000;
      font-family: Arial, sans-serif;
      color: white;
    }

    .box {
      background-color: #000;
      border-radius: 25px;
      margin: 100px auto;
      max-width: 600px;
      padding: 30px 20px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
      text-align: center;
    }

    h1 img {
      width: 50px;
    }

    h5 {
      text-align: left;
      margin-left: 15%;
      margin-top: 20px;
      margin-bottom: 5px;
    }

    input[type="text"], input[type="password"] {
      background-color: #000;
      width: 70%;
      padding: 10px;
      margin-left: 15%;
      border: 1px solid rgb(209, 208, 208);
      border-radius: 4px;
      color: white;
    }

    .login {
      display: block;
      margin: 30px auto;
      width: 250px;
      height: 45px;
      background-color: #1DB954;
      color: white;
      border: none;
      border-radius: 20px;
      font-size: large;
      cursor: pointer;
    }

    .login:hover {
      background-color: #18a54b;
    }

    .signup-text {
      color: rgb(142, 142, 142);
      text-align: center;
    }

    .signup-text a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .message {
      margin-top: 15px;
      color: #1DB954;
      font-weight: bold;
    }

    .error {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="box">
    <h1><img src="1.png" alt="Logo"><br>Login to Spotify</h1>

    <form method="POST" action="">
      <h5>Email</h5>
      <input type="text" name="email" placeholder="Enter your email" required>

      <h5>Password</h5>
      <input type="password" name="password" placeholder="Enter your password" required>

      <button type="submit" class="login">Login</button>

      <?php if ($message): ?>
        <div class="<?php echo (strpos($message, '❌') !== false) ? 'error' : 'message'; ?>">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>
    </form>

    <p class="signup-text">Don’t have an account? <a href="register.php">Sign up here</a></p>
  </div>

</body>
</html>
