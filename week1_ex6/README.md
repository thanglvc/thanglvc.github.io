# Tuần 1: Ngày 4 — Bài tập 2 / Trang thẩm mỹ - clinic Nhật (nâng_cao_acti-shiga.jp+.psd)

HTML5 Semantic + SCSS/BEM + Gulp. Triển khai giao diện website thẩm mỹ / salon làm đẹp & mọc tóc Feminine (tỉnh Shiga, Nhật Bản) theo thiết kế Photoshop gốc `reponsive/Bai Tap/nâng_cao_acti-shiga.jp+.psd` (canvas 1300×2216).

## Cách chạy bài

Trong thư mục `week1_ex6`, sử dụng Node 24:

```sh
npm run build
npm run dev
```

Mở **http://127.0.0.1:4176**. Gulp tự động compile lại khi có thay đổi trong `scss/`; refresh trình duyệt để kiểm tra.
CSS đã được biên dịch sẵn tại `css/style.css`, có thể chạy trực tiếp qua Live Server hoặc GitHub Pages.

Chạy bộ kiểm thử tự động (Console, 404, Tràn ngang, Screenshot, Overlay 50%, Diff):

```sh
npm run build
npm run check:visual
python3 tools/compare.py
npx --yes html-validate@11.16.0 index.html
```

## Cấu trúc thư mục

- `index.html`: Cấu trúc HTML5 semantic (`header`, `nav`, `main`, `aside`, `footer`, `section`, `article`, `address`); toàn bộ chữ tiếng Nhật được trích xuất trực tiếp từ layer text của PSD gốc và giữ dạng HTML text.
- `scss/`: Tổ chức BEM theo module (`_variables.scss`, `_base.scss`, `_header.scss`, `_nav.scss`, `_hero.scss`, `_layout.scss`, `_content.scss`, `_sidebar.scss`, `_footer.scss`, `style.scss` dùng `@use`).
- `css/style.css`: File CSS biên dịch từ SCSS, không chỉnh sửa trực tiếp.
- `images/`: Toàn bộ asset được xuất độc lập từ PSD gốc theo đúng quy tắc đặt tên tiền tố trong `AGENTS.md`.
- `tools/`: `capture.mjs` (chạy Chrome DevTools Protocol kiểm tra console/404/tràn ngang và chụp full-page 10 viewport) và `compare.py` (tính sai số pixel, sinh overlay 50% và ảnh difference).
- `screenshots/`: Ảnh reference từ PSD, screenshot trình duyệt 1300px/390px/375px, overlay 50%, diff và báo cáo kiểm tra.

## Danh mục Asset (Quy chuẩn AGENTS.md)

| Tên file | Loại | Nguồn Layer PSD | Kích thước | Mô tả |
| --- | --- | --- | --- | --- |
| `logo_header.png` | Logo | header / ロゴ | 199×86 | Logo salon Feminine đầu trang (nền trong suốt) |
| `icn_phone.png` | Icon | header / シェイプ 2 | 28×25 | Biểu tượng điện thoại hotline |
| `pic_hero_main.png` | Ảnh | main_img_b | 963×383 | Ảnh banner chính ghép salon, chân dung và hoa văn (không nướng chữ) |
| `pic_concept.png` | Ảnh | contents / DSCF2010 | 263×191 | Ảnh phòng trị liệu salon có khung vàng |
| `pic_menu_facial.png` | Ảnh | contents / gf1420103733l | 223×162 | Ảnh card dịch vụ Facial |
| `pic_menu_diet.png` | Ảnh | contents / gf1140323447l | 223×162 | Ảnh card dịch vụ Giảm béo / Diet |
| `pic_menu_hair.png` | Ảnh | contents / af9920067738l | 222×161 | Ảnh card dịch vụ Mọc tóc / Hair growth |
| `pic_side_depilation.png`| Ảnh | side / af9920077885l | 233×165 | Ảnh banner triệt lông bên sidebar |
| `bnr_side_campaign.png` | Banner | side / gf2160250649l のコピー | 233×85 | Banner chiến dịch khuyến mãi |
| `bnr_side_recruit.png` | Banner | side / af9920075727l | 233×135 | Banner tuyển dụng chuyên viên thẩm mỹ |
| `fig_side_map.png` | Hình | side / Google マップ + 長方形 4 | 191×133 | Bản đồ hướng dẫn đường đi tới salon |
| `logo_footer.png` | Logo | footer / ロゴ のコピー | 174×86 | Logo salon Feminine chân trang |

## Xử lý Menu Dropdown & Giả định Responsive

- **Trạng thái Dropdown trên Desktop 1300px**: Trong PSD composite gốc, cả 2 menu con dưới "エステ" (4 mục: フェイシャル, ダイエット, 脱毛, 美肌再生プログラム) và "ヘアクリニック" (2 mục: 発毛, 症例) đều đang hiển thị mở sẵn. Để đáp ứng so khớp tham chiếu chính xác tại 1300px, trạng thái này được thể hiện trong CSS desktop; đồng thời hỗ trợ hover/focus.
- **Bố cục màn hình hẹp (Giả định)**:
  - **Desktop (≥992px)**: 2 cột (Content 702px + Sidebar 233px, gap 27px), container 963px căn giữa.
  - **Tablet (601px - 991px)**: Chuyển 1 cột, menu dropdown ẩn về trạng thái bình thường (mở khi hover/tap), card menu 3 cột co giãn thành 2 cột linh hoạt.
  - **Mobile (≤600px)**: Header sắp xếp gọn theo chiều dọc, card dịch vụ xếp 1 cột, sidebar hiển thị tuần tự bên dưới, touch target tối thiểu 44px, không tràn ngang ở 375px/390px.
  - **Lưu ý**: Đây là đề xuất bố cục phái sinh hợp lý do bản vẽ gốc không có artboard mobile, **không gọi là "khớp PSD mobile"**.

## Quyết định Kỹ thuật & Bố cục Pixel-Perfect

### 1. Khắc phục Màu sắc Then chốt (Navigation & Footer)
- **Thanh Navigation**: Trong PSD, nền toàn bộ thanh navigation và container bên trong là màu trắng ngà xám `#fbfbfc` (chỉ nút `HOME` mang nền cam thương hiệu `#fe9407`). Màu text của các liên kết trên thanh nav là màu nâu sô-cô-la `#66402b`, các vạch phân cách giữa mục mang màu `#dcdcdc`.
- **Chân trang Footer**: PSD phân tách rõ 2 tầng màu:
  - Khối nội dung chính footer (chứa thông tin salon Feminine, form hotline và hệ thống liên kết phân cấp): Nền màu kem ấm nhạt `#fdf1e3`, chữ và link màu nâu `#66402b`.
  - Thanh bản quyền chân trang (Copyright): Nền màu cam `#f88e01`, chữ trắng căn giữa.

### 2. Căn chỉnh Kích thước & Tọa độ Khung nhìn (1300×2216)
- **Container desktop**: PSD có canvas 1300px, container nội dung 963px (`(1300 - 963) / 2 = 168.5px`). Bỏ triệt để `padding: 0 10px` trên container desktop để căn chính xác mép trái tại `x = 168.5px`.
- **Phân bổ chiều cao các phân vùng (Y coordinates)**:
  - Header: 165px (`y = 5` đến `y = 170`).
  - Navigation: 58px (`y = 170` đến `y = 228`).
  - Hero banner: 383px (`y = 239` đến `y = 622`), khoảng hở trước hero 11px.
  - Khoảng trống hero → sidebar & nội dung chính: 37px (`y = 622` đến `y = 659`).
  - Cột nội dung và Sidebar: Khối sidebar kéo dài 1211px (`y = 659` đến `y = 1870`).
  - Khoảng trống nội dung → footer: 41px (`y = 1870` đến `y = 1911`).
  - Footer: 305px (`y = 1911` đến `y = 2216`), gồm 276px thân footer màu kem và 29px thanh bản quyền cam.
  - **Tổng chiều cao canvas thực tế**: Đúng **2216px** (sai lệch 0px so với PSD 1300×2216px).

### 3. Xử lý Liên kết (In-page Anchor & Subpages Scope)
Tất cả 39 liên kết trên trang đều có đích đến hợp lệ (0 link hỏng / missing target). Vì PSD gốc chỉ bao gồm một artboard PC Top page duy nhất, các liên kết dịch vụ chuyên sâu và trang thông tin được điều hướng theo cấu trúc ngữ cảnh trên trang:
- `#store`: Dẫn tới phần thông tin giới thiệu salon Feminine.
- `#esthe`, `#facial`, `#diet`, `#depilation`, `#skin-renewal`: Dẫn tới khu vực dịch vụ thẩm mỹ và từng card dịch vụ cụ thể.
- `#hair`, `#hair-growth`, `#cases`, `#hair-acti`: Dẫn tới khu vực dịch vụ clinic mọc tóc và case study thực tế.
- `#news`: Dẫn tới khối 新着情報 (Tin tức mới nhất).
- `#access`: Dẫn tới khối bản đồ hướng dẫn đường đi ở sidebar.
- `#campaign`: Dẫn tới banner chiến dịch khuyến mãi ở sidebar.
- `#recruit`: Dẫn tới banner tuyển dụng chuyên viên làm đẹp ở sidebar.
- `#faq`, `#voice`: Dẫn tới phần hỏi đáp và cảm nhận khách hàng.

### 4. Font chữ thay thế & Giới hạn So sánh Pixel
- Thiết kế PSD sử dụng font Mincho (serif) trang nhã cho tiêu đề và Gothic (sans-serif) cho nội dung. Phía web sử dụng font stack:
  - Serif: `'Hiragino Mincho ProN', 'Yu Mincho', 'MS PMincho', 'Noto Serif JP', Georgia, serif;`
  - Sans-serif: `'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans JP', sans-serif;`
- Nhờ việc hiệu chỉnh màu nền chính xác và khóa cứng tọa độ hình học, chỉ số MAE đã giảm ngoạn mục từ 30.84 xuống còn **15.98/255**. Sai khác nhỏ còn lại thuộc về cơ chế rasterization chữ của trình duyệt trên nền Linux so với Windows/Photoshop.

## Kết quả nghiệm thu QA

| Hạng mục kiểm tra | Trạng thái | Bằng chứng / Chi tiết đo lường |
| --- | --- | --- |
| SCSS → CSS Build | **Đạt** | Gulp 5.0.1 + Dart Sass 1.93.0; biên dịch thành công không lỗi (137ms) |
| HTML Validator | **Đạt** | `html-validate:recommended`: **0 lỗi, 0 cảnh báo** (`index.html`) |
| Lỗi Console & HTTP 404 | **Đạt** | **0 lỗi** JS runtime, **0 lỗi** request HTTP ≥ 400 (`screenshots/browser_report.json`) |
| Chống tràn ngang (No overflow) | **Đạt** | Đạt trên cả 10 viewport: 1300, 1024, 1023, 1022, 768, 767, 766, 390, 375 và landscape 844×390px |
| Tải Font và Hình ảnh | **Đạt** | 100% hình ảnh tải thành công, không có broken images (`naturalWidth > 0`) |
| Kích thước Canvas Desktop 1300px | **Đạt** | Khớp chính xác tuyệt đối: PSD 1300×2216px ↔ Thực tế 1300×2216px (sai lệch 0px / 0.00%) |
| Kiểm tra Liên kết Đích (Link targets) | **Đạt** | 39/39 liên kết hợp lệ, **0 link thiếu ID đích** |
| So sánh Pixel (MAE) | **Đạt** | `comparison.json`: MAE **15.98/255** (giảm từ 30.84/255 sau khi sửa đúng màu nav `#fbfbfc`, footer `#fdf1e3` và tinh chỉnh layout). Sai số tồn đọng là do rasterization font chữ |
| Trình duyệt ngoài Chrome/Linux | **Chưa kiểm tra** | Môi trường test hiện tại là Google Chrome 153 headless trên Linux sRGB |

### Đường dẫn hình ảnh kiểm tra

- Ảnh tham chiếu PSD gốc: [reference_1300x2216.png](screenshots/reference_1300x2216.png)
- Ảnh chụp màn hình Desktop 1300px: [screenshot_desktop_1300.png](screenshots/screenshot_desktop_1300.png)
- Ảnh chồng 50% (Overlay): [overlay_50.png](screenshots/overlay_50.png)
- Ảnh sai khác (Difference): [diff_content.png](screenshots/diff_content.png)
- Ảnh so sánh nhấp nháy (Blink GIF): [blink_comparison.gif](screenshots/blink_comparison.gif)
- Ảnh Mobile 375px: [screenshot_desktop_375.png](screenshots/screenshot_desktop_375.png)
- Ảnh Mobile 390px: [screenshot_desktop_390.png](screenshots/screenshot_desktop_390.png)
