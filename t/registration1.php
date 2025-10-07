<?php
session_start();

// --- DATABASE CONNECTION ---
$conn = new mysqli("localhost", "root", "", "app");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// --- FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. SANITIZE INPUTS
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pass = trim($_POST['password']);

    // 2. VALIDATE INPUTS
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Invalid email format!";
    } elseif (strlen($pass) < 6) {
        $message = "⚠️ Password must be at least 6 characters!";
    } else {
        // 3. CHECK IF USER EXISTS
        $stmt = $conn->prepare("SELECT id FROM spotify_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "⚠️ Email already registered!";
        } else {
            // 4. HASH PASSWORD
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            // 5. INSERT INTO DATABASE
            $insert = $conn->prepare("INSERT INTO spotify_users (email, pass) VALUES (?, ?)");
            $insert->bind_param("ss", $email, $hashed_pass);
            if ($insert->execute()) {
                $message = "✅ Registration successful! You can now log in.";
            } else {
                $message = "❌ Error occurred. Try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Spotify Registration</title>
<style>
body { background: #000; color: #fff; font-family: Arial, sans-serif; }
.container { width: 400px; margin: 80px auto; padding: 30px; background: #111; border-radius: 20px; box-shadow: 0 0 15px #1DB954; text-align: center; }
h1 { color: #1DB954; margin-bottom: 20px; }
input { width: 80%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #1DB954; background: #000; color: #fff; }
button { background: #1DB954; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; color: #fff; }
button:hover { background: #18a54b; }
.message { margin-top: 15px; color: #1DB954; font-weight: bold; }
a { color: #1DB954; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
<h1>Sign Up for Spotify</h1>
<form method="POST">
<input type="text" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password (min 6 chars)" required>
<button type="submit">Register</button>
</form>
<p class="message"><?= $message ?></p>
<p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
