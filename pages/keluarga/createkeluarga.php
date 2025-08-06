<?php
require_once 'database.php';
require_once 'functions/keluarga.php';

// Ambil data rayon untuk dropdown
$rayon_list = query("SELECT id_rayon, nama_rayon FROM rayon ORDER BY nama_rayon");

if (isset($_POST['insertKeluarga'])) {
    $data = $_POST;

    $results = insertKeluarga($data); // Harus mengembalikan array hasil per entry

    // Pastikan $results selalu array
    if (!is_array($results)) {
        $results = [$results];
    }

    $success = 0;
    $duplicate = 0;
    $fail = 0;

    foreach ($results as $res) {
        if ($res === true) {
            $success++;
        } elseif ($res === 'duplicate_kode_kk') {
            $duplicate++;
        } else {
            $fail++;
        }
    }

    if ($success > 0) {
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data keluarga.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data sudah pernah ditambahkan.");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan.");
    }

    header("Location: index.php?page=keluarga");
    exit;
}
?>

<h5>Tambah Data Keluarga</h5>

<form method="POST" id="createKeluargaForm">
    <div id="keluargaContainer">
        <div class="card card-primary keluarga-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center keluarga-form-header">
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kode_kk_0" class="form-label required">Kode KK</label>
                            <input type="text" id="kode_kk_0" name="kode_kk[]" pattern="^KK[0-9]{4}$" placeholder="Contoh : KK0001" maxlength="6" title="Format harus 'KK' diikuti 4 angka, contoh: KK0001" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_keluarga_0" class="form-label required">Nama Keluarga</label>
                            <input type="text" id="nama_keluarga_0" name="nama_keluarga[]" class="form-control capitalize-first" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tempat_tinggal_0">Tempat Tinggal</label>
                            <input type="text" id="tempat_tinggal_0" name="tempat_tinggal[]" class="form-control capitalize-first">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_rayon_0" class="form-label required">Rayon</label>
                            <select id="id_rayon_0" name="id_rayon[]" class="form-control" required>
                                <option value="" disabled selected>-- Pilih Rayon --</option>
                                <?php foreach ($rayon_list as $r): ?>
                                    <option value="<?= $r['id_rayon'] ?>"><?= htmlspecialchars($r['nama_rayon']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>





                <div class="form-group">
                    <label for="alamat_0">Alamat</label>
                    <textarea id="alamat_0" name="alamat[]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-secondary btn-sm" id="addKeluargaBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertKeluarga" class="btn btn-primary">Simpan</button>
        <a href="index.php?page=keluarga" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".keluarga-form-group").each(function(index) {
                    const $header = $(this).find(".keluarga-form-header");
                    let $left = $header.children("div").not(".card-tools").first();

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $header.prepend($left);
                    }

                    $left.find("strong").text(`Form Keluarga ke-${index + 1}`);

                    const $removeBtn = $header.find("button.btn-remove-confirm");
                    if (index === 0) {
                        $removeBtn.remove();
                    } else if ($removeBtn.length === 0) {
                        const removeBtn = $('<button type="button" class="btn btn-tool btn-remove-confirm"><i class="fas fa-times"></i></button>');
                        $header.find(".card-tools").append(removeBtn);
                    }
                });
            }

            applyRequiredMarkers(document, [{
                selector: "label.required",
                position: "label-right"
            }]);

            cloneFormGroup({
                containerSelector: "#keluargaContainer",
                groupSelector: ".keluarga-form-group",
                addBtnSelector: "#addKeluargaBtn",
                max: maxForm,
                updateHeaders: updateFormHeaders,
                requiredConfigs: [{
                    selector: "label.required",
                    position: "label-right"
                }]
            });

            $(document).on("click", ".keluarga-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".keluarga-form-group");
                confirmAction({
                    title: "Hapus form ini?",
                    text: "Data akan dihapus permanen.",
                    icon: "warning",
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                    callback: () => {
                        $formGroup.remove();
                        updateFormHeaders();
                    }
                });
            });

            updateFormHeaders();
        });
    });
</script>