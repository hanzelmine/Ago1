<?php
require_once 'database.php';
require_once 'functions/rayon.php';

if (isset($_POST['insertRayon'])) {
    $data = $_POST;

    $results = insertRayon($data); // Harus mengembalikan array hasil per entry

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
        set_alert('success', 'Berhasil Ditambahkan', "Berhasil menambahkan $success data rayon.");
    }

    if ($duplicate > 0) {
        set_alert('warning', 'Data Duplikat', "$duplicate data sudah pernah ditambahkan.");
    }

    if ($fail > 0) {
        set_alert('error', 'Gagal Menambahkan', "$fail data gagal ditambahkan.");
    }

    header("Location: index.php?page=rayon");
    exit;
}
?>

<h5>Tambah Data Rayon</h5>

<form method="POST" id="createRayonForm">
    <div id="rayonContainer">
        <div class="card card-primary rayon-form-group mb-3" data-index="0">
            <div class="card-header d-flex justify-content-between align-items-center rayon-form-header">
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_rayon_0" class="form-label required">Nama Rayon</label>
                    <input id="nama_rayon_0" type="text" name="nama_rayon[]" class="form-control capitalize-first"
                        placeholder="Contoh: Rayon 1"
                        pattern="^Rayon\s[1-9][0-9]*$"
                        title="Format harus 'Rayon' diikuti spasi dan angka, contoh: Rayon 1"
                        required>
                </div>
                <div class="form-group">
                    <label for="keterangan_0">Keterangan</label>
                    <textarea id="keterangan_0" name="keterangan[]" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-secondary btn-sm" id="addRayonBtn"><i class="fas fa-plus"></i> Tambah Form</button>
    </div>
    <div class="text-right">
        <button type="submit" name="insertRayon" class="btn btn-primary">Simpan</button>
        <a href="index.php?page=rayon" class="btn btn-secondary">Batal</a>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        $(function() {
            const maxForm = 5;

            function updateFormHeaders() {
                $(".rayon-form-group").each(function(index) {
                    const $header = $(this).find(".rayon-form-header");
                    let $left = $header.children("div").not(".card-tools").first();

                    if ($left.length === 0) {
                        $left = $('<div><strong></strong></div>');
                        $header.prepend($left);
                    }

                    $left.find("strong").text(`Form Rayon ke-${index + 1}`);

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
                containerSelector: "#rayonContainer",
                groupSelector: ".rayon-form-group",
                addBtnSelector: "#addRayonBtn",
                max: maxForm,
                updateHeaders: updateFormHeaders,
                requiredConfigs: [{
                    selector: "label.required",
                    position: "label-right"
                }]
            });

            $(document).on("click", ".rayon-form-group .btn-remove-confirm", function() {
                const $formGroup = $(this).closest(".rayon-form-group");
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