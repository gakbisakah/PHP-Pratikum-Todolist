<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Todo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1b5e20;
        }

        .back-btn {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #1565c0;
            text-decoration: none;
            font-size: 14px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Todo</h2>
        <form method="POST" action="index.php?page=update&id=<?= $todo['id'] ?>">
            <input type="hidden" name="id" value="<?= $todo['id'] ?>">

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($todo['title']) ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?= htmlspecialchars($todo['description']) ?></textarea>

            <label for="is_finished">Status:</label>
            <select id="is_finished" name="is_finished">
                <option value="false" <?= !$todo['is_finished'] ? 'selected' : '' ?>>Belum Selesai</option>
                <option value="true" <?= $todo['is_finished'] ? 'selected' : '' ?>>Selesai</option>
            </select>

            <button type="submit">Update Todo</button>
        </form>

        <a href="index.php" class="back-btn">← Kembali ke Daftar</a>
    </div>

</body>
</html>
