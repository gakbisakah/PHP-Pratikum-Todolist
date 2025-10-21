<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    private $model;

    public function __construct()
    {
        $this->model = new TodoModel();
    }

    public function index()
    {
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $todos = $this->model->getAll($filter, $search);
        include(__DIR__ . '/../views/index.php');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            if (!$this->model->create($title, $description)) {
                $_SESSION['error'] = "Judul sudah ada! Gunakan judul lain.";
            }
        }
        header("Location: index.php");
        exit;
    }

public function update()
{
    $id = $_GET['id'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
        $todo = $this->model->find($id);
        include(__DIR__ . '/../views/edit.php');
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        // ✅ Pastikan hasilnya boolean, bukan string kosong
        $is_finished = isset($_POST['is_finished']) ? true : false;

        $this->model->update($id, $title, $description, $is_finished);
        header("Location: index.php");
        exit;
    }
}



    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->model->delete($_GET['id']);
        }
        header("Location: index.php");
        exit;
    }

    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $todo = $this->model->find($id);
            include(__DIR__ . '/../views/detail.php');
        }
    }

    public function sort()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = $_POST['order'] ?? [];
            $this->model->sort($order);
        }
    }
}
