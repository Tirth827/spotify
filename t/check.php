<?php
if (isset($_GET['name']) && strlen($_GET['name']) > 0) {
    $name = htmlentities($_GET['name']);
    echo "<p>Hello, <strong>$name</strong>!</p>";
} else {
    echo "<p>Name parameter missing.</p>";
}
?>
