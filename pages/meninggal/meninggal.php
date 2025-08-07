<?php
require_once 'functions/meninggal.php';

$meninggal = query("SELECT m.*, j.nama_lengkap 
                    FROM meninggal m 
                    LEFT JOIN jemaat j ON m.id_jemaat = j.id_jemaat 
                    ORDER BY created_at DESC");

if (isset($_GET['deleteMeninggal'])) {
    $data = $_GET['deleteMeninggal'];

    if (deleteMeninggal($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data meninggal berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=meninggal");
    exit;
}

if (isset($_POST['updateMeninggal'])) {
    $id = $_POST['id_meninggal'];
    $data = $_POST;

    $result = updateMeninggal($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data meninggal berhasil diperbarui.');
    } elseif ($result === 'duplicate') {
        set_alert('error', 'Gagal Mengupdate', 'ID Jemaat sudah digunakan dalam data meninggal.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=meninggal");
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
                    <h5 class="mb-0">Data Meninggal</h5>
                    <a href="index.php?page=createmeninggal" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="meninggalTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat Meninggal</th>
                            <th>Tanggal Meninggal</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($meninggal as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['tempat_meninggal']) ?></td>
                                <td><?= tanggalIndo(htmlspecialchars($row['tanggal_meninggal'])) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailMeninggalBtn" data-id="<?= $row['id_meninggal'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editMeninggalBtn" data-id="<?= $row['id_meninggal'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteMeninggalBtn" data-id="<?= $row['id_meninggal'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="meninggalModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalMeninggalContent">
            <!-- AJAX Loaded Content -->
        </div>
    </div>
</div>

<script>
    function loadMeninggalModal(action, id = '') {
        let url = `pages/meninggal/meninggal_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalMeninggalContent').html(data);
            $('#meninggalModal').modal('show');
        });
    }

    $(document).on('click', '.editMeninggalBtn', function() {
        const id = $(this).data('id');
        loadMeninggalModal('edit', id);
    });

    $(document).on('click', '.detailMeninggalBtn', function() {
        const id = $(this).data('id');
        loadMeninggalModal('detail', id);
    });

    $(document).on('click', '.deleteMeninggalBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=meninggal&deleteMeninggal=' + id;
            }
        });
    });

    $(function() {
        let table = $('#meninggalTable').DataTable({
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
                title: 'Data Meninggal',
                text: 'Cetak Data Meninggal <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3],
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });

        table.buttons().container().appendTo('#meninggalTable_wrapper .col-md-6:eq(0)');
    });
</script>