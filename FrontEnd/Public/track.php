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
        <tr>
          <td><span class="status-badge pending">Pending</span></td>
          <td>Login Authentication Issues</td>
          <td>Sep 15, 2025</td>
        </tr>
        <tr>
          <td><span class="status-badge in-progress">In Progress</span></td>
          <td>Payment Processing Delays</td>
          <td>Sep 16, 2025</td>
        </tr>
        <tr>
          <td><span class="status-badge resolved">Resolved</span></td>
          <td>Dark Mode Feature Request</td>
          <td>Sep 14, 2025</td>
        </tr>
      </tbody>
    </table>
  </div>

  <script src="track.js"></script>
</body>
</html>
