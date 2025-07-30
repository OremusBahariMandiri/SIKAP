@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-building me-2"></i>Edit Perusahaan</span>
                    <a href="{{ route('perusahaan.index') }}" class="btn btn-light btn-sm">
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

                    <form action="{{ route('perusahaan.update', $perusahaan->id) }}" method="POST" id="perusahaanForm">
                        @csrf
                        @method('PUT')
                        <input type="text" class="form-control" id="IdKode" name="IdKode" value="{{ old('IdKode', $perusahaan->IdKode) }}" hidden readonly>

                        <!-- Grouped form sections with cards -->
                        <div class="row g-4">
                            <!-- Company Information -->
                            <div class="col-md-6">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Perusahaan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="NamaPrsh" class="form-label fw-bold">Nama Perusahaan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <input type="text" class="form-control" id="NamaPrsh" name="NamaPrsh"
                                                    value="{{ old('NamaPrsh', $perusahaan->NamaPrsh) }}" placeholder="PT Example Indonesia" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="AlamatPrsh" class="form-label fw-bold">Alamat Perusahaan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <textarea class="form-control" id="AlamatPrsh" name="AlamatPrsh"
                                                    rows="4" placeholder="Jl. Contoh No. 123, Jakarta Selatan" required>{{ old('AlamatPrsh', $perusahaan->AlamatPrsh) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="TglBerdiri" class="form-label fw-bold">Tanggal Berdiri</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                                <input type="date" class="form-control" id="TglBerdiri" name="TglBerdiri"
                                                    value="{{ old('TglBerdiri', $perusahaan->TglBerdiri ? $perusahaan->TglBerdiri->format('Y-m-d') : '') }}">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="WebPrsh" class="form-label fw-bold">Website</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                                <input type="url" class="form-control" id="WebPrsh" name="WebPrsh"
                                                    value="{{ old('WebPrsh', $perusahaan->WebPrsh) }}" placeholder="https://example.com">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Format: https://alamat-website (Opsional)</div>
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
                                        <div class="form-group mb-3">
                                            <label for="TelpPrsh" class="form-label fw-bold">Telepon 1 <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" id="TelpPrsh" name="TelpPrsh"
                                                    value="{{ old('TelpPrsh', $perusahaan->TelpPrsh) }}" placeholder="" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="TelpPrsh2" class="form-label fw-bold">Telepon 2</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" id="TelpPrsh2" name="TelpPrsh2"
                                                    value="{{ old('TelpPrsh2', $perusahaan->TelpPrsh2 ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="EmailPrsh" class="form-label fw-bold">Email 1 <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="EmailPrsh" name="EmailPrsh"
                                                    value="{{ old('EmailPrsh', $perusahaan->EmailPrsh) }}" placeholder="" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="EmailPrsh2" class="form-label fw-bold">Email 2</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="EmailPrsh2" name="EmailPrsh2"
                                                    value="{{ old('EmailPrsh2', $perusahaan->EmailPrsh2 ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
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
                                        <div class="form-group mb-3">
                                            <label for="BidangUsh" class="form-label fw-bold">Bidang Usaha</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                <input type="text" class="form-control" id="BidangUsh" name="BidangUsh"
                                                    value="{{ old('BidangUsh', $perusahaan->BidangUsh ?? '') }}" placeholder="Contoh: Teknologi Informasi">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="IzinUsh" class="form-label fw-bold">Izin Usaha</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-certificate"></i></span>
                                                <input type="text" class="form-control" id="IzinUsh" name="IzinUsh"
                                                    value="{{ old('IzinUsh', $perusahaan->IzinUsh ?? '') }}" placeholder="Contoh: SIUP, TDP, NIB">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="GolonganUsh" class="form-label fw-bold">Golongan Usaha</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                                <select class="form-control" id="GolonganUsh" name="GolonganUsh">
                                                    <option value="">Pilih Golongan Usaha</option>
                                                    <option value="Mikro" {{ old('GolonganUsh', $perusahaan->GolonganUsh) == 'Mikro' ? 'selected' : '' }}>Mikro</option>
                                                    <option value="Kecil" {{ old('GolonganUsh', $perusahaan->GolonganUsh) == 'Kecil' ? 'selected' : '' }}>Kecil</option>
                                                    <option value="Menengah" {{ old('GolonganUsh', $perusahaan->GolonganUsh) == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                                    <option value="Besar" {{ old('GolonganUsh', $perusahaan->GolonganUsh) == 'Besar' ? 'selected' : '' }}>Besar</option>
                                                </select>
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
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
                                        <div class="form-group mb-3">
                                            <label for="DirekturUtm" class="form-label fw-bold">Direktur Utama</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="DirekturUtm" name="DirekturUtm"
                                                    value="{{ old('DirekturUtm', $perusahaan->DirekturUtm ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="Direktur" class="form-label fw-bold">Direktur</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="Direktur" name="Direktur"
                                                    value="{{ old('Direktur', $perusahaan->Direktur ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="KomisarisUtm" class="form-label fw-bold">Komisaris Utama</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                <input type="text" class="form-control" id="KomisarisUtm" name="KomisarisUtm"
                                                    value="{{ old('KomisarisUtm', $perusahaan->KomisarisUtm ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="Komisaris" class="form-label fw-bold">Komisaris</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-check"></i></span>
                                                <input type="text" class="form-control" id="Komisaris" name="Komisaris"
                                                    value="{{ old('Komisaris', $perusahaan->Komisaris ?? '') }}" placeholder="">
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Opsional</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-md-4 mx-auto mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Update
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
    .form-label {
        margin-bottom: 0.3rem;
    }
    .card {
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .text-danger {
        font-weight: bold;
    }
    .bg-light {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation with visual feedback
    const form = document.getElementById('perusahaanForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();

            // Highlight missing required fields
            document.querySelectorAll('[required]').forEach(function(input) {
                if (!input.value) {
                    input.classList.add('is-invalid');
                    // Create error message if it doesn't exist
                    if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Field ini wajib diisi';
                        input.parentNode.insertBefore(feedback, input.nextElementSibling);
                    }
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // Remove invalid class when input changes
    document.querySelectorAll('[required]').forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.value) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush