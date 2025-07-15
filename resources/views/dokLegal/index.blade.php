{{-- resources/views/dokLegal/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container-fluid dokLegalPage">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-file-contract me-2"></i>Manajemen Dokumen Legal</span>
                        <div>
                            <button type="button" class="btn btn-light me-2" id="filterButton">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-light me-2" id="exportButton">
                                <i class="fas fa-download me-1"></i> Export
                            </button>
                            @if ($hasCreatePermission)
                                <a href="{{ route('dokLegal.create') }}" class="btn btn-light">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah
                                </a>
                            @endif
                        </div>
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

                        <div class="mb-3 document-status-summary">
                            <span id="expiredDocsBadge" class="badge bg-danger me-2" style="font-size: 0.9rem;">
                                <i class="fas fa-exclamation-circle me-1"></i> Dokumen Expired : <span
                                    id="expiredDocsCount">0</span>
                            </span>
                            <span id="warningDocsBadge" class="badge bg-warning text-dark me-2" style="font-size: 0.9rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Dokumen Akan Expired : <span
                                    id="warningDocsCount">0</span>
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table id="dokLegalTable" class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Reg</th>
                                        <th>Perusahaan</th>
                                        <th>Kategori</th>
                                        <th>Jenis</th>
                                        <th>Peruntukan</th>
                                        <th>Atas Nama</th>
                                        <th>Tgl Terbit</th>
                                        <th>Tgl Berakhir</th>
                                        <th>Tgl Peringatan</th>
                                        <th>Peringatan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokLegals as $index => $dokLegal)
                                        <tr
                                            data-tgl-peringatan="{{ $dokLegal->TglPengingat ? $dokLegal->TglPengingat->format('Y-m-d') : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $dokLegal->NoRegDok }}</td>
                                            <td>{{ $dokLegal->DokPerusahaan }}</td>
                                            <td>{{ $dokLegal->KategoriDok }}</td>
                                            <td>{{ $dokLegal->JenisDok }}</td>

                                            <td>{{ $dokLegal->PeruntukanDok }}</td>
                                            <td>{{ $dokLegal->DokAtasNama }}</td>
                                            <td>{{ $dokLegal->TglTerbitDok->format('d/m/Y') }}</td>

                                            <td>{{ $dokLegal->TglBerakhirDok ? $dokLegal->TglBerakhirDok->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>{{ $dokLegal->TglPengingat ? $dokLegal->TglPengingat->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="sisa-peringatan-col">{{ $dokLegal->MasaPengingat }}</td>
                                            <td class="text-center">
                                                @if ($dokLegal->StsBerlakuDok == 'Berlaku')
                                                    <span class="badge bg-success">{{ $dokLegal->StsBerlakuDok }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning text-dark">{{ $dokLegal->StsBerlakuDok }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if ($hasViewPermission)
                                                        <a href="{{ route('dokLegal.show', $dokLegal) }}"
                                                            class="btn btn-sm btn-info text-white" data-bs-toggle="tooltip"
                                                            title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    @if ($hasEditPermission)
                                                        <a href="{{ route('dokLegal.edit', $dokLegal) }}"
                                                            class="btn btn-sm btn-secondary" data-bs-toggle="tooltip"
                                                            title="Edit">
                                                            <i class="fas fa-edit text-white"></i>
                                                        </a>
                                                    @endif

                                                    @if ($hasDownloadPermission && $dokLegal->FileDok)
                                                        <a href="{{ route('dokLegal.download', $dokLegal) }}"
                                                            class="btn btn-sm btn-success" data-bs-toggle="tooltip"
                                                            title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif

                                                    @if ($hasDeletePermission)
                                                        <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                            data-id="{{ $dokLegal->id }}"
                                                            data-name="{{ $dokLegal->NoRegDok }}" data-bs-toggle="tooltip"
                                                            data-has-permission="true" title="Hapus">
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
                        <i class="fas fa-filter me-2"></i>Filter Dokumen Legal
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_noreg" class="form-label">Nomor Registrasi</label>
                                    <input type="text" class="form-control" id="filter_noreg"
                                        placeholder="Masukan No Registrasi">
                                </div>
                                <div class="mb-3">
                                    <label for="filter_perusahaan" class="form-label">Perusahaan</label>
                                    <select class="form-select" id="filter_perusahaan">
                                        <option value="">Semua Perusahaan</option>
                                        @foreach ($perusahaans as $id => $nama)
                                            <option value="{{ $nama }}">{{ $nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filter_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="filter_kategori">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filter_peruntukan" class="form-label">Peruntukan</label>
                                    <select class="form-select" id="filter_peruntukan">
                                        <option value="">Semua Peruntukan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_jenis" class="form-label">Jenis Dokumen</label>
                                    <select class="form-select" id="filter_jenis">
                                        <option value="">Semua Jenis</option>
                                        @foreach ($jenisDoks as $jenis)
                                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filter_atas_nama" class="form-label">Atas Nama</label>
                                    <select class="form-select" id="filter_atas_nama">
                                        <option value="">Semua Atas Nama</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="filter_tgl_terbit" class="form-label">Tanggal Terbit</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter_tgl_terbit_from"
                                            placeholder="Dari">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="filter_tgl_terbit_to"
                                            placeholder="Sampai">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="filter_tgl_berakhir" class="form-label">Tanggal Berakhir</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="filter_tgl_berakhir_from"
                                            placeholder="Dari">
                                        <span class="input-group-text">s/d</span>
                                        <input type="date" class="form-control" id="filter_tgl_berakhir_to"
                                            placeholder="Sampai">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_status" class="form-label">Status Dokumen</label>
                                    <select class="form-select" id="filter_status">
                                        <option value="">Semua Status</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="hampir_kedaluwarsa">Hampir Kedaluwarsa (30 hari)</option>
                                        <option value="kedaluwarsa">Kedaluwarsa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filter_sts_berlaku" class="form-label">Status Berlaku</label>
                                    <select class="form-select" id="filter_sts_berlaku">
                                        <option value="">Semua</option>
                                        <option value="Berlaku">Berlaku</option>
                                        <option value="Tidak Berlaku">Tidak Berlaku</option>
                                    </select>
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus dokumen dengan No. Reg <strong id="dokNoRegToDelete"></strong>?
                    </p>
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
                        <button type="button" class="btn btn-danger" id="exportPdf">
                            <i class="fas fa-file-pdf me-2"></i> Ekspor ke PDF
                        </button>
                        <button type="button" class="btn btn-secondary" id="exportPrint">
                            <i class="fas fa-print me-2"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- BAGIAN STYLE - LETAKKAN DI @push('styles') -->
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.bootstrap5.min.css">
        <style>
            /* CSS dengan spesifisitas tinggi untuk DataTables */
            .dokLegalPage .dataTables_wrapper .dataTables_length,
            .dokLegalPage .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem !important;
            }

            .dokLegalPage .dataTables_wrapper .dataTables_filter {
                text-align: right !important;
                margin-right: 0 !important;
            }

            .dokLegalPage .dataTables_wrapper .dataTables_filter label {
                display: inline-flex !important;
                align-items: center !important;
                margin-bottom: 0 !important;
                font-weight: normal !important;
            }

            .dokLegalPage .dataTables_wrapper .dataTables_filter input {
                margin-left: 5px !important;
                border-radius: 4px !important;
                border: 1px solid #ced4da !important;
                padding: 0.375rem 0.75rem !important;
                width: 200px !important;
                max-width: 100% !important;
            }

            .dokLegalPage table.dataTable thead th {
                position: relative;
                background-image: none !important;
            }

            .dokLegalPage table.dataTable thead th.sorting:after,
            .dokLegalPage table.dataTable thead th.sorting_asc:after,
            .dokLegalPage table.dataTable thead th.sorting_desc:after {
                position: absolute;
                top: 12px;
                right: 8px;
                display: block;
                font-family: "Font Awesome 5 Free";
            }

            .dokLegalPage table.dataTable thead th.sorting:after {
                content: "\f0dc";
                color: #ddd;
                font-size: 0.8em;
                opacity: 0.5;
            }

            .dokLegalPage table.dataTable thead th.sorting_asc:after {
                content: "\f0de";
            }

            .dokLegalPage table.dataTable thead th.sorting_desc:after {
                content: "\f0dd";
            }

            /* ===== HIGHLIGHT ROWS STYLING ===== */
            /* Custom CSS untuk highlight rows */
            .dokLegalPage table#dokLegalTable tbody tr.highlight-red {
                background-color: #fc0000 !important;
                color: rgb(0, 0, 0) !important;
                --bs-table-accent-bg: none !important;
                --bs-table-striped-bg: none !important;
            }

            .dokLegalPage table#dokLegalTable tbody tr.highlight-yellow {
                background-color: #ffff00 !important;
                color: rgb(0, 0, 0) !important;
                --bs-table-accent-bg: none !important;
                --bs-table-striped-bg: none !important;
            }

            .dokLegalPage table#dokLegalTable tbody tr.highlight-orange {
                background-color: #ffaa00 !important;
                color: rgb(0, 0, 0) !important;
                --bs-table-accent-bg: none !important;
                --bs-table-striped-bg: none !important;
            }

            /* Ensure hover states don't override highlight colors */
            .dokLegalPage table#dokLegalTable tbody tr.highlight-red:hover {
                background-color: #ff3333 !important;
            }

            .dokLegalPage table#dokLegalTable tbody tr.highlight-yellow:hover {
                background-color: #ffff66 !important;
            }

            .dokLegalPage table#dokLegalTable tbody tr.highlight-orange:hover {
                background-color: #ffbb33 !important;
            }

            /* Override Bootstrap's striped table styles */
            .dokLegalPage .table-striped>tbody>tr:nth-of-type(odd).highlight-red,
            .dokLegalPage .table-striped>tbody>tr:nth-of-type(even).highlight-red {
                background-color: #fc0000 !important;
            }

            .dokLegalPage .table-striped>tbody>tr:nth-of-type(odd).highlight-yellow,
            .dokLegalPage .table-striped>tbody>tr:nth-of-type(even).highlight-yellow {
                background-color: #ffff00 !important;
            }

            .dokLegalPage .table-striped>tbody>tr:nth-of-type(odd).highlight-orange,
            .dokLegalPage .table-striped>tbody>tr:nth-of-type(even).highlight-orange {
                background-color: #ffaa00 !important;
            }

            /* Memastikan kolom tabel tetap terlihat meskipun dalam baris yang di-highlight */
            .dokLegalPage table#dokLegalTable tbody tr.highlight-red>td,
            .dokLegalPage table#dokLegalTable tbody tr.highlight-yellow>td,
            .dokLegalPage table#dokLegalTable tbody tr.highlight-orange>td {
                background-color: inherit !important;
            }

            /* Add hover effect to action buttons */
            .dokLegalPage .btn-sm {
                transition: transform 0.2s;
            }

            .dokLegalPage .btn-sm:hover {
                transform: scale(1.1);
            }

            /* Hover effect for table rows */
            .dokLegalPage #dokLegalTable tbody tr {
                transition: all 0.2s ease;
            }

            .dokLegalPage #dokLegalTable tbody tr:hover {
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                transform: translateY(-2px);
                cursor: pointer;
                position: relative;
                z-index: 1;
            }

            /* Flash effect when hovering */
            @keyframes flashBorder {
                0% {
                    box-shadow: 0 0 0 rgba(13, 110, 253, 0);
                }

                50% {
                    box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
                }

                100% {
                    box-shadow: 0 0 0 rgba(13, 110, 253, 0);
                }
            }

            .dokLegalPage #dokLegalTable tbody tr.row-hover-active {
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



    <!-- BAGIAN JAVASCRIPT - LETAKKAN DI @push('scripts') -->
        @push('scripts')
            <!-- DataTables JS -->
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
            <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>
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
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });

                    // Format tanggal untuk filter
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            // Tanggal terbit filter
                            let terbitFrom = $('#filter_tgl_terbit_from').val();
                            let terbitTo = $('#filter_tgl_terbit_to').val();
                            let terbitDate = moment(data[7], 'DD/MM/YYYY');

                            if ((terbitFrom === '' && terbitTo === '') ||
                                (terbitFrom === '' && terbitDate.isSameOrBefore(moment(terbitTo))) ||
                                (terbitTo === '' && terbitDate.isSameOrAfter(moment(terbitFrom))) ||
                                (terbitDate.isBetween(moment(terbitFrom), moment(terbitTo), null, '[]'))) {
                                // Tanggal berakhir filter
                                let berakhirFrom = $('#filter_tgl_berakhir_from').val();
                                let berakhirTo = $('#filter_tgl_berakhir_to').val();
                                let berakhirDate = data[8] !== '-' ? moment(data[8], 'DD/MM/YYYY') : null;

                                if (berakhirDate === null) {
                                    if (berakhirFrom === '' && berakhirTo === '') {
                                        return true;
                                    } else {
                                        return false;
                                    }
                                }

                                if ((berakhirFrom === '' && berakhirTo === '') ||
                                    (berakhirFrom === '' && berakhirDate.isSameOrBefore(moment(berakhirTo))) ||
                                    (berakhirTo === '' && berakhirDate.isSameOrAfter(moment(berakhirFrom))) ||
                                    (berakhirDate.isBetween(moment(berakhirFrom), moment(berakhirTo), null, '[]'))) {
                                    // Filter status dokumen
                                    let status = $('#filter_status').val();
                                    if (status === '') {
                                        return true;
                                    } else if (status === 'aktif') {
                                        return berakhirDate === null || berakhirDate.isAfter(moment().add(30, 'days'));
                                    } else if (status === 'hampir_kedaluwarsa') {
                                        return berakhirDate !== null &&
                                            berakhirDate.isAfter(moment()) &&
                                            berakhirDate.isBefore(moment().add(30, 'days'));
                                    } else if (status === 'kedaluwarsa') {
                                        return berakhirDate !== null && berakhirDate.isBefore(moment());
                                    }
                                    return true;
                                }
                                return false;
                            }
                            return false;
                        }
                    );

                    // Inisialisasi DataTable
                    var table = $('#dokLegalTable').DataTable({
                        responsive: true,
                        language: indonesianLanguage,
                        columnDefs: [{
                                // Prioritas tertinggi untuk kolom yang paling penting
                                responsivePriority: 1,
                                targets: [0, 1, 5, 10, 11] // No, No.Reg, Peruntukan, Peringatan, Status
                            },
                            {
                                // Prioritas kedua untuk kolom penting lainnya
                                responsivePriority: 2,
                                targets: [2, 3, 4] // Perusahaan, Kategori, Jenis
                            },
                            {
                                // Prioritas ketiga untuk kolom tanggal
                                responsivePriority: 3,
                                targets: [7, 8, 9] // Tgl Terbit, Tgl Berakhir, Tgl Peringatan
                            },
                            {
                                // Prioritas keempat untuk kolom yang tidak terlalu penting
                                responsivePriority: 4,
                                targets: [6, 12] // Atas Nama, Aksi
                            },
                            {
                                // Kolom yang tidak bisa di-sort
                                orderable: false,
                                targets: [0, 12] // No dan Aksi
                            }
                        ],
                        order: [
                            [0, 'asc']
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
                            // Mendapatkan data unik untuk dropdown Peruntukan dan Atas Nama
                            let uniquePeruntukan = new Set();
                            let uniqueAtasNama = new Set();

                            // Iterasi semua data pada tabel untuk mengumpulkan nilai unik
                            $('#dokLegalTable tbody tr').each(function() {
                                const peruntukan = $(this).find('td:eq(5)').text()
                                    .trim(); // Kolom Peruntukan (index 5)
                                const atasNama = $(this).find('td:eq(6)').text()
                                    .trim(); // Kolom Atas Nama (index 6)

                                if (peruntukan) uniquePeruntukan.add(peruntukan);
                                if (atasNama) uniqueAtasNama.add(atasNama);
                            });

                            // Mengisi dropdown Peruntukan dengan nilai unik yang ditemukan
                            $('#filter_peruntukan').find('option:not(:first)').remove();
                            Array.from(uniquePeruntukan).sort().forEach(function(peruntukan) {
                                $('#filter_peruntukan').append('<option value="' + peruntukan + '">' +
                                    peruntukan + '</option>');
                            });

                            // Mengisi dropdown Atas Nama dengan nilai unik yang ditemukan
                            $('#filter_atas_nama').find('option:not(:first)').remove();
                            Array.from(uniqueAtasNama).sort().forEach(function(atasNama) {
                                $('#filter_atas_nama').append('<option value="' + atasNama + '">' +
                                    atasNama + '</option>');
                            });

                            // Implementasi custom filter untuk kolom-kolom lainnya (Perusahaan, Kategori, Jenis)
                            this.api().columns([2, 3, 4]).each(function(index) {
                                let column = this;
                                let select = null;

                                // Menentukan select berdasarkan index kolom
                                switch (index) {
                                    case 0: // Perusahaan (kolom index 2)
                                        select = $('#filter_perusahaan');
                                        break;
                                    case 1: // Kategori (kolom index 3)
                                        select = $('#filter_kategori');
                                        break;
                                    case 2: // Jenis (kolom index 4)
                                        select = $('#filter_jenis');
                                        break;
                                }

                                // Populate opsi filter dengan nilai unik dari kolom
                                if (select) {
                                    // Reset select options (kecuali opsi default "Semua")
                                    select.find('option:not(:first)').remove();

                                    // Tambahkan opsi dari data kolom
                                    let uniqueValues = new Set();
                                    column.data().unique().sort().each(function(d, j) {
                                        if (d && d.trim() !== '' && !uniqueValues.has(d)) {
                                            uniqueValues.add(d);
                                            select.append('<option value="' + d + '">' + d +
                                                '</option>');
                                        }
                                    });
                                }
                            });

                            // Force style search input box
                            $('.dataTables_filter input').addClass('form-control');

                            // Perbarui masa peringatan dan terapkan highlighting
                            updateMasaPeringatanText();
                            applyOriginalHighlighting();
                        }
                    });

                    // Fungsi untuk memperbarui teks masa peringatan tanpa mengubah highlighting
                    function updateMasaPeringatanText() {
                        const today = moment();

                        $('#dokLegalTable tbody tr').each(function() {
                            const tglPengingatStr = $(this).data('tgl-peringatan');
                            const $masaPeringatanCol = $(this).find('.sisa-peringatan-col');

                            // Jika tidak ada tanggal pengingat, lewati baris ini
                            if (!tglPengingatStr) {
                                $masaPeringatanCol.text('-');
                                return;
                            }

                            // Parse tanggal pengingat
                            const tglPengingat = moment(tglPengingatStr);

                            // Hitung selisih dalam hari
                            const diffDays = tglPengingat.diff(today, 'days');

                            // Menentukan teks yang akan ditampilkan di kolom masa peringatan
                            let masaPeringatanText = '';

                            if (diffDays < 0) {
                                // Tanggal pengingat sudah lewat
                                masaPeringatanText = 'Terlambat ' + Math.abs(diffDays) + ' hari';
                            } else if (diffDays === 0) {
                                // Tanggal pengingat hari ini
                                masaPeringatanText = 'Hari ini';
                            } else {
                                // Tanggal pengingat di masa depan
                                masaPeringatanText = diffDays + ' hari lagi';
                            }

                            // Update teks masa peringatan
                            $masaPeringatanCol.text(masaPeringatanText);
                        });
                    }

                    // Fungsi terpadu untuk menerapkan highlighting berdasarkan aturan bisnis yang benar
                    function applyOriginalHighlighting() {
                        // Reset counters
                        let expiredCount = 0;
                        let warningCount = 0;

                        // Apply highlighting based on both dates
                        $('#dokLegalTable tbody tr').each(function() {
                            // Reset all highlight classes first
                            $(this).removeClass('highlight-red highlight-yellow highlight-orange');

                            const row = $(this);

                            // Check Tanggal Pengingat first (higher priority)
                            const tglPengingatStr = row.data('tgl-peringatan');
                            if (tglPengingatStr) {
                                const tglPengingat = moment(tglPengingatStr);
                                const today = moment();
                                const diffDays = tglPengingat.diff(today, 'days');

                                if (diffDays < 0 || diffDays === 0) {
                                    // Tanggal pengingat sudah lewat atau hari ini
                                    row.addClass('highlight-red');
                                    expiredCount++;
                                    return; // Stop here - red has priority
                                } else if (diffDays <= 7) {
                                    row.addClass('highlight-yellow');
                                    warningCount++;
                                    return; // Stop here - yellow has next priority
                                } else if (diffDays <= 30) {
                                    row.addClass('highlight-orange');
                                    warningCount++;
                                    return; // Stop here
                                }
                                // Tidak memberikan warna untuk pengingat > 30 hari
                            }

                            // If not highlighted by Tanggal Pengingat, check Tanggal Berakhir
                            const tglBerakhir = row.find('td:eq(8)').text();
                            if (tglBerakhir !== '-') {
                                const berakhirDate = moment(tglBerakhir, 'DD/MM/YYYY');
                                const today = moment();

                                if (berakhirDate.isBefore(today)) {
                                    row.addClass('highlight-red');
                                    expiredCount++;
                                } else if (berakhirDate.isBefore(moment().add(30, 'days'))) {
                                    // Hanya berikan warna orange untuk dokumen yang akan berakhir dalam 30 hari
                                    row.addClass('highlight-orange');
                                    warningCount++;
                                }
                                // Tidak memberikan warna lain untuk dokumen yang berakhirnya > 30 hari
                            }
                        });

                        // Update the badge counters
                        $('#expiredDocsCount').text(expiredCount);
                        $('#warningDocsCount').text(warningCount);

                        // Show/hide badges
                        $('#expiredDocsBadge').toggle(expiredCount > 0);
                        $('#warningDocsBadge').toggle(warningCount > 0);
                    }

                    // Fungsi untuk reset dan menerapkan highlight dengan benar
                    function resetAndApplyHighlighting() {
                        // Clear DataTable search and column filters but preserve the highlighting
                        table.search('').columns().search('').draw();

                        // Karena draw() pada DataTable bisa mengubah DOM, kita perlu mengaplikasikan ulang
                        // highlighting dengan benar berdasarkan data asli
                        applyOriginalHighlighting();
                    }

                    // Event untuk filter dan export button
                    $('#filterButton').on('click', function() {
                        $('#filterModal').modal('show');
                    });

                    $('#exportButton').on('click', function() {
                        $('#exportModal').modal('show');
                    });

                    // Modifikasi event handler untuk tombol Apply Filter
                    $('#applyFilter').on('click', function() {
                        // Terapkan filter untuk kolom-kolom
                        table.column(1).search($('#filter_noreg').val());
                        table.column(2).search($('#filter_perusahaan').val());
                        table.column(3).search($('#filter_kategori').val());
                        table.column(4).search($('#filter_jenis').val());
                        table.column(5).search($('#filter_peruntukan').val());
                        table.column(6).search($('#filter_atas_nama').val());

                        // Filter status berlaku
                        if ($('#filter_sts_berlaku').val()) {
                            table.column(11).search($('#filter_sts_berlaku').val());
                        }

                        // Refresh table untuk menerapkan semua filter
                        table.draw();
                        $('#filterModal').modal('hide');

                        // Highlight filter button jika ada filter aktif
                        highlightFilterButton();

                        // Penting: Pastikan warna highlight dipertahankan setelah filter
                        // Kita perlu memanggil ini setelah table.draw() karena draw() mengubah DOM
                        applyOriginalHighlighting();
                    });

                    // Modify the Reset Filter event handler
                    $('#resetFilter').on('click', function() {
                        // Reset the form fields
                        $('#filterForm')[0].reset();

                        // Remove active class from filter button
                        $('#filterButton').removeClass('filter-active');

                        // Apply proper highlighting while preserving original colors
                        resetAndApplyHighlighting();
                    });

                    // Event listener untuk table draw event - untuk mempertahankan warna highlight setelah filter/paging/search
                    table.on('draw', function() {
                        // Pertahankan warna highlight asli setelah operasi tabel
                        applyOriginalHighlighting();
                    });

                    // Highlight filter button jika ada filter aktif
                    function highlightFilterButton() {
                        if ($('#filter_noreg').val() ||
                            $('#filter_perusahaan').val() ||
                            $('#filter_kategori').val() ||
                            $('#filter_jenis').val() ||
                            $('#filter_peruntukan').val() ||
                            $('#filter_atas_nama').val() ||
                            $('#filter_tgl_terbit_from').val() ||
                            $('#filter_tgl_terbit_to').val() ||
                            $('#filter_tgl_berakhir_from').val() ||
                            $('#filter_tgl_berakhir_to').val() ||
                            $('#filter_status').val() ||
                            $('#filter_sts_berlaku').val()) {
                            $('#filterButton').addClass('filter-active');
                        } else {
                            $('#filterButton').removeClass('filter-active');
                        }
                    }

                    // Export buttons
                    $('#exportExcel').on('click', function() {
                        $('.excel-export-btn').trigger('click');
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

                    // ======================== FIXED DELETE CONFIRMATION HANDLER ========================
                    // Handle Delete Confirmation - FIXED VERSION
                    $(document).on('click', '.delete-confirm', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var id = $(this).data('id');
                        var name = $(this).data('name');

                        // Langsung tampilkan modal konfirmasi tanpa cek permission
                        $('#dokNoRegToDelete').text(name);
                        var deleteUrl = "{{ url('dokLegal') }}/" + id;
                        $('#deleteForm').attr('action', deleteUrl);
                        $('#deleteConfirmationModal').modal('show');
                    });

                    // Alternative approach: Handle permission check with custom confirmation dialog
                    function showDeleteConfirmation(id, name, hasPermission) {
                        if (!hasPermission) {
                            // Use a custom styled alert instead of modal
                            showAccessDeniedAlert();
                            return;
                        }

                        // Show delete confirmation using SweetAlert2
                        Swal.fire({
                            title: 'Konfirmasi Hapus',
                            html: `
                                <div class="text-start">
                                    <p>Apakah Anda yakin ingin menghapus dokumen dengan No. Reg <strong>${name}</strong>?</p>
                                    <p class="text-danger"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan!</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fas fa-trash me-1"></i>Hapus',
                            cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
                            customClass: {
                                popup: 'swal-wide'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Create and submit delete form
                                var form = $('<form>', {
                                    'method': 'POST',
                                    'action': "{{ url('dokLegal') }}/" + id
                                });

                                form.append($('<input>', {
                                    'type': 'hidden',
                                    'name': '_token',
                                    'value': $('meta[name="csrf-token"]').attr('content')
                                }));

                                form.append($('<input>', {
                                    'type': 'hidden',
                                    'name': '_method',
                                    'value': 'DELETE'
                                }));

                                $('body').append(form);
                                form.submit();
                            }
                        });
                    }

                    // Function to show access denied alert
                    function showAccessDeniedAlert() {
                        // Create a custom toast notification
                        const toast = $(`
                            <div class="toast align-items-center text-white bg-danger border-0 position-fixed"
                                 style="top: 20px; right: 20px; z-index: 9999;"
                                 role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <strong>Akses Ditolak!</strong><br>
                                        Anda tidak memiliki hak akses untuk menghapus dokumen.
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                            data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </div>
                        `);

                        $('body').append(toast);
                        const bsToast = new bootstrap.Toast(toast[0], {
                            autohide: true,
                            delay: 5000
                        });
                        bsToast.show();

                        // Remove toast element after it's hidden
                        toast.on('hidden.bs.toast', function() {
                            $(this).remove();
                        });
                    }

                    // Alternative delete button handler using the new approach
                    $(document).on('click', '.delete-confirm-alt', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var hasPermission = $(this).data('has-permission') === 'true';
                        var id = $(this).data('id');
                        var name = $(this).data('name');

                        showDeleteConfirmation(id, name, hasPermission);
                    });

                    // ======================== END FIXED DELETE CONFIRMATION HANDLER ========================

                    // Tambahkan efek klik pada baris tabel untuk menuju halaman detail
                    $('#dokLegalTable tbody').on('click', 'tr', function(e) {
                        // Jangan ikuti link jika yanaccg diklik adalah tombol atau link di dalam baris
                        if ($(e.target).is('button') || $(e.target).is('a') || $(e.target).is('i') ||
                            $(e.target).closest('button').length || $(e.target).closest('a').length) {
                            return;
                        }

                        // Dapatkan ID dokumen dari tombol detail
                        var detailLink = $(this).find('a[title="Detail"]').attr('href');
                        if (detailLink) {
                            window.location.href = detailLink;
                        }
                    });

                    // Tambahkan efek flash saat baris di-hover
                    $('#dokLegalTable tbody').on('mouseenter', 'tr', function() {
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
