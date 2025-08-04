<?php
require_once 'functions/jemaat.php';

$jemaat = query("SELECT j.*, k.*, r.* FROM jemaat j LEFT JOIN keluarga k ON j.id_keluarga = k.id_keluarga LEFT JOIN rayon r ON k.id_rayon = r.id_rayon");


if (isset($_GET['deleteJemaat'])) {
    $data = [
        'id_jemaat' => $_GET['deleteJemaat']
    ];

    if (deleteJemaat($data)) {
        set_alert('success', 'Berhasil Dihapus', 'Data jemaat berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal Menghapus', 'Terjadi kesalahan saat menghapus data.');
    }

    header("Location: index.php?page=jemaat");
    exit;
}

if (isset($_POST['updateJemaat'])) {
    $id = $_POST['id_jemaat'];
    $data = $_POST;

    if (updateJemaat($id, $data)) {
        set_alert('success', 'Berhasil Diupdate', 'Data jemaat berhasil diperbarui.');
    } else {
        set_alert('error', 'Gagal Mengupdate', 'Terjadi kesalahan saat memperbarui data.');
    }

    header("Location: index.php?page=jemaat");
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
                    <h5 class="mb-0">Data Jemaat</h5>
                    <a href="index.php?page=createjemaat" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Jemaat
                    </a>

                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center mb-1">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recap">Rekap Data Jemaat</button>
                </div>

                <div class="modal fade" id="recap" tabindex="-1" role="dialog" aria-labelledby="recapLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Rekap Data Jemaat</h5>
                            </div>
                            <div class="modal-body">
                                <?php
                                $total = count($jemaat);

                                $gender = [];
                                $status_baptis = [];
                                $status_sidi = [];
                                $status_perkawinan = [];
                                $pendidikan_terakhir = [];
                                $pekerjaan = [];
                                $status_jemaat = [];

                                foreach ($jemaat as $j) {
                                    $jk = $j['jenis_kelamin'] === 'Laki-laki' ? 'Laki-laki' : ($j['jenis_kelamin'] === 'Perempuan' ? 'Perempuan' : '-');

                                    $baptis = $j['status_baptis'] ?? '-';
                                    $sidi = $j['status_sidi'] ?? '-';
                                    $perkawinan = $j['status_perkawinan'] ?? '-';
                                    $pendidikan = $j['pendidikan_terakhir'] ?? '-';
                                    $kerja = $j['pekerjaan'] ?? '-';
                                    $stat = $j['status_jemaat'] ?? '-';

                                    $gender[$jk] = ($gender[$jk] ?? 0) + 1;
                                    $status_baptis[$baptis] = ($status_baptis[$baptis] ?? 0) + 1;
                                    $status_sidi[$sidi] = ($status_sidi[$sidi] ?? 0) + 1;
                                    $status_perkawinan[$perkawinan] = ($status_perkawinan[$perkawinan] ?? 0) + 1;
                                    $pendidikan_terakhir[$pendidikan] = ($pendidikan_terakhir[$pendidikan] ?? 0) + 1;
                                    $pekerjaan[$kerja] = ($pekerjaan[$kerja] ?? 0) + 1;
                                    $status_jemaat[$stat] = ($status_jemaat[$stat] ?? 0) + 1;
                                }

                                function renderCountTable($title, $data)
                                {
                                    echo "<div class='mb-3'>";
                                    echo "<h6 class='font-weight-bold'>$title</h6>";
                                    echo "<table class='table table-sm table-bordered mb-0'>";
                                    foreach ($data as $key => $val) {
                                        echo "<tr><td>$key</td><td class='text-right'><strong>$val</strong> Orang</td></tr>";
                                    }
                                    echo "</table></div>";
                                }
                                ?>

                                <p class="mb-4"><strong>Total Jemaat:</strong> <span class="text-dark"><?= $total ?> Orang</span></p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        renderCountTable("Jenis Kelamin", $gender);
                                        renderCountTable("Status Baptis", $status_baptis);
                                        renderCountTable("Status Sidi", $status_sidi);
                                        renderCountTable("Status Perkawinan", $status_perkawinan);
                                        renderCountTable("Status Jemaat", $status_jemaat);
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                        renderCountTable("Pendidikan Terakhir", $pendidikan_terakhir);
                                        renderCountTable("Pekerjaan", $pekerjaan);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <a href="laporan/jemaat/rekap_jemaat.php" class="btn btn-warning">Cetak Rekap Data Jemaat</a>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>





                <table id="jemaatTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lengkap</th>
                            <th>Kode KK - Kepala Keluarga</th>
                            <th class="d-none">Tempat Lahir</th>
                            <th class="d-none">Tanggal Lahir</th>
                            <th>Jenis Kelamin</th>
                            <th class="d-none">Status Perkawinan</th>
                            <th class="d-none">Status dalam Keluarga</th>
                            <th class="d-none">Status Baptis</th>
                            <th class="d-none">Status Sidi</th>
                            <th class="d-none">Pendidikan Terakhir</th>
                            <th class="d-none">Alamat</th>
                            <th class="d-none">Tempat Tinggal</th>
                            <th>Rayon</th>
                            <th>Pekerjaan</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no = 1;
                        foreach ($jemaat as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['kode_kk']) ?> - <?= htmlspecialchars($row['nama_keluarga']) ?> </td>
                                <td class="d-none"><?= htmlspecialchars($row['tempat_lahir']) ?></td>
                                <td class="d-none"><?= tanggalIndo($row['tanggal_lahir']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['status_perkawinan']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['status_dlm_keluarga']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['status_baptis']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['status_sidi']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['pendidikan_terakhir']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['alamat']) ?></td>
                                <td class="d-none"><?= htmlspecialchars($row['tempat_tinggal']) ?></td>
                                <td><?= htmlspecialchars($row['nama_rayon']) ?></td>
                                <td><?= htmlspecialchars($row['pekerjaan']) ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm detailJemaatBtn" data-id="<?= $row['id_jemaat'] ?>"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm editJemaatBtn" data-id="<?= $row['id_jemaat'] ?>"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm deleteJemaatBtn" data-id="<?= $row['id_jemaat'] ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="jemaatModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" id="modalJemaatContent">
            <!-- AJAX Loaded Content -->
        </div>
    </div>
</div>

<script>
    // Modal Function edit, detail (remove add)
    function loadJemaatModal(action, id = '') {
        let url = `pages/jemaat/jemaat_form.php?action=${action}`;
        if (id) url += `&id=${id}`;

        $.get(url, function(data) {
            $('#modalJemaatContent').html(data);
            $('#jemaatModal').modal('show');
        });
    }
    $(document).on('click', '.editJemaatBtn', function() {
        const id = $(this).data('id');
        loadJemaatModal('edit', id);
    });

    $(document).on('click', '.detailJemaatBtn', function() {
        const id = $(this).data('id');
        loadJemaatModal('detail', id);
    });


    // delete alert sweetalert2
    $(document).on('click', '.deleteJemaatBtn', function() {
        const id = $(this).data('id');
        confirmAction({
            title: 'Hapus data?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            callback: function() {
                window.location.href = 'index.php?page=jemaat&deleteJemaat=' + id;
            }
        });
    });

    // Table definition
    $(function() {
        let table = $('#jemaatTable').DataTable({
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
                "targets": [3, 5, 6, 7, 8, 9, 10, 11], // index kolom tersembunyi
                "visible": false,
                "searchable": false
            }],
            "buttons": [{
                extend: 'excel',
                title: 'Data Jemaat',
                text: 'Cetak Data Jemaat <small>(excel)</small>',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13], // tanpa aksi
                    modifier: {
                        page: 'all'
                    }
                }
            }]
        });


        // Tempatkan tombol di bagian atas
        table.buttons().container().appendTo('#jemaatTable_wrapper .col-md-6:eq(0)');
    });
</script>