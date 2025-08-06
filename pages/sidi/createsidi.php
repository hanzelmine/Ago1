<?php
require_once 'database.php';
require_once 'functions/sidi.php'; // Ganti dengan fungsi Sidi

// Ambil data jemaat yang belum memiliki sidi
$jemaat_list = query("
    SELECT j.id_jemaat, j.nama_lengkap
    FROM jemaat j
    LEFT JOIN sidi s ON j.id_jemaat = s.id_jemaat
    WHERE s.id_jemaat IS NULL
    ORDER BY j.nama_lengkap
");

if (isset($_POST['insertSidi'])) {
    $data = $_POST;

    $result = insertSidi($data);

    if ($result === true) {
        $total = count($_POST['tempat_sidi']);
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $total data sidi.");
    } elseif ($result === 'duplicate') {
        set_alert('error', 'Gagal Menambahkan', 'ID Jemaat atau No. Surat Sidi sudah digunakan.');
    } else {
        set_alert('error', 'Gagal Menambahkan', 'Terjadi kesalahan saat menambahkan data.');
    }

    header("Location: index.php?page=sidi");
    exit;
}
?>

<h5>Tambah Data Sidi</h5>

<form method="POST" id="createSidiForm">
    <div id="sidiContainer">
        <div class="card card-success sidi-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center sidi-form-header">
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
                                Semua jemaat sudah memiliki data sidi.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tempat_sidi_0" class="form-label required">Tempat Sidi</label>
                        <input id="tempat_sidi_0" type="text" name="tempat_sidi[]" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="tanggal_sidi_0" class="form-label required">Tanggal Sidi</label>
                        <input id="tanggal_sidi_0" type="date" name="tanggal_sidi[]" class="form-control" required>
                        <small>Bulan/Tanggal/Tahun</small>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="no_surat_sidi_0" class="form-label required">No Surat Sidi</label>
                        <input id="no_surat_sidi_0" type="text" name="no_surat_sidi[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pendeta_0" class="form-label required">Pendeta</label>
                        <input id="pendeta_0" type="text" name="pendeta[]" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keterangan_0">Keterangan</label>
                    <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-sm btn-secondary" id="addSidiBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertSidi" class="btn btn-success">Simpan</button>
        <a href="index.php?page=sidi" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".sidi-form-group").each(function(index) {
                    const $header = $(this).find(".sidi-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $right.before($left);
                    }

                    $left.find("strong").text(`Form Sidi ke-${index + 1}`);

                    const $removeBtn = $right.find("button.btn-remove-confirm");
                    if (index === 0) {
                        $removeBtn.remove();
                    } else if ($removeBtn.length === 0) {
                        const removeBtn = $('<button type="button" class="btn btn-tool btn-remove-confirm"><i class="fas fa-times"></i></button>');
                        $right.append(removeBtn);
                    }
                });
            }

            // Apply required markers (gunakan config kamu sebelumnya)
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
                containerSelector: "#sidiContainer",
                groupSelector: ".sidi-form-group",
                addBtnSelector: "#addSidiBtn",
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

            $(document).on("click", ".sidi-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".sidi-form-group");
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