<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Complaint Processing — Prototype</title>
  <style>
    /* Reset + base */
    :root{--bg:#f4f6fb;--card:#fff;--muted:#6b7280;--accent:#0f62fe;--success:#138000;--danger:#b00020}
    *{box-sizing:border-box;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial}
    body{margin:0;background:var(--bg);color:#111}
    .container{max-width:1200px;margin:28px auto;padding:18px}
    h1{font-size:20px;margin:0 0 12px}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
    .topbar .actions{display:flex;gap:8px}
    .card{background:var(--card);border-radius:12px;padding:12px;box-shadow:0 6px 18px rgba(15,22,39,0.06)}

    /* Two-panel layout */
    .layout{display:grid;grid-template-columns:420px 1fr;gap:16px}
    .queue{height:72vh;display:flex;flex-direction:column}
    .queue .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
    .search{display:flex;gap:8px}
    .search input{padding:8px 10px;border-radius:8px;border:1px solid #e6e9ef;width:220px}
    .filters select{padding:8px;border-radius:8px;border:1px solid #e6e9ef}
    .list{overflow:auto;border-radius:8px;border:1px solid #eef2ff;padding:8px;margin-top:6px}
    .item{display:flex;gap:8px;align-items:center;padding:10px;border-radius:8px;cursor:pointer}
    .item:hover{background:#f6f9ff}
    .item.active{background:#eef6ff;border-left:4px solid var(--accent)}
    .meta{font-size:12px;color:var(--muted)}

    /* Details */
    .details{height:72vh;display:flex;flex-direction:column}
    .details .body{flex:1;overflow:auto;margin-top:8px}
    .field{margin-bottom:10px}
    label{display:block;font-size:13px;margin-bottom:6px;color:var(--muted)}
    textarea{width:100%;min-height:120px;padding:10px;border-radius:8px;border:1px solid #e6e9ef}
    select, input[type=text]{padding:8px;border-radius:8px;border:1px solid #e6e9ef;width:100%}
    .row{display:flex;gap:10px}
    .row .col{flex:1}
    .actions-row{display:flex;gap:8px;align-items:center}
    button{background:var(--accent);color:#fff;border:none;padding:9px 12px;border-radius:8px;cursor:pointer}
    button.ghost{background:transparent;color:var(--accent);border:1px solid #e6eefc}
    button.danger{background:var(--danger)}

    .note{font-size:13px;color:var(--muted)}

    /* Investigator modal */
    .modal-backdrop{position:fixed;inset:0;background:rgba(9,12,23,0.4);display:none;align-items:center;justify-content:center}
    .modal{width:760px;max-width:94%;background:var(--card);border-radius:12px;padding:18px}
    .inv-table{max-height:420px;overflow:auto;margin-top:8px;border-radius:8px;border:1px solid #eef2ff}
    .inv-row{display:flex;align-items:center;gap:12px;padding:12px;border-bottom:1px solid #f3f6fb}
    .inv-row:last-child{border-bottom:none}
    .badge{background:#eef2ff;padding:6px 8px;border-radius:8px;font-weight:600}

    /* Messages */
    .toast{position:fixed;right:22px;bottom:22px;padding:12px 16px;border-radius:10px;color:#fff;display:none}
    .toast.success{background:var(--success)}
    .toast.error{background:var(--danger)}

    /* Responsive */
    @media (max-width:900px){.layout{grid-template-columns:1fr}.queue{order:2}.details{order:1}}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div>
        <h1>STEP 2 — Complaint Processing</h1>
        <div class="note">Review, categorize and assign complaints to investigators. This prototype uses sample data.</div>
      </div>
      <div class="actions">
        <button class="ghost" id="refreshBtn">Refresh</button>
        <button id="bulkAssignBtn">Bulk Assign</button>
      </div>
    </div>

    <div class="layout">
      <!-- LEFT: Complaint Queue -->
      <div class="card queue">
        <div class="header">
          <div class="search">
            <input id="searchInput" placeholder="Search by ID or name..." />
            <select id="typeFilter"><option value="">All types</option><option>Billing Dispute</option><option>Network Quality Issue</option><option>Customer Service Problem</option><option>Contract Violation</option><option>Service Interruption</option></select>
          </div>
          <div class="filters">
            <select id="severityFilter"><option value="">All severities</option><option>High</option><option>Medium</option><option>Low</option></select>
          </div>
        </div>
        <div class="list" id="complaintList" role="list">
          <!-- list items injected by JS -->
        </div>
      </div>

      <!-- RIGHT: Complaint Details -->
      <div class="card details">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div>
            <strong id="detailTitle">Select a complaint from the queue</strong>
            <div class="meta" id="detailMeta"></div>
          </div>
          <div class="actions-row">
            <button id="assignBtn" disabled>Assign Investigator</button>
            <button class="ghost" id="saveCatBtn" disabled>Save Categorization</button>
          </div>
        </div>

        <div class="body">
          <div class="field">
            <label for="complaintDesc">Complaint Description</label>
            <textarea id="complaintDesc" placeholder="Full complaint text..." disabled></textarea>
          </div>

          <div class="row">
            <div class="col field">
              <label for="categorySelect">Category</label>
              <select id="categorySelect" disabled>
                <option value="">-- Select category --</option>
                <option>Billing Dispute</option>
                <option>Network Quality Issue</option>
                <option>Customer Service Problem</option>
                <option>Contract Violation</option>
                <option>Service Interruption</option>
              </select>
            </div>
            <div class="col field">
              <label for="severitySelect">Severity</label>
              <select id="severitySelect" disabled>
                <option value="">-- Select severity --</option>
                <option>High</option>
                <option>Medium</option>
                <option>Low</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label for="notes">Notes / Comments</label>
            <textarea id="notes" placeholder="Add internal notes..." disabled></textarea>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:8px">
            <button class="ghost" id="clearBtn" disabled>Clear</button>
            <button id="saveBtn" disabled>Save</button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Investigator Modal -->
  <div class="modal-backdrop" id="modalBackdrop" aria-hidden="true">
    <div class="modal card" role="dialog" aria-modal="true" aria-labelledby="invTitle">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <h2 id="invTitle">Assign Investigator</h2>
          <div class="note">Select an investigator. The system shows current workload and availability.</div>
        </div>
        <button class="ghost" id="closeModal">Close</button>
      </div>

      <div style="margin-top:12px">
        <div style="display:flex;gap:8px;align-items:center">
          <input id="invFilter" placeholder="Filter by name or skill..." style="flex:1;padding:8px;border-radius:8px;border:1px solid #e6e9ef" />
          <select id="invSort">
            <option value="workload">Sort: Workload</option>
            <option value="avail">Sort: Availability</option>
            <option value="skill">Sort: Skill</option>
          </select>
        </div>

        <div class="inv-table" id="invList">
          <!-- investigators injected by JS -->
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:10px">
          <button class="ghost" id="bulkConfirm">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast success" id="toastSuccess">Success</div>
  <div class="toast error" id="toastError">Error</div>

  <script>
    // Fetch complaints from API
    let complaints = [];
    async function fetchComplaints() {
      try {
        const response = await fetch('https://macos-u5hl.onrender.com/complaints/fetch-all-complaints/');
        const data = await response.json();
        if (data.success && Array.isArray(data.complaints)) {
          complaints = data.complaints.map(c => ({
            id: c.id ? `C-${new Date(c.complaint_date).getFullYear()}-${String(c.id).padStart(3, '0')}` : '',
            name: c.consumer || '',
            date: c.complaint_date || '',
            status: c.status || '',
            desc: c.complaint_details || '',
            provider: c.provider || '',
            category: c.category || '',
            assignedTo: c.assigned_staff || '',
            images: c.images || []
          }));
        } else {
          complaints = [];
        }
      } catch (e) {
        complaints = [];
      }
    }

    // Investigators will be fetched from API
    let investigators = [];

    async function fetchInvestigators() {
      try {
        const response = await fetch('https://macos-u5hl.onrender.com/Auth/investigators/');
        const data = await response.json();
        if (Array.isArray(data)) {
          investigators = data.map(inv => ({
            id: inv.id ? `INV-${String(inv.id).padStart(2, '0')}` : '',
            name: inv.name || inv.username || '',
            skill: inv.skills || '',
            workload: inv.workload !== undefined ? inv.workload : 0,
            available: inv.available !== undefined ? inv.available : true
          }));
        } else if (Array.isArray(data.investigators)) {
          investigators = data.investigators.map(inv => ({
            id: inv.id ? `INV-${String(inv.id).padStart(2, '0')}` : '',
            name: inv.name || inv.username || '',
            skill: inv.skills || '',
            workload: inv.workload !== undefined ? inv.workload : 0,
            available: inv.available !== undefined ? inv.available : true
          }));
        } else {
          investigators = [];
        }
      } catch (e) {
        investigators = [];
      }
    }

    // State
    let activeComplaint = null;

    // UI refs
    const complaintListEl = document.getElementById('complaintList');
    const detailTitle = document.getElementById('detailTitle');
    const detailMeta = document.getElementById('detailMeta');
    const complaintDesc = document.getElementById('complaintDesc');
    const categorySelect = document.getElementById('categorySelect');
    const severitySelect = document.getElementById('severitySelect');
    const notesEl = document.getElementById('notes');
    const saveBtn = document.getElementById('saveBtn');
    const saveCatBtn = document.getElementById('saveCatBtn');
    const assignBtn = document.getElementById('assignBtn');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const invList = document.getElementById('invList');
    const toastSuccess = document.getElementById('toastSuccess');
    const toastError = document.getElementById('toastError');

    function renderList(filter = ''){
      complaintListEl.innerHTML='';
      const q = document.getElementById('searchInput').value.toLowerCase();
      // For this API, typeFilter and severityFilter are not used, but you can add them if needed
      complaints.forEach(c=>{
        if(q && !(String(c.id).toLowerCase().includes(q)||c.name.toLowerCase().includes(q))) return;
        // HCI: clear border, background, spacing, readable structure
        const wrapper = document.createElement('div');
        wrapper.className = 'item';
        if(activeComplaint && activeComplaint.id===c.id) wrapper.classList.add('active');
        wrapper.tabIndex=0;
        wrapper.style.border = '1px solid #e6e9ef';
        wrapper.style.borderRadius = '10px';
        wrapper.style.background = '#fff';
        wrapper.style.marginBottom = '14px';
        wrapper.style.boxShadow = '0 1px 4px rgba(15,22,39,0.04)';
        wrapper.style.transition = 'box-shadow 0.2s';
        wrapper.onmouseover = () => wrapper.style.boxShadow = '0 2px 8px rgba(15,22,39,0.10)';
        wrapper.onmouseout = () => wrapper.style.boxShadow = '0 1px 4px rgba(15,22,39,0.04)';

        // Severity badge class
        let sevClass = 'severity-unassigned';
        if (c.status && /assigned/i.test(c.status)) sevClass = 'severity-assigned';
        if (c.status && /resolved|closed/i.test(c.status)) sevClass = 'severity-resolved';
        if (c.status && /progress/i.test(c.status)) sevClass = 'severity-inprogress';
        if (c.status && /pending/i.test(c.status)) sevClass = 'severity-pending';
        if (c.status && /closed/i.test(c.status)) sevClass = 'severity-closed';

        // Format date
        let dateStr = c.date;
        if (dateStr && !isNaN(Date.parse(dateStr))) {
          const d = new Date(dateStr);
          dateStr = d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        // Images section
        let imagesSection = '';
        if (Array.isArray(c.images) && c.images.length > 0) {
          imagesSection = `<div style=\"margin-top:7px;display:flex;gap:6px;flex-wrap:wrap;\">` +
            c.images.map(imgUrl => `<img src=\"${imgUrl}\" alt=\"attachment\" style=\"width:38px;height:38px;object-fit:cover;border-radius:6px;border:1px solid #e6e9ef;box-shadow:0 1px 2px rgba(0,0,0,0.04);\">`).join('') +
            `</div>`;
        }
        wrapper.innerHTML = `
          <div style=\"display:flex;justify-content:space-between;align-items:flex-start;\">
            <div style=\"display:flex;flex-direction:column;gap:4px;\">
              <div style=\"display:flex;align-items:center;gap:10px;\">
                <span style=\"font-size:15px;font-weight:600;color:#0f62fe;letter-spacing:0.5px;\">${c.id}</span>
                <span class=\"severity-badge ${sevClass}\" style=\"font-size:12px;padding:2px 10px;border-radius:8px;background:#eef2ff;\">${c.status||'Unassigned'}</span>
              </div>
              <span style=\"font-size:13px;color:#6b7280;\">${c.name||''}</span>
              ${imagesSection}
            </div>
            <span style=\"font-size:13px;color:#6b7280;min-width:90px;text-align:right;\">${dateStr||''}</span>
          </div>
        `;
        wrapper.onclick = ()=>selectComplaint(c.id);
        complaintListEl.appendChild(wrapper);
      });
    }

    function selectComplaint(id){
      activeComplaint = complaints.find(x=>x.id===id);
      renderList();
      // populate details
      detailTitle.textContent = `${activeComplaint.id} — ${activeComplaint.name}`;
      detailMeta.textContent = `Submitted: ${activeComplaint.date} • Status: ${activeComplaint.status}`;
      complaintDesc.value = activeComplaint.desc;
      categorySelect.value = activeComplaint.type || '';
      severitySelect.value = activeComplaint.severity || '';
      notesEl.value = activeComplaint.notes || '';
      // enable controls
      [complaintDesc, categorySelect, severitySelect, notesEl, saveBtn, saveCatBtn, assignBtn, document.getElementById('clearBtn')].forEach(el=>el.disabled=false);
    }

    // Save categorization only
    saveCatBtn.addEventListener('click', ()=>{
      if(!activeComplaint) return;
      activeComplaint.type = categorySelect.value;
      activeComplaint.severity = severitySelect.value;
      showToast('Categorization saved', 'success');
      renderList();
    });

    // Save full record
    saveBtn.addEventListener('click', ()=>{
      if(!activeComplaint) return;
      activeComplaint.desc = complaintDesc.value;
      activeComplaint.type = categorySelect.value;
      activeComplaint.severity = severitySelect.value;
      activeComplaint.notes = notesEl.value;
      showToast('Complaint saved', 'success');
      renderList();
    });

    document.getElementById('clearBtn').addEventListener('click', ()=>{
      if(!activeComplaint) return;
      complaintDesc.value='';categorySelect.value='';severitySelect.value='';notesEl.value='';
    });

    // Assign investigator flow
    assignBtn.addEventListener('click', ()=>{
      if(!activeComplaint) return;
      // open modal
      modalBackdrop.style.display='flex';
      modalBackdrop.setAttribute('aria-hidden','false');
      renderInvestigators();
    });

    document.getElementById('closeModal').addEventListener('click', ()=>closeModal());
    modalBackdrop.addEventListener('click', (e)=>{ if(e.target===modalBackdrop) closeModal(); });

    function closeModal(){ modalBackdrop.style.display='none'; modalBackdrop.setAttribute('aria-hidden','true'); }

    function renderInvestigators(){
      invList.innerHTML='';
      if (!investigators.length) {
        invList.innerHTML = '<div class="note">No investigators found.</div>';
        return;
      }
      investigators.forEach(inv=>{
        const row=document.createElement('div');
        row.className='inv-row';
        // Show skills as badges if comma-separated
        let skillsHtml = '';
        if (inv.skill && typeof inv.skill === 'string') {
          const skillsArr = inv.skill.split(',').map(s=>s.trim()).filter(Boolean);
          skillsHtml = skillsArr.map(skill => `<span class='badge' style='margin-right:4px;'>${skill}</span>`).join(' ');
        }
        row.innerHTML = `
          <div style='flex:1'>
            <strong>${inv.name}</strong>
            <div class='meta'>${skillsHtml || '—'} • ${inv.available ? 'Available' : 'Unavailable'}</div>
          </div>
          <div style='width:140px;text-align:right'>
            <div class='meta'>Workload</div>
            <div class='badge'>${inv.workload}</div>
          </div>
          <div style='width:140px;text-align:right'>
            <button ${inv.available ? '' : 'disabled'} data-id='${inv.id}' class='assignNow'>Select</button>
          </div>
        `;
        invList.appendChild(row);
      });

      document.querySelectorAll('.assignNow').forEach(btn=>btn.addEventListener('click', async (e)=>{
        const invId = e.currentTarget.getAttribute('data-id');
        const inv = investigators.find(x=>x.id===invId);
        if (!activeComplaint) return;
        // confirmation
        const ok = confirm(`Assign ${activeComplaint.id} to ${inv.name}?\n\nSkills: ${inv.skill}\nWorkload: ${inv.workload}`);
        if(!ok) return;
        // Call backend assign-task endpoint
        try {
          // Extract numeric IDs if needed
          const staffId = inv.id.replace(/[^\d]/g, '');
          const complaintId = activeComplaint.id.replace(/[^\d]/g, '');
          const assignRes = await fetch('https://macos-u5hl.onrender.com/Auth/assign-task/', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Token ' + getAuthToken(),
            },
            body: JSON.stringify({
              complaint_id: complaintId,
              staff_id: staffId
            })
          });
          const assignData = await assignRes.json();
          if (!assignData.success) {
            showToast(assignData.message || 'Assignment failed', 'error');
            return;
          }
          // Fetch updated workload
          const workloadRes = await fetch(`https://macos-u5hl.onrender.com/Auth/investigators/${staffId}/workload/`, {
            headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Token ' + getAuthToken(),
            }
          });
          const workloadData = await workloadRes.json();
          if (typeof workloadData.workload === 'number') {
            inv.workload = workloadData.workload;
          }
          activeComplaint.status = 'Assigned';
          activeComplaint.assignedTo = inv.name;
          showToast(`Assigned to ${inv.name}`, 'success');
          closeModal();
          renderList();
          selectComplaint(activeComplaint.id);
        } catch (err) {
          showToast('Network or server error', 'error');
        }
      }));
    }

    // Toasts
    function showToast(msg, type='success'){
      const el = type==='success' ? toastSuccess : toastError;
      el.textContent = msg;
      el.style.display='block';
      setTimeout(()=>el.style.display='none',2800);
    }

    // Filters
    document.getElementById('searchInput').addEventListener('input', ()=>renderList());
    document.getElementById('typeFilter').addEventListener('change', ()=>renderList());
    document.getElementById('severityFilter').addEventListener('change', ()=>renderList());

    // Utility: Get auth token from localStorage (or other storage)
    function getAuthToken() {
      return localStorage.getItem('authToken') || '';
    }

    // initial render
  // Fetch and render on load
  Promise.all([
    fetchComplaints(),
    fetchInvestigators()
  ]).then(() => {
    renderList();
  });
  </script>
</body>
</html>
