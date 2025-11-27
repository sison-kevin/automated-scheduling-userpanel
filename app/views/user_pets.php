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
<title>My Pets | Veterinary Dashboard</title>
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
  transition: filter 0.3s ease;
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
  animation: fadeIn 0.4s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== CARDS ===== */
.card {
  background: var(--card);
  border-radius: 14px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.card h2 {
  color: var(--primary);
  margin-bottom: 10px;
  font-size: 1.4rem;
}
.card:hover {
  transform: translateY(-2px);
}

/* ===== PETS GRID ===== */
.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 25px;
  margin-top: 25px;
}
.pet-card {
  background: linear-gradient(135deg, #fff 60%, #fff8f3);
  border-radius: 12px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 8px 24px rgba(0,0,0,0.05);
  transition: 0.3s;
}
.pet-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(255,145,77,0.2);
}
.pet-card img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
  border: 3px solid var(--primary);
}
.pet-card h4 {
  color: var(--primary);
  margin-bottom: 5px;
}
.pet-card p {
  color: #555;
  margin: 3px 0;
}
.actions a {
  display: inline-block;
  margin: 5px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
.actions .edit {
  background: #ffb347;
  color: #333;
}
.actions .delete {
  background: #ff6b6b;
  color: white;
}
.actions a:hover { opacity: 0.9; }

/* ===== OVERLAY FORM ===== */
.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(6px);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 999;
}
.overlay.active { display: flex; }
.form-card {
  background: white;
  padding: 30px;
  border-radius: 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  width: 400px;
  animation: fadeIn 0.3s ease;
  position: relative;
}
.form-card h3 {
  color: var(--primary);
  text-align: center;
  margin-bottom: 20px;
}
form label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
}
form input, form select, form textarea {
  width: 100%;
  padding: 10px;
  margin-top: 6px;
  border-radius: 8px;
  border: 1px solid #ddd;
  outline: none;
  transition: 0.2s;
}
form input:focus, form select:focus, form textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 2px rgba(255,145,77,0.2);
}
form button {
  margin-top: 18px;
  background: var(--primary);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(255,145,77,0.25);
  transition: all 0.3s ease;
  width: 100%;
}
form button:hover {
  background: var(--light-orange);
  transform: translateY(-2px);
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 16px;
  background: none;
  border: none;
  font-size: 1.4rem;
  color: #555;
  cursor: pointer;
}

/* ===== FOOTER ===== */
footer {
  text-align: center;
  padding: 20px;
  color: #666;
  font-size: 0.9rem;
  border-top: 1px solid #eee;
  background: #fff;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
  .dashboard { grid-template-columns: 1fr; }
  .sidebar {
    flex-direction: row;
    justify-content: center;
    border-bottom: 1px solid #eee;
  }
  .main-content { padding: 30px 20px; }
}

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
  --primary: #ff914d;
  --primary-light: #ffb47b;
  --accent: #fff4ed;
  --bg: #f8fafc;
  --card: #ffffff;
  --text-dark: #222;
  --text-muted: #666;
  --border: #e6e8ec;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text-dark);
  transition: 0.3s ease all;
}

/* HEADER */
header {
  position: sticky;
  top: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 50px;
  z-index: 100;
}

header h1 {
  font-weight: 700;
  font-size: 1.4rem;
  color: var(--primary);
  letter-spacing: 0.2px;
}

.user-info {
  font-size: 0.9rem;
  color: var(--text-muted);
}

.btn {
  background: var(--primary);
  color: #fff;
  padding: 10px 20px;
  border-radius: 10px;
  font-weight: 600;
  text-decoration: none;
  transition: 0.3s ease all;
  box-shadow: 0 4px 12px rgba(255, 145, 77, 0.3);
}

.btn:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
}

/* DASHBOARD LAYOUT */
.dashboard {
  display: grid;
  grid-template-columns: 250px 1fr;
  min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
  background: var(--card);
  padding: 40px 25px;
  border-right: 1px solid var(--border);
  box-shadow: 3px 0 15px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

.sidebar h3 {
  color: var(--primary);
  font-weight: 700;
  text-align: center;
  margin-bottom: 35px;
}

.sidebar a {
  padding: 12px 16px;
  border-radius: 8px;
  color: var(--text-dark);
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  margin-bottom: 10px;
}

.sidebar a:hover,
.sidebar a.active {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white;
  box-shadow: 0 4px 16px rgba(255, 145, 77, 0.25);
  transform: translateX(5px);
}

/* MAIN */
.main-content {
  padding: 50px 70px;
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* CARD */
.card {
  background: var(--card);
  border-radius: 16px;
  padding: 35px;
  margin-bottom: 40px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
}

.card h2 {
  color: var(--primary);
  margin-bottom: 10px;
  font-size: 1.5rem;
}

.card p {
  color: var(--text-muted);
}

/* PET CARDS */
.pets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 30px;
  margin-top: 30px;
}

.pet-card {
  background: var(--card);
  border-radius: 14px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
  padding: 25px;
  text-align: center;
  transition: 0.3s ease;
  border: 1px solid var(--border);
}

.pet-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(255, 145, 77, 0.2);
}

.pet-card img {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  border: 3px solid var(--primary);
  object-fit: cover;
  margin-bottom: 15px;
}

.pet-card h4 {
  color: var(--primary);
  margin-bottom: 5px;
  font-size: 1.1rem;
}

.pet-card p {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin: 3px 0;
}

/* ACTION BUTTONS */
.actions a {
  display: inline-block;
  padding: 7px 12px;
  border-radius: 6px;
  font-size: 0.85rem;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s ease;
  margin: 4px;
}

.actions .edit {
  background: #ffe5b4;
  color: #333;
}

.actions .delete {
  background: #ff6b6b;
  color: #fff;
}

.actions a:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

/* MODALS */
.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(8px);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 999;
  animation: fadeIn 0.3s ease;
}

.overlay.active { display: flex; }

.form-card {
  background: rgba(255, 255, 255, 0.9);
  padding: 35px;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  width: 420px;
  position: relative;
  animation: fadeIn 0.4s ease;
}

.form-card h3 {
  text-align: center;
  color: var(--primary);
  font-size: 1.4rem;
  margin-bottom: 20px;
}

/* FORM */
form label {
  display: block;
  margin-top: 12px;
  font-weight: 600;
  color: var(--text-dark);
}

form input, form select, form textarea {
  width: 100%;
  padding: 10px;
  margin-top: 6px;
  border-radius: 8px;
  border: 1px solid var(--border);
  outline: none;
  font-size: 0.95rem;
  transition: 0.3s;
}

form input:focus, form select:focus, form textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(255, 145, 77, 0.2);
}

form button {
  background: var(--primary);
  color: white;
  padding: 12px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 20px;
  width: 100%;
  transition: 0.3s;
  box-shadow: 0 4px 12px rgba(255, 145, 77, 0.25);
}

form button:hover {
  background: var(--primary-light);
  transform: translateY(-2px);
}

/* CLOSE BUTTON */
.close-btn {
  position: absolute;
  top: 10px;
  right: 16px;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: var(--text-muted);
  cursor: pointer;
  transition: 0.3s;
}

.close-btn:hover {
  color: var(--primary);
  transform: scale(1.1);
}

/* FOOTER */
footer {
  text-align: center;
  padding: 20px;
  color: var(--text-muted);
  font-size: 0.9rem;
  border-top: 1px solid var(--border);
  background: var(--card);
}

</style>
</head>
<body>

<header>
  <div>
    <h1>Veterinary Services Dashboard</h1>
    <div class="user-info">
      <?= htmlspecialchars($_SESSION['user_name']); ?> | <?= htmlspecialchars($_SESSION['user_email']); ?>
    </div>
  </div>
  <a href="<?= site_url('logout') ?>" class="btn">Logout</a>
</header>

<div class="dashboard" id="content">
  <aside class="sidebar">
    <h3>Navigation</h3>
    <a href="<?= site_url('landing') ?>">Landing Page</a>
    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
    <a href="<?= site_url('appointments') ?>">Appointments</a>
    <a href="<?= site_url('pets') ?>" class="active">Pets</a>
    <a href="<?= site_url('veterinarians') ?>">Veterinarians</a>
    <a href="<?= site_url('settings') ?>">Settings</a>
  </aside>

  <section class="main-content">
    <div class="card">
      <h2>My Pets</h2>
      <p>Manage your pets’ profiles and health history here.</p>
      <button class="btn" id="addPetBtn">Add New Pet</button>

      <?php if (!empty($pets)): ?>
        <div class="pets-grid">
          <?php foreach ($pets as $pet): ?>
            <div class="pet-card">
              <img src="<?= !empty($pet['photo']) ? '/'.PUBLIC_DIR.'/'.ltrim($pet['photo'], '/') : '/'.PUBLIC_DIR.'/assets/default-pet.svg' ?>" alt="Pet Photo" onerror="this.onerror=null; this.src=this.src.replace('/<?= PUBLIC_DIR ?>/','/')">
              <h4><?= htmlspecialchars($pet['name']) ?></h4>
              <p><strong>Species:</strong> <?= htmlspecialchars($pet['species'] ?? 'N/A') ?></p>
              <p><strong>Breed:</strong> <?= htmlspecialchars($pet['breed']) ?></p>
              <p><strong>Age:</strong> <?= htmlspecialchars($pet['age'] ?? 'Unknown') ?></p>
              <p><strong>Vaccinated:</strong> <?= $pet['vaccinated'] ? 'Yes' : 'No' ?></p>
              <p><strong>Medical History:</strong> <?= htmlspecialchars($pet['medical_history'] ?? 'None') ?></p>

              <div class="actions">
                <a href="#" class="edit"
                  data-id="<?= htmlspecialchars($pet['id'] ?? '') ?>"
                  data-name="<?= htmlspecialchars($pet['name'] ?? '') ?>"
                  data-species="<?= htmlspecialchars($pet['species'] ?? '') ?>"
                  data-breed="<?= htmlspecialchars($pet['breed'] ?? '') ?>"
                  data-birthdate="<?= htmlspecialchars($pet['birthdate'] ?? '') ?>"
                  data-age="<?= htmlspecialchars($pet['age'] ?? '') ?>"
                  data-vaccinated="<?= htmlspecialchars($pet['vaccinated'] ?? '') ?>"
                  data-medical_history="<?= htmlspecialchars($pet['medical_history'] ?? '') ?>">Edit</a>
                <a href="<?= site_url('pets/delete/' . $pet['id']) ?>" class="delete" onclick="return confirm('Delete this pet?');">Delete</a>
                <div style="margin-top:12px;">
                  <?php $qrUrl = site_url('pets/qr/' . $pet['id']); ?>
                  <img src="<?= $qrUrl ?>" alt="QR Code" width="100" height="100" style="border:1px solid #ccc;border-radius:6px;" onerror="console.error('QR Load Failed:', this.src); this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EQR Error%3C/text%3E%3C/svg%3E';">
                  <br>
                  <small style="color:#666;font-size:0.7rem;">Pet ID: <?= $pet['id'] ?></small><br>
                  <small style="color:#999;font-size:0.65rem;word-break:break-all;"><?= htmlspecialchars($qrUrl) ?></small><br>
                  <a href="<?= site_url('pets/download-qr/' . $pet['id']) ?>" class="btn" style="margin-top:5px;padding:5px 10px;font-size:0.8rem;">Download QR</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div style="background:#fff8f3;padding:15px;border-left:5px solid var(--primary);border-radius:8px;">
          You haven’t added any pets yet.
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<!-- Add Pet Overlay -->
<div class="overlay" id="addPetOverlay">
  <div class="form-card">
    <button class="close-btn" id="closeAdd">&times;</button>
    <h3>Add New Pet</h3>
    <form method="POST" action="<?= site_url('pets/add') ?>" enctype="multipart/form-data">
      <label for="name">Pet Name</label>
      <input type="text" id="name" name="name" required>
      <label for="species">Species</label>
      <input type="text" id="species" name="species" required>
      <label for="breed">Breed</label>
      <input type="text" id="breed" name="breed" required>
      <label for="birthdate">Birthdate</label>
      <input type="date" id="birthdate" name="birthdate">
      <label for="age">Age</label>
      <input type="text" id="age" name="age" readonly>
      <label for="vaccinated">Vaccinated</label>
      <select id="vaccinated" name="vaccinated">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
      <label for="medical_history">Medical History</label>
      <textarea id="medical_history" name="medical_history" rows="3"></textarea>
      <label for="photo">Photo</label>
      <input type="file" id="photo" name="photo" accept="image/*">
      <button type="submit">Save Pet</button>
    </form>
  </div>
</div>

<script>
// Robust QR loader: fetch QR endpoint as blob and set image src to object URL.
// This avoids browser onerror replacing the image when the server returns
// non-image payload or when content-type is not set correctly.
document.addEventListener('DOMContentLoaded', function () {
  const qrImgs = Array.from(document.querySelectorAll('img')).filter(i => i.src && i.src.includes('/pets/qr/'));
  qrImgs.forEach(img => {
    const url = img.src;
    // fetch as blob
    fetch(url, { cache: 'no-store' }).then(resp => {
      if (!resp.ok) throw new Error('QR fetch failed: ' + resp.status);
      return resp.blob();
    }).then(blob => {
      // create object URL and set as src
      const objectUrl = URL.createObjectURL(blob);
      img.src = objectUrl;
      // revoke after load to free memory
      img.onload = () => { URL.revokeObjectURL(objectUrl); };
    }).catch(err => {
      console.error('QR load failed for', url, err);
      // keep existing onerror placeholder
    });
  });
});
</script>
    </form>
  </div>
</div>

<!-- Edit Pet Overlay -->
<div class="overlay" id="editPetOverlay">
  <div class="form-card">
    <button class="close-btn" id="closeEdit">&times;</button>
    <h3>Edit Pet</h3>
    <form method="POST" action="<?= site_url('pets/update') ?>" enctype="multipart/form-data">
      <input type="hidden" id="edit_id" name="id">
      <label for="edit_name">Pet Name</label>
      <input type="text" id="edit_name" name="name" required>
      <label for="edit_species">Species</label>
      <input type="text" id="edit_species" name="species" required>
      <label for="edit_breed">Breed</label>
      <input type="text" id="edit_breed" name="breed" required>
      <label for="edit_birthdate">Birthdate</label>
      <input type="date" id="edit_birthdate" name="birthdate">
      <label for="edit_age">Age</label>
      <input type="text" id="edit_age" name="age" readonly>
      <label for="edit_vaccinated">Vaccinated</label>
      <select id="edit_vaccinated" name="vaccinated">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
      <label for="edit_medical_history">Medical History</label>
      <textarea id="edit_medical_history" name="medical_history" rows="3"></textarea>
      <label for="edit_photo">Photo</label>
      <input type="file" id="edit_photo" name="photo" accept="image/*">
      <button type="submit">Update Pet</button>
    </form>
  </div>
</div>

<footer>
  &copy; <?= date('Y'); ?> Automated Scheduling and Tracking System for Veterinary Services – Calapan City, Oriental Mindoro
</footer>

<script>
const addBtn = document.getElementById('addPetBtn');
const addOverlay = document.getElementById('addPetOverlay');
const closeAdd = document.getElementById('closeAdd');
const editOverlay = document.getElementById('editPetOverlay');
const closeEdit = document.getElementById('closeEdit');
const content = document.getElementById('content');

addBtn.addEventListener('click', () => { addOverlay.classList.add('active'); content.style.filter = 'blur(5px)'; });
closeAdd.addEventListener('click', () => { addOverlay.classList.remove('active'); content.style.filter = 'none'; });
closeEdit.addEventListener('click', () => { editOverlay.classList.remove('active'); content.style.filter = 'none'; });

// ✅ Function to calculate age from birthdate
function calculateAge(birthdate) {
  if (!birthdate) return '';
  
  const birth = new Date(birthdate);
  const today = new Date();
  
  let years = today.getFullYear() - birth.getFullYear();
  let months = today.getMonth() - birth.getMonth();
  let days = today.getDate() - birth.getDate();
  
  // Adjust for negative months or days
  if (days < 0) {
    months--;
    days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
  }
  if (months < 0) {
    years--;
    months += 12;
  }
  
  // Format age string
  let ageStr = '';
  if (years > 0) {
    ageStr = years + ' year' + (years > 1 ? 's' : '');
    if (months > 0) {
      ageStr += ' ' + months + ' month' + (months > 1 ? 's' : '');
    }
  } else if (months > 0) {
    ageStr = months + ' month' + (months > 1 ? 's' : '');
  } else {
    ageStr = days + ' day' + (days > 1 ? 's' : '');
  }
  
  return ageStr;
}

// ✅ Auto-calculate age for Add form
document.getElementById('birthdate').addEventListener('change', function() {
  document.getElementById('age').value = calculateAge(this.value);
});

// ✅ Auto-calculate age for Edit form
document.getElementById('edit_birthdate').addEventListener('change', function() {
  document.getElementById('edit_age').value = calculateAge(this.value);
});

document.querySelectorAll('.edit').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_id').value = btn.dataset.id;
    document.getElementById('edit_name').value = btn.dataset.name;
    document.getElementById('edit_species').value = btn.dataset.species;
    document.getElementById('edit_breed').value = btn.dataset.breed;
    document.getElementById('edit_birthdate').value = btn.dataset.birthdate;
    document.getElementById('edit_age').value = btn.dataset.age;
    document.getElementById('edit_vaccinated').value = btn.dataset.vaccinated;
    document.getElementById('edit_medical_history').value = btn.dataset.medical_history;
    editOverlay.classList.add('active');
    content.style.filter = 'blur(5px)';
  });
});
</script>

</body>
</html>
