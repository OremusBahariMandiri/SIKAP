@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-eye me-2"></i>Detail Dokumen Legal</span>
                    <div>
                        <a href="{{ route('dokLegal.edit', $dokLegal) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('dokLegal.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- Basic Document Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi Dokumen</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-hashtag text-primary me-1"></i> Nomor Register:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->NoRegDok }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-building text-primary me-1"></i> Perusahaan:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->DokPerusahaan }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-folder text-primary me-1"></i> Kategori Dokumen:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->KategoriDok }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-file-invoice text-primary me-1"></i> Jenis Dokumen:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->JenisDok }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-info-circle text-primary me-1"></i> Peruntukan:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->PeruntukanDok }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-user text-primary me-1"></i> Atas Nama:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->DokAtasNama }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validity and Date Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku & Tanggal</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-history text-success me-1"></i> Jenis Masa Berlaku:
                                        </div>
                                        <div class="col-lg-7">
                                            @if($dokLegal->JnsMasaBerlaku == 'Tetap')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-infinity me-1"></i>Tetap
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sync me-1"></i>Perpanjangan
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-calendar text-success me-1"></i> Tanggal Terbit:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->TglTerbitDok ? $dokLegal->TglTerbitDok->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-calendar-times text-success me-1"></i> Tanggal Berakhir:
                                        </div>
                                        <div class="col-lg-7">
                                            @if($dokLegal->TglBerakhirDok)
                                                {{ $dokLegal->TglBerakhirDok->format('d/m/Y') }}

                                                @php
                                                    $today = \Carbon\Carbon::now();
                                                    $berakhir = \Carbon\Carbon::parse($dokLegal->TglBerakhirDok);
                                                    $selisihHari = $today->diffInDays($berakhir, false);
                                                @endphp

                                                @if($selisihHari < 0)
                                                    <span class="badge bg-danger ms-1">Kedaluwarsa</span>
                                                @elseif($selisihHari <= 30)
                                                    <span class="badge bg-warning text-dark ms-1">{{ $selisihHari }} hari lagi</span>
                                                @else
                                                    <span class="badge bg-success ms-1">Aktif</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-hourglass-half text-success me-1"></i> Masa Berlaku:
                                        </div>
                                        <div class="col-lg-7">
                                            <span class="badge bg-light text-dark border">{{ $dokLegal->MasaBerlaku }}</span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-bell text-success me-1"></i> Tanggal Pengingat:
                                        </div>
                                        <div class="col-lg-7">
                                            {{ $dokLegal->TglPengingat ? $dokLegal->TglPengingat->format('d/m/Y') : 'Tidak ada' }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-stopwatch text-success me-1"></i> Masa Pengingat:
                                        </div>
                                        <div class="col-lg-7">
                                            <span class="badge bg-light text-dark border">{{ $dokLegal->MasaPengingat }}</span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-lg-5 fw-bold text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i> Status Berlaku:
                                        </div>
                                        <div class="col-lg-7">
                                            @if($dokLegal->StsBerlakuDok == 'Berlaku')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Berlaku
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Tidak Berlaku
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-12">
                            <div class="card border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-muted mb-3">
                                                    <i class="fas fa-file-upload text-warning me-1"></i> File Dokumen:
                                                </h6>
                                                @if ($dokLegal->FileDok)
                                                    <div class="d-flex align-items-center bg-light p-3 rounded">
                                                        <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $dokLegal->FileDok }}</div>
                                                            <a href="{{ route('dokLegal.download', $dokLegal) }}" class="btn btn-sm btn-primary mt-2">
                                                                <i class="fas fa-download me-1"></i> Download File
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-secondary">
                                                        <i class="fas fa-exclamation-circle me-2"></i>Tidak ada file dokumen yang diunggah
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-muted mb-3">
                                                    <i class="fas fa-sticky-note text-warning me-1"></i> Keterangan:
                                                </h6>
                                                <div class="bg-light p-3 rounded" style="min-height: 100px;">
                                                    @if($dokLegal->KetDok)
                                                        {{ $dokLegal->KetDok }}
                                                    @else
                                                        <span class="text-muted fst-italic">Tidak ada keterangan</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('dokLegal.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                        <a href="{{ route('dokLegal.edit', $dokLegal) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Dokumen
                        </a>
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
    .card {
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .text-muted {
        color: #6c757d !important;
    }
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .border {
        border: 1px solid #dee2e6 !important;
    }
    .rounded {
        border-radius: 0.25rem !important;
    }
</style>
@endpush