// -----------------------------------
// Import page-specific CSS
// -----------------------------------
import '../styles/orderindex.css';

// -----------------------------------
// Import dependencies
// -----------------------------------
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';

// -----------------------------------
// Safe DataTable Initialization
// -----------------------------------
function initOrdersTable() {
    const $table = $('#ordersTable');
    if (!$table.length) return;

    // Prevent double init within the same lifecycle using a flag
    if ($table.data('dt-initialized')) {
        return;
    }

    // If already initialized (e.g., after Turbo navigation), destroy first
    if ($.fn.DataTable.isDataTable($table)) {
        try {
            $table.DataTable().clear().destroy();
        } catch (e) {
            try { $table.DataTable().destroy(true); } catch (_) {}
        }
    }

    $table.DataTable({
        destroy: true, // allow re-init if needed without throwing
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        info: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: 5 }
        ],
        language: {
            search: "Search orders:",
            info: "Showing _START_ to _END_ of _TOTAL_ orders",
            paginate: { previous: "Prev", next: "Next" },
            emptyTable: "No orders available"
        }
    });

    // Mark as initialized for this lifecycle
    $table.data('dt-initialized', true);
}

// Handle both full reload and Turbo-driven navigation
document.addEventListener('DOMContentLoaded', initOrdersTable);
document.addEventListener('turbo:load', initOrdersTable);

// Ensure clean teardown before Turbo swaps the DOM
document.addEventListener('turbo:before-render', () => {
    const $table = $('#ordersTable');
    if ($table.length && $.fn.DataTable.isDataTable($table)) {
        try {
            $table.DataTable().clear().destroy();
        } catch (e) {
            try { $table.DataTable().destroy(true); } catch (_) {}
        }
        // remove lifecycle flag so it can re-init on next load
        $table.removeData('dt-initialized');
    }
});
