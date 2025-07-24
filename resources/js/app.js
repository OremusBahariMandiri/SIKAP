import './bootstrap';

// This file will be processed by Vite
// We'll ensure jQuery and Bootstrap are properly loaded

// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import DataTables (if needed)
import 'datatables.net';
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';
// resources/js/app.js

// Import the main CSS file
import '../css/app.css';

// Import the sidebar CSS explicitly
import '../css/sidebar.css';

// Any other imports and code you have in your app.js

// Initialize components when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.data-table').each(function() {
            // Check if table is already initialized
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }

            // Initialize DataTable
            $(this).DataTable({
                responsive: true,
                // The language is now set globally in the layout file
                columnDefs: [
                    {
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

    // Delete confirmation
    $('.delete-confirm').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const url = $(this).data('url') || $(this).data('route');

        // Set item name in modal
        $('#itemNameToDelete').text(name);

        // Set form action URL
        $('#deleteForm').attr('action', url);

        // Show modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 5000);

    // Sidebar functionality
    const layoutContainer = document.getElementById('layoutContainer');
    const mediaQuery = window.matchMedia('(max-width: 991.98px)');
    const menuItems = document.querySelectorAll('.sidebar-menu-item');





    // Handle resize events
    function handleResize(e) {
        if (e.matches) {
            // Mobile view
            layoutContainer.classList.remove('sidebar-collapsed');
        } else {
            // Desktop view
            layoutContainer.classList.remove('sidebar-active');

            // Restore sidebar state from localStorage
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed) {
                layoutContainer.classList.add('sidebar-collapsed');
            } else {
                layoutContainer.classList.remove('sidebar-collapsed');
            }
        }
    }

    // Initial check for sidebar state
    if (mediaQuery) {
        handleResize(mediaQuery);
        mediaQuery.addEventListener('change', handleResize);
    }

    // Handle submenu toggle
    menuItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            const menuId = this.getAttribute('data-menu');
            const submenu = document.getElementById(menuId);

            // Check if we're in collapsed state on desktop
            const isCollapsedDesktop = !mediaQuery.matches && layoutContainer.classList.contains('sidebar-collapsed');

            // Only toggle if not in collapsed desktop mode (handled by hover)
            if (!isCollapsedDesktop) {
                // Toggle open class on menu item
                this.classList.toggle('open');

                // Toggle show class on submenu
                if (submenu) {
                    submenu.classList.toggle('show');
                }
            }
        });
    });

    // Initialize any open menus (for active state)
    const activeSubmenus = document.querySelectorAll('.sidebar-submenu.show');
    activeSubmenus.forEach(function(submenu) {
        const menuItem = document.querySelector(`[data-menu="${submenu.id}"]`);
        if (menuItem) {
            menuItem.classList.add('open');
        }
    });
});