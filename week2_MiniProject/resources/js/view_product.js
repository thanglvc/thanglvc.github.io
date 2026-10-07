/**
 * Product Detail Page Logic
 * Implements dd_view_product.md and dd_delete_product.md
 */

document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('[data-product-id]');
  const statusEl = document.querySelector('.js-detail-status');
  const cardEl = document.querySelector('.js-detail-card');
  const idEl = document.querySelector('.js-detail-id');
  const nameEl = document.querySelector('.js-detail-name');
  const priceEl = document.querySelector('.js-detail-price');
  const categoryEl = document.querySelector('.js-detail-category');
  const descriptionEl = document.querySelector('.js-detail-description');
  const createdAtEl = document.querySelector('.js-detail-created-at');
  const updatedAtEl = document.querySelector('.js-detail-updated-at');
  const btnEdit = document.querySelector('.js-btn-edit');
  const btnDelete = document.querySelector('.js-btn-delete');

  const productId = container ? container.dataset.productId : null;
  let loadedProduct = null;
  let isDeleting = false;

  /**
   * Format price with VNĐ suffix
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
   * Set UI status message
   * @param {string} text
   * @param {'info'|'error'} type
   */
  const setStatus = (text, type = 'info') => {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.className = 'status-box js-detail-status';
    if (type === 'error') {
      statusEl.classList.add('status-box--error');
    } else {
      statusEl.classList.add('status-box--info');
    }
    statusEl.style.display = text ? 'block' : 'none';
  };

  /**
   * Lock action buttons
   */
  const lockActions = () => {
    if (btnEdit) {
      btnEdit.setAttribute('aria-disabled', 'true');
      btnEdit.setAttribute('tabindex', '-1');
      btnEdit.classList.add('btn[aria-disabled="true"]');
    }
    if (btnDelete) {
      btnDelete.disabled = true;
    }
  };

  /**
   * Unlock action buttons
   */
  const unlockActions = () => {
    if (btnEdit && loadedProduct) {
      btnEdit.setAttribute('aria-disabled', 'false');
      btnEdit.removeAttribute('tabindex');
      btnEdit.href = `/products/${loadedProduct.id}/edit`;
    }
    if (btnDelete && loadedProduct) {
      btnDelete.disabled = false;
    }
  };

  /**
   * Load product details from API
   */
  const loadProductDetail = async () => {
    if (!productId) {
      setStatus('Sản phẩm không tồn tại.', 'error');
      lockActions();
      return;
    }

    lockActions();
    setStatus('Đang tải sản phẩm…', 'info');
    if (cardEl) cardEl.style.display = 'none';

    try {
      const response = await fetch(`/api/products/${productId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (response.status === 404) {
        setStatus('Sản phẩm không tồn tại.', 'error');
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const json = await response.json();

      // Validate schema and matching ID
      if (!json || !json.data || !json.data.id || String(json.data.id) !== String(productId)) {
        throw new Error('Cấu trúc dữ liệu sản phẩm không hợp lệ.');
      }

      const product = json.data;
      loadedProduct = product;

      // Populate text safely
      if (idEl) idEl.textContent = product.id;
      if (nameEl) nameEl.textContent = product.name;
      if (priceEl) priceEl.textContent = formatPriceVND(product.price);
      if (categoryEl) {
        categoryEl.textContent = product.category && product.category.name ? product.category.name : '—';
      }
      if (descriptionEl) {
        descriptionEl.textContent = product.description !== null && product.description !== undefined && product.description !== ''
          ? product.description
          : 'Chưa có mô tả';
      }
      if (createdAtEl) {
        createdAtEl.textContent = product.created_at ? product.created_at : '—';
      }
      if (updatedAtEl) {
        updatedAtEl.textContent = product.updated_at ? product.updated_at : '—';
      }

      // Show content and enable actions
      if (cardEl) cardEl.style.display = 'block';
      setStatus('', 'info');
      unlockActions();
    } catch {
      setStatus('Không tải được thông tin sản phẩm.', 'error');
      lockActions();
    }
  };

  /**
   * Handle deleting from product detail view
   */
  const handleDelete = async () => {
    if (!loadedProduct || isDeleting) return;

    const confirmed = window.confirm(`Xóa sản phẩm "${loadedProduct.name}"?`);
    if (!confirmed) {
      return;
    }

    isDeleting = true;
    const originalText = btnDelete.textContent;
    btnDelete.disabled = true;
    btnDelete.textContent = 'Đang xóa…';

    try {
      const response = await fetch(`/api/products/${loadedProduct.id}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (response.status === 204) {
        // Successful delete: redirect to products list
        window.location.href = '/products';
        return;
      }

      if (response.status === 404) {
        setStatus('Sản phẩm không còn tồn tại.', 'error');
        window.location.href = '/products';
        return;
      }

      setStatus('Chưa xác nhận được kết quả xóa sản phẩm.', 'error');
      btnDelete.disabled = false;
      btnDelete.textContent = originalText;
    } catch {
      setStatus('Chưa xác nhận được kết quả xóa sản phẩm.', 'error');
      btnDelete.disabled = false;
      btnDelete.textContent = originalText;
    } finally {
      isDeleting = false;
    }
  };

  if (btnDelete) {
    btnDelete.addEventListener('click', handleDelete);
  }

  // Initial load
  loadProductDetail();
});
