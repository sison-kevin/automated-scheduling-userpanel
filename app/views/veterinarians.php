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
<title>Veterinarians | PetCare Dashboard</title>
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
.card h3 {
  color: var(--primary);
  margin-bottom: 18px;
  font-size: 1.35rem;
  font-weight: 700;
}
.card p {
  color: #555;
  font-size: 0.96rem;
}

/* TABLE STYLES */
.vet-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 25px; /* Added spacing below the intro text */
  animation: fadeUp 0.6s ease;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.vet-table th, .vet-table td {
  padding: 14px 16px;
  text-align: left;
}
.vet-table th {
  background: var(--primary);
  color: #fff;
  border: none;
  font-weight: 600;
}
.vet-table td {
  color: #333;
}
.vet-table tr:nth-child(even) {
  background: #fdf7f2;
}
.vet-table tr:hover {
  background: #fff0e4;
  transition: 0.2s;
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
    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
    <a href="<?= site_url('appointments') ?>">Appointments</a>
    <a href="<?= site_url('pets') ?>">Pets</a>
    <a href="<?= site_url('veterinarians') ?>" class="active">Veterinarians</a>
    <a href="<?= site_url('settings') ?>">Settings</a>
  </aside>

  <!-- Main Content -->
  <section class="main-content">
    <a href="<?= site_url('dashboard') ?>" class="back-link">← Back to Dashboard</a>

    <div class="card">
      <h3>Available Veterinarians</h3>
      <p>Explore our list of qualified veterinarians, their specializations, and contact information below.</p>

      <?php if (!empty($veterinarians)): ?>
        <!-- DEBUG: Show raw data -->
        <?php if (false): // Set to false to hide debug ?>
        <pre style="background: #f0f0f0; padding: 10px; margin: 20px 0; overflow: auto;">
          <?php print_r($veterinarians); ?>
        </pre>
        <?php endif; ?>
        
        <table class="vet-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Specialization</th>
              <th>Contact</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            foreach ($veterinarians as $vet): 
              // Check is_active field (1 = active, 0 = inactive)
              $isActive = isset($vet['is_active']) && $vet['is_active'] == 1;
            ?>
              <tr>
                <td><?= htmlspecialchars($vet['id']); ?></td>
                <td><?= htmlspecialchars($vet['name']); ?></td>
                <td><?= htmlspecialchars($vet['specialization']); ?></td>
                <td><?= htmlspecialchars($vet['contact']); ?></td>
                <td>
                  <?php if ($isActive): ?>
                    <span style="background: #d1f4e0; color: #0f5132; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-block;">● ACTIVE</span>
                  <?php else: ?>
                    <span style="background: #f8d7da; color: #721c24; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-block;">● INACTIVE</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php 
            endforeach;
            ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="background: #fff5ef; padding: 15px; border-radius: 8px; border-left: 5px solid var(--primary); margin-top: 25px;">
          No veterinarians found in the system.
        </p>
      <?php endif; ?>
    </div>
  </section>
</div>

<footer>
  &copy; <?= date('Y'); ?> Automated Scheduling and Tracking System for Veterinary Services – Calapan City, Oriental Mindoro
</footer>

</body>
</html>
