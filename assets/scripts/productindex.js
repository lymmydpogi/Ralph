import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css';
import '../styles/productindex.css';

function initProductsTable() {
  const $table = $('#productsTable');
  if (!$table.length) return;

  if ($table.data('dt-initialized')) return;

  if ($.fn.DataTable.isDataTable($table)) {
    try {
      $table.DataTable().clear().destroy();
    } catch (e) {
      try { $table.DataTable().destroy(true); } catch (_) {}
    }
  }

  const dt = $table.DataTable({
    destroy: true,
    pageLength: 10,
    lengthChange: false,
    ordering: true,
    pagingType: 'full_numbers',
    dom: '<"dt-toolbar d-flex justify-content-between align-items-center"lf>t<"dt-footer d-flex justify-content-between align-items-center"ip>',
    responsive: { details: false }, // keep header/body aligned, no child rows
    autoWidth: false,
    // Use default DataTables layout for consistent alignment
    // dom defaults to 'lfrtip' when lengthChange is true; since it's false, effectively 'frtip'
    columnDefs: [
      { orderable: false, targets: 5 }, // actions
      { className: 'dt-desc', targets: 1 }, // description wraps
      { className: 'dt-nowrap text-end', targets: 2 }, // price
      { className: 'text-center', targets: 4 }, // active
      { className: 'dt-nowrap text-center', targets: 5 } // actions
    ],
    language: {
      search: 'Search products:',
      info: 'Showing _START_ to _END_ of _TOTAL_ products',
      emptyTable: 'No products found',
      paginate: { first: 'First', previous: 'Prev', next: 'Next', last: 'Last' }
    }
  });

  $table.data('dt-initialized', true);

  // Explicitly wire pagination to ensure all buttons work and scroll to top
  const wirePagination = () => {
    const api = dt;
    const $wrapper = $table.closest('.dataTables_wrapper');
    $wrapper.off('click.dt-pg');
    $wrapper.on('click.dt-pg', '.paginate_button.first, .page-link[aria-label="First"]', (e) => { e.preventDefault(); api.page('first').draw('page'); window.scrollTo({ top: 0, behavior: 'smooth' }); });
    $wrapper.on('click.dt-pg', '.paginate_button.previous, .page-link[aria-label="Previous"]', (e) => { e.preventDefault(); api.page('previous').draw('page'); window.scrollTo({ top: 0, behavior: 'smooth' }); });
    $wrapper.on('click.dt-pg', '.paginate_button.next, .page-link[aria-label="Next"]', (e) => { e.preventDefault(); api.page('next').draw('page'); window.scrollTo({ top: 0, behavior: 'smooth' }); });
    $wrapper.on('click.dt-pg', '.paginate_button.last, .page-link[aria-label="Last"]', (e) => { e.preventDefault(); api.page('last').draw('page'); window.scrollTo({ top: 0, behavior: 'smooth' }); });
  };
  wirePagination();
}

function wireRowActions() {
  // Delegate to the table container
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;

    const action = btn.getAttribute('data-action');
    if (action === 'view' || action === 'edit') {
      const href = btn.getAttribute('data-href');
      if (href) window.location.href = href;
      return;
    }

    if (action === 'delete') {
      const form = btn.closest('form.js-delete-form');
      if (form) {
        if (confirm('Delete this product?')) {
          form.submit();
        }
      }
    }
  });
}

// lifecycle hooks
function onLoad() {
  initProductsTable();
  wireRowActions();
}

document.addEventListener('DOMContentLoaded', onLoad);
// Turbo support
document.addEventListener('turbo:load', onLoad);

document.addEventListener('turbo:before-render', () => {
  const $table = $('#productsTable');
  if ($table.length && $.fn.DataTable.isDataTable($table)) {
    try {
      $table.DataTable().clear().destroy();
    } catch (e) {
      try { $table.DataTable().destroy(true); } catch (_) {}
    }
    $table.removeData('dt-initialized');
  }
});
