/**
 * Create Product Client Interaction Script
 * Handles category loading, form validation, submit states, and API responses.
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.js-product-form');
  const nameInput = document.querySelector('.js-product-name');
  const priceInput = document.querySelector('.js-product-price');
  const categorySelect = document.querySelector('.js-product-category');
  const descriptionInput = document.querySelector('.js-product-description');
  const submitButton = document.querySelector('.js-product-submit');
  const categoryStatusEl = document.querySelector('.js-category-status');
  const nameErrorEl = document.querySelector('.js-name-error');
  const priceErrorEl = document.querySelector('.js-price-error');
  const categoryErrorEl = document.querySelector('.js-category-error');
  const descriptionErrorEl = document.querySelector('.js-description-error');
  const resultEl = document.querySelector('.js-form-result');

  if (!form || !nameInput || !priceInput || !categorySelect || !descriptionInput || !submitButton) {
    return;
  }

  /** @type {'loading' | 'ready' | 'empty' | 'error'} */
  let categoryStatus = 'loading';
  /** @type {boolean} */
  let isSubmitting = false;
  /** @type {Object.<string, string>} */
  let fieldErrors = {};

  /**
   * Count the number of Unicode characters (code points) in a string.
   *
   * @param {string} str - String to count
   * @returns {number} Number of Unicode characters
   */
  function countUnicodeChars(str) {
    return [...str].length;
  }

  /**
   * Display a status/result message near the submit button.
   *
   * @param {string} message - Message text to display
   * @param {'success' | 'error'} type - Message status type
   * @returns {void}
   */
  function showResult(message, type) {
    if (!resultEl) return;
    resultEl.textContent = message;
    resultEl.className = 'product-form__result js-form-result';
    if (type === 'success') {
      resultEl.classList.add('product-form__result--success');
    } else if (type === 'error') {
      resultEl.classList.add('product-form__result--error');
    }
  }

  /**
   * Clear the result message.
   *
   * @returns {void}
   */
  function clearResult() {
    if (!resultEl) return;
    resultEl.textContent = '';
    resultEl.className = 'product-form__result js-form-result';
  }

  /**
   * Clear all field errors and reset ARIA attributes.
   *
   * @returns {void}
   */
  function clearAllErrors() {
    fieldErrors = {};

    if (nameErrorEl) nameErrorEl.textContent = '';
    nameInput.setAttribute('aria-invalid', 'false');
    nameInput.removeAttribute('aria-describedby');

    if (priceErrorEl) priceErrorEl.textContent = '';
    priceInput.setAttribute('aria-invalid', 'false');
    priceInput.setAttribute('aria-describedby', 'product-price-help');

    if (categoryErrorEl) categoryErrorEl.textContent = '';
    categorySelect.setAttribute('aria-invalid', 'false');
    categorySelect.setAttribute('aria-describedby', 'product-category-status');

    if (descriptionErrorEl) descriptionErrorEl.textContent = '';
    descriptionInput.setAttribute('aria-invalid', 'false');
    descriptionInput.removeAttribute('aria-describedby');
  }

  /**
   * Update form UI controls when submission starts or ends.
   *
   * @param {boolean} submitting - Whether form is currently submitting
   * @returns {void}
   */
  function setSubmittingState(submitting) {
    isSubmitting = submitting;
    nameInput.disabled = submitting;
    priceInput.disabled = submitting;
    descriptionInput.disabled = submitting;

    if (submitting) {
      categorySelect.disabled = true;
      submitButton.disabled = true;
      submitButton.textContent = 'Đang tạo…';
    } else {
      categorySelect.disabled = (categoryStatus !== 'ready');
      submitButton.textContent = 'Tạo sản phẩm';
      submitButton.disabled = (categoryStatus !== 'ready' || categorySelect.value === '');
    }
  }

  /**
   * Validate 201 response product data against expected contract.
   *
   * @param {any} data - Data object returned from API
   * @returns {boolean} True if data matches the contract schema
   */
  function isValidCreatedProduct(data) {
    if (!data || typeof data !== 'object') return false;
    if (typeof data.id !== 'number' || !Number.isInteger(data.id)) return false;
    if (typeof data.name !== 'string') return false;
    if (typeof data.price !== 'string') return false;
    if (!data.category || typeof data.category !== 'object') return false;
    if (typeof data.category.id !== 'number' || !Number.isInteger(data.category.id)) return false;
    if (typeof data.category.name !== 'string') return false;
    return true;
  }

  /**
   * Load category list from GET /api/categories.
   *
   * @returns {Promise<void>}
   */
  async function loadCategories() {
    categoryStatus = 'loading';
    categorySelect.disabled = true;
    submitButton.disabled = true;

    if (categoryStatusEl) {
      categoryStatusEl.textContent = 'Đang tải danh mục…';
      categoryStatusEl.className = 'product-form__status js-category-status';
    }

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
        throw new Error('Invalid response structure');
      }

      if (body.data.length === 0) {
        categoryStatus = 'empty';
        categorySelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Chọn danh mục';
        categorySelect.appendChild(defaultOption);
        categorySelect.disabled = true;
        submitButton.disabled = true;

        if (categoryStatusEl) {
          categoryStatusEl.textContent = 'Chưa có danh mục để chọn';
        }
      } else {
        categoryStatus = 'ready';
        categorySelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Chọn danh mục';
        categorySelect.appendChild(defaultOption);

        body.data.forEach((item) => {
          if (item && typeof item.id !== 'undefined' && typeof item.name !== 'undefined') {
            const option = document.createElement('option');
            option.value = String(item.id);
            option.textContent = String(item.name);
            categorySelect.appendChild(option);
          }
        });

        if (categoryStatusEl) {
          categoryStatusEl.textContent = '';
        }

        categorySelect.disabled = isSubmitting;
        submitButton.disabled = isSubmitting || categorySelect.value === '';
      }
    } catch (err) {
      categoryStatus = 'error';
      categorySelect.disabled = true;
      submitButton.disabled = true;

      if (categoryStatusEl) {
        categoryStatusEl.textContent = 'Không tải được danh mục';
        categoryStatusEl.classList.add('product-form__status--error');
      }
    }
  }

  /**
   * Perform client-side validation on form inputs.
   *
   * @returns {{isValid: boolean, errors: Object.<string, string>}} Validation result
   */
  function validateForm() {
    const errors = {};

    const trimmedName = nameInput.value.trim();
    if (trimmedName === '') {
      errors.name = 'Vui lòng nhập tên sản phẩm';
    } else if (countUnicodeChars(trimmedName) > 255) {
      errors.name = 'Tên sản phẩm tối đa 255 ký tự';
    }

    const trimmedPrice = priceInput.value.trim();
    if (trimmedPrice === '') {
      errors.price = 'Vui lòng nhập giá';
    } else {
      const priceRegex = /^[+-]?(?:\d+(?:\.\d{0,2})?|\.\d{1,2})$/;
      const num = Number(trimmedPrice);
      if (!priceRegex.test(trimmedPrice) || isNaN(num) || num < 0 || num > 99999999.99) {
        errors.price = 'Giá phải từ 0 đến 99999999.99 và có tối đa 2 chữ số thập phân';
      }
    }

    if (!categorySelect.value) {
      errors.category_id = 'Vui lòng chọn danh mục';
    }

    const trimmedDesc = descriptionInput.value.trim();
    if (trimmedDesc !== '' && countUnicodeChars(trimmedDesc) > 2000) {
      errors.description = 'Mô tả tối đa 2000 ký tự';
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  }

  // EVENT_EDIT handlers: clear only that field's error and clear old result message
  nameInput.addEventListener('input', () => {
    if (isSubmitting) return;
    if (fieldErrors.name) {
      delete fieldErrors.name;
      if (nameErrorEl) nameErrorEl.textContent = '';
      nameInput.setAttribute('aria-invalid', 'false');
      nameInput.removeAttribute('aria-describedby');
    }
    clearResult();
  });

  priceInput.addEventListener('input', () => {
    if (isSubmitting) return;
    if (fieldErrors.price) {
      delete fieldErrors.price;
      if (priceErrorEl) priceErrorEl.textContent = '';
      priceInput.setAttribute('aria-invalid', 'false');
      priceInput.setAttribute('aria-describedby', 'product-price-help');
    }
    clearResult();
  });

  categorySelect.addEventListener('change', () => {
    if (isSubmitting) return;
    if (fieldErrors.category_id) {
      delete fieldErrors.category_id;
      if (categoryErrorEl) categoryErrorEl.textContent = '';
      categorySelect.setAttribute('aria-invalid', 'false');
      categorySelect.setAttribute('aria-describedby', 'product-category-status');
    }
    clearResult();

    if (categoryStatus === 'ready') {
      submitButton.disabled = (categorySelect.value === '');
    }
  });

  descriptionInput.addEventListener('input', () => {
    if (isSubmitting) return;
    if (fieldErrors.description) {
      delete fieldErrors.description;
      if (descriptionErrorEl) descriptionErrorEl.textContent = '';
      descriptionInput.setAttribute('aria-invalid', 'false');
      descriptionInput.removeAttribute('aria-describedby');
    }
    clearResult();
  });

  // EVENT_SUBMIT handler
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // 1. Guard against duplicate submit or loading category state
    if (isSubmitting) {
      return;
    }
    if (categoryStatus !== 'ready') {
      return;
    }

    // 2. Validate form inputs
    clearResult();
    const validation = validateForm();

    if (!validation.isValid) {
      fieldErrors = validation.errors;

      if (fieldErrors.name) {
        if (nameErrorEl) nameErrorEl.textContent = fieldErrors.name;
        nameInput.setAttribute('aria-invalid', 'true');
        nameInput.setAttribute('aria-describedby', 'product-name-error');
      }
      if (fieldErrors.price) {
        if (priceErrorEl) priceErrorEl.textContent = fieldErrors.price;
        priceInput.setAttribute('aria-invalid', 'true');
        priceInput.setAttribute('aria-describedby', 'product-price-error product-price-help');
      }
      if (fieldErrors.category_id) {
        if (categoryErrorEl) categoryErrorEl.textContent = fieldErrors.category_id;
        categorySelect.setAttribute('aria-invalid', 'true');
        categorySelect.setAttribute('aria-describedby', 'product-category-error product-category-status');
      }
      if (fieldErrors.description) {
        if (descriptionErrorEl) descriptionErrorEl.textContent = fieldErrors.description;
        descriptionInput.setAttribute('aria-invalid', 'true');
        descriptionInput.setAttribute('aria-describedby', 'product-description-error');
      }

      // Focus first error field in DOM order
      if (fieldErrors.name) {
        nameInput.focus();
      } else if (fieldErrors.price) {
        priceInput.focus();
      } else if (fieldErrors.category_id) {
        categorySelect.focus();
      } else if (fieldErrors.description) {
        descriptionInput.focus();
      }
      return;
    }

    // 3. Prepare payload snapshot and lock form
    const trimmedName = nameInput.value.trim();
    const trimmedPrice = priceInput.value.trim();
    const trimmedDescription = descriptionInput.value.trim();
    const selectedCategoryId = parseInt(categorySelect.value, 10);

    const payload = {
      name: trimmedName,
      price: trimmedPrice,
      category_id: selectedCategoryId,
      description: trimmedDescription === '' ? null : trimmedDescription,
    };

    setSubmittingState(true);

    try {
      const response = await fetch('/api/products', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      if (response.status === 201) {
        let body = null;
        try {
          body = await response.json();
        } catch (parseErr) {
          // JSON parse failed
        }

        if (body && isValidCreatedProduct(body.data)) {
          // Happy case: 201 with valid contract schema
          showResult('Tạo sản phẩm thành công', 'success');
          nameInput.value = '';
          priceInput.value = '';
          descriptionInput.value = '';
          categorySelect.value = '';
          clearAllErrors();
        } else {
          // 201 but invalid contract schema (FE41)
          showResult('Chưa xác nhận được kết quả tạo sản phẩm', 'error');
        }
      } else if (response.status === 415) {
        // Content-Type rejected
        showResult('Không gửi được dữ liệu tạo sản phẩm', 'error');
      } else if (response.status === 422) {
        // Validation error from server
        let body = null;
        try {
          body = await response.json();
        } catch (parseErr) {
          // JSON parse failed
        }

        const errorsObj = body?.errors;
        const hasErrors = errorsObj && typeof errorsObj === 'object';
        const knownFields = ['name', 'price', 'category_id', 'description'];
        const hasKnownFieldError = hasErrors && knownFields.some(
          (key) => Array.isArray(errorsObj[key]) && errorsObj[key].length > 0
        );

        if (!hasKnownFieldError) {
          showResult('Thông tin chưa hợp lệ, vui lòng kiểm tra lại', 'error');
        } else {
          if (Array.isArray(errorsObj.name) && errorsObj.name.length > 0) {
            fieldErrors.name = errorsObj.name[0];
            if (nameErrorEl) nameErrorEl.textContent = errorsObj.name[0];
            nameInput.setAttribute('aria-invalid', 'true');
            nameInput.setAttribute('aria-describedby', 'product-name-error');
          }
          if (Array.isArray(errorsObj.price) && errorsObj.price.length > 0) {
            fieldErrors.price = errorsObj.price[0];
            if (priceErrorEl) priceErrorEl.textContent = errorsObj.price[0];
            priceInput.setAttribute('aria-invalid', 'true');
            priceInput.setAttribute('aria-describedby', 'product-price-error product-price-help');
          }
          if (Array.isArray(errorsObj.description) && errorsObj.description.length > 0) {
            fieldErrors.description = errorsObj.description[0];
            if (descriptionErrorEl) descriptionErrorEl.textContent = errorsObj.description[0];
            descriptionInput.setAttribute('aria-invalid', 'true');
            descriptionInput.setAttribute('aria-describedby', 'product-description-error');
          }
          if (Array.isArray(errorsObj.category_id) && errorsObj.category_id.length > 0) {
            const catMsg = 'Danh mục không còn hợp lệ, vui lòng chọn lại';
            fieldErrors.category_id = catMsg;
            if (categoryErrorEl) categoryErrorEl.textContent = catMsg;
            categorySelect.setAttribute('aria-invalid', 'true');
            categorySelect.setAttribute('aria-describedby', 'product-category-error product-category-status');
            categorySelect.value = '';
            // Reload categories per DD §5.3 step 5
            loadCategories();
          }
        }
      } else {
        // 500, network/other errors
        showResult('Chưa xác nhận được kết quả tạo sản phẩm', 'error');
      }
    } catch (networkErr) {
      // Network failure, timeout, connection lost
      showResult('Chưa xác nhận được kết quả tạo sản phẩm', 'error');
    } finally {
      setSubmittingState(false);

      // Focus first error field in DOM order after form controls are re-enabled
      if (fieldErrors.name) {
        nameInput.focus();
      } else if (fieldErrors.price) {
        priceInput.focus();
      } else if (fieldErrors.category_id) {
        categorySelect.focus();
      } else if (fieldErrors.description) {
        descriptionInput.focus();
      }
    }
  });

  // Initial load
  loadCategories();
});
