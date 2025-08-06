<?php
require_once 'database.php';
require_once 'functions/pernikahan.php';

// Ambil data jemaat (jika perlu filter yang belum menikah, sesuaikan query-nya)
$suami_list = query("SELECT id_jemaat, nama_lengkap FROM jemaat 
                    WHERE jenis_kelamin = 'Laki-laki' AND id_jemaat NOT IN (
                        SELECT id_suami FROM pernikahan
                    ) ORDER BY nama_lengkap");
$istri_list = query("SELECT id_jemaat, nama_lengkap FROM jemaat 
                    WHERE jenis_kelamin = 'Perempuan' AND id_jemaat NOT IN (
                        SELECT id_istri FROM pernikahan
                    ) ORDER BY nama_lengkap");


if (isset($_POST['insertPernikahan'])) {
    $data = $_POST;

    $results = insertPernikahan($data); // Function should return array of results like insertSidi

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
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data pernikahan.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data duplikat (pasangan atau no surat).");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan karena kesalahan sistem.");
    }

    header("Location: index.php?page=pernikahan");
    exit;
}
?>

<h5>Tambah Data Pernikahan</h5>

<form method="POST" id="createPernikahanForm">
    <div id="pernikahanContainer">
        <div class="card card-success pernikahan-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center pernikahan-form-header">
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="id_suami_0" class="form-label required">Nama Suami</label>
                        <select id="id_suami_0" name="id_suami[]" class="form-control select2" required>
                            <option value="">- Pilih Jemaat Laki-laki -</option>
                            <?php foreach ($suami_list as $j): ?>
                                <option value="<?= $j['id_jemaat'] ?>"><?= htmlspecialchars($j['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="id_istri_0" class="form-label required">Nama Istri</label>
                        <select id="id_istri_0" name="id_istri[]" class="form-control select2" required>
                            <option value="">- Pilih Jemaat Perempuan -</option>
                            <?php foreach ($istri_list as $j): ?>
                                <option value="<?= $j['id_jemaat'] ?>"><?= htmlspecialchars($j['nama_lengkap']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="tanggal_nikah_0" class="form-label required">Tanggal Pernikahan</label>
                        <input id="tanggal_nikah_0" type="date" name="tanggal_nikah[]" class="form-control" required>
                        <small>Bulan/Tanggal/Tahun</small>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tempat_nikah_0" class="form-label required">Tempat Pernikahan</label>
                        <input id="tempat_nikah_0" type="text" name="tempat_nikah[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pendeta_0" class="form-label required">Pendeta</label>
                        <input id="pendeta_0" type="text" name="pendeta[]" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="no_surat_nikah_0" class="form-label required">No Surat Nikah</label>
                    <input id="no_surat_nikah_0" type="text" name="no_surat_nikah[]" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="keterangan_0">Keterangan</label>
                    <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-sm btn-secondary" id="addPernikahanBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertPernikahan" class="btn btn-success">Simpan</button>
        <a href="index.php?page=pernikahan" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".pernikahan-form-group").each(function(index) {
                    const $header = $(this).find(".pernikahan-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $right.before($left);
                    }

                    $left.find("strong").text(`Form Pernikahan ke-${index + 1}`);

                    const $removeBtn = $right.find("button.btn-remove-confirm");
                    if (index === 0) {
                        $removeBtn.remove();
                    } else if ($removeBtn.length === 0) {
                        const removeBtn = $('<button type="button" class="btn btn-tool btn-remove-confirm"><i class="fas fa-times"></i></button>');
                        $right.append(removeBtn);
                    }
                });
            }

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
                containerSelector: "#pernikahanContainer",
                groupSelector: ".pernikahan-form-group",
                addBtnSelector: "#addPernikahanBtn",
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

            $(document).on("click", ".pernikahan-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".pernikahan-form-group");
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