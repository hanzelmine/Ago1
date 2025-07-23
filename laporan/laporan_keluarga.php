<?php
require_once '../vendor/autoload.php'; // adjust if needed
require_once '../database.php';

$id_keluarga = $_GET['id'] ?? null;

if (!$id_keluarga) {
    die('ID keluarga tidak ditemukan.');
}

// Get keluarga data
$keluarga = query("SELECT * FROM keluarga WHERE id_keluarga = $id_keluarga")[0] ?? null;
if (!$keluarga) {
    die('Data keluarga tidak ditemukan.');
}

// Get rayon data
$rayon = query("SELECT * FROM rayon WHERE id_rayon = {$keluarga['id_rayon']}")[0] ?? ['nama_rayon' => '-', 'keterangan' => '-'];

// Get jemaat data
$jemaat = query("SELECT * FROM jemaat WHERE id_keluarga = $id_keluarga");

function tanggalIndo($tanggal)
{
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));

    return "$tgl $bln $thn";
}

// Start buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keluarga - <?= htmlspecialchars($keluarga['kode_kk']) ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12pt;
        }

        h2 {
            text-align: center;
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
        }

        th {
            background-color: #f0f0f0;
        }

        .info {
            margin-top: 20px;
        }

        .label {
            width: 150px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>

<body>

    <h2>Laporan Data Keluarga</h2>

    <div class="info">
        <p><span class="label">Kode KK:</span> <?= htmlspecialchars($keluarga['kode_kk']) ?></p>
        <p><span class="label">Rayon:</span> <?= htmlspecialchars($rayon['nama_rayon']) ?> - <?= htmlspecialchars($rayon['keterangan']) ?></p>
        <p><span class="label">Alamat:</span> <?= htmlspecialchars($keluarga['alamat']) ?></p>
        <p><span class="label">Tempat Tinggal:</span> <?= htmlspecialchars($keluarga['tempat_tinggal']) ?></p>
    </div>

    <h3>Daftar Jemaat dalam Keluarga Ini</h3>

    <?php if (count($jemaat) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat / Tgl Lahir</th>
                    <th>Status Keluarga</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jemaat as $j): ?>
                    <tr>
                        <td><?= htmlspecialchars($j['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($j['jenis_kelamin']) ?></td>
                        <td><?= htmlspecialchars($j['tempat_lahir']) ?>, <?= tanggalIndo($j['tanggal_lahir']) ?></td>
                        <td><?= htmlspecialchars($j['status_dlm_keluarga']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><em>Belum ada jemaat yang terdaftar dalam keluarga ini.</em></p>
    <?php endif; ?>

</body>

</html>

<?php
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'orientation' => 'P'
]);

$mpdf->WriteHTML($html);
$mpdf->Output('Laporan_Keluarga_' . $keluarga['kode_kk'] . '.pdf', \Mpdf\Output\Destination::INLINE);
