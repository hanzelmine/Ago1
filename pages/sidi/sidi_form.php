<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_jemaat = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

$sidi = [
    'id_sidi' => '',
    'id_jemaat' => $id_jemaat,
    'tempat_sidi' => '',
    'tanggal_sidi' => '',
    'no_surat_sidi' => '',
    'pendeta' => '',
    'keterangan' => '',
    'nama_lengkap' => ''
];

if ($id_jemaat) {
    $result = query("
        SELECT s.*, j.nama_lengkap 
        FROM sidi s
        JOIN jemaat j ON s.id_jemaat = j.id_jemaat
        WHERE s.id_jemaat = $id_jemaat
    ");

    if (!empty($result)) {
        $sidi = $result[0];
    }
}

if ($action === 'detail') {
    $readonly = 'readonly disabled';
    $submit_name = '';
    $title = 'Detail';
} else {
    $submit_name = 'updateSidi';
    $title = 'Edit';
}

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
    return $tanggal . ' ' . $bulan[$bulanNum] . ' ' . $tahun;
}
?>

<div class="modal-header <?= $action === "edit" ? "bg-success" : "bg-info" ?> text-white">
    <h5 class="modal-title"><?= $title === "Detail" ? $title . " Sidi" : $title . " Data: " . htmlspecialchars($sidi['nama_lengkap']) ?></h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <!-- DETAIL MODE -->
    <div class="modal-body px-3 px-sm-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase">Informasi Sidi</h4>
            <p class="text-black mb-2">Nama Jemaat: <strong><?= htmlspecialchars($sidi['nama_lengkap']) ?></strong></p>
        </div>

        <hr>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Tempat Sidi:</strong><br><?= htmlspecialchars($sidi['tempat_sidi']) ?></div>
                <div class="col-md-4"><strong>Tanggal Sidi:</strong><br><?= formatTanggal($sidi['tanggal_sidi']) ?></div>
                <div class="col-md-4"><strong>No Surat Sidi:</strong><br><?= htmlspecialchars($sidi['no_surat_sidi']) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>Pendeta:</strong><br><?= htmlspecialchars($sidi['pendeta']) ?></div>
                <div class="col-md-8"><strong>Keterangan:</strong><br><?= htmlspecialchars($sidi['keterangan']) ?></div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>

<?php else: ?>

    <!-- EDIT MODE -->
    <form method="POST">
        <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <input type="hidden" name="id_sidi" value="<?= $sidi['id_sidi'] ?>">
            <input type="hidden" name="id_jemaat" value="<?= $id_jemaat ?>">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tempat Sidi</label>
                    <input type="text" name="tempat_sidi" class="form-control" value="<?= htmlspecialchars($sidi['tempat_sidi']) ?>" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-4">
                    <label>Tanggal Sidi</label>
                    <input type="date" name="tanggal_sidi" class="form-control" value="<?= $sidi['tanggal_sidi'] ?>" <?= $readonly ?> required>
                    <small>Bulan/Tanggal/Tahun</small>
                </div>
                <div class="form-group col-md-4">
                    <label>No Surat Sidi</label>
                    <input type="text" name="no_surat_sidi" class="form-control" value="<?= htmlspecialchars($sidi['no_surat_sidi']) ?>" <?= $readonly ?>>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Pendeta</label>
                    <input type="text" name="pendeta" class="form-control" value="<?= htmlspecialchars($sidi['pendeta']) ?>" <?= $readonly ?>>
                </div>
                <div class="form-group col-md-8">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4" <?= $readonly ?>><?= htmlspecialchars($sidi['keterangan']) ?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-success">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>
<?php endif; ?>