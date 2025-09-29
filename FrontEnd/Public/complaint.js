
  // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();

    // Simple client-side behaviour (demo)
    const submitBtn = document.getElementById('btn-submit');
    const trackBtn = document.getElementById('btn-track');

    // Keyboard accessible: Enter triggers
    submitBtn.addEventListener('click', () => {
      // In a real app you'd route to the submit complaint form
      alert('Navigate to: Submit Complaint (this is a demo).');
    }, { passive:true });

    trackBtn.addEventListener('click', () => {
      // In a real app you'd show a small modal or input for complaint ID
      const id = prompt('Enter your complaint ID to track (demo):');
      if (id) {
        alert('Tracking complaint ID: ' + id + ' (demo).');
      }
    }, { passive:true });

    // Focus the primary action for convenience and keyboard users
    window.addEventListener('load', () => {
      submitBtn.focus();
    });

    // Basic a11y: add role to buttons that are actually buttons (already <button> but useful if changed)
    submitBtn.setAttribute('role', 'button');
    trackBtn.setAttribute('role', 'button');

    // Simple visual reduction for reduced-motion users
    const mq = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (mq.matches) {
      document.documentElement.style.setProperty('--motion-scale', '0.01');
    }