<?php
require_once 'functions/keluarga.php';

$keluarga = query("
    SELECT 
        k.*, 
        r.*, 
        COUNT(j.id_jemaat) AS total_jemaat
    FROM keluarga k
    LEFT JOIN rayon r ON k.id_rayon = r.id_rayon
    LEFT JOIN jemaat j ON k.id_keluarga = j.id_keluarga
    GROUP BY k.id_keluarga
");



if (isset($_GET['deleteKeluarga'])) {
    $data = ['id_keluarga' => $_GET['deleteKeluarga']];
    if (deleteKeluarga($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data keluarga berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }
    header("Location: index.php?page=keluarga");
    exit;
}

if (isset($_POST['updateKeluarga'])) {
    $id = $_POST['id_keluarga'];
    $data = $_POST;

    $result = updateKeluarga($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data keluarga berhasil diperbarui.');
    } elseif ($result === 'duplicate_kode_kk') {
        set_alert('warning', 'Kode KK Duplikat', 'Kode KK sudah digunakan oleh keluarga lain.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=keluarga");
    exit;
}


?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                    <h5 class="mb-0">Data Keluarga</h5>
                    <a href="index.php?page=createkeluarga" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="keluargaTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode KK</th>
                            <th>Nama Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th>Rayon</th>
                            <th>Tempat Tinggal</th>
                            <th>Jumlah Keluarga</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($keluarga as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['kode_kk']) ?></td>
                                <td><?= htmlspecialchars($row['nama_keluarga']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($row['nama_rayon']) ?></td>
                                <td><?= htmlspecialchars($row['tempat_tinggal']) ?></td>
                                <td>
                                    <?= ($row['total_jemaat'] ?? 0) > 0
                                        ? htmlspecialchars($row['total_jemaat']) . ' Orang'
                                        : '<em>Belum ada data</em>' ?>
                                </td>

                                <td>
                                    <button class="btn btn-info btn-sm detailKeluargaBtn" data-id="<?= $row['id_keluarga'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editKeluargaBtn" data-id="<?= $row['id_keluarga'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteKeluargaBtn" data-id="<?= $row['id_keluarga'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="keluargaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg " role="document">
        <div class="modal-content" id="modalKeluargaContent"></div>
    </div>
</div>

<script>
    function loadKeluargaModal(action, id = '') {
        let url = `pages/keluarga/keluarga_form.php?action=${action}`;
        if (id) url += `&id=${id}`;
        $.get(url, function(data) {
            $('#modalKeluargaContent').html(data);
            $('#keluargaModal').modal('show');
        });
    }

    $(document).on('click', '.addKeluargaBtn', function() {
        loadKeluargaModal('add');
    });

    $(document).on('click', '.editKeluargaBtn', function() {
        const id = $(this).data('id');
        loadKeluargaModal('edit', id);
    });

    $(document).on('click', '.detailKeluargaBtn', function() {
        const id = $(this).data('id');
        loadKeluargaModal('detail', id);
    });

    $(document).on('click', '.deleteKeluargaBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data keluarga akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=keluarga&deleteKeluarga=' + id;
            }
        });
    });

    $(function() {
        let table = $('#keluargaTable').DataTable({
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
                title: 'Data Keluarga',
                text: 'Cetak Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6] // sesuaikan, kolom aksi jangan disertakan
                }
            }]
        });

        // Tempatkan tombol di bagian atas
        table.buttons().container().appendTo('#keluargaTable_wrapper .col-md-6:eq(0)');
    });
</script>