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
<title>Veterinary Services Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
  --primary: #ff914d;
  --light-orange: #ffb47b;
  --bg: #f7f9fb;
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
  padding: 40px 60px;
}

/* DASHBOARD STATS */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}
.stat-card {
  background: linear-gradient(135deg, #fff 60%, #fff8f3);
  border-radius: 14px;
  padding: 25px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(255,145,77,0.2);
}
.stat-card h4 {
  color: #777;
  font-size: 0.95rem;
  margin-bottom: 10px;
}
.stat-card p {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
}

/* CARDS */
.card {
  background: var(--card);
  border-radius: 14px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.card h3 {
  color: var(--primary);
  margin-bottom: 20px;
  font-size: 1.3rem;
}
.card:hover {
  transform: translateY(-2px);
}

/* SEARCH BAR */
.search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.search-bar input {
  width: 250px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  outline: none;
  transition: 0.2s;
}
.search-bar input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(255,145,77,0.2);
}

/* TABLE */
.appointments {
  width: 100%;
  border-collapse: collapse;
}
.appointments th, .appointments td {
  padding: 14px 16px;
  text-align: left;
}
.appointments th {
  background: var(--primary);
  color: #fff;
  border: none;
}
.appointments tr:nth-child(even) {
  background: #fdf7f2;
}
.appointments tr:hover {
  background: #fff0e4;
  transition: 0.2s;
}

/* FOOTER */
footer {
  text-align: center;
  padding: 20px;
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
    <h1>Veterinary Services Dashboard</h1>
    <div class="user-info">
      <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> |
      <?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
    </div>
  </div>
  <a href="<?= site_url('logout') ?>" class="btn">Logout</a>
</header>

<div class="dashboard">
  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Navigation</h3>
    <a href="<?= site_url('landing') ?>">Landing Page</a>
    <a href="<?= site_url('dashboard') ?>" class="active">Dashboard</a>
    <a href="<?= site_url('appointments') ?>">Appointments</a>
    <a href="<?= site_url('pets') ?>">Pets</a>
    <a href="<?= site_url('veterinarians') ?>">Veterinarians</a>
    <a href="<?= site_url('settings') ?>">Settings</a>
  </aside>

  <!-- Main Content -->
  <section class="main-content">

    <div class="card">
      <h3>Welcome Back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</h3>
      <p>Manage appointments, track your pets’ medical records, and connect with veterinarians — all in one clean, modern dashboard.</p>
    </div>

    <div class="card">
      <div class="search-bar">
        <h3>Upcoming Appointments</h3>
      </div>

      <table class="appointments">
        <thead>
          <tr>
            <th>Date</th>
            <th>Pet Name</th>
            <th>Service</th>
            <th>Veterinarian</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appt): ?>
              <tr>
                <td><?= htmlspecialchars($appt['appointment_date']); ?></td>
                <td><?= htmlspecialchars($appt['pet_name'] ?? 'N/A'); ?></td>
        <td><?= htmlspecialchars($appt['service'] ?? 'N/A'); ?></td>
        <td><?= htmlspecialchars($appt['veterinarian'] ?? 'N/A'); ?></td>
                <td><?= htmlspecialchars($appt['status']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No upcoming appointments.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<footer>
  &copy; <?= date('Y'); ?> Automated Scheduling and Tracking System for Veterinary Services – Calapan City, Oriental Mindoro
</footer>

</body>
</html>
