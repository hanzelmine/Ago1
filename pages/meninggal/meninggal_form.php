<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_meninggal = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

$meninggal = [
    'id_meninggal' => '',
    'id_jemaat' => '',
    'tanggal_meninggal' => '',
    'tempat_meninggal' => '',
    'sebab_meninggal' => '',
    'keterangan' => '',
    'nama_lengkap' => ''
];

if ($id_meninggal) {
    $result = query("
        SELECT m.*, j.nama_lengkap 
        FROM meninggal m
        JOIN jemaat j ON m.id_jemaat = j.id_jemaat
        WHERE m.id_meninggal = $id_meninggal
    ");

    if (!empty($result)) {
        $meninggal = $result[0];
        $id_jemaat = $meninggal['id_jemaat'];
    }
}

if ($action === 'detail') {
    $readonly = 'readonly disabled';
    $submit_name = '';
    $title = 'Detail';
} else {
    $submit_name = 'updateMeninggal';
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

<div class="modal-header <?= $action === "edit" ? "bg-primary" : "bg-success" ?> text-white">
    <h5 class="modal-title">
        <?= $title === "Detail" ? $title . " Data Kematian" : $title . " Data: " . htmlspecialchars($meninggal['nama_lengkap'] ?? '') ?>
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <!-- DETAIL MODE -->
    <div class="modal-body px-3 px-sm-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase">Informasi Kematian</h4>
            <p class="text-black mb-2">Nama Jemaat: <strong><?= htmlspecialchars($meninggal['nama_lengkap'] ?? '') ?></strong></p>
        </div>

        <hr>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Tempat Meninggal:</strong><br><?= htmlspecialchars($meninggal['tempat_meninggal']) ?></div>
                <div class="col-md-4"><strong>Tanggal Meninggal:</strong><br><?= formatTanggal($meninggal['tanggal_meninggal']) ?></div>
                <div class="col-md-4"><strong>Sebab Meninggal:</strong><br><?= htmlspecialchars($meninggal['sebab_meninggal']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12"><strong>Keterangan:</strong><br><?= htmlspecialchars($meninggal['keterangan']) ?></div>
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
            <input type="hidden" name="id_meninggal" value="<?= $meninggal['id_meninggal'] ?>">
            <input type="hidden" name="id_jemaat" value="<?= $id_jemaat ?>">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tempat Meninggal</label>
                    <input type="text" name="tempat_meninggal" class="form-control capitalize-first" value="<?= htmlspecialchars($meninggal['tempat_meninggal']) ?>" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-4">
                    <label>Tanggal Meninggal</label>
                    <input type="date" name="tanggal_meninggal" class="form-control" value="<?= $meninggal['tanggal_meninggal'] ?>" <?= $readonly ?> required>
                    <small>Bulan/Tanggal/Tahun</small>
                </div>
                <div class="form-group col-md-4">
                    <label>Sebab Meninggal</label>
                    <input type="text" name="sebab_meninggal" class="form-control" value="<?= htmlspecialchars($meninggal['sebab_meninggal']) ?>" <?= $readonly ?>>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4" <?= $readonly ?>><?= htmlspecialchars($meninggal['keterangan']) ?></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-danger">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>

<?php endif; ?>