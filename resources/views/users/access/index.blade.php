@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-key me-2"></i>Kelola Hak Akses: {{ $user->NamaKry }}</span>
                    <a href="{{ route('users.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('users.access.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" {{ $user->isAdmin() ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_admin">
                                    <span class="fw-bold text-danger">Administrator (Akses Penuh)</span>
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> Administrator memiliki akses penuh ke semua fitur aplikasi.
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Detail</th>
                                        <th class="text-center">Tambah</th>
                                        <th class="text-center">Ubah</th>
                                        <th class="text-center">Hapus</th>
                                        <th class="text-center">Download</th>
                                        <th class="text-center">Monitoring</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($availableMenus as $menu)
                                    @php
                                        $userAccessItem = $userAccess[$menu['name']] ?? null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $menu['display_name'] }}</strong>
                                            <div class="text-muted small">{{ $menu['controller'] }}</div>
                                            <input type="hidden" name="access[{{ $loop->index }}][MenuAcs]" value="{{ $menu['name'] }}">
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="DetailAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][DetailAcs]"
                                                    {{ isset($userAccessItem['DetailAcs']) && $userAccessItem['DetailAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="TambahAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][TambahAcs]"
                                                    {{ isset($userAccessItem['TambahAcs']) && $userAccessItem['TambahAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="UbahAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][UbahAcs]"
                                                    {{ isset($userAccessItem['UbahAcs']) && $userAccessItem['UbahAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="HapusAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][HapusAcs]"
                                                    {{ isset($userAccessItem['HapusAcs']) && $userAccessItem['HapusAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="DownloadAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][DownloadAcs]"
                                                    {{ isset($userAccessItem['DownloadAcs']) && $userAccessItem['DownloadAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox"
                                                    id="MonitoringAcs_{{ $menu['name'] }}"
                                                    name="access[{{ $loop->index }}][MonitoringAcs]"
                                                    {{ isset($userAccessItem['MonitoringAcs']) && $userAccessItem['MonitoringAcs'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle admin checkbox
        $('#is_admin').on('change', function() {
            var isChecked = $(this).prop('checked');
            if (isChecked) {
                // Disable all other checkboxes if admin is checked
                $('input[type="checkbox"]').not('#is_admin').prop('disabled', true);
            } else {
                // Enable all checkboxes if admin is unchecked
                $('input[type="checkbox"]').prop('disabled', false);
            }
        });

        // Trigger change event on page load to set initial state
        $('#is_admin').trigger('change');
    });
</script>
@endpush