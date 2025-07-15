@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</span>
                    <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
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

                    <form action="{{ route('users.store') }}" method="POST" id="userForm">
                        @csrf
                        <input type="text" class="form-control" id="IdKode" name="IdKode" value="{{ old('IdKode', $idKode) }}" hidden readonly>

                        <!-- Grouped form sections with cards -->
                        <div class="row g-4">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card h-100 border-info">
                                    <div class="card-header bg-info bg-opacity-25 text-dark">
                                        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Informasi Personal</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="NikKry" class="form-label fw-bold">NIK Karyawan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input type="text" class="form-control" id="NikKry" name="NikKry"
                                                    value="{{ old('NikKry') }}" placeholder="" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="NamaKry" class="form-label fw-bold">Nama Karyawan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="NamaKry" name="NamaKry"
                                                    value="{{ old('NamaKry') }}" placeholder="" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="DepartemenKry" class="form-label fw-bold">Departemen <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <select class="form-control" id="DepartemenKry" name="DepartemenKry" required>
                                                    <option value="">-- Pilih Departemen --</option>
                                                    @foreach ($departemen as $key => $value)
                                                        <option value="{{ $key }}" {{ old('DepartemenKry') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
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
                                        <div class="form-group mb-3">
                                            <label for="JabatanKry" class="form-label fw-bold">Jabatan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="JabatanKry" name="JabatanKry"
                                                    value="{{ old('JabatanKry') }}" placeholder="" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="WilkerKry" class="form-label fw-bold">Wilayah Kerja <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <select class="form-control" id="WilkerKry" name="WilkerKry" required>
                                                    <option value="">-- Pilih Wilayah Kerja --</option>
                                                    @foreach ($wilayahKerja as $key => $value)
                                                        <option value="{{ $key }}" {{ old('WilkerKry') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="PasswordKry" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                            <div class="input-group password-input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="PasswordKry" name="PasswordKry"
                                                    placeholder="" required>
                                                <button type="button" class="btn btn-outline-secondary password-toggle" onclick="togglePassword('PasswordKry', 'password-eye-1')">
                                                    <i class="fas fa-eye" id="password-eye-1"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                                            <div class="input-group password-input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                                    placeholder="" required>
                                                <button type="button" class="btn btn-outline-secondary password-toggle" onclick="togglePassword('password_confirmation', 'password-eye-2')">
                                                    <i class="fas fa-eye" id="password-eye-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 col-md-4 mx-auto mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Simpan
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

    /* Password Toggle Styles */
    .password-input-group {
        position: relative;
    }

    .password-toggle {
        border-left: 0;
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border-color: #ced4da;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
    }

    .password-toggle:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .password-toggle:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }

    .password-toggle i {
        font-size: 0.875rem;
    }

    .input-group .password-toggle {
        z-index: 4;
    }
</style>
@endpush

@push('scripts')
<script>
// Password toggle function
function togglePassword(inputId, eyeId) {
    const passwordInput = document.getElementById(inputId);
    const passwordEye = document.getElementById(eyeId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordEye.classList.remove('fa-eye');
        passwordEye.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordEye.classList.remove('fa-eye-slash');
        passwordEye.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Form validation with visual feedback
    const form = document.getElementById('userForm');
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

    // Password confirmation validation
    const password = document.getElementById('PasswordKry');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Password tidak sama');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }

    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endpush