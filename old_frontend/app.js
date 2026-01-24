// App logic for AIU-MMS demo (frontend only)
(function(){
  // simple auth check against SAMPLE_USERS from data.js
  function findUser(username, password, role){
    return SAMPLE_USERS.find(u => u.username === username && u.password === password && u.role === role);
  }

  // login form on index.html
  const lf = document.getElementById('loginForm');
  if(lf){
    lf.addEventListener('submit', (e)=>{
      e.preventDefault();
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value.trim();
      const role = document.getElementById('role').value;
      const user = findUser(username,password,role);
      const msg = document.getElementById('loginMsg');
      if(!user){ msg.textContent = 'Invalid credentials for selected role.'; return; }
      // store current user in sessionStorage (demo only)
      sessionStorage.setItem('aiu_user', JSON.stringify(user));
      if(role==='admin') location.href = 'admin.html'; else location.href = 'worker.html';
    });
  }

  // Common: logout buttons
  const logout = document.getElementById('logoutBtn');
  if(logout) logout.addEventListener('click', ()=>{ sessionStorage.removeItem('aiu_user'); location.href='index.html'; });
  const logoutW = document.getElementById('logoutBtnW');
  if(logoutW) logoutW.addEventListener('click', ()=>{ sessionStorage.removeItem('aiu_user'); location.href='index.html'; });

  // Admin page init
  if(document.body.contains(document.getElementById('k_totalTasks'))){
    const user = JSON.parse(sessionStorage.getItem('aiu_user')||'null');
    if(!user || user.role !== 'admin'){ location.href = 'index.html'; return; }
    document.getElementById('adminName').textContent = user.name || 'Admin';
    // populate KPIs
    document.getElementById('k_totalTasks').textContent = SAMPLE_TASKS.length;
    document.getElementById('k_workers').textContent = SAMPLE_WORKERS.length;
    document.getElementById('k_completed').textContent = SAMPLE_TASKS.filter(t=>t.status==='completed').length;
    // equipment list
    const eq = document.getElementById('equipList');
    SAMPLE_EQUIPMENT.forEach(e=>{
      const d = document.createElement('div'); d.className='small-muted'; d.textContent = `${e.type} — ${e.location} — ${e.status}`; eq.appendChild(d);
    });
    // notifications for admin
    const an = document.getElementById('adminNotifs');
    SAMPLE_NOTIFICATIONS.filter(n=>n.role==='admin').forEach(n=>{ const x = document.createElement('div'); x.className='note small'; x.textContent = `${n.title}: ${n.message}`; an.appendChild(x); });
    // tasks list + assign dropdown
    const assign = document.getElementById('assignTo');
    SAMPLE_WORKERS.forEach(w=>{ const o = document.createElement('option'); o.value = w.id; o.textContent = w.name; assign.appendChild(o); });
    const tl = document.getElementById('taskList');
    function renderTasks(){ tl.innerHTML=''; SAMPLE_TASKS.forEach(t=>{
      const el = document.createElement('div'); el.className='task';
      el.innerHTML = `<div style="display:flex;justify-content:space-between"><strong>${t.id} — ${t.title}</strong><span class="badge">${t.status}</span></div><div class="meta">Due: ${t.due} | Assigned: ${t.assignedTo} | Priority: ${t.priority}</div><div style="margin-top:6px"><button data-id="${t.id}" class="btn" onclick="markDone('${t.id}')">Mark Done</button></div>`;
      tl.appendChild(el);
    }); }
    window.markDone = function(id){ const task = SAMPLE_TASKS.find(t=>t.id===id); if(task){ task.status='completed'; renderTasks(); alert('Task marked completed (demo)'); } }
    renderTasks();
    document.getElementById('createTask').addEventListener('click', ()=>{
      const title = document.getElementById('newTaskTitle').value.trim(); const to = document.getElementById('assignTo').value;
      if(!title){ alert('Provide title'); return; }
      const nid = 'T-'+(Math.floor(Math.random()*900)+100);
      SAMPLE_TASKS.push({ id:nid, title, due: new Date().toISOString().slice(0,10), equipmentId:null, assignedTo:to, status:'pending', priority:'medium' });
      document.getElementById('newTaskTitle').value=''; renderTasks(); alert('Task created and assigned (demo)');
    });
    // workers table
    const wt = document.querySelector('#workersTable tbody'); wt.innerHTML=''; SAMPLE_WORKERS.forEach(w=>{ const r=document.createElement('tr'); r.innerHTML = `<td>${w.name}</td><td>${w.trade}</td><td>${w.phone}</td><td>${w.tasksAssigned}</td>`; wt.appendChild(r); });
    // inventory list + alerts
    const inv = document.getElementById('inventoryList');
    SAMPLE_INVENTORY.forEach(i=>{
      const d = document.createElement('div');
      d.className = 'small-muted';
      if(i.qty <= i.minQty){
        d.innerHTML = `${i.name} — Qty: ${i.qty} <span style="color:#ef4444;font-weight:700;margin-left:8px">LOW</span>`;
      } else {
        d.textContent = `${i.name} — Qty: ${i.qty}`;
      }
      inv.appendChild(d);
    });
    // reports generator
    document.getElementById('genReport').addEventListener('click', ()=>{
      const total = SAMPLE_TASKS.length; const completed = SAMPLE_TASKS.filter(t=>t.status==='completed').length; const rate = ((completed/total)*100).toFixed(1)+'%';
      const out = `Tasks: ${total}\nCompleted: ${completed}\nCompletion Rate: ${rate}\nWorkers: ${SAMPLE_WORKERS.length}`;
      document.getElementById('reportOut').textContent = out;
    });
  }

  // Worker page init
  if(document.body.contains(document.getElementById('workerTasks'))){
    const user = JSON.parse(sessionStorage.getItem('aiu_user')||'null');
    if(!user || user.role !== 'worker'){ location.href = 'index.html'; return; }
    document.getElementById('workerName').textContent = user.name || user.username;
    document.getElementById('workerAvatar').textContent = user.avatar || 'W';
  // tasks for this worker
  const wt = document.getElementById('workerTasks');
    function renderWorkerTasks(){ wt.innerHTML=''; SAMPLE_TASKS.filter(t=>t.assignedTo===user.id).forEach(t=>{
      const d = document.createElement('div'); d.className='task'; d.innerHTML = `<div style="display:flex;justify-content:space-between"><strong>${t.title}</strong><span class="badge">${t.status}</span></div><div class="meta">Due: ${t.due} | Priority: ${t.priority}</div><div style="margin-top:8px"><button class="btn" onclick="startTask('${t.id}')">Start</button> <button class="btn" onclick="completeTask('${t.id}')">Complete</button></div>`; wt.appendChild(d);
    }); }
    window.startTask = function(id){ const t = SAMPLE_TASKS.find(x=>x.id===id); if(t){ t.status='in-progress'; renderWorkerTasks(); showNotif('Task started', 'You started task '+id); } }
    window.completeTask = function(id){ const t = SAMPLE_TASKS.find(x=>x.id===id); if(t){ t.status='completed'; renderWorkerTasks(); showNotif('Task completed', 'You completed '+id); } }
    renderWorkerTasks();
    // schedule view (simple list) 
    const sv = document.getElementById('scheduleView');
    document.getElementById('viewWeek').addEventListener('click', ()=>{ sv.innerHTML = '<div class="small-muted">Weekly schedule (mock)</div>'; SAMPLE_TASKS.filter(t=>t.assignedTo===user.id).forEach(t=>{ const x=document.createElement('div'); x.className='small-muted'; x.textContent = `${t.due} — ${t.title}`; sv.appendChild(x); }) });
    document.getElementById('viewMonth').addEventListener('click', ()=>{ sv.innerHTML = '<div class="small-muted">Monthly schedule (mock)</div>'; SAMPLE_TASKS.filter(t=>t.assignedTo===user.id).forEach(t=>{ const x=document.createElement('div'); x.className='small-muted'; x.textContent = `${t.due} — ${t.title}`; sv.appendChild(x); }) });
    // mock upload
    document.getElementById('uploadReport').addEventListener('click', ()=>{
      const f = document.getElementById('mockFile'); const msg = document.getElementById('uploadMsg');
      if(!f.files || f.files.length===0){ msg.textContent = 'No file selected (mock)'; return; }
      msg.textContent = `Uploaded ${f.files[0].name} (mock)`; setTimeout(()=>{ msg.textContent = 'Report submitted for review'; },800);
    });
    // notifications area
    const na = document.getElementById('notifArea');
    function renderNotifs(){ na.innerHTML=''; SAMPLE_NOTIFICATIONS.filter(n=>n.role==='worker').forEach(n=>{ const el = document.createElement('div'); el.className='note'; el.textContent = `${n.title} — ${n.message}`; na.appendChild(el); setTimeout(()=>{ el.remove(); },7000); }); }
    renderNotifs();
    window.showNotif = function(title, msg){ const el = document.createElement('div'); el.className='note'; el.textContent = `${title} — ${msg}`; na.appendChild(el); setTimeout(()=> el.remove(),6000); }
  }
})();
