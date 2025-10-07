<?php
// --- STEP 1: Initialize variables ---
$message = "";

// --- STEP 2: Check if form is submitted using POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- STEP 3: Sanitize and validate input ---
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $feedback = htmlspecialchars(trim($_POST['feedback']));

    if (!empty($name) && !empty($email) && !empty($feedback)) {

        // --- STEP 4: Store data in text file in CSV format ---
        $file = fopen("data.txt", "a"); // "a" = append mode
        if ($file) {
            $data = "$name, $email, $feedback\n";
            fwrite($file, $data);
            fclose($file);
            $message = "✅ Thank you, $name! Your data has been saved.";
        } else {
            $message = "❌ Error: Unable to open file for writing.";
        }
    } else {
        $message = "⚠️ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Submission - PHP File Storage</title>
    <style>
        body {
            background-color: #000;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            margin-top: 100px;
        }
        form {
            background-color: #111;
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            margin: 0 auto;
            box-shadow: 0 0 10px #1DB954;
        }
        input, textarea {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #1DB954;
            background-color: #000;
            color: white;
        }
        button {
            background-color: #1DB954;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #18a54b;
        }
        .msg {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>Submit Your Feedback 🎧</h1>

    <form method="POST" action="">
        <input type="text" name="name" placeholder="Enter your name" required><br>
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <textarea name="feedback" placeholder="Enter your feedback" rows="4" required></textarea><br>
        <button type="submit">Submit</button>
    </form>

    <?php if ($message): ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>

</body>
</html>
