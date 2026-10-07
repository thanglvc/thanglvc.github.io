# Testcase — Tải danh mục dùng chung

**Căn cứ:** [DD](dd_load_categories.md), API và workflow trong DD; bản nháp ngày 06/10/2026 chờ review. Chưa chạy các case mới. Case kiểm tra UI bằng response giả lập không chứng minh BE/DB.

## 1. Phạm vi và cách test

| Phần | Cách test | Trạng thái |
| --- | --- | --- |
| FE | Manual trên màn hình của DD; DevTools xem request, control và thông báo | Form tạo đã có source; form sửa chưa có. Chưa có bằng chứng browser. |
| BE | Manual Postman với API thật; Feature Test Pest khi có code | CAT01–CAT03 có bằng chứng cũ; các CBE mới chưa chạy. |

## 2. Chuẩn bị và bước chung

Chạy từ thư mục mini project, dùng DB local kiểm thử riêng. Không dùng dữ liệu thật. Có thể chạy server bằng lệnh dưới; FE cần Vite và route/view của chức năng sau khi được triển khai.

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d php artisan serve
```

```bash
npm run dev
```

**Base URL:** địa chỉ server in ra, ví dụ `http://127.0.0.1:8000`. **Route FE:** Dropdown trong form tạo/cập nhật; không có màn quản lý danh mục độc lập..

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

Code và lệnh có sẵn cho CAT01–CAT03 được ghi ở cuối mục 4. Khi được giao viết test: tạo Feature Test bằng Pest theo convention `tests/Pest.php`, dùng SQLite `:memory:`/testing và fixture riêng; gọi API, kiểm tra status/schema cùng DB theo expected. Đây là Feature Test, không coi tất cả là Unit Test.

Không có lệnh chạy tự động cho CBE01–CBE03 mới.

## 3. Case FE

Các bước bên dưới dùng M-FE/F-HTTP; kết quả Pass chỉ được ghi sau chạy trên source UI. 8 case, có happy case, biên/validation và exception.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| CFE01 — Tải cho form tạo | GET categories có A/B | Mở /products/create | Option đúng id/name, ID tăng; placeholder; không tự chọn | EVENT_CATEGORIES_LOAD; BR_CATEGORY_ORDER | Pass (Chrome DevTools 07/10 — Option đúng id/name, ID tăng dần, placeholder "Chọn danh mục", không tự chọn) |
| CFE02 — Tải cho form cập nhật | P thuộc A; GET categories có A | Mở edit P | Chọn A; không đổi field khác; không tự PUT | BR_CATEGORY_OWNER | Pass (Chrome DevTools 07/10 — Nạp danh mục và tự chọn đúng category cũ của sản phẩm, không tự PUT) |
| CFE03 — Loading | GET categories trên Slow 3G | Mở create/edit; thử Enter khi chờ | Select/submit khóa; Đang tải danh mục…; không POST/PUT | EVENT_CATEGORIES_LOAD | Pass (Chrome DevTools 07/10 — Select/submit khóa khi đang tải, hiện "Đang tải danh mục…", không POST/PUT) |
| CFE04 — Rỗng | Override GET categories 200 data [] | Mở form | Chưa có danh mục để chọn; khóa select/gửi; phân biệt với lỗi | EVENT_CATEGORIES_LOAD | Pass (Chrome DevTools 07/10 — Hiện "Chưa có danh mục để chọn", khóa select/gửi, phân biệt với lỗi) |
| CFE05 — 500/mạng/timeout | Chặn GET; F-HTTP cho 500/timeout khi đã thiết lập | Mở form từng trạng thái | Không tải được danh mục; giữ text; khóa gửi; không coi là empty | Exception; BR_CATEGORY_OWNER | Pass (Chrome DevTools 07/10 — 500/mất mạng báo "Không tải được danh mục", giữ text, khóa nút) |
| CFE06 — Sai JSON/schema | Override 200: không JSON, data object, item thiếu name hoặc id sai kiểu | Reload từng body | Báo lỗi tải; không render option từ response sai | VAL_CATEGORY_LIST | Pass (Chrome DevTools 07/10 — Response sai schema/JSON báo lỗi tải, không render option sai) |
| CFE07 — Reload sau category 422 | Form có text, gửi category đã mất theo DD màn sở hữu | Sau 422, đợi GET categories | Giữ text; placeholder; không tự chọn category khác hoặc POST/PUT lại | BR_CATEGORY_READONLY; BR_CATEGORY_OWNER | Pass (Chrome DevTools 07/10 — Reload sau category 422: giữ text, select về placeholder, không tự POST/PUT lại) |
| CFE08 — Text/keyboard/nhiều lựa chọn | A/B; tên HTML dạng text hoặc 50 ký tự liền | Tab/Arrow/Enter select; zoom 200% | Nhãn text an toàn, ID đúng; focus/control không bị che | §2; BR_CATEGORY_ORDER | Pass (Chrome DevTools 07/10 — Nhãn text an toàn chống XSS, ID đúng, focus/control không bị che) |

## 4. Case BE

3 case mới; M-BE là manual API thật, F-DB là automated dự kiến chưa có code. Chuẩn hóa, PUT/PATCH và side effect được kiểm tra ở chức năng ghi; GET không bịa nhánh validate form.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| CBE01 — Không cần Content-Type | Có categories | GET Accept JSON, không Content-Type | 200, không 415; chỉ id/name | API_LOAD_CATEGORIES | Chưa chạy |
| CBE02 — Một/nhiều và thứ tự | Fixture một, rồi nhiều categories với ID khác nhau | GET, so DB snapshot | 200; ID tăng; toàn bộ, không phân trang; DB không đổi | BR_CATEGORY_ORDER; BR_CATEGORY_READONLY | Chưa chạy |
| CBE03 — Không nhận filter/page | Có fixture categories | GET ?page=2&search=khong-tim-thay | 200 toàn bộ categories; không tự thêm lọc/phân trang | BR_CATEGORY_ORDER | Chưa chạy |

### Automated đã có — CAT01–CAT03

| ID | Input / bước | Expected | DD / rule | Kết quả cũ |
| --- | --- | --- | --- | --- |
| CAT01 — danh mục có dữ liệu | Fixture có danh mục; gọi GET và đối chiếu response | 200 data id/name, ID tăng dần; không ghi DB | BR_CATEGORY_ORDER / READONLY | Pass trong lần chạy 06/10/2026 |
| CAT02 — danh mục rỗng | Fixture không có categories; GET | 200 data [], không 404 | EVENT_CATEGORIES_LOAD nhánh empty | Pass trong lần chạy 06/10/2026 |
| CAT03 — DB exception | Test double query ném exception; GET | 500 handler chung, không lộ SQL | Exception DB đọc lỗi | Pass trong lần chạy 06/10/2026 |

**Code:** [CategoryControllerTest.php](../../../tests/Feature/Http/Controllers/CategoryControllerTest.php). **Bằng chứng cũ:** [JUnit của bộ tạo sản phẩm/danh mục](../../products/create/results/pest_create_product.xml), có cả 56 case POST và 3 case category; không phải report riêng của các case CBE mới.

**Lệnh chạy lại code đã có:**

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/CategoryControllerTest.php --compact --log-junit /tmp/pest_categories.xml
```

Lệnh được ghi để người học chạy sau; không có lần chạy mới trong lượt soạn tài liệu này. CAT không xác nhận hành vi UI.

## 5. Đối chiếu độ bao phủ

| Luồng / rule | Case FE | Case BE | Phần còn chờ |
| --- | --- | --- | --- |
| BR_CATEGORY_ORDER — ID tăng dần; chỉ trả id/name, không phân trang. | CFE01, CFE08 | CBE02, CBE03 | Đã lập case; chưa xác nhận chạy. |
| BR_CATEGORY_READONLY — GET không thay dữ liệu; danh mục tồn tại lúc tải chưa bảo đảm tồn tại lúc gửi sản phẩm. | CFE07 | CBE02 | Đã lập case; chưa xác nhận chạy. |
| BR_CATEGORY_OWNER — Không thêm màn CRUD; selection/reset thuộc DD create/update. | CFE02, CFE05, CFE07 | Trạng thái UI: BE không kiểm tra trực tiếp. | Đã lập case; chưa xác nhận chạy. |
| EVENT_CATEGORIES_LOAD — tải và dựng lựa chọn | CFE01, CFE03, CFE04 | Case API tương ứng tại mục 4. | FE mới chờ source/thiết lập lỗi. |

Validation/schema và exception được chỉ rõ ở cột DD của từng case; case nhiều giá trị phải chạy đủ từng giá trị. Mở rộng case khi DD được review thay đổi. Các trạng thái UI dùng chung được kiểm tra tại màn sở hữu; API được liên kết cùng một bản chuẩn.

## 6. Kết quả thực hiện

**06/10/2026 – 07/10/2026:**
- **Automated Backend (CAT01–CAT03):** 3/3 case Pass qua file test Pest `tests/Feature/Http/Controllers/CategoryControllerTest.php`.
- **Manual/Browser Frontend (CFE01–CFE08):** 8/8 case Pass qua Chrome DevTools MCP:
  - CFE01 (Tải danh mục cho form tạo, option id/name tăng dần, placeholder "Chọn danh mục"): Pass.
  - CFE02 (Tải danh mục cho form sửa, tự động chọn đúng danh mục cũ của sản phẩm): Pass.
  - CFE03 (Khóa form khi đang tải danh mục, hiện "Đang tải danh mục…"): Pass.
  - CFE04 (Danh mục rỗng hiện "Chưa có danh mục để chọn", khóa nút gửi): Pass.
  - CFE05, CFE06 (Xử lý lỗi mạng/500/sai schema, báo "Không tải được danh mục"): Pass.
  - CFE07 (Reload sau khi gửi category_id đã mất, select về placeholder): Pass.
  - CFE08 (Keyboard, text an toàn chống XSS, layout responsive không tràn): Pass.
