<?php
require_once '../../database.php';

$action = $_GET['action'] ?? 'detail';
$id_jemaat = $_GET['id'] ?? null;

// Ambil data keluarga untuk dropdown
$keluarga_list = query("SELECT id_keluarga, kode_kk, nama_keluarga FROM keluarga ORDER BY kode_kk");

$readonly = '';
$submit_name = '';
$title = '';

$jemaat = [
    'nama_lengkap' => '',
    'jenis_kelamin' => '',
    'id_keluarga' => '',
    'tempat_lahir' => '',
    'tanggal_lahir' => '',
    'status_perkawinan' => '',
    'status_dlm_keluarga' => '',
    'status_baptis' => '',
    'status_sidi' => '',
    'pendidikan_terakhir' => '',
    'pekerjaan' => ''
];

$keluarga_info = [
    'alamat' => '-',
    'rayon' => '-',
    'nama_keluarga' => '-',
    'keterangan' => '-',
    'tempat_tinggal' => '-'
];

if ($action === 'edit' || $action === 'detail') {
    if ($id_jemaat) {
        $jemaat = query("SELECT * FROM jemaat WHERE id_jemaat = $id_jemaat")[0];

        if ($action === 'detail' && $jemaat['id_keluarga']) {
            $keluarga = query("SELECT k.*, r.* 
                               FROM keluarga k 
                               LEFT JOIN rayon r ON r.id_rayon = k.id_rayon 
                               WHERE k.id_keluarga = {$jemaat['id_keluarga']}")[0];
            $keluarga_info = [
                'alamat' => $keluarga['alamat'],
                'rayon' => $keluarga['nama_rayon'] ?? '-',
                'nama_keluarga' => $keluarga['nama_keluarga'] ?? '-',
                'keterangan' => $keluarga['keterangan'] ?? '-',
                'tempat_tinggal' => $keluarga['tempat_tinggal'] ?? '-'
            ];
        }
    }

    if ($action === 'detail') {
        $readonly = 'readonly disabled';
        $submit_name = '';
        $title = 'Detail';
    } else {
        $submit_name = 'updateJemaat';
        $title = 'Edit';
    }
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

<div class="modal-header <?= $action === "edit" ? "bg-success" : "bg-info" ?>  text-white">
    <h5 class="modal-title"><?= $title ?> Jemaat: <?= strtoupper($jemaat['nama_lengkap']) ?></h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php if ($action === 'detail'): ?>
    <!-- DETAIL MODE -->

    <div class="modal-body px-3 px-sm-5 px-md-5 px-lg-5 px-xl-5">
        <div class="text-center mb-4">
            <h4 class="font-weight-bold text-uppercase"><?= $jemaat['nama_lengkap'] ?></h4>
            <p class="text-muted mb-0">Kode Keluarga: <?= $keluarga['kode_kk'] ?></p>
        </div>

        <hr>

        <div class="container-fluid">
            <!-- Informasi Utama -->
            <div class="row mb-3">
                <div class="col-md-4"><strong>Jenis Kelamin:</strong><br><?= $jemaat['jenis_kelamin'] ?></div>
                <div class="col-md-4"><strong>Tempat & Tanggal Lahir:</strong><br><?= $jemaat['tempat_lahir'] ?>, <?= formatTanggal($jemaat['tanggal_lahir']) ?></div>
                <div class="col-md-4"><strong>Status dalam Keluarga:</strong><br><?= $jemaat['status_dlm_keluarga'] ?></div>
            </div>

            <!-- Status Jemaat -->
            <div class="row mb-3">

                <div class="col-md-4"><strong>Baptis / Sidi:</strong><br><?= $jemaat['status_baptis'] ?> / <?= $jemaat['status_sidi'] ?></div>
                <div class="col-md-4"><strong>Status Pernikahan:</strong><br><?= $jemaat['status_perkawinan'] ?></div>
                <div class="col-md-4"><strong>Status Jemaat:</strong><br>
                    <?php
                    $status = $jemaat['status_jemaat'];
                    $badgeClass = match ($status) {
                        'Aktif'     => 'success',
                        'Pindah'    => 'warning',
                        'Meninggal' => 'danger',
                        default     => 'secondary',
                    };
                    ?>

                    <span class="badge badge-<?= $badgeClass ?>">
                        <?= htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>

            <!-- Pendidikan dan Pekerjaan -->
            <div class="row mb-3">
                <div class="col-md-4"><strong>Pendidikan Terakhir:</strong><br><?= $jemaat['pendidikan_terakhir'] ?></div>
                <div class="col-md-4"><strong>Pekerjaan:</strong><br><?= $jemaat['pekerjaan'] ?></div>
                <div class="col-md-4"></div>
            </div>

            <!-- Info Keluarga -->
            <hr>
            <h6 class="text-secondary font-weight-bold">Informasi Keluarga</h6>

            <div class="row mb-3">
                <div class="col-md-4"><strong>Alamat:</strong><br><?= $keluarga_info['alamat'] ?></div>
                <div class="col-md-4"><strong>Rayon:</strong><br><?= $keluarga_info['rayon'] ?></div>
                <div class="col-md-4"><strong>Tempat Tinggal:</strong><br><?= $keluarga_info['tempat_tinggal'] ?></div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <a href="laporan/jemaat/laporan_jemaat.php?id=<?= $id_jemaat ?>" class="btn btn-warning" target="_blank">
            Cetak
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>

<?php else: ?>
    <!-- EDIT MODE -->
    <form method="POST">
        <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <input type="hidden" name="id_jemaat" value="<?= $id_jemaat ?>">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" pattern="[A-Za-z\s\.\,\-]+" value="<?= htmlspecialchars($jemaat['nama_lengkap']) ?>" title="Hanya huruf, spasi, titik, koma, dan tanda hubung" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-3">
                    <label>Kode Kepala Keluarga</label>
                    <select name="id_keluarga" class="form-control select2" <?= $readonly ?> required>
                        <option value="">- Pilih Keluarga -</option>
                        <?php foreach ($keluarga_list as $k): ?>
                            <option value="<?= $k['id_keluarga'] ?>" <?= $jemaat['id_keluarga'] == $k['id_keluarga'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['kode_kk']) ?> - <?= htmlspecialchars($k['nama_keluarga']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" <?= $readonly ?> required>
                        <option value="">- Pilih -</option>
                        <option value="Laki-laki" <?= $jemaat['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= $jemaat['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($jemaat['tempat_lahir']) ?>" pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= $jemaat['tanggal_lahir'] ?>" <?= $readonly ?> required>
                    <small>Bulan/Hari/Tahun</small>
                </div>
                <div class="form-group col-md-3">
                    <label>Status dalam Keluarga</label>
                    <input type="text" name="status_dlm_keluarga" class="form-control" pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi" value="<?= htmlspecialchars($jemaat['status_dlm_keluarga']) ?>" <?= $readonly ?> required>
                </div>
                <div class="form-group col-md-3">
                    <label>Status Jemaat</label>
                    <input type="text" name="status_jemaat" class="form-control" pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi" value="<?= htmlspecialchars($jemaat['status_jemaat']) ?>" <?= $readonly ?> required>
                    <small>(Aktif/Pindah/Meninggal/dll)</small>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Status Baptis</label>
                    <!-- Status Baptis -->
                    <select name="status_baptis" class="form-control" <?= $readonly === 'readonly' ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Status Baptis --</option>
                        <option value="Sudah Baptis" <?= $jemaat['status_baptis'] == 'Sudah Baptis' ? 'selected' : '' ?>>Sudah Baptis</option>
                        <option value="Belum Baptis" <?= $jemaat['status_baptis'] == 'Belum Baptis' ? 'selected' : '' ?>>Belum Baptis</option>
                    </select>
                    <?php if ($readonly === 'readonly'): ?>
                        <input type="hidden" name="status_baptis" value="<?= htmlspecialchars($jemaat['status_baptis']) ?>">
                    <?php endif; ?>
                </div>
                <div class="form-group col-md-4">
                    <label>Status Sidi</label>
                    <!-- Status Sidi -->
                    <select name="status_sidi" class="form-control" <?= $readonly === 'readonly' ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Status Sidi --</option>
                        <option value="Sudah Sidi" <?= $jemaat['status_sidi'] == 'Sudah Sidi' ? 'selected' : '' ?>>Sudah Sidi</option>
                        <option value="Belum Sidi" <?= $jemaat['status_sidi'] == 'Belum Sidi' ? 'selected' : '' ?>>Belum Sidi</option>
                    </select>
                    <?php if ($readonly === 'readonly'): ?>
                        <input type="hidden" name="status_sidi" value="<?= htmlspecialchars($jemaat['status_sidi']) ?>">
                    <?php endif; ?>
                </div>
                <div class="form-group col-md-4">
                    <label>Status Pernikahan</label>
                    <!-- Status Perkawinan -->
                    <select name="status_perkawinan" class="form-control" <?= $readonly === 'readonly' ? 'disabled' : '' ?>>
                        <option value="">-- Pilih Status Pernikahan --</option>
                        <option value="Sudah Menikah" <?= $jemaat['status_perkawinan'] == 'Sudah Menikah' ? 'selected' : '' ?>>Sudah Menikah</option>
                        <option value="Belum Menikah" <?= $jemaat['status_perkawinan'] == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                    </select>
                    <?php if ($readonly === 'readonly'): ?>
                        <input type="hidden" name="status_perkawinan" value="<?= htmlspecialchars($jemaat['status_perkawinan']) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row">

                <div class="form-group col-md-4">
                    <label>Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan_terakhir" class="form-control" value="<?= htmlspecialchars($jemaat['pendidikan_terakhir']) ?>" <?= $readonly ?>>
                </div>
                <div class="form-group col-md-4">
                    <label>Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi" value="<?= htmlspecialchars($jemaat['pekerjaan']) ?>" <?= $readonly ?>>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="<?= $submit_name ?>" class="btn bg-success">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </form>
<?php endif; ?>