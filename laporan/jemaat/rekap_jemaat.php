<?php
require_once '../../vendor/autoload.php';
require_once '../../database.php';

$jemaat = query("SELECT * FROM jemaat");

$total = count($jemaat);

$gender = [];
$status_baptis = [];
$status_sidi = [];
$status_perkawinan = [];
$pendidikan_terakhir = [];
$pekerjaan = [];
$status_jemaat = [];

foreach ($jemaat as $j) {
    $jk = $j['jenis_kelamin'] === 'Laki-laki' ? 'Laki-laki' : ($j['jenis_kelamin'] === 'Perempuan' ? 'Perempuan' : '-');
    $baptis = $j['status_baptis'] ?? '-';
    $sidi = $j['status_sidi'] ?? '-';
    $perkawinan = $j['status_perkawinan'] ?? '-';
    $pendidikan = $j['pendidikan_terakhir'] ?? '-';
    $kerja = $j['pekerjaan'] ?? '-';
    $stat = $j['status_jemaat'] ?? '-';

    $gender[$jk] = ($gender[$jk] ?? 0) + 1;
    $status_baptis[$baptis] = ($status_baptis[$baptis] ?? 0) + 1;
    $status_sidi[$sidi] = ($status_sidi[$sidi] ?? 0) + 1;
    $status_perkawinan[$perkawinan] = ($status_perkawinan[$perkawinan] ?? 0) + 1;
    $pendidikan_terakhir[$pendidikan] = ($pendidikan_terakhir[$pendidikan] ?? 0) + 1;
    $pekerjaan[$kerja] = ($pekerjaan[$kerja] ?? 0) + 1;
    $status_jemaat[$stat] = ($status_jemaat[$stat] ?? 0) + 1;
}

function generateTable($title, $data)
{
    $html = "<table width='100%' border='0' cellpadding='4' cellspacing='0' style='margin-bottom: 20px;'>
                <thead>
                    <tr>
                        <th colspan='2' style='background-color: #f2f2f2; font-weight: bold;'>$title</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($data as $key => $val) {
        $html .= "<tr>
                    <td width='70%'>$key</td>
                    <td width='30%' align='right'><strong>$val</strong></td>
                  </tr>";
    }
    $html .= "</tbody></table>";
    return $html;
}

ob_start();
?>

<style>
    body {
        font-family: sans-serif;
        font-size: 11pt;
    }

    .title {
        font-size: 16pt;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .total {
        margin-bottom: 15px;
        font-weight: bold;
    }

    .column-wrapper {
        width: 100%;
    }

    .column {
        vertical-align: top;
        width: 50%;
    }
</style>

<div class="title">Rekap Data Jemaat</div>
<p class="total">Total Jemaat: <?= $total ?></p>

<table class="column-wrapper">
    <tr>
        <td class="column">
            <?= generateTable("Jenis Kelamin", $gender) ?>
            <?= generateTable("Status Baptis", $status_baptis) ?>
            <?= generateTable("Status Sidi", $status_sidi) ?>
            <?= generateTable("Status Perkawinan", $status_perkawinan) ?>
            <?= generateTable("Status Jemaat", $status_jemaat) ?>
        </td>
        <td class="column">
            <?= generateTable("Pendidikan Terakhir", $pendidikan_terakhir) ?>
            <?= generateTable("Pekerjaan", $pekerjaan) ?>
        </td>
    </tr>
</table>

<?php
$html = ob_get_clean();

$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'orientation' => 'P']);
$mpdf->SetTitle("Rekap Data Jemaat");
$mpdf->WriteHTML($html);
$mpdf->Output("rekap-data-jemaat.pdf", "I");
