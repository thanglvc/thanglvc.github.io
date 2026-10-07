<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chỉnh sửa thông tin định giá và danh mục mặt hàng trong hệ thống MiniStore.">
    <title>Cập nhật sản phẩm #{{ $id }} - MiniStore</title>

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

    @vite(['resources/css/products.css', 'resources/js/update_product.js'])
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
          <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 text-[13px] font-medium transition-colors" data-path="them-san-pham" href="/products/create">
            <span class="material-symbols-outlined text-[19px] text-slate-400">add_circle</span>
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
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden" data-product-id="{{ $id }}">
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
          <span class="text-slate-900 font-semibold">Cập nhật SP-{{ $id }}</span>
        </nav>

        <!-- Action Button -->
        <div>
          <a class="inline-flex items-center gap-1.5 h-9 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3.5 rounded-lg font-medium text-xs shadow-sm transition-all active:scale-[0.98] js-link-back" data-path="tat-ca-san-pham" href="/products">
            <span class="material-symbols-outlined text-[17px] text-slate-400">arrow_back</span>
            <span>Quay lại danh sách</span>
          </a>
        </div>
      </header>

      <!-- Main Content Area: Modern SaaS Form Layout -->
      <main class="flex-1 bg-slate-50 overflow-y-auto p-6 md:p-8" id="main-content">
        <div class="w-full">
          <!-- Page Header -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
              <div class="flex items-center gap-2.5">
                <h1 class="text-lg font-bold text-slate-900 tracking-tight leading-tight">
                  Cập nhật sản phẩm
                </h1>
                <span class="font-mono text-xs px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                  SP-{{ $id }}
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5">
                Chỉnh sửa thông tin định giá và danh mục mặt hàng
              </p>
            </div>
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-700 font-medium text-xs border border-slate-200 shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Đang kinh doanh
              </span>
            </div>
          </div>

          <!-- Status Alert Box -->
          <div
            class="status-box js-edit-status w-full mb-6"
            id="edit-status"
            role="status"
            aria-live="polite"
          >
            Đang tải dữ liệu sản phẩm…
          </div>

          <!-- Form Grid (2-Column SaaS Layout) -->
          <form class="w-full js-product-form" id="update-product-form" novalidate>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
              <!-- Left Column (lg:col-span-2): Main Information -->
              <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Card: Basic Information -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 md:p-7">
                  <div class="flex items-center gap-3 pb-5 border-b border-slate-100 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[19px]">edit_note</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-bold text-slate-900">Thông tin chung</h2>
                      <p class="text-xs text-slate-500">Tên định danh và nội dung mô tả chi tiết của mặt hàng</p>
                    </div>
                  </div>

                  <div class="space-y-5">
                    <!-- 1. Name -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-name">
                        Tên sản phẩm <span class="text-rose-500">*</span>
                      </label>
                      <input
                        class="w-full h-10 px-3.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm transition-all js-product-name"
                        id="product-name"
                        name="name"
                        type="text"
                        autocomplete="off"
                        aria-invalid="false"
                        placeholder="Nhập tên sản phẩm..."
                      >
                      <div
                        class="product-form__error js-name-error text-xs text-rose-600 font-medium"
                        id="product-name-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>

                    <!-- 2. Description -->
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-description">
                          Mô tả sản phẩm
                        </label>
                        <span class="text-xs text-slate-400 font-normal">Không bắt buộc</span>
                      </div>
                      <textarea
                        class="w-full p-3.5 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg resize-y min-h-[140px] transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm js-product-description"
                        id="product-description"
                        name="description"
                        rows="5"
                        aria-invalid="false"
                        placeholder="Nhập mô tả chi tiết sản phẩm..."
                      ></textarea>
                      <div
                        class="product-form__error js-description-error text-xs text-rose-600 font-medium"
                        id="product-description-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Sync Info Banner -->
                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-4.5 flex items-start gap-3.5">
                  <div class="w-8 h-8 rounded-lg bg-indigo-100/80 text-indigo-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[19px]">verified_user</span>
                  </div>
                  <div class="text-xs text-slate-600 leading-relaxed">
                    <span class="font-semibold text-slate-900">Đồng bộ an toàn:</span> Dữ liệu kho hàng được cập nhật trực tiếp vào cơ sở dữ liệu thời gian thực sau khi lưu thay đổi. Mọi chỉnh sửa sẽ lập tức phản ánh trong bảng danh mục.
                  </div>
                </div>
              </div>

              <!-- Right Column (lg:col-span-1): Pricing, Category, Action Controls -->
              <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Card: Pricing & Category -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                  <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[19px]">payments</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-bold text-slate-900">Định giá & Phân loại</h2>
                      <p class="text-xs text-slate-500">Giá bán niêm yết và ngành hàng</p>
                    </div>
                  </div>

                  <div class="space-y-5">
                    <!-- Price -->
                    <div class="space-y-1.5">
                      <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-price">
                          Giá bán (VNĐ) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-mono">VNĐ</span>
                      </div>
                      <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                          <span class="font-mono text-xs text-slate-400 font-semibold">₫</span>
                        </div>
                        <input
                          class="w-full h-10 pl-8 pr-14 text-sm font-mono text-slate-900 bg-white border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm transition-all js-product-price"
                          id="product-price"
                          name="price"
                          type="text"
                          inputmode="decimal"
                          autocomplete="off"
                          aria-invalid="false"
                          aria-describedby="product-price-help"
                          placeholder="0.00"
                        >
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11px] font-mono text-slate-400 pointer-events-none uppercase">VNĐ</span>
                      </div>
                      <p class="text-[11px] text-slate-500 flex items-center gap-1 pt-0.5" id="product-price-help">
                        <span class="material-symbols-outlined text-[14px] text-slate-400">info</span>
                        Dùng dấu chấm (.) cho phần thập phân
                      </p>
                      <div
                        class="product-form__error js-price-error text-xs text-rose-600 font-medium"
                        id="product-price-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>

                    <!-- Category -->
                    <div class="space-y-1.5">
                      <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700" for="product-category">
                        Danh mục phân loại <span class="text-rose-500">*</span>
                      </label>
                      <div class="relative">
                        <select
                          class="w-full h-10 px-3.5 pr-10 text-sm text-slate-900 bg-white border border-slate-300 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm transition-all js-product-category"
                          id="product-category"
                          name="category_id"
                          aria-invalid="false"
                          aria-describedby="product-category-status"
                          disabled
                        >
                          <option value="">Chọn danh mục</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none material-symbols-outlined text-[20px] text-slate-400">
                          expand_more
                        </span>
                      </div>
                      <div
                        class="product-form__status js-category-status text-xs text-slate-500"
                        id="product-category-status"
                        role="status"
                        aria-live="polite"
                      >Đang tải danh mục…</div>
                      <div
                        class="product-form__error js-category-error text-xs text-rose-600 font-medium"
                        id="product-category-error"
                        role="alert"
                        aria-live="polite"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Card: Save Changes & Actions -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                  <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                      <span class="material-symbols-outlined text-[19px]">save</span>
                    </div>
                    <div>
                      <h2 class="text-sm font-bold text-slate-900">Lưu dữ liệu</h2>
                      <p class="text-xs text-slate-500">Cập nhật hồ sơ mặt hàng</p>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                      <div class="flex items-center justify-between">
                        <span>Mã sản phẩm:</span>
                        <span class="font-mono font-semibold text-slate-900">SP-{{ $id }}</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span>Trạng thái:</span>
                        <span class="font-medium text-emerald-700 flex items-center gap-1">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang hoạt động
                        </span>
                      </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2 pt-2">
                      <button
                        class="w-full h-10 px-4 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm inline-flex items-center justify-center gap-2 transition-all active:scale-[0.98] js-product-submit disabled:opacity-40 disabled:cursor-not-allowed"
                        id="btn-submit"
                        type="submit"
                        disabled
                      >
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Lưu thay đổi</span>
                      </button>
                      <a class="w-full h-9 px-4 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 inline-flex items-center justify-center transition-colors" href="/products">
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
