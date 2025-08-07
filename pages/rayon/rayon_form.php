<?php
require_once '../../database.php';

$action = $_GET['action'] ?? '';
$id_rayon = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';
$rayon = [
    'nama_rayon' => '',
    'keterangan' => '',
];

if ($action === 'edit' || $action === 'detail') {
    if ($id_rayon) {
        $rayon = query("SELECT * FROM rayon WHERE id_rayon = $id_rayon")[0];
    }

    if ($action === 'detail') {
        $readonly = 'readonly disabled';
        $submit_name = '';
        $title = 'Detail';
    } else {
        $submit_name = 'updateRayon';
        $title = 'Edit';
    }
} else {
    http_response_code(400);
    exit('Aksi tidak valid');
}
?>

<div class="modal-header <?= $action === 'edit' ? 'bg-success' : ($action === 'detail' ? 'bg-info' : '') ?> text-white">
    <h5 class="modal-title"><?= $title ?> Data Rayon</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span>&times;</span>
    </button>
</div>

<form method="POST">
    <div class="modal-body">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id_rayon" value="<?= $id_rayon ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="nama_rayon" class="form-label required">Nama Rayon</label>
            <input type="text"
                name="nama_rayon"
                placeholder="Contoh : Rayon 1"
                class="form-control capitalize-first"
                pattern="^Rayon\s[1-9][0-9]*$"
                title="Format harus 'Rayon' diikuti spasi dan angka, contoh: Rayon 1"
                value="<?= htmlspecialchars($rayon['nama_rayon']) ?>"
                <?= $readonly ?>
                required>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="5" <?= $readonly ?>><?= htmlspecialchars($rayon['keterangan']) ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <?php if ($submit_name): ?>
            <button type="submit" name="<?= $submit_name ?>" class="btn <?= $action === 'edit' ? 'bg-success' : ($action === 'detail' ? 'bg-info' : '') ?>">Simpan</button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>
</form>