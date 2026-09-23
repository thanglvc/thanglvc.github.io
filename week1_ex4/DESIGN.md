# SHOP-PC — đặc tả đối chiếu Figma

## Nguồn và phạm vi

- Đề: `Html,css,js/Ngày 2/BaiTap/Bài tập.docx`, đã đọc chữ và ảnh nhúng.
- [Product Detail desktop — node 1:2](https://www.figma.com/design/6qq9GTustc0RwmL8hGN9Q8/?node-id=1-2), frame 1440 × 3066.
- Chỉ vùng đề yêu cầu: 3 tab, toolbar review, 6 review / 2 cột, Load More Reviews, 4 sản phẩm gợi ý.
- Không gồm header, thông tin sản phẩm chính, newsletter hoặc footer.
- Ảnh đối chiếu là crop `x=0, y=800, width=1440, height=1620` từ PNG xuất trực tiếp Figma. Trên trang bài tập, mọi tọa độ y bên dưới trừ 800.
- Phạm vi ngày 2 là HTML/SCSS tĩnh. Control được disabled, không giả lập chức năng. Tab/filter/slider thuộc SHOP-JS ngày 3; chưa triển khai.
- Responsive trong bài này chỉ chống vỡ layout; không được coi là đã nghiệm thu bản mobile Figma của SHOP-RESP.

## Kích thước gốc

| Thành phần | X | Y trong Figma | W × H / ghi chú |
| --- | ---: | ---: | --- |
| Container | 100 | — | 1240px |
| Tab label | 240 / 642 / 1110 | 826 | 20px, line-height 22px, trim cap/alphabetic |
| Tab underline | 513 | 864 | 414 × 2px, stroke giữa đường |
| All Reviews | 100 | 896 | 125 × 32px, Satoshi Bold 24px |
| Toolbar controls | 986 | 888 | 354 × 48px; gap 10px |
| Review row 1 | 100 / 730 | 960 | 610 × 241.578979px |
| Review row 2 | 100 / 730 | 1222 | Như hàng 1 |
| Review row 3 | 100 / 730 | 1484 | Như hàng 1 |
| Load More Reviews | 605 | 1762 | 230 × 52px |
| Related heading text box | 431 | 1878 | 579 × 58px, Integral CF Bold 48px |
| Related heading ink bounds | 431.28125 | 1895.28003 | 577.02368 × 35.88px |
| 4 ảnh sản phẩm | 100 / 415 / 730 / 1045 | 1991 | 295 × 298px |
| Tên sản phẩm | Như ảnh | 2305 | Satoshi Bold 20px, line-height 27px |
| Rating sản phẩm | Như ảnh | 2340 | Line-height 19px, sao cao 18.489876px |
| Giá sản phẩm | Như ảnh | 2367 | Satoshi Bold 24px, line-height 32px |

Review: padding dọc 28px, ngang 32px; radius 20px; stroke inset 1px đen 10%.
Nội dung text rộng 522px, chừa 24px cho nút more. Rating cao 22.578985px.
Tên người viết 20px Bold, trim cap/alphabetic; khoảng cách rating → author 15px.
Author → nội dung 12px; nội dung → ngày 24px. Nội dung/ngày 16px, line-height 22px, đen 60%.

## Asset và typography

- Font Satoshi Regular/Medium/Bold lấy từ [Fontshare](https://www.fontshare.com/fonts/satoshi); lưu local trong `fonts/`.
- Tiêu đề Integral CF sử dụng SVG outline xuất nguyên bản từ text layer `9:249`, với alt trong heading semantic. Không dùng font thay thế.
- Icon: filter `9:25`, chevron `9:19`, verified `9:137`, more `9:119`; tải asset xuất bởi Figma.
- Review stars: `9:34` (4.5), `9:149` (4), `9:168` (3.5).
- Product stars: `9:259` (4), `9:290` (3.5), `9:300` (4.5), `9:310` (5).
- Ảnh sản phẩm xuất trực tiếp các frame `9:250`, `9:251`, `9:252`, `9:288` ở 1× và 2×. Giữ crop/nền/bo góc nguyên bản; dùng srcset.
- Trừ tiêu đề outline, mọi tên sản phẩm, review, ngày, giá, tab và nhãn nút là HTML text.

## Giả định triển khai

- Browser QA: Chrome trên Linux, DPR 1, zoom 100%. Chưa có browser matrix của đề.
- Grid/Flexbox dùng luồng tài liệu; không định vị tuyệt đối toàn bộ trang.
- Các khoảng cách phần lẻ được làm tròn trên lưới 1/64px của Chrome, giữ chính xác vị trí bắt đầu hàng.
- Dưới 1024px: sản phẩm chuyển 2 cột. Dưới 768px: review 1 cột. Đây là fallback của bài PC, không phải breakpoint được suy ra từ Figma mobile.
- Giá/discount giữ nguyên design kể cả tỷ lệ discount không khớp phép tính từ giá cũ/mới.
- `451` là tổng review theo thiết kế; 6 review là dữ liệu hiển thị của bài.
