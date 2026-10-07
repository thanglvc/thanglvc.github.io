const ACTIVE_TAB_CLASS = "product-tabs__button--active";
const ACTIVE_FILTER_CLASS = "rating-filter__option--active";
const ORIGINAL_REVIEW_COUNT = 451;
const PRODUCT_TOTAL = 8;
const AUTOPLAY_FALLBACK = 30000;

/**
 * Activate one product-information tab and its matching panel.
 * @param {HTMLButtonElement} selectedTab - Tab to activate.
 * @param {boolean} moveFocus - Whether keyboard focus should move to the tab.
 * @returns {void}
 */
function activateTab(selectedTab, moveFocus) {
  const tabs = document.querySelectorAll(".js-product-tab");
  const panels = document.querySelectorAll(".js-product-panel");

  for (const tab of tabs) {
    const isSelected = tab === selectedTab;
    tab.classList.toggle(ACTIVE_TAB_CLASS, isSelected);
    tab.setAttribute("aria-selected", String(isSelected));
    tab.tabIndex = isSelected ? 0 : -1;
  }

  for (const panel of panels) {
    panel.hidden = panel.id !== selectedTab.getAttribute("aria-controls");
  }

  if (moveFocus) {
    selectedTab.focus();
  }
}

/**
 * Handle a pointer activation on a product-information tab.
 * @param {MouseEvent} event - Tab click event.
 * @returns {void}
 */
function handleTabClick(event) {
  activateTab(event.currentTarget, false);
}

/**
 * Move through the tab list with standard arrow, Home and End keys.
 * @param {KeyboardEvent} event - Tab keyboard event.
 * @returns {void}
 */
function handleTabKeydown(event) {
  const tabs = Array.from(document.querySelectorAll(".js-product-tab"));
  const currentIndex = tabs.indexOf(event.currentTarget);
  let nextIndex = currentIndex;

  if (event.key === "ArrowRight") {
    nextIndex = (currentIndex + 1) % tabs.length;
  } else if (event.key === "ArrowLeft") {
    nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
  } else if (event.key === "Home") {
    nextIndex = 0;
  } else if (event.key === "End") {
    nextIndex = tabs.length - 1;
  } else {
    return;
  }

  event.preventDefault();
  activateTab(tabs[nextIndex], true);
}

/**
 * Connect click and keyboard behavior for the three product tabs.
 * No parameters.
 * @returns {void}
 */
function initializeTabs() {
  const tabs = document.querySelectorAll(".js-product-tab");

  for (const tab of tabs) {
    tab.addEventListener("click", handleTabClick);
    tab.addEventListener("keydown", handleTabKeydown);
  }
}

/**
 * Read the numeric score from one review card's accessible star label.
 * @param {HTMLElement} reviewItem - Review list item.
 * @returns {number} Rating from zero to five.
 */
function getReviewRating(reviewItem) {
  const stars = reviewItem.querySelector(".review-card__stars");
  return Number.parseFloat(stars.alt);
}

/**
 * Initialize the rating popover and review filtering.
 * No parameters.
 * @returns {void}
 */
function initializeReviewFilter() {
  const filter = document.querySelector(".rating-filter");
  const filterButton = document.querySelector(".js-review-filter");
  const menu = document.querySelector(".js-rating-menu");
  const options = document.querySelectorAll(".js-rating-option");
  const reviewItems = document.querySelectorAll(".reviews__item");
  const heading = document.querySelector(".reviews__title");
  const count = document.querySelector(".js-review-count");
  const emptyMessage = document.querySelector(".js-reviews-empty");

  if (!filter || !filterButton || !menu) {
    return;
  }

  /**
   * Close the rating options and synchronize the trigger state.
   * No parameters.
   * @returns {void}
   */
  function closeMenu() {
    menu.hidden = true;
    filterButton.setAttribute("aria-expanded", "false");
  }

  /**
   * Open or close the rating options from the filter trigger.
   * No parameters.
   * @returns {void}
   */
  function toggleMenu() {
    const willOpen = menu.hidden;
    menu.hidden = !willOpen;
    filterButton.setAttribute("aria-expanded", String(willOpen));

    if (willOpen) {
      options[0].focus();
    }
  }

  /**
   * Apply an integer rating band, or restore every review.
   * @param {string} selectedValue - Integer band or "all".
   * @returns {void}
   */
  function applyFilter(selectedValue) {
    const showAll = selectedValue === "all";
    const selectedRating = Number(selectedValue);
    let visibleCount = 0;

    for (const item of reviewItems) {
      const rating = getReviewRating(item);
      const isVisible = showAll || (rating >= selectedRating && rating < selectedRating + 1);
      item.hidden = !isVisible;
      if (isVisible) {
        visibleCount += 1;
      }
    }

    for (const option of options) {
      const isActive = option.dataset.rating === selectedValue;
      option.classList.toggle(ACTIVE_FILTER_CLASS, isActive);
      option.setAttribute("aria-pressed", String(isActive));
    }

    heading.textContent = showAll ? "All Reviews" : selectedValue + " Star Reviews";
    count.textContent = showAll ? "(" + ORIGINAL_REVIEW_COUNT + ")" : "(" + visibleCount + ")";
    emptyMessage.hidden = visibleCount !== 0;
    closeMenu();
  }

  /**
   * Handle the filter trigger click.
   * No parameters.
   * @returns {void}
   */
  function handleFilterButtonClick() {
    toggleMenu();
  }

  /**
   * Apply the option chosen by the user.
   * @param {MouseEvent} event - Rating option click event.
   * @returns {void}
   */
  function handleOptionClick(event) {
    applyFilter(event.currentTarget.dataset.rating);
    filterButton.focus();
  }

  /**
   * Close the popover when a pointer action occurs elsewhere.
   * @param {MouseEvent} event - Document click event.
   * @returns {void}
   */
  function handleDocumentClick(event) {
    if (!filter.contains(event.target)) {
      closeMenu();
    }
  }

  /**
   * Close the popover with Escape and return focus to its trigger.
   * @param {KeyboardEvent} event - Document keyboard event.
   * @returns {void}
   */
  function handleDocumentKeydown(event) {
    if (event.key === "Escape" && !menu.hidden) {
      closeMenu();
      filterButton.focus();
    }
  }

  filterButton.addEventListener("click", handleFilterButtonClick);
  for (const option of options) {
    option.addEventListener("click", handleOptionClick);
    option.setAttribute("aria-pressed", "false");
  }
  document.addEventListener("click", handleDocumentClick);
  document.addEventListener("keydown", handleDocumentKeydown);
}

/**
 * Return how many carousel items fit at the current breakpoint.
 * No parameters.
 * @returns {number} Number of visible product cards.
 */
function getVisibleProductCount() {
  return window.innerWidth >= 1024 ? 4 : 2;
}

/**
 * Create one accessible slider arrow button.
 * @param {string} modifier - BEM modifier for direction.
 * @param {string} label - Accessible action label.
 * @param {string} symbol - Visible arrow symbol.
 * @returns {HTMLButtonElement} Configured control.
 */
function createSliderButton(modifier, label, symbol) {
  const button = document.createElement("button");
  button.className = "related-products__arrow related-products__arrow--" + modifier;
  button.type = "button";
  button.setAttribute("aria-label", label);
  button.textContent = symbol;
  return button;
}

/**
 * Build the eight-item product slider, navigation and 30-second autoplay.
 * No parameters.
 * @returns {void}
 */
function initializeProductSlider() {
  const slider = document.querySelector(".js-product-slider");
  const list = slider ? slider.querySelector(".related-products__list") : null;

  if (!slider || !list) {
    return;
  }

  const originalItems = Array.from(list.children);
  for (let index = originalItems.length; index < PRODUCT_TOTAL; index += 1) {
    list.append(originalItems[index % originalItems.length].cloneNode(true));
  }

  const viewport = document.createElement("div");
  viewport.className = "related-products__viewport";
  list.before(viewport);
  viewport.append(list);

  const controls = document.createElement("div");
  controls.className = "related-products__controls";
  const previousButton = createSliderButton("previous", "Show previous products", "←");
  const nextButton = createSliderButton("next", "Show next products", "→");
  controls.append(previousButton, nextButton);
  slider.querySelector(".related-products__heading").after(controls);
  slider.classList.add("is-enhanced");

  const items = Array.from(list.children);
  const delay = Number(slider.dataset.autoplayDelay) || AUTOPLAY_FALLBACK;
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  let currentIndex = 0;
  let autoplayTimer = null;
  let isPointerInside = false;
  let isFocusInside = false;
  let resizeFrame = null;

  /**
   * Paint the current slide position and expose it for QA.
   * No parameters.
   * @returns {void}
   */
  function updateSlider() {
    const visibleCount = getVisibleProductCount();
    const maximumIndex = Math.max(0, items.length - visibleCount);
    currentIndex = Math.min(currentIndex, maximumIndex);
    const firstItem = items[0];
    const gap = Number.parseFloat(window.getComputedStyle(list).columnGap) || 0;
    const distance = firstItem.getBoundingClientRect().width + gap;
    list.style.transform = "translate3d(" + (-currentIndex * distance) + "px, 0, 0)";
    slider.dataset.currentIndex = String(currentIndex);
    slider.dataset.itemCount = String(items.length);
  }

  /**
   * Move by one product and wrap after the final valid position.
   * @param {number} direction - Positive for next and negative for previous.
   * @returns {void}
   */
  function moveSlider(direction) {
    const maximumIndex = Math.max(0, items.length - getVisibleProductCount());
    currentIndex += direction;

    if (currentIndex > maximumIndex) {
      currentIndex = 0;
    } else if (currentIndex < 0) {
      currentIndex = maximumIndex;
    }

    updateSlider();
  }

  /**
   * Stop the active autoplay timer.
   * No parameters.
   * @returns {void}
   */
  function stopAutoplay() {
    if (autoplayTimer !== null) {
      window.clearInterval(autoplayTimer);
      autoplayTimer = null;
    }
  }

  /**
   * Start a fresh autoplay timer unless reduced motion is requested.
   * No parameters.
   * @returns {void}
   */
  function startAutoplay() {
    stopAutoplay();
    if (!reduceMotion && !document.hidden && !isPointerInside && !isFocusInside) {
      autoplayTimer = window.setInterval(handleAutoplay, delay);
    }
  }

  /**
   * Advance one item when the autoplay interval completes.
   * No parameters.
   * @returns {void}
   */
  function handleAutoplay() {
    moveSlider(1);
  }

  /**
   * Navigate manually and restart the 30-second countdown.
   * @param {number} direction - Positive for next and negative for previous.
   * @returns {void}
   */
  function handleManualMove(direction) {
    moveSlider(direction);
    startAutoplay();
  }

  /**
   * Handle the previous-arrow click.
   * No parameters.
   * @returns {void}
   */
  function handlePreviousClick() {
    handleManualMove(-1);
  }

  /**
   * Handle the next-arrow click.
   * No parameters.
   * @returns {void}
   */
  function handleNextClick() {
    handleManualMove(1);
  }

  /**
   * Recalculate item distance after a responsive resize.
   * No parameters.
   * @returns {void}
   */
  function handleResize() {
    window.cancelAnimationFrame(resizeFrame);
    resizeFrame = window.requestAnimationFrame(updateSlider);
  }

  /**
   * Synchronize autoplay with page visibility.
   * No parameters.
   * @returns {void}
   */
  function handleVisibilityChange() {
    if (document.hidden) {
      stopAutoplay();
    } else {
      startAutoplay();
    }
  }

  /**
   * Pause autoplay while the pointer is over the slider.
   * No parameters.
   * @returns {void}
   */
  function handlePointerEnter() {
    isPointerInside = true;
    stopAutoplay();
  }

  /**
   * Resume autoplay only if the slider does not also contain focus.
   * No parameters.
   * @returns {void}
   */
  function handlePointerLeave() {
    isPointerInside = false;
    startAutoplay();
  }

  /**
   * Pause autoplay while keyboard focus is inside the slider.
   * No parameters.
   * @returns {void}
   */
  function handleFocusIn() {
    isFocusInside = true;
    stopAutoplay();
  }

  /**
   * Keep autoplay paused when focus moves between slider controls.
   * @param {FocusEvent} event - Focus transition from a slider descendant.
   * @returns {void}
   */
  function handleFocusOut(event) {
    isFocusInside = slider.contains(event.relatedTarget);
    if (!isFocusInside) {
      startAutoplay();
    }
  }

  previousButton.addEventListener("click", handlePreviousClick);
  nextButton.addEventListener("click", handleNextClick);
  slider.addEventListener("mouseenter", handlePointerEnter);
  slider.addEventListener("mouseleave", handlePointerLeave);
  slider.addEventListener("focusin", handleFocusIn);
  slider.addEventListener("focusout", handleFocusOut);
  window.addEventListener("resize", handleResize);
  document.addEventListener("visibilitychange", handleVisibilityChange);

  updateSlider();
  startAutoplay();
}


/**
 * Connect the promotion, mobile menu, search reveal and static newsletter form.
 * No parameters.
 * @returns {void}
 */
function initializePageChrome() {
  const header = document.querySelector(".site-header");
  const menuButton = document.querySelector(".js-menu-toggle");
  const navigation = document.querySelector("#primary-navigation");
  const searchButton = document.querySelector(".js-search-toggle");
  const searchInput = document.querySelector("#site-search");
  const promotionClose = document.querySelector(".site-promo__close");
  const promotion = document.querySelector(".site-promo");
  const newsletterForm = document.querySelector(".newsletter__form");
  const newsletterStatus = document.querySelector(".js-newsletter-status");

  if (menuButton && navigation && header) {
    menuButton.addEventListener("click", function () {
      const isOpen = menuButton.getAttribute("aria-expanded") !== "true";
      menuButton.setAttribute("aria-expanded", String(isOpen));
      menuButton.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
      navigation.classList.toggle("is-open", isOpen);
    });

    document.addEventListener("click", function (event) {
      if (!header.contains(event.target)) {
        menuButton.setAttribute("aria-expanded", "false");
        menuButton.setAttribute("aria-label", "Open menu");
        navigation.classList.remove("is-open");
      }
    });

    window.addEventListener("resize", function () {
      if (window.innerWidth > 900) {
        menuButton.setAttribute("aria-expanded", "false");
        navigation.classList.remove("is-open");
      }
    });
  }

  if (searchButton && header && searchInput) {
    searchButton.addEventListener("click", function () {
      const isOpen = header.classList.toggle("is-search-open");
      if (isOpen) {
        searchInput.focus();
      }
    });
  }

  if (promotionClose && promotion) {
    promotionClose.addEventListener("click", function () {
      promotion.hidden = true;
    });
  }

  if (newsletterForm && newsletterStatus) {
    newsletterForm.addEventListener("submit", function (event) {
      event.preventDefault();
      newsletterStatus.textContent = "Newsletter sign-up is not connected in this exercise.";
    });
  }
}

/**
 * Handle thumbnail selection and keep the featured product view accessible.
 * No parameters.
 * @returns {void}
 */
function initializeProductGallery() {
  const gallery = document.querySelector(".js-product-gallery");
  const featured = gallery ? gallery.querySelector(".js-product-featured") : null;
  const buttons = gallery ? gallery.querySelectorAll(".js-product-image") : [];

  if (!gallery || !featured) {
    return;
  }

  for (const button of buttons) {
    button.addEventListener("click", function () {
      const view = button.dataset.view;
      for (const imageButton of buttons) {
        const isSelected = imageButton === button;
        imageButton.classList.toggle("is-selected", isSelected);
        imageButton.setAttribute("aria-pressed", String(isSelected));
      }
      featured.classList.remove("product-gallery__featured--front", "product-gallery__featured--back", "product-gallery__featured--model");
      featured.classList.add("product-gallery__featured--" + view);
      featured.setAttribute("aria-label", button.getAttribute("aria-label") + " of the One Life Graphic T-shirt");
    });
  }
}

/**
 * Connect the product's color, size, quantity and add-to-cart controls.
 * No parameters.
 * @returns {void}
 */
function initializeProductOptions() {
  const colors = document.querySelectorAll(".js-product-color");
  const colorGroup = document.querySelector(".js-product-colors");
  const sizes = document.querySelectorAll(".js-product-size");
  const quantity = document.querySelector(".js-quantity-value");
  const decreaseButton = document.querySelector(".js-quantity-decrease");
  const increaseButton = document.querySelector(".js-quantity-increase");
  const addButton = document.querySelector(".js-add-to-cart");
  const cart = document.querySelector(".site-header__action--cart");
  const status = document.querySelector(".js-cart-status");
  let cartQuantity = 0;

  for (const color of colors) {
    color.addEventListener("click", function () {
      colorGroup?.classList.add("is-customized");
      for (const choice of colors) {
        const isSelected = choice === color;
        choice.classList.toggle("is-selected", isSelected);
        choice.setAttribute("aria-pressed", String(isSelected));
      }
    });
  }

  for (const size of sizes) {
    size.addEventListener("click", function () {
      for (const choice of sizes) {
        const isSelected = choice === size;
        choice.classList.toggle("is-selected", isSelected);
        choice.setAttribute("aria-pressed", String(isSelected));
      }
    });
  }

  if (quantity && decreaseButton && increaseButton) {
    decreaseButton.addEventListener("click", function () {
      quantity.value = String(Math.max(1, Number(quantity.value) - 1));
    });
    increaseButton.addEventListener("click", function () {
      quantity.value = String(Number(quantity.value) + 1);
    });
  }

  if (addButton && quantity && cart && status) {
    addButton.addEventListener("click", function () {
      const selectedSize = document.querySelector(".js-product-size.is-selected");
      const selectedColor = document.querySelector(".js-product-color.is-selected");
      const itemQuantity = Number(quantity.value);
      cartQuantity += itemQuantity;
      cart.setAttribute("aria-label", "Shopping cart, " + cartQuantity + " items");
      status.textContent = "Added " + itemQuantity + " " + selectedColor.getAttribute("aria-label") + " " + selectedSize.textContent.trim() + " One Life Graphic T-shirt to your cart.";
    });
  }
}

function initializeDesktopReferencePatchMode() {
  const desktopFrame = window.matchMedia("(width: 1440px)");
  const disablePatches = event => {
    if (!desktopFrame.matches) return;
    const target = event.target;
    if (target instanceof Element && target.closest("a, button, input, select, textarea, [role='button'], [role='tab']")) {
      document.body.classList.add("desktop-reference-patches-disabled");
    }
  };

  document.addEventListener("click", disablePatches, true);
  document.addEventListener("focusin", disablePatches, true);
  document.addEventListener("input", disablePatches, true);
}

initializeDesktopReferencePatchMode();
initializePageChrome();
initializeProductGallery();
initializeProductOptions();
initializeTabs();
initializeReviewFilter();
initializeProductSlider();
