{{-- resources/views/layouts/sidebar.blade.php --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <i class="fas fa-file-contract"></i>
            <span class="brand-text">SIKAP</span>
        </a>
        <button type="button" class="sidebar-close d-md-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-menu">
            <div class="sidebar-heading">Menu Utama</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('home') || request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>

                @php
                    // Check if user has access to any of the master data menus
                    $hasUserAccess = Auth::user()->isAdmin() || Auth::user()->hasAccess('users', 'detail');
                    $hasPerusahaanAccess = Auth::user()->isAdmin() || Auth::user()->hasAccess('perusahaan', 'detail');
                    $hasKategoriDokAccess =
                        Auth::user()->isAdmin() || Auth::user()->hasAccess('kategori-dok', 'detail');
                    $hasJenisDokAccess = Auth::user()->isAdmin() || Auth::user()->hasAccess('jenis-dok', 'detail');

                    // Check if user has access to any master data menu
                    $hasMasterDataAccess =
                        $hasUserAccess || $hasPerusahaanAccess || $hasKategoriDokAccess || $hasJenisDokAccess;

                    // Check if user has access to dokumen menu
                    $hasDokumenLegalAccess = Auth::user()->isAdmin() || Auth::user()->hasAccess('dokLegal', 'detail');

                    // Check if user has access to rekap menu
                    $hasRekapCabangAccess = Auth::user()->isAdmin() || Auth::user()->hasAccess('rekapCabang', 'detail');
                @endphp

                @if ($hasMasterDataAccess)
                    <li class="nav-item">
                        <a class="nav-link sidebar-menu-item {{ request()->is('users*') || request()->is('perusahaan*') || request()->is('kategori-dok*') || request()->is('jenis-dok*') ? 'active' : '' }}"
                            href="#" data-menu="dataMaster">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="menu-icon-text">
                                    <i class="fas fa-gear"></i>
                                    <span class="nav-text">Data Master</span>
                                </div>
                                <i class="fas fa-chevron-down submenu-indicator"></i>
                            </div>
                        </a>
                        <ul class="sidebar-submenu {{ request()->is('users*') || request()->is('perusahaan*') || request()->is('kategori-dok*') || request()->is('jenis-dok*') ? 'show' : '' }}"
                            id="dataMaster">
                            @if ($hasUserAccess)
                                <li class="nav-item">
                                    <a class="submenu-link {{ request()->is('users*') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}">
                                        <i class="fas fa-user-tie"></i>
                                        <span>Pengguna</span>
                                    </a>
                                </li>
                            @endif

                            @if ($hasPerusahaanAccess)
                                <li class="nav-item">
                                    <a class="submenu-link {{ request()->is('perusahaan*') ? 'active' : '' }}"
                                        href="{{ route('perusahaan.index') }}">
                                        <i class="fas fa-building"></i>
                                        <span>Data Perusahaan</span>
                                    </a>
                                </li>
                            @endif

                            @if ($hasKategoriDokAccess)
                                <li class="nav-item">
                                    <a class="submenu-link {{ request()->is('kategori-dok*') ? 'active' : '' }}"
                                        href="{{ route('kategori-dok.index') }}">
                                        <i class="fas fa-list"></i>
                                        <span>Kategori Dokumen</span>
                                    </a>
                                </li>
                            @endif

                            @if ($hasJenisDokAccess)
                                <li class="nav-item">
                                    <a class="submenu-link {{ request()->is('jenis-dok*') ? 'active' : '' }}"
                                        href="{{ route('jenis-dok.index') }}">
                                        <i class="fas fa-layer-group"></i>
                                        <span>Jenis Dokumen</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if ($hasDokumenLegalAccess)
                    <li class="nav-item">
                        <a class="nav-link sidebar-menu-item {{ request()->is('dokLegal*') ? 'active' : '' }}"
                            href="#" data-menu="dokumen">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="menu-icon-text">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="nav-text">Dokumen</span>
                                </div>
                                <i class="fas fa-chevron-down submenu-indicator"></i>
                            </div>
                        </a>
                        <ul class="sidebar-submenu {{ request()->is('dokLegal*') ? 'show' : '' }}"
                            id="dokumen">
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('dokLegal*') ? 'active' : '' }}"
                                    href="{{ route('dokLegal.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Legal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($hasRekapCabangAccess)
                    <li class="nav-item">
                        <a class="nav-link sidebar-menu-item {{ request()->is('cabang.rekap') ? 'active' : '' }}"
                            href="#" data-menu="rekap">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="menu-icon-text">
                                    <i class="fas fa-chart-bar"></i>
                                    <span class="nav-text">Rekap</span>
                                </div>
                                <i class="fas fa-chevron-down submenu-indicator"></i>
                            </div>
                        </a>
                        <ul class="sidebar-submenu {{ request()->is('cabang.rekap') ? 'show' : '' }}"
                            id="rekap">
                            <li class="nav-item">
                                <a class="submenu-link {{ request()->is('cabang.rekap') ? 'active' : '' }}"
                                    href="{{ route('cabang.rekap') }}">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Cabang</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('settings.index') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}">
                        <i class="fas fa-gear"></i>
                        <span class="nav-text">Pengaturan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>