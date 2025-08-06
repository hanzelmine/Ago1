<?php
require_once 'database.php';
require_once 'functions/meninggal.php';

// Ambil jemaat yang belum memiliki data meninggal
$jemaat_list = query("
    SELECT j.id_jemaat, j.nama_lengkap
    FROM jemaat j
    LEFT JOIN meninggal m ON j.id_jemaat = m.id_jemaat
    WHERE m.id_jemaat IS NULL
      AND j.status_jemaat IN ('Aktif', 'Meninggal')
    ORDER BY j.nama_lengkap
");


if (isset($_POST['insertMeninggal'])) {
    $data = $_POST;

    $results = insertMeninggal($data); // Assume this returns an array per record

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
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data meninggal.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data sudah pernah ditambahkan.");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan.");
    }

    header("Location: index.php?page=meninggal");
    exit;
}
?>

<h5>Tambah Data Meninggal</h5>

<form method="POST" id="createMeninggalForm">
    <div id="meninggalContainer">
        <div class="card card-danger meninggal-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center meninggal-form-header">
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
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_meninggal_0" class="form-label required">Tanggal Meninggal</label>
                        <input id="tanggal_meninggal_0" type="date" name="tanggal_meninggal[]" class="form-control" required>
                        <small>Bulan/Hari/Tahun</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="tempat_meninggal_0" class="form-label required">Tempat Meninggal</label>
                        <input id="tempat_meninggal_0" type="text" name="tempat_meninggal[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sebab_meninggal_0" class="form-label">Penyebab</label>
                        <input id="sebab_meninggal_0" type="text" name="sebab_meninggal[]" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="keterangan_0">Keterangan Tambahan</label>
                    <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-secondary btn-sm" id="addMeninggalBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertMeninggal" class="btn btn-danger">Simpan</button>
        <a href="index.php?page=meninggal" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".meninggal-form-group").each(function(index) {
                    const $header = $(this).find(".meninggal-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $right.before($left);
                    }

                    $left.find("strong").text(`Form Meninggal ke-${index + 1}`);

                    const $removeBtn = $right.find("button.btn-remove-confirm");
                    if (index === 0) {
                        $removeBtn.remove();
                    } else if ($removeBtn.length === 0) {
                        const removeBtn = $('<button type="button" class="btn btn-tool btn-remove-confirm"><i class="fas fa-times"></i></button>');
                        $right.append(removeBtn);
                    }
                });
            }

            // Terapkan required markers
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
                }
            ]);

            // Cloning form dinamis
            cloneFormGroup({
                containerSelector: "#meninggalContainer",
                groupSelector: ".meninggal-form-group",
                addBtnSelector: "#addMeninggalBtn",
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
                    }
                ]
            });

            // Tombol hapus form
            $(document).on("click", ".meninggal-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".meninggal-form-group");
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