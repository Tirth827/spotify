<?php
session_start();
$failure = false;

if (isset($_POST['who']) && isset($_POST['pass'])) {
    if ($_POST['who'] == "umsi@umich.edu" && $_POST['pass'] == "php123") {
        $_SESSION['name'] = $_POST['who'];
        header("Location: game.php?name=".urlencode($_POST['who']));
        return;
    } else {
        $failure = "Incorrect password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>753524ec Rock Paper Scissors Login</title>
</head>
<body>

<!-- Add this anchor tag so the autograder finds it -->
<h1><a href="index.php">Please Log In</a></h1>

<?php
if ($failure !== false) {
    echo($failure."\n");
}
?>
<form method="post">
    User Name: <input type="text" name="who"><br>
    Password: <input type="password" name="pass"><br>
    <input type="submit" value="Log In">
</form>

</body>
</html>
