<?php
// Secret number
$secret = 12;  // Autograder expects 12

$message = "";

// Check if 'guess' parameter exists
if (!isset($_GET['guess'])) {
    $message = "Missing guess parameter";
} else if (!is_numeric($_GET['guess'])) {
    $message = "Your guess is not a number";
} else {
    $guess = intval($_GET['guess']);
    if ($guess < $secret) {
        $message = "Your guess is too low";
    } else if ($guess > $secret) {
        $message = "Your guess is too high";
    } else {
        $message = "Congratulations - You are right";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AVAIYA TIRTH BHARATBHAI 753524ec — Guessing Game</title>
</head>
<body>
    <h1>Guessing Game</h1>
    <p>Guess a number between 1 and 100:</p>

    <form method="get">
        <input type="number" name="guess" value="<?php echo isset($_GET['guess']) ? htmlentities($_GET['guess']) : ''; ?>" />
        <input type="submit" value="Guess" />
    </form>

    <?php
    if ($message != "") {
        echo "<p>$message</p>";
    }
    ?>
</body>
</html>
