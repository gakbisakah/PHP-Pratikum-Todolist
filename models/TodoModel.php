<?php
require_once(__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        try {
            $this->conn = new PDO($dsn, DB_USER, DB_PASSWORD);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi gagal: " . $e->getMessage());
        }
    }

    // Ambil semua todo (filter + search) — urut berdasarkan position
    public function getAll($filter = 'all', $search = '')
    {
        $query = "SELECT * FROM todo WHERE 1=1";
        $params = [];

        if ($filter === 'finished') {
            $query .= " AND is_finished = TRUE";
        } elseif ($filter === 'unfinished') {
            $query .= " AND is_finished = FALSE";
        }

        if (!empty($search)) {
            $query .= " AND (LOWER(title) LIKE LOWER(:search) OR LOWER(description) LIKE LOWER(:search))";
            $params[':search'] = "%$search%";
        }

        // <-- gunakan `position` sesuai struktur DB-mu
        $query .= " ORDER BY position ASC, created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM todo WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($title, $description)
    {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM todo WHERE LOWER(title) = LOWER(:title)");
        $check->execute([':title' => $title]);
        if ($check->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->conn->prepare("INSERT INTO todo (title, description, is_finished, created_at, updated_at, position)
            VALUES (:title, :description, FALSE, NOW(), NOW(), (SELECT COALESCE(MAX(position),0)+1 FROM todo))");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description
        ]);
    }

    public function update($id, $title, $description, $is_finished)
    {
        $is_finished = ($is_finished === '1' || $is_finished === true || $is_finished === 1) ? true : false;

        $stmt = $this->conn->prepare("UPDATE todo 
            SET title = :title, description = :description, is_finished = :is_finished, updated_at = NOW()
            WHERE id = :id");
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':is_finished' => $is_finished,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM todo WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // perbarui posisi dari array order (array berisi id berurut)
    public function sort($orders)
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("UPDATE todo SET position = :pos WHERE id = :id");
            foreach ($orders as $index => $id) {
                $stmt->execute([':pos' => $index + 1, ':id' => $id]);
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
