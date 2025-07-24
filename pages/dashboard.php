<?php
require_once 'database.php';

// Total data
$totalRayon = query("SELECT COUNT(*) AS total FROM rayon")[0]['total'];
$totalKeluarga = query("SELECT COUNT(*) AS total FROM keluarga")[0]['total'];
$totalJemaat = query("SELECT COUNT(*) AS total FROM jemaat")[0]['total'];

// Ulang tahun dalam 7 hari
$today = date('Y-m-d');
$nextWeek = date('Y-m-d', strtotime('+7 days'));

$ulangTahun = query("
    SELECT nama_lengkap, tanggal_lahir 
    FROM jemaat 
    WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') BETWEEN DATE_FORMAT(NOW(), '%m-%d') 
    AND DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 7 DAY), '%m-%d')
    ORDER BY tanggal_lahir
");

function hariIndo($tanggal)
{
    $hariInggris = date('l', strtotime($tanggal));
    $hariIndonesia = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];
    return $hariIndonesia[$hariInggris] ?? $hariInggris;
}

function hitungUmur($tanggalLahir)
{
    $birthDate = new DateTime($tanggalLahir);
    $today = new DateTime();
    $umur = $today->diff($birthDate)->y;
    return $umur;
}
?>

<div class="row">
    <!-- Kartu Total Rayon -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $totalRayon ?></h3>
                <p>Total Rayon</p>
            </div>
            <div class="icon"><i class="fas fa-layer-group"></i></div>
        </div>
    </div>

    <!-- Kartu Total Keluarga -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalKeluarga ?></h3>
                <p>Total Keluarga</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <!-- Kartu Total Jemaat -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $totalJemaat ?></h3>
                <p>Total Jemaat</p>
            </div>
            <div class="icon"><i class="fas fa-user-friends"></i></div>
        </div>
    </div>
</div>

<div class="card card-red card-outline">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
            <h5 class="mb-0">Jemaat Ulang Tahun Minggu Ini</h5>
            <a href="laporan/laporan_ultah.php" class="btn btn-warning" target="_blank">
                Cetak
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover" id="birthdayTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Hari</th>
                    <th>Umur</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($ulangTahun) > 0): ?>
                    <?php foreach ($ulangTahun as $jemaat): ?>
                        <tr>
                            <td><?= htmlspecialchars($jemaat['nama_lengkap']) ?></td>
                            <td><?= date('d M Y', strtotime($jemaat['tanggal_lahir'])) ?></td>
                            <td><?= hariIndo($jemaat['tanggal_lahir']) ?></td>
                            <td><?= hitungUmur($jemaat['tanggal_lahir']) ?> tahun</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada jemaat yang ulang tahun minggu ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function() {
        let table = $('#birthdayTable').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [
                [0, 'asc']
            ]
        });
    });
</script>