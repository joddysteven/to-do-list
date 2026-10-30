<?php
// =====================================================
// To-Do List Sederhana
// Lokasi proyek : htdocs/todolist/index.php
// Konsep        : Pemrograman terstruktur + Array of objects
// =====================================================
session_start();

/* ---------- 1. DATA (Array) ---------- */
function inisialisasiTugas()
{
    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [
            ["id" => 1, "title" => "Belajar PHP",        "status" => "belum"],
            ["id" => 2, "title" => "Kerjakan tugas UX", "status" => "selesai"],
        ];
    }
}

/* ---------- 2. FUNGSI-FUNGSI ---------- */
function ambilTugas()
{
    return $_SESSION['tasks'];
}

function idBerikutnya($tasks)
{
    $max = 0;
    foreach ($tasks as $task) {
        if ($task['id'] > $max) {
            $max = $task['id'];
        }
    }
    return $max + 1;
}

function tambahTugas($title)
{
    $title = trim($title);
    if ($title === "") {
        return false;
    }
    $tasks = ambilTugas();
    $tasks[] = [
        "id"     => idBerikutnya($tasks),
        "title"  => htmlspecialchars($title),
        "status" => "belum",
    ];
    $_SESSION['tasks'] = $tasks;
    return true;
}

function ubahStatus($id)
{
    foreach ($_SESSION['tasks'] as $i => $task) {
        if ($task['id'] == $id) {
            $_SESSION['tasks'][$i]['status'] =
                ($task['status'] === "belum") ? "selesai" : "belum";
        }
    }
}

function hapusTugas($id)
{
    $baru = [];
    foreach ($_SESSION['tasks'] as $task) {
        if ($task['id'] != $id) {
            $baru[] = $task;
        }
    }
    $_SESSION['tasks'] = $baru;
}

/* ---------- 3. PROSES UTAMA ---------- */
inisialisasiTugas();
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    if (tambahTugas($_POST['title'])) {
        $pesan = "Tugas berhasil ditambahkan.";
    } else {
        $pesan = "Judul tugas tidak boleh kosong.";
    }
}

if (isset($_GET['toggle'])) {
    ubahStatus((int) $_GET['toggle']);
    header("Location: index.php");
    exit;
}

if (isset($_GET['hapus'])) {
    hapusTugas((int) $_GET['hapus']);
    header("Location: index.php");
    exit;
}

$tasks = ambilTugas();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- HEADER -->
<header class="bg-primary text-white text-center py-4 mb-4">
    <h1 class="h3 mb-0">Aplikasi To-Do List</h1>
    <small>Kelola tugas harian Anda</small>
</header>

<main class="container" style="max-width: 700px;">

    <!-- FORM TAMBAH TUGAS -->
    <section class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5">Tambah Tugas</h2>
            <?php if ($pesan !== ""): ?>
                <div class="alert alert-info py-2"><?= $pesan ?></div>
            <?php endif; ?>
            <form method="post" class="d-flex gap-2">
                <input type="text" name="title" class="form-control"
                       placeholder="Tulis tugas baru..." required>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </section>

    <!-- DAFTAR TUGAS -->
    <section class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5">Daftar Tugas</h2>

            <?php if (count($tasks) === 0): ?>
                <p class="text-muted mb-0">Belum ada tugas.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($tasks as $task): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="<?= $task['status'] === 'selesai' ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= $task['id'] ?>. <?= $task['title'] ?>
                            </span>
                            <span>
                                <span class="badge <?= $task['status'] === 'selesai' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= ucfirst($task['status']) ?>
                                </span>
                                <a href="?toggle=<?= $task['id'] ?>" class="btn btn-sm btn-outline-secondary ms-2">Ubah</a>
                                <a href="?hapus=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Hapus tugas ini?')">Hapus</a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>

</main>
</body>
</html>