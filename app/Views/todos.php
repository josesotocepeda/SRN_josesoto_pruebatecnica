<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>To-Do CI4 + API</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: #f6f7fb; }
    .card { border: 0; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,.05); }
    .done { text-decoration: line-through; opacity:.6; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">To-Do List</h1>
    <button id="btnNew" class="btn btn-primary">Nueva tarea</button>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row g-2 mb-3">
        <div class="col-sm-6">
          <input id="search" class="form-control" placeholder="Buscar por título...">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:60px">#</th>
              <th>Título</th>
              <th style="width:120px">Estado</th>
              <th style="width:200px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="taskForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="taskModalTitle">Nueva tarea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="taskId">
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input id="title" class="form-control" required maxlength="255">
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="completed">
          <label class="form-check-label" for="completed">Completada</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap 5 JS (no jQuery necesario) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const API = '/api/tasks';
let tasks = [];
const tbody = document.getElementById('tbody');
const modalEl = document.getElementById('taskModal');
const bsModal = new bootstrap.Modal(modalEl);

document.getElementById('btnNew').addEventListener('click', () => openModal());
document.getElementById('search').addEventListener('input', render);

document.getElementById('taskForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const id = document.getElementById('taskId').value;
  const payload = getFormData();
  // console.log(id);
  // console.log(payload);

  try {
    if (id) {
      await fetch(`${API}/${id}`, {
        method: 'PUT',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
    } else {
      await fetch(API, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
    }
    bsModal.hide();
    await loadTasks();
  } catch (err) { alert('Error: ' + err.message); }
});

function getFormData() {
  return {
    title: document.getElementById('title').value.trim(),
    completed: document.getElementById('completed').checked ? 1 : 0
  }
}

function openModal(task = null) {
  document.getElementById('taskForm').reset();
  document.getElementById('taskId').value = task ? task.id : '';
  document.getElementById('title').value = task ? task.title : '';
  document.getElementById('completed').checked = task ? (Number(task.completed) === 1) : false;
  document.getElementById('taskModalTitle').textContent = task ? 'Editar tarea' : 'Nueva tarea';
  bsModal.show();
}

async function loadTasks() {
  const res = await fetch(API);
  tasks = await res.json();
  render();
}

function render() {
  const q = document.getElementById('search').value.toLowerCase();
  const filtered = tasks.filter(t => t.title.toLowerCase().includes(q));
  tbody.innerHTML = filtered.map(t => rowTemplate(t)).join('');
}

function rowTemplate(t) {
  const done = Number(t.completed) === 1;
  return `
    <tr>
      <td>${t.id}</td>
      <td class="${done ? 'done' : ''}">${escapeHtml(t.title)}</td>
      <td>
        <span class="badge ${done ? 'bg-success' : 'bg-secondary'}">${done ? 'Hecha' : 'Pendiente'}</span>
      </td>
      <td>
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-secondary" onclick='toggleDone(${t.id}, ${done ? 0 : 1})'>${done ? 'Marcar pendiente' : 'Marcar hecha'}</button>
          <button class="btn btn-sm btn-outline-primary" onclick='editTask(${t.id})'>Editar</button>
          <button class="btn btn-sm btn-outline-danger" onclick='deleteTask(${t.id})'>Eliminar</button>
        </div>
      </td>
    </tr>
  `;
}

function escapeHtml(s){ return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])) }

async function editTask(id) {
  const t = tasks.find(x => x.id == id);
  // console.log(id);
  // if (!t) return;
  openModal(t);
}

async function deleteTask(id) {
  if (!confirm('¿Eliminar la tarea #' + id + '?')) return;
  await fetch(`${API}/${id}`, { method: 'DELETE' });
  await loadTasks();
}

async function toggleDone(id, value) {
  await fetch(`${API}/${id}`, {
    method: 'PUT',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ completed: value })
  });
  await loadTasks();
}

loadTasks();
</script>
</body>
</html>
