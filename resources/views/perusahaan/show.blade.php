@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-building me-2"></i>Detail Perusahaan</span>
                    <div>
                        <a href="{{ route('perusahaan.edit', $perusahaan->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('perusahaan.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Grouped information sections with cards -->
                    <div class="row g-4">
                        <!-- Company Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Perusahaan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">ID Kode</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                <div class="form-control">{{ $perusahaan->IdKode }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Nama Perusahaan</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <div class="form-control">{{ $perusahaan->NamaPrsh }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Alamat Perusahaan</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <div class="form-control" style="min-height: 100px; white-space: pre-wrap;">{{ $perusahaan->AlamatPrsh }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Tanggal Berdiri</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->TglBerdiri)
                                                        {{ $perusahaan->TglBerdiri->format('d/m/Y') }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Website</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->WebPrsh)
                                                        <a href="{{ $perusahaan->WebPrsh }}" target="_blank" class="text-decoration-none">
                                                            {{ $perusahaan->WebPrsh }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Informasi Kontak</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Telepon 1</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <div class="form-control">{{ $perusahaan->TelpPrsh }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Telepon 2</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->TelpPrsh2)
                                                        {{ $perusahaan->TelpPrsh2 }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Email 1</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <div class="form-control">{{ $perusahaan->EmailPrsh }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Email 2</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->EmailPrsh2)
                                                        {{ $perusahaan->EmailPrsh2 }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-industry me-2"></i>Informasi Usaha</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Bidang Usaha</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->BidangUsh)
                                                        {{ $perusahaan->BidangUsh }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Izin Usaha</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-certificate"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->IzinUsh)
                                                        {{ $perusahaan->IzinUsh }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Golongan Usaha</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->GolonganUsh)
                                                        <span class="badge bg-primary">{{ $perusahaan->GolonganUsh }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Management Information -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Informasi Manajemen</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Direktur Utama</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->DirekturUtm)
                                                        {{ $perusahaan->DirekturUtm }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Direktur</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->Direktur)
                                                        {{ $perusahaan->Direktur }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Komisaris Utama</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->KomisarisUtm)
                                                        {{ $perusahaan->KomisarisUtm }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Komisaris</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                <div class="form-control">
                                                    @if ($perusahaan->Komisaris)
                                                        {{ $perusahaan->Komisaris }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Legal Section -->
                    {{-- <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Dokumen Legal Terkait</h5>
                                </div>
                                <div class="card-body">
                                    @if ($perusahaan->dokumenLegal()->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="15%">No. Reg</th>
                                                        <th width="15%">Kategori</th>
                                                        <th width="15%">Jenis</th>
                                                        <th width="15%">Tanggal Terbit</th>
                                                        <th width="15%">Tanggal Berakhir</th>
                                                        <th width="20%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($perusahaan->dokumenLegal as $index => $dokumen)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>{{ $dokumen->NoRegDok }}</td>
                                                            <td>
                                                                <span class="badge bg-secondary">{{ $dokumen->KategoriDok }}</span>
                                                            </td>
                                                            <td>{{ $dokumen->JenisDok }}</td>
                                                            <td>{{ $dokumen->TglTerbitDok->format('d/m/Y') }}</td>
                                                            <td>
                                                                @if ($dokumen->TglBerakhirDok)
                                                                    {{ $dokumen->TglBerakhirDok->format('d/m/Y') }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ route('dokLegal.show', $dokumen->id) }}"
                                                                   class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye me-1"></i>Detail
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <span>Tidak ada dokumen legal terkait dengan perusahaan ini.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> --}}
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
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.05);
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
    .alert {
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }
</style>
@endpush