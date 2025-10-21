<?php
// pastikan session started di public/index.php
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Todo List — Aplikasi</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      tbody tr.dragging { opacity: 0.5; }
      tbody tr.drag-over { border: 2px dashed #007bff; }
      .todo-card { cursor: grab; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Todo List</h1>
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">+ Tambah</button>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- filter + search -->
    <form class="row g-2 mb-3" method="GET" action="../public/index.php">
        <input type="hidden" name="page" value="index">
        <div class="col-md-3">
            <select name="filter" class="form-select">
                <option value="all" <?= ($filter_active ?? 'all') === 'all' ? 'selected' : '' ?>>Semua</option>
                <option value="unfinished" <?= ($filter_active ?? '') === 'unfinished' ? 'selected' : '' ?>>Belum Selesai</option>
                <option value="finished" <?= ($filter_active ?? '') === 'finished' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-6">
            <input name="search" class="form-control" placeholder="Cari (judul/deskripsi)..." value="<?= htmlspecialchars($search_q ?? '') ?>">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Terapkan</button>
            <a href="../public/index.php?page=index" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0" id="todoTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th style="width:220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($todos)): ?>
                    <?php foreach ($todos as $i => $t): ?>
                    <tr draggable="true" data-id="<?= $t['id'] ?>" class="todo-card">
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($t['title']) ?></td>
                        <td>
                            <?php if ($t['is_finished']): ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Belum</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                        <td>
                            <a href="../public/index.php?page=detail&id=<?= $t['id'] ?>" class="btn btn-sm btn-info">Detail</a>

                            <button class="btn btn-sm btn-warning"
                                onclick="openEdit(<?= $t['id'] ?>, '<?= addslashes(htmlspecialchars($t['title'])) ?>', '<?= addslashes(htmlspecialchars($t['description'] ?? '')) ?>', <?= $t['is_finished'] ? 1 : 0 ?>)">
                                Edit
                            </button>

                            <a href="../public/index.php?page=delete&id=<?= $t['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus todo ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada todo sesuai filter.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalAdd" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../public/index.php?page=create" method="POST">
        <div class="modal-header"><h5 class="modal-title">Tambah Todo</h5></div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Judul</label>
                <input name="title" class="form-control" required maxlength="250">
            </div>
            <div class="mb-2">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../public/index.php?page=update" method="POST">
        <input type="hidden" name="id" id="editId">
        <div class="modal-header"><h5 class="modal-title">Edit Todo</h5></div>
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label">Judul</label>
                <input name="title" id="editTitle" class="form-control" required maxlength="250">
            </div>
            <div class="mb-2">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="editDescription" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_finished" id="editFinished" class="form-check-input">
                <label class="form-check-label" for="editFinished">Tandai sebagai selesai</label>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEdit(id, title, description, status) {
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editFinished').checked = status ? true : false;
    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
    myModal.show();
}

// Drag & drop save order
let dragSrc = null;
document.querySelectorAll('#todoTable tbody tr[draggable="true"]').forEach(row => {
    row.addEventListener('dragstart', e => {
        dragSrc = row;
        row.classList.add('dragging');
    });
    row.addEventListener('dragover', e => {
        e.preventDefault();
        row.classList.add('drag-over');
    });
    row.addEventListener('dragleave', e => row.classList.remove('drag-over'));
    row.addEventListener('drop', e => {
        e.preventDefault();
        row.classList.remove('drag-over');
        if (dragSrc && dragSrc !== row) {
            const tbody = row.parentNode;
            tbody.insertBefore(dragSrc, row.nextSibling);
            saveOrder();
        }
    });
    row.addEventListener('dragend', e => row.classList.remove('dragging'));
});

function saveOrder() {
    const positions = {};
    document.querySelectorAll('#todoTable tbody tr').forEach((r, idx) => {
        const id = r.dataset.id;
        positions[id] = idx + 1;
    });

    fetch('../public/index.php?page=sort', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(positions)
    }).then(res => res.json()).then(data => {
        if (!data.success) {
            alert('Gagal menyimpan urutan');
        }
    }).catch(() => alert('Gagal menyimpan urutan (network)'));
}
</script>
</body>
</html>
