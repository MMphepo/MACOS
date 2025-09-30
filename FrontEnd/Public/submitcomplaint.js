document.getElementById("complaintForm").addEventListener("submit", async function(event) {
  event.preventDefault();

  // Get values from form
  const category_id = parseInt(document.getElementById("category").value);
  const provider_id = parseInt(document.getElementById("provider").value);
  const complaint_details = document.getElementById("description").value.trim();
  const evidenceInput = document.getElementById("evidence");
  // You may want to get consumer_id from session/localStorage or ask user to login
  let consumer_id = null;
  try {
    const user = JSON.parse(localStorage.getItem('macos_user'));
    consumer_id = user && user.id ? user.id : null;
  } catch (e) { consumer_id = null; }
  if (!consumer_id) {
    alert('You must be logged in to submit a complaint.');
    return;
  }

  // Set default status_id (e.g., 1 for "Submitted" or as required by backend)
  const status_id = 1;

  // Optional fields
  // assigned_staff_id can be added if needed, e.g., from a dropdown or left blank
  // For now, we leave it undefined

  // Prepare form data for file upload
  const formData = new FormData();
  formData.append('consumer_id', consumer_id);
  formData.append('provider_id', provider_id);
  formData.append('category_id', category_id);
  formData.append('status_id', status_id);
  formData.append('complaint_details', complaint_details);
  if (evidenceInput.files.length > 0) {
    for (let i = 0; i < evidenceInput.files.length; i++) {
      formData.append('attachments', evidenceInput.files[i]);
    }
  }

  // Optionally add assigned_staff_id if you have it
  // formData.append('assigned_staff_id', assigned_staff_id);

  // Get token for authentication if required
  const token = localStorage.getItem('macos_token');

  try {
    const response = await fetch('https://macos-u5hl.onrender.com/complaints/file-complaint/', {
      method: 'POST',
      headers: token ? { 'Authorization': 'Bearer ' + token } : {},
      body: formData
    });
    const data = await response.json();
    if (response.ok) {
      alert('✅ Your complaint has been submitted successfully!');
      document.getElementById('complaintForm').reset();
    } else {
      let errorMsg = data.detail || 'Failed to submit complaint.';
      if (data.errors) {
        errorMsg += '\n' + Object.values(data.errors).join(' ');
      }
      alert('❌ ' + errorMsg);
    }
  } catch (error) {
    alert('❌ Network error. Please try again later.');
  }
});
