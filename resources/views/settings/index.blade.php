@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-cog me-2"></i>Pengaturan</span>
                        <a href="{{ route('home') }}" class="btn btn-warning">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        {{-- <button type="button" class="btn btn-warning btn-sm text-dark" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </button> --}}
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="card border-primmary mb-3">
                            <div class="card-header bg-secondary bg-opacity-25 text-dark">
                                <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Informasi Akun</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">NIK</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <div class="form-control">{{ $user->NikKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Nama</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <div class="form-control">{{ $user->NamaKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Departemen</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <div class="form-control">{{ $user->DepartemenKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Jabatan</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                            <div class="form-control">{{ $user->JabatanKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-group mb-3">
                                    <label class="info-label fw-bold">Wilker</label>
                                    <div class="info-value">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <div class="form-control">{{ $user->WilkerKry }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-key me-2"></i>Ubah Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('settings.update-password') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-text">Password minimal 6 karakter.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-header {
            font-weight: 600;
        }

        .info-label {
            margin-bottom: 0.3rem;
            display: block;
        }

        .info-group {
            margin-bottom: 1rem;
        }

        .info-value .form-control {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            min-height: 38px;
        }

        .card {
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Show modal with validation errors if any
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                var changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                changePasswordModal.show();
            @endif
        });
    </script>
@endpush
