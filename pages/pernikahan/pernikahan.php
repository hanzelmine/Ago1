<?php
require_once 'functions/pernikahan.php';

$pernikahan = query("SELECT p.*, j1.nama_lengkap AS nama_suami, j2.nama_lengkap AS nama_istri 
                     FROM pernikahan p 
                     LEFT JOIN jemaat j1 ON p.id_suami = j1.id_jemaat 
                     LEFT JOIN jemaat j2 ON p.id_istri = j2.id_jemaat 
                     ORDER BY p.created_at DESC");

if (isset($_POST['updatePernikahan'])) {
    $id = $_POST['id_pernikahan'];
    $data = $_POST;

    $result = updatePernikahan($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data pernikahan berhasil diperbarui.');
    } elseif ($result === 'duplicate_surat') {
        set_alert('error', 'Gagal Mengupdate', 'No. Surat Nikah sudah digunakan.');
    } elseif ($result === 'duplicate_pair') {
        set_alert('error', 'Gagal Mengupdate', 'Pasangan suami dan istri ini sudah terdaftar.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=pernikahan");
    exit;
}


if (isset($_GET['deletePernikahan'])) {
    $id = $_GET['deletePernikahan'];

    if (deletePernikahan($id)) {
        set_alert('success', 'Berhasil Dihapus', 'Data pernikahan berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=pernikahan");
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
                    <h5 class="mb-0">Data Pernikahan</h5>
                    <a href="index.php?page=createpernikahan" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Pernikahan
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="pernikahanTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Suami</th>
                            <th>Nama Istri</th>
                            <th>Tanggal Pernikahan</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($pernikahan as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_suami']) ?></td>
                                <td><?= htmlspecialchars($row['nama_istri']) ?></td>
                                <td><?= tanggalIndo($row['tanggal_nikah']) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailPernikahanBtn" data-id="<?= $row['id_pernikahan'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editPernikahanBtn" data-id="<?= $row['id_pernikahan'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deletePernikahanBtn" data-id="<?= $row['id_pernikahan'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pernikahanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalPernikahanContent">
            <!-- AJAX Loaded -->
        </div>
    </div>
</div>

<script>
    function loadPernikahanModal(action, id = '') {
        let url = `pages/pernikahan/pernikahan_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalPernikahanContent').html(data);
            $('#pernikahanModal').modal('show');
        });
    }

    $(document).on('click', '.editPernikahanBtn', function() {
        const id = $(this).data('id');
        loadPernikahanModal('edit', id);
    });

    $(document).on('click', '.detailPernikahanBtn', function() {
        const id = $(this).data('id');
        loadPernikahanModal('detail', id);
    });

    $(document).on('click', '.deletePernikahanBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=pernikahan&deletePernikahan=' + id;
            }
        });
    });

    $(function() {
        let table = $('#pernikahanTable').DataTable({
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            order: [
                [0, 'asc']
            ],
            buttons: [{
                extend: 'excel',
                title: 'Data Pernikahan',
                text: 'Cetak Data Pernikahan <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3],
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });

        table.buttons().container().appendTo('#pernikahanTable_wrapper .col-md-6:eq(0)');
    });
</script>