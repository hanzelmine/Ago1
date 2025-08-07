<?php
require_once 'functions/atestasi.php';

$atestasi = query("SELECT a.*, j.nama_lengkap 
                   FROM atestasi a 
                   LEFT JOIN jemaat j ON a.id_jemaat = j.id_jemaat 
                   ORDER BY a.created_at DESC");

if (isset($_GET['deleteAtestasi'])) {
    $data = $_GET['deleteAtestasi'];

    if (deleteAtestasi($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data atestasi berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=atestasi");
    exit;
}

if (isset($_POST['updateAtestasi'])) {
    $id = $_POST['id_atestasi'];
    $data = $_POST;

    $result = updateAtestasi($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data atestasi berhasil diperbarui.');
    } elseif ($result === 'duplicate') {
        set_alert('error', 'Gagal Mengupdate', 'ID Jemaat sudah digunakan dalam data atestasi.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=atestasi");
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
                    <h5 class="mb-0">Data Atestasi</h5>
                    <a href="index.php?page=createatestasi" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="atestasiTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Atestasi</th>
                            <th>Gereja Asal/Tujuan</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($atestasi as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_atestasi']) ?></td>
                                <td><?= htmlspecialchars($row['gereja_asal_tujuan']) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailAtestasiBtn" data-id="<?= $row['id_atestasi'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editAtestasiBtn" data-id="<?= $row['id_atestasi'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteAtestasiBtn" data-id="<?= $row['id_atestasi'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="atestasiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalAtestasiContent">
            <!-- AJAX Loaded Content -->
        </div>
    </div>
</div>

<script>
    function loadAtestasiModal(action, id = '') {
        let url = `pages/atestasi/atestasi_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalAtestasiContent').html(data);
            $('#atestasiModal').modal('show');
        });
    }

    $(document).on('click', '.editAtestasiBtn', function() {
        const id = $(this).data('id');
        loadAtestasiModal('edit', id);
    });

    $(document).on('click', '.detailAtestasiBtn', function() {
        const id = $(this).data('id');
        loadAtestasiModal('detail', id);
    });

    $(document).on('click', '.deleteAtestasiBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=atestasi&deleteAtestasi=' + id;
            }
        });
    });

    $(function() {
        let table = $('#atestasiTable').DataTable({
            paging: true,
            lengthChange: false,
            pageLength: 10,
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
                title: 'Data Atestasi',
                text: 'Cetak Data Atestasi <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3],
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });

        table.buttons().container().appendTo('#atestasiTable_wrapper .col-md-6:eq(0)');
    });
</script>