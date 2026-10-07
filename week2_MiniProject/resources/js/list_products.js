/**
 * Product List Page Logic
 * Implements dd_list_products.md and dd_delete_product.md
 */

document.addEventListener('DOMContentLoaded', () => {
  const statusEl = document.querySelector('.js-list-status');
  const tableWrapperEl = document.querySelector('.js-table-wrapper');
  const tableBodyEl = document.querySelector('.js-product-table-body');
  const paginationNavEl = document.querySelector('.js-pagination');
  const paginationInfoEl = document.querySelector('.js-pagination-info');
  const btnPrev = document.querySelector('.js-btn-prev');
  const btnNext = document.querySelector('.js-btn-next');

  let currentPage = 1;
  let lastPage = 1;
  let totalItems = 0;
  let isLoading = false;
  let isDeleting = false;
  let isFallbackAttempted = false;

  /**
   * Format decimal price with VNĐ suffix
   * @param {string|number} price
   * @returns {string}
   */
  const formatPriceVND = (price) => {
    const num = Number.parseFloat(price);
    if (Number.isNaN(num)) {
      return `${price} VNĐ`;
    }
    return `${num.toFixed(2)} VNĐ`;
  };

  /**
   * Parse page parameter from current URL
   * @returns {number}
   */
  const getPageFromUrl = () => {
    const params = new URLSearchParams(window.location.search);
    const rawPage = params.get('page');
    if (!rawPage) {
      return 1;
    }
    const parsed = Number.parseInt(rawPage, 10);
    return Number.isInteger(parsed) && parsed >= 1 ? parsed : 1;
  };

  /**
   * Update browser URL without page reload
   * @param {number} page
   */
  const updateUrlPage = (page) => {
    const url = new URL(window.location.href);
    if (page === 1) {
      url.searchParams.delete('page');
    } else {
      url.searchParams.set('page', page.toString());
    }
    window.history.pushState({}, '', url.toString());
  };

  /**
   * Set UI status message and style
   * @param {string} text
   * @param {'info'|'empty'|'error'|'success'} type
   */
  const setStatus = (text, type = 'info') => {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.className = 'status-box js-list-status';
    if (type === 'empty') statusEl.classList.add('status-box--empty');
    else if (type === 'error') statusEl.classList.add('status-box--error');
    else if (type === 'success') statusEl.classList.add('status-box--success');
    else statusEl.classList.add('status-box--info');
    statusEl.style.display = text ? 'block' : 'none';
  };

  /**
   * Render table rows safely using textContent
   * @param {Array} products
   */
  const renderTable = (products) => {
    if (!tableBodyEl) return;
    tableBodyEl.replaceChildren();

    products.forEach((product) => {
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-slate-50/80 transition-colors group';

      // ID
      const tdId = document.createElement('td');
      tdId.className = 'py-3.5 px-6 align-middle';
      const idBadge = document.createElement('span');
      idBadge.className = 'inline-flex items-center px-2 py-0.5 rounded font-mono text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60';
      idBadge.textContent = `SP-${product.id}`;
      tdId.appendChild(idBadge);
      tr.appendChild(tdId);

      // Name with link to detail
      const tdName = document.createElement('td');
      tdName.className = 'py-3.5 px-6 align-middle';
      const linkDetail = document.createElement('a');
      linkDetail.href = `/products/${product.id}`;
      linkDetail.className = 'text-[13.5px] font-semibold text-slate-900 block leading-snug hover:text-indigo-600 transition-colors';
      linkDetail.textContent = product.name;
      tdName.appendChild(linkDetail);
      if (product.description) {
        const descSpan = document.createElement('span');
        descSpan.className = 'text-[12px] text-slate-500 mt-0.5 block truncate max-w-md';
        descSpan.textContent = product.description;
        tdName.appendChild(descSpan);
      }
      tr.appendChild(tdName);

      // Price
      const tdPrice = document.createElement('td');
      tdPrice.className = 'py-3.5 px-6 align-middle text-right font-semibold text-[13.5px] text-slate-900 font-mono tabular-nums whitespace-nowrap';
      tdPrice.textContent = formatPriceVND(product.price);
      tr.appendChild(tdPrice);

      // Category
      const tdCategory = document.createElement('td');
      tdCategory.className = 'py-3.5 px-6 align-middle text-center';
      const badge = document.createElement('span');
      badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/80';
      const dot = document.createElement('span');
      dot.className = 'w-1.5 h-1.5 rounded-full bg-indigo-500 inline-block';
      badge.appendChild(dot);
      const badgeText = document.createTextNode(product.category && product.category.name ? ` ${product.category.name}` : ' —');
      badge.appendChild(badgeText);
      tdCategory.appendChild(badge);
      tr.appendChild(tdCategory);

      // Actions
      const tdActions = document.createElement('td');
      tdActions.className = 'py-3.5 px-6 align-middle text-right';
      const actionGroup = document.createElement('div');
      actionGroup.className = 'inline-flex items-center justify-end gap-1.5';

      // Edit link
      const linkEdit = document.createElement('a');
      linkEdit.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 text-xs font-medium transition-all js-edit-link';
      linkEdit.href = `/products/${product.id}/edit`;
      linkEdit.title = 'Chỉnh sửa sản phẩm';
      linkEdit.innerHTML = `<span class="material-symbols-outlined text-[16px]">edit</span><span>Sửa</span>`;
      actionGroup.appendChild(linkEdit);

      // Delete button
      const btnDelete = document.createElement('button');
      btnDelete.className = 'inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 text-xs font-medium transition-all js-btn-delete';
      btnDelete.type = 'button';
      btnDelete.title = 'Xóa sản phẩm';
      btnDelete.innerHTML = `<span class="material-symbols-outlined text-[16px]">delete</span><span>Xóa</span>`;
      btnDelete.dataset.id = product.id;
      btnDelete.dataset.name = product.name;
      btnDelete.addEventListener('click', () => handleDeleteProduct(product.id, product.name, btnDelete));
      actionGroup.appendChild(btnDelete);

      tdActions.appendChild(actionGroup);
      tr.appendChild(tdActions);

      tableBodyEl.appendChild(tr);
    });

    // Update Overview Metric Cards if present
    const metricCountEl = document.querySelector('.js-metric-total-products');
    if (metricCountEl) {
      metricCountEl.textContent = `${totalItems} mặt hàng`;
    }
    const metricValEl = document.querySelector('.js-metric-total-value');
    if (metricValEl && products.length > 0) {
      const sum = products.reduce((acc, p) => acc + (parseFloat(p.price) || 0), 0);
      metricValEl.textContent = formatPriceVND(sum);
    }
    const metricCatEl = document.querySelector('.js-metric-total-categories');
    if (metricCatEl && products.length > 0) {
      const cats = new Set(products.map((p) => (p.category ? p.category.name : null)).filter(Boolean));
      metricCatEl.textContent = `${cats.size} danh mục`;
    }
  };

  /**
   * Update pagination controls state
   */
  const updatePaginationUI = () => {
    const headerBadgeEl = document.querySelector('.js-header-badge');
    if (headerBadgeEl && totalItems > 0) {
      headerBadgeEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ${totalItems} mặt hàng trong kho`;
    }

    if (!paginationNavEl || !paginationInfoEl || !btnPrev || !btnNext) return;

    if (totalItems === 0) {
      paginationNavEl.style.display = 'none';
      return;
    }

    paginationNavEl.style.display = 'flex';
    paginationInfoEl.innerHTML = `Hiển thị trang <span class="text-slate-800 font-semibold">${currentPage}</span> / ${lastPage} (Tổng cộng: <span class="font-semibold text-slate-700">${totalItems}</span> sản phẩm)`;

    btnPrev.disabled = isLoading || currentPage <= 1;
    btnNext.disabled = isLoading || currentPage >= lastPage;
  };

  /**
   * Handle deleting a product
   * @param {number|string} id
   * @param {string} name
   * @param {HTMLButtonElement} btnElement
   */
  const handleDeleteProduct = async (id, name, btnElement) => {
    if (isDeleting || isLoading) return;

    const confirmed = window.confirm(`Xóa sản phẩm "${name}"?`);
    if (!confirmed) {
      return;
    }

    isDeleting = true;
    const originalHtml = btnElement.innerHTML;
    btnElement.disabled = true;
    btnElement.innerHTML = '<span>Đang xóa…</span>';

    try {
      const response = await fetch(`/api/products/${id}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (response.status === 204) {
        setStatus('Xóa sản phẩm thành công.', 'success');
        // Reload current page
        await loadProducts(currentPage);
      } else if (response.status === 404) {
        setStatus('Sản phẩm không còn tồn tại.', 'error');
        await loadProducts(currentPage);
      } else {
        setStatus('Chưa xác nhận được kết quả xóa sản phẩm.', 'error');
        btnElement.disabled = false;
        btnElement.innerHTML = originalHtml;
      }
    } catch {
      setStatus('Chưa xác nhận được kết quả xóa sản phẩm.', 'error');
      btnElement.disabled = false;
      btnElement.innerHTML = originalHtml;
    } finally {
      isDeleting = false;
    }
  };

  /**
   * Load products list from API
   * @param {number} page
   */
  const loadProducts = async (page = 1) => {
    if (isLoading) return;
    isLoading = true;

    if (btnPrev) btnPrev.disabled = true;
    if (btnNext) btnNext.disabled = true;

    // Show loading state if table not rendered yet
    if (!tableWrapperEl || tableWrapperEl.style.display === 'none') {
      setStatus('Đang tải danh sách sản phẩm…', 'info');
    }

    try {
      const response = await fetch(`/api/products?page=${page}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const json = await response.json();

      // Validate schema
      if (!json || !Array.isArray(json.data) || !json.meta) {
        throw new Error('Cấu trúc dữ liệu danh sách sản phẩm không hợp lệ.');
      }

      const { data, meta } = json;
      totalItems = typeof meta.total === 'number' ? meta.total : 0;
      lastPage = typeof meta.last_page === 'number' && meta.last_page >= 1 ? meta.last_page : 1;
      currentPage = typeof meta.current_page === 'number' ? meta.current_page : page;

      // Handle empty system
      if (totalItems === 0) {
        if (tableWrapperEl) tableWrapperEl.style.display = 'none';
        setStatus('Chưa có sản phẩm.', 'empty');
        updatePaginationUI();
        updateUrlPage(1);
        isLoading = false;
        return;
      }

      // Handle page out of bounds (current_page > last_page)
      if (data.length === 0 && currentPage > lastPage) {
        if (!isFallbackAttempted) {
          isFallbackAttempted = true;
          isLoading = false;
          await loadProducts(lastPage);
          return;
        }
        // Fallback already attempted once, avoid infinite loop
        throw new Error('Không thể tải trang dữ liệu yêu cầu.');
      }

      // Reset fallback flag on valid page render
      isFallbackAttempted = false;

      // Render items
      renderTable(data);
      if (tableWrapperEl) tableWrapperEl.style.display = 'block';

      // Clear loading status unless it is a success message from delete
      if (statusEl && !statusEl.classList.contains('status-box--success')) {
        setStatus('', 'info');
      }

      updatePaginationUI();
      updateUrlPage(currentPage);
    } catch {
      setStatus('Không tải được danh sách sản phẩm.', 'error');
      // If table has items, keep table visible but disable pagination controls appropriately
      updatePaginationUI();
    } finally {
      isLoading = false;
      if (totalItems > 0) {
        updatePaginationUI();
      }
    }
  };

  // Pagination event listeners
  if (btnPrev) {
    btnPrev.addEventListener('click', () => {
      if (!isLoading && currentPage > 1) {
        loadProducts(currentPage - 1);
      }
    });
  }

  if (btnNext) {
    btnNext.addEventListener('click', () => {
      if (!isLoading && currentPage < lastPage) {
        loadProducts(currentPage + 1);
      }
    });
  }

  // Handle browser back / forward navigation
  window.addEventListener('popstate', () => {
    const page = getPageFromUrl();
    loadProducts(page);
  });

  // Initial load
  const initialPage = getPageFromUrl();
  loadProducts(initialPage);
});
