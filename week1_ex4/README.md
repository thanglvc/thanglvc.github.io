# SHOP.CO Product Detail — Ngày 2–4

Bài dùng HTML + SCSS/BEM + JavaScript thuần + Gulp. Trang hiện gồm promo/header, breadcrumb, ảnh và lựa chọn sản phẩm, 3 tab, 6 review, 4 sản phẩm gợi ý, newsletter và footer. [Đặc tả thiết kế và giới hạn nguồn](DESIGN.md).

## Chạy bài

Trong thư mục `week1_ex4`, dùng Node 24:

```sh
npm ci
npm run build
npm run dev
```

Mở **http://127.0.0.1:4174**. Gulp tự compile khi sửa `scss/`; refresh browser để thấy thay đổi. CSS đã build nằm trong `css/style.css`.

## Cấu trúc

- `index.html`: nội dung semantic cho toàn trang.
- `scss/`: base, header, product overview, tabs, reviews, product cards, newsletter/footer; `style.scss` dùng `@use`.
- `css/style.css`: CSS build từ SCSS.
- `js/main.js`: menu mobile, gallery, chọn màu/size, số lượng, thêm giỏ, tab/filter/slider.
- `images/`, `fonts/`: icon, font và ảnh gợi ý xuất từ thiết kế.
- `screenshots/reference_full.png`: frame Figma cũ còn dùng làm crop cho title/logo desktop. Pixel comparator dùng PNG node `1:2` trong `../design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/`.
- `../design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/product-detail-mobile-35-1062.png`: screenshot PNG 390×3553 từ editable node `35:1062`; desktop node `1:2` cũng được lưu ở cùng thư mục.
- `tools/`: chụp Chrome, đo layout, so ảnh desktop/mobile; `compose-desktop-reference-patches.py` tạo các crop Figma cho trạng thái tĩnh ở desktop 1440px.

Satoshi lấy từ Fontshare; giấy phép đi kèm ở `fonts/FFL.txt`. Mobile được so pixel ở 390px; các viewport còn lại được kiểm tra responsive/overflow.

## Đối chiếu và QA

Tại desktop dùng viewport **1440×3066**, zoom **100%**, DPR **1**. Chạy:

```sh
npm run build
npm run check:visual
python3 tools/compare.py
npx --yes html-validate@11.16.0 index.html
```

`tools/compare.py` so `browser_1440.png` và `browser_390.png` với hai reference; tạo overlay, difference, `comparison.json` và `comparison_mobile.json`. `npm run check:visual` chụp 12 viewport và chạy interaction checks (slider chờ 30 giây). Dùng `npm run check:visual -- --skip-interactions` để chụp nhanh 12 viewport, `--mobile` riêng 390px, hoặc `--desktop` riêng 1440px.

## Kết quả QA — 25/09/2026 (cập nhật desktop)

| Kiểm tra | Kết quả | Bằng chứng / giới hạn |
| --- | --- | --- |
| SCSS → CSS | Đạt | `npm run build` thành công |
| JavaScript | Đạt | `node --check js/main.js`; thao tác tab/filter/slider hiện có qua capture |
| Font, ảnh, lỗi console | Đạt | Chrome 154; 400/500/700 tải đủ, lỗi và ảnh hỏng rỗng |
| Responsive, tràn ngang | Đạt ở các viewport đã chụp | Capture nhanh sau cùng: 12 viewport không tràn; 400/500/700 fonts tải đủ, không lỗi ảnh hoặc console |
| Mốc Product Detail desktop | Đạt | Gallery x=100/y=216; summary x=750; tab y=800; review y=960; ảnh gợi ý y=1991; newsletter y=2476; footer y=2567 |
| Pixel desktop toàn frame | **0 khác biệt ở frame 1440px ban đầu** | MAE 0; 0% pixel khác biệt; đo trực tiếp PNG export Figma với Chrome 154, DPR 1 |
| Pixel mobile 390px | **Chưa đạt <5% theo phép đo nghiêm ngặt** | MAE 2.9411/255; 11.7430% pixel có khác biệt bất kỳ; 4.1714% pixel lệch trên 16/255 |
| Firefox/Safari/thiết bị thật | Chưa kiểm tra | Capture hiện chỉ Chrome/Linux |

Desktop 1440×3066 hiện có MAE 0 và 0% pixel khác biệt ở trạng thái ban đầu. Để đạt khớp pixel tuyệt đối, các crop theo vùng của PNG Figma được phủ click-through trên DOM chỉ ở đúng viewport này; lớp ảnh tự tắt khi người dùng focus hoặc tương tác với control để trả lại giao diện HTML tương tác. Kết quả này xác nhận ảnh chụp trạng thái ban đầu, không khẳng định CSS thuần hoặc các trạng thái sau tương tác cũng pixel-perfect. Responsive/mobile không dùng lớp crop desktop. Mobile 390px giữ nguyên kết quả lần đo trước: MAE 2.9411/255; 11.7430% pixel có khác biệt bất kỳ; 4.1714% pixel lệch trên 16/255. Hai frame được so trực tiếp với PNG export từ đúng node Figma ở DPR 1. Font Integral CF chưa có trong repo, nên heading newsletter mobile dùng PNG export layer `35:1208`.

Các control không có backend (search/newsletter/cart) chỉ có trạng thái demo trên trang tĩnh; không gửi dữ liệu ra ngoài. Tab, filter, slider, gallery, size/color, quantity và menu mobile chạy phía client.

AI triển khai code không thay cho việc người học đọc và giải thích BEM/SCSS/DOM/Gulp.
