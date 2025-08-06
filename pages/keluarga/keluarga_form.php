<?php
require_once '../../database.php';

$rayon = query("SELECT * FROM rayon");

$action = $_GET['action'] ?? '';
$id_keluarga = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

$keluarga = [
    'kode_kk' => '',
    'nama_keluarga' => '',
    'alamat' => '',
    'id_rayon' => '',
    'tempat_tinggal' => ''
];

$jemaat = [];

if ($action === 'edit' || $action === 'detail') {
    if ($id_keluarga) {
        $keluarga = query("SELECT * FROM keluarga WHERE id_keluarga = $id_keluarga")[0];
    }

    if ($action === 'detail') {
        $readonly = 'readonly disabled';
        $submit_name = '';
        $title = 'Detail';
        $jemaat = query("SELECT * FROM jemaat WHERE id_keluarga = $id_keluarga");
    } else {
        $submit_name = 'updateKeluarga';
        $title = 'Edit';
    }
}

function tanggalIndo($tanggal)
{
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));

    return "$tgl $bln $thn";
}
?>

<div class="modal-header <?= $action === 'edit' ? 'bg-success' : ($action === 'detail' ? 'bg-info' : '') ?> text-white">
    <h5 class="modal-title"><?= $title ?> Data Keluarga</h5>
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>

<form method="POST" id="keluargaForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="id_keluarga" value="<?= $id_keluarga ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Kode KK</label>
            <input type="text" name="kode_kk" class="form-control"
                maxlength="6"
                placeholder="Contoh : KK0001"
                pattern="^KK[0-9]{4}$"
                title="Format harus 'KK' diikuti 4 angka, contoh: KK0001"
                value="<?= htmlspecialchars($keluarga['kode_kk']) ?>" <?= $readonly ?> required>
        </div>

        <div class="form-group">
            <label>Nama Kepala Keluarga</label>
            <input type="text" name="nama_keluarga" class="form-control capitalize-first" pattern="[A-Za-z\s\.\,\-]+" title="Hanya huruf, spasi, titik, koma, dan tanda hubung"
                value="<?= htmlspecialchars($keluarga['nama_keluarga']) ?>" <?= $readonly ?> required>
        </div>

        <div class="form-group">
            <label>Rayon</label>
            <select name="id_rayon" class="form-control" <?= $readonly ?> required>
                <option value="">- Pilih Rayon -</option>
                <?php foreach ($rayon as $r): ?>
                    <option value="<?= $r['id_rayon'] ?>"
                        <?= isset($keluarga['id_rayon']) && $keluarga['id_rayon'] == $r['id_rayon'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nama_rayon']) ?> - <?= htmlspecialchars($r['keterangan']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" <?= $readonly ?>><?= htmlspecialchars($keluarga['alamat']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Tempat Tinggal</label>
            <input type="text" name="tempat_tinggal" class="form-control capitalize-first" maxlength="100"
                value="<?= htmlspecialchars($keluarga['tempat_tinggal']) ?>" <?= $readonly ?>>
        </div>

        <?php if ($action === 'detail'): ?>
            <hr>
            <h5 class="mt-3">Daftar Jemaat dalam Keluarga Ini</h5>
            <?php if (count($jemaat) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Lengkap</th>
                                <th>Jenis Kelamin</th>
                                <th>Tempat / Tgl Lahir</th>
                                <th>Status Keluarga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($jemaat as $j): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($j['nama_lengkap']) ?></td>
                                    <td><?= htmlspecialchars($j['jenis_kelamin']) ?></td>
                                    <td><?= htmlspecialchars($j['tempat_lahir']) ?>, <?= tanggalIndo($j['tanggal_lahir']) ?></td>
                                    <td><?= htmlspecialchars($j['status_dlm_keluarga']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p><em>Belum ada jemaat yang terdaftar dalam keluarga ini.</em></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?php if ($action === "detail"): ?>
            <a href="laporan/laporan_keluarga.php?id=<?= $id_keluarga ?>" class="btn btn-warning" target="_blank">
                Cetak
            </a>
        <?php endif; ?>

        <?php if ($submit_name): ?>
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-success">Simpan</button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>
</form>