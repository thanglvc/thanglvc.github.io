<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chi tiết sản phẩm trong hệ thống quản lý kho MiniStore.">
    <title>Chi tiết sản phẩm #{{ $id }} - MiniStore</title>

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

    @vite(['resources/css/products.css', 'resources/js/view_product.js'])
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
          <span class="text-slate-900 font-semibold">Chi tiết SP-{{ $id }}</span>
        </nav>

        <!-- Action Button: Back to List -->
        <div>
          <a class="inline-flex items-center gap-1.5 h-9 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-3.5 rounded-lg font-medium text-xs shadow-sm transition-all active:scale-[0.98] js-link-back" data-path="tat-ca-san-pham" href="/products">
            <span class="material-symbols-outlined text-[17px] text-slate-400">arrow_back</span>
            <span>Quay lại danh sách</span>
          </a>
        </div>
      </header>

      <!-- Main Content Area: Modern SaaS Details Layout -->
      <main class="flex-1 bg-slate-50 overflow-y-auto p-6 md:p-8" id="main-content">
        <!-- Status Box for loading/error -->
        <div
          class="status-box js-detail-status w-full mb-6"
          id="detail-status"
          role="status"
          aria-live="polite"
        >
          Đang tải sản phẩm…
        </div>

        <!-- Full-Width Container (cardEl) -->
        <div class="w-full flex flex-col gap-6 js-detail-card">
          <!-- Page Header & Actions Bar -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
            <div class="flex items-center gap-2.5">
              <h1 class="text-lg font-bold text-slate-900 tracking-tight leading-tight">Chi tiết sản phẩm</h1>
              <span class="font-mono text-xs px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200 js-detail-id">
                SP-{{ $id }}
              </span>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-slate-700 font-medium text-xs border border-slate-200 shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Đang kinh doanh
              </span>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-2">
              <button class="h-9 px-3.5 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 text-xs font-medium inline-flex items-center gap-1.5 border border-slate-200 bg-white shadow-sm transition-all active:scale-[0.98] js-btn-delete" id="btn-delete" type="button">
                <span class="material-symbols-outlined text-[17px]">delete</span>
                <span>Xóa sản phẩm</span>
              </button>
              <a class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm transition-all active:scale-[0.98] js-btn-edit" href="/products/{{ $id }}/edit">
                <span class="material-symbols-outlined text-[17px]">edit</span>
                <span>Chỉnh sửa</span>
              </a>
            </div>
          </div>

          <!-- 2-Column Grid Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Left Column (lg:col-span-2): Main Showcase & Description -->
            <div class="lg:col-span-2 flex flex-col gap-6">
              <!-- Banner Highlight: Name & Price Hero -->
              <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 md:p-7 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex flex-col gap-1.5 min-w-0">
                  <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Tên mặt hàng</span>
                  <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight break-words js-detail-name">
                    Đang tải…
                  </h2>
                </div>
                <div class="flex flex-col sm:items-end gap-1 shrink-0 p-4 px-6 rounded-lg bg-slate-50 border border-slate-200/80">
                  <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Giá bán niêm yết</span>
                  <span class="text-2xl md:text-3xl font-bold text-indigo-600 tracking-tight tabular-nums js-detail-price">
                    —
                  </span>
                </div>
              </div>

              <!-- Card: Detailed Description -->
              <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 md:p-7">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[19px]">description</span>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">Mô tả sản phẩm</h3>
                    <p class="text-xs text-slate-500">Thông tin quy cách, công năng và hướng dẫn sử dụng</p>
                  </div>
                </div>

                <div class="text-sm text-slate-700 leading-relaxed bg-slate-50 border border-slate-200/80 p-5 rounded-lg whitespace-pre-line js-detail-description">
                  Đang tải mô tả…
                </div>
              </div>

              <!-- Card: Warehouse & Quality Assurance Note -->
              <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-4.5 flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100/80 text-indigo-600 flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[19px]">verified</span>
                </div>
                <div class="text-xs text-slate-600 leading-relaxed">
                  <span class="font-semibold text-slate-900">Quy trình kho:</span> Hàng hóa đã kiểm tra tình trạng niêm phong và sẵn sàng xuất kho. Hồ sơ được lưu trữ và đồng bộ tự động với cơ sở dữ liệu trung tâm.
                </div>
              </div>
            </div>

            <!-- Right Column (lg:col-span-1): Meta Specs & Timestamps -->
            <div class="lg:col-span-1 flex flex-col gap-6">
              <!-- Card: Category & SKU Specs -->
              <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[19px]">category</span>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">Thông số phân loại</h3>
                    <p class="text-xs text-slate-500">Ngành hàng và mã quản lý</p>
                  </div>
                </div>

                <div class="space-y-3">
                  <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 flex flex-col gap-1.5">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Danh mục sản phẩm</span>
                    <div>
                      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 inline-block"></span>
                        <span class="js-detail-category">—</span>
                      </span>
                    </div>
                  </div>

                  <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Mã SKU nội bộ</span>
                    <span class="text-xs font-mono font-semibold text-slate-900">SP-{{ $id }}</span>
                  </div>

                  <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">Vị trí lưu kho</span>
                    <span class="text-xs font-medium text-slate-600">Khu vực A-01</span>
                  </div>
                </div>
              </div>

              <!-- Card: Audit Log & Timestamps -->
              <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[19px]">update</span>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-900">Lịch sử hồ sơ</h3>
                    <p class="text-xs text-slate-500">Nhật ký cập nhật hệ thống</p>
                  </div>
                </div>

                <div class="space-y-3">
                  <!-- Created Timestamp -->
                  <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 shrink-0 shadow-2xs">
                      <span class="material-symbols-outlined text-[17px]">calendar_today</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                      <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Ngày tạo</span>
                      <span class="text-xs font-mono text-slate-800 font-medium truncate js-detail-created-at">—</span>
                    </div>
                  </div>

                  <!-- Updated Timestamp -->
                  <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-200/80">
                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 shrink-0 shadow-2xs">
                      <span class="material-symbols-outlined text-[17px]">history</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                      <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Cập nhật lần cuối</span>
                      <span class="text-xs font-mono text-slate-800 font-medium truncate js-detail-updated-at">—</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
