@extends('layouts.app')

@section('content')
    <div class="container-fluid perusahaanPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-building me-2"></i>Manajemen Perusahaan</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>

                            {{-- Tampilkan button tambah hanya jika user memiliki akses --}}
                            @if ($isAdmin || $hasCreatePermission)
                                <a href="{{ route('perusahaan.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="perusahaanTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Bidang Usaha</th>
                                        <th>Izin Usaha</th>
                                        <th>Golongan Usaha</th>
                                        <th>Direktur Utama</th>
                                        <th>Direktur</th>
                                        <th>Komisaris Utama</th>
                                        <th>Komisaris</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Website</th>
                                        <th>Tanggal Berdiri</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($perusahaans as $perusahaan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $perusahaan->NamaPrsh }}</td>
                                            <td>{{ $perusahaan->BidangUsh }}</td>
                                            <td>{{ $perusahaan->IzinUsh }}</td>
                                            <td>{{ $perusahaan->GolonganUsh }}</td>
                                            <td>{{ $perusahaan->DirekturUtm }}</td>
                                            <td>{{ $perusahaan->Direktur }}</td>
                                            <td>{{ $perusahaan->KomisarisUtm}}</td>
                                            <td>{{ $perusahaan->Komisaris }}</td>
                                            <td>{{ $perusahaan->TelpPrsh }}</td>
                                            <td>{{ $perusahaan->EmailPrsh }}</td>
                                            <td>
                                                @if ($perusahaan->WebPrsh)
                                                    <a href="{{ $perusahaan->WebPrsh }}" target="_blank" class="text-decoration-none">
                                                        <i class="fas fa-globe me-1"></i>{{ $perusahaan->WebPrsh }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($perusahaan->TglBerdiri)
                                                    {{ $perusahaan->TglBerdiri->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    {{-- Detail button --}}
                                                    @if ($isAdmin || $hasViewPermission)
                                                        <a href="{{ route('perusahaan.show', $perusahaan->id) }}"
                                                            class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Edit button --}}
                                                    @if ($isAdmin || $hasEditPermission)
                                                        <a href="{{ route('perusahaan.edit', $perusahaan->id) }}"
                                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Delete button --}}
                                                    @if ($isAdmin || $hasDeletePermission)
                                                        <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                            data-id="{{ $perusahaan->id }}" data-name="{{ $perusahaan->NamaPrsh }}"
                                                            data-bs-toggle="tooltip" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
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

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filter Perusahaan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_nama" class="form-label">Nama Perusahaan</label>
                                    <select class="form-select" id="filter_nama">
                                        <option value="">Semua Perusahaan</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_bidang" class="form-label">Bidang Usaha</label>
                                    <select class="form-select" id="filter_bidang">
                                        <option value="">Semua Bidang Usaha</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_izin" class="form-label">Izin Usaha</label>
                                    <select class="form-select" id="filter_izin">
                                        <option value="">Semua Izin Usaha</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_golongan" class="form-label">Golongan Usaha</label>
                                    <select class="form-select" id="filter_golongan">
                                        <option value="">Semua Golongan Usaha</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_direktur_utama" class="form-label">Direktur Utama</label>
                                    <select class="form-select" id="filter_direktur_utama">
                                        <option value="">Semua Direktur Utama</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_direktur" class="form-label">Direktur</label>
                                    <select class="form-select" id="filter_direktur">
                                        <option value="">Semua Direktur</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_komisaris_utama" class="form-label">Komisaris Utama</label>
                                    <select class="form-select" id="filter_komisaris_utama">
                                        <option value="">Semua Komisaris Utama</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_komisaris" class="form-label">Komisaris</label>
                                    <select class="form-select" id="filter_komisaris">
                                        <option value="">Semua Komisaris</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_telepon" class="form-label">Telepon</label>
                                    <select class="form-select" id="filter_telepon">
                                        <option value="">Semua Telepon</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_email" class="form-label">Email</label>
                                    <select class="form-select" id="filter_email">
                                        <option value="">Semua Email</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_website" class="form-label">Website</label>
                                    <select class="form-select" id="filter_website">
                                        <option value="">Semua Website</option>
                                        <!-- Akan diisi secara dinamis dengan JavaScript -->
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="filter_tgl_berdiri" class="form-label">Tanggal Berdiri</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter_tgl_berdiri_from"
                                            placeholder="Dari">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="filter_tgl_berdiri_to"
                                            placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="resetFilter">
                        <i class="fas fa-undo me-1"></i>Reset Filter
                    </button>
                    <button type="button" class="btn btn-primary" id="applyFilter">
                        <i class="fas fa-check me-1"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus perusahaan <strong id="companyNameToDelete"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="fas fa-download me-2"></i>Ekspor Data
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" id="exportExcel">
                            <i class="fas fa-file-excel me-2"></i> Ekspor ke Excel
                        </button>
                        {{-- <button type="button" class="btn btn-danger" id="exportPdf">
                            <i class="fas fa-file-pdf me-2"></i> Ekspor ke PDF
                        </button>
                        <button type="button" class="btn btn-secondary" id="exportPrint">
                            <i class="fas fa-print me-2"></i> Print
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Export -->
    <form id="exportForm" action="{{ route('perusahaan.export-excel') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="filter_nama" id="export_filter_nama">
        <input type="hidden" name="filter_bidang" id="export_filter_bidang">
        <input type="hidden" name="filter_izin" id="export_filter_izin">
        <input type="hidden" name="filter_golongan" id="export_filter_golongan">
        <input type="hidden" name="filter_direktur_utama" id="export_filter_direktur_utama">
        <input type="hidden" name="filter_direktur" id="export_filter_direktur">
        <input type="hidden" name="filter_komisaris_utama" id="export_filter_komisaris_utama">
        <input type="hidden" name="filter_komisaris" id="export_filter_komisaris">
        <input type="hidden" name="filter_telepon" id="export_filter_telepon">
        <input type="hidden" name="filter_email" id="export_filter_email">
        <input type="hidden" name="filter_website" id="export_filter_website">
        <input type="hidden" name="filter_tgl_berdiri_from" id="export_filter_tgl_berdiri_from">
        <input type="hidden" name="filter_tgl_berdiri_to" id="export_filter_tgl_berdiri_to">
    </form>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <style>
        /* CSS dengan spesifisitas tinggi untuk DataTables */
        .perusahaanPage .dataTables_wrapper .dataTables_length,
        .perusahaanPage .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem !important;
        }

        .perusahaanPage .dataTables_wrapper .dataTables_filter {
            text-align: right !important;
            margin-right: 0 !important;
        }

        .perusahaanPage .dataTables_wrapper .dataTables_filter label {
            display: inline-flex !important;
            align-items: center !important;
            margin-bottom: 0 !important;
            font-weight: normal !important;
        }

        .perusahaanPage .dataTables_wrapper .dataTables_filter input {
            margin-left: 5px !important;
            border-radius: 4px !important;
            border: 1px solid #ced4da !important;
            padding: 0.375rem 0.75rem !important;
            width: 200px !important;
            max-width: 100% !important;
        }

        .perusahaanPage table.dataTable thead th {
            position: relative;
            background-image: none !important;
        }

        .perusahaanPage table.dataTable thead th.sorting:after,
        .perusahaanPage table.dataTable thead th.sorting_asc:after,
        .perusahaanPage table.dataTable thead th.sorting_desc:after {
            position: absolute;
            top: 12px;
            right: 8px;
            display: block;
            font-family: "Font Awesome 5 Free";
        }

        .perusahaanPage table.dataTable thead th.sorting:after {
            content: "\f0dc";
            color: #ddd;
            font-size: 0.8em;
            opacity: 0.5;
        }

        .perusahaanPage table.dataTable thead th.sorting_asc:after {
            content: "\f0de";
        }

        .perusahaanPage table.dataTable thead th.sorting_desc:after {
            content: "\f0dd";
        }

        /* Add hover effect to action buttons */
        .perusahaanPage .btn-sm {
            transition: transform 0.2s;
        }
        .perusahaanPage .btn-sm:hover {
            transform: scale(1.1);
        }

        /* Hover effect for table rows */
        .perusahaanPage #perusahaanTable tbody tr {
            transition: all 0.2s ease;
        }

        .perusahaanPage #perusahaanTable tbody tr:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
            cursor: pointer;
            position: relative;
            z-index: 1;
        }

        /* Flash effect when hovering */
        @keyframes flashBorder {
            0% { box-shadow: 0 0 0 rgba(13, 110, 253, 0); }
            50% { box-shadow: 0 0 8px rgba(13, 110, 253, 0.5); }
            100% { box-shadow: 0 0 0 rgba(13, 110, 253, 0); }
        }

        .perusahaanPage #perusahaanTable tbody tr.row-hover-active {
            animation: flashBorder 1s ease infinite;
        }

        /* Highlight filter active state */
        .filter-active {
            background-color: #e8f4ff !important;
            border-left: 3px solid #0d6efd !important;
        }

        /* Hidden buttons untuk export */
        .dt-buttons {
            display: none !important;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

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
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Format tanggal untuk filter
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Tanggal berdiri filter (kolom 12)
                    let berdiriFrom = $('#filter_tgl_berdiri_from').val();
                    let berdiriTo = $('#filter_tgl_berdiri_to').val();
                    let berdiriDate = data[12] !== '-' ? moment(data[12], 'DD/MM/YYYY') : null;

                    if (berdiriDate === null) {
                        if (berdiriFrom === '' && berdiriTo === '') {
                            return true;
                        } else {
                            return false;
                        }
                    }

                    if ((berdiriFrom === '' && berdiriTo === '') ||
                        (berdiriFrom === '' && berdiriDate.isSameOrBefore(moment(berdiriTo))) ||
                        (berdiriTo === '' && berdiriDate.isSameOrAfter(moment(berdiriFrom))) ||
                        (berdiriDate.isBetween(moment(berdiriFrom), moment(berdiriTo), null, '[]'))) {
                        return true;
                    }
                    return false;
                }
            );

            // Initialize DataTable
            var table = $('#perusahaanTable').DataTable({
                responsive: true,
                language: indonesianLanguage,
                columnDefs: [{
                        responsivePriority: 1,
                        targets: [0, 1, 13] // No, Nama Perusahaan, Aksi
                    },
                    {
                        responsivePriority: 2,
                        targets: [2, 9, 10] // Bidang Usaha, Telepon, Email
                    },
                    {
                        orderable: false,
                        targets: [13] // Aksi
                    }
                ],
                buttons: [{
                        extend: 'excel',
                        text: 'Excel',
                        className: 'btn btn-sm btn-success d-none excel-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        className: 'btn btn-sm btn-danger d-none pdf-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-sm btn-secondary d-none print-export-btn',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                initComplete: function() {
                    // Force style search input box
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_filter input').css({
                        'width': '200px',
                        'max-width': '100%'
                    });

                    // Populasi dropdown filter dengan data dari tabel
                    populateFilterDropdowns(this.api());
                }
            });

            // Fungsi untuk mengisi dropdown filter dengan data dari tabel
            function populateFilterDropdowns(tableApi) {
                // Inisialisasi Set untuk menyimpan nilai unik
                const namaSet = new Set();
                const bidangSet = new Set();
                const izinSet = new Set();
                const golonganSet = new Set();
                const direkturUtamaSet = new Set();
                const direkturSet = new Set();
                const komisarisUtamaSet = new Set();
                const komisarisSet = new Set();
                const teleponSet = new Set();
                const emailSet = new Set();
                const websiteSet = new Set();

                // Fungsi helper untuk memeriksa apakah nilai valid
                function isValidValue(value) {
                    if (!value) return false;

                    // Hapus whitespace
                    const trimmed = value.toString().trim();

                    // Cek jika string kosong, tanda "-", atau teks "tidak ada", dll
                    return trimmed !== '' &&
                           trimmed !== '-' &&
                           trimmed.toLowerCase() !== 'tidak ada' &&
                           trimmed.toLowerCase() !== 'n/a' &&
                           trimmed.toLowerCase() !== 'null' &&
                           !trimmed.includes('text-muted');
                }

                // Kumpulkan semua nilai unik dari SELURUH data
                // Nama Perusahaan (kolom 1)
                tableApi.column(1).data().each(function(value) {
                    if (isValidValue(value)) namaSet.add(value.trim());
                });

                // Bidang Usaha (kolom 2)
                tableApi.column(2).data().each(function(value) {
                    if (isValidValue(value)) bidangSet.add(value.trim());
                });

                // Izin Usaha (kolom 3)
                tableApi.column(3).data().each(function(value) {
                    if (isValidValue(value)) izinSet.add(value.trim());
                });

                // Golongan Usaha (kolom 4)
                tableApi.column(4).data().each(function(value) {
                    if (isValidValue(value)) golonganSet.add(value.trim());
                });

                // Direktur Utama (kolom 5)
                tableApi.column(5).data().each(function(value) {
                    if (isValidValue(value)) direkturUtamaSet.add(value.trim());
                });

                // Direktur (kolom 6)
                tableApi.column(6).data().each(function(value) {
                    if (isValidValue(value)) direkturSet.add(value.trim());
                });

                // Komisaris Utama (kolom 7)
                tableApi.column(7).data().each(function(value) {
                    if (isValidValue(value)) komisarisUtamaSet.add(value.trim());
                });

                // Komisaris (kolom 8)
                tableApi.column(8).data().each(function(value) {
                    if (isValidValue(value)) komisarisSet.add(value.trim());
                });

                // Telepon (kolom 9)
                tableApi.column(9).data().each(function(value) {
                    if (isValidValue(value)) teleponSet.add(value.trim());
                });

                // Email (kolom 10)
                tableApi.column(10).data().each(function(value) {
                    if (isValidValue(value)) emailSet.add(value.trim());
                });

                // Website (kolom 11)
                tableApi.column(11).data().each(function(value) {
                    // Extract only the website URL (remove icon and formatting)
                    let website = value;
                    if (value.includes('</i>')) {
                        website = $(value).text().trim();
                    }
                    // Hapus "span" HTML jika ada
                    if (website.includes('<span')) {
                        website = '-'; // Marking as invalid
                    }

                    if (isValidValue(website)) {
                        websiteSet.add(website.trim());
                    }
                });

                // Konversi Set ke Array dan urutkan
                const namaValues = Array.from(namaSet).sort();
                const bidangValues = Array.from(bidangSet).sort();
                const izinValues = Array.from(izinSet).sort();
                const golonganValues = Array.from(golonganSet).sort();
                const direkturUtamaValues = Array.from(direkturUtamaSet).sort();
                const direkturValues = Array.from(direkturSet).sort();
                const komisarisUtamaValues = Array.from(komisarisUtamaSet).sort();
                const komisarisValues = Array.from(komisarisSet).sort();
                const teleponValues = Array.from(teleponSet).sort();
                const emailValues = Array.from(emailSet).sort();
                const websiteValues = Array.from(websiteSet).sort();

                // Fungsi untuk mengisi dropdown
                function populateDropdown(dropdownId, values) {
                    const dropdown = $(`#${dropdownId}`);
                    if (values.length > 0) {
                        values.forEach(function(value) {
                            dropdown.append(
                                $('<option>', {
                                    value: value,
                                    text: value
                                })
                            );
                        });
                    } else {
                        // Sembunyikan dropdown jika tidak ada nilai valid
                        dropdown.closest('.mb-3').hide();
                    }
                }

                // Isi semua dropdown
                populateDropdown('filter_nama', namaValues);
                populateDropdown('filter_bidang', bidangValues);
                populateDropdown('filter_izin', izinValues);
                populateDropdown('filter_golongan', golonganValues);
                populateDropdown('filter_direktur_utama', direkturUtamaValues);
                populateDropdown('filter_direktur', direkturValues);
                populateDropdown('filter_komisaris_utama', komisarisUtamaValues);
                populateDropdown('filter_komisaris', komisarisValues);
                populateDropdown('filter_telepon', teleponValues);
                populateDropdown('filter_email', emailValues);
                populateDropdown('filter_website', websiteValues);

                // Log untuk debugging
                console.log('Filter values loaded:', {
                    nama: namaValues.length,
                    bidang: bidangValues.length,
                    izin: izinValues.length,
                    golongan: golonganValues.length,
                    direkturUtama: direkturUtamaValues.length,
                    direktur: direkturValues.length,
                    komisarisUtama: komisarisUtamaValues.length,
                    komisaris: komisarisValues.length,
                    telepon: teleponValues.length,
                    email: emailValues.length,
                    website: websiteValues.length
                });
            }

            // Event for filter and export buttons
            $('#filterButton').on('click', function() {
                $('#filterModal').modal('show');
            });

            $('#exportButton').on('click', function() {
                $('#exportModal').modal('show');
            });

            // Apply Filter event handler
            $('#applyFilter').on('click', function() {
                // Reset semua filter terlebih dahulu
                table.columns().search('').draw();

                // Hapus custom filter yang mungkin masih ada
                while ($.fn.dataTable.ext.search.length > 1) { // Biarkan filter tanggal
                    $.fn.dataTable.ext.search.pop();
                }

                // Fungsi untuk menambahkan filter kolom dengan exact match
                function addColumnFilter(columnIdx, value) {
                    if (value) {
                        table.column(columnIdx).search('^' + $.fn.dataTable.util.escapeRegex(value) + '$', true, false);
                    }
                }

                // Menerapkan filter dari dropdown
                addColumnFilter(1, $('#filter_nama').val()); // Nama Perusahaan
                addColumnFilter(2, $('#filter_bidang').val()); // Bidang Usaha
                addColumnFilter(3, $('#filter_izin').val()); // Izin Usaha
                addColumnFilter(4, $('#filter_golongan').val()); // Golongan Usaha
                addColumnFilter(5, $('#filter_direktur_utama').val()); // Direktur Utama
                addColumnFilter(6, $('#filter_direktur').val()); // Direktur
                addColumnFilter(7, $('#filter_komisaris_utama').val()); // Komisaris Utama
                addColumnFilter(8, $('#filter_komisaris').val()); // Komisaris
                addColumnFilter(9, $('#filter_telepon').val()); // Telepon
                addColumnFilter(10, $('#filter_email').val()); // Email

                // Website perlu penanganan khusus karena format HTML
                const websiteValue = $('#filter_website').val();
                if (websiteValue) {
                    // Custom filter function untuk website
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            // Kolom website
                            const website = data[11];

                            // Cek jika website HTML (dengan tag) atau teks biasa
                            if (website.includes('<')) {
                                // Extract teks dari HTML
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = website;
                                const websiteText = tempDiv.textContent || tempDiv.innerText || '';
                                return websiteText.trim() === websiteValue;
                            } else {
                                // Bandingkan teks biasa
                                return website.trim() === websiteValue;
                            }
                        }
                    );
                }

                // Refresh table untuk menerapkan semua filter
                table.draw();

                $('#filterModal').modal('hide');

                // Highlight filter button jika ada filter aktif
                highlightFilterButton();
            });

            // Reset Filter event handler
            $('#resetFilter').on('click', function() {
                // Reset form fields
                $('#filterForm')[0].reset();

                // Remove active class from filter button
                $('#filterButton').removeClass('filter-active');

                // Reset table filters
                table.search('').columns().search('').draw();

                // Hapus custom filter
                while ($.fn.dataTable.ext.search.length > 1) { // Biarkan filter tanggal
                    $.fn.dataTable.ext.search.pop();
                }
            });

            // Highlight filter button if any filter is active
            function highlightFilterButton() {
                if ($('#filter_nama').val() ||
                    $('#filter_bidang').val() ||
                    $('#filter_izin').val() ||
                    $('#filter_golongan').val() ||
                    $('#filter_direktur_utama').val() ||
                    $('#filter_direktur').val() ||
                    $('#filter_komisaris_utama').val() ||
                    $('#filter_komisaris').val() ||
                    $('#filter_telepon').val() ||
                    $('#filter_email').val() ||
                    $('#filter_website').val() ||
                    $('#filter_tgl_berdiri_from').val() ||
                    $('#filter_tgl_berdiri_to').val()) {
                    $('#filterButton').addClass('filter-active');
                } else {
                    $('#filterButton').removeClass('filter-active');
                }
            }

            // Export buttons
            $('#exportExcel').on('click', function() {
                // Copy current filter values to export form
                $('#export_filter_nama').val($('#filter_nama').val());
                $('#export_filter_bidang').val($('#filter_bidang').val());
                $('#export_filter_izin').val($('#filter_izin').val());
                $('#export_filter_golongan').val($('#filter_golongan').val());
                $('#export_filter_direktur_utama').val($('#filter_direktur_utama').val());
                $('#export_filter_direktur').val($('#filter_direktur').val());
                $('#export_filter_komisaris_utama').val($('#filter_komisaris_utama').val());
                $('#export_filter_komisaris').val($('#filter_komisaris').val());
                $('#export_filter_telepon').val($('#filter_telepon').val());
                $('#export_filter_email').val($('#filter_email').val());
                $('#export_filter_website').val($('#filter_website').val());
                $('#export_filter_tgl_berdiri_from').val($('#filter_tgl_berdiri_from').val());
                $('#export_filter_tgl_berdiri_to').val($('#filter_tgl_berdiri_to').val());

                // Submit export form
                $('#exportForm').submit();
                $('#exportModal').modal('hide');
            });

            $('#exportPdf').on('click', function() {
                $('.pdf-export-btn').trigger('click');
                $('#exportModal').modal('hide');
            });

            $('#exportPrint').on('click', function() {
                $('.print-export-btn').trigger('click');
                $('#exportModal').modal('hide');
            });

            // Handle Delete Confirmation
            $('.delete-confirm').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                // Set company name in modal
                $('#companyNameToDelete').text(name);

                // Set form action URL
                $('#deleteForm').attr('action', "{{ url('perusahaan') }}/" + id);

                // Show modal
                $('#deleteConfirmationModal').modal('show');
            });

            // Tambahkan efek klik pada baris tabel untuk menuju halaman detail
            $('#perusahaanTable tbody').on('click', 'tr', function(e) {
                // Jangan ikuti link jika yang diklik adalah tombol atau link di dalam baris
                if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                    $(e.target).closest('button').length || $(e.target).closest('a').length) {
                    return;
                }

                // Dapatkan ID dokumen dari tombol detail - hanya jika user memiliki akses
                @if ($isAdmin || $hasViewPermission)
                var detailLink = $(this).find('a[title="Detail"]').attr('href');
                if (detailLink) {
                    window.location.href = detailLink;
                }
                @endif
            });

            // Tambahkan efek flash saat baris di-hover
            $('#perusahaanTable tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('row-hover-active');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('row-hover-active');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush