<?php
session_start();
$conn = new mysqli("localhost", "root", "", "app");
if ($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);

// --- ADMIN CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: spotify_login.php");
    exit();
}

// Verify role
$stmt = $conn->prepare("SELECT role FROM spotify_users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    die("❌ Access Denied. Admins only.");
}

$message = "";

// --- DELETE USER ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM spotify_users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $message = "🗑️ User deleted successfully!";
}

// --- UPDATE USER STATUS ---
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE spotify_users SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    $message = "✅ User status updated!";
}

// --- FETCH ALL USERS ---
$users = $conn->query("SELECT id, email, role, status FROM spotify_users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body { background: #000; color: #fff; font-family: Arial; text-align: center; padding: 40px 0; }
.container { width: 90%; margin: auto; background: #111; padding: 30px; border-radius: 20px; box-shadow: 0 0 15px #1DB954; }
h1 { color: #1DB954; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; border-bottom: 1px solid #333; text-align: center; }
th { background: #1DB954; }
select, button, a { padding: 5px 10px; border-radius: 5px; border: none; cursor: pointer; }
button, a { font-weight: bold; text-decoration: none; }
a { background: red; color: #fff; }
a:hover { background: darkred; }
button { background: #1DB954; color: #fff; }
button:hover { background: #18a54b; }
.message { margin: 15px 0; color: #1DB954; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
<h1>🎵 Admin Dashboard</h1>

<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>

<table>
<tr>
<th>ID</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php while($user = $users->fetch_assoc()): ?>
<tr>
<td><?= $user['id'] ?></td>
<td><?= htmlspecialchars($user['email']) ?></td>
<td><?= $user['role'] ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="id" value="<?= $user['id'] ?>">
<select name="status">
<option value="active" <?= $user['status']=='active' ? 'selected' : '' ?>>Active</option>
<option value="inactive" <?= $user['status']=='inactive' ? 'selected' : '' ?>>Inactive</option>
</select>
<button type="submit" name="update_status">Update</button>
</form>
</td>
<td>
<a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure to delete this user?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<a href="spotify_logout.php">Logout</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
