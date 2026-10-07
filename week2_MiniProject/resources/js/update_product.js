/**
 * Product Edit Page Logic
 * Implements dd_update_product.md
 */

document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('[data-product-id]');
  const statusEl = document.querySelector('.js-edit-status');
  const formEl = document.querySelector('.js-product-form');
  const nameInput = document.querySelector('.js-product-name');
  const nameErrorEl = document.querySelector('.js-name-error');
  const priceInput = document.querySelector('.js-product-price');
  const priceErrorEl = document.querySelector('.js-price-error');
  const categorySelect = document.querySelector('.js-product-category');
  const categoryStatusEl = document.querySelector('.js-category-status');
  const categoryErrorEl = document.querySelector('.js-category-error');
  const descriptionInput = document.querySelector('.js-product-description');
  const descriptionErrorEl = document.querySelector('.js-description-error');
  const submitButton = document.querySelector('.js-product-submit');
  const resultEl = document.querySelector('.js-form-result');

  const productId = container ? container.dataset.productId : null;

  let loadedProduct = null;
  let availableCategories = [];
  let categoryStatus = 'loading'; // 'loading' | 'ready' | 'empty' | 'error'
  let isSubmitting = false;
  let isProductReady = false;

  /**
   * Count Unicode code points
   * @param {string} str
   * @returns {number}
   */
  const countUnicodeChars = (str) => [...str].length;

  /**
   * Clear all field errors
   */
  const clearAllErrors = () => {
    if (nameErrorEl) nameErrorEl.textContent = '';
    if (nameInput) {
      nameInput.setAttribute('aria-invalid', 'false');
      nameInput.removeAttribute('aria-describedby');
    }

    if (priceErrorEl) priceErrorEl.textContent = '';
    if (priceInput) {
      priceInput.setAttribute('aria-invalid', 'false');
      priceInput.setAttribute('aria-describedby', 'product-price-help');
    }

    if (categoryErrorEl) categoryErrorEl.textContent = '';
    if (categorySelect) {
      categorySelect.setAttribute('aria-invalid', 'false');
      categorySelect.setAttribute('aria-describedby', 'product-category-status');
    }

    if (descriptionErrorEl) descriptionErrorEl.textContent = '';
    if (descriptionInput) {
      descriptionInput.setAttribute('aria-invalid', 'false');
      descriptionInput.removeAttribute('aria-describedby');
    }
  };

  /**
   * Set form submission state
   * @param {boolean} submitting
   */
  const setSubmittingState = (submitting) => {
    isSubmitting = submitting;

    if (nameInput) nameInput.disabled = submitting || !isProductReady;
    if (priceInput) priceInput.disabled = submitting || !isProductReady;
    if (descriptionInput) descriptionInput.disabled = submitting || !isProductReady;

    if (submitting) {
      if (categorySelect) categorySelect.disabled = true;
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Đang lưu…';
      }
    } else {
      if (categorySelect) {
        categorySelect.disabled = categoryStatus !== 'ready';
      }
      if (submitButton) {
        submitButton.textContent = 'Lưu thay đổi';
        updateSubmitButtonState();
      }
    }
  };

  /**
   * Update submit button enabled/disabled state
   */
  const updateSubmitButtonState = () => {
    if (!submitButton) return;
    const isCategorySelected = categorySelect && categorySelect.value !== '';
    submitButton.disabled = (
      !isProductReady ||
      categoryStatus !== 'ready' ||
      !isCategorySelected ||
      isSubmitting
    );
  };

  /**
   * Display top-level status
   * @param {string} text
   * @param {'info'|'error'} type
   */
  const setTopStatus = (text, type = 'info') => {
    if (!statusEl) return;
    statusEl.textContent = text;
    statusEl.className = 'status-box js-edit-status';
    if (type === 'error') {
      statusEl.classList.add('status-box--error');
    } else {
      statusEl.classList.add('status-box--info');
    }
    statusEl.style.display = text ? 'block' : 'none';
  };

  /**
   * Display result banner
   * @param {string} message
   * @param {'success'|'error'} type
   */
  const setResult = (message, type = 'success') => {
    if (!resultEl) return;
    resultEl.textContent = message;
    resultEl.className = 'product-form__result js-form-result';
    if (type === 'success') {
      resultEl.classList.add('is-success');
    } else {
      resultEl.classList.add('is-error');
    }
    resultEl.style.display = message ? 'block' : 'none';
  };

  /**
   * Load categories from API
   */
  const loadCategories = async () => {
    categoryStatus = 'loading';
    if (categorySelect) categorySelect.disabled = true;
    if (categoryStatusEl) {
      categoryStatusEl.textContent = 'Đang tải danh mục…';
      categoryStatusEl.className = 'product-form__status js-category-status';
    }
    updateSubmitButtonState();

    try {
      const response = await fetch('/api/categories', {
        headers: {
          'Accept': 'application/json',
        },
      });

      if (!response.ok) {
        throw new Error('HTTP error');
      }

      const body = await response.json();
      if (!body || !Array.isArray(body.data)) {
        throw new Error('Invalid structure');
      }

      availableCategories = body.data;

      if (body.data.length === 0) {
        categoryStatus = 'empty';
        if (categorySelect) {
          categorySelect.replaceChildren();
          const defaultOpt = document.createElement('option');
          defaultOpt.value = '';
          defaultOpt.textContent = 'Chọn danh mục';
          categorySelect.appendChild(defaultOpt);
          categorySelect.disabled = true;
        }
        if (categoryStatusEl) {
          categoryStatusEl.textContent = 'Chưa có danh mục để chọn';
        }
        updateSubmitButtonState();
        return;
      }

      categoryStatus = 'ready';
      if (categorySelect) {
        categorySelect.replaceChildren();
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Chọn danh mục';
        categorySelect.appendChild(defaultOpt);

        body.data.forEach((cat) => {
          if (cat && typeof cat.id !== 'undefined' && typeof cat.name !== 'undefined') {
            const opt = document.createElement('option');
            opt.value = String(cat.id);
            opt.textContent = String(cat.name);
            categorySelect.appendChild(opt);
          }
        });

        // Set selected category if matches product's original category
        if (loadedProduct && loadedProduct.category && loadedProduct.category.id) {
          const matchingCat = body.data.find(
            (c) => String(c.id) === String(loadedProduct.category.id)
          );
          if (matchingCat) {
            categorySelect.value = String(matchingCat.id);
            if (categoryStatusEl) categoryStatusEl.textContent = '';
          } else {
            categorySelect.value = '';
            if (categoryStatusEl) {
              categoryStatusEl.textContent = 'Danh mục cũ không còn hợp lệ, vui lòng chọn lại.';
            }
          }
        } else {
          categorySelect.value = '';
          if (categoryStatusEl) categoryStatusEl.textContent = '';
        }

        categorySelect.disabled = isSubmitting;
      }

      updateSubmitButtonState();
    } catch {
      categoryStatus = 'error';
      if (categorySelect) categorySelect.disabled = true;
      if (categoryStatusEl) {
        categoryStatusEl.textContent = 'Không tải được danh mục';
      }
      updateSubmitButtonState();
    }
  };

  /**
   * Load product details and initialize form
   */
  const loadProductAndForm = async () => {
    if (!productId) {
      setTopStatus('Sản phẩm không tồn tại.', 'error');
      return;
    }

    setTopStatus('Đang tải thông tin sản phẩm…', 'info');
    if (formEl) formEl.style.display = 'none';

    try {
      const response = await fetch(`/api/products/${productId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });

      if (response.status === 404) {
        setTopStatus('Sản phẩm không tồn tại.', 'error');
        return;
      }

      if (!response.ok) {
        throw new Error('HTTP error');
      }

      const json = await response.json();
      if (!json || !json.data || !json.data.id || String(json.data.id) !== String(productId)) {
        throw new Error('Invalid structure');
      }

      loadedProduct = json.data;
      isProductReady = true;

      // Populate text fields
      if (nameInput) {
        nameInput.value = loadedProduct.name || '';
        nameInput.disabled = false;
      }
      if (priceInput) {
        priceInput.value = loadedProduct.price || '';
        priceInput.disabled = false;
      }
      if (descriptionInput) {
        descriptionInput.value = loadedProduct.description || '';
        descriptionInput.disabled = false;
      }

      // Display form and hide top loading
      if (formEl) formEl.style.display = '';
      setTopStatus('', 'info');

      // Load categories
      await loadCategories();
    } catch {
      setTopStatus('Không tải được thông tin sản phẩm.', 'error');
    }
  };

  /**
   * Client-side validation
   * @returns {{isValid: boolean, errors: Object.<string, string>, normalized: Object}}
   */
  const validateClientForm = () => {
    const errors = {};
    const normalized = {};

    const trimmedName = nameInput ? nameInput.value.trim() : '';
    if (trimmedName === '') {
      errors.name = 'Vui lòng nhập tên sản phẩm';
    } else if (countUnicodeChars(trimmedName) > 255) {
      errors.name = 'Tên sản phẩm tối đa 255 ký tự';
    } else {
      normalized.name = trimmedName;
    }

    const trimmedPrice = priceInput ? priceInput.value.trim() : '';
    if (trimmedPrice === '') {
      errors.price = 'Vui lòng nhập giá';
    } else {
      const priceRegex = /^[+-]?(?:\d+(?:\.\d{0,2})?|\.\d{1,2})$/;
      const num = Number(trimmedPrice);
      if (!priceRegex.test(trimmedPrice) || Number.isNaN(num) || num < 0 || num > 99999999.99) {
        errors.price = 'Giá phải từ 0 đến 99999999.99 và có tối đa 2 chữ số thập phân';
      } else {
        normalized.price = trimmedPrice;
      }
    }

    const categoryVal = categorySelect ? categorySelect.value : '';
    if (!categoryVal) {
      errors.category_id = 'Vui lòng chọn danh mục';
    } else {
      const catId = Number.parseInt(categoryVal, 10);
      const isExisting = availableCategories.some((c) => String(c.id) === String(catId));
      if (!isExisting) {
        errors.category_id = 'Vui lòng chọn danh mục';
      } else {
        normalized.category_id = catId;
      }
    }

    const rawDesc = descriptionInput ? descriptionInput.value : '';
    const trimmedDesc = rawDesc.trim();
    if (trimmedDesc !== '' && countUnicodeChars(trimmedDesc) > 2000) {
      errors.description = 'Mô tả tối đa 2000 ký tự';
    } else {
      normalized.description = trimmedDesc === '' ? null : trimmedDesc;
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
      normalized,
    };
  };

  /**
   * Render validation errors and focus first error field
   * @param {Object.<string, string>} errors
   */
  const displayErrorsAndFocus = (errors) => {
    clearAllErrors();
    let firstErrorInput = null;

    if (errors.name) {
      if (nameErrorEl) nameErrorEl.textContent = errors.name;
      if (nameInput) {
        nameInput.setAttribute('aria-invalid', 'true');
        nameInput.setAttribute('aria-describedby', 'product-name-error');
        if (!firstErrorInput) firstErrorInput = nameInput;
      }
    }

    if (errors.price) {
      if (priceErrorEl) priceErrorEl.textContent = errors.price;
      if (priceInput) {
        priceInput.setAttribute('aria-invalid', 'true');
        priceInput.setAttribute('aria-describedby', 'product-price-error');
        if (!firstErrorInput) firstErrorInput = priceInput;
      }
    }

    if (errors.category_id) {
      if (categoryErrorEl) categoryErrorEl.textContent = errors.category_id;
      if (categorySelect) {
        categorySelect.setAttribute('aria-invalid', 'true');
        categorySelect.setAttribute('aria-describedby', 'product-category-error');
        if (!firstErrorInput) firstErrorInput = categorySelect;
      }
    }

    if (errors.description) {
      if (descriptionErrorEl) descriptionErrorEl.textContent = errors.description;
      if (descriptionInput) {
        descriptionInput.setAttribute('aria-invalid', 'true');
        descriptionInput.setAttribute('aria-describedby', 'product-description-error');
        if (!firstErrorInput) firstErrorInput = descriptionInput;
      }
    }

    if (firstErrorInput) {
      firstErrorInput.focus();
    }
  };

  /**
   * Handle form submission via PUT
   * @param {Event} e
   */
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (isSubmitting || !isProductReady || categoryStatus !== 'ready') return;

    if (resultEl) resultEl.style.display = 'none';

    const { isValid, errors, normalized } = validateClientForm();
    if (!isValid) {
      displayErrorsAndFocus(errors);
      return;
    }

    clearAllErrors();
    setSubmittingState(true);

    try {
      const response = await fetch(`/api/products/${productId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(normalized),
      });

      if (response.status === 200) {
        const json = await response.json();
        if (
          !json ||
          !json.data ||
          !json.data.id ||
          String(json.data.id) !== String(productId)
        ) {
          setResult('Chưa xác nhận được kết quả cập nhật sản phẩm.', 'error');
          setSubmittingState(false);
          return;
        }

        // Update local loaded product data and form
        loadedProduct = json.data;
        if (nameInput) nameInput.value = loadedProduct.name;
        if (priceInput) priceInput.value = loadedProduct.price;
        if (descriptionInput) descriptionInput.value = loadedProduct.description || '';
        if (categorySelect && loadedProduct.category) {
          categorySelect.value = String(loadedProduct.category.id);
        }

        setResult('Cập nhật sản phẩm thành công.', 'success');
        setSubmittingState(false);
        return;
      }

      if (response.status === 422) {
        const json = await response.json().catch(() => ({}));
        const serverErrors = json.errors || {};
        const fieldErrors = {};

        if (serverErrors.name && serverErrors.name[0]) {
          fieldErrors.name = serverErrors.name[0];
        }
        if (serverErrors.price && serverErrors.price[0]) {
          fieldErrors.price = serverErrors.price[0];
        }
        if (serverErrors.category_id && serverErrors.category_id[0]) {
          fieldErrors.category_id = serverErrors.category_id[0];
          // UFE16: reset select and reload categories
          if (categorySelect) categorySelect.value = '';
          loadCategories();
        }
        if (serverErrors.description && serverErrors.description[0]) {
          fieldErrors.description = serverErrors.description[0];
        }

        // Unmapped errors
        const unmapped = Object.keys(serverErrors).filter(
          (k) => !['name', 'price', 'category_id', 'description'].includes(k)
        );
        if (unmapped.length > 0) {
          setResult(serverErrors[unmapped[0]][0] || 'Dữ liệu không hợp lệ.', 'error');
        }

        setSubmittingState(false);
        displayErrorsAndFocus(fieldErrors);
        return;
      }

      if (response.status === 404) {
        setResult('Sản phẩm không tồn tại.', 'error');
        setSubmittingState(false);
        if (submitButton) submitButton.disabled = true;
        return;
      }

      if (response.status === 415) {
        setResult('Không gửi được dữ liệu cập nhật sản phẩm.', 'error');
        setSubmittingState(false);
        return;
      }

      // 500 or unknown status
      setResult('Chưa xác nhận được kết quả cập nhật sản phẩm.', 'error');
      setSubmittingState(false);
    } catch {
      setResult('Chưa xác nhận được kết quả cập nhật sản phẩm.', 'error');
      setSubmittingState(false);
    }
  };

  // Field input events to clear respective field errors
  const attachFieldClearError = (inputEl, errorEl) => {
    if (!inputEl) return;
    inputEl.addEventListener('input', () => {
      if (errorEl) errorEl.textContent = '';
      inputEl.setAttribute('aria-invalid', 'false');
      if (resultEl) resultEl.style.display = 'none';
      updateSubmitButtonState();
    });
  };

  attachFieldClearError(nameInput, nameErrorEl);
  attachFieldClearError(priceInput, priceErrorEl);
  attachFieldClearError(descriptionInput, descriptionErrorEl);

  if (categorySelect) {
    categorySelect.addEventListener('change', () => {
      if (categoryErrorEl) categoryErrorEl.textContent = '';
      categorySelect.setAttribute('aria-invalid', 'false');
      if (resultEl) resultEl.style.display = 'none';
      updateSubmitButtonState();
    });
  }

  if (formEl) {
    formEl.addEventListener('submit', handleSubmit);
  }

  // Initial load
  loadProductAndForm();
});
