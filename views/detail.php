<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Todo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #333;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        p {
            line-height: 1.5;
            font-size: 14px;
        }

        .info {
            margin: 8px 0;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
        }

        .done {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .pending {
            background: #ffe0b2;
            color: #ef6c00;
        }

        .description {
            background: #f8f8f8;
            border-left: 3px solid #1565c0;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #1565c0;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php if ($todo): ?>
            <h2><?= htmlspecialchars($todo['title']) ?></h2>

            <div class="info">
                <strong>Status:</strong>
                <span class="status <?= $todo['is_finished'] ? 'done' : 'pending' ?>">
                    <?= $todo['is_finished'] ? 'Selesai' : 'Belum Selesai' ?>
                </span>
            </div>

            <div class="info">
                <strong>Dibuat:</strong> <?= date('d M Y, H:i', strtotime($todo['created_at'])) ?>
            </div>

            <div class="description">
                <?= nl2br(htmlspecialchars($todo['description'])) ?>
            </div>

        <?php else: ?>
            <h2>Todo Tidak Ditemukan</h2>
            <p>Data todo yang kamu cari tidak tersedia.</p>
        <?php endif; ?>

        <a href="index.php" class="back-link">← Kembali ke Daftar</a>
    </div>

</body>
</html>
