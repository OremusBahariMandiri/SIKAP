@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-folder me-2"></i>Detail Kategori Dokumen</span>
                    <div>
                        <a href="{{ route('kategori-dok.edit', $kategoriDok->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('kategori-dok.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Kategori Information -->
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kategori</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Kategori Dokumen</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                <div class="form-control">{{ $kategoriDok->KategoriDok }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Dibuat pada</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                                                <div class="form-control">{{ $kategoriDok->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-group mb-3">
                                        <label class="info-label fw-bold">Diperbarui pada</label>
                                        <div class="info-value">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                                <div class="form-control">{{ $kategoriDok->updated_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Terkait Section -->
                    {{-- <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning bg-opacity-25 text-dark">
                                    <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Dokumen Legal dengan Kategori Ini</h5>
                                </div>
                                <div class="card-body">
                                    @if($kategoriDok->dokumenLegal()->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="15%">No. Reg</th>
                                                        <th width="20%">Perusahaan</th>
                                                        <th width="15%">Jenis</th>
                                                        <th width="15%">Tanggal Terbit</th>
                                                        <th width="15%">Tanggal Berakhir</th>
                                                        <th width="15%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($kategoriDok->dokumenLegal as $index => $dokumen)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>{{ $dokumen->NoRegDok }}</td>
                                                            <td>{{ $dokumen->DokPerusahaan }}</td>
                                                            <td>{{ $dokumen->JenisDok }}</td>
                                                            <td>{{ $dokumen->TglTerbitDok->format('d/m/Y') }}</td>
                                                            <td>
                                                                @if($dokumen->TglBerakhirDok)
                                                                    {{ $dokumen->TglBerakhirDok->format('d/m/Y') }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ route('dokLegal.show', $dokumen->id) }}" class="btn btn-sm btn-info">
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
                                            <span>Tidak ada dokumen legal dengan kategori ini.</span>
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