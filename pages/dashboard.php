<?php
require_once 'database.php';

$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

function hariIndo($tanggal)
{
    $map = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
    return $map[date('l', strtotime($tanggal))] ?? $tanggal;
}

function hitungUmur($tanggalLahir)
{
    $birthDate = new DateTime($tanggalLahir);
    return (new DateTime())->diff($birthDate)->y;
}

// Ulang Tahun Minggu Ini
$ulangTahun = query("SELECT nama_lengkap, tanggal_lahir FROM jemaat 
    WHERE DATE_FORMAT(tanggal_lahir, '%m-%d') BETWEEN DATE_FORMAT('$monday', '%m-%d') AND DATE_FORMAT('$sunday', '%m-%d')
    ORDER BY tanggal_lahir");

// Anniversary Nikah (based on tanggal_nikah, ignoring year)
$anniversary = query("
    SELECT 
        CONCAT(j1.nama_lengkap, ' & ', j2.nama_lengkap) AS pasangan, 
        p.tanggal_nikah 
    FROM pernikahan p
    LEFT JOIN jemaat j1 ON p.id_suami = j1.id_jemaat
    LEFT JOIN jemaat j2 ON p.id_istri = j2.id_jemaat
    WHERE DATE_FORMAT(p.tanggal_nikah, '%m-%d') 
        BETWEEN DATE_FORMAT('$monday', '%m-%d') 
        AND DATE_FORMAT('$sunday', '%m-%d')
    ORDER BY p.tanggal_nikah ASC
");

function hitungLamaMenikah($tanggalNikah)
{
    $mulai = new DateTime($tanggalNikah);
    $sekarang = new DateTime();
    return $sekarang->diff($mulai)->y;
}



// Data baru dari semua tabel (gunakan jika tabel memiliki kolom created_at)
$dataBaru = [
    'jemaat' => query("
        SELECT nama_lengkap AS nama, created_at 
        FROM jemaat 
        WHERE created_at BETWEEN '$monday' AND '$sunday' 
        ORDER BY created_at DESC
    "),

    'keluarga' => query("
        SELECT nama_keluarga AS nama, created_at 
        FROM keluarga 
        WHERE created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY created_at DESC
    "),

    'rayon' => query("
        SELECT nama_rayon AS nama, created_at 
        FROM rayon 
        WHERE created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY created_at DESC
    "),

    'atestasi' => query("
        SELECT j.nama_lengkap AS nama, a.created_at 
        FROM atestasi a
        JOIN jemaat j ON a.id_jemaat = j.id_jemaat
        WHERE a.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY a.created_at DESC
    "),

    'meninggal' => query("
        SELECT j.nama_lengkap AS nama, m.created_at 
        FROM meninggal m
        JOIN jemaat j ON m.id_jemaat = j.id_jemaat
        WHERE m.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY m.created_at DESC
    "),

    'pernikahan' => query("
        SELECT 
            CONCAT(
                COALESCE(j1.nama_lengkap, '-'), 
                ' & ', 
                COALESCE(j2.nama_lengkap, '-'), 
                ' (', DATE_FORMAT(p.tanggal_nikah, '%d %M %Y'), ')'
            ) AS nama, 
            p.created_at 
        FROM pernikahan p
        LEFT JOIN jemaat j1 ON p.id_suami = j1.id_jemaat
        LEFT JOIN jemaat j2 ON p.id_istri = j2.id_jemaat
        WHERE p.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY p.created_at DESC
    "),

    'baptisan' => query("
        SELECT j.nama_lengkap AS nama, b.created_at 
        FROM baptisan b
        JOIN jemaat j ON b.id_jemaat = j.id_jemaat
        WHERE b.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY b.created_at DESC
    "),

    'sidi' => query("
        SELECT j.nama_lengkap AS nama, s.created_at 
        FROM sidi s
        JOIN jemaat j ON s.id_jemaat = j.id_jemaat
        WHERE s.created_at BETWEEN '$monday' AND '$sunday'
        ORDER BY s.created_at DESC
    "),
];



$totalRayon = query("SELECT COUNT(*) AS total FROM rayon")[0]['total'];
$totalKeluarga = query("SELECT COUNT(*) AS total FROM keluarga")[0]['total'];
$totalJemaat = query("SELECT COUNT(*) AS total FROM jemaat")[0]['total'];

// Check if there's any new data in $dataBaru
$hasNewData = false;
foreach ($dataBaru as $items) {
    if (!empty($items)) {
        $hasNewData = true;
        break;
    }
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

<div class="card card-outline card-red">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
            <h5 class="mb-0">
                <i class="fas fa-calendar-week me-2"></i>
                Informasi Minggu Ini (<?= date('d M', strtotime($monday)) ?> - <?= date('d M Y', strtotime($sunday)) ?>)
            </h5>
            <a href="laporan/laporan_dashboard.php" class="btn btn-warning">
                <i class="fas fa-print"></i> Cetak
            </a>

        </div>
    </div>
    <div class="card-body">
        <h6><strong><i class="fas fa-birthday-cake me-2 text-danger"></i> Ulang Tahun</strong></h6>
        <ul>
            <?php if ($ulangTahun): foreach ($ulangTahun as $j): ?>
                    <li>
                        <?= htmlspecialchars($j['nama_lengkap']) ?> -
                        <?= hariIndo($j['tanggal_lahir']) ?> (<?= date('d M', strtotime($j['tanggal_lahir'])) ?>) -
                        <?= hitungUmur($j['tanggal_lahir']) ?> tahun
                    </li>
                <?php endforeach;
            else: ?>
                <li><em>Tidak ada ulang tahun minggu ini.</em></li>
            <?php endif; ?>
        </ul>

        <h6 class="mt-3"><strong><i class="fas fa-ring me-2 text-warning"></i> Ulang Tahun Pernikahan</strong></h6>
        <ul>
            <?php if (!empty($anniversary)): ?>
                <ul>
                    <?php foreach ($anniversary as $a): ?>
                        <?php
                        $namaPasangan = isset($a['pasangan'])
                            ? htmlspecialchars($a['pasangan'])
                            : htmlspecialchars(($a['nama_suami'] ?? '') . ' & ' . ($a['nama_istri'] ?? ''));
                        $tanggalNikah = $a['tanggal_nikah'];
                        $hari = hariIndo($tanggalNikah);
                        $lamaNikah = hitungLamaMenikah($tanggalNikah);
                        ?>
                        <li>
                            <?= $namaPasangan ?> -
                            <?= $hari ?> (<?= date('d M Y', strtotime($tanggalNikah)) ?>),
                            <?= $lamaNikah ?> tahun
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><em>Tidak ada anniversary minggu ini.</em></p>
            <?php endif; ?>
        </ul>

        <?php if ($hasNewData): ?>
            <h6 class="mt-3"><strong><i class="fas fa-plus-circle me-2 text-success"></i> Data Baru</strong></h6>
            <ul>
                <?php foreach ($dataBaru as $label => $items): ?>
                    <?php if (!empty($items)): ?>
                        <li>
                            <strong><?= ucfirst($label) ?>:</strong>
                            <ul>
                                <?php foreach ($items as $i): ?>
                                    <li><strong><?= htmlspecialchars($i['nama']) ?></strong> - Ditambahkan pada: <?= date('d M Y', strtotime($i['created_at'])) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

            </ul>
        <?php else: ?>
            <h6 class="mt-3"><strong><i class="fas fa-minus-circle me-2 text-purple"></i> <em>Tidak ada Data Baru minggu ini.</em></strong></h6>

        <?php endif; ?>
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