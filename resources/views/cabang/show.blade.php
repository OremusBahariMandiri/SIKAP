@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i>Detail Cabang Perusahaan
        </h1>
        <a href="{{ route('cabang.rekap') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Kembali
        </a>
    </div>

    <!-- Company Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Informasi Perusahaan</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold text-primary">{{ $perusahaan->NamaPrsh }}</h5>
                            <p class="mb-0">
                                <strong>Jumlah Cabang:</strong>
                                <span class="badge bg-primary">{{ count($cabangDokumen) }}</span>
                            </p>
                        </div>
                        {{-- <div class="col-md-6 text-md-right">
                            @if ($isAdmin || $hasExportPermission)
                            <button class="btn btn-sm btn-success" id="exportBtn">
                                <i class="fas fa-file-excel me-1"></i> Export Data
                            </button>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Card (If you want to add a map visualization) -->
    {{-- <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-map-marker-alt me-2"></i>Sebaran Cabang</h6>
                </div>
                <div class="card-body">
                    <div id="mapContainer" style="height: 400px; width: 100%;">
                        <!-- Map will be rendered here -->
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-map fa-3x mb-3"></i>
                            <p>Visualisasi peta tidak tersedia. Gunakan tabel di bawah untuk melihat detail cabang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Branch List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-list me-2"></i>Daftar Cabang</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="branchTable" class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>No. Registrasi</th>
                                    <th>Lokasi Cabang</th>
                                    <th>Atas Nama</th>
                                    <th>Tgl. Terbit</th>
                                    <th>Tgl. Berakhir</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cabangDokumen as $index => $dokumen)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dokumen->NoRegDok }}</td>
                                        <td>{{ $dokumen->PeruntukanDok }}</td>
                                        <td>{{ $dokumen->DokAtasNama }}</td>
                                        <td>{{ $dokumen->tgl_terbit_formatted }}</td>
                                        <td>{{ $dokumen->tgl_berakhir_formatted }}</td>
                                        <td>
                                            @if($dokumen->StsBerlakuDok == 'Berlaku')
                                                <span class="badge bg-success">Berlaku</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Berlaku</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isAdmin || $hasViewPermission)
                                            <div class="btn-group">
                                                <a href="{{ route('dokLegal.show', $dokumen->id) }}"
                                                   class="btn btn-sm btn-info"
                                                   data-bs-toggle="tooltip"
                                                   title="Detail Dokumen">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($isAdmin || $hasExportPermission)
                                                <a href="{{ route('dokLegal.download', $dokumen->id) }}"
                                                   class="btn btn-sm btn-primary"
                                                   data-bs-toggle="tooltip"
                                                   title="Unduh Dokumen">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
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
</style>
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

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
        },
        "buttons": {
            "copyTitle": "Salin ke Clipboard",
            "copySuccess": {
                "_": "%d baris disalin",
                "1": "1 baris disalin"
            }
        }
    };

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize DataTable
    var table = $('#branchTable').DataTable({
        responsive: true,
        language: indonesianLanguage,
        columnDefs: [{
            responsivePriority: 1,
            targets: [0, 1, 2, 6, 7] // Priority columns
        },
        {
            orderable: false,
            targets: [7] // Actions column not sortable
        }],
        initComplete: function() {
            // Force style search input box
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_filter input').css({
                'width': '200px',
                'max-width': '100%'
            });
        }
    });

    // Export to Excel functionality
    $('#exportBtn').on('click', function() {
        // Get table data
        var data = [];
        var headers = [];

        // Get headers (excluding the action column)
        $('#branchTable thead th').each(function(index) {
            if (index < 7) { // Skip the action column
                headers.push($(this).text());
            }
        });

        // Get row data (excluding the action column)
        $('#branchTable tbody tr').each(function() {
            var rowData = [];
            $(this).find('td').each(function(index) {
                if (index < 7) { // Skip the action column
                    rowData.push($(this).text().trim());
                }
            });
            data.push(rowData);
        });

        // Create CSV content
        var csvContent = headers.join(',') + '\n';
        data.forEach(function(row) {
            csvContent += row.join(',') + '\n';
        });

        // Create download link
        var encodedUri = encodeURI('data:text/csv;charset=utf-8,' + csvContent);
        var link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'Cabang_{{ $perusahaan->NamaPrsh }}_{{ date('Ymd') }}.csv');
        document.body.appendChild(link);

        // Trigger download
        link.click();
        document.body.removeChild(link);
    });

    // You could add map visualization here using a library like Leaflet or Google Maps
    // This would require geocoding the branch locations (PeruntukanDok)
});
</script>
@endpush