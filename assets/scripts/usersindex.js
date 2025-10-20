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
// Initialize DataTable on page load
// -------------------------------
$(document).ready(() => {
    $('#customersTable').DataTable({
        pageLength: 10,           // Number of rows per page
        lengthChange: false,      // Disable changing page length
        ordering: true,           // Enable column sorting
        responsive: true,         // Enable responsive layout
        autoWidth: false,         // Disable automatic column width
        columnDefs: [
            { orderable: false, targets: 5 } // Disable sorting on "Actions" column
        ],
        language: {
            search: "Search customers:",
            info: "Showing _START_ to _END_ of _TOTAL_ customers",
            paginate: { previous: "Prev", next: "Next" }
        }
    });
});
