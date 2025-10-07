<?php
session_start();

/* ========= DB CONFIG ========= */
$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "d1";

/* ========= CONNECT ========= */
$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

/* ========= FLASH MESSAGE HELPER ========= */
$flash = "";
if (!empty($_GET['message'])) { $flash = $_GET['message']; }

/* ========= LOGOUT ========= */
if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: ?page=login&message=Logged out successfully");
  exit;
}

/* ========= LOGIN SUBMIT =========
   Demo uses MD5 for quick testing (match your DB inserts).
   For real apps, use password_hash / password_verify. */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
  $user = trim($_POST['username']);
  $pass = md5(trim($_POST['password']));

  // Basic prepared statement (safer than raw SQL)
  $stmt = $conn->prepare("SELECT username, role FROM users WHERE username=? AND password=? LIMIT 1");
  $stmt->bind_param("ss", $user, $pass);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];
    $_SESSION['last_activity'] = time(); // for timeout
    header("Location: ?page=dashboard");
    exit;
  } else {
    $flash = "Invalid username or password!";
  }
  $stmt->close();
}

/* ========= SESSION TIMEOUT ========= */
$timeout = 300; // 5 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
  session_unset();
  session_destroy();
  header("Location: ?page=login&message=Session timed out");
  exit;
}
if (isset($_SESSION['username'])) {
  $_SESSION['last_activity'] = time();
}

/* ========= ROUTER ========= */
$page = isset($_GET['page']) ? $_GET['page'] : (isset($_SESSION['username']) ? 'dashboard' : 'login');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Spotify Clone • Auth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --bg: #000;
      --fg: #fff;
      --muted: #8e8e8e;
      --brand: #1DB954;
      --border: rgba(255,255,255,0.25);
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: var(--bg);
      color: var(--fg);
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
    }
    .box {
      background: #000;
      border: 1px solid #000;
      border-radius: 25px;
      width: 100%;
      max-width: 640px;
      padding: 30px 22px;
      box-shadow: 0 0 15px rgba(255,255,255,0.6);
    }
    .header {
      text-align: center;
      margin-bottom: 24px;
    }
    .logo {
      width: 50px; height: 50px; object-fit: contain;
      filter: drop-shadow(0 0 1px rgba(255,255,255,0.35));
    }
    h1 {
      margin: 12px 0 0;
      font-size: 28px;
      line-height: 1.2;
    }
    .auth-options a {
      display: block;
      width: 75%;
      margin: 12px auto;
      padding: 12px 16px;
      text-align: center;
      text-decoration: none;
      color: var(--fg);
      border: 1px solid var(--fg);
      border-radius: 24px;
      transition: transform .05s ease, background ,2s ease, border-color .2s ease;
    }
    .auth-options a:hover { transform: translateY(-1px); }
    .auth-options img {
      width: 22px; height: 22px; vertical-align: middle; margin-right: 10px;
    }
    hr {
      border: 0;
      border-top: 1px solid var(--border);
      width: 82%;
      margin: 26px auto;
    }
    h5 {
      margin: 0 0 8px 13.5%;
      font-weight: normal;
    }
    input[type="text"], input[type="password"], input[type="email"] {
      background: #000;
      color: var(--fg);
      width: 75%;
      display: block;
      margin: 0 auto 12px;
      padding: 12px 14px;
      border: 1px solid rgb(209, 208, 208);
      border-radius: 6px;
      outline: none;
    }
    input:focus { border-color: var(--brand); }
    .login {
      display: block;
      margin: 22px auto 8px;
      width: 260px; height: 46px;
      background: var(--brand);
      color: #fff;
      border: 0; border-radius: 24px;
      font-size: 16px; cursor: pointer;
      transition: transform .05s ease, filter .2s ease;
    }
    .login:hover { transform: translateY(-1px); filter: brightness(1.05); }
    .signup-text {
      text-align: center;
      color: var(--muted);
      margin-top: 8px;
    }
    .signup-text a { color: #fff; font-weight: bold; text-decoration: none; }
    .back-btn {
      background: transparent;
      color: #fff;
      border: 2px solid #fff;
      border-radius: 999px;
      width: 32px; height: 32px;
      display: inline-grid; place-items: center;
      cursor: pointer;
    }

    /* Table (dashboard) */
    .table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 18px;
    }
    .table th, .table td {
      border: 1px solid #fff;
      padding: 10px 12px;
      text-align: left;
    }
    .table th { background: var(--brand); color: #000; }

    /* Popup */
    .popup {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    .popup-card {
      background: var(--brand);
      color: #fff;
      padding: 26px 24px;
      border-radius: 14px;
      width: min(90vw, 420px);
      text-align: center;
      box-shadow: 0 0 20px rgba(255,255,255,0.3);
    }
    .popup-card button {
      margin-top: 14px;
      background: #000;
      color: #fff;
      border: 0;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
    }

    /* Utility */
    .row { display: flex; gap: 12px; align-items: center; justify-content: space-between; }
    .right { text-align: right; }
    .muted { color: var(--muted); }
    .spaced { margin-top: 20px; }
    .link-btn {
      display: inline-block;
      padding: 8px 14px;
      border: 1px solid var(--fg);
      border-radius: 999px;
      color: var(--fg);
      text-decoration: none;
    }
  </style>
</head>
<body>

<?php if ($page === 'login' && !isset($_SESSION['username'])): ?>
  <div class="box">
    <div class="row">
      <button class="back-btn" onclick="history.back()" aria-label="Back">‹</button>
      <div class="right" style="flex:1;"></div>
    </div>

    <div class="header">
      <!-- Replace src with your logo if available -->
      <img class="logo" src="1.png" alt="Logo" onerror="this.style.display='none'">
      <h1>Login to Spotify</h1>
    </div>

    <nav class="auth-options">
      <a href="#"><img src="logo.png" alt="" onerror="this.style.display='none'">Continue with Google</a>
      <a href="#"><img src="facebook.png" alt="" onerror="this.style.display='none'">Continue with Facebook</a>
      <a href="#"><img src="apple.png" alt="" onerror="this.style.display='none'">Continue with Apple</a>
      <a href="#"><img src="telephone.png" alt="" onerror="this.style.display='none'">Continue with Mobile Number</a>
    </nav>

    <hr>

    <form method="POST" id="loginForm" novalidate>
      <h5>Username</h5>
      <input type="text" name="username" id="username" placeholder="Enter username" required>

      <h5>Password</h5>
      <input type="password" name="password" id="password" placeholder="Enter password" required>

      <button class="login" type="submit" name="login">Login</button>
    </form>

    <p class="signup-text">Don't have an account? <a href="#">Sign up for Spotify</a></p>
  </div>

<?php elseif ($page === 'dashboard' && isset($_SESSION['username'])): ?>
  <div class="box">
    <div class="row">
      <div>
        <h1 style="margin:0;">Dashboard</h1>
        <p class="muted" style="margin:6px 0 0;">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
      </div>
      <div>
        <a class="link-btn" href="?logout=true">Logout</a>
      </div>
    </div>

    <hr>

    <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>

    <?php if ($_SESSION['role'] === 'admin'): ?>
      <div class="spaced">
        <p><strong>Admin Panel</strong></p>
        <p class="muted">You can put admin-only actions here.</p>
      </div>
    <?php else: ?>
      <div class="spaced">
        <p class="muted">Standard user access.</p>
      </div>
    <?php endif; ?>

    <div class="spaced">
      <p class="muted">This page is protected by session. Inactivity timeout: 5 minutes.</p>
    </div>
  </div>

<?php else: ?>
  <?php header("Location: ?page=login"); exit; ?>
<?php endif; ?>

<!-- POPUP (Flash Messages) -->
<div class="popup" id="popup">
  <div class="popup-card">
    <h2 id="popupText">Message</h2>
    <button onclick="hidePopup()">Close</button>
  </div>
</div>

<script>
  // Simple front-end validation to match Spotify feel
  const form = document.getElementById('loginForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const u = document.getElementById('username');
      const p = document.getElementById('password');
      if (!u.value.trim() || !p.value.trim()) {
        e.preventDefault();
        showPopup("Please fill in both username and password.");
      }
    });
  }

  // Popup helpers
  function showPopup(text) {
    const p = document.getElementById('popup');
    const t = document.getElementById('popupText');
    if (t) t.textContent = text || "Done!";
    if (p) p.style.display = 'flex';
  }
  function hidePopup() {
    const p = document.getElementById('popup');
    if (p) p.style.display = 'none';
  }

  // Show PHP/redirect messages as popup
  (function initFlashFromPHP(){
    const msg = <?php echo json_encode($flash, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    if (msg) showPopup(msg);
  })();
</script>
</body>
</html>
