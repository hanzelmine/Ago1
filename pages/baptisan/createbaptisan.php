<?php
require_once 'database.php';
require_once 'functions/baptisan.php'; // Pastikan ada file function sesuai

// Ambil data jemaat untuk dropdown
$jemaat_list = query("
    SELECT j.id_jemaat, j.nama_lengkap
    FROM jemaat j
    LEFT JOIN baptisan b ON j.id_jemaat = b.id_jemaat
    WHERE b.id_jemaat IS NULL
    ORDER BY j.nama_lengkap
");


if (isset($_POST['insertBaptisan'])) {
    $data = $_POST;

    $results = insertBaptisan($data);

    $success = 0;
    $duplicate = 0;
    $fail = 0;

    foreach ($results as $res) {
        if ($res === true) {
            $success++;
        } elseif ($res === 'duplicate') {
            $duplicate++;
        } else {
            $fail++;
        }
    }

    if ($success > 0) {
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data baptisan.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data duplikat (id jemaat atau no surat).");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan karena kesalahan sistem.");
    }

    header("Location: index.php?page=baptisan");
    exit;
}

?>

<h5>Tambah Data Baptisan</h5>

<form method="POST" id="createBaptisanForm">
    <div id="baptisanContainer">
        <div class="card card-primary baptisan-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center baptisan-form-header">
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_jemaat_0" class="form-label required">Nama Jemaat</label>
                        <select id="id_jemaat_0" name="id_jemaat[]" class="form-control select2" required>
                            <option value="">- Pilih Jemaat -</option>
                            <?php foreach ($jemaat_list as $j): ?>
                                <option value="<?= $j['id_jemaat'] ?>"><?= htmlspecialchars($j['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (count($jemaat_list) === 0): ?>
                            <div class="alert alert-warning mt-2">
                                Semua jemaat sudah memiliki data baptisan.
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="form-group col-md-6">
                        <label for="tempat_baptis_0" class="form-label required">Tempat Baptis</label>
                        <input id="tempat_baptis_0" type="text" name="tempat_baptis[]" class="form-control capitalize-first" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="tanggal_baptis_0" class="form-label required">Tanggal Baptis</label>
                        <input id="tanggal_baptis_0" type="date" name="tanggal_baptis[]" class="form-control" required>
                        <small>Bulan/Tanggal/Tahun</small>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="no_surat_baptis_0" class="form-label required">No Surat Baptis</label>
                        <input id="no_surat_baptis_0" type="text" name="no_surat_baptis[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pendeta_0" class="form-label required">Pendeta</label>
                        <input id="pendeta_0" type="text" name="pendeta[]" class="form-control capitalize-first" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keterangan_0">Keterangan</label>
                    <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-sm btn-secondary" id="addBaptisanBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertBaptisan" class="btn btn-primary">Simpan</button>
        <a href="index.php?page=baptisan" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".baptisan-form-group").each(function(index) {
                    const $header = $(this).find(".baptisan-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $right.before($left);
                    }

                    $left.find("strong").text(`Form Baptisan ke-${index + 1}`);

                    const $removeBtn = $right.find("button.btn-remove-confirm");
                    if (index === 0) {
                        $removeBtn.remove();
                    } else if ($removeBtn.length === 0) {
                        const removeBtn = $('<button type="button" class="btn btn-tool btn-remove-confirm"><i class="fas fa-times"></i></button>');
                        $right.append(removeBtn);
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

            cloneFormGroup({
                containerSelector: "#baptisanContainer",
                groupSelector: ".baptisan-form-group",
                addBtnSelector: "#addBaptisanBtn",
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

            $(document).on("click", ".baptisan-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".baptisan-form-group");
                confirmAction({
                    title: "Hapus form ini?",
                    text: "Data akan dihapus permanen.",
                    icon: "warning",
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                    callback: () => {
                        $formGroup.remove();
                        updateFormHeaders();
                    },
                });
            });

            updateFormHeaders();
        });
    });
</script>