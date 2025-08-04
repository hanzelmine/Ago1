<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_jemaat = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

$baptisan = [
    'id_baptisan' => '',
    'id_jemaat' => $id_jemaat,
    'tempat_baptis' => '',
    'tanggal_baptis' => '',
    'no_surat_baptis' => '',
    'pendeta' => '',
    'keterangan' => '',
    'nama_lengkap' => ''
];

if ($id_jemaat) {
    $result = query("
        SELECT b.*, j.nama_lengkap 
        FROM baptisan b
        JOIN jemaat j ON b.id_jemaat = j.id_jemaat
        WHERE b.id_jemaat = $id_jemaat
    ");

    if (!empty($result)) {
        $baptisan = $result[0];
    }
}

if ($action === 'detail') {
    $readonly = 'readonly disabled';
    $submit_name = '';
    $title = 'Detail';
} else {
    $submit_name = 'updateBaptisan';
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
    <h5 class="modal-title"><?= $title === "Detail" ? $title . " Baptisan" : $title . " Data: " . htmlspecialchars($baptisan['nama_lengkap']) ?></h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <!-- DETAIL MODE -->
    <div class="modal-body px-3 px-sm-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase">Informasi Baptisan</h4>
            <p class="text-black mb-2">Nama Jemaat: <strong><?= htmlspecialchars($baptisan['nama_lengkap']) ?></strong></p>

        </div>

        <hr>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Tempat Baptis:</strong><br><?= htmlspecialchars($baptisan['tempat_baptis']) ?></div>
                <div class="col-md-4"><strong>Tanggal Baptis:</strong><br><?= formatTanggal($baptisan['tanggal_baptis']) ?></div>
                <div class="col-md-4"><strong>No Surat Baptis:</strong><br><?= htmlspecialchars($baptisan['no_surat_baptis']) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>Pendeta:</strong><br><?= htmlspecialchars($baptisan['pendeta']) ?></div>
                <div class="col-md-8"><strong>Keterangan:</strong><br><?= htmlspecialchars($baptisan['keterangan']) ?></div>
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
            <input type="hidden" name="id_baptisan" value="<?= $baptisan['id_baptisan'] ?>">
            <input type="hidden" name="id_jemaat" value="<?= $id_jemaat ?>">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tempat Baptis</label>
                    <input type="text" name="tempat_baptis" class="form-control" value="<?= htmlspecialchars($baptisan['tempat_baptis']) ?>" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-4">
                    <label>Tanggal Baptis</label>
                    <input type="date" name="tanggal_baptis" class="form-control" value="<?= $baptisan['tanggal_baptis'] ?>" <?= $readonly ?> required>
                    <small>Bulan/Tanggal/Tahun</small>
                </div>
                <div class="form-group col-md-4">
                    <label>No Surat Baptis</label>
                    <input type="text" name="no_surat_baptis" class="form-control" value="<?= htmlspecialchars($baptisan['no_surat_baptis']) ?>" <?= $readonly ?>>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Pendeta</label>
                    <input type="text" name="pendeta" class="form-control" value="<?= htmlspecialchars($baptisan['pendeta']) ?>" <?= $readonly ?>>
                </div>
                <div class="form-group col-md-8">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4" <?= $readonly ?>><?= htmlspecialchars($baptisan['keterangan']) ?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-success">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>
<?php endif; ?>