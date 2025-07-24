<?php
require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_POST['filteredData'], $_POST['visibleHeaders'])) {
    die('Data tidak lengkap.');
}

$data = json_decode($_POST['filteredData'], true);
$headers = json_decode($_POST['visibleHeaders'], true);
$totalRows = count($data);

// Format tanggal Indonesia
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
    return "$tanggal {$bulan[$bulanNum]} $tahun";
}
$tanggalCetak = formatTanggalIndo(date('Y-m-d'));

// Ringkasan
$ignoredLabels = ['#', 'Nama Lengkap', 'Tempat, Tanggal Lahir', 'Alamat'];
$columnSummary = [];
$columnDetailCounts = [];

foreach ($headers as $index => $headerName) {
    $values = [];
    $detailCount = [];
    foreach ($data as $row) {
        $val = trim($row[$index] ?? '-');
        $val = $val === '' ? '-' : $val;
        $values[] = $val;
        $detailCount[$val] = ($detailCount[$val] ?? 0) + 1;
    }
    $columnSummary[$headerName] = count(array_unique($values));
    if (!in_array($headerName, $ignoredLabels)) {
        $columnDetailCounts[$headerName] = $detailCount;
    }
}

$stylesheet = <<<CSS
<style>
    body { font-family: sans-serif; font-size: 11pt; }

    /* Hapus style kop-container dan kop-teks yang sudah tidak dipakai */

    .tanggal-cetak {
        text-align: right;
        font-size: 9pt;
        font-style: italic;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid #444;
        padding: 6px;
        text-align: left;
    }

    th {
        background-color: #eee;
    }

    ul {
        padding-left: 1.2rem;
        margin-top: 0.2rem;
    }

    .rekap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px;
    }

    .rekap-item {
        font-size: 10pt;
        border: 1px solid #ccc;
        padding: 8px;
        background: #f9f9f9;
    }
</style>
CSS;
$kop = <<<HTML
<table width="100%" style="border-collapse: collapse; border: none; margin-bottom: 15px;">
    <tr>
        <td style="width: 100px; border: none; vertical-align: middle; text-align: center;">
            <img src="../dist/img/logo.png" alt="Logo Gereja" style="width: 80px; height: 100px; object-fit: contain;">
        </td>
        <td style="border: none; vertical-align: middle; text-align: center;">
            <h2 style="margin: 0; font-size: 16pt; line-height: 1.2;">GMIT TAMARISKA MAULAFA</h2>
            <p style="margin: 0; font-size: 10pt; line-height: 1.2;">
            Jln. S.D Laning, Kel. Maulafa, Kec. Maulafa, Kota Kupang, NTT
            </p>
        </td>
    </tr>
</table>

<div style="border-bottom: 2px solid #000;"></div>

<div class="tanggal-cetak">Dicetak pada: $tanggalCetak</div>
<strong>Total Data Ditampilkan:</strong> $totalRows baris<br><br>
<strong>Rekap Kolom:</strong>
<ul>
HTML;




foreach ($columnSummary as $col => $jumlah) {
    $kop .= "<li><strong>" . htmlspecialchars($col) . ":</strong> $jumlah nilai unik</li>";
}
$kop .= "</ul>";

// Halaman 2: Detail Nilai dan Tabel Data
$rekapHtml = "<strong>Rincian Nilai per Kolom:</strong><br><table style='border:0;'><tr>";

$totalItems = count($columnDetailCounts);
$cols = ($totalItems >= 6) ? 3 : (($totalItems >= 3) ? 2 : 1);
$chunked = array_chunk($columnDetailCounts, ceil($totalItems / $cols), true);

foreach ($chunked as $columnGroup) {
    $rekapHtml .= '<td valign="top" width="' . round(100 / $cols) . '%">';
    foreach ($columnGroup as $col => $items) {
        $rekapHtml .= "<strong>" . htmlspecialchars($col) . ":</strong><ul>";
        foreach ($items as $val => $count) {
            $rekapHtml .= "<li>" . htmlspecialchars($val) . ": $count data</li>";
        }
        $rekapHtml .= "</ul><br>";
    }
    $rekapHtml .= '</td>';
}
$rekapHtml .= "</tr></table><br>";

// Tabel Data
$tableHtml = "<table><thead><tr>";
foreach ($headers as $header) {
    $tableHtml .= "<th>" . htmlspecialchars($header) . "</th>";
}
$tableHtml .= "</tr></thead><tbody>";
foreach ($data as $row) {
    $tableHtml .= "<tr>";
    foreach ($row as $cell) {
        $safeCell = htmlspecialchars($cell ?: '-');
        $tableHtml .= "<td>$safeCell</td>";
    }
    $tableHtml .= "</tr>";
}
$tableHtml .= "</tbody></table>";

// Setup mPDF
$mpdf = new Mpdf(['format' => 'A4']);
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

// Halaman pertama potrait
$mpdf->WriteHTML($kop, \Mpdf\HTMLParserMode::HTML_BODY);

// Jika jumlah data besar, pisahkan ke halaman landscape
if ($totalRows > 0) {
    $mpdf->AddPage('L'); // Halaman 2 - Landscape
    $mpdf->WriteHTML($rekapHtml . $tableHtml, \Mpdf\HTMLParserMode::HTML_BODY);
}

$mpdf->Output('laporan_Jemaat_' . $tanggalCetak . '.pdf', 'I');
