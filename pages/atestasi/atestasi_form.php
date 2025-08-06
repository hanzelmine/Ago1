<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_atestasi = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

$atestasi = [
    'id_atestasi' => '',
    'id_jemaat' => '',
    'jenis_atestasi' => '',
    'gereja_asal_tujuan' => '',
    'keterangan' => '',
    'nama_lengkap' => ''
];

if ($id_atestasi) {
    $result = query("
        SELECT a.*, j.nama_lengkap 
        FROM atestasi a
        JOIN jemaat j ON a.id_jemaat = j.id_jemaat
        WHERE a.id_atestasi = $id_atestasi
    ");

    if (!empty($result)) {
        $atestasi = $result[0];
        $id_jemaat = $atestasi['id_jemaat'];
    }
}

if ($action === 'detail') {
    $readonly = 'readonly disabled';
    $submit_name = '';
    $title = 'Detail';
} else {
    $submit_name = 'updateAtestasi';
    $title = 'Edit';
}
?>

<div class="modal-header <?= $action === "edit" ? "bg-info" : "bg-dark" ?> text-white">
    <h5 class="modal-title">
        <?= $title === "Detail" ? $title . " Atestasi" : $title . " Data: " . htmlspecialchars($atestasi['nama_lengkap'] ?? '') ?>
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <!-- DETAIL MODE -->
    <div class="modal-body px-3 px-sm-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase">Informasi Atestasi</h4>
            <p class="text-black mb-2">Nama Jemaat: <strong><?= htmlspecialchars($atestasi['nama_lengkap'] ?? '') ?></strong></p>
        </div>

        <hr>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-6"><strong>Jenis Atestasi:</strong><br><?= htmlspecialchars($atestasi['jenis_atestasi']) ?></div>
                <div class="col-md-6"><strong>Gereja Asal/Tujuan:</strong><br><?= htmlspecialchars($atestasi['gereja_asal_tujuan']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12"><strong>Keterangan:</strong><br><?= htmlspecialchars($atestasi['keterangan']) ?></div>
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
            <input type="hidden" name="id_atestasi" value="<?= $atestasi['id_atestasi'] ?>">
            <input type="hidden" name="id_jemaat" value="<?= $id_jemaat ?>">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Jenis Atestasi</label>
                    <select name="jenis_atestasi" class="form-control" <?= $readonly ?> required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Masuk" <?= $atestasi['jenis_atestasi'] === 'Masuk' ? 'selected' : '' ?>>Masuk</option>
                        <option value="Keluar" <?= $atestasi['jenis_atestasi'] === 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Gereja Asal/Tujuan</label>
                    <input type="text" name="gereja_asal_tujuan" class="form-control" value="<?= htmlspecialchars($atestasi['gereja_asal_tujuan']) ?>" <?= $readonly ?> required>
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="4" <?= $readonly ?>><?= htmlspecialchars($atestasi['keterangan']) ?></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-info">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>

<?php endif; ?>