<?php
require_once 'functions/baptisan.php';

$baptisan = query("SELECT b.*, j.nama_lengkap FROM baptisan b LEFT JOIN jemaat j ON b.id_jemaat = j.id_jemaat ORDER BY created_at DESC");


if (isset($_GET['deleteBaptisan'])) {
    $data = $_GET['deleteBaptisan'];

    if (deleteBaptisan($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data baptisan berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=baptisan");
    exit;
}

if (isset($_POST['updateBaptisan'])) {
    $id = $_POST['id_baptisan'];
    $data = $_POST;

    $result = updateBaptisan($id, $data);

    if ($result === true) {
        set_alert('success', 'Berhasil Diupdate', 'Data baptisan berhasil diperbarui.');
    } elseif ($result === 'duplicate') {
        set_alert('error', 'Gagal Mengupdate', 'ID Jemaat atau No. Surat Baptis sudah digunakan.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=baptisan");
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
                    <h5 class="mb-0">Data Baptisan</h5>
                    <a href="index.php?page=createbaptisan" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Baptisan
                    </a>

                </div>
            </div>
            <div class="card-body">
                <table id="baptisanTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat Baptis</th>
                            <th>Tanggal Baptis</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1;
                        foreach ($baptisan as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['tempat_baptis']) ?></td>
                                <td><?= tanggalIndo(htmlspecialchars($row['tanggal_baptis'])) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailBaptisanBtn" data-id="<?= $row['id_baptisan'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editBaptisanBtn" data-id="<?= $row['id_baptisan'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteBaptisanBtn" data-id="<?= $row['id_baptisan'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="baptisanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalBaptisanContent">
            <!-- AJAX Loaded Content -->
        </div>
    </div>
</div>

<script>
    // Modal Function edit, detail (remove add)
    function loadJemaatModal(action, id = '') {
        let url = `pages/baptisan/baptisan_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalBaptisanContent').html(data);
            $('#baptisanModal').modal('show');
        });
    }
    $(document).on('click', '.editBaptisanBtn', function() {
        const id = $(this).data('id');
        loadJemaatModal('edit', id);
    });

    $(document).on('click', '.detailBaptisanBtn', function() {
        const id = $(this).data('id');
        loadJemaatModal('detail', id);
    });


    // delete alert sweetalert2
    $(document).on('click', '.deleteBaptisanBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=baptisan&deleteBaptisan=' + id;
            }
        });
    });

    // Table definition
    $(function() {
        let table = $('#baptisanTable').DataTable({
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
            "columnDefs": [{
                "targets": [], // index kolom tersembunyi
                "visible": false,
                "searchable": false
            }],
            "buttons": [{
                extend: 'excel',
                title: 'Data Jemaat',
                text: 'Cetak Data Baptisan <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3, ], // tanpa aksi
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });


        // Tempatkan tombol di bagian atas
        table.buttons().container().appendTo('#baptisanTable_wrapper .col-md-6:eq(0)');
    });
</script>