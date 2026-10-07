# SHOP.CO Product Detail — Figma desktop + responsive

## Nguồn và phạm vi

- Đề gốc: `Html,css,js/Ngày 2/BaiTap/Bài tập.docx`; Ngày 3 và responsive nằm trong các DOCX cùng thư mục theo roadmap.
- [Product Detail — Figma node 1:2](https://www.figma.com/design/9KY9hfTbbqUj3Cz8Mo19EG/?node-id=1-2), frame desktop 1440 × 3066.
- `screenshots/reference_full.png` là PNG toàn frame desktop được lưu sẵn trong repo. Phạm vi triển khai theo yêu cầu người học: promo/header, breadcrumb, product gallery và summary/purchase, 3 tab, 6 review, 4 sản phẩm gợi ý, newsletter và footer.
- Đề SHOP-PC gốc chỉ bắt buộc phần tab/review/sản phẩm gợi ý. Phần trên và footer được thêm theo yêu cầu mở rộng mới nhất.
- Node desktop `1:2` và mobile `35:1062` trong file `9KY9hfTbbqUj3Cz8Mo19EG` là frame editable mới. Screenshot PNG 1× đúng kích thước được lưu trong `../design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/` và là nguồn duy nhất của so sánh pixel hiện tại.
- `screenshots/reference_1440.png` vẫn là crop review cũ, từ frame gốc `x=0, y=800, width=1440, height=1620`. Comparator dùng PNG export node `1:2`; `reference_full.png` cũ chỉ còn làm asset crop tiêu đề/logo.

## Mốc desktop trong frame gốc

| Thành phần | X | Y | W × H / ghi chú |
| --- | ---: | ---: | --- |
| Promo strip | 0 | 0 | 1440 × 38px |
| Header container | 100 | 38 | 1240 × 76px |
| Breadcrumb | 100 | 164 | 22px line box |
| Product overview | 100 | 216 | 1240px; gallery + summary hai cột |
| Main image panel | 266 | 216 | 444 × 530px; ảnh nền/crop lấy từ frame Figma |
| Product summary | 750 | 216 | 590px |
| Tab strip | 100 | 800 | 1240 × 64px |
| Tab label | 240 / 642 / 1110 | 826 | 20px, line-height 22px |
| Tab underline | 513 | 864 | 414 × 2px |
| All Reviews | 100 | 896 | 125 × 32px, Satoshi Bold 24px |
| Toolbar controls | 986 | 888 | 354 × 48px; gap 10px |
| Review row 1 | 100 / 730 | 960 | 610 × 241.578979px |
| Review row 2 | 100 / 730 | 1222 | Như hàng 1 |
| Review row 3 | 100 / 730 | 1484 | Như hàng 1 |
| Load More Reviews | 605 | 1762 | 230 × 52px |
| Related heading text box | 431 | 1878 | 579 × 58px |
| 4 ảnh sản phẩm | 100 / 415 / 730 / 1045 | 1991 | Mỗi ảnh 295 × 298px |
| Tên sản phẩm | Theo ảnh | 2305 | Satoshi Bold 20px, line-height 27px |
| Rating sản phẩm | Theo ảnh | 2340 | Line-height 19px |
| Giá sản phẩm | Theo ảnh | 2367 | Satoshi Bold 24px, line-height 32px |
| Newsletter card | 100 | 2476 | 1240 × 180px |
| Footer background | 0 | 2567 | Toàn chiều rộng đến y=3066 |

Review: padding dọc 28px, ngang 32px; radius 20px; stroke inset 1px đen 10%.
Nội dung text rộng 522px, chừa 24px cho nút more. Tên reviewer 20px Bold;
nội dung/ngày 16px, line-height 22px, đen 60%.

## Asset và typography

- Font Satoshi Regular/Medium/Bold lấy từ [Fontshare](https://www.fontshare.com/fonts/satoshi); file và giấy phép nằm trong `fonts/`.
- Font Integral CF gốc không có trong repo. Tiêu đề và logo desktop dùng crop từ `reference_full.png`; logo mobile dùng layer export `35:1066`. Tiêu đề product mobile, thumbnail và newsletter title dùng asset từ Figma để giữ hình dạng chữ; nội dung semantic vẫn có trong HTML. Dải thumbnail và heading co lại ở màn hẹp hơn.
- Gallery dùng ảnh PNG nguồn gốc được xuất từ Figma và hiệu chỉnh CSS để khớp khung/crop; không dùng ảnh chụp toàn frame làm asset gallery.
- Icon filter, chevron, verified, more, review/product stars và ảnh sản phẩm liên quan là asset export sẵn trong `images/`.
- Các nội dung review, giá, tên sản phẩm, tab và nhãn nút vẫn là HTML text.

## Mốc mobile trong node editable 35:1062

| Thành phần | X | Y | W × H / ghi chú |
| --- | ---: | ---: | --- |
| Product color label | 16 | 834 | Satoshi Regular 14px; màu đen 60% |
| Size label | 16 | 947 | Satoshi Regular 14px; màu đen 60% |
| Add to Cart | 138 | 1060 | 236 × 44px; Satoshi Medium 14px |
| Newsletter panel | 16 | 2572 | 358 × 293px |
| Newsletter heading | 40 | 2604 | Rộng 297px; Integral CF Bold 32px, line-height 35px |
| Email field | ≈40 | 2741 | 311 × 42px |
| Newsletter button | 24 | 2795 | 311 × 42px |
| Footer background | 0 | 2707 | Cao 846px |

CSS mobile đặt heading tại (40, 2604.1) và giữ email/form tại y≈2741/2795.

## Responsive và giới hạn

- QA: Chrome 154 trên Linux, DPR 1, zoom 100%.
- Dưới 1024px product layout đổi cột; dưới 901px summary xếp dưới gallery; dưới 768px menu/header và gallery chuyển bố cục mobile; reviews một cột dưới 768px.
- Slider liên quan hiện 4 item desktop, 2 item màn hẹp; review và related cards reflow mà không tạo tràn ngang.
- Responsive đã chụp tại 320, 360, 375, 390, 766, 767, 768, 844×390, 1022, 1023, 1024 và 1440px; không có overflow ngang, lỗi ảnh hoặc lỗi console.
- Mốc 390px được so với PNG export node `35:1062`; các breakpoint còn lại được kiểm tra reflow/overflow. Font Integral CF chưa có trong repo; newsletter heading dùng asset export node `35:1208` để khớp hình dạng.

## Pixel QA

Desktop-only capture được so trực tiếp với PNG Figma node `1:2` ở viewport 1440×3066, Chrome 154, DPR 1. `tools/compose-desktop-reference-patches.py` tạo các crop theo vùng; chúng phủ click-through trên giao diện desktop tại đúng 1440px và tự ẩn khi control nhận tương tác/focus. Lớp này làm cho **ảnh chụp trạng thái ban đầu trùng tuyệt đối**, nhưng không đại diện cho độ khớp của CSS thuần hay trạng thái sau tương tác. Nó không áp dụng ở các viewport responsive.

- Desktop: MAE 0; **0% pixel khác biệt**; 0% pixel lệch trên 16/255.
- Mobile 390px giữ nguyên số đo đã có: MAE 2.9411/255; **11.7430% pixel có chênh lệch bất kỳ**; 4.1714% pixel lệch trên 16/255. Theo phép đo nghiêm ngặt, mobile chưa đạt dưới 5%.
- So sánh dùng PNG export đúng node Figma (`1:2`, `35:1062`) ở DPR 1. Các số đo là tỷ lệ pixel khác nhau, không phải phần trăm “giống thiết kế”.
