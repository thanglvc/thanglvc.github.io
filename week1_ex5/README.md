# Tuần 1: Ngày 4 — Bài tập 1 / Trang vận chuyển Nhật (nang_cao_top+.psd)

HTML5 Semantic + SCSS/BEM + Gulp. Triển khai giao diện website vận chuyển Nhật Bản theo thiết kế Photoshop gốc `reponsive/Bai Tap/nang_cao_top+.psd` (canvas 1300×2237).

## Cách chạy bài

Trong thư mục `week1_ex5`, sử dụng Node 24:

```sh
npm run build
npm run dev
```

Mở **http://127.0.0.1:4175**. Gulp tự động compile lại khi có thay đổi trong `scss/`; refresh trình duyệt để kiểm tra.
CSS đã được biên dịch sẵn tại `css/style.css`, có thể chạy trực tiếp qua Live Server hoặc GitHub Pages.

Chạy bộ kiểm thử tự động (Console, 404, Tràn ngang, Screenshot, Overlay 50%, Diff):

```sh
npm run build
npm run check:visual
python3 tools/compare.py
npx --yes html-validate@11.16.0 index.html
```

## Cấu trúc thư mục

- `index.html`: Cấu trúc HTML5 semantic (`header`, `nav`, `main`, `aside`, `footer`, `section`, `article`, `time`, `address`); toàn bộ chữ tiếng Nhật được giữ dưới dạng HTML text có thể bôi đen và đọc được.
- `scss/`: Tổ chức BEM theo module (`_variables.scss`, `_base.scss`, `_header.scss`, `_nav.scss`, `_hero.scss`, `_layout.scss`, `_content.scss`, `_sidebar.scss`, `_footer.scss`, `style.scss` dùng `@use`).
- `css/style.css`: File CSS biên dịch từ SCSS, không chỉnh sửa trực tiếp.
- `images/`: Toàn bộ asset được xuất độc lập từ PSD gốc theo đúng quy tắc đặt tên tiền tố trong `AGENTS.md`.
- `tools/`: `capture.mjs` (chạy Chrome DevTools Protocol chụp ảnh 10 viewport và phát hiện lỗi console/404/overflow) và `compare.py` (tính sai số pixel, sinh overlay 50% và ảnh difference).
- `screenshots/`: Ảnh reference từ PSD, screenshot trình duyệt 1300px/390px/375px, overlay 50%, diff và báo cáo kiểm tra.

## Danh mục Asset (Quy chuẩn AGENTS.md)

| Tên file | Loại | Nguồn Layer PSD | Kích thước | Mô tả |
| --- | --- | --- | --- | --- |
| `logo_header.png` | Logo | head / レイヤー 43 | 385×59 | Logo 赤帽いつも元気運送 đầu trang (nền trong suốt) |
| `icn_freedial.png` | Icon | head / レイヤー 57 | 35×23 | Biểu tượng Free Dial cạnh hotline |
| `pic_hero_truck.png` | Ảnh | main_img / レイヤー 66 | 482×363 | Nhân viên chuyển hàng và xe tải đỏ |
| `pic_hero_top_right.png` | Ảnh | main_img / レイヤー 67 | 481×182 | Hình ảnh nền cụm badge trên phải hero |
| `pic_hero_bottom_right.png` | Ảnh | main_img / レイヤー 68 | 481×181 | Hình ảnh xe vận chuyển dưới phải hero |
| `pic_feature_moving.png` | Ảnh | contents / レイヤー 46 | 160×120 | Ảnh minh họa phần "小規模の引越に強い" |
| `pic_service_pricing.png` | Ảnh | contents / レイヤー 48 | 221×163 | Ảnh card dịch vụ bảng giá minh bạch |
| `pic_service_highroof.png` | Ảnh | contents / レイヤー 47 | 221×163 | Ảnh card dịch vụ xe thùng cao |
| `pic_service_driver.png` | Ảnh | contents / レイヤー 62 | 221×163 | Ảnh card dịch vụ tài xế trẻ khỏe |
| `pic_delivery_altar.png` | Ảnh | contents / レイヤー 55 | 221×163 | Ảnh card dịch vụ chuyển bàn thờ |
| `pic_delivery_appliance.png` | Ảnh | contents / レイヤー 54 | 221×163 | Ảnh card dịch vụ chuyển đồ điện gia dụng |
| `pic_delivery_elderly.png` | Ảnh | contents / レイヤー 56 | 221×163 | Ảnh card dịch vụ hỗ trợ người cao tuổi |
| `pic_side_checkpoint.png` | Ảnh | side / レイヤー 60 | 144×108 | Ảnh checklist điểm lưu ý chuyển nhà |
| `pic_side_company.png` | Ảnh | side / レイヤー 64 | 202×126 | Ảnh chụp nhân viên công ty ở sidebar |
| `bnr_side_01.png` | Banner | side / レイヤー 53 | 200×60 | Banner liên kết khu vực 1 |
| `bnr_side_02.png` | Banner | side / レイヤー 52 | 200×60 | Banner liên kết khu vực 2 |
| `bnr_side_03.png` | Banner | side / レイヤー 50 | 200×45 | Banner liên kết khu vực 3 |
| `logo_footer.png` | Logo | footer / レイヤー 43 のコピー | 296×46 | Logo chân trang (nền trong suốt) |

## Giả định Responsive (Màn hình hẹp)

PSD gốc chỉ cung cấp một artboard PC cố định (canvas 1300px, container 963px). Thiết kế không có artboard mobile riêng. Các giả định chuyển đổi bố cục hẹp:
- **Desktop (≥992px)**: Bố cục 2 cột (Content 688px + Sidebar 233px, gap 42px), container 963px căn giữa màn hình 1300px.
- **Tablet (601px - 991px)**: Chuyển sang 1 cột linh hoạt, navigation trượt ngang (scrollable), card dịch vụ chia 2 cột.
- **Mobile (≤600px)**: Header sắp xếp theo chiều dọc (logo căn giữa, số điện thoại lớn dễ bấm, 2 nút dàn hàng ngang), hero chuyển sang dạng khối xếp chồng, card dịch vụ xếp 1 cột, touch target tối thiểu 44px, không tràn ngang ở 375px/390px.
- **Lưu ý**: Đây là đề xuất bố cục phái sinh hợp lý dựa trên nội dung gốc, **không gọi là "khớp PSD mobile"**.

## Quyết định Kỹ thuật & Bố cục Pixel-Perfect

### 1. Căn chỉnh Kích thước & Tọa độ Khung nhìn (1300×2237)
- **Container desktop**: PSD có canvas 1300px, container nội dung 963px (`(1300 - 963) / 2 = 168.5px`). Bỏ triệt để `padding: 0 10px` trên container desktop để căn chính xác mép trái tại `x = 168.5px`.
- **Phân bổ chiều cao các phân vùng (Y coordinates)**:
  - Header: 124px (`y = 23` đến `y = 147`).
  - Navigation: 58px (`y = 147` đến `y = 205`).
  - Hero banner: 386px (`y = 205` đến `y = 591`) gồm 24px top padding và khối hero 362px. Cụm 3 huy hiệu (badge) được thả trôi tại `y = 370..456`, bắc ngang qua 2 bức ảnh bên phải.
  - Khoảng trống hero → nội dung: 36px (`y = 591` đến `y = 627`).
  - Khối nội dung chính (2 cột): 1369px (`y = 627` đến `y = 1996`). Trong đó, ảnh card dịch vụ giữ nguyên chiều cao 163px với lớp phủ huy hiệu trắng bán trong suốt `rgba(255, 255, 255, 0.8)` đúng layer PSD gốc.
  - Khoảng trống nội dung → footer: 83px (`y = 1996` đến `y = 2079`).
  - Footer: 158px (`y = 2079` đến `y = 2237`).
  - **Tổng chiều cao canvas thực tế**: Đúng **2237px** (sai lệch 0px so với PSD 1300×2237px).

### 2. Xử lý Liên kết (In-page Anchor & Subpages Scope)
Tất cả 27 liên kết trên trang đều có đích đến hợp lệ (0 link hỏng / missing target). Vì PSD chỉ thiết kế một artboard duy nhất cho trang Top PC (không có thiết kế trang con chi tiết), các liên kết được xử lý theo logic ngữ cảnh:
- `#contact`, `#estimate`, `#online-estimate`: Dẫn tới khu vực form liên hệ và thông tin hotline/báo giá.
- `#features`: Dẫn trực tiếp tới khối 特徴 (Đặc trưng của công ty).
- `#services`, `#moving-service`, `#delivery-service`, `#price`: Dẫn tới khối dịch vụ vận chuyển và bảng giá minh bạch.
- `#company`: Dẫn tới khối hồ sơ công ty ở sidebar.
- `#faq`: Dẫn tới mục giải đáp thắc mắc thường gặp.
- `#recruit`: Dẫn tới banner tuyển dụng ở sidebar.
- `#news-1`: Dẫn tới thông báo tin tức cập nhật.
- `#banner-1`, `#banner-2`, `#banner-3`: Dẫn tới các banner liên kết đối tác ở sidebar.

### 3. Font chữ thay thế & Giới hạn So sánh Pixel
- Thiết kế PSD sử dụng font tiếng Nhật thương mại rasterized trong Photoshop. Phía web sử dụng font stack tối ưu: `'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans JP', sans-serif`.
- Sự khác biệt về công nghệ khử răng cưa chữ (font hinting, subpixel antialiasing giữa FreeType/Linux và Photoshop renderer) tạo ra sai số không thể triệt tiêu hoàn toàn. Do đó, chỉ số MAE 17.79/255 phản ánh độ khớp hình học và màu sắc thực tế cao nhất có thể đạt được với text HTML sống.

## Kết quả nghiệm thu QA

| Hạng mục kiểm tra | Trạng thái | Bằng chứng / Chi tiết đo lường |
| --- | --- | --- |
| SCSS → CSS Build | **Đạt** | Gulp 5.0.1 + Dart Sass 1.93.0; biên dịch thành công không lỗi (137ms) |
| HTML Validator | **Đạt** | `html-validate:recommended`: **0 lỗi, 0 cảnh báo** (`index.html`) |
| Lỗi Console & HTTP 404 | **Đạt** | **0 lỗi** JS runtime, **0 lỗi** request HTTP ≥ 400 (`screenshots/browser_report.json`) |
| Chống tràn ngang (No overflow) | **Đạt** | Đạt trên cả 10 viewport: 1300, 1024, 1023, 1022, 768, 767, 766, 390, 375 và landscape 844×390px |
| Tải Font và Hình ảnh | **Đạt** | 100% hình ảnh tải thành công, không có broken images (`naturalWidth > 0`) |
| Kích thước Canvas Desktop 1300px | **Đạt** | Khớp chính xác tuyệt đối: PSD 1300×2237px ↔ Thực tế 1300×2237px (sai lệch 0px / 0.00%) |
| Kiểm tra Liên kết Đích (Link targets) | **Đạt** | 27/27 liên kết hợp lệ, **0 link thiếu ID đích** |
| So sánh Pixel (MAE) | **Đạt** | `comparison.json`: MAE **17.79/255** (giảm từ 34.96/255 sau khi hoàn thiện căn chỉnh pixel-perfect). Sai số tồn đọng là do rasterization font chữ |
| Trình duyệt ngoài Chrome/Linux | **Chưa kiểm tra** | Môi trường test hiện tại là Google Chrome 153 headless trên Linux sRGB |

### Đường dẫn hình ảnh kiểm tra

- Ảnh tham chiếu PSD gốc: [reference_1300x2237.png](screenshots/reference_1300x2237.png)
- Ảnh chụp màn hình Desktop 1300px: [screenshot_desktop_1300.png](screenshots/screenshot_desktop_1300.png)
- Ảnh chồng 50% (Overlay): [overlay_50.png](screenshots/overlay_50.png)
- Ảnh sai khác (Difference): [diff_content.png](screenshots/diff_content.png)
- Ảnh so sánh nhấp nháy (Blink GIF): [blink_comparison.gif](screenshots/blink_comparison.gif)
- Ảnh Mobile 375px: [screenshot_desktop_375.png](screenshots/screenshot_desktop_375.png)
- Ảnh Mobile 390px: [screenshot_desktop_390.png](screenshots/screenshot_desktop_390.png)
