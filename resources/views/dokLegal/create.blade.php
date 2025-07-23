@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Tambah Dokumen Legal</span>
                        <a href="{{ route('dokLegal.index') }}" class="btn btn-light btn-sm">
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

                        <form action="{{ route('dokLegal.store') }}" method="POST" enctype="multipart/form-data"
                            id="dokLegalForm">
                            @csrf
                            <input type="text" class="form-control" id="IdKode" name="IdKode"
                                value="{{ old('IdKode', $idKode ?? '') }}" hidden readonly>

                            <!-- Grouped form sections with cards -->
                            <div class="row g-4">
                                <!-- Basic Document Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-info">
                                        <div class="card-header bg-info bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Informasi Dokumen</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="NoRegDok" class="form-label fw-bold">Nomor Register Dokumen
                                                    <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('NoRegDok') is-invalid @enderror"
                                                    id="NoRegDok" name="NoRegDok" value="{{ old('NoRegDok') }}"
                                                    placeholder="Masukan No Register Dokumen" required>
                                                @error('NoRegDok')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="perusahaan_id" class="form-label fw-bold">Perusahaan <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('perusahaan_id') is-invalid @enderror"
                                                    id="perusahaan_id" name="perusahaan_id" required>
                                                    <option value="">-- Pilih Perusahaan --</option>
                                                    @foreach ($perusahaans as $id => $nama)
                                                        <option value="{{ $id }}"
                                                            {{ old('perusahaan_id') == $id ? 'selected' : '' }}>
                                                            {{ $nama }}</option>
                                                    @endforeach
                                                </select>
                                                @error('perusahaan_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="DokPerusahaan" name="DokPerusahaan"
                                                    value="{{ old('DokPerusahaan') }}">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="kategori_id" class="form-label fw-bold">Kategori Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('kategori_id') is-invalid @enderror"
                                                    id="kategori_id" name="kategori_id" required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    @foreach ($kategoris as $id => $kategori)
                                                        <option value="{{ $id }}"
                                                            {{ old('kategori_id') == $id ? 'selected' : '' }}>
                                                            {{ $kategori }}</option>
                                                    @endforeach
                                                </select>
                                                @error('kategori_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="KategoriDok" name="KategoriDok"
                                                    value="{{ old('KategoriDok') }}">
                                            </div>

                                            <!-- Form jenis_id dropdown with data attributes for kategori filtering -->
                                            <div class="form-group mb-3">
                                                <label for="jenis_id" class="form-label fw-bold">Jenis Dokumen <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('jenis_id') is-invalid @enderror"
                                                    id="jenis_id" name="jenis_id" required
                                                    data-old-value="{{ old('jenis_id') }}">
                                                    <option value="">-- Pilih Jenis --</option>
                                                    @foreach ($formattedJenisDoks as $id => $jenis)
                                                        <option value="{{ $id }}"
                                                            data-kategori-id="{{ $jenis['kategori_id'] }}"
                                                            {{ old('jenis_id') == $id ? 'selected' : '' }}>
                                                            {{ $jenis['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('jenis_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="JenisDok" name="JenisDok"
                                                    value="{{ old('JenisDok') }}">
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="PeruntukanDok" class="form-label fw-bold">Peruntukan Dokumen
                                                    <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('PeruntukanDok') is-invalid @enderror"
                                                    id="PeruntukanDok" name="PeruntukanDok"
                                                    value="{{ old('PeruntukanDok') }}"
                                                    placeholder="Masukan Peruntukan Dokumen" required>
                                                @error('PeruntukanDok')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="DokAtasNama" class="form-label fw-bold">Atas Nama <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('DokAtasNama') is-invalid @enderror"
                                                    id="DokAtasNama" name="DokAtasNama" value="{{ old('DokAtasNama') }}"
                                                    placeholder="Masukan Nama" required>
                                                @error('DokAtasNama')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validity and Date Information -->
                                <div class="col-md-6">
                                    <div class="card h-100 border-info">
                                        <div class="card-header bg-info bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Masa Berlaku &
                                                Tanggal</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label fw-bold">Jenis Masa Berlaku <span
                                                        class="text-danger">*</span></label>
                                                <div class="bg-light p-2 rounded">
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input jenis-masa-berlaku @error('JnsMasaBerlaku') is-invalid @enderror"
                                                            type="radio" name="JnsMasaBerlaku" id="tetap"
                                                            value="Tetap"
                                                            {{ old('JnsMasaBerlaku', 'Tetap') == 'Tetap' ? 'checked' : '' }}
                                                            required>
                                                        <label class="form-check-label" for="tetap">
                                                            <i class="fas fa-infinity text-primary me-1"></i>Tetap
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input
                                                            class="form-check-input jenis-masa-berlaku @error('JnsMasaBerlaku') is-invalid @enderror"
                                                            type="radio" name="JnsMasaBerlaku" id="perpanjangan"
                                                            value="Perpanjangan"
                                                            {{ old('JnsMasaBerlaku') == 'Perpanjangan' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perpanjangan">
                                                            <i class="fas fa-sync text-success me-1"></i>Perpanjangan
                                                        </label>
                                                    </div>
                                                    @error('JnsMasaBerlaku')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="TglTerbitDok" class="form-label fw-bold">Tanggal
                                                            Terbit <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglTerbitDok') is-invalid @enderror"
                                                                id="TglTerbitDok" name="TglTerbitDok"
                                                                value="{{ old('TglTerbitDok') }}" required>
                                                            @error('TglTerbitDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3 tgl-berakhir-group">
                                                        <label for="TglBerakhirDok" class="form-label fw-bold">Tanggal
                                                            Berakhir</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-calendar-times"></i></span>
                                                            <input type="date"
                                                                class="form-control tanggal-input @error('TglBerakhirDok') is-invalid @enderror"
                                                                id="TglBerakhirDok" name="TglBerakhirDok"
                                                                value="{{ old('TglBerakhirDok') }}">
                                                            @error('TglBerakhirDok')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="MasaBerlaku" class="form-label fw-bold">Masa Berlaku</label>
                                                <input type="text" class="form-control bg-light" id="MasaBerlaku"
                                                    name="MasaBerlaku" value="{{ old('MasaBerlaku', 'Tetap') }}"
                                                    readonly>
                                                <div class="form-text text-muted"><i
                                                        class="fas fa-info-circle me-1"></i>Masa berlaku akan dihitung
                                                    otomatis</div>
                                            </div>

                                            <div class="form-group mb-3 tgl-pengingat-group">
                                                <label for="TglPengingat" class="form-label fw-bold">Tanggal
                                                    Peringatan</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-bell"></i></span>
                                                    <input type="date"
                                                        class="form-control tanggal-input @error('TglPengingat') is-invalid @enderror"
                                                        id="TglPengingat" name="TglPengingat"
                                                        value="{{ old('TglPengingat') }}">
                                                    @error('TglPengingat')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="MasaPengingat" class="form-label fw-bold">Peringatan</label>
                                                <input type="text" class="form-control bg-light" id="MasaPengingat"
                                                    name="MasaPengingat" value="{{ old('MasaPengingat', '-') }}"
                                                    readonly>
                                                <div class="form-text text-muted"><i
                                                        class="fas fa-info-circle me-1"></i>Masa pengingat akan dihitung
                                                    otomatis</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-md-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info bg-opacity-25 text-dark">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="file_dokumen" class="form-label fw-bold">File
                                                            Dokumen</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i
                                                                    class="fas fa-file-upload"></i></span>
                                                            <input type="file"
                                                                class="form-control @error('file_dokumen') is-invalid @enderror"
                                                                id="file_dokumen" name="file_dokumen">
                                                            @error('file_dokumen')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text text-muted"><i
                                                                class="fas fa-info-circle me-1"></i>Format: PDF, JPG, PNG,
                                                            DOC, DOCX, XLS, atau XLSX</div>
                                                    </div>

                                                    <div class="form-group mb-3">
                                                        <label class="form-label fw-bold">Status Berlaku Dokumen <span
                                                                class="text-danger">*</span></label>
                                                        <div class="bg-light p-2 rounded">
                                                            <div class="form-check form-check-inline">
                                                                <input
                                                                    class="form-check-input @error('StsBerlakuDok') is-invalid @enderror"
                                                                    type="radio" name="StsBerlakuDok" id="berlaku"
                                                                    value="Berlaku"
                                                                    {{ old('StsBerlakuDok', 'Berlaku') == 'Berlaku' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label" for="berlaku">
                                                                    <i
                                                                        class="fas fa-check-circle text-success me-1"></i>Berlaku
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input
                                                                    class="form-check-input @error('StsBerlakuDok') is-invalid @enderror"
                                                                    type="radio" name="StsBerlakuDok"
                                                                    id="tidak_berlaku" value="Tidak Berlaku"
                                                                    {{ old('StsBerlakuDok') == 'Tidak Berlaku' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="tidak_berlaku">
                                                                    <i
                                                                        class="fas fa-times-circle text-danger me-1"></i>Tidak
                                                                    Berlaku
                                                                </label>
                                                            </div>
                                                            @error('StsBerlakuDok')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3 h-100">
                                                        <label for="KetDok"
                                                            class="form-label fw-bold">Keterangan</label>
                                                        <textarea class="form-control @error('KetDok') is-invalid @enderror" id="KetDok" name="KetDok" rows="5"
                                                            placeholder="Masukkan keterangan tambahan jika ada">{{ old('KetDok') }}</textarea>
                                                        @error('KetDok')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .text-danger {
            font-weight: bold;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store all available jenis dokumen options
            const jenisDokSelect = document.getElementById('jenis_id');
            const allJenisDokOptions = Array.from(jenisDokSelect.options);

            // Function to filter jenis dokumen based on selected kategori
            function filterJenisDokumen() {
                const kategoriId = document.getElementById('kategori_id').value;

                // Reset jenis_id dropdown
                jenisDokSelect.innerHTML = '';

                // Add default empty option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.text = '-- Pilih Jenis --';
                jenisDokSelect.add(defaultOption);

                // If no kategori selected, just show the default option
                if (!kategoriId) {
                    return;
                }

                // Find and add all jenis options that match the selected kategori
                allJenisDokOptions.forEach(option => {
                    // Skip the default empty option
                    if (option.value === '') return;

                    // Get the data attribute from the option that identifies its kategori
                    const optionKategoriId = option.getAttribute('data-kategori-id');

                    // Add the option if it belongs to the selected kategori
                    if (optionKategoriId === kategoriId) {
                        const newOption = option.cloneNode(true);
                        jenisDokSelect.add(newOption);
                    }
                });

                // Clear JenisDok hidden field when kategori changes
                document.getElementById('JenisDok').value = '';
            }

            // Set hidden fields value from select dropdown
            document.getElementById('perusahaan_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('DokPerusahaan').value = selectedOption.text;
            });

            document.getElementById('kategori_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('KategoriDok').value = selectedOption.text;

                // Filter jenis dokumen when kategori changes
                filterJenisDokumen();
            });

            document.getElementById('jenis_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('JenisDok').value = selectedOption.text;
            });

            // Trigger change event to set initial values
            if (document.getElementById('perusahaan_id').value) {
                const perusahaanSelect = document.getElementById('perusahaan_id');
                document.getElementById('DokPerusahaan').value = perusahaanSelect.options[perusahaanSelect
                    .selectedIndex].text;
            }

            if (document.getElementById('kategori_id').value) {
                const kategoriSelect = document.getElementById('kategori_id');
                document.getElementById('KategoriDok').value = kategoriSelect.options[kategoriSelect.selectedIndex]
                    .text;

                // Also trigger the filter for jenis dokumen
                filterJenisDokumen();

                // If there's a previously selected jenis_id value, try to restore it
                const savedJenisId = document.getElementById('jenis_id').getAttribute('data-old-value');
                if (savedJenisId) {
                    document.getElementById('jenis_id').value = savedJenisId;
                    // Trigger change event to update the hidden field
                    const event = new Event('change');
                    document.getElementById('jenis_id').dispatchEvent(event);
                }
            }

            if (document.getElementById('jenis_id').value) {
                const jenisSelect = document.getElementById('jenis_id');
                document.getElementById('JenisDok').value = jenisSelect.options[jenisSelect.selectedIndex].text;
            }

            // Fungsi untuk menghitung dan menampilkan masa berlaku
            function hitungMasaBerlaku() {
                const jenisMasaBerlaku = document.querySelector('input[name="JnsMasaBerlaku"]:checked').value;
                const tglTerbit = document.getElementById('TglTerbitDok').value;
                const tglBerakhir = document.getElementById('TglBerakhirDok').value;
                const masaBerlakuField = document.getElementById('MasaBerlaku');

                if (jenisMasaBerlaku === 'Tetap') {
                    masaBerlakuField.value = 'Tetap';
                    return;
                }

                if (tglTerbit && tglBerakhir) {
                    const startDate = new Date(tglTerbit);
                    const endDate = new Date(tglBerakhir);

                    if (endDate < startDate) {
                        masaBerlakuField.value = 'Tanggal berakhir harus setelah tanggal terbit';
                        return;
                    }

                    // Hitung selisih tahun, bulan, dan hari
                    let years = endDate.getFullYear() - startDate.getFullYear();
                    let months = endDate.getMonth() - startDate.getMonth();
                    let days = endDate.getDate() - startDate.getDate();

                    if (days < 0) {
                        months--;
                        // Tambahkan hari dari bulan sebelumnya
                        const lastDayOfMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0).getDate();
                        days += lastDayOfMonth;
                    }

                    if (months < 0) {
                        years--;
                        months += 12;
                    }

                    let result = '';
                    if (years > 0) result += years + ' thn ';
                    if (months > 0) result += months + ' bln ';
                    if (days > 0) result += days + ' hri';

                    masaBerlakuField.value = result || '0 hri';
                } else {
                    masaBerlakuField.value = '';
                }
            }

            // Fungsi untuk menghitung dan menampilkan masa pengingat
            // Dihitung dari tanggal pengingat ke tanggal berakhir
            function hitungMasaPengingat() {
                const tglPengingat = document.getElementById('TglPengingat').value;
                const tglBerakhir = document.getElementById('TglBerakhirDok').value;
                const masaPengingatField = document.getElementById('MasaPengingat');

                if (tglPengingat && tglBerakhir) {
                    const reminderDate = new Date(tglPengingat);
                    const expiryDate = new Date(tglBerakhir);

                    if (reminderDate > expiryDate) {
                        masaPengingatField.value = 'Tanggal pengingat harus sebelum tanggal berakhir';
                        return;
                    }

                    // Hitung selisih tahun, bulan, dan hari
                    let years = expiryDate.getFullYear() - reminderDate.getFullYear();
                    let months = expiryDate.getMonth() - reminderDate.getMonth();
                    let days = expiryDate.getDate() - reminderDate.getDate();

                    if (days < 0) {
                        months--;
                        // Tambahkan hari dari bulan sebelumnya
                        const lastDayOfMonth = new Date(expiryDate.getFullYear(), expiryDate.getMonth(), 0)
                            .getDate();
                        days += lastDayOfMonth;
                    }

                    if (months < 0) {
                        years--;
                        months += 12;
                    }

                    let result = '';
                    if (years > 0) result += years + ' thn ';
                    if (months > 0) result += months + ' bln ';
                    if (days > 0) result += days + ' hri';

                    masaPengingatField.value = result || '0 hri';
                } else {
                    masaPengingatField.value = '-';
                }
            }

            // Fungsi untuk toggle visibilitas field berdasarkan jenis masa berlaku
            function toggleFieldsVisibility() {
                const jenisMasaBerlaku = document.querySelector('input[name="JnsMasaBerlaku"]:checked').value;
                const tglBerakhirGroup = document.querySelector('.tgl-berakhir-group');
                const tglPengingatGroup = document.querySelector('.tgl-pengingat-group');
                const tglBerakhirInput = document.getElementById('TglBerakhirDok');
                const tglBerakhirLabel = tglBerakhirGroup.querySelector('.form-label');
                const tglPengingatInput = document.getElementById('TglPengingat');

                if (jenisMasaBerlaku === 'Tetap') {
                    tglBerakhirGroup.style.display = 'none';
                    tglPengingatGroup.style.display = 'none';
                    document.getElementById('TglBerakhirDok').value = '';
                    document.getElementById('TglPengingat').value = '';
                    document.getElementById('MasaBerlaku').value = 'Tetap';
                    document.getElementById('MasaPengingat').value = '-';

                    // Hapus atribut required
                    tglBerakhirInput.removeAttribute('required');
                    tglPengingatInput.removeAttribute('required');
                } else {
                    tglBerakhirGroup.style.display = 'block';
                    tglPengingatGroup.style.display = 'block';

                    // Tambahkan tanda wajib diisi pada label
                    if (!tglBerakhirLabel.innerHTML.includes('*')) {
                        tglBerakhirLabel.innerHTML = tglBerakhirLabel.innerHTML +
                            ' <span class="text-danger">*</span>';
                    }

                    // Tambahkan atribut required
                    tglBerakhirInput.setAttribute('required', 'required');

                    hitungMasaBerlaku();
                    hitungMasaPengingat();
                }
            }

            // Tambahkan event listener untuk jenis masa berlaku
            document.querySelectorAll('.jenis-masa-berlaku').forEach(function(radio) {
                radio.addEventListener('change', toggleFieldsVisibility);
            });

            // Tambahkan event listener untuk tanggal
            document.querySelectorAll('.tanggal-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (this.id === 'TglTerbitDok' || this.id === 'TglBerakhirDok') {
                        hitungMasaBerlaku();
                    }
                    if (this.id === 'TglPengingat' || this.id === 'TglBerakhirDok') {
                        hitungMasaPengingat();
                    }
                });
            });

            // Tambahkan validasi untuk file dokumen
            document.getElementById('file_dokumen').addEventListener('change', function() {
                const fileInput = this;
                const fileFeedback = document.getElementById('file-feedback');

                if (fileFeedback) {
                    fileFeedback.remove();
                }

                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const allowedExtensions = /(\.pdf|\.jpg|\.jpeg|\.png|\.doc|\.docx|\.xls|\.xlsx)$/i;
                }
            });

            // Initialize form state
            toggleFieldsVisibility();

            // Hitung masa pengingat awal jika field sudah memiliki nilai
            if (document.getElementById('TglPengingat').value && document.getElementById('TglBerakhirDok').value) {
                hitungMasaPengingat();
            }
        });
    </script>
@endpush
