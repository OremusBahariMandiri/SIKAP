@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Pengguna</span>
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

                    <form action="{{ route('users.update', $user->id) }}" method="POST" id="userEditForm">
                        @csrf
                        @method('PUT')
                        <input type="text" class="form-control" id="IdKode" name="IdKode" value="{{ old('IdKode', $user->IdKode) }}" hidden readonly>

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
                                                    value="{{ old('NikKry', $user->NikKry) }}" placeholder="Masukan NIK Karyawan" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="NamaKry" class="form-label fw-bold">Nama Karyawan <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="NamaKry" name="NamaKry"
                                                    value="{{ old('NamaKry', $user->NamaKry) }}" placeholder="Masukan Nama Karyawan" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="DepartemenKry" class="form-label fw-bold">Departemen <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <select class="form-control" id="DepartemenKry" name="DepartemenKry" required>
                                                    <option value="">-- Pilih Departemen --</option>
                                                    @foreach ($departemen as $key => $value)
                                                        <option value="{{ $key }}" {{ old('DepartemenKry', $user->DepartemenKry) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                                                    value="{{ old('JabatanKry', $user->JabatanKry) }}" placeholder="Masukan Jabatan Karyawan" required>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="WilkerKry" class="form-label fw-bold">Wilayah Kerja <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <select class="form-control" id="WilkerKry" name="WilkerKry" required>
                                                    <option value="">-- Pilih Wilayah Kerja --</option>
                                                    @foreach ($wilayahKerja as $key => $value)
                                                        <option value="{{ $key }}" {{ old('WilkerKry', $user->WilkerKry) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="PasswordKry" class="form-label fw-bold">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="PasswordKry" name="PasswordKry"
                                                    placeholder="Masukan Password Baru">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                                </button>
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password</div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                                    placeholder="Masukan Konfirmasi Password">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                                    <i class="fas fa-eye" id="eyeIconConfirmation"></i>
                                                </button>
                                            </div>
                                            <div class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Wajib diisi jika mengubah password</div>
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
    /* Password toggle button styling */
    .password-toggle {
        border: none;
        background: none;
        cursor: pointer;
        padding: 0.375rem 0.75rem;
        transition: color 0.15s ease-in-out;
    }
    .password-toggle:hover {
        color: #0056b3;
    }
    .password-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('PasswordKry');
    const eyeIcon = document.getElementById('eyeIcon');

    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const eyeIconConfirmation = document.getElementById('eyeIconConfirmation');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle eye icon
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    // Toggle password confirmation visibility
    togglePasswordConfirmation.addEventListener('click', function() {
        const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationInput.setAttribute('type', type);

        // Toggle eye icon
        if (type === 'text') {
            eyeIconConfirmation.classList.remove('fa-eye');
            eyeIconConfirmation.classList.add('fa-eye-slash');
        } else {
            eyeIconConfirmation.classList.remove('fa-eye-slash');
            eyeIconConfirmation.classList.add('fa-eye');
        }
    });

    // Form validation with visual feedback
    const form = document.getElementById('userEditForm');
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

    // Password confirmation validation (only if password is filled)
    const password = document.getElementById('PasswordKry');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value && password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Password tidak sama');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }

    password.addEventListener('input', function() {
        validatePassword();
        // Make confirmation required if password is filled
        if (this.value) {
            passwordConfirmation.setAttribute('required', 'required');
        } else {
            passwordConfirmation.removeAttribute('required');
        }
    });

    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endpush