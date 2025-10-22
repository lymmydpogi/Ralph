function buildRowFromPrototype(html) {
  const tmp = document.createElement('div');
  tmp.innerHTML = html.trim();

  const row = document.createElement('tr');
  row.className = 'order-item-row';

  const cell = () => document.createElement('td');

  const productEl = tmp.querySelector('[name$="[product]"]');
  const serviceEl = tmp.querySelector('[name$="[service]"]');
  const nameEl = tmp.querySelector('[name$="[name]"]');
  const priceEl = tmp.querySelector('[name$="[price]"]');
  const qtyEl = tmp.querySelector('[name$="[quantity]"]');

  const c1 = cell(); c1.appendChild(productEl); row.appendChild(c1);
  const c2 = cell(); c2.appendChild(serviceEl); row.appendChild(c2);
  // Hidden carry fields to keep form structure
  const hidden = cell(); hidden.style.display = 'none';
  hidden.appendChild(nameEl); hidden.appendChild(priceEl); hidden.appendChild(qtyEl);
  row.appendChild(hidden);

  return row;
}

function wireRowBehavior(row) {
  const productSel = row.querySelector('[name$="[product]"]');
  const serviceSel = row.querySelector('[name$="[service]"]');
  const nameInput = row.querySelector('[name$="[name]"]');
  const priceInput = row.querySelector('[name$="[price]"]');
  const qtyInput = row.querySelector('[name$="[quantity]"]');

  // No type toggle: allow choosing product and/or service

  // Optional UX: when selecting product/service, set name if empty
  const syncName = (sel) => {
    if (nameInput && !nameInput.value) {
      const opt = sel.options[sel.selectedIndex];
      if (opt && opt.text && sel.value) nameInput.value = opt.text;
    }
  };
  productSel.addEventListener('change', () => syncName(productSel));
  serviceSel.addEventListener('change', () => syncName(serviceSel));
  if (qtyInput) qtyInput.addEventListener('input', updateClientTotal);
  productSel.addEventListener('change', updateClientTotal);
  serviceSel.addEventListener('change', updateClientTotal);
}
// Optional: rows are not removable in simplified UI; implement if needed

function setupOrderItems() {
  const tbody = document.getElementById('order-items-body');
  if (!tbody) return;

  const proto = tbody.dataset.prototype;
  let index = parseInt(tbody.dataset.index || '0', 10);

  // Wire existing rows
  tbody.querySelectorAll('tr.order-item-row').forEach(wireRowBehavior);

  // If no rows yet, add one so Product/Service dropdowns are visible
  if (tbody.querySelectorAll('tr.order-item-row').length === 0 && proto) {
    const html = proto.replace(/__name__/g, index);
    index += 1;
    tbody.dataset.index = String(index);
    const row = buildRowFromPrototype(html);
    tbody.appendChild(row);
    wireRowBehavior(row);
  }

  const addBtn = document.getElementById('add-order-item');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const html = proto.replace(/__name__/g, index);
      index += 1;
      tbody.dataset.index = String(index);
      const row = buildRowFromPrototype(html);
      tbody.appendChild(row);
      wireRowBehavior(row);
      updateClientTotal();
    });
  }

  // No submit-time validation: allow selecting product, service, or both per row
  updateClientTotal();
}

function updateClientTotal() {
  const tbody = document.getElementById('order-items-body');
  if (!tbody) return;
  let total = 0;
  tbody.querySelectorAll('tr.order-item-row').forEach((row) => {
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

document.addEventListener('DOMContentLoaded', setupOrderItems);
document.addEventListener('turbo:load', setupOrderItems);

// Ensure Save button submits the form reliably
function wireSaveButton() {
  const btn = document.getElementById('save-order');
  const form = document.getElementById('order_form');
  if (!btn || !form) return;
  btn.addEventListener('click', (e) => {
    // If it's already type=submit this is redundant but safe; we also recompute total
    try { updateClientTotal(); } catch (_) {}
    btn.setAttribute('disabled', 'disabled');
    setTimeout(() => btn.removeAttribute('disabled'), 3000);
    // Let native submission proceed as it's a submit button
    // For robustness, explicitly call submit if default prevented elsewhere
    setTimeout(() => {
      if (!form.dataset._submitted) {
        form.dataset._submitted = '1';
        try { form.requestSubmit ? form.requestSubmit(btn) : form.submit(); } catch (_) {}
      }
    }, 0);
  }, { once: false });
}

document.addEventListener('DOMContentLoaded', wireSaveButton);
document.addEventListener('turbo:load', wireSaveButton);
