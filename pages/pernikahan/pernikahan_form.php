<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_pernikahan = $_GET['id'] ?? null;

$readonly = '';
$submit_name = '';
$title = '';

// Default data pernikahan kosong
$pernikahan = [
    'id_pernikahan' => '',
    'id_suami' => '',
    'id_istri' => '',
    'tempat_nikah' => '',
    'tanggal_nikah' => '',
    'no_surat_nikah' => '',
    'pendeta' => '',
    'keterangan' => '',
    'nama_suami' => '',
    'nama_istri' => ''
];

// Ambil data jemaat untuk suami (termasuk yang sedang diedit)
// Ambil semua jemaat laki-laki
$suami_list = query("
    SELECT id_jemaat, nama_lengkap FROM jemaat 
    WHERE jenis_kelamin = 'Laki-laki'
    ORDER BY nama_lengkap
");

// Ambil semua jemaat perempuan
$istri_list = query("
    SELECT id_jemaat, nama_lengkap FROM jemaat 
    WHERE jenis_kelamin = 'Perempuan'
    ORDER BY nama_lengkap
");



if ($id_pernikahan) {
    $result = query("
        SELECT p.*, 
               s.nama_lengkap AS nama_suami,
               i.nama_lengkap AS nama_istri
        FROM pernikahan p
        LEFT JOIN jemaat s ON p.id_suami = s.id_jemaat
        LEFT JOIN jemaat i ON p.id_istri = i.id_jemaat
        WHERE p.id_pernikahan = $id_pernikahan
    ");
    if (!empty($result)) {
        $pernikahan = $result[0];
    }
}

if ($action === 'detail') {
    $readonly = 'readonly disabled';
    $submit_name = '';
    $title = 'Detail';
} else {
    $submit_name = 'updatePernikahan';
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
    <h5 class="modal-title">
        <?= $title === "Detail" ? $title . " Pernikahan" : $title . " Data: " . htmlspecialchars($pernikahan['nama_suami'] . ' & ' . $pernikahan['nama_istri']) ?>
    </h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <div class="modal-body px-3 px-sm-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase">Informasi Pernikahan</h4>
            <p class="text-black mb-2"><strong><?= htmlspecialchars($pernikahan['nama_suami']) ?></strong> & <strong><?= htmlspecialchars($pernikahan['nama_istri']) ?></strong></p>
        </div>
        <hr>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Tempat Nikah:</strong><br><?= htmlspecialchars($pernikahan['tempat_nikah']) ?></div>
                <div class="col-md-4"><strong>Tanggal Nikah:</strong><br><?= formatTanggal($pernikahan['tanggal_nikah']) ?></div>
                <div class="col-md-4"><strong>No Surat Nikah:</strong><br><?= htmlspecialchars($pernikahan['no_surat_nikah']) ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Pendeta:</strong><br><?= htmlspecialchars($pernikahan['pendeta']) ?></div>
                <div class="col-md-8"><strong>Keterangan:</strong><br><?= htmlspecialchars($pernikahan['keterangan']) ?></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>

<?php else: ?>
    <form method="POST">
        <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <input type="hidden" name="id_pernikahan" value="<?= $pernikahan['id_pernikahan'] ?>">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="id_suami" class="form-label required">Suami</label>
                    <select id="id_suami" name="id_suami" class="form-control select2" required>
                        <option value="">-- Pilih Suami --</option>
                        <?php foreach ($suami_list as $j): ?>
                            <option value="<?= $j['id_jemaat'] ?>" <?= $j['id_jemaat'] == $pernikahan['id_suami'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="id_istri" class="form-label required">Istri</label>
                    <select id="id_istri" name="id_istri" class="form-control select2" required>
                        <option value="">-- Pilih Istri --</option>
                        <?php foreach ($istri_list as $j): ?>
                            <option value="<?= $j['id_jemaat'] ?>" <?= $j['id_jemaat'] == $pernikahan['id_istri'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tempat Nikah</label>
                    <input type="text" name="tempat_nikah" class="form-control" value="<?= htmlspecialchars($pernikahan['tempat_nikah']) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Tanggal Nikah</label>
                    <input type="date" name="tanggal_nikah" class="form-control" value="<?= $pernikahan['tanggal_nikah'] ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>No Surat Nikah</label>
                    <input type="text" name="no_surat_nikah" class="form-control" value="<?= htmlspecialchars($pernikahan['no_surat_nikah']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Pendeta</label>
                    <input type="text" name="pendeta" class="form-control" value="<?= htmlspecialchars($pernikahan['pendeta']) ?>">
                </div>
                <div class="form-group col-md-8">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4"><?= htmlspecialchars($pernikahan['keterangan']) ?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-success">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>
<?php endif; ?>