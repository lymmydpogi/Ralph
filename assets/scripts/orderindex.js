// assets/scripts/orderindex.js

// -------------------------------
// Import page-specific CSS
// -------------------------------
import '../styles/orderindex.css';

// -------------------------------
// Import jQuery and DataTables
// -------------------------------
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

// -------------------------------
// Initialize DataTable on page load
// -------------------------------
$(document).ready(() => {
    $('#ordersTable').DataTable({
        pageLength: 10,          // Number of rows per page
        lengthChange: false,     // Disable changing page length
        ordering: true,          // Enable column sorting
        info: true,              // Show info text
        responsive: true,        // Enable responsive layout
        autoWidth: false,        // Disable automatic column width
        columnDefs: [
            { orderable: false, targets: 5 } // Disable sorting on "Actions" column
        ],
        language: {
            search: "Search orders:",
            info: "Showing _START_ to _END_ of _TOTAL_ orders",
            paginate: { previous: "Prev", next: "Next" }
        }
    });
});
