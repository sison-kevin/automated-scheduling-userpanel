<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Ensure session is started before reading session variables
if (session_status() !== PHP_SESSION_ACTIVE) {
  @session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: ' . site_url('login'));
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Welcome | PetCare Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* {
  margin: 0; padding: 0; box-sizing: border-box;
  scroll-behavior: smooth;
}

body {
  font-family: 'Poppins', sans-serif;
  background: #fffaf5;
  color: #333;
  overflow-x: hidden;
  transition: background 0.4s, color 0.4s;
}

h1, h2, h3 { color: #1a1a1a; }
a { text-decoration: none; }

/* HEADER */
header {
  position: fixed;
  top: 0; width: 100%;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(12px);
  box-shadow: 0 2px 15px rgba(0,0,0,0.05);
  display: flex; justify-content: space-between; align-items: center;
  padding: 15px 50px;
  z-index: 1000;
  transition: all 0.3s ease;
}
header h2 {
  font-weight: 700;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 26px;
  letter-spacing: 0.5px;
  transition: 0.3s;
}
header:hover h2 { filter: brightness(1.1); }

nav {
  display: flex;
  gap: 25px;
  align-items: center;
}

nav a {
  color: #333;
  font-weight: 500;
  position: relative;
  padding: 6px 0;
  transition: color 0.3s, transform 0.3s;
}

nav a::after {
  content: "";
  position: absolute;
  bottom: -5px; left: 0;
  width: 0%;
  height: 2px;
  background: linear-gradient(90deg, #ff914d, #ffb47b);
  transition: width 0.3s;
  border-radius: 2px;
}
nav a:hover::after, nav a.active::after { width: 100%; }
nav a:hover, nav a.active { color: #ff914d; transform: translateY(-2px); }

.btn-dashboard {
  background: linear-gradient(45deg, #ff914d, #ffb47b);
  color: white !important;
  padding: 8px 18px;
  border-radius: 25px;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(255,145,77,0.3);
}
.btn-dashboard::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.5s ease;
}
.btn-dashboard:hover::before {
  left: 100%;
}
.btn-dashboard:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(255,145,77,0.4);
}

/* DARK MODE */
body.dark-mode {
  background: #1e1e1e;
  color: #eee;
}
body.dark-mode header {
  background: rgba(30,30,30,0.9);
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}
body.dark-mode nav a { color: #ccc; }
body.dark-mode nav a.active, body.dark-mode nav a:hover { color: #ff914d; }

.dark-toggle {
  cursor: pointer;
  border: none;
  background: #ff914d;
  color: white;
  border-radius: 20px;
  padding: 6px 14px;
  font-weight: 600;
  transition: 0.3s;
}
.dark-toggle:hover {
  background: #ffb47b;
}

/* LOGOUT BUTTON */
.logout-btn {
  background: linear-gradient(45deg, #ff914d, #ffb47b);
  color: white;
  border: none;
  border-radius: 25px;
  padding: 8px 20px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
  box-shadow: 0 3px 10px rgba(255,145,77,0.3);
}
.logout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255,145,77,0.4);
}

/* PROGRESS BAR */
#progress-bar {
  position: fixed;
  top: 0;
  left: 0;
  height: 4px;
  background: linear-gradient(90deg, #ff914d, #ffb47b);
  width: 0%;
  z-index: 1500;
  transition: width 0.25s ease-out;
}

/* TOAST */
.toast {
  position: fixed;
  top: 80px;
  right: 30px;
  background: #ff914d;
  color: white;
  padding: 15px 25px;
  border-radius: 10px;
  box-shadow: 0 6px 20px rgba(255,145,77,0.4);
  opacity: 0;
  transform: translateY(-20px);
  transition: all 0.6s ease;
  z-index: 2000;
}
.toast.show {
  opacity: 1;
  transform: translateY(0);
}

/* HERO */
section#home {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 100vh;
  padding: 120px 80px;
  background: linear-gradient(120deg, #fff7ef 40%, #fff 60%);
  animation: fadeInUp 1s ease;
}
.hero-content {
  flex: 1;
  padding-left: 60px;
  animation: fadeInUp 1s ease-in-out;
}
.hero-content h1 {
  font-size: 50px;
  margin-bottom: 20px;
  background: linear-gradient(45deg, #ff914d, #ffb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero-content p {
  color: #555;
  font-size: 18px;
  max-width: 460px;
  margin-bottom: 30px;
}
.cta-btn {
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  color: white;
  padding: 12px 35px;
  border-radius: 30px;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(255,145,77,0.4);
  transition: 0.3s;
}
.cta-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.5s ease;
}
.cta-btn:hover::before {
  left: 100%;
}
.cta-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255,145,77,0.5);
}
.hero-img {
  flex: 1;
  text-align: center;
}
.hero-img img {
  width: 450px;
  border-radius: 50%;
  animation: float 3s ease-in-out infinite;
}

/* CAROUSEL */
.carousel-section {
  position: relative;
  margin-top: -80px;
  padding: 0 0 60px 0;
  background: transparent;
  overflow: visible;
  z-index: 10;
}
.carousel-container {
  position: relative;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
  overflow: hidden;
  padding: 20px 0;
}
.carousel-gallery {
  display: flex;
  gap: 20px;
  animation: scroll-carousel 20s linear infinite;
  width: max-content;
  padding-left: 0;
}
.carousel-gallery:hover {
  animation-play-state: paused;
}
@keyframes scroll-carousel {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}
.pet-card {
  flex-shrink: 0;
  width: 300px;
  background: #fafaf8;
  border-radius: 12px;
  padding: 15px 15px 60px 15px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #e8e8e6;
  transform-style: preserve-3d;
}
.pet-card:hover {
  transform: translateY(-10px) rotate(-2deg);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
  border-color: #d4d4d2;
}
.pet-card img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  border-radius: 12px;
  transition: all 0.4s ease;
  filter: brightness(1);
}
.pet-card:hover img {
  filter: brightness(1.05);
  transform: scale(1.02);
}

/* PARALLAX DIVIDER */
  .parallax-divider {
  position: relative;
  height: 400px;
  background-image: url('/<?= PUBLIC_DIR ?>/dog%20peeking.jfif');
  background-attachment: fixed;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin: 50px 0;
}
.parallax-divider::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1;
}
.parallax-content {
  position: relative;
  z-index: 2;
  text-align: center;
  color: white;
  padding: 20px;
}
.parallax-content h3 {
  font-size: 42px;
  font-weight: 700;
  margin-bottom: 15px;
  color: white;
  text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
}
.parallax-content p {
  font-size: 20px;
  font-weight: 300;
  color: #f0f0f0;
  text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
}

/* SECTIONS */
section#stats {
  padding-top: 80px;
}
section {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.8s ease, transform 0.8s ease;
}
section.show {
  opacity: 1;
  transform: translateY(0);
}

/* FOOTER */
footer {
  background: linear-gradient(120deg, #ff914d, #ffb47b);
  color: white;
  text-align: center;
  padding: 30px 0;
  margin-top: 50px;
}

/* ANIMATIONS */
@keyframes fadeInUp {
  from {opacity: 0; transform: translateY(30px);}
  to {opacity: 1; transform: translateY(0);}
}
@keyframes float {
  0%,100% {transform: translateY(0);}
  50% {transform: translateY(-10px);}
}

/* ===== STATS SECTION ===== */
#stats {
  text-align: center;
  padding: 100px 60px;
  background: #fffaf5;
}
.section-title {
  font-size: 2rem;
  margin-bottom: 40px;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 700;
}
.stats-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 25px;
}
.stat-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 8px 25px rgba(255,145,77,0.15);
  padding: 25px 40px;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  width: 260px;
}
.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, #ff914d, #ffb47b);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}
.stat-card:hover::before {
  transform: scaleX(1);
}
.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(255,145,77,0.25);
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(255,145,77,0.25);
}
.stat-card h3 {
  color: #333;
  font-size: 18px;
  margin-bottom: 10px;
}
.stat-card p {
  font-size: 24px;
  font-weight: 700;
  color: #ff914d;
}

/* ===== APPOINTMENTS ===== */
#appointments {
  padding: 100px 60px;
  text-align: center;
  background: #fff;
}
.appointment-grid {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 25px;
}
.appointment-card {
  background: #fffaf5;
  border-left: 5px solid #ff914d;
  border-radius: 15px;
  box-shadow: 0 8px 20px rgba(255,145,77,0.1);
  padding: 20px 30px;
  transition: 0.3s;
  width: 300px;
  text-align: left;
}
.appointment-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 25px rgba(255,145,77,0.25);
}
.appointment-card h3 {
  color: #ff914d;
  font-size: 18px;
  margin-bottom: 10px;
}

/* ===== PET TIPS ===== */
#tips {
  padding: 100px 60px;
  text-align: center;
  background: #fffaf5;
}
.tips-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 25px;
}
.tip {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 8px 25px rgba(255,145,77,0.15);
  width: 280px;
  transition: 0.3s;
}
.tip:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(255,145,77,0.25);
}
.tip-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  box-shadow: 0 8px 20px rgba(255,145,77,0.3);
  transition: all 0.3s ease;
}
.tip:hover .tip-icon {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 12px 30px rgba(255,145,77,0.4);
}
.tip h3 {
  color: #ff914d;
  font-size: 18px;
  margin-bottom: 8px;
}
.tip p {
  color: #555;
  font-size: 15px;
}

</style>
</head>

<body>
<div id="progress-bar"></div>

<header>
  <h2>PetCare Portal</h2>
  <nav>
    <a href="#home" class="active">Home</a>
    <a href="#stats">Overview</a>
    <a href="#appointments">Appointments</a>
    <a href="#tips">Pet Tips</a>
    <a href="<?= site_url('dashboard') ?>" class="btn-dashboard">Go to Dashboard</a>
  </nav>
  <div style="display:flex;align-items:center;gap:10px;">
    <button class="dark-toggle" id="darkToggle">Dark Mode</button>
    <form method="POST" action="<?= site_url('logout') ?>">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>
</header>

<div class="toast" id="toast">Welcome back, <?= htmlspecialchars($_SESSION['user_name']); ?></div>

<!-- HERO -->
<section id="home">
  <div class="hero-content">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>Your pet’s wellbeing is our top priority. Manage appointments, track health, and discover expert pet tips — all in one place.</p>
    <a href="#appointments" class="cta-btn">View Appointments</a>
  </div>
  <div class="hero-img">
    <img src="/<?= PUBLIC_DIR ?>/vet%20cat.jfif.jfif" alt="Vet Illustration" onerror="this.onerror=null; this.src='/vet cat.jfif.jfif'">
  </div>
</section>

<!-- CAROUSEL -->
<div class="carousel-section">
  <div class="carousel-container">
    <div class="carousel-gallery">
      <!-- First set of images -->
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet1.jfif" alt="Pet 1" onerror="this.onerror=null; this.src='/pet1.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet2.jfif" alt="Pet 2" onerror="this.onerror=null; this.src='/pet2.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet3.jfif" alt="Pet 3" onerror="this.onerror=null; this.src='/pet3.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet4.jfif" alt="Pet 4" onerror="this.onerror=null; this.src='/pet4.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet5.jfif" alt="Pet 5" onerror="this.onerror=null; this.src='/pet5.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet6.jfif" alt="Pet 6" onerror="this.onerror=null; this.src='/pet6.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet7.jfif" alt="Pet 7" onerror="this.onerror=null; this.src='/pet7.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet8.jfif" alt="Pet 8" onerror="this.onerror=null; this.src='/pet8.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet9.jfif" alt="Pet 9" onerror="this.onerror=null; this.src='/pet9.jfif'"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet10.jfif" alt="Pet 10" onerror="this.onerror=null; this.src='/pet10.jfif'"></div>
      <!-- Duplicate set for seamless loop -->
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet1.jfif" alt="Pet 1"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet2.jfif" alt="Pet 2"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet3.jfif" alt="Pet 3"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet4.jfif" alt="Pet 4"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet5.jfif" alt="Pet 5"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet6.jfif" alt="Pet 6"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet7.jfif" alt="Pet 7"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet8.jfif" alt="Pet 8"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet9.jfif" alt="Pet 9"></div>
      <div class="pet-card"><img src="/<?= PUBLIC_DIR ?>/pet10.jfif" alt="Pet 10"></div>
    </div>
  </div>
</div>

<section id="stats">
  <h2 class="section-title">Your Dashboard Overview</h2>
  <div class="stats-container">
    <div class="stat-card"><h3>Upcoming Appointments</h3><p><?= $upcoming_appointments ?? 0; ?></p></div>
    <div class="stat-card"><h3>Registered Pets</h3><p><?= $total_pets ?? 0; ?></p></div>
    <div class="stat-card"><h3>Completed Visits</h3><p><?= $completed_visits ?? 0; ?></p></div>
  </div>
</section>

<section id="appointments">
  <h2 class="section-title">Upcoming Appointments</h2>
  <div class="appointment-grid">
    <?php if (!empty($upcoming_details)): ?>
      <?php foreach ($upcoming_details as $apt): ?>
        <div class="appointment-card">
          <h3><?= htmlspecialchars($apt['pet_name']); ?> - <?= htmlspecialchars($apt['service']); ?></h3>
          <p>Date: <?= htmlspecialchars($apt['date']); ?></p>
          <p>Vet: <?= htmlspecialchars($apt['vet_name']); ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="appointment-card">
        <h3>No Upcoming Appointments</h3>
        <p>You don't have any scheduled appointments yet.</p>
        <p><a href="<?= site_url('appointments'); ?>" style="color: #ff914d;">Book an appointment now!</a></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- PARALLAX DIVIDER -->
<div class="parallax-divider">
  <div class="parallax-content">
    <h3>We Care For Your Pets</h3>
    <p>Like Family, Because They Are</p>
  </div>
</div>

<section id="tips">
  <h2 class="section-title">Pet Wellness Tips</h2>
  <div class="tips-container">
    <div class="tip">
      <div class="tip-icon">💧</div>
      <h3>Stay Hydrated</h3>
      <p>Keep fresh water available at all times.</p>
    </div>
    <div class="tip">
      <div class="tip-icon">✂️</div>
      <h3>Regular Grooming</h3>
      <p>Brushing helps prevent matting and promotes healthy skin.</p>
    </div>
    <div class="tip">
      <div class="tip-icon">🏥</div>
      <h3>Annual Checkups</h3>
      <p>Routine vet visits ensure long-term health.</p>
    </div>
  </div>
</section>

<footer>
  <p>© 2025 PetCare Portal | All rights reserved.</p>
</footer>

<script>
// Scroll progress bar
const progressBar = document.getElementById('progress-bar');
window.addEventListener('scroll', () => {
  const scrollTop = window.scrollY;
  const docHeight = document.documentElement.scrollHeight - window.innerHeight;
  progressBar.style.width = `${(scrollTop / docHeight) * 100}%`;
});

// Section fade-in on scroll
const sections = document.querySelectorAll('section');
const revealOnScroll = () => {
  const trigger = window.innerHeight * 0.85;
  sections.forEach(sec => {
    const top = sec.getBoundingClientRect().top;
    if (top < trigger) sec.classList.add('show');
  });
};
window.addEventListener('scroll', revealOnScroll);
revealOnScroll();

// Active nav link
const navLinks = document.querySelectorAll('nav a');
window.addEventListener('scroll', () => {
  let fromTop = window.scrollY + 150;
  navLinks.forEach(link => {
    let section = document.querySelector(link.hash);
    if (section && section.offsetTop <= fromTop && section.offsetTop + section.offsetHeight > fromTop) {
      navLinks.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    }
  });
});

// Toast welcome
const toast = document.getElementById('toast');
setTimeout(() => toast.classList.add('show'), 500);
setTimeout(() => toast.classList.remove('show'), 4000);

// Dark mode
const toggle = document.getElementById('darkToggle');
toggle.addEventListener('click', () => {
  document.body.classList.toggle('dark-mode');
  toggle.textContent = document.body.classList.contains('dark-mode') ? '☀️' : '🌙';
});
</script>

</body>
</html>
