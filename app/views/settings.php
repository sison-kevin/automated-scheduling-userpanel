<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

if (!isset($_SESSION['user_id'])) {
  header('Location: ' . site_url('login'));
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Account Settings | PetCare Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
  --primary: #ff914d;
  --light-orange: #ffb47b;
  --bg: #f8fafc;
  --text: #222;
  --card: #fff;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text);
  line-height: 1.6;
}

/* ===== HEADER ===== */
header {
  position: sticky;
  top: 0;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(12px);
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 50px;
  z-index: 100;
}
header h1 {
  color: var(--primary);
  font-size: 1.4rem;
  font-weight: 700;
  letter-spacing: 0.3px;
}
.user-info {
  color: #555;
  font-size: 0.9rem;
  margin-top: 5px;
}
.btn {
  background: var(--primary);
  color: #fff;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 4px 10px rgba(255,145,77,0.25);
  transition: all 0.3s ease;
}
.btn:hover {
  background: var(--light-orange);
  transform: translateY(-2px);
}

/* ===== LAYOUT ===== */
.dashboard {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
}

/* ===== SIDEBAR ===== */
.sidebar {
  background: var(--card);
  box-shadow: 3px 0 12px rgba(0,0,0,0.05);
  padding: 40px 20px;
  display: flex;
  flex-direction: column;
  border-right: 1px solid #eee;
}
.sidebar h3 {
  text-align: center;
  color: var(--primary);
  margin-bottom: 30px;
  font-weight: 700;
}
.sidebar a {
  display: block;
  padding: 12px 16px;
  border-radius: 10px;
  color: #444;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  margin-bottom: 8px;
}
.sidebar a:hover,
.sidebar a.active {
  background: linear-gradient(45deg, var(--primary), var(--light-orange));
  color: white;
  box-shadow: 0 3px 12px rgba(255,145,77,0.25);
  transform: translateX(5px);
}

/* ===== MAIN ===== */
.main-content {
  padding: 50px 70px;
  animation: fadeIn 0.4s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  background: var(--card);
  border-radius: 14px;
  padding: 40px 35px;
  margin-bottom: 30px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.06);
  transition: 0.3s;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}
.card h3, .card h2 {
  color: var(--primary);
  margin-bottom: 18px;
  font-size: 1.35rem;
  font-weight: 700;
}
.card p {
  color: #555;
  font-size: 0.96rem;
  margin-bottom: 25px;
}

/* ===== FORM STYLING ===== */
form label {
  display: block;
  font-weight: 600;
  margin-top: 15px;
  color: #333;
}
form input {
  width: 100%;
  padding: 10px 12px;
  margin-top: 6px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: border-color 0.2s;
}
form input:focus {
  border-color: var(--primary);
  outline: none;
  box-shadow: 0 0 0 3px rgba(255,145,77,0.15);
}
form button {
  margin-top: 20px;
  background: var(--primary);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
form button:hover {
  background: var(--light-orange);
  transform: translateY(-2px);
}

/* BACK LINK */
.back-link {
  display: inline-block;
  margin-bottom: 25px;
  color: var(--primary);
  font-weight: 500;
  text-decoration: none;
  transition: 0.2s;
}
.back-link:hover {
  text-decoration: underline;
}

/* FOOTER */
footer {
  text-align: center;
  padding: 25px;
  color: #666;
  font-size: 0.9rem;
  border-top: 1px solid #eee;
  background: #fff;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .dashboard {
    grid-template-columns: 1fr;
  }
  .sidebar {
    flex-direction: row;
    justify-content: center;
    border-right: none;
    border-bottom: 1px solid #eee;
  }
  .main-content {
    padding: 30px 20px;
  }
}
</style>
</head>

<body>

<header>
  <div>
    <h1>PetCare Dashboard</h1>
    <div class="user-info">
      <?= htmlspecialchars($_SESSION['user_name']); ?> |
      <?= htmlspecialchars($_SESSION['user_email']); ?>
    </div>
  </div>
  <a href="<?= site_url('logout') ?>" class="btn">Logout</a>
</header>

<div class="dashboard">
  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Navigation</h3>
    <a href="<?= site_url('landing') ?>">Landing Page</a>
    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
    <a href="<?= site_url('appointments') ?>">Appointments</a>
    <a href="<?= site_url('pets') ?>">Pets</a>
    <a href="<?= site_url('veterinarians') ?>">Veterinarians</a>
    <a href="<?= site_url('settings') ?>" class="active">Settings</a>
  </aside>

  <!-- Main content -->
  <section class="main-content">
    <a href="<?= site_url('dashboard') ?>" class="back-link">← Back to Dashboard</a>

    <?php if (isset($_SESSION['success'])): ?>
      <div style="background:#d4edda;color:#155724;padding:15px;border-radius:8px;border:1px solid #c3e6cb;margin-bottom:20px;">
        ✓ <?= htmlspecialchars($_SESSION['success']); ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background:#f8d7da;color:#721c24;padding:15px;border-radius:8px;border:1px solid #f5c6cb;margin-bottom:20px;">
        ✗ <?= htmlspecialchars($_SESSION['error']); ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
      <h2>Account Settings</h2>
      <p>Update your personal details and contact information below.</p>

      <form method="POST" action="<?= site_url('settings/update_profile'); ?>">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($_SESSION['user_name']); ?>" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($_SESSION['user_email']); ?>" required>

        <button type="submit">Save Changes</button>
      </form>
    </div>

    <div class="card">
      <h3>Change Password</h3>
      <p>Keep your account secure by regularly updating your password.</p>

      <form method="POST" action="<?= site_url('settings/change_password'); ?>">
        <label for="current_password">Current Password</label>
        <input type="password" name="current_password" id="current_password" required>

        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">Update Password</button>
      </form>
    </div>
  </section>
</div>

<footer>
  &copy; <?= date('Y'); ?> Automated Scheduling and Tracking System for Veterinary Services – Calapan City, Oriental Mindoro
</footer>

</body>
</html>
