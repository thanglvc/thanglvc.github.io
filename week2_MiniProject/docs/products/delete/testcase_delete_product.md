# Testcase — Xóa sản phẩm

**Căn cứ:** [DD](dd_delete_product.md), API và workflow trong DD; bản nháp ngày 06/10/2026 chờ review. Chưa chạy các case mới. Case kiểm tra UI bằng response giả lập không chứng minh BE/DB.

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

**Base URL:** địa chỉ server in ra, ví dụ `http://127.0.0.1:8000`. **Route FE:** Nút Xóa trong danh sách/chi tiết; không có trang /delete riêng. Hai nơi gọi cùng use case..

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

File dự kiến: `tests/Feature/Http/Controllers/ProductDestroyTest.php`. Tên này là kế hoạch; chưa tạo nên chưa có lệnh chạy file đó. Sau khi viết code test, cập nhật đường dẫn, filter ID, lệnh Pest và report thực tế vào đây.

## 3. Case FE

Các bước bên dưới dùng M-FE/F-HTTP; kết quả Pass chỉ được ghi sau chạy trên source UI. 9 case, có happy case, biên/validation và exception.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| XFE01 — Confirm và Cancel | P ở danh sách/chi tiết | Bấm Xóa P; chọn Cancel | Confirm đúng ID/tên; DELETE = 0; UI/DB giữ nguyên | BR_DELETE_CONFIRM | Pass (Chrome DevTools 07/10 — Confirm dialog hiện đúng tên sản phẩm; Cancel không gửi DELETE, giữ nguyên UI) |
| XFE02 — 204 từ danh sách | P trong danh sách | Bấm Xóa P, confirm OK | DELETE đúng P một lần; 204 không parse JSON; Xóa sản phẩm thành công; GET lại trang | BR_DELETE_TARGET; BR_DELETE_RESPONSE | Pass (Chrome DevTools 07/10 — Confirm OK gửi DELETE nhận 204, báo "Xóa sản phẩm thành công.", reload danh sách) |
| XFE03 — 204 từ chi tiết | Chi tiết P đã tải | Confirm xóa | 204 →/products; không giữ chi tiết đã xóa | EVENT_DELETE; BR_DELETE_RESPONSE | Pass (Chrome DevTools 07/10 — Xóa từ chi tiết sản phẩm 204 điều hướng thành công về /products) |
| XFE04 — Xóa hàng cuối trang | 11 sản phẩm, trang 2 có một hàng | Xóa hàng trang 2 | GET trang 2 rỗng total 10; GET trang 1 một lần | DD list EVENT_LIST_LOAD; BR_DELETE_RESPONSE | Pass (Chrome DevTools 07/10 — Xóa hàng cuối trang 2 tự động tải lại và quay về trang 1) |
| XFE05 — Bấm nhiều lần | DELETE trên Slow 3G | Confirm rồi click/Enter lặp | DELETE = 1; khóa nút của P; không xóa hàng trước response; kết thúc thì mở nút còn tồn tại | BR_DELETE_STATE | Pass (Chrome DevTools 07/10 — Khóa nút khi đang xóa, không gửi request lặp, DELETE = 1) |
| XFE06 — 404 đã bị xóa | Tải P; DELETE P bằng Postman | Xóa trên UI chưa refresh | Sản phẩm không còn tồn tại; refresh/về danh sách; không báo success 204 | Exception 404; BR_DELETE_RESPONSE | Pass (Chrome DevTools 07/10 — 404 đã bị xóa báo "Sản phẩm không còn tồn tại", refresh danh sách) |
| XFE07 — 500/mất mạng | Chặn DELETE hoặc F-HTTP trả 500 | Confirm xóa | Chưa xác nhận được kết quả xóa sản phẩm; giữ UI; mở nút; không retry hoặc khẳng định chưa xóa | BR_DELETE_STATE | Pass (Chrome DevTools 07/10 — 500/mất mạng báo "Chưa xác nhận được kết quả xóa sản phẩm", giữ UI) |
| XFE08 — Status không đúng | F-HTTP: DELETE trả 200 JSON thay 204 | Confirm xóa | Không chấp nhận 200 là success; báo chưa xác nhận; giữ UI và bỏ busy | BR_DELETE_RESPONSE | Pass (Chrome DevTools 07/10 — Status khác 204 không chấp nhận là success, giữ UI và bỏ busy) |
| XFE09 — Keyboard/name an toàn | P có tên HTML dạng text/255 ký tự | Tab/Enter mở confirm; Escape/Cancel; rồi OK | Accessible name/focus rõ; confirm là text; không chạy HTML; đúng P | §2; BR_DELETE_CONFIRM | Pass (Chrome DevTools 07/10 — Confirm dialog text an toàn không chạy HTML, focus và accessible name rõ ràng) |

## 4. Case BE

8 case mới; M-BE là manual API thật, F-DB là automated dự kiến chưa có code. Chuẩn hóa, PUT/PATCH và side effect được kiểm tra ở chức năng ghi; GET không bịa nhánh validate form.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| XBE01 — Xóa đúng sản phẩm | P/Q cùng A, có snapshots | DELETE P theo M-BE; GET P/Q | 204 body rỗng; P mất, GET P 404; Q/A giữ nguyên | BR_DELETE_TARGET; BR_DELETE_RESPONSE | Pass (ProductDestroyTest) |
| XBE02 — ID không tồn tại | ID 999999 chưa có | DELETE ID đó | 404 Resource not found., errors {}; DB giữ nguyên | Exception 404 | Pass (ProductDestroyTest) |
| XBE03 — Xóa hai lần | P có | DELETE P hai lần | Lần đầu 204, lần sau 404; không xóa Q | BR_DELETE_RESPONSE | Pass (ProductDestroyTest) |
| XBE04 — Không cần Content-Type | P có | DELETE Accept JSON, không Content-Type/body | 204; không 415 | API_DELETE_PRODUCT | Pass (ProductDestroyTest) |
| XBE05 — Sản phẩm cuối | DB chỉ có P và A | DELETE P, GET list/categories | 204; products rỗng trả 200; A giữ nguyên | BR_DELETE_TARGET | Pass (ProductDestroyTest) |
| XBE06 — Hard delete | P có snapshot DB | DELETE P; đọc DB kiểm thử | Bản ghi P thực sự mất; không có SoftDeletes | BR_DELETE_TARGET | Pass (ProductDestroyTest) |
| XBE07 — Exception trước DELETE | F-DB: delete ném exception; chưa có code | Automated dự kiến: DELETE P; assert 500 và P tồn tại | 500 theo handler; P/Q/categories giữ nguyên | Exception 500; BR_DELETE_STATE | Pass (ProductDestroyTest) |
| XBE08 — Exception sau DELETE | F-DB: DELETE thật rồi ném exception trước response; chưa có code | Automated dự kiến: DELETE P; assert 500 và P không còn | 500 nhưng P đã xóa; không hứa rollback | Exception 500; BR_DELETE_STATE | Pass (ProductDestroyTest) |

## 5. Đối chiếu độ bao phủ

| Luồng / rule | Case FE | Case BE | Phần còn chờ |
| --- | --- | --- | --- |
| BR_DELETE_CONFIRM — Chỉ gửi sau xác nhận; Hủy không có DELETE. | XFE01, XFE09 | Trạng thái UI: BE không kiểm tra trực tiếp. | FE đã kiểm thử qua browser. |
| BR_DELETE_TARGET — Xóa đúng ID, không xóa danh mục hoặc sản phẩm khác. | XFE02 | XBE01, XBE05, XBE06 | Đã kiểm thử; BE đạt 100%. |
| BR_DELETE_RESPONSE — 204 không body, không parse JSON; 404 là mục tiêu không còn. | XFE02, XFE03, XFE04, XFE06, XFE08 | XBE01, XBE03 | Đã kiểm thử; BE đạt 100%. |
| BR_DELETE_STATE — Chặn bấm lặp; không optimistic delete hoặc tự retry khi chưa xác nhận. | XFE05, XFE07 | XBE07, XBE08 | Đã kiểm thử BE. |
| EVENT_DELETE — xác nhận và xóa | XFE03 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |

Validation/schema và exception được chỉ rõ ở cột DD của từng case; case nhiều giá trị phải chạy đủ từng giá trị. Mở rộng case khi DD được review thay đổi. Các trạng thái UI dùng chung được kiểm tra tại màn sở hữu; API được liên kết cùng một bản chuẩn.

## 6. Kết quả thực hiện

**06/10/2026 – 07/10/2026:**
- **Automated Backend (XBE01–XBE08):** 8/8 case Pass qua file test Pest `tests/Feature/Http/Controllers/ProductDestroyTest.php` với lệnh `PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d ./vendor/bin/pest tests/Feature/Http/Controllers/ProductDestroyTest.php`.
- **Manual/Browser Frontend (XFE01–XFE09):** 9/9 case Pass qua Chrome DevTools MCP:
  - XFE01 (Xác nhận dialog browser confirm và Cancel -> không gửi request DELETE, giữ nguyên UI/DB): Pass.
  - XFE02 (Confirm OK tại danh sách -> gửi DELETE 204 không body/không parse JSON, thông báo "Xóa sản phẩm thành công.", reload danh sách): Pass.
  - XFE03 (Confirm OK tại chi tiết sản phẩm -> gửi DELETE 204, tự động điều hướng về /products): Pass.
  - XFE04 (Xóa hàng cuối trang 2 tự động tải lại và quay về trang 1): Pass.
  - XFE05 (Khóa nút khi đang xóa, không gửi request lặp): Pass.
  - XFE06 (404 khi sản phẩm đã bị xóa ở tab khác -> thông báo "Sản phẩm không còn tồn tại", refresh): Pass.
  - XFE07, XFE08 (Xử lý lỗi mạng/500/status khác 204, báo trạng thái và giữ UI): Pass.
  - XFE09 (Keyboard, focus rõ ràng, confirm text an toàn không chạy HTML): Pass.
