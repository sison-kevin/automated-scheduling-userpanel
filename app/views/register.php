<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetCare Veterinary Portal</title>
<style>
/* ========== GLOBAL STYLES ========== */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  scroll-behavior: smooth;
}
body {
  font-family: 'Poppins', sans-serif;
  background: #fffaf5;
  color: #333;
  overflow-x: hidden;
}
h1, h2, h3 {
  color: #1a1a1a;
}
a {
  text-decoration: none;
}

/* ========== HEADER ========== */
header {
  position: fixed;
  top: 0;
  width: 100%;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 50px;
  z-index: 1000;
  transition: all 0.3s ease;
}
header.scrolled {
  background: #fff;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
header h2 {
  font-weight: 700;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 26px;
  letter-spacing: 1px;
}
nav {
  display: flex;
  gap: 25px;
}
nav a {
  color: #333;
  font-weight: 500;
  position: relative;
  transition: color 0.3s;
}
nav a::after {
  content: "";
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0%;
  height: 2px;
  background: #ff914d;
  transition: width 0.3s;
}
nav a:hover::after, nav a.active::after {
  width: 100%;
}
nav a:hover, nav a.active {
  color: #ff914d;
}
.header-buttons {
  display: flex;
  gap: 12px;
}
.toggle-btn {
  padding: 8px 20px;
  background: linear-gradient(45deg, #ff914d, #ffb47b);
  color: white;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(255,145,77,0.3);
}
.toggle-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.5s ease;
}
.toggle-btn:hover::before {
  left: 100%;
}
.toggle-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255,145,77,0.4);
}

/* ========== HERO SECTION ========== */
section#home {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 100vh;
  padding: 120px 80px;
  background: linear-gradient(120deg, #fff7ef 40%, #fff 60%);
  position: relative;
  overflow: hidden;
}
.hero-content {
  flex: 1;
  padding-left: 120px;
  animation: fadeInUp 1.2s ease;
}
.hero-content h1 {
  font-size: 52px;
  margin-bottom: 25px;
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
  display: inline-block;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  color: white;
  padding: 12px 35px;
  border-radius: 30px;
  font-weight: 600;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(255,145,77,0.4);
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
  width: 460px;
  border-radius: 50%;
  animation: float 3s ease-in-out infinite;
  filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
}
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

/* ========== SECTIONS ========== */
section {
  padding: 100px 80px;
  transition: background 0.3s ease;
}
section:nth-child(even) {
  background: #fff6ef;
}
.section-title {
  text-align: center;
  font-size: 34px;
  margin-bottom: 50px;
  position: relative;
  background: linear-gradient(135deg, #ff914d, #ffb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.section-title::after {
  content: '';
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, #ff914d, #ffb47b);
  display: block;
  margin: 10px auto;
  border-radius: 2px;
}

/* ========== ABOUT ========== */
.about-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 60px;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 0 auto;
  animation: fadeInUp 1s ease;
}

.about-image img {
  width: 420px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  transition: transform 0.4s ease;
}

.about-image img:hover {
  transform: scale(1.03);
}

.about-text {
  max-width: 550px;
}

.about-text p {
  font-size: 18px;
  color: #555;
  line-height: 1.8;
  margin-bottom: 25px;
}

/* ========== WHY CHOOSE US ========== */
.reasons {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
}
.reason {
  background: white;
  padding: 25px;
  border-radius: 15px;
  width: 280px;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}
.reason::before {
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
.reason:hover::before {
  transform: scaleX(1);
}
.reason:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px rgba(255,145,77,0.2);
}
.reason img {
  width: 160px;
  height: 160px;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 15px;
}
.reason h3 {
  color: #ff914d;
  margin-bottom: 10px;
}

/* ========== SERVICES ========== */
.services-grid {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
}
.service-card {
  background: white;
  width: 280px;
  padding: 25px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.05);
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  border: 1px solid #f0f0f0;
}
.service-card::before {
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
.service-card:hover::before {
  transform: scaleX(1);
}
.service-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 25px rgba(255,145,77,0.2);
  border-color: rgba(255,145,77,0.3);
}
.service-card .icon {
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
.service-card:hover .icon {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 12px 30px rgba(255,145,77,0.4);
}
.service-card h3 {
  color: #ff914d;
  margin-bottom: 12px;
  font-size: 1.25rem;
  font-weight: 600;
}
.service-card p {
  color: #666;
  font-size: 0.9375rem;
  line-height: 1.6;
}

/* ========== CONTACT ========== */
#contact .contact-info {
  text-align: center;
  font-size: 18px;
  color: #555;
  line-height: 1.7;
  margin-bottom: 50px;
}

/* ========== CAROUSEL ========== */
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
.gallery {
  display: flex;
  gap: 20px;
  animation: scroll-carousel 20s linear infinite;
  width: max-content;
  padding-left: 0;
}
.gallery:hover {
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

/* ========== PARALLAX DIVIDER ========== */
.parallax-divider {
  position: relative;
  height: 400px;
  background-image: url('<?= BASE_URL ?>cat peeking.jfif');
  background-attachment: fixed;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
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

/* ========== FOOTER ========== */
#about {
  padding-top: 80px;
}
footer {
  background: linear-gradient(120deg, #ff914d, #ffb47b);
  color: white;
  text-align: center;
  padding: 30px 0;
  margin-top: 50px;
}
footer p {
  margin: 5px 0;
}

/* ========== SCROLL TO TOP ========== */
#scrollTopBtn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  background: #ff914d;
  color: white;
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  font-size: 20px;
  cursor: pointer;
  box-shadow: 0 5px 15px rgba(255,145,77,0.4);
  display: none;
  transition: all 0.3s ease;
  z-index: 999;
}
#scrollTopBtn:hover {
  background: #ffb47b;
  transform: translateY(-4px);
}

/* ========== ANIMATIONS ========== */
@keyframes fadeInUp {
  from {opacity: 0; transform: translateY(30px);}
  to {opacity: 1; transform: translateY(0);}
}

/* ABOUT LAYOUT */
.about-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 60px;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 0 auto;
  animation: fadeInUp 1s ease;
}
.about-image img {
  width: 420px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  transition: transform 0.4s ease;
}
.about-image img:hover { transform: scale(1.03); }
.about-text { max-width: 550px; }
.about-text p { font-size: 18px; color: #555; line-height: 1.8; margin-bottom: 25px; }

/* FORM CONTAINERS (popup) */
.form-container {
  display: none;
  position: fixed;
  top: 110px;
  right: 50px;
  background: white;
  padding: 26px 34px;
  border-radius: 10px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.18);
  width: 340px;
  z-index: 999;
}
.form-container h2 { margin-bottom: 18px; font-size: 20px; color: #1a1a1a; }
.form-container input {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border: 1px solid #e6e6e6;
  border-radius: 6px;
}
.form-container button[type="submit"] {
  width: 100%;
  padding: 10px;
  background: #ff914d;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

/* MESSAGE STYLES */
.message {
  padding: 10px 15px;
  border-radius: 8px;
  margin-bottom: 15px;
  font-size: 13px;
  font-weight: 500;
  animation: slideIn 0.3s ease;
}
.error-message {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
.success-message {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ========== LOCATION SECTION ========== */
#location {
  background: #fffaf5;
  padding: 100px 80px;
}
.location-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 60px;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 0 auto;
  animation: fadeInUp 1s ease;
}
.location-text {
  flex: 1;
  font-size: 18px;
  color: #555;
  line-height: 1.8;
}
.location-text p {
  margin-bottom: 15px;
}
.map-container {
  flex: 1;
  min-width: 400px;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

</style>
</head>
<body>

<header>
  <h2>PetCare</h2>
  <nav>
    <a href="#home" class="active">Home</a>
    <a href="#about">About Us</a>
    <a href="#why">Why Choose Us</a>
    <a href="#services">Services</a>
    <a href="#contact">Contact</a>
    <a href="#location">Location</a>
  </nav>
  <div class="header-buttons">
    <button class="toggle-btn" id="showRegister">Register</button>
    <button class="toggle-btn" id="showLogin">Login</button>
  </div>
</header>


<!-- HERO -->
<section id="home">
  <div class="hero-content">
    <h1>Your Trusted Veterinary Companion</h1>
    <p>Expert care, compassionate treatment, and modern facilities to keep your pets healthy and happy.</p>
    <a href="#services" class="cta-btn">Explore Services</a>
  </div>
  <div class="hero-img">
    <img src="<?= BASE_URL ?>vet%20dog.jfif" alt="Veterinary Dog">
  </div>
</section>

<!-- CAROUSEL -->
<div class="carousel-section">
  <div class="carousel-container">
    <div class="gallery">
      <!-- First set of images -->
      <div class="pet-card"><img src="<?= BASE_URL ?>pet1.jfif" alt="Pet 1"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet2.jfif" alt="Pet 2"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet3.jfif" alt="Pet 3"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet4.jfif" alt="Pet 4"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet5.jfif" alt="Pet 5"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet6.jfif" alt="Pet 6"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet7.jfif" alt="Pet 7"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet8.jfif" alt="Pet 8"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet9.jfif" alt="Pet 9"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet10.jfif" alt="Pet 10"></div>
      <!-- Duplicate set for seamless loop -->
      <div class="pet-card"><img src="<?= BASE_URL ?>pet1.jfif" alt="Pet 1"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet2.jfif" alt="Pet 2"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet3.jfif" alt="Pet 3"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet4.jfif" alt="Pet 4"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet5.jfif" alt="Pet 5"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet6.jfif" alt="Pet 6"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet7.jfif" alt="Pet 7"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet8.jfif" alt="Pet 8"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet9.jfif" alt="Pet 9"></div>
      <div class="pet-card"><img src="<?= BASE_URL ?>pet10.jfif" alt="Pet 10"></div>
    </div>
  </div>
</div>

<!-- ABOUT -->
<section id="about">
  <h2 class="section-title">About Us</h2>
  <div class="about-container">
    <div class="about-image">
      <img src="<?= BASE_URL ?>vet%20with%20pet.jfif" alt="Vet with pet">
    </div>
    <div class="about-text">
      <p>At <strong>PetCare</strong>, we’re passionate about keeping your furry companions happy and healthy. 
      Our team of experienced veterinarians and caring staff provide expert medical care in a warm, stress-free environment. 
      From preventive checkups to emergency treatment, we ensure your pets receive the best care possible — because they’re family.</p>
      <a href="#services" class="cta-btn">Learn More</a>
    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section id="why">
  <h2 class="section-title">Why Choose Us?</h2>
  <div class="reasons">
    <div class="reason">
      <img src="<?= BASE_URL ?>expert%20vet.jfif" alt="Expert Vet">
      <h3>Expert Veterinarians</h3>
      <p>Certified professionals passionate about animal wellness.</p>
    </div>
    <div class="reason">
      <img src="<?= BASE_URL ?>emergency.jfif" alt="Emergency">
      <h3>24/7 Emergency</h3>
      <p>Round-the-clock emergency service for your peace of mind.</p>
    </div>
    <div class="reason">
      <img src="<?= BASE_URL ?>modern%20facility.jfif" alt="Facility">
      <h3>Modern Facilities</h3>
      <p>Advanced equipment for accurate diagnosis and care.</p>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section id="services">
  <h2 class="section-title">Our Services</h2>
  <div class="services-grid">
    <div class="service-card">
      <div class="icon">🏥</div>
      <h3>Routine Checkups</h3>
      <p>Comprehensive exams to keep your pets in great shape.</p>
    </div>
    <div class="service-card">
      <div class="icon">💉</div>
      <h3>Vaccinations</h3>
      <p>Preventative care to protect your furry friends.</p>
    </div>
    <div class="service-card">
      <div class="icon">✂️</div>
      <h3>Grooming</h3>
      <p>Pamper your pet with our expert grooming services.</p>
    </div>
    <div class="service-card">
      <div class="icon">🔬</div>
      <h3>Surgery</h3>
      <p>Safe, advanced surgical care from trusted veterinarians.</p>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section id="contact">
  <h2 class="section-title">Contact Us</h2>
  <div class="contact-info">
    <p>Email: cityvet@gmail.com.com | Phone: +63 912-3456-789</p>
    <p>Visit us at 123 Paw Avenue, Calapan City, Oriental Mindoro</p>
  </div>
</section>

<!-- PARALLAX DIVIDER -->
<div class="parallax-divider">
  <div class="parallax-content">
    <h3>We Care For Your Pets</h3>
    <p>Like Family, Because They Are</p>
  </div>
</div>

<!-- LOCATION -->
<section id="location">
  <h2 class="section-title">Find Us</h2>
  <div class="location-container">
    <div class="location-text">
      <p>We’re conveniently located in the heart of <strong>Calapan City, Oriental Mindoro</strong>.</p>
      <p>Drop by for a visit — we're always happy to welcome you and your furry friends!</p>
  <p><strong>Address:</strong> 123 Paw Avenue, Calapan City, Oriental Mindoro</p>
      <p><strong>Operating Hours:</strong> Open 24 Hours, 7 Days a Week</p>
    </div>
    <div class="map-container">
      <!-- Replace this embed link with your real location if needed -->
      <iframe
        src="https://www.google.com/maps?q=13.4138889,121.18&z=16&output=embed"
        width="100%"
        height="350"
        style="border:0; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

<!-- REGISTER & LOGIN FORMS -->
<div id="registerForm" class="form-container" aria-hidden="true">
  <h2>Register</h2>
  
  <?php
  // Start session if not already started
  if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
  }
  
  // Display REGISTER error message only if form_type is 'register'
  if (isset($_SESSION['error']) && isset($_SESSION['form_type']) && $_SESSION['form_type'] === 'register'): ?>
    <div class="message error-message">
      ❌ <?= htmlspecialchars($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  
  <?php
  // Display REGISTER success message only if form_type is 'register'
  if (isset($_SESSION['success']) && isset($_SESSION['form_type']) && $_SESSION['form_type'] === 'register'): ?>
    <div class="message success-message">
      ✅ <?= htmlspecialchars($_SESSION['success']); ?>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="<?= site_url('register') ?>">
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
  </form>
</div>

<div id="loginForm" class="form-container" aria-hidden="true">
  <h2>Login</h2>
  
  <?php
  // Display LOGIN error message only if form_type is 'login'
  if (isset($_SESSION['error']) && isset($_SESSION['form_type']) && $_SESSION['form_type'] === 'login'): ?>
    <div class="message error-message">
      ❌ <?= htmlspecialchars($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
  
  <?php
  // Clear error and success messages after displaying
  if (isset($_SESSION['error'])) unset($_SESSION['error']);
  if (isset($_SESSION['success'])) unset($_SESSION['success']);
  ?>
  
  <form method="POST" action="<?= site_url('login') ?>">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
  </form>
</div>

<!-- FOOTER -->
<footer>
  <p>© 2025 PetCare Veterinary Portal</p>
</footer>

<button id="scrollTopBtn">&#8679;</button>

<script>
// Header scroll effect
window.addEventListener('scroll', () => {
  const header = document.querySelector('header');
  header.classList.toggle('scrolled', window.scrollY > 80);
});

// Active nav link highlight
const navLinks = document.querySelectorAll('nav a');
window.addEventListener('scroll', () => {
  let fromTop = window.scrollY + 150;
  navLinks.forEach(link => {
    let section = document.querySelector(link.hash);
    if (section.offsetTop <= fromTop && section.offsetTop + section.offsetHeight > fromTop) {
      navLinks.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    }
  });
});

// Scroll to top button
const scrollBtn = document.getElementById("scrollTopBtn");
window.onscroll = function() {
  scrollBtn.style.display = window.scrollY > 400 ? "block" : "none";
};
scrollBtn.onclick = function() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// FORM TOGGLE HANDLERS (merge into your main <script>)
const showRegisterBtn = document.getElementById('showRegister');
const showLoginBtn = document.getElementById('showLogin');
const registerForm = document.getElementById('registerForm');
const loginForm = document.getElementById('loginForm');

function hideForms() {
  if (registerForm) registerForm.style.display = 'none';
  if (loginForm) loginForm.style.display = 'none';
}

if (showRegisterBtn) {
  showRegisterBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    hideForms();
    registerForm.style.display = 'block';
  });
}
if (showLoginBtn) {
  showLoginBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    hideForms();
    loginForm.style.display = 'block';
  });
}

// Close popups when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.form-container') && !e.target.closest('.toggle-btn')) {
    hideForms();
  }
});

// Auto-show form popup if there's an error or success message
window.addEventListener('DOMContentLoaded', () => {
  <?php if (isset($_SESSION['form_type'])): ?>
    const formType = '<?= $_SESSION['form_type']; ?>';
    <?php unset($_SESSION['form_type']); ?>
    
    hideForms();
    if (formType === 'register' && registerForm) {
      registerForm.style.display = 'block';
    } else if (formType === 'login' && loginForm) {
      loginForm.style.display = 'block';
    }
  <?php endif; ?>
});

</script>

</body>
</html>
