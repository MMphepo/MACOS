<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Investigation Letter Manager</title>
  <meta name="description" content="Investigation letter drafting, preview, send simulation and log (single-file app)" />
  <style>
    :root{--bg:#f6f8fb;--card:#ffffff;--muted:#6b7280;--accent:#055a8c;--accent-2:#06a0e9}
    *{box-sizing:border-box}
    body{font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; margin:0; background:var(--bg); color:#111; -webkit-font-smoothing:antialiased}
    .site-header{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:linear-gradient(90deg,#063b5a, #0a6184);color:#fff}
    .site-header .brand{display:flex;gap:12px;align-items:center}
    .logo{font-weight:700;padding:6px 10px;border-radius:6px;background:rgba(255,255,255,0.08)}
    .title{font-size:1.1rem}
    .main-nav button{background:transparent;border:0;color:rgba(255,255,255,0.95);padding:8px 10px;border-radius:6px;cursor:pointer}
    .main-nav button.active{background:rgba(255,255,255,0.08)}
    .container{display:grid;grid-template-columns:1fr 420px;gap:20px;padding:20px;max-width:1200px;margin:18px auto}
    .editor, .preview{background:var(--card);padding:18px;border-radius:12px;box-shadow:0 6px 18px rgba(12,20,30,0.06)}
    h2{margin:0 0 12px 0}
    .row{margin-bottom:12px}
    .two-up{display:flex;gap:12px}
    input,select,textarea{width:100%;padding:10px;border:1px solid #d6dbe7;border-radius:8px;font-size:0.95rem}
    textarea{resize:vertical}
    label{display:block;margin-bottom:6px;font-size:0.85rem;color:var(--muted)}
    .controls{display:flex;gap:10px;align-items:center}
    .controls button{padding:10px 12px;border-radius:10px;border:0;background:var(--accent);color:#fff;cursor:pointer}
    .controls button#print-letter{background:transparent;color:var(--accent);border:1px solid var(--accent)}
    .btn-ghost{background:transparent;border:1px solid #d6dbe7;color:#111;padding:8px 10px;border-radius:8px;cursor:pointer}
    .letter-card{white-space:pre-wrap;padding:16px;border:1px dashed #e6eef8;border-radius:8px;background:linear-gradient(180deg,#ffffff,#fbfdff);min-height:240px}
    .log{max-width:1200px;margin:18px auto;padding:20px}
    .log-controls{display:flex;gap:12px;align-items:center;margin-bottom:12px}
    .log-table{width:100%;border-collapse:collapse;background:var(--card);border-radius:8px;overflow:hidden;box-shadow:0 6px 14px rgba(12,20,30,0.04)}
    .log-table th, .log-table td{padding:10px;border-bottom:1px solid #f1f5f9;text-align:left;font-size:0.95rem}
    .log-table thead th{background:#f8fafc;color:var(--muted);font-weight:600}
    .status-badge{padding:6px 8px;border-radius:999px;font-weight:600;font-size:0.85rem;display:inline-block}
    .status-awaiting{background:#fff4e5;color:#ad5b00}
    .status-responded{background:#e6ffef;color:#087f3b}
    .status-overdue{background:#ffe6e6;color:#a10f0f}
    .actions button{margin-right:6px}
    /* Print styles */
    @media print{
      body *{visibility:hidden}
      #letter-preview, #letter-preview *{visibility:visible}
      #letter-preview{position:fixed;left:0;top:0;width:100%;padding:20px}
    }
    /* Focus styles for keyboard users */
    :focus{outline:3px solid rgba(5,90,140,0.18);outline-offset:2px}
    /* Responsive */
    @media (max-width:900px){
      .container{grid-template-columns:1fr; padding:12px}
      .preview{order:2}
    }
    /* small helpers */
    .muted{color:var(--muted);font-size:0.9rem}
    .small{font-size:0.85rem}
    .top-row{display:flex;justify-content:space-between;align-items:center;gap:12px}
    .pill{padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.06);font-weight:600}
    button:disabled{opacity:0.6;cursor:not-allowed}
  </style>
</head>
<body>
  <header class="site-header" role="banner">
    <div class="brand" aria-hidden="false">
      <div class="logo">MACRA</div>
      <div class="title">Investigation Letters</div>
    </div>
    <nav class="main-nav" aria-label="Main navigation">
      <button id="nav-dashboard" class="">Dashboard</button>
      <button id="nav-letters" class="active">Letters</button>
      <button id="nav-log">Log</button>
      <button id="nav-templates">Templates</button>
    </nav>
  </header>

  <main class="container" role="main">
    <section class="editor" aria-labelledby="editor-heading">
      <div class="top-row">
        <h2 id="editor-heading">Draft Investigation Letter</h2>
        <div class="pill small" id="app-clock" aria-hidden="true"></div>
      </div>

      <form id="letter-form" onsubmit="return false;" aria-describedby="editor-heading">
        <div class="row">
          <label for="template-select">Template</label>
          <select id="template-select" name="template-select" aria-label="Select template"></select>
        </div>

        <div class="row two-up" style="margin-bottom:8px">
          <div>
            <label for="provider">Service Provider</label>
            <input id="provider" name="provider" required aria-required="true" placeholder="e.g. Acme Telecom" />
          </div>
        </div>

        <div class="row two-up">
          <div>
            <label for="ref">Reference</label>
            <input id="ref" name="ref" placeholder="INV-2025-001" />
          </div>
          <div>
            <label for="deadline">Response Deadline</label>
            <input type="date" id="deadline" name="deadline" />
          </div>
        </div>

        <div class="row">
          <label for="body">Custom text (optional)</label>
          <textarea id="body" rows="6" placeholder="Extra details to append to the template..."></textarea>
        </div>

        <div class="controls" style="margin-top:8px">
          <button type="button" id="save-draft" class="btn-ghost">Save Draft</button>
          <button type="button" id="send-letter">Send Letter</button>
          <button type="button" id="print-letter" class="btn-ghost">Print</button>
          <button type="button" id="clear-form" class="btn-ghost" title="Reset form">Reset</button>
        </div>
      </form>
    </section>

    <aside class="preview" aria-labelledby="preview-heading">
      <h2 id="preview-heading">Letter Preview</h2>
      <article id="letter-preview" class="letter-card" aria-live="polite" aria-atomic="true">
        <!-- preview text injected here -->
      </article>
    </aside>
  </main>

  <section class="log" aria-labelledby="log-heading">
    <h2 id="log-heading">Letter Log & Tracking</h2>
    <div class="log-controls">
      <label class="small">Filter
        <select id="filter-status" aria-label="Filter log">
          <option value="all">All</option>
          <option value="awaiting">Awaiting Response</option>
          <option value="responded">Responded</option>
          <option value="overdue">Overdue</option>
        </select>
      </label>
      <input id="search-log" placeholder="Search by provider, ref, dept" aria-label="Search log" />
      <div style="flex:1"></div>
      <button id="export-json" class="btn-ghost" title="Export log to JSON">Export JSON</button>
      <button id="import-json" class="btn-ghost" title="Import JSON">Import JSON</button>
    </div>

    <table id="log-table" class="log-table" aria-live="polite" role="table">
      <thead>
        <tr><th>#</th><th>Date</th><th>Provider</th><th>Dept</th><th>Ref</th><th>Deadline</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </section>

  <input type="file" id="file-input" accept="application/json" style="display:none" />

  <script>
    (function(){
      // Templates
      const templates = {
        "Standard Investigation": "{{date}}\n\nTo: {{department}}\n{{provider}}\n\nSubject: Investigation Request - {{ref}}\n\nDear Sir/Madam,\n\nWe write to request information and supporting documentation regarding recent complaints received concerning your services. Please provide an explanation of the matters raised, copies of relevant logs, outage reports, and corrective actions taken.\n\nPlease respond by {{deadline}}.\n\nYours faithfully,\nConsumer Affairs Office",
        "Short Notice": "{{date}}\n\nTo: {{department}} - {{provider}}\n\nRef: {{ref}}\n\nThis is to notify you that an investigation has been opened related to consumer complaints. Please respond with the requested information by {{deadline}}.",
        "Custom Formal": "{{date}}\n\n{{department}}\n{{provider}}\n\nRef: {{ref}}\n\nSubject: Request for information and documents\n\nDear Sir/Madam,\n\nThe Consumer Affairs Office has received complaints alleging service disruption and potential contractual breaches. Kindly submit the following documentation: incident reports, customer-contact logs, and remedial action plans. Respond by {{deadline}}.\n\nSincerely,\nConsumer Affairs Manager"
      };

      // DOM
      const tplSelect = document.getElementById('template-select');
      const providerInput = document.getElementById('provider');
      const refInput = document.getElementById('ref');
      const deadlineInput = document.getElementById('deadline');
      const bodyInput = document.getElementById('body');
      const preview = document.getElementById('letter-preview');
      const saveBtn = document.getElementById('save-draft');
      const sendBtn = document.getElementById('send-letter');
      const printBtn = document.getElementById('print-letter');
      const clearBtn = document.getElementById('clear-form');
      const filter = document.getElementById('filter-status');
      const searchLog = document.getElementById('search-log');
      const logTableBody = document.querySelector('#log-table tbody');
      const exportBtn = document.getElementById('export-json');
      const importBtn = document.getElementById('import-json');
      const fileInput = document.getElementById('file-input');
      const appClock = document.getElementById('app-clock');

      // Populate template selector
      Object.keys(templates).forEach(k=>{
        const opt = document.createElement('option'); opt.value = k; opt.textContent = k; tplSelect.appendChild(opt);
      });

      // Helpers
      function formatDateISO(date){
        if(!date) return '';
        const d = new Date(date);
        return d.toISOString().split('T')[0];
      }
      function todayString(){
        return new Date().toLocaleDateString();
      }

      function collectForm(){
        return {
          date: todayString(),
          provider: providerInput.value.trim() || '[Provider name]',
          ref: refInput.value.trim() || 'REF-000',
          deadline: deadlineInput.value || '',
          body: bodyInput.value.trim() || ''
        };
      }

      function fillTemplate(tpl, data){
        let text = tpl.replace(/{{(\\w+)}}/g, (m,key)=> data[key] || '');
        if(data.body) text += "\\n\\n" + data.body;
        return text;
      }

      function renderPreview(){
        const data = collectForm();
        const tpl = templates[tplSelect.value] || Object.values(templates)[0];
        const text = fillTemplate(tpl, data);
        preview.textContent = text;
      }

      // Storage
      const STORAGE_KEY = 'investigation_letters_log_v1';
      function loadLog(){ try { const raw = localStorage.getItem(STORAGE_KEY); return raw ? JSON.parse(raw) : []; } catch(e) { return []; } }
      function saveLog(list){ localStorage.setItem(STORAGE_KEY, JSON.stringify(list)); }
      function addLogItem(item){ const list = loadLog(); list.unshift(item); saveLog(list); renderLog(); }
      function updateLogItem(pos, patch){ const list = loadLog(); if(!list[pos]) return; Object.assign(list[pos], patch); saveLog(list); renderLog(); }

      function isOverdue(it){
        if(!it.deadline) return false;
        const now = new Date(); const d = new Date(it.deadline); // compare dates relative to local timezone
        // set time portion to midnight for fairness
        d.setHours(0,0,0,0); now.setHours(0,0,0,0);
        return d < now && it.status !== 'responded';
      }

      function renderLog(){
        const list = loadLog();
        const q = (searchLog.value||'').toLowerCase();
        logTableBody.innerHTML = '';
        list.forEach((it, idx)=>{
          // compute derived
          const overdue = isOverdue(it);
          let statusLabel = 'Awaiting';
          if(it.status === 'draft') statusLabel = 'Draft';
          if(it.status === 'sent') statusLabel = 'Awaiting';
          if(it.status === 'responded') statusLabel = 'Responded';
          if(overdue) statusLabel = 'Overdue';

          const statusClass = overdue ? 'status-overdue' : (it.status === 'responded' ? 'status-responded' : 'status-awaiting');

          // filters
          if(filter.value === 'awaiting' && it.status !== 'sent') return;
          if(filter.value === 'responded' && it.status !== 'responded') return;
          if(filter.value === 'overdue' && !overdue) return;
          if(q && !( (it.provider||'') + (it.ref||'') ).toLowerCase().includes(q)) return;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${idx+1}</td>
            <td>${it.date || ''}</td>
            <td>${escapeHtml(it.provider || '')}</td>
            <td>${/*escapeHtml(it.department || '')*/ ''}</td>
            <td>${escapeHtml(it.ref || '')}</td>
            <td>${it.deadline || ''}</td>
            <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
            <td class="actions">
              <button data-action="view" data-idx="${idx}" class="btn-ghost small">View</button>
              <button data-action="resend" data-idx="${idx}" class="btn-ghost small">Resend</button>
              <button data-action="mark-responded" data-idx="${idx}" class="btn-ghost small">Mark responded</button>
              <button data-action="delete" data-idx="${idx}" class="btn-ghost small">Delete</button>
            </td>
          `;
          logTableBody.appendChild(tr);
        });
      }

      // Safe text insertion helper
      function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]) }); }

      // Events
      [tplSelect, providerInput, refInput, deadlineInput, bodyInput].forEach(el=>el.addEventListener('input', renderPreview));

      saveBtn.addEventListener('click', ()=>{
        const data = collectForm();
        addLogItem({
          date: new Date().toLocaleDateString(),
          provider: data.provider,
          ref: data.ref,
          deadline: data.deadline || null,
          status: 'draft',
          content: fillTemplate(templates[tplSelect.value], data)
        });
        notify('Saved as draft');
      });

      sendBtn.addEventListener('click', ()=>{
        if(!providerInput.value.trim()){ alert('Please enter the service provider'); providerInput.focus(); return; }
        const data = collectForm();
        // simulate email send: add to log as 'sent'
        addLogItem({
          date: new Date().toLocaleDateString(),
          provider: data.provider,
          ref: data.ref,
          deadline: data.deadline || null,
          status: 'sent',
          content: fillTemplate(templates[tplSelect.value], data)
        });
        notify('Letter recorded as sent (email simulated)');
      });

      printBtn.addEventListener('click', ()=>{ window.print(); });

      clearBtn.addEventListener('click', ()=>{
        if(confirm('Reset form? Unsaved changes will be lost.')) {
          providerInput.value=''; /* deptInput.value=''; */ refInput.value=''; deadlineInput.value=''; bodyInput.value='';
          tplSelect.selectedIndex = 0;
          renderPreview();
        }
      });

      filter.addEventListener('change', renderLog);
      searchLog.addEventListener('input', renderLog);

      // Log table actions (event delegation)
      logTableBody.addEventListener('click', (e)=>{
        const btn = e.target.closest('button'); if(!btn) return;
        const act = btn.dataset.action; const idx = Number(btn.dataset.idx);
        const list = loadLog();
        const item = list[idx];
        if(!item) return;
        if(act === 'view'){ preview.textContent = item.content || '(no content)'; window.scrollTo({top:0,behavior:'smooth'}); }
        if(act === 'resend'){ // simulate resend - add copy with new date
          addLogItem(Object.assign({}, item, { date: new Date().toLocaleDateString(), status:'sent' }));
          notify('Letter resent (simulated)');
        }
        if(act === 'mark-responded'){ updateLogItem(idx, { status: 'responded' }); notify('Marked as responded'); }
        if(act === 'delete'){ if(confirm('Delete this log entry?')){ const l = loadLog(); l.splice(idx,1); saveLog(l); renderLog(); notify('Deleted'); } }
      });

      // Export / Import
      exportBtn.addEventListener('click', ()=>{
        const data = localStorage.getItem(STORAGE_KEY) || '[]';
        const blob = new Blob([data], {type:'application/json'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'investigation_letters_log.json'; document.body.appendChild(a); a.click();
        a.remove(); URL.revokeObjectURL(url);
      });

      importBtn.addEventListener('click', ()=> fileInput.click());
      fileInput.addEventListener('change', (e)=>{
        const f = e.target.files[0]; if(!f) return;
        const reader = new FileReader();
        reader.onload = function(ev){
          try{
            const parsed = JSON.parse(ev.target.result);
            if(!Array.isArray(parsed)) throw new Error('Not an array');
            // replace current log after confirmation
            if(confirm('Replace current log with imported file?')){ saveLog(parsed); renderLog(); notify('Import successful'); }
          } catch(err){ alert('Invalid JSON file'); }
        };
        reader.readAsText(f);
        e.target.value = '';
      });

      // Simple notifications (non-intrusive)
      function notify(msg){
        // small inline notification using alert for simplicity; could be replaced by a toast
        try{ console.log(msg); } catch(e){}
        // show a temporary aria-live message inside preview area
        const t = document.createElement('div'); t.setAttribute('role','status'); t.style.position='absolute'; t.style.left='-9999px'; t.textContent = msg; document.body.appendChild(t);
        setTimeout(()=> t.remove(), 1500);
        alert(msg);
      }

      // Clock in header (small UX nicety)
      function updateClock(){ appClock.textContent = new Date().toLocaleString(); }
      setInterval(updateClock, 1000); updateClock();

      // Initialize
      tplSelect.value = Object.keys(templates)[0];
      renderPreview();
      renderLog();

      // Accessibility: keyboard shortcut (S) to save draft, (Enter) in preview won't submit
      document.addEventListener('keydown', (e)=>{
        if(e.key === 's' && (e.ctrlKey || e.metaKey)){ e.preventDefault(); saveBtn.click(); }
      });

      // Expose small debug API for console (useful during testing)
      window.__investigationApp = { loadLog, saveLog, templates };

    })();
  </script>
</body>
</html>
