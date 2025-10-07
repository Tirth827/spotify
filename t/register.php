<?php
// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validation
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare user data
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Load existing users
        $users_file = 'users.json';
        $users = [];
        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true) ?? [];
        }

        // Check if username or email already exists
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $errors[] = "Username already exists";
                break;
            }
            if ($user['email'] === $email) {
                $errors[] = "Email already registered";
                break;
            }
        }

        if (empty($errors)) {
            // Add new user
            $users[] = $user_data;

            // Save to file
            if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
                // Redirect to success page
                header("Location: success.php?username=" . urlencode($username));
                exit;
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
    echo "
    <html>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Registration Success</title>
      <style>
        body {
          background-color: #000;
          color: white;
          font-family: Arial, sans-serif;
          text-align: center;
        }
        .box {
          margin: 150px auto;
          background-color: #111;
          padding: 40px;
          width: 400px;
          border-radius: 20px;
          box-shadow: 0 0 20px rgba(255,255,255,0.3);
        }
        a {
          color: #1DB954;
          text-decoration: none;
        }
        button {
          background-color: #1DB954;
          border: none;
          color: white;
          padding: 10px 25px;
          border-radius: 15px;
          cursor: pointer;
          font-size: 16px;
        }
        button:hover {
          background-color: #17a44b;
        }
      </style>
    </head>
    <body>
      <div class='box'>
        <h1>✅ Registration Successful!</h1>
        <p>Welcome, <strong>$username</strong></p>
        <p>Your data has been saved successfully.</p>
        <a href='spotify_register.php'><button>Go Back</button></a>
      </div>
    </body>
    </html>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Spotify Signup</title>
  <style>
    body {
      background-color: #000;
      color: white;
      font-family: Arial, sans-serif;
      text-align: center;
    }

    .box {
      background-color: #111;
      border-radius: 20px;
      padding: 40px;
      width: 350px;
      margin: 100px auto;
      box-shadow: 0 0 15px rgba(255,255,255,0.3);
    }

    h1 {
      color: white;
      margin-bottom: 20px;
    }

    input {
      width: 80%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #888;
      background-color: black;
      color: white;
    }

    button {
      background-color: #1DB954;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 16px;
      margin-top: 15px;
    }

    button:hover {
      background-color: #17a44b;
    }

    a {
      color: #1DB954;
      text-decoration: none;
    }

    .success {
      color: #1DB954;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="box">
    <h1>Sign Up for Spotify</h1>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="#">Login here</a></p>
  </div>

</body>
</html>
