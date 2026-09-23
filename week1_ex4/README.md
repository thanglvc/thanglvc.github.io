# Ngày 2 — Product Detail / SHOP-PC

HTML + SCSS/BEM + Gulp. Phạm vi: vùng 3 tab, 6 review và 4 sản phẩm gợi ý trong ảnh đề.
Thông số gốc và node Figma: [DESIGN.md](DESIGN.md).

## Chạy bài

Trong thư mục `week1_ex4`, dùng Node 24:

```sh
npm ci
npm run build
npm run dev
```

Mở **http://127.0.0.1:4174**. Gulp tự compile lại khi sửa `scss/`; refresh browser để thấy thay đổi.
CSS build đã có sẵn trong `css/`, có thể phục vụ thư mục này bằng Live Server hoặc GitHub Pages.
Nên dùng HTTP server thay vì mở file:// để preload font hoạt động đúng.

## Cấu trúc

- `index.html`: nội dung semantic; text review/giá/tên sản phẩm vẫn chọn và đọc được.
- `scss/`: base, tab, button, review, product card; `style.scss` dùng `@use`.
- `css/style.css`: CSS build từ SCSS, không sửa trực tiếp.
- `images/`, `fonts/`: asset local từ Figma/Fontshare; ảnh sản phẩm 1×/2×.
- `gulpfile.js`: build, watch và localhost server; chỉ hai dev dependency Gulp/Sass.
- `tools/`: chụp Chrome và so PNG; không tải script kiểm tra lên giao diện.
- `screenshots/`: reference, screenshot, overlay, difference và báo cáo JSON.

Satoshi lấy từ Fontshare; giấy phép đi kèm ở `fonts/FFL.txt`. Font được dùng cho
website này, không phải bộ font để phân phối lại; người tái sử dụng nên lấy bản riêng từ Fontshare.

Các control được disabled vì đây là bài giao diện ngày 2. Chưa có hành vi chuyển tab,
filter, Latest, viết review, Load More hoặc slider. Không dùng link giả để mô phỏng chức năng.

## Kiểm tra PerfectPixel

1. Mở bài ở viewport **1440px**, zoom **100%**, DPR **1**.
2. Dùng `screenshots/reference_1440.png` làm ảnh overlay, offset **x=0, y=0**, opacity **50%**.
3. So với `screenshots/browser_1440.png`; ảnh chồng có sẵn là `screenshots/overlay_50.png`.

Reference được cắt từ ảnh gốc toàn frame tại y=800 đến y=2420; vì vậy trang bài tập
không có khoảng trắng 800px hoặc header giả trước phần tab.

Chạy lại kiểm tra:

```sh
npm run build
npm run check:visual
python3 tools/compare.py
npx --yes html-validate@11.16.0 index.html
```

Chụp ảnh cần Chrome (`/usr/bin/google-chrome`; có thể đặt `CHROME_BIN`), Node 24.
So ảnh cần Python 3 và Pillow. Script tạo profile Chrome riêng trong thư mục tạm,
phục vụ bài ở localhost, đóng browser/server kiểm tra khi xong.

## Kết quả ngày 23/09/2026

| Kiểm tra | Trạng thái | Bằng chứng / giới hạn |
| --- | --- | --- |
| SCSS → CSS | Đạt | Gulp 5.0.1, Sass 1.105.0; `npm run build` exit 0 |
| Watch/server | Đạt | Sửa partial kích hoạt build; localhost:4174 chạy |
| HTML validator | Đạt | html-validate 11.16.0, 0 lỗi / 0 warning |
| JS công cụ build/QA | Đạt | `node --check` cho gulpfile và capture |
| Font, ảnh, HTTP, console | Đạt | `screenshots/browser_report.json`: errors rỗng, font tải đủ, không ảnh hỏng |
| Tràn ngang | Đạt | 1440, 1024, 1023, 1022, 768, 767, 766, 390, 375px và landscape 844×390 |
| Mốc bố cục PC | Đạt ở Chrome đã kiểm tra | Container 1240; card 610; các hàng y=160/422/684; ảnh 295×298 ở y=1191 |
| So pixel tuyệt đối 100% | **Chưa đạt** | Pixel diff vẫn khác ở nét chữ, viền và SVG; không báo “100%” từ cảm quan |
| Mobile khớp Figma | Chưa kiểm tra | Chỉ fallback chống vỡ layout; SHOP-RESP là bài riêng |
| Firefox/Safari/thiết bị thật | Chưa kiểm tra | Chưa có browser matrix; QA hiện chỉ Chrome/Linux |
| Tương tác JS | Không áp dụng ngày 2 | Thuộc SHOP-JS; control tĩnh đã ghi rõ ở trên |
| GitHub publish | Chưa thực hiện | Source ở local; chưa commit/push |

Ảnh so sánh cuối ở Chrome **153.0.8010.47**, Linux, sRGB, DPR 1,
LCD subpixel antialiasing tắt để so với PNG Figma; `font-render-hinting=none`.
Browser mặc định trên hệ điều hành khác có thể render cạnh chữ khác.

Theo `screenshots/comparison.json`: sai số tuyệt đối trung bình **0.876/255 mỗi kênh màu**;
**3.7818% pixel có khác biệt bất kỳ**, **1.6998% pixel khác trên 16/255 ở ít nhất một kênh**.
Đây là số đo pixel thô trên cả crop, **không phải tỷ lệ “giống thiết kế”**; ảnh có nhiều nền trắng.
Ảnh difference được tăng tương phản 4× để dễ nhìn sai lệch, không dùng ảnh tăng tương phản để tính số đo.

`text-box-trim` khớp chế độ trim của Figma; `calc-size(..., round(...))` khớp việc Figma
làm tròn độ rộng hộp chữ. Các thuộc tính mới này cần xác minh khi bổ sung browser matrix;
[calc-size chưa có hỗ trợ đồng đều trên mọi browser](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/calc-size).

AI đã dựng bài không đồng nghĩa người học đã hoàn thành phần kiến thức BEM/SCSS/Gulp/Git.
