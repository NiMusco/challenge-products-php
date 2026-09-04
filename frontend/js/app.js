(() => {
  'use strict';

  const API_BASE = (window.APP_CONFIG && window.APP_CONFIG.apiBaseUrl) || 'http://localhost:8081';
  const PER_PAGE = 5;

  let currentPage = 1;
  let totalPages = 1;

  const flash = document.getElementById('flash');
  const form = document.getElementById('product-form');
  const formTitle = document.getElementById('form-title');
  const submitBtn = document.getElementById('submit-btn');
  const cancelEdit = document.getElementById('cancel-edit');
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

  function resetForm() {
    form.reset();
    productId.value = '';
    formTitle.textContent = 'Agregar producto';
    submitBtn.textContent = 'Guardar';
    cancelEdit.classList.add('hidden');
  }

  function enterEditMode(product) {
    productId.value = String(product.id);
    nombre.value = product.nombre;
    descripcion.value = product.descripcion;
    precio.value = String(product.precio);
    formTitle.textContent = `Editar producto #${product.id}`;
    submitBtn.textContent = 'Actualizar';
    cancelEdit.classList.remove('hidden');
  }

  function updatePager() {
    pageInfo.textContent = `Página ${currentPage} de ${Math.max(totalPages, 1)}`;
    prevPage.disabled = currentPage <= 1;
    nextPage.disabled = currentPage >= totalPages;
  }

  function renderProducts(products) {
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
        <td class="border border-gray-400 p-2">
          <button type="button" data-action="edit" class="border border-gray-500 bg-gray-200 px-2 py-0.5">Editar</button>
          <button type="button" data-action="delete" class="border border-red-700 bg-red-600 px-2 py-0.5 text-white">Eliminar</button>
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
      resetForm();
      await loadProducts(currentPage);
    } catch (error) {
      showFlash(error.message, true);
    } finally {
      submitBtn.disabled = false;
    }
  });

  cancelEdit.addEventListener('click', () => {
    clearFlash();
    resetForm();
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

  tbody.addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-action]');
    if (!button) return;

    const row = button.closest('tr[data-id]');
    if (!row) return;

    const id = Number(row.dataset.id);

    if (button.dataset.action === 'edit') {
      clearFlash();
      try {
        enterEditMode(await apiRequest(`/productos/${id}`));
      } catch (error) {
        showFlash(error.message, true);
      }
      return;
    }

    if (button.dataset.action === 'delete') {
      if (!window.confirm(`¿Eliminar producto #${id}?`)) return;
      clearFlash();
      try {
        await apiRequest(`/productos/${id}`, { method: 'DELETE' });
        showFlash(`Producto #${id} eliminado`);
        if (productId.value === String(id)) resetForm();
        await loadProducts(currentPage);
      } catch (error) {
        showFlash(error.message, true);
      }
    }
  });

  loadProducts(1);
})();
