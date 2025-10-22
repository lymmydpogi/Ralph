// Build a new row from the Symfony prototype
function buildRowFromPrototype(html) {
  const tmp = document.createElement('div');
  tmp.innerHTML = html.trim();

  const row = document.createElement('tr');
  row.className = 'order-item-row';

  const cell = (el) => {
    const td = document.createElement('td');
    if (el) td.appendChild(el);
    return td;
  };

  // Clone inputs to prevent removing from prototype
  const productEl = tmp.querySelector('[name$="[product]"]')?.cloneNode(true);
  const serviceEl = tmp.querySelector('[name$="[service]"]')?.cloneNode(true);
  const nameEl = tmp.querySelector('[name$="[name]"]')?.cloneNode(true);
  const priceEl = tmp.querySelector('[name$="[price]"]')?.cloneNode(true);
  const qtyEl = tmp.querySelector('[name$="[quantity]"]')?.cloneNode(true);

  if (productEl) row.appendChild(cell(productEl));
  if (serviceEl) row.appendChild(cell(serviceEl));

  // Hidden cell for name, price, quantity
  const hidden = document.createElement('td');
  hidden.style.display = 'none';
  if (nameEl) hidden.appendChild(nameEl);
  if (priceEl) hidden.appendChild(priceEl);
  if (qtyEl) hidden.appendChild(qtyEl);
  row.appendChild(hidden);

  // Remove button
  const removeTd = cell();
  removeTd.innerHTML = '<button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>';
  row.appendChild(removeTd);

  return row;
}

// Wire row inputs for syncing and recalculating total
function wireRowBehavior(row) {
  const productSel = row.querySelector('[name$="[product]"]');
  const serviceSel = row.querySelector('[name$="[service]"]');
  const nameInput = row.querySelector('[name$="[name]"]');
  const qtyInput = row.querySelector('[name$="[quantity]"]');

  const syncName = (sel) => {
    if (nameInput && !nameInput.value) {
      const opt = sel.options[sel.selectedIndex];
      if (opt && opt.text && sel.value) nameInput.value = opt.text;
    }
  };

  if (productSel) productSel.addEventListener('change', () => { syncName(productSel); updateClientTotal(); });
  if (serviceSel) serviceSel.addEventListener('change', () => { syncName(serviceSel); updateClientTotal(); });
  if (qtyInput) qtyInput.addEventListener('input', updateClientTotal);
}

// Initialize Order Items
function setupOrderItems() {
  const tbody = document.getElementById('order-items-body');
  if (!tbody) return;

  const proto = tbody.dataset.prototype;
  let index = parseInt(tbody.dataset.index || '0', 10);

  // Wire existing rows
  tbody.querySelectorAll('tr.order-item-row').forEach(wireRowBehavior);

  // Create default row if empty
  if (tbody.querySelectorAll('tr.order-item-row').length === 0 && proto) {
    const html = proto.replace(/__name__/g, index);
    index++;
    tbody.dataset.index = String(index);
    const row = buildRowFromPrototype(html);
    tbody.appendChild(row);
    wireRowBehavior(row);
  }

  // Add new row button
  const addBtn = document.getElementById('add-order-item');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const html = proto.replace(/__name__/g, index);
      index++;
      tbody.dataset.index = String(index);
      const row = buildRowFromPrototype(html);
      tbody.appendChild(row);
      wireRowBehavior(row);
      updateClientTotal();
    });
  }

  // Remove row dynamically
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-item')) {
      const row = e.target.closest('tr');
      if (row) row.remove();
      updateClientTotal();
    }
  });

  updateClientTotal();
}

// Recalculate total price
function updateClientTotal() {
  const tbody = document.getElementById('order-items-body');
  if (!tbody) return;

  let total = 0;
  tbody.querySelectorAll('tr.order-item-row').forEach(row => {
    const productSel = row.querySelector('[name$="[product]"]');
    const serviceSel = row.querySelector('[name$="[service]"]');
    const qtyInput = row.querySelector('[name$="[quantity]"]');
    const qty = qtyInput && qtyInput.value ? parseInt(qtyInput.value, 10) : 1;

    let price = 0;
    const addFrom = (sel) => {
      if (!sel || !sel.value) return;
      const opt = sel.options[sel.selectedIndex];
      const p = parseFloat(opt?.dataset?.price || '0');
      price += isNaN(p) ? 0 : p;
    };

    addFrom(productSel);
    addFrom(serviceSel);
    total += price * (isNaN(qty) ? 1 : Math.max(qty, 1));
  });

  const totalInput = document.querySelector('[name$="[totalPrice]"]');
  if (totalInput) totalInput.value = total.toFixed(2);
}

// Save button recalculation
function wireSaveButton() {
  const btn = document.getElementById('save-order');
  const form = document.getElementById('order_form');
  if (!btn || !form) return;

  btn.addEventListener('click', () => {
    updateClientTotal(); // Ensure all dynamic fields are counted
    // Native form submission continues automatically
  });
}

// Initialize everything
document.addEventListener('DOMContentLoaded', () => {
  setupOrderItems();
  wireSaveButton();
});
