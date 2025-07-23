<?php
require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_POST['filteredData']) || !isset($_POST['visibleHeaders'])) {
    die('Data tidak lengkap.');
}

$data = json_decode($_POST['filteredData'], true);
$headers = json_decode($_POST['visibleHeaders'], true);
$totalRows = count($data);

// Fungsi tanggal Indonesia
function formatTanggalIndo($tgl)
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
    return $tanggal . ' ' . $bulan[$bulanNum] . ' ' . $tahun;
}
$tanggalCetak = formatTanggalIndo(date('Y-m-d'));

// Daftar kolom yang tidak dihitung rekap detail
$ignoredLabels = ['#', 'Nama Lengkap', 'Tempat, Tanggal Lahir', 'Alamat'];

// Rekap ringkasan (jumlah nilai unik)
$columnSummary = [];
$columnDetailCounts = [];
foreach ($headers as $index => $headerName) {
    $values = [];
    $detailCount = [];

    foreach ($data as $row) {
        $val = trim($row[$index] ?? '-');
        $val = ($val === '' ? '-' : $val);
        $values[] = $val;

        if (!isset($detailCount[$val])) {
            $detailCount[$val] = 0;
        }
        $detailCount[$val]++;
    }

    $columnSummary[$headerName] = count(array_unique($values));

    // Rekap detail jika bukan kolom yang diabaikan
    if (!in_array($headerName, $ignoredLabels)) {
        $columnDetailCounts[$headerName] = $detailCount;
    }
}

ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Jemaat</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11pt;
        }

        .kop-container {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .kop-container img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 15px;
        }

        .kop-teks {
            text-align: center;
            flex: 1;
        }

        .kop-teks h2 {
            margin: 0;
            font-size: 16pt;
        }

        .kop-teks p {
            margin: 0;
            font-size: 10pt;
        }

        .tanggal-cetak {
            text-align: right;
            margin-bottom: 10px;
            font-size: 9pt;
            font-style: italic;
        }

        .summary {
            margin-bottom: 15px;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #eee;
        }

        ul {
            padding-left: 1.2rem;
            margin-top: 0.2rem;
            margin-bottom: 0.5rem;
        }

        li {
            margin-bottom: 0.1rem;
        }

        .rekap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
        }

        .rekap-item {
            font-size: 10pt;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            background-color: #f9f9f9;
        }

        .rekap-item strong {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 10pt;
        }

        .rekap-item ul {
            padding-left: 1.2rem;
            margin: 0;
        }

        .rekap-item li {
            margin: 0;
            font-size: 9.5pt;
        }
    </style>
</head>

<body>

    <div class="kop-container">
        <img src="../dist/img/logo.png" alt="Logo Gereja" style="width: 80px;
            height: 90px;
            object-fit: contain;
            margin-right: 15px;">
        <div class="kop-teks">
            <h2>GMIT TAMARISKA MAULAFA</h2>
            <p>Jln. S.D Laning, Kel. Maulafa, Kec. Maulafa, Kota Kupang, Nusa Tenggara Timur</p>
        </div>
    </div>

    <div class="tanggal-cetak">
        Dicetak pada: <?= $tanggalCetak ?>
    </div>

    <h3 style="text-align:center; margin-bottom: 15px;">Laporan Jemaat (Hasil Pencarian & Filter)</h3>

    <div class="summary">
        <strong>Total Data Ditampilkan:</strong> <?= $totalRows ?> baris<br>

        <strong>Rekap Kolom:</strong>
        <ul>
            <?php foreach ($columnSummary as $col => $jumlah): ?>
                <li><strong><?= htmlspecialchars($col) ?>:</strong> <?= $jumlah ?> nilai unik</li>
            <?php endforeach; ?>
        </ul>

        <strong>Rincian Nilai per Kolom:</strong><br>

        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; border:0;">
            <tr>
                <?php
                $totalItems = count($columnDetailCounts);
                $cols = ($totalItems >= 6) ? 3 : (($totalItems >= 3) ? 2 : 1);
                $chunked = array_chunk($columnDetailCounts, ceil($totalItems / $cols), true);

                foreach ($chunked as $columnGroup) {
                    echo '<td valign="top" width="' . round(100 / $cols) . '%">';
                    foreach ($columnGroup as $col => $items) {
                        echo '<strong>' . htmlspecialchars($col) . ':</strong><ul style="margin:0; padding-left:15px;">';
                        foreach ($items as $val => $count) {
                            echo '<li>' . htmlspecialchars($val) . ': ' . $count . ' data</li>';
                        }
                        echo '</ul><br>';
                    }
                    echo '</td>';
                }
                ?>
            </tr>
        </table>
    </div>


    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th><?= htmlspecialchars($header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $cell): ?>
                        <td><?= htmlspecialchars($cell ?: '-') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>

<?php
$html = ob_get_clean();
$mpdf = new Mpdf(['format' => 'A4-L']);
$mpdf->WriteHTML($html);
$mpdf->Output('laporan-jemaat.pdf', 'I');
