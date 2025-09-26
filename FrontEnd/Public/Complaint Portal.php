<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>MACRA Complaint Portal</title>
  <meta name="description" content="Submit or track complaints — MACRA Complaint Portal" />
  <link rel="stylesheet" href="complaint.css">
</head>
<body>
  <!-- Decorative background blobs -->
  <div class="blob one" aria-hidden="true"></div>
  <div class="blob two" aria-hidden="true"></div>

  <main class="portal" role="main" aria-labelledby="portal-title">
    <section class="hero" aria-label="Welcome area">
      <div class="logo-row" style="margin-bottom:8px">
        <a href="#" class="logo" aria-label="MACRA home">
          <span style="display:inline-block;width:44px;height:44px;border-radius:10px;background:linear-gradient(90deg,#ffb56b,#ff6b9e);box-shadow:0 6px 20px rgba(255,107,158,0.18);"></span>
          <span style="font-weight:800;font-size:18px;color:#fff">MACRA <mark>Portal</mark></span>
        </a>

        <nav class="header-nav" aria-label="Main navigation">
          <a href="#" tabindex="0">Home</a>
          <a href="#" tabindex="0">About</a>
          <a href="#" tabindex="0">Help</a>
          <a href="#" tabindex="0">Contact</a>
        </nav>
      </div>

      <h1 id="portal-title">MACRA Complaint Portal</h1>
      <p class="lead">Submit complaints or track the status of existing complaints quickly and securely. We value your feedback and strive for timely resolution.</p>

      <div class="actions" role="group" aria-label="Primary actions">
  <!-- Submit Complaint link -->
  <a href="submit complaint.php" class="action-btn btn-submit" tabindex="0" aria-label="Submit a new complaint">
    <span class="btn-icon" aria-hidden="true">
      <!-- document / pencil icon -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" fill="#fff" opacity="0.95"/>
        <path d="M20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" fill="#fff" opacity="0.95"/>
      </svg>
    </span>
    <span>Submit Complaint</span>
  </a>

  <!-- Track Complaint link -->
  <a href="track.php" class="action-btn btn-track" tabindex="0" aria-label="Track an existing complaint">
    <span class="btn-icon" aria-hidden="true">
      <!-- search / magnifier icon -->
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L20 20.49 21.49 19 15.5 14z" fill="#fff" opacity="0.95"/>
      </svg>
    </span>
    <span>Track Complaint</span>
  </a>
</div>


      <div class="footer" aria-hidden="false">
        © <span id="year"></span> Malawi Communications Regulatory Authority (MACRA)
      </div>
    </section>

    <aside class="side" aria-label="Portal summary">
      <div class="card" role="region" aria-labelledby="summary-title">
        <h3 id="summary-title" style="margin:0 0 8px 0;color:#fff;font-size:18px">Quick summary</h3>
        <p style="margin:0 0 16px 0;color:rgba(255,255,255,0.9);font-size:14px">
          Use the buttons on the left to submit a new complaint or track an existing complaint using your complaint ID.
        </p>

        <div class="stats" aria-hidden="false">
          <div class="stat" role="article" aria-label="Open complaints">
            <span class="num">1,248</span>
            <span class="lbl">Open complaints</span>
          </div>
          <div class="stat" role="article" aria-label="Resolved">
            <span class="num">9,462</span>
            <span class="lbl">Resolved (last 12 months)</span>
          </div>
        </div>
      </div>

      <div class="card" style="margin-top:14px" role="region" aria-labelledby="how-title">
        <h4 id="how-title" style="margin:0 0 8px 0;color:#fff;font-size:16px">How it works</h4>
        <ol style="margin:0;color:rgba(255,255,255,0.9);font-size:14px;padding-left:20px;line-height:1.6">
          <li>Click <strong>Submit Complaint</strong> and fill the short form.</li>
          <li>Receive your complaint via Email.</li>
          <li>Use <strong>Track Complaint</strong>  to check status.</li>
        </ol>
      </div>
    </aside>
  </main>
   <script src="complaint.js"></script>
</body>
</html>
