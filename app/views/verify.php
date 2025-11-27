<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification | PetCare</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ===== RESET & BASE ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at top left, #fff5ec, #fffaf5, #fff8f0);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow: hidden;
      color: #333;
    }

    /* ===== ANIMATED BACKGROUND GRADIENT ===== */
    .gradient-bg {
      position: absolute;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, #ffeedf, #fff7ef, #fffaf5, #fff7ef);
      background-size: 300% 300%;
      animation: gradientShift 12s ease infinite;
      z-index: 0;
    }
    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    /* ===== FLOATING ELEMENTS ===== */
    .floating-paw {
      position: absolute;
      opacity: 0.07;
      width: 90px;
      animation: floatPaw 12s infinite ease-in-out;
    }
    .floating-paw:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
    .floating-paw:nth-child(2) { top: 65%; left: 5%; animation-delay: 3s; }
    .floating-paw:nth-child(3) { top: 35%; right: 15%; animation-delay: 5s; }
    .floating-paw:nth-child(4) { bottom: 15%; right: 25%; animation-delay: 7s; }

    @keyframes floatPaw {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(10deg); }
    }

    /* ===== CARD CONTAINER ===== */
    .verify-card {
      position: relative;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(18px);
      border: 1px solid rgba(255, 255, 255, 0.5);
      box-shadow: 0 10px 40px rgba(255, 145, 77, 0.15);
      border-radius: 30px;
      padding: 50px 60px;
      text-align: center;
      max-width: 420px;
      width: 90%;
      z-index: 2;
      animation: fadeUp 0.8s ease forwards;
      transition: all 0.3s ease;
    }

    .verify-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 50px rgba(255, 145, 77, 0.25);
    }

    /* ===== LOGO ===== */
    .verify-card img.logo {
      width: 85px;
      margin-bottom: 20px;
      animation: floatLogo 3s ease-in-out infinite;
    }

    .verify-card h2 {
      color: #ff914d;
      font-weight: 700;
      font-size: 28px;
      margin-bottom: 10px;
    }

    .verify-card p {
      color: #555;
      font-size: 16px;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    /* ===== FORM ===== */
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="text"] {
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ddd;
      text-align: center;
      font-size: 18px;
      letter-spacing: 3px;
      font-weight: 500;
      color: #333;
      transition: all 0.3s ease;
    }

    input[type="text"]:focus {
      border-color: #ff914d;
      box-shadow: 0 0 10px rgba(255, 145, 77, 0.4);
      outline: none;
      transform: scale(1.03);
    }

    /* ===== BUTTON ===== */
    button[type="submit"] {
      padding: 12px;
      background: linear-gradient(90deg, #ff914d, #ffa76c);
      border: none;
      border-radius: 12px;
      color: #fff;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 6px 20px rgba(255, 145, 77, 0.3);
    }

    button[type="submit"]:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(255, 145, 77, 0.45);
      background: linear-gradient(90deg, #ffa76c, #ff914d);
    }

    /* ===== FOOTER NOTE ===== */
    .footer-note {
      margin-top: 20px;
      font-size: 14px;
      color: #777;
    }
    .footer-note a {
      color: #ff914d;
      text-decoration: none;
      font-weight: 500;
    }
    .footer-note a:hover {
      text-decoration: underline;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes floatLogo {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 480px) {
      .verify-card {
        padding: 40px 30px;
      }
      .verify-card h2 { font-size: 24px; }
      .verify-card p { font-size: 15px; }
      input[type="text"], button { font-size: 15px; }
    }
  </style>
</head>
<body>

  <!-- Background Layer -->
  <div class="gradient-bg"></div>

  <!-- Verification Card -->
  <div class="verify-card">
    <img src="/<?= PUBLIC_DIR ?>/dog.jfif" alt="PetCare Logo" class="logo" onerror="this.onerror=null; this.src='/dog.jfif'">
    <h2>Email Verification</h2>
    <p>Please enter the 6-digit code sent to your email to verify your account and continue.</p>

    <form method="POST" action="<?= site_url('index.php/verify') ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
      <input type="text" name="code" maxlength="6" placeholder="Enter Code" required>
      <button type="submit">Verify Email</button>
    </form>

    <div class="footer-note">
      Didn’t get the code? <a href="#">Resend</a>
    </div>
  </div>

</body>
</html>
