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


<h5>Tambah Data Jemaat</h5>

<form method="POST" id="createJemaatForm">
    <div id="jemaatContainer">
        <div class="card card-primary jemaat-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center jemaat-form-header">

                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <!-- No remove button on first form -->
                </div>
            </div>

            <div class="card-body jemaat-form-body">
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="nama_lengkap_0" class="form-label required">Nama Lengkap</label>
                        <input id="nama_lengkap_0" type="text" name="nama_lengkap[]" class="form-control capitalize-first" pattern="[A-Za-z\s\.\,\-]+" title="Hanya huruf, spasi, titik, koma, dan tanda hubung. Setiap kata harus diawali huruf kapital" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="id_keluarga_0" class="form-label select2-required required">Kode Kepala Keluarga</label>
                        <select id="id_keluarga_0" name="id_keluarga[]" class="form-control  select2" required>
                            <option value="">- Pilih Kode KK -</option>
                            <?php foreach ($keluarga_list as $k): ?>
                                <option value="<?= $k['id_keluarga'] ?>"><?= htmlspecialchars($k['kode_kk']) ?> - <?= htmlspecialchars($k['nama_keluarga']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="jenis_kelamin_0" class="form-label required">Jenis Kelamin</label>
                        <select id="jenis_kelamin_0" name="jenis_kelamin[]" class="form-control" required>
                            <option value="">- Pilih -</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="tempat_lahir_0" class="form-label required">Tempat Lahir</label>
                        <input id="tempat_lahir_0" type="text" name="tempat_lahir[]" class="form-control capitalize-first" pattern="([A-Z][a-z]*(\s)?)+" title="Hanya boleh huruf dan spasi. dan Setiap kata harus diawali huruf kapital" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tanggal_lahir_0" class="form-label required">Tanggal Lahir</label>
                        <input id="tanggal_lahir_0" type="date" name="tanggal_lahir[]" class="form-control" required>
                        <small>Bulan/Hari/Tahun</small>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status_dlm_keluarga_0" class="form-label required">Status dalam Keluarga</label>
                        <input id="status_dlm_keluarga_0" type="text" name="status_dlm_keluarga[]" class="form-control capitalize-first" pattern="([A-Z][a-z]*(\s)?)+" title="Hanya boleh huruf dan spasi. dan Setiap kata harus diawali huruf kapital" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status_jemaat_0" class="form-label required">Status Jemaat</label>
                        <input
                            id="status_jemaat_0"
                            type="text"
                            name="status_jemaat[]"
                            class="form-control capitalize-first"
                            pattern="([A-Z][a-z]*(\s)?)+"
                            title="Setiap kata harus diawali huruf kapital, contoh: Aktif, Pindah, Meninggal"
                            placeholder="Aktif/Pindah/Meninggal/dll"
                            required>

                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="status_baptis_0" class="form-label required">Status Baptis</label>
                        <select id="status_baptis_0" name="status_baptis[]" class="form-control" required>
                            <option value="">-- Pilih Status Baptis --</option>
                            <option value="Sudah Baptis">Sudah Baptis</option>
                            <option value="Belum Baptis">Belum Baptis</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status_sidi_0" class="form-label required">Status Sidi</label>
                        <select id="status_sidi_0" name="status_sidi[]" class="form-control" required>
                            <option value="">-- Pilih Status Sidi --</option>
                            <option value="Sudah Sidi">Sudah Sidi</option>
                            <option value="Belum Sidi">Belum Sidi</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="status_perkawinan_0" class="form-label required">Status Pernikahan</label>
                        <select id="status_perkawinan_0" name="status_perkawinan[]" class="form-control" required>
                            <option value="">-- Pilih Status Pernikahan --</option>
                            <option value="Sudah Menikah">Sudah Menikah</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="pendidikan_terakhir_0">Pendidikan Terakhir</label>
                        <input id="pendidikan_terakhir_0" type="text" name="pendidikan_terakhir[]" class="form-control capitalize-first">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pekerjaan_0">Pekerjaan</label>
                        <input id="pekerjaan_0" type="text" name="pekerjaan[]" class="form-control capitalize-first" pattern="[A-Za-z\s]+" title="Hanya boleh huruf dan spasi">
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
    <div class="text-right">
        <button type="submit" name="insertJemaat" class="btn btn-primary">Simpan</button>
        <a href="index.php?page=jemaat" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                const groups = $(".jemaat-form-group");
                groups.each(function(index) {
                    const $header = $(this).find(".jemaat-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        if ($right.length) {
                            $right.before($left);
                        } else {
                            $header.append($left);
                        }
                    }

                    let titleText = "";
                    if (index === 0 && groups.length === 1) {
                        titleText = `Form Jemaat`;
                    } else if (index === 0) {
                        titleText = `Form Jemaat ke-1`;
                    } else {
                        titleText = `Form Jemaat ke-${index + 1}`;
                    }
                    $left.find("strong").text(titleText);

                    // Show remove button only if index > 0
                    const $removeBtn = $right.find("button.btn-remove-confirm");

                    if (index === 0) {
                        if ($removeBtn.length) $removeBtn.remove();
                    } else {
                        if ($removeBtn.length === 0) {
                            const removeBtn = $(`
                                <button type="button" class="btn btn-tool btn-remove-confirm">
                                 <i class="fas fa-times"></i>
                                </button>

                            `);
                            $right.append(removeBtn);
                        }
                    }
                });
            }

            // Apply required markers on the initial form(s) once on page load
            applyRequiredMarkers(document, [{
                    selector: "label.required",
                    position: "label-right"
                },
                {
                    selector: ".form-group.required",
                    position: "above"
                },
                {
                    selector: "label.required:not(.select2-required)",
                    position: "label-right"
                },
                {
                    selector: ".form-group.required.select2-required",
                    position: "above"
                },
                {
                    selector: ".form-group.required:not(.select2-required)",
                    position: "above"
                },
            ]);

            // Setup cloning with your clone function
            cloneFormGroup({
                containerSelector: "#jemaatContainer",
                groupSelector: ".jemaat-form-group",
                addBtnSelector: "#addJemaatBtn",
                max: maxForm,
                updateHeaders: updateFormHeaders,
                requiredConfigs: [{
                        selector: "label.required",
                        position: "label-right"
                    },
                    {
                        selector: ".form-group.required",
                        position: "above"
                    },
                    {
                        selector: "label.required:not(.select2-required)",
                        position: "label-right"
                    },
                    {
                        selector: ".form-group.required.select2-required",
                        position: "above"
                    },
                    {
                        selector: ".form-group.required:not(.select2-required)",
                        position: "above"
                    },
                ],
            });

            // Remove button handler (delegated) with confirmAction
            $(document).on("click", ".jemaat-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".jemaat-form-group");

                confirmAction({
                    title: "Yakin ingin menghapus form ini?",
                    text: "Data dalam form ini akan hilang jika dihapus.",
                    icon: "warning",
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                    callback: () => {
                        $formGroup.remove();
                        updateFormHeaders();

                        Swal.fire({
                            icon: "success",
                            title: "Terhapus!",
                            text: "Form telah berhasil dihapus.",
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    },
                });
            });



            // Initial header update
            updateFormHeaders();
        });
    });
</script>