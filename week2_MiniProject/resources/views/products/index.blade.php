<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản lý kho hàng và danh sách sản phẩm hệ thống MiniStore.">
    <title>Quản lý kho - MiniStore</title>

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
              'card-hover': '0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05)',
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

    @vite(['resources/css/products.css', 'resources/js/list_products.js'])
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
          <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-[13px] transition-colors is-active" data-path="tat-ca-san-pham" href="/products">
            <span class="material-symbols-outlined text-[19px] text-indigo-600">inventory_2</span>
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
          <span class="text-slate-900 font-semibold">Danh sách sản phẩm</span>
        </nav>

        <!-- Action Button -->
        <div>
          <a class="inline-flex items-center gap-1.5 h-9 bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 rounded-lg font-medium text-xs shadow-sm transition-all active:scale-[0.98] js-btn-create" data-path="them-san-pham" href="/products/create">
            <span class="material-symbols-outlined text-[17px]">add</span>
            <span>Thêm mới</span>
          </a>
        </div>
      </header>

      <!-- Main Content Area: Modern SaaS Layout -->
      <main class="flex-1 bg-slate-50 overflow-y-auto p-6 md:p-8" id="main-content">
        <div class="w-full flex flex-col gap-6">

          <!-- Overview KPI Cards Section -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Metric 1: Total Products -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-card flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
              </div>
              <div class="flex flex-col min-w-0">
                <span class="text-xs font-medium text-slate-500">Tổng sản phẩm</span>
                <span class="text-xl font-bold text-slate-900 tracking-tight js-metric-total-products mt-0.5">-- mặt hàng</span>
                <span class="text-[11px] text-slate-400 mt-0.5">Thời gian thực trong kho</span>
              </div>
            </div>

            <!-- Metric 2: Estimated Inventory Value -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-card flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-[20px]">payments</span>
              </div>
              <div class="flex flex-col min-w-0">
                <span class="text-xs font-medium text-slate-500">Tổng giá trị niêm yết</span>
                <span class="text-xl font-bold text-slate-900 tracking-tight font-mono js-metric-total-value mt-0.5">-- VNĐ</span>
                <span class="text-[11px] text-slate-400 mt-0.5">Tổng giá bán các mặt hàng</span>
              </div>
            </div>

            <!-- Metric 3: Category Groups -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-card flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <span class="material-symbols-outlined text-[20px]">category</span>
              </div>
              <div class="flex flex-col min-w-0">
                <span class="text-xs font-medium text-slate-500">Nhóm ngành hàng</span>
                <span class="text-xl font-bold text-slate-900 tracking-tight js-metric-total-categories mt-0.5">-- danh mục</span>
                <span class="text-[11px] text-slate-400 mt-0.5">Phân loại hàng hóa</span>
              </div>
            </div>
          </div>

          <!-- Main Table Card -->
          <div class="w-full bg-white rounded-xl border border-slate-200 shadow-card flex flex-col overflow-hidden">
            <!-- Card Header -->
            <div class="p-5 md:px-6 border-b border-slate-200 bg-white flex items-center justify-between">
              <div>
                <h1 class="text-base font-bold text-slate-900 tracking-tight">Danh mục sản phẩm</h1>
                <p class="text-xs text-slate-500 mt-0.5">Quản lý định giá và phân nhóm các mặt hàng trong kho hệ thống</p>
              </div>
              <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 bg-slate-100 border border-slate-200 px-3 py-1 rounded-full js-header-badge">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Đang tải...
              </span>
            </div>

            <!-- Status Message Box (Loading, Error, Empty) -->
            <div
              class="status-box js-list-status m-6"
              id="list-status"
              role="status"
              aria-live="polite"
            >
              Đang tải dữ liệu sản phẩm…
            </div>

            <!-- Table View: Full width with balanced columns -->
            <div class="overflow-x-auto w-full js-table-wrapper">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold">
                    <th class="py-3 px-6 text-[11px] uppercase tracking-wider w-28">Mã SP</th>
                    <th class="py-3 px-6 text-[11px] uppercase tracking-wider">Tên sản phẩm</th>
                    <th class="py-3 px-6 text-[11px] uppercase tracking-wider text-right w-52">Giá bán</th>
                    <th class="py-3 px-6 text-[11px] uppercase tracking-wider text-center w-48">Danh mục</th>
                    <th class="py-3 px-6 text-[11px] uppercase tracking-wider text-right w-40">Thao tác</th>
                  </tr>
                </thead>
                <tbody class="text-slate-800 divide-y divide-slate-100 js-product-table-body" id="productTableBody">
                  <!-- Rows injected dynamically by list_products.js -->
                </tbody>
              </table>
            </div>

            <!-- Bottom Pagination -->
            <div class="p-3.5 px-6 flex items-center justify-between bg-slate-50/70 border-t border-slate-200 js-pagination">
              <span class="text-xs text-slate-500 js-pagination-info">
                Đang tải danh sách...
              </span>
              <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-1.5">
                  <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-medium transition-colors shadow-2xs active:scale-95 js-btn-prev disabled:opacity-40 disabled:cursor-not-allowed" type="button" disabled>
                    <span class="material-symbols-outlined text-[15px]">chevron_left</span>
                    <span>Trước</span>
                  </button>
                  <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-medium transition-colors shadow-2xs active:scale-95 js-btn-next disabled:opacity-40 disabled:cursor-not-allowed" type="button" disabled>
                    <span>Sau</span>
                    <span class="material-symbols-outlined text-[15px]">chevron_right</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
