<?php
require_once 'functions/sidi.php';

$sidi = query("SELECT s.*, j.nama_lengkap 
               FROM sidi s 
               LEFT JOIN jemaat j ON s.id_jemaat = j.id_jemaat 
               ORDER BY created_at DESC");

if (isset($_GET['deleteSidi'])) {
    $data = $_GET['deleteSidi'];

    if (deleteSidi($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data sidi berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=sidi");
    exit;
}

if (isset($_POST['updateSidi'])) {
    $id = $_POST['id_sidi'];
    $data = $_POST;

    $result = updateSidi($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data sidi berhasil diperbarui.');
    } elseif ($result === 'duplicate') {
        set_alert('error', 'Gagal Mengupdate', 'ID Jemaat atau No. Surat Sidi sudah digunakan.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=sidi");
    exit;
}

function tanggalIndo($tanggal)
{
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];

    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));

    return "$tgl $bln $thn";
}
?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                    <h5 class="mb-0">Data Sidi</h5>
                    <a href="index.php?page=createsidi" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Sidi
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="sidiTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat Sidi</th>
                            <th>Tanggal Sidi</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($sidi as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['tempat_sidi']) ?></td>
                                <td><?= tanggalIndo(htmlspecialchars($row['tanggal_sidi'])) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailSidiBtn" data-id="<?= $row['id_sidi'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editSidiBtn" data-id="<?= $row['id_sidi'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteSidiBtn" data-id="<?= $row['id_sidi'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sidiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalSidiContent">
            <!-- AJAX Loaded Content -->
        </div>
    </div>
</div>

<script>
    function loadSidiModal(action, id = '') {
        let url = `pages/sidi/sidi_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalSidiContent').html(data);
            $('#sidiModal').modal('show');
        });
    }

    $(document).on('click', '.editSidiBtn', function() {
        const id = $(this).data('id');
        loadSidiModal('edit', id);
    });

    $(document).on('click', '.detailSidiBtn', function() {
        const id = $(this).data('id');
        loadSidiModal('detail', id);
    });

    $(document).on('click', '.deleteSidiBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=sidi&deleteSidi=' + id;
            }
        });
    });

    $(function() {
        let table = $('#sidiTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [
                [0, 'asc']
            ],
            "buttons": [{
                extend: 'excel',
                title: 'Data Sidi',
                text: 'Cetak Data Sidi <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3],
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });

        table.buttons().container().appendTo('#sidiTable_wrapper .col-md-6:eq(0)');
    });
</script>