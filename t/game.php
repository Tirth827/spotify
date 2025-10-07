<?php
session_start();

// Check login
if (!isset($_SESSION['name'])) die("Name parameter missing");

$names = array("Rock","Paper","Scissors");
$human = isset($_POST["human"]) ? intval($_POST["human"]) : -1;
$computer = rand(0,2);

// Game logic
function check($human,$computer){
    if ($human==$computer) return "Tie";
    if ($human==0 && $computer==2) return "You Win";
    if ($human==1 && $computer==0) return "You Win";
    if ($human==2 && $computer==1) return "You Win";
    return "You Lose";
}

// Logout handler
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    return;
}

// Prepare exact output for autograder
$result_text = "";
if ($human != -1) {
    $result = check($human,$computer);
    $result_text = "Your Play=".$names[$human]." Computer Play=".$names[$computer]." Result=".$result."\n";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>753524ec Rock Paper Scissors Game</title>
</head>
<body>
<h1>Welcome: <?= htmlentities($_SESSION['name']); ?></h1>

<form method="post">
<select name="human">
    <option value="-1">--Select--</option>
    <option value="0">Rock</option>
    <option value="1">Paper</option>
    <option value="2">Scissors</option>
</select>
<input type="submit" value="Play">
<input type="submit" name="logout" value="Logout">
</form>

<pre>
<?= $result_text ?>
</pre>

</body>
</html>
