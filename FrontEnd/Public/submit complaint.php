<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Complaint</title>
  <link rel="stylesheet" href="submit complaint.css">
</head>
<body>

  <!-- Navigation Bar -->
  <div class="navbar">
    <a href="Complaint Portal.php" class="back-btn">⬅ Back</a>
    <div class="logo">
      <img src="image/MACRA.webp" alt="MACRA Logo">
      <span>MACRA</span>
    </div>
    <div class="profile">
      <img src="image/tt.jpg" alt="Profile">
      <span>KHYGON</span>
    </div>
  </div>

  <!-- Complaint Form -->
  <div class="container">
    <h2>📩 Submit Your Complaint</h2>
    <div class="step-indicator">Step 1 of 1 — Complaint Form</div>

    <form id="complaintForm">
      <label for="category">Complaint Category</label>
      <select id="category" name="category" required>
        <option value="">-- Select Category --</option>
        <option value="1">📡 Network Issues</option>
        <option value="2">💳 Billing Problems</option>
        <option value="3">⚡ Service Termination</option>
        <option value="4">⚡ customer service</option>
        <option value="5">⚡Data Bundles</option>
        <option value="other">📝 Other</option>
      </select>

      <label for="provider">Service Provider</label>
      <select id="provider" name="provider" required>
        <option value="">-- Select Provider --</option>
        <option value="1">TNM Plc</option>
        <option value="2">Airtel Malawi</option>
        <option value="3">skyband</option>
        <option value="4">Access Communications</option>
        <option value="5">Globe Internet</option>
      </select>

      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Describe your complaint clearly..." required></textarea>
      <div class="help-text">⚠️ Be specific and mention times, dates, or error messages if possible.</div>

      <label for="evidence">Upload Evidence</label>
      <input type="file" id="evidence" name="evidence">
      <div class="help-text">You can upload screenshots, bills, or related files.</div>

      <div class="checkbox">
        <input type="checkbox" id="confirm" required>
        <label for="confirm">I confirm my details are correct</label>
      </div>

      <button type="submit" class="btn">🚀 Submit Complaint</button>
    </form>
  </div>

  <!-- Footer -->
  <footer>
    <p>📞 Helpline: <strong>+265 1 772 300</strong></p>
    <p>📧 Email: <a href="mailto:info@macra.org.mw">info@macra.org.mw</a></p>
    <p>🏢 Address: MACRA House, Salmin Armour Road, Blantyre, Malawi</p>
    <p class="copy">© 2025 MACRA. All Rights Reserved.</p>
  </footer>
 <script src="submitcomplaint.js"></script>
</body>
</html>
