import '../styles/serviceindex.css';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

$(document).ready(() => {
    $('#servicesTable').DataTable({
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [{ orderable: false, targets: 5 }], // Actions column
        language: {
            search: "Search services:",
            info: "Showing _START_ to _END_ of _TOTAL_ services",
            paginate: { previous: "Prev", next: "Next" }
        }
    });
});
