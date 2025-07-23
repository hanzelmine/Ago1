<?php
require_once 'database.php';
require_once 'functions/jemaat.php';

// Ambil data keluarga untuk dropdown
$keluarga_list = query("SELECT id_keluarga, kode_kk, nama_keluarga FROM keluarga ORDER BY kode_kk");

if (isset($_POST['insertJemaat'])) {
    $data = $_POST;

    if (insertJemaat($data)) {
        $total = count($_POST['nama_lengkap']); // Hitung total jemaat yang dimasukkan
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $total jemaat.");
    } else {
        set_alert('error', 'Gagal Menambahkan', 'Terjadi kesalahan saat menambahkan data.');
    }

    header("Location: index.php?page=jemaat");
    exit;
}

// Fungsi format tanggal
function formatTanggal($tgl)
{
    return $tgl ? date('d/m/Y', strtotime($tgl)) : '';
}
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h5>Tambah Data Jemaat</h5>
    </div>
    <form method="POST" id="createJemaatForm">
        <div class="card-body">
            <div id="jemaatContainer">
                <div class="card jemaat-form-group mb-3" data-index="0">
                    <div class="card-header d-flex justify-content-between align-items-center jemaat-form-header">
                        <!-- First form: only collapse button, no title or remove button -->
                        <div class="d-flex w-100 justify-content-end">
                            <button type="button" class="btn btn-warning btn-sm toggleCollapseBtn">
                                <i class="fas fa-angle-up"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body jemaat-form-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap[]" class="form-control" pattern="[A-Za-z\s\.\,\-]+" title="Hanya huruf, spasi, titik, koma, dan tanda hubung" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin[]" class="form-control" required>
                                    <option value="">- Pilih -</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir[]" class="form-control" required>
                                <small>Bulan/Hari/Tahun</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir[]" class="form-control" pattern="[A-Za-z\s]+" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Keluarga</label>
                                <select name="id_keluarga[]" class="form-control select2" required>
                                    <option value="">- Pilih Keluarga -</option>
                                    <?php foreach ($keluarga_list as $k): ?>
                                        <option value="<?= $k['id_keluarga'] ?>"><?= htmlspecialchars($k['kode_kk']) ?> - <?= htmlspecialchars($k['nama_keluarga']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Status Perkawinan</label>
                                <select name="status_perkawinan[]" class="form-control" required>
                                    <option value="">-- Pilih Status Perkawinan --</option>
                                    <option value="Sudah Menikah">Sudah Menikah</option>
                                    <option value="Belum Menikah">Belum Menikah</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Status Keluarga</label>
                                <input type="text" name="status_dlm_keluarga[]" class="form-control" pattern="[A-Za-z\s]+" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Status Baptis</label>
                                <select name="status_baptis[]" class="form-control" required>
                                    <option value="">-- Pilih Status Baptis --</option>
                                    <option value="Sudah Baptis">Sudah Baptis</option>
                                    <option value="Belum Baptis">Belum Baptis</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Status Sidi</label>
                                <select name="status_sidi[]" class="form-control" required>
                                    <option value="">-- Pilih Status Sidi --</option>
                                    <option value="Sudah Sidi">Sudah Sidi</option>
                                    <option value="Belum Sidi">Belum Sidi</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir[]" class="form-control" pattern="[A-Za-z\s]+">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Pekerjaan</label>
                                <input type="text" name="pekerjaan[]" class="form-control" pattern="[A-Za-z\s]+">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mb-3">
                <button type="button" class="btn btn-sm btn-secondary" id="addJemaatBtn">
                    <i class="fas fa-plus"></i> Tambah Jemaat
                </button>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-right">
                <button type="submit" name="insertJemaat" class="btn btn-primary">Simpan</button>
                <a href="index.php?page=jemaat" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </form>
</div>



<script>
    $(function() {
        const maxForm = 5;

        function updateFormHeaders() {
            const groups = $('.jemaat-form-group');
            groups.each(function(index) {
                const $header = $(this).find('.jemaat-form-header');
                $header.empty();

                const $left = $('<div><strong></strong></div>');
                const $right = $('<div class="ml-auto d-flex align-items-center"></div>');

                if (index === 0 && groups.length === 1) {
                    // Only collapse button
                    $right.append(`<button type="button" class="btn btn-warning btn-sm toggleCollapseBtn"><i class="fas fa-angle-up"></i></button>`);
                } else if (index === 0) {
                    $left.find('strong').text(`Form Jemaat ke-1`);
                    $right.append(`<button type="button" class="btn btn-warning btn-sm toggleCollapseBtn"><i class="fas fa-angle-up"></i></button>`);
                } else {
                    $left.find('strong').text(`Form Jemaat ke-${index + 1}`);
                    $right.append(`
                <button type="button" class="btn btn-warning btn-sm toggleCollapseBtn mr-2"><i class="fas fa-angle-up"></i></button>
                <button type="button" class="btn btn-danger btn-sm removeJemaatBtn"><i class="fas fa-times"></i> Hapus</button>
            `);
                }

                $header.append($left).append($right);
            });
        }


        $('#addJemaatBtn').click(function() {
            const totalForms = $('.jemaat-form-group').length;
            if (totalForms >= maxForm) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maksimal form tercapai!',
                    text: `Hanya bisa menambah maksimal ${maxForm} form.`,
                    timer: 1500, // 3 seconds
                    timerProgressBar: false,
                    showConfirmButton: false
                });

                return;
            }

            const $firstForm = $('.jemaat-form-group').first();
            const $clone = $firstForm.clone();

            // Reset inputs
            $clone.find('input, select').val('');
            $clone.find('.jemaat-form-body').show();
            $clone.attr('data-index', $('.jemaat-form-group').length);

            $('#jemaatContainer').append($clone);
            updateFormHeaders();
        });

        $(document).on('click', '.removeJemaatBtn', function() {
            $(this).closest('.jemaat-form-group').remove();
            updateFormHeaders();
        });

        $(document).on('click', '.toggleCollapseBtn', function() {
            const $btn = $(this);
            const $body = $btn.closest('.jemaat-form-group').find('.jemaat-form-body');

            $body.slideToggle(200, function() {
                const icon = $btn.find('i');
                icon.toggleClass('fa-angle-up fa-angle-down');
            });
        });

        updateFormHeaders();
    });
</script>