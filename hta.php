<?php
// htaccess_disabler.php
$SECRET_KEY = "3"; // Wajib diubah!

// 1. Autentikasi
if (($_GET['key'] ?? '') !== $SECRET_KEY) {
    header('HTTP/1.1 403 Forbidden');
    die("Akses ditolak!");
}

// 2. Cari .htaccess di root website
function disableHtaccess() {
    // Method 1: Gunakan DOCUMENT_ROOT (paling akurat)
    $root = $_SERVER['DOCUMENT_ROOT'] ?? dirname(getcwd(), 2); // Fallback
    $htaccess = "$root/.htaccess";
    
    // Method 2: Cari parent folder (jika DOCUMENT_ROOT tidak ada)
    if (!file_exists($htaccess)) {
        $commonRoots = ['public_html', 'html', 'www', 'htdocs'];
        foreach ($commonRoots as $dir) {
            $path = dirname($root) . "/$dir/.htaccess";
            if (file_exists($path)) {
                $htaccess = $path;
                break;
            }
        }
    }

    // 3. Nonaktifkan dengan rename
    if (file_exists($htaccess)) {
        $backup = "$htaccess.bak_" . date('YmdHis');
        if (rename($htaccess, $backup)) {
            return "SUKSES: .htaccess dinonaktifkan (direname ke " . basename($backup) . ")";
        }
        return "GAGAL: Tidak bisa rename file";
    }
    return "ERROR: .htaccess tidak ditemukan di:<br>- $root<br>- " . implode('<br>- ', $commonRoots);
}

// 4. Eksekusi
header('Content-Type: text/html; charset=utf-8');
echo "<pre>";
echo disableHtaccess();
echo "\n\n" . 'Script ini akan menghapus dirinya sendiri...';
unlink(__FILE__); // Hapus otomatis setelah digunakan

?>
