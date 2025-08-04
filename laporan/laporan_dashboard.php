<?php
require_once '../vendor/autoload.php';
require_once '../database.php';

$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

// Utility Functions
function hariIndo($tanggal)
{
    $map = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    return $map[date('l', strtotime($tanggal))] ?? $tanggal;
}

function hitungUmur($tanggalLahir)
{
    $birthDate = new DateTime($tanggalLahir);
    return (new DateTime())->diff($birthDate)->y;
}

function hitungLamaMenikah($tanggalNikah)
{
    $mulai = new DateTime($tanggalNikah);
    $sekarang = new DateTime();
    return $sekarang->diff($mulai)->y;
}

// Query ulang tahun jemaat minggu ini
$ulangTahun = query("SELECT nama_lengkap, tanggal_lahir FROM jemaat 
    WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') BETWEEN DATE_FORMAT('$monday', '%m-%d') AND DATE_FORMAT('$sunday', '%m-%d') 
    ORDER BY tanggal_lahir");

// Query anniversary nikah minggu ini
$anniversary = query("SELECT 
        j1.nama_lengkap AS nama_suami,
        j2.nama_lengkap AS nama_istri,
        p.tanggal_nikah
    FROM pernikahan p
    LEFT JOIN jemaat j1 ON p.id_suami = j1.id_jemaat
    LEFT JOIN jemaat j2 ON p.id_istri = j2.id_jemaat
    WHERE DATE_FORMAT(p.tanggal_nikah, '%m-%d') BETWEEN DATE_FORMAT('$monday', '%m-%d') AND DATE_FORMAT('$sunday', '%m-%d')
    ORDER BY p.tanggal_nikah");

// Data baru minggu ini
$dataBaru = [
    'jemaat' => query("SELECT nama_lengkap AS nama, created_at FROM jemaat WHERE created_at BETWEEN '$monday' AND '$sunday'"),
    'keluarga' => query("SELECT nama_keluarga AS nama, created_at FROM keluarga WHERE created_at BETWEEN '$monday' AND '$sunday'"),
    'rayon' => query("SELECT nama_rayon AS nama, created_at FROM rayon WHERE created_at BETWEEN '$monday' AND '$sunday'"),

    'atestasi' => query("
        SELECT j.nama_lengkap AS nama, a.created_at 
        FROM atestasi a
        JOIN jemaat j ON a.id_jemaat = j.id_jemaat
        WHERE a.created_at BETWEEN '$monday' AND '$sunday'
    "),
    'meninggal' => query("
        SELECT j.nama_lengkap AS nama, m.created_at 
        FROM meninggal m
        JOIN jemaat j ON m.id_jemaat = j.id_jemaat
        WHERE m.created_at BETWEEN '$monday' AND '$sunday'
    "),
    'pernikahan' => query("
        SELECT 
            CONCAT(
                COALESCE(j1.nama_lengkap, '-'), 
                ' & ', 
                COALESCE(j2.nama_lengkap, '-'), 
                ' (', DATE_FORMAT(p.tanggal_nikah, '%d %M %Y'), ')'
            ) AS nama, 
            p.created_at 
        FROM pernikahan p
        LEFT JOIN jemaat j1 ON p.id_suami = j1.id_jemaat
        LEFT JOIN jemaat j2 ON p.id_istri = j2.id_jemaat
        WHERE p.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY p.created_at DESC
    "),

    'baptisan' => query("
        SELECT j.nama_lengkap AS nama, b.created_at 
        FROM baptisan b
        JOIN jemaat j ON b.id_jemaat = j.id_jemaat
        WHERE b.created_at BETWEEN '$monday' AND '$sunday'
    "),
    'sidi' => query("
        SELECT j.nama_lengkap AS nama, s.created_at 
        FROM sidi s
        JOIN jemaat j ON s.id_jemaat = j.id_jemaat
        WHERE s.created_at BETWEEN '$monday' AND '$sunday'
    "),
];

// Check if any new data exists
$hasNewData = false;
foreach ($dataBaru as $items) {
    if (!empty($items)) {
        $hasNewData = true;
        break;
    }
}

ob_start();
?>

<style>
    body {
        font-family: sans-serif;
        font-size: 11pt;
    }

    h3,
    h4 {
        text-align: center;
        margin-bottom: 0.5rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
        text-align: left;
    }

    th {
        background-color: #eee;
    }

    ul {
        padding-left: 1.2rem;
    }
</style>

<h3>Informasi Minggu Ini</h3>
<p style="text-align: center; font-style: italic; margin-top: 0; margin-bottom: 1rem;">
    Periode: <?= date('d M Y', strtotime($monday)) ?> - <?= date('d M Y', strtotime($sunday)) ?>
</p>

<h4>Ulang Tahun</h4>
<?php if (count($ulangTahun) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Tanggal Lahir</th>
                <th>Hari</th>
                <th>Umur</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>
<?php else: ?>
    <p><em>Tidak ada ulang tahun minggu ini.</em></p>
<?php endif; ?>

<h4 style="margin-top: 2rem;">Ulang Tahun Pernikahan</h4>
<?php if (count($anniversary) > 0): ?>
    <ul>
        <?php foreach ($anniversary as $a): ?>
            <li>
                <?= htmlspecialchars($a['nama_suami']) ?> & <?= htmlspecialchars($a['nama_istri']) ?> -
                <?= hariIndo($a['tanggal_nikah']) ?> (<?= date('d M Y', strtotime($a['tanggal_nikah'])) ?>),
                <?= hitungLamaMenikah($a['tanggal_nikah']) ?> tahun
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><em>Tidak ada ulang tahun pernikahan minggu ini.</em></p>
<?php endif; ?>

<?php if ($hasNewData): ?>
    <h4 style="margin-top: 2rem;">Data Baru</h4>
    <ul>
        <?php foreach ($dataBaru as $label => $items): ?>
            <?php if (!empty($items)): ?>
                <li>
                    <strong><?= ucfirst($label) ?>:</strong>
                    <ul>
                        <?php foreach ($items as $i): ?>
                            <li><strong><?= htmlspecialchars($i['nama']) ?></strong> - Ditambahkan pada: <?= date('d M Y', strtotime($i['created_at'])) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <h4 style="margin-top: 2rem;">Tidak ada data baru</h4>
<?php endif; ?>

<?php
$html = ob_get_clean();
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$filename = 'Laporan_Minggu_Ini_Periode_' . date('d-m-Y', strtotime($monday)) . '_s.d_' . date('d-m-Y', strtotime($sunday)) . '.pdf';
$mpdf->Output($filename, 'I');
