@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>Rekap Data Cabang Perusahaan
            </h1>
        </div>

        <!-- Only show chart if there are companies with branches -->
        @if (count($chartData) > 0)
            <div class="row">
                <!-- Chart Area -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Grafik Jumlah Cabang Per Perusahaan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="cabangPieChart"></canvas>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Data Cabang Berdasarkan Dokumen dengan Jenis: {{ $jenisDokumen->JenisDok }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Area -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clipboard-list me-2"></i>Ringkasan Data
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h4 class="text-primary">
                                    {{ count(array_filter($rekapCabang, function ($item) {return $item['jumlah_cabang'] > 0;})) }}
                                </h4>
                                <p>Perusahaan Memiliki Cabang</p>
                            </div>

                            <div class="text-center">
                                <h4 class="text-primary">{{ array_sum(array_column($rekapCabang, 'jumlah_cabang')) }}</h4>
                                <p>Total Cabang</p>
                            </div>

                            @if (count($chartData) > 0)
                                <hr>
                                <div class="text-center mb-2">
                                    <h5>Perusahaan dengan Cabang Terbanyak:</h5>
                                    @php
                                        $maxCabang = max(array_column($rekapCabang, 'jumlah_cabang'));
                                        $perusahaanMax = array_filter($rekapCabang, function ($item) use ($maxCabang) {
                                            return $item['jumlah_cabang'] == $maxCabang && $item['jumlah_cabang'] > 0;
                                        });
                                    @endphp

                                    @foreach ($perusahaanMax as $perusahaan)
                                        <div class="mt-2">
                                            <strong>{{ $perusahaan['nama_perusahaan'] }}</strong>
                                            <div class="badge bg-primary">{{ $perusahaan['jumlah_cabang'] }} cabang</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                <h5>Tidak ada data cabang yang berlaku saat ini</h5>
                                <p>Tidak ada perusahaan yang memiliki dokumen cabang berstatus "Berlaku" dengan jenis
                                    {{ $jenisDokumen->JenisDok }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Data Rekap Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-table me-2"></i>Tabel Data Cabang Perusahaan
                        </h6>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="cabangTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 35%">Nama Perusahaan</th>
                                        <th style="width: 15%" class="text-center">Jumlah Cabang</th>
                                        <th class="text-center" style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Sort rekap data by jumlah_cabang in descending order
                                        $sortedRekapCabang = collect($rekapCabang)
                                            ->sortByDesc('jumlah_cabang')
                                            ->values()
                                            ->all();
                                    @endphp

                                    @if (count($sortedRekapCabang) > 0)
                                        @foreach ($sortedRekapCabang as $index => $rekap)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $rekap['nama_perusahaan'] }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $rekap['jumlah_cabang'] > 0 ? 'primary' : 'secondary' }}">{{ $rekap['jumlah_cabang'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($isAdmin || $hasViewPermission)
                                                        <a href="{{ route('cabang.detail', $rekap['perusahaan_id']) }}"
                                                            class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                            title="Detail Cabang">
                                                            <i class="fas fa-eye"></i> Detail
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data perusahaan yang tersedia
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 200px !important;
            max-width: 100% !important;
        }

        table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        table.dataTable thead th.sorting:after,
        table.dataTable thead th.sorting_asc:after,
        table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .btn-sm {
            transition: transform 0.2s;
        }

        .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Chart container */
        .chart-pie {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
@endpush

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Indonesian language configuration for DataTables
            const indonesianLanguage = {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                }
            };

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize DataTable
            var table = $('#cabangTable').DataTable({
                responsive: true,
                language: indonesianLanguage,
                columnDefs: [{
                        responsivePriority: 1,
                        targets: [0, 1, 2, 3]
                    },
                    {
                        orderable: false,
                        targets: [3]
                    }
                ],
                initComplete: function() {
                    // Force style search input box
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_filter input').css({
                        'width': '200px',
                        'max-width': '100%'
                    });
                }
            });

            // Chart.js - Pie Chart for Branch Distribution
            var chartData = {!! json_encode($chartData) !!};

            if (chartData.length > 0) {
                var ctxPie = document.getElementById('cabangPieChart');
                if (ctxPie) {
                    var myPieChart = new Chart(ctxPie.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: chartData.map(item => item.label),
                            datasets: [{
                                data: chartData.map(item => item.value),
                                backgroundColor: chartData.map(item => item.color),
                                hoverBackgroundColor: chartData.map(item => item.color),
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            },
                            legend: {
                                display: true,
                                position: 'bottom'
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var label = context.label || '';
                                            var value = context.raw || 0;
                                            return label + ': ' + value + ' cabang';
                                        }
                                    }
                                }
                            }
                        },
                    });
                }
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
