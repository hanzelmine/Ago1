<?php
require_once '../vendor/autoload.php';
require_once '../helpers.php'; // Adjust the path as needed

use Mpdf\Mpdf;

if (!isset($_POST['filteredData'], $_POST['visibleHeaders'])) {
    die('Data tidak lengkap.');
}

$data = json_decode($_POST['filteredData'], true);
$headers = json_decode($_POST['visibleHeaders'], true);
$activeFilters = isset($_POST['activeFilters']) ? json_decode($_POST['activeFilters'], true) : [];

$totalRows = count($data);

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

// Summary logic
$ignoredLabels = ['#', 'Tempat, Tanggal Lahir', 'Nama Lengkap'];
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

// STYLESHEET
$stylesheet = <<<CSS
<style>
    body { font-family: sans-serif; font-size: 11pt; }
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
</style>
CSS;

// PAGE 1: Header & Summary (Portrait)
ob_start();
?>

<?= $stylesheet ?>

<table width="100%" style="border-collapse: collapse; border: none; margin-bottom: 15px;">
    <tr>
        <?php
        $originalLogoPath = realpath(__DIR__ . '/../dist/img/logo.png');
        $tempJpgLogoPath = __DIR__ . '/tmp/logo_converted.jpg';

        if (convertPngToJpgWithWhiteBg($originalLogoPath, $tempJpgLogoPath)) {
            $logoPath = $tempJpgLogoPath;
        } else {
            $logoPath = $originalLogoPath;
        }

        $logoMime = mime_content_type($logoPath);
        $logoData = base64_encode(file_get_contents($logoPath));
        $base64Logo = "data:$logoMime;base64,$logoData";
        ?>
        <td style="width: 100px; border: none; vertical-align: middle; text-align: center;">
            <img src="<?= $base64Logo ?>" style="width:80px; height:auto;">
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
<div class="tanggal-cetak">Dicetak pada: <?= $tanggalCetak ?></div>
<strong>Total Data Ditampilkan:</strong> <?= $totalRows ?> baris<br><br>

<?php
$visibleColumnsList = array_map('htmlspecialchars', $headers);
$filterText = empty($activeFilters)
    ? 'Tidak ada'
    : implode(', ', array_map(
        fn($k, $v) => htmlspecialchars($k) . ': ' . htmlspecialchars($v),
        array_keys($activeFilters),
        $activeFilters
    ));
?>

<div style='margin-top:10px;'>
    Laporan ini disusun berdasarkan <strong>filter</strong> yang diterapkan yaitu:
    <strong><em><?= $filterText ?></em></strong>.<br><br>
    Kolom-kolom yang ditampilkan dalam laporan meliputi:
    <strong><em><?= implode(', ', $visibleColumnsList) ?></em></strong>.
</div>

<?php
$page1 = ob_get_clean();


// COMBINED PAGE (Rincian per Kolom + Table Data) - Only if $totalRows > 10
$pageSummaryDetail = '';
$pageOnlyTable = '';

if ($totalRows > 0) {
    // Rincian Nilai per Kolom - always show if data exists
    ob_start();
    echo $stylesheet;
    echo "<h4 style='margin-top: 0;'>Rincian Nilai per Kolom:</h4>";

    $totalItems = count($columnDetailCounts);
    $cols = ($totalItems >= 6) ? 3 : (($totalItems >= 3) ? 2 : 1);
    $chunked = array_chunk($columnDetailCounts, ceil($totalItems / $cols), true);

    echo "<table style='border:0;'>";
    echo "<tr>";
    foreach ($chunked as $columnGroup) {
        echo '<td valign="top" width="' . round(100 / $cols) . '%">';
        foreach ($columnGroup as $col => $items) {
            echo "<strong>" . htmlspecialchars($col) . ":</strong><ul>";
            foreach ($items as $val => $count) {
                echo "<li>" . htmlspecialchars($val) . ": $count data</li>";
            }
            echo "</ul><br>";
        }
        echo '</td>';
    }
    echo "</tr>";
    echo "</table><br>";
    $pageSummaryDetail = ob_get_clean();

    // Table
    ob_start();
    echo $stylesheet;
    echo "<table><thead><tr>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr></thead><tbody>";
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell ?: '-') . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    $pageOnlyTable = ob_get_clean();
}


// === GENERATE PDF ===
$mpdf = new Mpdf([
    'format' => 'A4',
    'tempDir' => __DIR__ . '/tmp',
    'allow_charset_conversion' => true,
    'defaultImageType' => 'png',
    'defaultImageMargin' => 0,
    'default_font' => 'sans',
    'setAutoTopMargin' => 'stretch',
]);

$mpdf->SetDisplayMode('fullpage');

// First page (portrait)
$mpdf->WriteHTML($page1);

// Combined landscape page (only if rows > 10)
if ($totalRows > 0) {
    $mpdf->AddPage('L');
    $mpdf->WriteHTML($pageSummaryDetail);
    $mpdf->WriteHTML($pageOnlyTable);
}


$mpdf->Output('laporan_Jemaat_' . $tanggalCetak . '.pdf', 'I');
