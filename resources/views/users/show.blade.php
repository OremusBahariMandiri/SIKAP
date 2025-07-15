@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-user me-2"></i>Detail Pengguna</span>
                    <div>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Grouped information sections with cards -->
                    <div class="row g-4">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informasi Personal</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">NIK Karyawan</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <div class="form-control">{{ $user->NikKry }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Nama Karyawan</label>
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
                                </div>
                            </div>
                        </div>

                        <!-- Work Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Informasi Kerja</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Jabatan</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <div class="form-control">{{ $user->JabatanKry }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Wilayah Kerja</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <div class="form-control">{{ $user->WilkerKry }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Password</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                                <div class="form-control">••••••••</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.75em;
    }
    .text-muted {
        color: #6c757d !important;
    }
    .bg-light {
        background-color: #f8f9fa;
    }
    .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
    }
</style>
@endpush