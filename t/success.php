<?php
if (!isset($_GET['username'])) {
    header("Location: register.php");
    exit;
}
$username = htmlspecialchars($_GET['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .success-icon {
            font-size: 48px;
            color: #1DB954;
            margin-bottom: 20px;
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
            margin: 10px;
        }
        button:hover {
            background-color: #17a44b;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="success-icon">✅</div>
        <h1>Registration Successful!</h1>
        <p>Welcome, <strong><?php echo $username; ?></strong>!</p>
        <p>Your account has been created successfully.</p>
        <div>
            <a href="login.php"><button>Login Now</button></a>
            <a href="index.php"><button>Home</button></a>
        </div>
    </div>
</body>
</html>