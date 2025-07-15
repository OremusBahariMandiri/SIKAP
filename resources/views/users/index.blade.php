@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-users me-2"></i>Manajemen Pengguna</span>
                        <a href="{{ route('users.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i> Tambah
                        </a>
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
                            <table class="table table-bordered table-striped data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Departemen</th>
                                        <th>Jabatan</th>
                                        <th>Wilayah Kerja</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->NikKry }}</td>
                                            <td>{{ $user->NamaKry }}</td>
                                            <td>{{ $user->DepartemenKry }}</td>
                                            <td>{{ $user->JabatanKry }}</td>
                                            <td>{{ $user->WilkerKry }}</td>
                                            <td class="text-center">
                                                @if ($user->isAdmin())
                                                    <span class="badge bg-danger">Administrator</span>
                                                @else
                                                    <span class="badge bg-primary">Pengguna</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('users.access.edit', $user->id) }}"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="Hak Akses">
                                                        <i class="fas fa-key"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-confirm"
                                                        data-id="{{ $user->id }}"
                                                        data-name="user {{ $user->NamaKry }}"
                                                        data-url="{{ route('users.destroy', $user->id) }}"
                                                        data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
                    <p>Apakah Anda yakin ingin menghapus user <strong id="userNameToDelete"></strong>?</p>
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

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize DataTable
            var table = $('#usersTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [{
                        responsivePriority: 1,
                        targets: [0, 2, 7] // Fixed index to match table columns
                    },
                    {
                        orderable: false,
                        targets: [7] // Fixed index for actions column
                    }
                ]
            });

            // Handle Delete Confirmation
            $('.delete-confirm').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                // Set user name in modal
                $('#userNameToDelete').text(name);

                // Set form action URL
                $('#deleteForm').attr('action', "{{ url('users') }}/" + id);

                // Show modal
                $('#deleteConfirmationModal').modal('show');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $(".alert").fadeOut("slow");
            }, 5000);
        });
    </script>
@endpush
