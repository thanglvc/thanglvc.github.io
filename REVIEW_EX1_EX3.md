# Review ex1–ex3 theo góp ý team lead

Ngày cập nhật: 25/09/2026. Rule chung: [AGENTS.md](../AGENTS.md).

## Thay đổi

- **Ex1:** dùng `figure`/`figcaption` cho cụm ảnh, danh sách hai cột bằng Flexbox, icon “more info” bằng background. Bỏ wrapper/icon span không cần thiết, Grid, transform căn pixel và hiệu ứng hover. Dùng margin/padding cho vị trí; tăng line-height đoạn giới thiệu trên mobile để tránh chữ chồng nhau.
- **Ex2:** đưa ảnh minh họa Blogroll, Libra, banner tác giả và điện thoại vào background CSS. Nội dung chính vẫn là text. Thanh cam và đường phân cách dùng border của component; bỏ thẻ rỗng, wrapper, style trùng và các span không cần thiết.
- **Ex3:** bullet ngôi sao dùng background của `li`; dải nền tiêu đề dùng pseudo-element; bỏ wrapper quanh nội dung bình luận, transform, hover và nút gửi ẩn. Giữ input/textarea có label trong section, không tạo chức năng gửi khi đề chưa yêu cầu.
- Rút gọn `li`, bỏ `href="#"`, giữ cách trình bày chữ theo mẫu. Các comment cần thiết có đúng hai dòng English/Japanese.
- Ex1 và ex3 dùng breakpoint **600px**; ex2 dùng một breakpoint **362px** để giữ đúng khoảng đệm của mẫu tại 320px. Override nằm trong `css/responsive.css`. Ex2 và ex3 được căn giữa theo chiều ngang ở desktop lẫn mobile; ex1 tự xuống hàng bằng Flexbox.
- Xuất **9 ảnh WebP lossless**, giữ nguyên PNG/JPG nguồn. Kích thước và pixel RGBA của bản xuất khớp nguồn. Các screenshot phục vụ QA giữ PNG.

| Bài | HTML + CSS trước | Sau, gồm responsive.css |
| --- | --- | --- |
| Ex1 | 599 dòng | 408 dòng |
| Ex2 | 571 dòng | 294 dòng |
| Ex3 | 650 dòng | 448 dòng |

## Kết quả kiểm tra

- **Đạt:** HTML validator với cấu hình `html-validate:recommended` có sẵn trong `week1_ex4/.htmlvalidate.json`; ba file không có lỗi/cảnh báo.
- **Đạt:** 39 trường hợp viewport Chrome headless. Mỗi bài kiểm tra đúng kích thước ảnh mẫu, 320, 375, 390, 599, 600, 601, 708, 709, 768, 844, 1024 và 1440px; viewport 844×390 kiểm tra bố cục ngang. Không tràn ngang, chữ bị cắt, ảnh hỏng, lỗi HTTP hay console.
- **Đạt:** vùng thông tin ex3 nhận focus và cuộn khi nội dung vượt khung; ba control có label liên kết đúng.
- **Đạt:** kiểm tra trực tiếp 10 điểm màu phẳng trước khi căn giữa. Căn giữa chỉ đổi vị trí; các mã màu CSS giữ nguyên. Xem ảnh overlay và số liệu review.
- **Đạt:** kiểm tra 9 WebP khớp pixel nguồn; `git diff --check` và `node --check tools/review-exercises.mjs` không có lỗi.
- **Đã review trực quan:** ảnh desktop, mobile và overlay. Render lại source cũ trên cùng Chrome để so đúng điều kiện: cả ba bài giữ nguyên kích thước ảnh tại viewport mẫu, 320px và 375px. Sai lệch khi so cùng Chrome có tăng ở những viewport mà yêu cầu mới đổi vị trí: panel ex2 được căn giữa hoàn toàn ở cả desktop/mobile (x=23.5px tại 363px; 29.5px tại 375px; 2px tại 320px); profile ex3 bắt đầu tại x=86px ở viewport 672px và chiếm toàn chiều rộng trên mobile. Sai lệch trung bình RGB do dịch chuyển và raster chữ: ex1 **0.21–0.47**, ex2 **0.66–15.46**, ex3 **1.02–6.60** trên các viewport đã so (thang 0–255). Sai số tăng tại ex2/ex3 do vị trí được dịch để căn giữa; output **không trùng từng pixel**; xem chi tiết theo viewport trong số liệu review. Vẫn có khác biệt ở raster hóa chữ và thanh cuộn so với PSD; chưa có ngưỡng pixel-perfect được thống nhất.
- **Chưa kiểm tra:** điện thoại vật lý và các browser khác. Viewport mô phỏng không thay thế thiết bị thật.
- **Không áp dụng:** build và test chức năng gửi form; ba bài là HTML/CSS thuần và đề chưa yêu cầu gửi dữ liệu.

## Bằng chứng và chạy lại

| Bài | Desktop | Mobile 375px | Overlay | Báo cáo browser |
| --- | --- | --- | --- | --- |
| Ex1 | [Ảnh](week1_ex1/screenshots/review_1031.png) | [Ảnh](week1_ex1/screenshots/review_375.png) | [Ảnh](week1_ex1/screenshots/review_overlay.png) | [JSON](week1_ex1/screenshots/review_browser.json) |
| Ex2 | [Ảnh](week1_ex2/screenshots/review_363.png) | [Ảnh](week1_ex2/screenshots/review_375.png) | [Ảnh](week1_ex2/screenshots/review_overlay.png) | [JSON](week1_ex2/screenshots/review_browser.json) |
| Ex3 | [Ảnh](week1_ex3/screenshots/review_672.png) | [Ảnh](week1_ex3/screenshots/review_375.png) | [Ảnh](week1_ex3/screenshots/review_overlay.png) | [JSON](week1_ex3/screenshots/review_browser.json) |

Chạy `node tools/review-exercises.mjs` từ repo để cập nhật ảnh/browser report. Script dùng Node 24 và Chrome cài trên máy, không thêm dependency vào bài; có thể đặt `CHROME_BIN` nếu Chrome nằm ở đường dẫn khác. Lệnh trả exit code khác 0 khi phát hiện lỗi. Overlay và số liệu pixel cần tạo lại khi thay đổi giao diện.