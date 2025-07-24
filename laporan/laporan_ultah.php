<?php
require_once '../vendor/autoload.php';
require_once '../database.php';

// Ambil data ulang tahun 7 hari ke depan
$ulangTahun = query("
    SELECT nama_lengkap, tanggal_lahir 
    FROM jemaat 
    WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') 
    BETWEEN DATE_FORMAT(NOW(), '%m-%d') 
    AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY), '%m-%d')
    ORDER BY DAYOFYEAR(tanggal_lahir)
");

function hariIndo($tanggal)
{
    $hariInggris = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    return $hariIndonesia[$hariInggris] ?? $hariInggris;
}

function hitungUmur($tanggalLahir)
{
    $birthDate = new DateTime($tanggalLahir);
    $today = new DateTime();
    return $today->diff($birthDate)->y;
}

// Layout HTML
ob_start();
?>

<h3 style="text-align: center; margin-bottom: 10px;">Daftar Jemaat Ulang Tahun Minggu Ini</h3>
<p style="text-align: center;">Tanggal Cetak: <?= date('d-m-Y') ?></p>

<table width="100%" cellspacing="0" cellpadding="6" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th style="border-bottom: 1px solid #000;">No</th>
            <th style="border-bottom: 1px solid #000;">Nama Lengkap</th>
            <th style="border-bottom: 1px solid #000;">Tanggal Lahir</th>
            <th style="border-bottom: 1px solid #000;">Hari</th>
            <th style="border-bottom: 1px solid #000;">Umur</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($ulangTahun) > 0): ?>
            <?php $no = 1;
            foreach ($ulangTahun as $j): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($j['nama_lengkap']) ?></td>
                    <td><?= date('d M Y', strtotime($j['tanggal_lahir'])) ?></td>
                    <td><?= hariIndo($j['tanggal_lahir']) ?></td>
                    <td><?= hitungUmur($j['tanggal_lahir']) ?> tahun</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada jemaat yang ulang tahun minggu ini.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$html = ob_get_clean();

// Output PDF
$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'orientation' => 'P'
]);
$mpdf->WriteHTML($html);
$mpdf->Output('laporan_ultah.pdf', 'I');
