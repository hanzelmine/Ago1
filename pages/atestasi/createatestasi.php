<?php
require_once 'database.php';
require_once 'functions/atestasi.php';

// Ambil jemaat yang belum memiliki data atestasi
$jemaat_list = query("
    SELECT j.id_jemaat, j.nama_lengkap
    FROM jemaat j
    LEFT JOIN atestasi a ON j.id_jemaat = a.id_jemaat
    WHERE a.id_jemaat IS NULL
      AND j.status_jemaat = 'Aktif'
    ORDER BY j.nama_lengkap
");

if (isset($_POST['insertAtestasi'])) {
    $data = $_POST;

    $results = insertAtestasi($data);

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
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data atestasi.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data sudah pernah ditambahkan.");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan.");
    }

    header("Location: index.php?page=atestasi");
    exit;
}
?>

<h5>Tambah Data Atestasi</h5>

<form method="POST" id="createAtestasiForm">
    <div id="atestasiContainer">
        <div class="card card-primary atestasi-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center atestasi-form-header">
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
                        <label for="jenis_atestasi_0" class="form-label required">Jenis Atestasi</label>
                        <select id="jenis_atestasi_0" name="jenis_atestasi[]" class="form-control" required>
                            <option value="">- Pilih Jenis -</option>
                            <option value="Masuk">Masuk</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gereja_asal_tujuan_0" class="form-label required">Gereja Asal/Tujuan</label>
                        <input id="gereja_asal_tujuan_0" type="text" name="gereja_asal_tujuan[]" class="form-control capitalize-first" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="keterangan_0">Keterangan Tambahan</label>
                        <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-secondary btn-sm" id="addAtestasiBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertAtestasi" class="btn btn-primary">Simpan</button>
        <a href="index.php?page=atestasi" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".atestasi-form-group").each(function(index) {
                    const $header = $(this).find(".atestasi-form-header");
                    let $left = $header.children("div").not(".card-tools").first();
                    const $right = $header.find(".card-tools");

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $right.before($left);
                    }

                    $left.find("strong").text(`Form Atestasi ke-${index + 1}`);

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

            cloneFormGroup({
                containerSelector: "#atestasiContainer",
                groupSelector: ".atestasi-form-group",
                addBtnSelector: "#addAtestasiBtn",
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

            $(document).on("click", ".atestasi-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".atestasi-form-group");
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