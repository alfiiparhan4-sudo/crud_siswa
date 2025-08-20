<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'siswa_db';

$conn = new mysqli($host, $user, $pass, $db);

// Buat database jika belum ada
$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);

// Buat tabel siswa jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Fungsi untuk CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $nis = $conn->real_escape_string($_POST['nis']);
        $nama = $conn->real_escape_string($_POST['nama']);
        $kelas = $conn->real_escape_string($_POST['kelas']);
        $alamat = $conn->real_escape_string($_POST['alamat']);
        
        $sql = "INSERT INTO siswa (nis, nama, kelas, alamat) VALUES ('$nis', '$nama', '$kelas', '$alamat')";
        $conn->query($sql);
    } elseif (isset($_POST['update'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $nis = $conn->real_escape_string($_POST['nis']);
        $nama = $conn->real_escape_string($_POST['nama']);
        $kelas = $conn->real_escape_string($_POST['kelas']);
        $alamat = $conn->real_escape_string($_POST['alamat']);
        
        $sql = "UPDATE siswa SET nis='$nis', nama='$nama', kelas='$kelas', alamat='$alamat' WHERE id=$id";
        $conn->query($sql);
    }
}

if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    $sql = "DELETE FROM siswa WHERE id=$id";
    $conn->query($sql);
}

// Ambil data siswa
$result = $conn->query("SELECT * FROM siswa ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-input {
            @apply w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
        .btn {
            @apply px-4 py-2 rounded-md font-medium transition duration-200;
        }
        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
        .btn-danger {
            @apply bg-red-600 text-white hover:bg-red-700;
        }
        .btn-secondary {
            @apply bg-gray-600 text-white hover:bg-gray-700;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-800">Data Siswa</h1>
        
        <!-- Form Tambah/Edit Data -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">
                <?= isset($_GET['edit']) ? 'Edit Data Siswa' : 'Tambah Data Siswa' ?>
            </h2>
            
            <form method="POST">
                <?php if (isset($_GET['edit'])): ?>
                    <?php 
                        $edit = $conn->query("SELECT * FROM siswa WHERE id=".$_GET['edit']);
                        $row = $edit->fetch_assoc();
                    ?>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">NIS</label>
                        <input type="text" name="nis" class="form-input" 
                               value="<?= isset($row) ? $row['nis'] : '' ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-input" 
                               value="<?= isset($row) ? $row['nama'] : '' ?>" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Kelas</label>
                        <input type="text" name="kelas" class="form-input" 
                               value="<?= isset($row) ? $row['kelas'] : '' ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block mb-2 font-medium">Alamat</label>
                        <input type="text" name="alamat" class="form-input" 
                               value="<?= isset($row) ? $row['alamat'] : '' ?>">
                    </div>
                </div>
                
                <div class="flex justify-end mt-4">
                    <?php if (isset($_GET['edit'])): ?>
                        <button type="submit" name="update" class="btn btn-primary">Update Data</button>
                        <a href="?" class="btn btn-secondary ml-2">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-primary">Tambah Data</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Daftar Siswa -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Daftar Siswa</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while($siswa = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $siswa['nis'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $siswa['nama'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $siswa['kelas'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $siswa['alamat'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="?edit=<?= $siswa['id'] ?>" class="btn btn-primary text-sm">Edit</a>
                                <a href="?delete=<?= $siswa['id'] ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus?')" 
                                   class="btn btn-danger text-sm ml-2">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
