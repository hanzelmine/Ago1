<?php
$pencarian = query("
    SELECT 
        -- jemaat (tanpa created_at & updated_at)
        j.id_jemaat,
        j.id_keluarga,
        j.nama_lengkap,
        j.jenis_kelamin,
        j.tempat_lahir,
        j.tanggal_lahir,
        j.status_perkawinan,
        j.status_dlm_keluarga,
        j.status_baptis,
        j.status_sidi,
        j.pendidikan_terakhir,
        j.pekerjaan,

        -- keluarga (tanpa created_at & updated_at)
        k.id_keluarga,
        k.kode_kk,
        k.nama_keluarga,
        k.alamat,
        k.tempat_tinggal,
        k.id_rayon,

        -- rayon (tanpa created_at & updated_at)
        r.id_rayon,
        r.nama_rayon,
        r.keterangan

    FROM 
        jemaat j
    LEFT JOIN 
        keluarga k ON j.id_keluarga = k.id_keluarga
    LEFT JOIN 
        rayon r ON k.id_rayon = r.id_rayon
");

function formatTanggal($tgl)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $tanggal = date('d', strtotime($tgl));
    $bulanNum = (int)date('m', strtotime($tgl));
    $tahun = date('Y', strtotime($tgl));

    return $tanggal . ' ' . $bulan[$bulanNum] . ' ' . $tahun;
}
?>

<div class="card card-outline card-primary">
    <div class="card-header ">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
            <h5 class="mb-0">Pencarian & Filtering</h5>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="filterArea"></div>
        <table id="pencarianTable" class="table table-bordered table-hover multi-filter-select">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>Kode KK - Nama Keluarga</th>
                    <th>Nama Rayon</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat, Tanggal Lahir</th>
                    <th>Status Perkawinan</th>
                    <th>Status Keluarga</th>
                    <th>Status Baptis</th>
                    <th>Status Sidi</th>
                    <th>Pendidikan</th>
                    <th>Pekerjaan</th>
                    <th>Alamat</th>
                    <th>Tempat Tinggal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($pencarian as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['kode_kk']) ?> - <?= htmlspecialchars($row['nama_keluarga']) ?></td>
                        <td><?= htmlspecialchars($row['nama_rayon']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                        <td><?= htmlspecialchars($row['tempat_lahir']) ?>, <?= formatTanggal($row['tanggal_lahir']) ?></td>
                        <td><?= htmlspecialchars($row['status_perkawinan']) ?></td>
                        <td><?= htmlspecialchars($row['status_dlm_keluarga']) ?></td>
                        <td><?= htmlspecialchars($row['status_baptis']) ?></td>
                        <td><?= htmlspecialchars($row['status_sidi']) ?></td>
                        <td><?= htmlspecialchars($row['pendidikan_terakhir']) ?></td>
                        <td><?= htmlspecialchars($row['pekerjaan']) ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td><?= htmlspecialchars($row['tempat_tinggal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    $(function() {
        const table = $('#pencarianTable').DataTable({
            scrollX: true,
            paging: true,
            lengthChange: false,
            pageLength: 5,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: false,
            // columnDefs: [{
            //         orderable: false,
            //         targets: 0
            //     } // disable sorting on first column (index 0)
            // ],
            order: [
                [0, 'asc']
            ],
            buttons: [{
                extend: 'colvis',
                text: 'Visibilitas Kolom'
            }, {
                extend: 'excel',
                title: 'Data Jemaat',
                text: 'Cetak Excel',
                exportOptions: {
                    columns: ':visible:not(.not-export)'
                }
            }, {
                text: 'Cetak PDF',
                action: function(e, dt, node, config) {
                    const visibleIndexes = dt.columns(':visible').indexes().toArray();

                    const headers = visibleIndexes.map(i =>
                        dt.column(i).header().textContent.trim()
                    );

                    const data = dt.rows({
                        search: 'applied'
                    }).data().toArray().map(row => {
                        return visibleIndexes.map(i => row[i]);
                    });

                    const form = $('<form>', {
                        method: 'POST',
                        action: 'laporan/laporan_pencarian.php',
                        target: '_blank'
                    }).appendTo('body');

                    $('<input>', {
                        type: 'hidden',
                        name: 'filteredData',
                        value: JSON.stringify(data)
                    }).appendTo(form);

                    $('<input>', {
                        type: 'hidden',
                        name: 'visibleHeaders',
                        value: JSON.stringify(headers)
                    }).appendTo(form);

                    form.submit();
                    form.remove();
                }
            }]
        });

        table.buttons().container().appendTo('#pencarianTable_wrapper .col-md-6:eq(0)');

        const filterColumns = [1, 2, 3, 4, 10, 11];
        renderExternalFilters('#pencarianTable', '.filterArea', filterColumns);

        table.on('search.dt draw.dt', function() {
            renderExternalFilters('#pencarianTable', '.filterArea', filterColumns);
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');

        if (page === 'pencarian') {
            document.body.classList.add('sidebar-collapse');
        }
    });
</script>