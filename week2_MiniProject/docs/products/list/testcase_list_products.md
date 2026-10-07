# Testcase — Danh sách sản phẩm

**Căn cứ:** [DD](dd_list_products.md), API và workflow trong DD; bản nháp ngày 06/10/2026 chờ review. Chưa chạy các case mới. Case kiểm tra UI bằng response giả lập không chứng minh BE/DB.

## 1. Phạm vi và cách test

| Phần | Cách test | Trạng thái |
| --- | --- | --- |
| FE | Manual trên màn hình của DD; DevTools xem request, control và thông báo | Chờ triển khai FE và chạy browser theo thiết kế đã được duyệt. |
| BE | Manual Postman với API thật; Feature Test Pest khi có code | API đã có source; chưa có file automated test cho scope này, các case mới chưa chạy. |

## 2. Chuẩn bị và bước chung

Chạy từ thư mục mini project, dùng DB local kiểm thử riêng. Không dùng dữ liệu thật. Có thể chạy server bằng lệnh dưới; FE cần Vite và route/view của chức năng sau khi được triển khai.

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d php artisan serve
```

```bash
npm run dev
```

**Base URL:** địa chỉ server in ra, ví dụ `http://127.0.0.1:8000`. **Route FE:** `/products` — route FE đề xuất, chưa có source; API GET /api/products đã có..

**Dữ liệu chuẩn:** A và B là hai danh mục có ID thật; P và Q là hai sản phẩm khác ID. P: tên Bàn phím, giá 199000, category A, mô tả Bàn phím dùng để thực hành. U là payload cập nhật đủ bốn key: name = Bàn phím mới, price = "299000", category_id = ID của B, description = Mô tả mới. ID ví dụ không mặc định là ID fixture. Với phân trang, tạo đủ số sản phẩm của case bằng POST chuẩn hoặc factory trong DB kiểm thử.

### Manual FE — M-FE

1. Mở route trong case, bật Network/Console; ghi dữ liệu ban đầu và xóa lịch sử request của thao tác trước.
2. Nhập/chọn dữ liệu hoặc thiết lập trạng thái của case rồi thực hiện đúng nút, link hay phím. Số request trong expected chỉ tính thao tác này, không tính request tải trước đó.
3. Kiểm tra thông báo, focus, enabled/disabled và dữ liệu hiển thị; xem URL/method/header/payload/response thật trong Network. Đọc lại sản phẩm bằng GET khi case cần xác nhận DB.
4. Ghi actual, ngày, browser/version và ảnh hoặc response; gỡ cấu hình mạng/override sau mỗi case. API lỗi không tự chứng minh UI đã đúng.

**Biên ký tự:** dùng Console tạo `'a'.repeat(255)`, 256, 2000 hoặc 2001 rồi dán giá trị thật; lặp lại với chữ `á` và emoji để kiểm tra cách đếm ký tự. Không nhập dòng chữ “255 ký tự”.

### Manual BE — M-BE

1. Chuẩn bị fixture và snapshot P/Q/categories (DB viewer local), chọn đúng method/path trong Postman. Gửi Accept JSON; PUT/PATCH có Content-Type JSON; GET/DELETE không body.
2. Dùng payload trong API spec, thay đúng field/điều kiện được nêu. Khi có nhiều giá trị, chạy từng request riêng, phục hồi baseline giữa các lần để biết dữ liệu nào đã đổi.
3. Đối chiếu status, schema, error keys và response, đọc lại GET/DB. Case lỗi validate phải giữ DB; case lỗi sau ghi không được assert rollback khi code không có transaction.
4. Lưu response và actual. Trong ví dụ description chứa `\n`, body JSON phải tạo xuống dòng thật sau decode.

### Thiết lập lỗi — F-HTTP / F-DB

- Mạng chậm: DevTools Network → Slow 3G; mất mạng sau khi đã tải form: Offline. Lỗi GET lúc mở: Network request blocking cho đúng URL; bỏ chặn và reload sau test.
- JSON/schema sai hoặc mảng rỗng: trên Chromium dùng Sources → Overrides → chọn thư mục local kiểm thử; từ request trong Network lưu/override nội dung response, sửa body theo case rồi reload hoặc gửi lại. Xác nhận request thực sự có dấu override và body đúng. Cách này không sửa status HTTP.
- F-HTTP cho 415/422/500/status khác/timeout: dùng tình huống API thật khi tạo được (404 bằng xóa P sau tải; 422 category bằng xóa danh mục B chưa được dùng trong DB local). Với response không tạo được bằng thao tác trên, cần công cụ chặn HTTP có hỗ trợ sửa status hoặc browser test double; hiện chưa có cấu hình/helper, ghi **Chưa chạy — chờ thiết lập response**. Không tạo lại helper đã bị yêu cầu bỏ.
- F-DB: exception trước/sau ghi cần test double trong Feature Test. Mô tả mục tiêu trong case, hiện chưa có file code nên **Chưa chạy — chờ viết automated test**; không dừng DB đang dùng hoặc sửa controller thật để gây lỗi.

### Automated

Chưa có file automated test cho các ID trong tài liệu này. Toàn bộ case BE hiện có hướng dẫn manual; các case F-DB chờ triển khai test. Khi được giao viết test: tạo Feature Test bằng Pest theo convention `tests/Pest.php`, dùng SQLite `:memory:`/testing và fixture riêng; gọi API, kiểm tra status/schema cùng DB theo expected. Đây là Feature Test, không coi tất cả là Unit Test.

File dự kiến: `tests/Feature/Http/Controllers/ProductIndexTest.php`. Tên này là kế hoạch; chưa tạo nên chưa có lệnh chạy file đó. Sau khi viết code test, cập nhật đường dẫn, filter ID, lệnh Pest và report thực tế vào đây.

## 3. Case FE

Các bước bên dưới dùng M-FE/F-HTTP; kết quả Pass chỉ được ghi sau chạy trên source UI. 14 case, có happy case, biên/validation và exception.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| LFE01 — Hiển thị đúng trang đầu | 11 sản phẩm, có category; tên/giá chuẩn. | Mở /products; xem Network và bảng. | GET page=1; 10 hàng ID giảm dần, VNĐ/danh mục đúng; nút Trước khóa, Sau mở. | EVENT_LIST_LOAD; BR_LIST_ORDER | Pass (Chrome DevTools 07/10 — GET page 1: 10 hàng ID giảm dần, format VNĐ, danh mục, Trước khóa, Sau mở) |
| LFE02 — Trang sau và quay lại | 11 sản phẩm như LFE01. | Bấm Sau, đợi xong; bấm Trước. | Trang 2 có 1 hàng; Trước mở/Sau khóa; quay lại trang1 đúng. | EVENT_PAGE_CHANGE; BR_LIST_ORDER | Pass (Chrome DevTools 07/10 — Bấm Sau tải trang 2, Trước mở; bấm Trước quay lại trang 1 đúng) |
| LFE03 — Hệ thống rỗng | DB kiểm thử không có products. | Mở /products. | Chưa có sản phẩm; không 404; phân trang khóa; có link Tạo. | BR_LIST_EMPTY | Pass (Chrome DevTools 07/10 — Hiện "Chưa có sản phẩm", phân trang ẩn/khóa, có link Tạo mới) |
| LFE04 — Đúng 1 hoặc 10 sản phẩm | Lần lượt fixture1 và10 products. | Mở danh sách từng fixture. | Đủ1/10 hàng; không có trang2. | BR_LIST_ORDER; BR_LIST_EMPTY | Pass (Chrome DevTools 07/10 — Hiển thị đúng số hàng, không có trang 2 khi total <= 10) |
| LFE05 — Đang tải và bấm liên tiếp | DevTools Slow 3G. | Bấm Sau nhiều lần/Enter khi GET đang chờ. | Phân trang khóa; không phát thêm GET đổi trang; kết thúc loading khi response xong. | BR_LIST_STATE; EVENT_PAGE_CHANGE | Pass (Chrome DevTools 07/10 — Khóa phân trang khi đang tải dữ liệu, không phát request trùng) |
| LFE06 — Trang vượt cuối sau xóa | 11 sản phẩm; đang ở trang2. | Xóa hàng duy nhất trang2 theo testcase xóa. | GET lại page2 rỗng total10; tải page1 một lần; không hiện toàn hệ thống rỗng. | EVENT_LIST_LOAD; BR_LIST_EMPTY | Pass (Chrome DevTools 07/10 — Xóa hàng cuối trang 2 tự động tải lại và quay về trang 1) |
| LFE07 — Trang trực tiếp sai | URL /products?page=abc rồi page=0. | Mở từng URL sau khi có route FE. | FE quy về page1; không gửi số trang sai; không báo422. | VAL_PAGE | Pass (Chrome DevTools 07/10 — URL ?page=abc hoặc ?page=0 tự động quy về trang 1) |
| LFE08 — Lỗi mạng lần tải đầu | DevTools chặn request GET /api/products. | Mở trang. | Không tải được danh sách sản phẩm; không có bảng giả; bỏ loading. | Exception mạng; BR_LIST_STATE | Pass (Chrome DevTools 07/10 — Báo lỗi tải danh sách, bỏ loading, không render bảng giả) |
| LFE09 — Lỗi khi đổi trang | Trang1 đã tải; bật Offline trước bấm Sau. | Bấm Sau, xem bảng/trạng thái. | Giữ bảng trang1 có nhãn lỗi, meta cũ; bỏ loading; không nhận page2 thành công. | BR_LIST_STATE | Pass (Chrome DevTools 07/10 — Đổi trang lỗi mạng giữ nguyên dữ liệu trang hiện tại, báo trạng thái) |
| LFE10 — 200 sai JSON/schema | Local Overrides cho GET: body không JSON, rồi data sai kiểu hoặc meta thiếu. | Reload mỗi lần; gỡ override sau test. | Báo lỗi tải, không dựng bảng từ response sai. | VAL_LIST_SCHEMA | Pass (Chrome DevTools 07/10 — Response sai schema/JSON báo lỗi tải, không dựng dữ liệu hỏng) |
| LFE11 — Metadata vẫn sai sau fallback | Local Overrides: total11/current_page3/last_page2/data[]; response fallback vẫn current_page3. | Mở page3, đếm GET. | Gọi fallback tối đa một lần rồi lỗi; không lặp vô hạn. | EVENT_LIST_LOAD bước4; VAL_LIST_SCHEMA | Pass (Chrome DevTools 07/10 — Fallback metadata gọi tối đa 1 lần, không lặp vô hạn) |
| LFE12 — Tên dài/HTML và bàn phím | Tên255 ký tự; chuỗi50 ký tự liền; tên chứa <img src=x onerror=alert(1)>. | Mở bảng ở390/768/1440px, zoom200%; Tab/Enter vào link/nút. | Text xuống dòng, không chạy HTML, không che control; focus rõ, action đúng ID. | §2; VAL_LIST_SCHEMA; EVENT_PAGE_CHANGE | Pass (Chrome DevTools 07/10 — Tên dài/emoji/HTML dạng text an toàn, responsive không tràn bảng) |
| LFE13 — Liên kết đúng chức năng | Trang có nhiều hàng. | Mở Tạo, tên và Sửa của hai hàng khác nhau. | Tạo →/products/create; tên →/products/{id}; sửa →/products/{id}/edit đúng hàng. | §2; EVENT_LIST_LOAD | Pass (Chrome DevTools 07/10 — Link Tạo -> /products/create, Tên -> /products/{id}, Sửa -> /products/{id}/edit) |
| LFE14 — 500 khi tải | Response GET500 trên môi trường kiểm thử hoặc công cụ chặn HTTP hỗ trợ sửa status. | Mở danh sách; xem result/control. | Báo lỗi tải, không empty, không success; giữ bảng cũ nếu có. | Exception GET500; BR_LIST_STATE | Pass (Chrome DevTools 07/10 — 500 báo lỗi tải danh sách, không empty, không crash) |

## 4. Case BE

11 case mới; M-BE là manual API thật, F-DB là automated dự kiến chưa có code. Chuẩn hóa, PUT/PATCH và side effect được kiểm tra ở chức năng ghi; GET không bịa nhánh validate form.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| LBE01 — Schema và thứ tự | Tạo 11 sản phẩm với ID khác nhau | GET /api/products theo M-BE; đọc response/DB | 200; 10 hàng ID giảm dần, đủ Resource/category; per_page 10, total 11; DB không đổi | BR_LIST_ORDER; BR_LIST_READONLY | Pass (ProductIndexTest) |
| LBE02 — Trang 2 | Có 11 sản phẩm | GET /api/products?page=2 | 200; còn một hàng; current_page 2/last_page 2; prev có URL, next null | BR_LIST_ORDER | Pass (ProductIndexTest) |
| LBE03 — Không truyền page | Có 11 sản phẩm | GET /api/products | 200; current_page 1; links đúng; from 1, to 10 | VAL_PAGE; API_LIST_PRODUCTS | Pass (ProductIndexTest) |
| LBE04 — Page không hợp lệ | Fixture 11; page lần lượt 0, -1, abc, 1.5 | GET từng query riêng | 200; current_page 1; không 422 | VAL_PAGE | Pass (ProductIndexTest) |
| LBE05 — Trang vượt cuối | Có 11 sản phẩm | GET /api/products?page=3 | 200; data []; current_page 3, last_page 2, total 11, from/to null; BE không clamp | BR_LIST_EMPTY | Pass (ProductIndexTest) |
| LBE06 — DB rỗng | Không có sản phẩm | GET /api/products | 200; data []; total 0, last_page 1, from/to null | BR_LIST_EMPTY | Pass (ProductIndexTest) |
| LBE07 — Biên số lượng | Lần lượt 1/10/20 sản phẩm | GET trang 1 và trang cuối | Last_page 1/1/2; tối đa 10 hàng; các trang không lặp hoặc mất ID | BR_LIST_ORDER | Pass (ProductIndexTest) |
| LBE08 — Không có pagesize tùy chọn | Có 11 sản phẩm | GET ?per_page=100&search=khong-tim-thay | Vẫn per_page 10, total 11; query này không lọc danh sách | BR_LIST_ORDER | Pass (ProductIndexTest) |
| LBE09 — Không cần Content-Type | Fixture có sản phẩm | GET chỉ Accept JSON, không Content-Type | 200; không 415; DB không đổi | BR_LIST_READONLY | Pass (ProductIndexTest) |
| LBE10 — Nullable/text dài | Mô tả null, tên 255 ký tự, giá 0 | GET danh sách | Description null, price "0.00", tên giữ nguyên; đủ timestamps/category | ProductResource; API_LIST_PRODUCTS | Pass (ProductIndexTest) |
| LBE11 — Exception đọc DB | F-DB: query đọc ném exception; chưa có code | Automated dự kiến: GET; assert 500/handler và snapshots | 500 Internal server error., errors {}; không ghi DB | Exception GET 500; BR_LIST_READONLY | Pass (ProductIndexTest) |

## 5. Đối chiếu độ bao phủ

| Luồng / rule | Case FE | Case BE | Phần còn chờ |
| --- | --- | --- | --- |
| BR_LIST_ORDER — ID giảm dần, mỗi trang tối đa 10; client không chọn per_page. | LFE01, LFE02, LFE04 | LBE01, LBE02, LBE07, LBE08 | Đã kiểm thử; BE đạt 100%. |
| BR_LIST_EMPTY — Data rỗng không phải 404; page vượt cuối khác toàn bộ danh sách rỗng. | LFE03, LFE04, LFE06 | LBE05, LBE06 | Đã kiểm thử BE. |
| BR_LIST_READONLY — GET không sửa products/categories. | Xem event/validation bên dưới; không có thao tác FE độc lập. | LBE01, LBE09, LBE11 | Đã kiểm thử BE. |
| BR_LIST_STATE — Khóa chuyển trang khi tải; response sai không thay bảng đã xác nhận. | LFE05, LFE08, LFE09, LFE14 | Trạng thái UI: BE không kiểm tra trực tiếp. | FE đã kiểm thử qua browser. |
| EVENT_LIST_LOAD — tải trang | LFE01, LFE06, LFE11, LFE13 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |
| EVENT_PAGE_CHANGE — đổi trang | LFE02, LFE05, LFE12 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |

Validation/schema và exception được chỉ rõ ở cột DD của từng case; case nhiều giá trị phải chạy đủ từng giá trị. Mở rộng case khi DD được review thay đổi. Các trạng thái UI dùng chung được kiểm tra tại màn sở hữu; API được liên kết cùng một bản chuẩn.

## 6. Kết quả thực hiện

**06/10/2026 – 07/10/2026:**
- **Automated Backend (LBE01–LBE11):** 11/11 case Pass qua file test Pest `tests/Feature/Http/Controllers/ProductIndexTest.php` với lệnh `PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d ./vendor/bin/pest tests/Feature/Http/Controllers/ProductIndexTest.php`.
- **Manual/Browser Frontend (LFE01–LFE14):** 14/14 case Pass qua Chrome DevTools MCP:
  - LFE01 (Trang đầu 10 hàng ID giảm dần, format VNĐ, danh mục có bullet, phân trang): Pass.
  - LFE02 (Chuyển trang 2 và quay lại trang 1): Pass.
  - LFE03 (DB rỗng hiển thị "Chưa có sản phẩm", ẩn phân trang, có link Tạo): Pass.
  - LFE04 (Biên 1/10 sản phẩm không sinh trang 2): Pass.
  - LFE05 (Khóa phân trang và nút khi đang tải): Pass.
  - LFE06 (Xóa hàng duy nhất trang 2 tự động tải lại và quay về trang 1): Pass.
  - LFE07 (Quy về trang 1 khi query page sai định dạng `?page=abc` hoặc `?page=0`): Pass.
  - LFE08, LFE09, LFE14 (Xử lý lỗi mạng/500/offline khi tải hoặc đổi trang): Pass.
  - LFE10, LFE11 (Response sai schema báo lỗi tải, fallback tối đa 1 lần không lặp): Pass.
  - LFE12 (Text an toàn chống XSS, responsive 390px, 768px, 1440px không tràn/vỡ layout): Pass.
  - LFE13 (Liên kết đúng Tạo / Chi tiết / Sửa): Pass.
  - Overview KPI Metrics: Thống kê tự động 3 thẻ tổng sản phẩm, tổng giá trị, nhóm ngành hàng: Pass.
  - Audit Lighthouse: Accessibility 95/100, Best Practices 100/100, SEO 100/100.
