<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIKAP - Sistem Informasi Kesekretariatan Administrasi Perusahaan</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Include Vite assets (will load our app.js and app.css) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')
</head>

<body>
    <div class="layout-container" id="layoutContainer">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="ms-auto d-flex align-items-center">
                    <!-- User Profile Dropdown -->
                    @auth
                        <div class="dropdown">
                            <div class="d-flex align-items-center dropdown-toggle" id="userDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                <div class="me-2 d-none d-md-block text-end">
                                    <h6 class="mb-0 fw-semibold">{{ Auth::user()->NamaKry }}</h6>
                                    <span class="small text-muted">{{ Auth::user()->NikKry }}</span>
                                </div>
                                <div class="user-avatar">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->NamaKry) }}&background=0D8ABC&color=fff"
                                        alt="User Avatar" class="rounded-circle"
                                        style="width: 40px; height: 40px; border: 2px solid #f0f0f0;">
                                </div>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <span id="itemNameToDelete"></span>?</p>
                    <p class="text-danger"><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- DataTables Language -->
    <script>
        // Pre-load the Indonesian language file to avoid the 404 error
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "loadingRecords": "Sedang memuat...",
                "processing": "Sedang memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "aria": {
                    "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
                    "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
                }
            }
        });

        // Check if our core app functionality is already initialized
        if (typeof window.appInitialized === 'undefined') {
            console.log("Backup JS initializing - app.js might not have loaded");

            // Set flag to prevent double initialization
            window.appInitialized = true;

            // Document ready function
            document.addEventListener('DOMContentLoaded', function() {
                console.log("DOM loaded - initializing backup functionality");

                // Initialize all functionality
                initSidebar();
                initDataTables();
                initBootstrapComponents();
                initDeleteConfirmation();

                // Auto-hide alerts after 5 seconds
                setTimeout(function() {
                    if (document.querySelector(".alert")) {
                        document.querySelector(".alert").style.display = "none";
                    }
                }, 5000);
            });

            // Initialize sidebar functionality
            function initSidebar() {
                console.log("Initializing sidebar");

                // Get DOM elements
                const layoutContainer = document.getElementById('layoutContainer');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                const sidebarClose = document.getElementById('sidebarClose');
                const mediaQuery = window.matchMedia('(max-width: 991.98px)');
                const menuItems = document.querySelectorAll('.sidebar-menu-item');

                // Toggle sidebar on hamburger click
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function(e) {
                        console.log("Sidebar toggle clicked");
                        e.preventDefault();

                        if (mediaQuery.matches) {
                            // Mobile view
                            layoutContainer.classList.toggle('sidebar-active');
                        } else {
                            // Desktop view
                            layoutContainer.classList.toggle('sidebar-collapsed');

                            // Save sidebar state
                            const isCollapsed = layoutContainer.classList.contains('sidebar-collapsed');
                            localStorage.setItem('sidebar-collapsed', isCollapsed);
                        }
                    });
                }

                // Close sidebar on mobile
                if (sidebarClose) {
                    sidebarClose.addEventListener('click', function(e) {
                        console.log("Sidebar close clicked");
                        e.preventDefault();
                        layoutContainer.classList.remove('sidebar-active');
                    });
                }

                // Close sidebar when overlay is clicked
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', function(e) {
                        console.log("Sidebar overlay clicked");
                        e.preventDefault();
                        layoutContainer.classList.remove('sidebar-active');
                    });
                }

                // Handle submenu toggles
                if (menuItems && menuItems.length > 0) {
                    console.log(`Found ${menuItems.length} menu items`);

                    menuItems.forEach(function(item) {
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log("Menu item clicked:", this);

                            const menuId = this.getAttribute('data-menu');
                            const submenu = document.getElementById(menuId);

                            // Toggle the open class on the menu item
                            this.classList.toggle('open');

                            // Toggle the show class on the submenu
                            if (submenu) {
                                console.log("Toggling submenu:", submenu);
                                submenu.classList.toggle('show');
                            }
                        });
                    });
                }

                // Check for mobile/desktop view
                function handleResize(e) {
                    if (e.matches) {
                        // Mobile view
                        layoutContainer.classList.remove('sidebar-collapsed');
                    } else {
                        // Desktop view
                        layoutContainer.classList.remove('sidebar-active');

                        // Restore sidebar state
                        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                        if (isCollapsed) {
                            layoutContainer.classList.add('sidebar-collapsed');
                        } else {
                            layoutContainer.classList.remove('sidebar-collapsed');
                        }
                    }
                }

                // Initial check and add listener
                if (mediaQuery) {
                    handleResize(mediaQuery);
                    mediaQuery.addEventListener('change', handleResize);
                }

                // Initialize any active submenus (for current page)
                const activeMenuItems = document.querySelectorAll('.sidebar-menu-item.active');
                activeMenuItems.forEach(function(item) {
                    console.log("Setting active menu item:", item);
                    item.classList.add('open');

                    const menuId = item.getAttribute('data-menu');
                    const submenu = document.getElementById(menuId);

                    if (submenu) {
                        submenu.classList.add('show');
                    }
                });
            }

            // Initialize DataTables
            function initDataTables() {
                console.log("Initializing DataTables");

                if ($.fn.DataTable) {
                    $('.data-table').each(function() {
                        // Check if table is already initialized
                        if ($.fn.DataTable.isDataTable(this)) {
                            console.log("Destroying existing DataTable");
                            $(this).DataTable().destroy();
                        }

                        // Initialize DataTable
                        console.log("Creating new DataTable");
                        $(this).DataTable({
                            responsive: true,
                            columnDefs: [{
                                    responsivePriority: 1,
                                    targets: [0, 1, -1] // Priority on first, second and last column
                                },
                                {
                                    orderable: false,
                                    targets: [-1] // Last column (actions) not sortable
                                }
                            ]
                        });
                    });
                }
            }

            // Initialize Bootstrap components
            function initBootstrapComponents() {
                console.log("Initializing Bootstrap components");

                // Initialize tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize popovers
                const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function(popoverTriggerEl) {
                    new bootstrap.Popover(popoverTriggerEl);
                });

                // Initialize dropdown manually
                const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                dropdownElementList.forEach(function(dropdownToggleEl) {
                    console.log("Initializing dropdown:", dropdownToggleEl);
                    new bootstrap.Dropdown(dropdownToggleEl);
                });

                // Special handling for user dropdown in navbar
                const userDropdown = document.getElementById('userDropdown');
                if (userDropdown) {
                    console.log("Adding click handler to user dropdown");
                    userDropdown.addEventListener('click', function(e) {
                        const dropdownMenu = this.nextElementSibling;
                        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                            dropdownMenu.classList.toggle('show');
                        }
                    });
                }
            }

            // Initialize delete confirmation
            function initDeleteConfirmation() {
                console.log("Initializing delete confirmation");

                $('.delete-confirm').on('click', function(e) {
                    e.preventDefault();

                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const url = $(this).data('url') || $(this).data('route');

                    console.log("Delete confirmation clicked for:", name);

                    // Set item name in modal
                    $('#itemNameToDelete').text(name);

                    // Set form action URL
                    $('#deleteForm').attr('action', url);

                    // Show modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                    deleteModal.show();
                });
            }
        }
    </script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('sweetalert::alert')


    <!-- SweetAlert Flash Messages -->
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif


    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
