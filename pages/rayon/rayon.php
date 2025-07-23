<?php
require_once 'functions/rayon.php';

// Ambil data dari tabel 'rayon'
$rayon = query("
    SELECT 
        r.*, 
        COUNT(k.id_keluarga) AS total_keluarga
    FROM 
        rayon r
    LEFT JOIN 
        keluarga k ON k.id_rayon = r.id_rayon
    GROUP BY 
        r.id_rayon
");


if (isset($_GET['deleteRayon'])) {
    $data = [
        'id_rayon' => $_GET['deleteRayon']
    ];

    if (deleteRayon($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data rayon berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=rayon");
    exit;
}

if (isset($_POST['insertRayon'])) {
    $result = insertRayon($_POST);
    if ($result === true) {
        set_alert('success', 'Berhasil Ditambahkan', 'Data rayon berhasil ditambahkan.');
    } elseif ($result === 'duplicate_nama_rayon') {
        set_alert('warning', 'Nama Rayon Duplikat', 'Nama rayon sudah digunakan. Silakan gunakan nama lain.');
    } else {
        set_alert('error', 'Gagal Menambahkan', 'Terjadi kesalahan saat menambahkan data.');
    }
    header("Location: index.php?page=rayon");
    exit;
}

if (isset($_POST['updateRayon'])) {
    $result = updateRayon($_POST['id_rayon'], $_POST);
    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data rayon berhasil diperbarui.');
    } elseif ($result === 'duplicate_nama_rayon') {
        set_alert('warning', 'Nama Rayon Duplikat', 'Nama rayon sudah digunakan. Silakan gunakan nama lain.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }
    header("Location: index.php?page=rayon");
    exit;
}

?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header ">
                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                    <h5 class="mb-0">Data Rayon</h5>
                    <button class="btn btn-primary btn-sm addRayonBtn">
                        <i class="fas fa-plus"></i> Tambah Rayon
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="rayonTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Rayon</th>
                            <th>Keterangan</th>
                            <th>Total KK</th>
                            <th width="75">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($rayon as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_rayon']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td><?= ($row['total_keluarga'] ?? 0) == 0 ? '<em>Belum ada data</em>' : htmlspecialchars($row['total_keluarga']) . ' KK' ?></td>

                                <td>
                                    <button class="btn btn-success btn-sm editRayonBtn" data-id="<?= $row['id_rayon'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteRayonBtn" data-id="<?= $row['id_rayon'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- Modal -->
<div class="modal fade" id="rayonModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content " id="modalRayonContent">
            <!-- Isi dari AJAX -->
        </div>
    </div>
</div>

<script>
    function loadRayonModal(action, id = '') {
        let url = `pages/rayon/rayon_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalRayonContent').html(data);
            $('#rayonModal').modal('show');
        });
    }

    $(document).on('click', '.addRayonBtn', function() {
        loadRayonModal('add');
    });

    $(document).on('click', '.editRayonBtn', function() {
        const id = $(this).data('id');
        loadRayonModal('edit', id);
    });

    // OPTIONAL: Untuk detail jika butuh
    // $(document).on('click', '.detailRayonBtn', function() {
    //     const id = $(this).data('id');
    //     loadRayonModal('detail', id);
    // });
</script>


<script>
    $(document).on('click', '.deleteRayonBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=rayon&deleteRayon=' + id;
            }
        });
    });
</script>



<script>
    $(function() {
        let table = $('#rayonTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "pageLength": 5,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [
                [0, 'asc']
            ], // ubah sesuai kolom pertama kamu sekarang
            "buttons": [{
                extend: 'excel',
                title: 'Data Rayon',
                text: 'Cetak Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3] // sesuaikan, kolom aksi jangan disertakan
                }
            }]
        });

        // Tempatkan tombol di bagian atas
        table.buttons().container().appendTo('#rayonTable_wrapper .col-md-6:eq(0)');
    });
</script>