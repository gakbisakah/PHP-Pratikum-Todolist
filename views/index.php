<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Todo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0f0f0, #e6e9f0);
            min-height: 100vh;
        }
        .todo-item {
            cursor: move;
        }
        .todo-item.dragging {
            opacity: 0.6;
            background: #f8f9fa;
        }
    </style>
</head>
<body class="p-4">

<div class="container">
    <h1 class="text-center mb-4">📋 Todo List</h1>

    <!-- Alert jika error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Form tambah todo -->
    <form action="?page=create" method="POST" class="card p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="title" class="form-control" placeholder="Judul Todo" required>
            </div>
            <div class="col-md-5">
                <input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Tambah</button>
            </div>
        </div>
    </form>

    <!-- Filter & Pencarian -->
    <form method="GET" class="d-flex justify-content-between mb-3">
        <input type="hidden" name="page" value="index">
        <div>
            <select name="filter" onchange="this.form.submit()" class="form-select d-inline w-auto">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua</option>
                <option value="finished" <?= $filter === 'finished' ? 'selected' : '' ?>>Selesai</option>
                <option value="unfinished" <?= $filter === 'unfinished' ? 'selected' : '' ?>>Belum Selesai</option>
            </select>
        </div>
        <div class="input-group w-50">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari todo..." class="form-control">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <!-- Tabel Todo -->
    <table class="table table-striped table-hover" id="todoTable">
        <thead>
            <tr>
                <th>Urutan</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($todos) > 0): ?>
            <?php foreach ($todos as $todo): ?>
            <tr class="todo-item" draggable="true" data-id="<?= $todo['id'] ?>">
                <td><?= $todo['position'] ?></td>
                <td><?= htmlspecialchars($todo['title']) ?></td>
                <td><?= $todo['is_finished'] ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-danger">Belum</span>' ?></td>
                <td><?= date('d-m-Y H:i', strtotime($todo['created_at'])) ?></td>
                <td>
                    <a href="?page=detail&id=<?= $todo['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                    <a href="?page=update&id=<?= $todo['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?page=delete&id=<?= $todo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus todo ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
const table = document.getElementById("todoTable");
let dragging = null;

table.addEventListener("dragstart", (e) => {
    dragging = e.target;
    e.target.classList.add("dragging");
});
table.addEventListener("dragover", (e) => {
    e.preventDefault();
    const afterElement = getDragAfterElement(table, e.clientY);
    const current = dragging;
    if (afterElement == null) {
        table.querySelector("tbody").appendChild(current);
    } else {
        table.querySelector("tbody").insertBefore(current, afterElement);
    }
});
table.addEventListener("dragend", () => {
    dragging.classList.remove("dragging");
    const ids = [...table.querySelectorAll("tr[data-id]")].map(row => row.dataset.id);
    fetch("?page=sort", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "order[]=" + ids.join("&order[]=")
    });
});
function getDragAfterElement(container, y) {
    const elements = [...container.querySelectorAll("tr.todo-item:not(.dragging)")];
    return elements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) return { offset, element: child };
        else return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}
</script>
</body>
</html>
