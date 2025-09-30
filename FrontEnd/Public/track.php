<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Complaints - 2 GUYS</title>
  <link rel="stylesheet" href="track.css">
</head>
<body>
  <!-- Particle background -->
  <canvas id="bgParticles"></canvas>

  <!-- Header -->
  <header class="header" role="banner">
    <div class="logo">
      <div class="logo-circle">logo</div>
      <div class="logo-text">MACRA</div>
    </div>
    <nav class="nav-buttons" role="navigation">
      <a href="Complaint Portal.php" class="btn back-btn" aria-label="Go back to previous page">← Back</a>
      <a href="track.php" class="btn btn-track">Track</a>
      <a href="submit complaint.php" class="btn btn-submit">Submit</a>
      <a href="profile.html" class="user-btn" aria-label="User menu">👤</a>
    </nav>
  </header>

  <!-- Main Content -->
  <div class="container">
    <h1>Track Your Complaints</h1>

    <!-- Search Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search complaints by keyword or date...">
    </div>

    <table class="status-table" id="complaintTable">
      <thead>
        <tr>
          <th>Status</th>
          <th>Complaint</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <!-- Complaints will be loaded here by JS -->
      </tbody>
    </table>
  </div>

  <script >

    // Background particles
console.log('[track.js] Script loaded');
const canvas = document.getElementById('bgParticles');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

class Particle {
  constructor() {
    this.x = Math.random() * canvas.width;
    this.y = Math.random() * canvas.height;
    this.size = Math.random() * 3 + 1;
    this.speedX = (Math.random() - 0.5) * 0.5;
    this.speedY = (Math.random() - 0.5) * 0.5;
  }
  update() {
    this.x += this.speedX;
    this.y += this.speedY;
    if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
    if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
  }
  draw() {
    ctx.fillStyle = 'rgba(255,255,255,0.6)';
    ctx.beginPath();
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
    ctx.fill();
  }
}

const particles = [];
for (let i = 0; i < 50; i++) {
  particles.push(new Particle());
}

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach(p => {
    p.update();
    p.draw();
  });
  requestAnimationFrame(animate);
}
animate();

window.addEventListener('resize', () => {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
});

// Search filter

// Fetch and display complaints for the logged-in user
async function loadComplaints() {
  console.log('[track.js] loadComplaints called');
  let user = null;
  try {
    user = JSON.parse(localStorage.getItem('macos_user'));
    console.log('[track.js] Loaded user from localStorage:', user);
  } catch (e) {
    user = null;
    console.log('[track.js] Error parsing user from localStorage:', e);
  }
  if (!user || !user.username) {
    console.log('[track.js] No user or username found, cannot fetch complaints');
    document.querySelector('#complaintTable tbody').innerHTML = '<tr><td colspan="3">You must be logged in to view your complaints.</td></tr>';
    return;
  }
  const username = encodeURIComponent(user.username);
  const token = localStorage.getItem('macos_token');
  console.log(`[track.js] Fetching complaints for username: ${username}`);
  try {
    const response = await fetch(`https://macos-u5hl.onrender.com/complaints/fetch-filed-complaints/${username}/`, {
      headers: token ? { 'Authorization': 'Bearer ' + token } : {}
    });
    const data = await response.json();
    console.log('[track.js] API response:', data);
    // Expecting { success: true, complaints: [...] }
    if (response.ok && data && Array.isArray(data.complaints)) {
      if (data.complaints.length === 0) {
        document.querySelector('#complaintTable tbody').innerHTML = '<tr><td colspan="3">No complaints found.</td></tr>';
        return;
      }
      let rows = '';
      data.complaints.forEach(complaint => {
        let status = complaint.status || 'Unknown';
        let statusClass = 'pending';
        if (/progress/i.test(status)) statusClass = 'in-progress';
        else if (/resolved|closed/i.test(status)) statusClass = 'resolved';
        let details = complaint.complaint_details || complaint.details || '-';
        let date = complaint.complaint_date ? new Date(complaint.complaint_date).toLocaleDateString() : '-';
        rows += `<tr><td><span class="status-badge ${statusClass}">${status}</span></td><td>${details}</td><td>${date}</td></tr>`;
      });
      document.querySelector('#complaintTable tbody').innerHTML = rows;
    } else {
      console.log('[track.js] Failed to load complaints, response not ok or missing complaints array');
      document.querySelector('#complaintTable tbody').innerHTML = '<tr><td colspan="3">Failed to load complaints.</td></tr>';
    }
  } catch (error) {
    console.log('[track.js] Network or fetch error:', error);
    document.querySelector('#complaintTable tbody').innerHTML = '<tr><td colspan="3">Network error. Please try again later.</td></tr>';
  }
}

// Initial load
window.addEventListener('DOMContentLoaded', loadComplaints);

// Search filter (works with dynamic data)
document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  let rows = document.querySelectorAll("#complaintTable tbody tr");
  rows.forEach(row => {
    let text = row.textContent.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  });
});

  </script>
</body>
</html>
