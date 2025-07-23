<?php
require_once '../vendor/autoload.php';
require_once '../database.php';

$id = $_GET['id'] ?? null;
if (!$id) die('ID Jemaat tidak ditemukan');

$jemaat = query("SELECT j.*, k.kode_kk, k.alamat, k.tempat_tinggal, r.nama_rayon, r.keterangan 
                FROM jemaat j
                LEFT JOIN keluarga k ON j.id_keluarga = k.id_keluarga
                LEFT JOIN rayon r ON k.id_rayon = r.id_rayon
                WHERE j.id_jemaat = $id")[0];

function formatTanggal($tgl)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $tanggal = date('d', strtotime($tgl));
    $bulanNum = (int)date('m', strtotime($tgl));
    $tahun = date('Y', strtotime($tgl));
    return "$tanggal {$bulan[$bulanNum]} $tahun";
}

ob_start();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Jemaat - <?= htmlspecialchars($jemaat['nama_lengkap']) ?></title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .label {
            width: 35%;
            font-weight: bold;
        }

        .value {
            width: 65%;
        }

        .spacer-row td {
            padding-top: 12px;
        }
    </style>
</head>

<body>

    <h2>Data Jemaat: <?= htmlspecialchars($jemaat['nama_lengkap']) ?></h2>

    <table>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="value"><?= htmlspecialchars($jemaat['nama_lengkap']) ?></td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="value"><?= htmlspecialchars($jemaat['jenis_kelamin']) ?></td>
        </tr>
        <tr>
            <td class="label">Tempat & Tanggal Lahir</td>
            <td class="value"><?= htmlspecialchars($jemaat['tempat_lahir']) ?>, <?= formatTanggal($jemaat['tanggal_lahir']) ?></td>
        </tr>
        <tr>
            <td class="label">Tempat Tinggal</td>
            <td class="value"><?= htmlspecialchars($jemaat['tempat_tinggal']) ?></td>
        </tr>
        <tr>
            <td class="label">Status dalam Keluarga</td>
            <td class="value"><?= htmlspecialchars($jemaat['status_dlm_keluarga']) ?></td>
        </tr>
        <tr>
            <td class="label">Status Perkawinan</td>
            <td class="value"><?= htmlspecialchars($jemaat['status_perkawinan']) ?></td>
        </tr>
        <tr>
            <td class="label">Status Baptis / Sidi</td>
            <td class="value"><?= htmlspecialchars($jemaat['status_baptis']) ?> / <?= htmlspecialchars($jemaat['status_sidi']) ?></td>
        </tr>
        <tr>
            <td class="label">Pendidikan Terakhir</td>
            <td class="value"><?= htmlspecialchars($jemaat['pendidikan_terakhir']) ?></td>
        </tr>
        <tr>
            <td class="label">Pekerjaan</td>
            <td class="value"><?= htmlspecialchars($jemaat['pekerjaan']) ?></td>
        </tr>
        <tr>
            <td class="label">Kode KK</td>
            <td class="value"><?= htmlspecialchars($jemaat['kode_kk']) ?></td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="value"><?= htmlspecialchars($jemaat['alamat']) ?></td>
        </tr>
        <tr>
            <td class="label">Rayon</td>
            <td class="value"><?= htmlspecialchars($jemaat['nama_rayon']) ?></td>
        </tr>
    </table>

</body>

</html>

<?php
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output("Detail Jemaat - {$jemaat['nama_lengkap']}.pdf", 'I');
