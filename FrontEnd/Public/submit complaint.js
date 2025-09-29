// Simple feedback when submitting form
document.getElementById("complaintForm").addEventListener("submit", function(event) {
  event.preventDefault(); // prevent page reload
  alert("✅ Your complaint has been submitted successfully!");
});
