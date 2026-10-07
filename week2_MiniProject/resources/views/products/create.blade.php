<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thêm mặt hàng mới vào danh mục quản lý kho MiniStore.">
    <title>Tạo sản phẩm mới - MiniStore</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              brand: {
                50: '#eef2ff',
                100: '#e0e7ff',
                500: '#6366f1',
                600: '#4f46e5',
                700: '#4338ca',
              }
            },
            fontFamily: {
              sans: ["Inter", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "sans-serif"]
            },
            boxShadow: {
              'card': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
              'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.03)'
            }
          }
        }
      };
    </script>
    <style>
      body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        letter-spacing: -0.015em;
      }
      ::-webkit-scrollbar { width: 5px; height: 5px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
      ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
      }
    </style>

    @vite(['resources/css/products.css', 'resources/js/create_product.js'])
  </head>
  <body class="bg-slate-50 text-slate-900 antialiased h-screen overflow-hidden flex selection:bg-indigo-100 selection:text-indigo-700">
    <a class="skip-link sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:rounded-lg focus:shadow-md" href="#main-content">
      Chuyển đến nội dung chính
    </a>

    <!-- Left Sidebar (Strict 256px SaaS layout) -->
    <aside class="w-64 min-w-[256px] shrink-0 border-r border-slate-200 bg-white h-full flex flex-col justify-between p-4 z-20">
      <div class="flex flex-col gap-6">
        <!-- Brand Logo & Title -->
        <a class="flex items-center gap-3 px-2 py-1.5 no-underline" href="/products">
          <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm ring-1 ring-indigo-500/20">
            <span class="material-symbols-outlined text-[20px]">storefront</span>
          </div>
          <div class="flex flex-col">
            <span class="text-[15px] font-bold text-slate-900 tracking-tight leading-snug">MiniStore</span>
            <span class="text-[10px] font-semibold text-slate-500 tracking-wider uppercase">QUẢN LÝ KHO</span>
          </div>
        </a>

        <!-- Navigation -->
        <nav class="flex flex-col gap-1" aria-label="Menu chức năng">
          <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 text-[13px] font-medium transition-colors" data-path="tat-ca-san-pham" href="/products">
            <span class="material-symbols-outlined text-[19px] text-slate-400">inventory_2</span>
            <span>Tất cả sản phẩm</span>
          </a>
          <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-[13px] transition-colors is-active" data-path="them-san-pham" href="/products/create">
            <span class="material-symbols-outlined text-[19px] text-indigo-600">add_circle</span>
            <span>Thêm sản phẩm</span>
          </a>
        </nav>
      </div>

      <!-- Status indicator badge at bottom -->
      <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
          </span>
          <span class="text-xs font-medium text-slate-600">Hệ thống trực tuyến</span>
        </div>
        <span class="text-[11px] font-mono font-medium text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200 shadow-2xs">v1.0</span>
      </div>
    </aside>

    <!-- Right Layout Area -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
      <!-- Topbar (h-14 px-8) -->
      <header class="h-14 px-8 border-b border-slate-200 bg-white/90 backdrop-blur-sm flex items-center justify-between shrink-0 z-10">
        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-medium text-slate-500">
          <a class="hover:text-slate-900 flex items-center gap-1.5 transition-colors" href="/products">
            <span class="material-symbols-outlined text-[16px] text-slate-400">home</span>
            <span>Trang chủ</span>
          </a>
          <span class="text-slate-300">/</span>
          <a class="text-slate-500 hover:text-slate-900 cursor-pointer" href="/products">Sản phẩm</a>
          <span class="text-slate-300">/</span>
          <span class="text-slate-900 font-semibold">Tạo sản phẩm mới</span>
        </nav>

        <!-- Action Button -->
        <div>
          <a class="inline-flex items-center gap-1.5 h-9 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3.5 rounded-lg font-medium text-xs shadow-sm transition-all active:scale-[0.98]" data-path="tat-ca-san-pham" href="/products">
            <span class="material-symbols-outlined text-[17px] text-slate-400">arrow_back</span>
            <span>Xem danh sách</span>
          </a>
        </div>
      </header>

      <!-- Main Content Area: Modern SaaS Form Layout -->
      <main class="flex-1 bg-slate-50 overflow-y-auto p-6 md:p-8" id="main-content">
        <div class="w-full">
          <!-- Page Header -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
              <h1 class="text-lg font-bold text-slate-900 tracking-tight leading-tight">
                Tạo sản phẩm mới
              </h1>
              <p class="text-xs text-slate-500 mt-0.5">
                Thêm mặt hàng mới vào danh mục quản lý kho hệ thống
              </p>
            </div>
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-700 font-medium text-xs border border-slate-200 shadow-2xs">
                <span class="material-symbols-outlined text-[15px] text-indigo-600">warehouse</span>
                <span>Kho hàng MiniStore</span>
              </span>
            </div>
          </div>

          <!-- Product Form Grid (2-Column SaaS Layout) -->
          <form class="w-full js-product-form" id="create-product-form" novalidate>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
              <!-- Left Column (lg:col-span-2): Main Information -->
              <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Card: Basic Information -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 md:p-7">
                  <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[18px]">edit_note</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-semibold text-slate-900">Thông tin chung</h2>
                      <p class="text-xs text-slate-500">Tên định danh và nội dung mô tả chi tiết của mặt hàng</p>
                    </div>
                  </div>

                  <div class="space-y-5">
                    <!-- 1. Product Name -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-name">
                        Tên sản phẩm <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <input
                          class="w-full h-10 px-3.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all js-product-name"
                          id="product-name"
                          name="name"
                          type="text"
                          autocomplete="off"
                          aria-invalid="false"
                          placeholder="Ví dụ: Bàn phím cơ không dây Logitech MX Mechanical..."
                        >
                      </div>
                      <div
                        class="product-form__error js-name-error text-xs text-red-600 font-medium"
                        id="product-name-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>

                    <!-- 2. Description -->
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-description">
                          Mô tả chi tiết
                        </label>
                        <span class="text-xs text-slate-400">Không bắt buộc</span>
                      </div>
                      <textarea
                        class="w-full p-3.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all resize-y min-h-[130px] js-product-description"
                        id="product-description"
                        name="description"
                        rows="5"
                        aria-invalid="false"
                        placeholder="Nhập mô tả tính năng, quy cách đóng gói, hướng dẫn bảo quản..."
                      ></textarea>
                      <div
                        class="product-form__error js-description-error text-xs text-red-600 font-medium"
                        id="product-description-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Guidelines Card -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-card flex items-start gap-3.5">
                  <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[19px]">lightbulb</span>
                  </div>
                  <div class="text-xs text-slate-600 leading-relaxed">
                    <span class="font-semibold text-slate-900">Quy chuẩn nhập liệu:</span> Tên sản phẩm tối đa 255 ký tự. Giá bán nhập số thực dương (tối thiểu 0 và tối đa 999.999.999 VNĐ). Sử dụng danh mục phù hợp để hỗ trợ thống kê kho hiệu quả.
                  </div>
                </div>
              </div>

              <!-- Right Column (lg:col-span-1): Pricing, Category, Publishing Actions -->
              <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Card: Pricing & Category -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                  <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[18px]">payments</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-semibold text-slate-900">Định giá & Phân loại</h2>
                      <p class="text-xs text-slate-500">Giá niêm yết và phân nhóm</p>
                    </div>
                  </div>

                  <div class="space-y-5">
                    <!-- Price Input -->
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-price">
                          Giá bán (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Đơn vị: VNĐ</span>
                      </div>
                      <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                          <span class="font-mono text-xs text-slate-500 font-semibold">₫</span>
                        </div>
                        <input
                          class="w-full h-10 pl-8 pr-3.5 text-sm font-mono text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all js-product-price"
                          id="product-price"
                          name="price"
                          type="text"
                          inputmode="decimal"
                          autocomplete="off"
                          aria-invalid="false"
                          aria-describedby="product-price-help"
                          placeholder="0.00"
                        >
                      </div>
                      <p class="text-[11px] text-slate-500 flex items-center gap-1 pt-0.5" id="product-price-help">
                        <span class="material-symbols-outlined text-[13px]">info</span>
                        Dùng dấu chấm (.) cho phần thập phân
                      </p>
                      <div
                        class="product-form__error js-price-error text-xs text-red-600 font-medium"
                        id="product-price-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>

                    <!-- Category Select -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-category">
                        Danh mục phân loại <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <select
                          class="w-full h-10 px-3.5 pr-10 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all appearance-none cursor-pointer js-product-category"
                          id="product-category"
                          name="category_id"
                          aria-invalid="false"
                          aria-describedby="product-category-status"
                          disabled
                        >
                          <option value="">Chọn danh mục</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                          <span class="material-symbols-outlined text-[18px]">expand_more</span>
                        </div>
                      </div>
                      <div
                        class="product-form__status js-category-status text-xs text-slate-500"
                        id="product-category-status"
                        role="status"
                        aria-live="polite"
                      >Đang tải danh mục…</div>
                      <div
                        class="product-form__error js-category-error text-xs text-red-600 font-medium"
                        id="product-category-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Card: Publication & Actions -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                  <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-semibold text-slate-900">Xuất bản</h2>
                      <p class="text-xs text-slate-500">Hoàn tất lưu trữ vào kho hàng</p>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
                      <div class="flex items-center justify-between">
                        <span>Mã SKU dự kiến:</span>
                        <span class="font-mono font-medium text-slate-900">Tự động sinh</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span>Trạng thái kho:</span>
                        <span class="font-medium text-emerald-700 flex items-center gap-1">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Khởi tạo
                        </span>
                      </div>
                    </div>

                    <!-- Submit & Cancel Buttons -->
                    <div class="flex flex-col gap-2 pt-2">
                      <button
                        class="w-full h-10 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs inline-flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] js-product-submit disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submit-btn"
                        type="submit"
                        disabled
                      >
                        <span class="material-symbols-outlined text-[17px]">add</span>
                        <span>Tạo sản phẩm</span>
                      </button>
                      <a class="w-full h-9 px-4 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium text-xs inline-flex items-center justify-center transition-all active:scale-[0.98]" href="/products">
                        Hủy bỏ
                      </a>
                    </div>

                    <div
                      class="product-form__result js-form-result"
                      id="product-form-result"
                      role="status"
                      aria-live="polite"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </main>
    </div>
  </body>
</html>
