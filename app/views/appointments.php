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
<title>My Appointments | Veterinary Dashboard</title>
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
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px) saturate(180%);
  box-shadow: 0 4px 16px rgba(255, 145, 77, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
  border-bottom: 1px solid rgba(255, 145, 77, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 50px;
  z-index: 1000;
  transition: all 0.3s ease;
}
header h1 {
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-size: 1.625rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}
.user-info {
  color: #6b7280;
  font-size: 0.9rem;
  font-weight: 500;
}
.btn {
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  color: #fff;
  padding: 11px 24px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(255,145,77,0.3), 0 2px 4px rgba(255,145,77,0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.5s ease;
}
.btn:hover::before {
  left: 100%;
}
.btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 20px rgba(255,145,77,0.4), 0 4px 8px rgba(255,145,77,0.25);
}

/* ===== LAYOUT ===== */
.dashboard {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
}

/* ===== SIDEBAR ===== */
.sidebar {
  background: linear-gradient(to bottom, #ffffff, #fafafa);
  box-shadow: 2px 0 12px rgba(0,0,0,0.03);
  padding: 40px 20px;
  display: flex;
  flex-direction: column;
  border-right: 1px solid rgba(255, 145, 77, 0.1);
}
.sidebar h3 {
  text-align: center;
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 30px;
  font-weight: 800;
  font-size: 1.125rem;
}
.sidebar a {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-radius: 10px;
  color: #6b7280;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-bottom: 8px;
  position: relative;
  overflow: hidden;
}
.sidebar a::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 3px;
  background: var(--primary);
  transform: scaleY(0);
  transition: transform 0.25s ease;
}
.sidebar a:hover::before {
  transform: scaleY(1);
}
.sidebar a:hover {
  background: linear-gradient(to right, rgba(255, 145, 77, 0.08), transparent);
  color: var(--primary);
  transform: translateX(5px);
}
.sidebar a.active {
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  color: white;
  box-shadow: 0 4px 12px rgba(255,145,77,0.4), 0 2px 4px rgba(255,145,77,0.2);
  transform: translateX(5px);
}
.sidebar a.active::before {
  display: none;
}

/* ===== MAIN CONTENT ===== */
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
  background: linear-gradient(to bottom right, #ffffff, #fefefe);
  border-radius: 16px;
  padding: 32px;
  margin-bottom: 30px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.08);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #e5e7eb;
  position: relative;
  overflow: hidden;
}
.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--light-orange));
  opacity: 0;
  transition: opacity 0.3s ease;
}
.card:hover::before {
  opacity: 1;
}
.card h3 {
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 24px;
  font-size: 1.375rem;
  font-weight: 700;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(255, 145, 77, 0.12), 0 4px 8px rgba(0, 0, 0, 0.08);
  border-color: rgba(255, 145, 77, 0.2);
}

/* ===== TABLE ===== */
.appointments {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.appointments th, .appointments td {
  padding: 16px 18px;
  text-align: left;
}
.appointments th {
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  color: #fff;
  border: none;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.8125rem;
  letter-spacing: 0.05em;
}
.appointments tr:nth-child(even) {
  background: #fef8f4;
}
.appointments tr:hover {
  background: rgba(255, 145, 77, 0.08);
  transition: all 0.2s ease;
  transform: scale(1.001);
}

/* ===== FORM ===== */
form label {
  display: block;
  margin-top: 12px;
  font-weight: 600;
  color: #374151;
  font-size: 0.9375rem;
}
form select, form input[type="date"], form input[type="time"] {
  width: 100%;
  padding: 12px 14px;
  margin-top: 8px;
  border-radius: 10px;
  border: 1.5px solid #e5e7eb;
  outline: none;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.9375rem;
  background: white;
}
form select:focus, form input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(255, 145, 77, 0.1), 0 2px 8px rgba(255, 145, 77, 0.15);
  transform: translateY(-1px);
}
form button {
  margin-top: 20px;
  background: linear-gradient(135deg, var(--primary), var(--light-orange));
  color: white;
  padding: 12px 28px;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.9375rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(255,145,77,0.3), 0 2px 4px rgba(255,145,77,0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
form button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.5s ease;
}
form button:hover::before {
  left: 100%;
}
form button:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 8px 20px rgba(255,145,77,0.4), 0 4px 8px rgba(255,145,77,0.25);
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
    <a href="<?= site_url('appointments') ?>" class="active">Appointments</a>
    <a href="<?= site_url('pets') ?>">Pets</a>
    <a href="<?= site_url('veterinarians') ?>">Veterinarians</a>
    <a href="<?= site_url('settings') ?>">Settings</a>
  </aside>

  <!-- Main Content -->
  <section class="main-content">

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
      <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        ✅ <?= htmlspecialchars($_SESSION['success']); ?>
      </div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        ❌ <?= htmlspecialchars($_SESSION['error']); ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
      <h3>📅 Upcoming Appointments</h3>
      <p>Your scheduled appointments that are pending or confirmed.</p>

      <table class="appointments">
        <thead>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Pet</th>
            <th>Service</th>
            <th>Veterinarian</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($upcomingAppointments)): ?>
            <?php foreach ($upcomingAppointments as $a): ?>
              <tr>
                <td><?= htmlspecialchars($a['appointment_date']); ?></td>
                <td><?= htmlspecialchars(substr($a['appointment_time'], 0, 5)); ?></td>
                <td><?= htmlspecialchars($a['pet_name']); ?></td>
                <td><?= htmlspecialchars($a['service_name']); ?></td>
                <td><?= htmlspecialchars($a['vet_name']); ?></td>
                <td><span style="color: #ff914d; font-weight: 600;"><?= htmlspecialchars($a['status']); ?></span></td>
                <td>
                  <?php if (in_array($a['status'], ['Pending', 'Confirmed'])): ?>
                    <a href="<?= site_url('appointments/cancel/' . $a['id']) ?>" 
                       onclick="return confirm('Are you sure you want to cancel this appointment?');"
                       style="color:#e74c3c; text-decoration:none; font-weight:600;">Cancel</a>
                  <?php else: ?>
                    <span style="color:gray;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align:center; color: #999;">No upcoming appointments.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card" style="margin-top: 30px;">
      <h3>📜 Appointment History</h3>
      <p>Past and cancelled appointments.</p>

      <table class="appointments">
        <thead>
          <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Pet</th>
            <th>Service</th>
            <th>Veterinarian</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($historyAppointments)): ?>
            <?php foreach ($historyAppointments as $a): ?>
              <tr style="opacity: 0.7;">
                <td><?= htmlspecialchars($a['appointment_date']); ?></td>
                <td><?= htmlspecialchars(substr($a['appointment_time'], 0, 5)); ?></td>
                <td><?= htmlspecialchars($a['pet_name']); ?></td>
                <td><?= htmlspecialchars($a['service_name']); ?></td>
                <td><?= htmlspecialchars($a['vet_name']); ?></td>
                <td>
                  <?php if ($a['status'] === 'Cancelled'): ?>
                    <span style="color: #e74c3c; font-weight: 600;">❌ Cancelled</span>
                  <?php elseif ($a['status'] === 'Completed'): ?>
                    <span style="color: #27ae60; font-weight: 600;">✅ Completed</span>
                  <?php else: ?>
                    <span style="color: #95a5a6; font-weight: 600;">📅 <?= htmlspecialchars($a['status']); ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center; color: #999;">No appointment history yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3>Book a New Appointment</h3>
      <form action="<?= site_url('appointments/book') ?>" method="POST">
        <label for="pet_id">Select Pet:</label>
        <select name="pet_id" id="pet_id" required>
          <option value="">-- Choose your pet --</option>
          <?php foreach ($pets as $pet): ?>
            <option value="<?= $pet['id']; ?>"><?= htmlspecialchars($pet['name']); ?> (<?= htmlspecialchars($pet['species']); ?>)</option>
          <?php endforeach; ?>
        </select>

        <label for="service">Select Service:</label>
        <select name="service" id="service" required>
          <option value="">-- Choose service --</option>
          <?php foreach ($services as $service): ?>
            <option value="<?= $service['id']; ?>" data-specialization="<?= htmlspecialchars($service['service_name']); ?>"><?= htmlspecialchars($service['service_name']); ?> - ₱<?= number_format($service['fee'], 2); ?></option>
          <?php endforeach; ?>
        </select>

        <label for="veterinarian_id">Select Veterinarian:</label>
        <select name="veterinarian_id" id="veterinarian_id" required disabled>
          <option value="">-- Select a service first --</option>
          <?php foreach ($vets as $vet): ?>
            <option value="<?= $vet['id']; ?>" data-specialization="<?= htmlspecialchars($vet['specialization']); ?>"><?= htmlspecialchars($vet['name']); ?> (<?= htmlspecialchars($vet['specialization']); ?>)</option>
          <?php endforeach; ?>
        </select>

        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required>

        <label for="time">Time:</label>
        <select name="time" id="time" required>
          <option value="">-- Choose time slot --</option>
        </select>
        <p id="timeSlotInfo" style="font-size: 0.85rem; color: #666; margin-top: 5px;">Select a service, veterinarian, and date to see available time slots.</p>

        <button type="submit">Book Appointment</button>
      </form>
    </div>

  </section>
</div>

<footer>
  &copy; <?= date('Y'); ?> Automated Scheduling and Tracking System for Veterinary Services – Calapan City, Oriental Mindoro
</footer>

<script>
// Prevent booking appointments in the past and manage 30-min time slots
document.addEventListener('DOMContentLoaded', function() {
  const dateInput = document.getElementById('date');
  const timeInput = document.getElementById('time');
  const vetInput = document.getElementById('veterinarian_id');
  const serviceInput = document.getElementById('service');
  const timeSlotInfo = document.getElementById('timeSlotInfo');
  
  // Set minimum date to today
  const today = new Date();
  const todayStr = today.toISOString().split('T')[0];
  dateInput.setAttribute('min', todayStr);
  
  // Generate 30-minute time slots from 8:00 AM to 6:00 PM
  function generateTimeSlots() {
    const slots = [];
    for (let hour = 8; hour < 18; hour++) {
      slots.push(String(hour).padStart(2, '0') + ':00');
      slots.push(String(hour).padStart(2, '0') + ':30');
    }
    return slots;
  }
  
  // Initialize time slots on page load
  function initializeTimeSlots() {
    const allSlots = generateTimeSlots();
    timeInput.innerHTML = '<option value="">-- Choose time slot --</option>';
    allSlots.forEach(slot => {
      const option = document.createElement('option');
      option.value = slot;
      option.textContent = slot;
      timeInput.appendChild(option);
    });
  }
  
  // Call initialization
  initializeTimeSlots();
  
  // Filter veterinarians based on selected service
  serviceInput.addEventListener('change', function() {
    const selectedService = serviceInput.options[serviceInput.selectedIndex];
    const serviceSpecialization = selectedService ? selectedService.dataset.specialization : '';
    
    console.log('Service changed to:', serviceSpecialization);
    
    // Reset veterinarian selection
    vetInput.value = '';
    
    // Reset time slots
    initializeTimeSlots();
    timeSlotInfo.textContent = 'Select a veterinarian and date to see available time slots.';
    timeSlotInfo.style.color = '#666';
    
    if (!serviceSpecialization) {
      // Disable vet dropdown if no service selected
      vetInput.disabled = true;
      vetInput.innerHTML = '<option value="">-- Select a service first --</option>';
      console.log('No service selected');
      return;
    }
    
    // Build new options with only matching veterinarians
    let newOptions = '<option value="">-- Choose veterinarian --</option>';
    let matchCount = 0;
    
    console.log('Checking veterinarians:');
    <?php foreach ($vets as $vet): ?>
    {
      const vetSpec = <?= json_encode($vet['specialization']); ?>;
      const vetName = "<?= htmlspecialchars($vet['name']); ?>";
      const vetId = "<?= $vet['id']; ?>";
      console.log('  Vet:', vetName, 'Spec:', vetSpec, 'Match:', vetSpec === serviceSpecialization);
      if (vetSpec === serviceSpecialization) {
        newOptions += '<option value="' + vetId + '" data-specialization="' + vetSpec + '">' + vetName + ' (' + vetSpec + ')</option>';
        matchCount++;
      }
    }
    <?php endforeach; ?>
    
    console.log('Total matches:', matchCount);
    
    if (matchCount === 0) {
      vetInput.innerHTML = '<option value="">-- No veterinarians available for this service --</option>';
      vetInput.disabled = true;
    } else {
      vetInput.innerHTML = newOptions;
      vetInput.disabled = false;
    }
  });
  
  // Trigger service change to initialize vet dropdown state
  serviceInput.dispatchEvent(new Event('change'));
  
  // Fetch booked slots for selected date and veterinarian
  async function fetchBookedSlots() {
    const selectedDate = dateInput.value;
    const selectedVet = vetInput.value;
    
    if (!selectedDate || !selectedVet) {
      return [];
    }
    
    try {
      const url = '<?= site_url('appointments/getBookedSlots') ?>?date=' + selectedDate + '&vet_id=' + selectedVet;
      console.log('Fetching booked slots from:', url);
      const response = await fetch(url);
      const data = await response.json();
      console.log('Booked slots received:', data.bookedSlots);
      return data.bookedSlots || [];
    } catch (error) {
      console.error('Error fetching booked slots:', error);
      return [];
    }
  }
  
  // Update available time slots
  async function updateTimeSlots() {
    const selectedDate = dateInput.value;
    const selectedVet = vetInput.value;
    
    console.log('Update time slots called - Date:', selectedDate, 'Vet:', selectedVet);
    
    if (!selectedDate || !selectedVet) {
      // Show all slots if date or vet not selected
      initializeTimeSlots();
      timeSlotInfo.textContent = 'Select a service, veterinarian, and date to see real-time availability.';
      timeSlotInfo.style.color = '#666';
      return;
    }
    
    timeSlotInfo.textContent = 'Loading available slots...';
    timeSlotInfo.style.color = '#ff914d';
    
    const allSlots = generateTimeSlots();
    const bookedSlots = await fetchBookedSlots();
    console.log('All slots:', allSlots);
    console.log('Booked slots to filter:', bookedSlots);
    const now = new Date();
    const isToday = selectedDate === todayStr;
    
    // Clear existing options
    timeInput.innerHTML = '<option value="">-- Choose time slot --</option>';
    
    let availableCount = 0;
    allSlots.forEach(slot => {
      // Check if slot is in the past (only for today)
      if (isToday) {
        const slotTime = new Date(selectedDate + 'T' + slot);
        if (slotTime < now) {
          return; // Skip past slots
        }
      }
      
      // Check if slot is already booked - completely skip it
      const isBooked = bookedSlots.includes(slot);
      console.log('Checking slot:', slot, 'isBooked:', isBooked, 'bookedSlots:', bookedSlots);
      if (isBooked) {
        console.log('SKIPPING booked slot:', slot);
        return; // Don't show booked slots at all
      }
      
      // Only add available slots
      const option = document.createElement('option');
      option.value = slot;
      option.textContent = slot;
      
      availableCount++;
      timeInput.appendChild(option);
    });
    
    if (availableCount === 0) {
      timeSlotInfo.textContent = 'No available slots for this date and veterinarian.';
      timeSlotInfo.style.color = '#e74c3c';
    } else {
      timeSlotInfo.textContent = availableCount + ' slot(s) available. Each appointment is 30 minutes.';
      timeSlotInfo.style.color = '#27ae60';
    }
  }
  
  // Update slots when date or veterinarian changes
  dateInput.addEventListener('change', updateTimeSlots);
  vetInput.addEventListener('change', updateTimeSlots);
  
  // Validate on form submission
  const form = timeInput.closest('form');
  form.addEventListener('submit', function(e) {
    const selectedDate = dateInput.value;
    const selectedTime = timeInput.value;
    
    if (!selectedTime) {
      e.preventDefault();
      alert('Please select a time slot.');
      return false;
    }
    
    // Check if selected datetime is in the past
    const now = new Date();
    const selected = new Date(selectedDate + 'T' + selectedTime);
    
    if (selected < now) {
      e.preventDefault();
      alert('Cannot book appointments in the past. Please select a future date and time.');
      return false;
    }
  });
});
</script>

</body>
</html>
