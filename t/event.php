<?php
// --- DATABASE CONNECTION ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// --- CREATE / INSERT EVENT ---
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $date = $_POST['date'];
    $desc = trim($_POST['description']);
    $status = $_POST['status'];

    if ($name && $date) {
        $stmt = $conn->prepare("INSERT INTO spotify_events (name, date, description, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $date, $desc, $status);
        $stmt->execute();
        $stmt->close();
        $message = "✅ Event added successfully!";
    } else {
        $message = "⚠️ Event name and date are required!";
    }
}

// --- UPDATE EVENT ---
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $date = $_POST['date'];
    $desc = trim($_POST['description']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE spotify_events SET name=?, date=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $date, $desc, $status, $id);
    $stmt->execute();
    $stmt->close();
    $message = "✅ Event updated successfully!";
}

// --- DELETE EVENT ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM spotify_events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "🗑️ Event deleted successfully!";
}

// --- FETCH EVENTS ---
$events = $conn->query("SELECT * FROM spotify_events ORDER BY date ASC");

// --- EDIT DATA FOR FORM ---
$edit_event = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM spotify_events WHERE id=$id");
    if ($res->num_rows) $edit_event = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Spotify Events Dashboard</title>
<style>
body { background: #000; color: #fff; font-family: Arial; text-align: center; padding: 40px 0; }
.container { width: 90%; margin: auto; background: #111; border-radius: 20px; padding: 30px; box-shadow: 0 0 15px #1DB954; }
h1 { color: #1DB954; margin-bottom: 20px; }
input, textarea, select, button { padding: 10px; margin: 5px; border-radius: 5px; border: 1px solid #1DB954; background: #000; color: #fff; }
button, a.btn { background: #1DB954; color: #fff; border: none; padding: 10px 15px; border-radius: 20px; cursor: pointer; text-decoration: none; font-weight: bold; }
button:hover, a.btn:hover { background: #18a54b; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; border-bottom: 1px solid #333; text-align: center; }
th { background-color: #1DB954; }
td a { color: red; text-decoration: none; font-weight: bold; }
td a:hover { color: #ff5c5c; }
.message { color: #1DB954; font-weight: bold; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="container">
<h1>🎶 Spotify Events Dashboard</h1>

<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>

<!-- ADD / EDIT FORM -->
<form method="POST">
<input type="hidden" name="id" value="<?= $edit_event['id'] ?? '' ?>">
<input type="text" name="name" placeholder="Event Name" required value="<?= $edit_event['name'] ?? '' ?>">
<input type="date" name="date" required value="<?= $edit_event['date'] ?? '' ?>">
<textarea name="description" placeholder="Description"><?= $edit_event['description'] ?? '' ?></textarea>
<select name="status">
    <option value="Open" <?= isset($edit_event['status']) && $edit_event['status']=='Open' ? 'selected' : '' ?>>Open</option>
    <option value="Closed" <?= isset($edit_event['status']) && $edit_event['status']=='Closed' ? 'selected' : '' ?>>Closed</option>
</select>
<button type="submit" name="<?= $edit_event ? 'update' : 'add' ?>"><?= $edit_event ? 'Update Event' : 'Add Event' ?></button>
<?php if($edit_event): ?>
<a href="spotify_events.php" class="btn">Cancel Edit</a>
<?php endif; ?>
</form>

<!-- EVENTS TABLE -->
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Date</th>
<th>Description</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = $events->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= $row['date'] ?></td>
<td><?= htmlspecialchars($row['description']) ?></td>
<td><?= $row['status'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> | 
<a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php if($events->num_rows==0): ?>
<tr><td colspan="6">No events found.</td></tr>
<?php endif; ?>
</table>

<a href="spotify_login.php" class="btn">⬅ Back to Login</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
