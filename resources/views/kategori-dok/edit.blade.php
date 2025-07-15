@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-edit me-2"></i>Edit Kategori Dokumen</span>
                    <a href="{{ route('kategori-dok.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('kategori-dok.update', $kategoriDok->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="text" class="form-control" id="IdKode" name="IdKode" value="{{ old('IdKode', $kategoriDok->IdKode) }}" hidden readonly>

                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Kategori</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-group mb-3">
                                            <label for="KategoriDok" class="info-label fw-bold">Kategori Dokumen</label>
                                            <div class="info-value">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                    <input type="text" class="form-control" id="KategoriDok" name="KategoriDok" value="{{ old('KategoriDok', $kategoriDok->KategoriDok) }}" required>
                                                </div>
                                                <small class="text-muted mt-1">Contoh: Dokumen Legalitas & Perizinan, Dokumen Kontrak & Perjanjian</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update
                            </button>
                        </div>
                    </form>
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