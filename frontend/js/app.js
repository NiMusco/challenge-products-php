(() => {
  'use strict';

  const API_BASE = (window.APP_CONFIG && window.APP_CONFIG.apiBaseUrl) || 'http://localhost:8081';
  const PER_PAGE = 5;

  let currentPage = 1;
  let totalPages = 1;
  let openMenu = null;

  const flash = document.getElementById('flash');
  const dialog = document.getElementById('product-dialog');
  const form = document.getElementById('product-form');
  const formTitle = document.getElementById('form-title');
  const submitBtn = document.getElementById('submit-btn');
  const cancelBtn = document.getElementById('cancel-btn');
  const newProductBtn = document.getElementById('new-product-btn');
  const refreshBtn = document.getElementById('refresh-btn');
  const prevPage = document.getElementById('prev-page');
  const nextPage = document.getElementById('next-page');
  const pageInfo = document.getElementById('page-info');
  const productId = document.getElementById('product-id');
  const nombre = document.getElementById('nombre');
  const descripcion = document.getElementById('descripcion');
  const precio = document.getElementById('precio');
  const tbody = document.getElementById('products-body');

  function showFlash(message, isError) {
    flash.hidden = false;
    flash.classList.remove('hidden');
    flash.textContent = message;
    flash.className = isError
      ? 'mb-3 border border-red-600 bg-red-100 p-2 text-red-800'
      : 'mb-3 border border-green-600 bg-green-100 p-2 text-green-800';
  }

  function clearFlash() {
    flash.hidden = true;
    flash.textContent = '';
    flash.className = 'mb-3 hidden border p-2';
  }

  function formatMoney(value, currency) {
    return new Intl.NumberFormat('es-AR', {
      style: 'currency',
      currency,
      minimumFractionDigits: 2,
    }).format(Number(value));
  }

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;');
  }

  function icon(name, className = 'h-4 w-4') {
    return `<img src="icons/${name}.svg" alt="" class="${className}">`;
  }

  function closeOpenMenu() {
    if (openMenu) {
      openMenu.classList.add('hidden');
      openMenu = null;
    }
  }

  async function apiRequest(path, options = {}) {
    const response = await fetch(`${API_BASE}${path}`, {
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
      ...options,
    });

    if (response.status === 204) {
      return null;
    }

    let data = null;
    const raw = await response.text();
    if (raw) {
      try {
        data = JSON.parse(raw);
      } catch {
        throw new Error('Respuesta inválida de la API');
      }
    }

    if (!response.ok) {
      throw new Error((data && data.error) || `Error HTTP ${response.status}`);
    }

    return data;
  }

  function openCreateDialog() {
    form.reset();
    productId.value = '';
    formTitle.textContent = 'Nuevo producto';
    submitBtn.textContent = 'Guardar';
    dialog.showModal();
    nombre.focus();
  }

  function openEditDialog(product) {
    productId.value = String(product.id);
    nombre.value = product.nombre;
    descripcion.value = product.descripcion;
    precio.value = String(product.precio);
    formTitle.textContent = `Editar producto #${product.id}`;
    submitBtn.textContent = 'Actualizar';
    dialog.showModal();
    nombre.focus();
  }

  function closeDialog() {
    dialog.close();
    form.reset();
    productId.value = '';
  }

  function updatePager() {
    pageInfo.textContent = `Página ${currentPage} de ${Math.max(totalPages, 1)}`;
    prevPage.disabled = currentPage <= 1;
    nextPage.disabled = currentPage >= totalPages;
  }

  function renderProducts(products) {
    closeOpenMenu();

    if (!products.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="border border-gray-400 p-2 text-center">Sin productos</td></tr>';
      return;
    }

    tbody.innerHTML = products.map((p) => `
      <tr data-id="${p.id}">
        <td class="border border-gray-400 p-2">${p.id}</td>
        <td class="border border-gray-400 p-2">${escapeHtml(p.nombre)}</td>
        <td class="border border-gray-400 p-2">${escapeHtml(p.descripcion)}</td>
        <td class="border border-gray-400 p-2">${formatMoney(p.precio, 'ARS')}</td>
        <td class="border border-gray-400 p-2">${formatMoney(p.precio_usd, 'USD')}</td>
        <td class="relative border border-gray-400 p-2">
          <button
            type="button"
            data-action="menu"
            class="inline-flex items-center border border-gray-500 bg-gray-200 p-1"
            aria-label="Acciones"
          >
            ${icon('ellipsis-vertical')}
          </button>
          <div class="menu absolute right-2 z-10 mt-1 hidden w-36 border border-gray-400 bg-white shadow">
            <button
              type="button"
              data-action="edit"
              class="flex w-full items-center gap-2 px-3 py-2 text-left hover:bg-gray-100"
            >
              ${icon('pencil')} Editar
            </button>
            <button
              type="button"
              data-action="delete"
              class="flex w-full items-center gap-2 px-3 py-2 text-left text-red-700 hover:bg-gray-100"
            >
              ${icon('trash-2')} Eliminar
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  async function loadProducts(page = currentPage) {
    tbody.innerHTML = '<tr><td colspan="6" class="border border-gray-400 p-2 text-center">Cargando...</td></tr>';
    try {
      const result = await apiRequest(`/productos?page=${page}&per_page=${PER_PAGE}`);
      currentPage = result.meta.page;
      totalPages = result.meta.total_pages;
      renderProducts(result.data);
      updatePager();
    } catch (error) {
      tbody.innerHTML = `<tr><td colspan="6" class="border border-gray-400 p-2 text-center">${escapeHtml(error.message)}</td></tr>`;
      showFlash(error.message, true);
    }
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearFlash();

    const payload = {
      nombre: nombre.value.trim(),
      descripcion: descripcion.value.trim(),
      precio: Number(precio.value),
    };

    if (!payload.nombre || !payload.descripcion || Number.isNaN(payload.precio) || payload.precio < 0) {
      showFlash('Completá todos los campos correctamente', true);
      return;
    }

    const id = productId.value;
    submitBtn.disabled = true;

    try {
      if (id) {
        await apiRequest(`/productos/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
        showFlash(`Producto #${id} actualizado`);
      } else {
        const created = await apiRequest('/productos', { method: 'POST', body: JSON.stringify(payload) });
        showFlash(`Producto #${created.id} creado`);
        currentPage = 1;
      }
      closeDialog();
      await loadProducts(currentPage);
    } catch (error) {
      showFlash(error.message, true);
    } finally {
      submitBtn.disabled = false;
    }
  });

  newProductBtn.addEventListener('click', () => {
    clearFlash();
    openCreateDialog();
  });

  cancelBtn.addEventListener('click', () => {
    closeDialog();
  });

  dialog.addEventListener('cancel', (event) => {
    event.preventDefault();
    closeDialog();
  });

  refreshBtn.addEventListener('click', () => {
    clearFlash();
    loadProducts(currentPage);
  });

  prevPage.addEventListener('click', () => {
    if (currentPage > 1) loadProducts(currentPage - 1);
  });

  nextPage.addEventListener('click', () => {
    if (currentPage < totalPages) loadProducts(currentPage + 1);
  });

  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-action="menu"]') && !event.target.closest('.menu')) {
      closeOpenMenu();
    }
  });

  tbody.addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-action]');
    if (!button) return;

    const row = button.closest('tr[data-id]');
    if (!row) return;

    const id = Number(row.dataset.id);
    const action = button.dataset.action;

    if (action === 'menu') {
      event.stopPropagation();
      const menu = row.querySelector('.menu');
      if (!menu) return;

      const isOpen = openMenu === menu;
      closeOpenMenu();
      if (!isOpen) {
        menu.classList.remove('hidden');
        openMenu = menu;
      }
      return;
    }

    closeOpenMenu();

    if (action === 'edit') {
      clearFlash();
      try {
        openEditDialog(await apiRequest(`/productos/${id}`));
      } catch (error) {
        showFlash(error.message, true);
      }
      return;
    }

    if (action === 'delete') {
      if (!window.confirm(`¿Eliminar producto #${id}?`)) return;
      clearFlash();
      try {
        await apiRequest(`/productos/${id}`, { method: 'DELETE' });
        showFlash(`Producto #${id} eliminado`);
        if (productId.value === String(id)) closeDialog();
        await loadProducts(currentPage);
      } catch (error) {
        showFlash(error.message, true);
      }
    }
  });

  loadProducts(1);
})();
