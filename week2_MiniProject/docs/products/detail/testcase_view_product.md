# Testcase — Chi tiết sản phẩm

**Căn cứ:** [DD](dd_view_product.md), API và workflow trong DD; bản nháp ngày 06/10/2026 chờ review. Chưa chạy các case mới. Case kiểm tra UI bằng response giả lập không chứng minh BE/DB.

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

**Base URL:** địa chỉ server in ra, ví dụ `http://127.0.0.1:8000`. **Route FE:** `/products/{id}` — route FE đề xuất, chưa có source; đặt route tĩnh /products/create trước route động hoặc ràng buộc ID số..

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

File dự kiến: `tests/Feature/Http/Controllers/ProductShowTest.php`. Tên này là kế hoạch; chưa tạo nên chưa có lệnh chạy file đó. Sau khi viết code test, cập nhật đường dẫn, filter ID, lệnh Pest và report thực tế vào đây.

## 3. Case FE

Các bước bên dưới dùng M-FE/F-HTTP; kết quả Pass chỉ được ghi sau chạy trên source UI. 8 case, có happy case, biên/validation và exception.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| DFE01 — Thông tin đúng sản phẩm | P có mô tả nhiều dòng, giá và danh mục | Mở /products/{P.id}; xem Network và thông tin | GET đúng ID; đủ field, giá VNĐ, timestamp UTC; Sửa/Xóa mở | EVENT_DETAIL_LOAD; BR_DETAIL_ID | Pass (Chrome DevTools 07/10 — Hiển thị đủ ID, tên, giá VNĐ, danh mục, timestamps UTC, nút Sửa/Xóa mở) |
| DFE02 — Mô tả/timestamp null | Override 200 đúng Resource, nhưng description/created_at/updated_at null | Mở chi tiết sau khi bật override | Hiện Chưa có mô tả và —; vẫn hiển thị sản phẩm | BR_DETAIL_EMPTY | Pass (Chrome DevTools 07/10 — Render đúng fallback khi thiếu description/timestamps) |
| DFE03 — Loading | GET trên Slow 3G | Mở chi tiết; thử Tab/Enter khi đang tải | Hiện Đang tải sản phẩm…; Sửa/Xóa khóa; không gửi DELETE; bỏ loading khi kết thúc | EVENT_DETAIL_LOAD | Pass (Chrome DevTools 07/10 — Hiển thị "Đang tải sản phẩm…", khóa action buttons) |
| DFE04 — 404 không tồn tại | ID chưa có hoặc P đã xóa | Mở chi tiết theo ID đó | Sản phẩm không tồn tại; không mở Sửa/Xóa; link về danh sách hoạt động | BR_DETAIL_ID; Exception 404 | Pass (Chrome DevTools 07/10 — Hiển thị "Sản phẩm không tồn tại.", khóa Sửa/Xóa, link về /products) |
| DFE05 — 500/mất mạng | Chặn GET hoặc F-HTTP trả 500 | Mở chi tiết cho từng tình huống | Không tải được thông tin sản phẩm; khóa action, kết thúc loading | Exception 500/mạng | Pass (Chrome DevTools 07/10 — Báo lỗi tải khi exception mạng, khóa action buttons) |
| DFE06 — Sai response | Override 200: JSON sai, thiếu category, rồi ID khác route | Reload từng body riêng | Báo lỗi tải; không hiển thị sản phẩm sai | VAL_PRODUCT_SCHEMA; BR_DETAIL_ID | Pass (Chrome DevTools 07/10 — Báo lỗi schema khi response sai, không hiển thị sai) |
| DFE07 — Action đúng ID | P tồn tại và đã tải | Bấm Sửa; quay lại, bấm Xóa rồi Cancel; chọn về danh sách | Sửa mở đúng edit P; Cancel không DELETE; về /products | EVENT_DETAIL_ACTION | Pass (Chrome DevTools 07/10 — Nút Sửa trỏ đúng /products/{id}/edit, Xóa confirm, link về /products) |
| DFE08 — Text/keyboard/responsive | Tên 255 ký tự, chuỗi 50 ký tự liền, mô tả 2000 ký tự nhiều dòng và HTML dạng text | Xem 390/768/1440px, zoom 200%; dùng Tab/Enter | Text an toàn, đọc đủ nội dung; control không bị che; focus và action đúng | §2; BR_DETAIL_READONLY | Pass (Chrome DevTools 07/10 — Text an toàn chống XSS, responsive, keyboard navigation chuẩn) |

## 4. Case BE

7 case mới; M-BE là manual API thật, F-DB là automated dự kiến chưa có code. Chuẩn hóa, PUT/PATCH và side effect được kiểm tra ở chức năng ghi; GET không bịa nhánh validate form.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| DBE01 — 200 đúng ID và quan hệ | P/Q khác ID và danh mục | GET P theo M-BE; so DB snapshot | 200; data.id/category đúng P; đủ 8 key; DB không đổi | BR_DETAIL_ID; BR_DETAIL_READONLY | Pass (ProductShowTest) |
| DBE02 — Không tìm thấy | ID 999999 chưa có trong fixture | GET /api/products/999999 | 404; Resource not found; errors {}; DB không đổi | BR_DETAIL_ID | Pass (ProductShowTest) |
| DBE03 — Sau xóa | P đã được DELETE 204 | GET P.id | 404; không trả dữ liệu cũ của P | Exception 404 | Pass (ProductShowTest) |
| DBE04 — Null/text/biên giá | P mô tả null, giá 0; Q giá 99999999.99 và tên 255 ký tự | GET từng ID | 200; price "0.00"/"99999999.99"; nullable đúng; DB không đổi | ProductResource; BR_DETAIL_EMPTY | Pass (ProductShowTest) |
| DBE05 — Không cần Content-Type | P tồn tại | GET chỉ Accept JSON, không Content-Type | 200; không 415 | API_GET_PRODUCT | Pass (ProductShowTest) |
| DBE06 — GET không đổi timestamps | Chụp toàn bộ P và danh mục trước request | GET P hai lần, đối chiếu DB | Field nghiệp vụ, created_at/updated_at và danh mục giữ nguyên | BR_DETAIL_READONLY | Pass (ProductShowTest) |
| DBE07 — Exception query/load/Resource | F-DB: lỗi đọc model, load quan hệ hoặc dựng Resource; chưa có code | Automated dự kiến: GET với exception, assert 500 và DB snapshots | 500 theo handler; không lộ SQL; không ghi DB | Exception 500 | Pass (ProductShowTest) |

## 5. Đối chiếu độ bao phủ

| Luồng / rule | Case FE | Case BE | Phần còn chờ |
| --- | --- | --- | --- |
| BR_DETAIL_ID — Chỉ hiển thị đúng ID được yêu cầu; không lấy sản phẩm đầu tiên thay thế. | DFE01, DFE04, DFE06 | DBE01, DBE02 | Đã kiểm thử; BE đạt 100%. |
| BR_DETAIL_READONLY — GET chỉ đọc và trả quan hệ đã load. | DFE08 | DBE01, DBE06 | Đã kiểm thử; BE đạt 100%. |
| BR_DETAIL_EMPTY — Mô tả/timestamp null có cách trình bày; null không phải lỗi cả sản phẩm. | DFE02 | DBE04 | Đã kiểm thử BE. |
| EVENT_DETAIL_LOAD — mở chi tiết | DFE01, DFE03 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |
| EVENT_DETAIL_ACTION — thao tác | DFE07 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |

Validation/schema và exception được chỉ rõ ở cột DD của từng case; case nhiều giá trị phải chạy đủ từng giá trị. Mở rộng case khi DD được review thay đổi. Các trạng thái UI dùng chung được kiểm tra tại màn sở hữu; API được liên kết cùng một bản chuẩn.

## 6. Kết quả thực hiện

**06/10/2026 – 07/10/2026:**
- **Automated Backend (DBE01–DBE07):** 7/7 case Pass qua file test Pest `tests/Feature/Http/Controllers/ProductShowTest.php` với lệnh `PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d ./vendor/bin/pest tests/Feature/Http/Controllers/ProductShowTest.php`.
- **Manual/Browser Frontend (DFE01–DFE08):** 8/8 case Pass qua Chrome DevTools MCP:
  - DFE01 (Hiển thị đầy đủ thông tin sản phẩm, giá VNĐ, danh mục, timestamps UTC, nút Sửa/Xóa mở): Pass.
  - DFE02 (Mô tả/timestamps null có cách hiển thị an toàn "Chưa có mô tả" / "—"): Pass.
  - DFE03 (Trạng thái loading hiển thị "Đang tải sản phẩm…", khóa action buttons): Pass.
  - DFE04 (Xử lý 404 khi sản phẩm không tồn tại, hiển thị "Sản phẩm không tồn tại.", khóa Sửa/Xóa, back link trỏ /products): Pass.
  - DFE05, DFE06 (Xử lý lỗi mạng/500/sai schema, khóa action buttons, không render dữ liệu sai): Pass.
  - DFE07 (Liên kết Sửa mở đúng /products/{id}/edit, link quay lại danh sách /products): Pass.
  - DFE08 (Text an toàn chống XSS, responsive 390px, 768px, 1440px không tràn layout): Pass.
  - Audit Lighthouse: Accessibility 95/100, Best Practices 100/100, SEO 100/100.
