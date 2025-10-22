// assets/scripts/usersindex.js

// -------------------------------
// Import page-specific CSS
// -------------------------------
import '../styles/usersindex.css';

// -------------------------------
// Import jQuery and DataTables
// -------------------------------
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

// -------------------------------
// Initialize DataTable (Turbo-aware, idempotent)
// -------------------------------
function initCustomersTable() {
    const $table = $('#customersTable');
    if (!$table.length) return;

    if ($table.data('dt-initialized')) {
        return;
    }

    if ($.fn.DataTable.isDataTable($table)) {
        try {
            $table.DataTable().clear().destroy();
        } catch (e) {
            try { $table.DataTable().destroy(true); } catch (_) {}
        }
    }

    $table.DataTable({
        destroy: true,
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: 5 }
        ],
        language: {
            search: "Search customers:",
            info: "Showing _START_ to _END_ of _TOTAL_ customers",
            paginate: { previous: "Prev", next: "Next" },
            emptyTable: "No customers found"
        }
    });

    $table.data('dt-initialized', true);
}

document.addEventListener('DOMContentLoaded', initCustomersTable);
document.addEventListener('turbo:load', initCustomersTable);

document.addEventListener('turbo:before-render', () => {
    const $table = $('#customersTable');
    if ($table.length && $.fn.DataTable.isDataTable($table)) {
        try {
            $table.DataTable().clear().destroy();
        } catch (e) {
            try { $table.DataTable().destroy(true); } catch (_) {}
        }
        $table.removeData('dt-initialized');
    }
});
