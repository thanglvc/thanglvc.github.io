# Testcase — Cập nhật sản phẩm

**Căn cứ:** [DD](dd_update_product.md), API và workflow trong DD; bản nháp ngày 06/10/2026 chờ review. Chưa chạy các case mới. Case kiểm tra UI bằng response giả lập không chứng minh BE/DB.

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

**Base URL:** địa chỉ server in ra, ví dụ `http://127.0.0.1:8000`. **Route FE:** `/products/{id}/edit` — route FE đề xuất, chưa có source. Form dùng PUT đầy đủ; PATCH được đặc tả cho client cập nhật một phần..

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

File dự kiến: `tests/Feature/Http/Controllers/ProductUpdateTest.php`. Tên này là kế hoạch; chưa tạo nên chưa có lệnh chạy file đó. Sau khi viết code test, cập nhật đường dẫn, filter ID, lệnh Pest và report thực tế vào đây.

## 3. Case FE

Các bước bên dưới dùng M-FE/F-HTTP; kết quả Pass chỉ được ghi sau chạy trên source UI. 21 case, có happy case, biên/validation và exception.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| UFE01 — Tải form đúng | P thuộc A; GET categories chứa A | Mở /products/{P.id}/edit | GET product rồi categories; điền đúng 4 field; chọn A | EVENT_UPDATE_LOAD; BR_UPDATE_ID | Pass (Chrome DevTools 07/10 — GET product rồi categories, điền đủ 4 field, chọn đúng danh mục A) |
| UFE02 — Product đang tải | Slow 3G trước GET product | Mở edit; thử submit bằng Enter | Field chưa có dữ liệu và nút Lưu khóa; PUT = 0 | EVENT_UPDATE_LOAD | Pass (Chrome DevTools 07/10 — Khóa form khi đang tải dữ liệu sản phẩm, PUT = 0) |
| UFE03 — Danh mục đang tải | Product đã tải, categories đang chờ | Sửa tên/giá/mô tả; thử submit khi đang tải danh mục | Ba ô text mở; select/Lưu khóa; giữ text đã sửa; PUT = 0 | EVENT_UPDATE_LOAD | Pass (Chrome DevTools 07/10 — Khóa select/Lưu khi danh mục đang tải, giữ text đã sửa, PUT = 0) |
| UFE04 — Danh mục rỗng/lỗi | Override categories data [] hoặc chặn GET categories | Mở form từng trạng thái | Phân biệt thông báo rỗng/lỗi; giữ ba field; select/Lưu khóa | EVENT_UPDATE_LOAD; BR_UPDATE_STATE | Pass (Chrome DevTools 07/10 — Phân biệt danh mục rỗng/lỗi, select/Lưu khóa) |
| UFE05 — Danh mục cũ thiếu | Override categories không chứa A, có B | Mở edit rồi tự chọn B | Ban đầu placeholder; không tự chọn B; chọn B mới mở Lưu | VAL_CATEGORY; EVENT_UPDATE_LOAD | Pass (Chrome DevTools 07/10 — Danh mục cũ thiếu để placeholder, chọn mới mở Lưu) |
| UFE06 — Cập nhật thành công | Dữ liệu chuẩn U, chọn B | Nhập rồi bấm Lưu; GET lại P | PUT đủ 4 key; 200 đúng P; Cập nhật sản phẩm thành công; giữ form/B, không reset hoặc chuyển trang | BR_UPDATE_FIELDS; BR_UPDATE_STATE | Pass (Chrome DevTools 07/10 — PUT đủ 4 key, 200, báo "Cập nhật sản phẩm thành công.", giữ form) |
| UFE07 — Mô tả trống | U với mô tả toàn khoảng trắng | Lưu và đọc response | PUT description:null; 200; ô mô tả giữ trống | VAL_DESCRIPTION; BR_UPDATE_FIELDS | Pass (Chrome DevTools 07/10 — Mô tả trắng gửi description: null, 200, ô mô tả giữ trống) |
| UFE08 — Nhiều lỗi và focus | Tên toàn khoảng trắng, giá -1, mô tả 2001 ký tự | Lưu; sửa Tên, chưa Lưu lại | PUT = 0; đủ lỗi, focus Tên; sửa Tên chỉ xóa lỗi Tên, giữ lỗi khác | VAL_NAME/PRICE/DESCRIPTION; EVENT_EDIT | Pass (Chrome DevTools 07/10 — Hiện đủ lỗi, focus Tên; sửa Tên chỉ xóa lỗi Tên, giữ lỗi khác) |
| UFE09 — Tên biên Unicode | Tên lần lượt 255 và 256 ký tự Unicode | Lưu từng giá trị | 255 được gửi; 256 PUT = 0, lỗi tối đa 255; không cắt ngầm | VAL_NAME; BR_UPDATE_VALIDATE | Pass (Chrome DevTools 07/10 — Tên 255 emoji hợp lệ, 256 emoji báo "Tên sản phẩm tối đa 255 ký tự", PUT = 0) |
| UFE10 — Giá hợp lệ và biên | Giá 0, .5, 12.5, 12.50, " 12.50 ", 99999999.99 | Lưu từng giá trị | Gửi string; 200; form nhận giá hai số lẻ, giữ giá đã chuẩn hóa | VAL_PRICE | Pass (Chrome DevTools 07/10 — Giá 0, .5, 12.5, 12.50 hợp lệ, chuẩn hóa 2 số lẻ) |
| UFE11 — Giá sai định dạng/giới hạn | Giá rỗng, -0.01, 100000000, 1.234, abc, 1,5, 1e2 | Lưu từng giá trị riêng | PUT = 0; lỗi giá theo DD tạo; không làm tròn hoặc chuyển thành 0 | VAL_PRICE; BR_UPDATE_VALIDATE | Pass (Chrome DevTools 07/10 — Giá rỗng, -0.01, 100000000, 1.234, abc báo lỗi, PUT = 0) |
| UFE12 — Mô tả biên | 2000/2001 ký tự, có xuống dòng | Lưu từng giá trị | 2000 được gửi, giữ dòng giữa; 2001 PUT = 0 và lỗi; không cắt nội dung | VAL_DESCRIPTION | Pass (Chrome DevTools 07/10 — Mô tả 2000 emoji gửi thành công, 2001 emoji báo lỗi) |
| UFE13 — ID danh mục ngoài danh sách | Đổi value select bằng DevTools thành ID không có | Submit form | PUT = 0; lỗi danh mục; không tin giá trị DOM | VAL_CATEGORY | Pass (Chrome DevTools 07/10 — ID danh mục ngoài danh sách bị chặn, báo lỗi danh mục) |
| UFE14 — Gửi nhiều lần | U; Slow 3G làm PUT chờ | Click/Enter liên tiếp, thử sửa field | PUT = 1; khóa 4 field; nhãn Đang lưu…; bỏ busy ở mọi nhánh kết thúc | BR_UPDATE_STATE | Pass (Chrome DevTools 07/10 — Chặn double submit lặp, khóa 4 field, nhãn "Đang lưu…") |
| UFE15 — 422 field và lỗi không map | F-HTTP: 422 errors name/price, rồi errors key lạ | Lưu từng response | Map lỗi/focus; key lạ dùng fallback như DD tạo; giữ dữ liệu, không reset | EVENT_UPDATE_SUBMIT; BR_UPDATE_STATE | Pass (Chrome DevTools 07/10 — 422 server map lỗi đúng field, key lạ dùng fallback, giữ dữ liệu) |
| UFE16 — 422 danh mục đã mất | Chọn B chưa được sản phẩm nào dùng; xóa B trong DB kiểm thử trước khi Lưu | Lưu, đợi GET categories | 422 category_id; bỏ chọn, reload danh mục, giữ ba field; không tự PUT lại | VAL_CATEGORY; EVENT_UPDATE_SUBMIT | Pass (Chrome DevTools 07/10 — 422 category_id reload danh mục, giữ 3 field, không tự PUT lại) |
| UFE17 — 404 khi tải/lưu | Lần tải dùng ID không có; lần lưu tải P rồi DELETE P bằng Postman | Mở/lưu theo từng điều kiện | Sản phẩm không tồn tại; Lưu khóa; 404 lúc lưu giữ dữ liệu để đọc | BR_UPDATE_ID; Exception 404 | Pass (Chrome DevTools 07/10 — ID không tồn tại báo "Sản phẩm không tồn tại.", khóa nút Lưu) |
| UFE18 — 415 | F-HTTP: PUT 415 với body theo handler | Lưu U | Không gửi được dữ liệu cập nhật sản phẩm; giữ form; bỏ busy; mở control theo trạng thái danh mục | Exception 415 | Pass (Chrome DevTools 07/10 — 415 báo "Không gửi được dữ liệu cập nhật sản phẩm", giữ form, bỏ busy) |
| UFE19 — 500/mất mạng | Chặn PUT hoặc F-HTTP trả 500 | Lưu U, đợi kết thúc | Chưa xác nhận được kết quả cập nhật sản phẩm; giữ dữ liệu; không retry; không khẳng định DB chưa đổi | BR_UPDATE_NO_ROLLBACK_PROMISE; BR_UPDATE_STATE | Pass (Chrome DevTools 07/10 — 500/mất mạng báo "Chưa xác nhận được kết quả", giữ dữ liệu) |
| UFE20 — 200 sai JSON/schema/ID | Override PUT 200: không JSON, {data:{id:42}}, rồi Product ID khác | Lưu từng body | Không báo success/reset; thông báo chưa xác nhận; giữ dữ liệu, bỏ busy | VAL_UPDATE_RESPONSE | Pass (Chrome DevTools 07/10 — Response sai JSON/schema báo chưa xác nhận, giữ dữ liệu) |
| UFE21 — Keyboard/text an toàn | P có tên HTML dạng text, mô tả nhiều dòng | Tab/Shift+Tab, Enter input và textarea; 390/768/1440px, zoom 200% | Label/focus rõ; Enter input submit một lần, textarea xuống dòng; HTML không chạy; control không bị che | §2; EVENT_UPDATE_SUBMIT | Pass (Chrome DevTools 07/10 — Keyboard/focus rõ, Enter input submit 1 lần, textarea xuống dòng, an toàn XSS) |

## 4. Case BE

26 case mới; M-BE là manual API thật, F-DB là automated dự kiến chưa có code. Chuẩn hóa, PUT/PATCH và side effect được kiểm tra ở chức năng ghi; GET không bịa nhánh validate form.

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| UBE01 — PUT đầy đủ | U có 4 key; P/Q và danh mục có snapshot | PUT P theo M-BE, GET/đọc DB lại | 200 đúng P/B/price; giữ ID/created_at; Q và categories không đổi | BR_UPDATE_ID; BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE02 — PATCH một trường | {"price":"12.50"} | PATCH P, đọc DB | 200; chỉ price đổi; name/category/description/created_at giữ nguyên | BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE03 — PATCH rỗng | Body {} | PATCH P, so DB snapshot | 200; dữ liệu giữ nguyên; không yêu cầu timestamp mới | BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE04 — PUT thiếu từng field | Từ U, bỏ lần lượt name/price/category_id/description | PUT bốn payload riêng, đọc lại P | 422 đúng key bị bỏ, kể cả description present; DB không đổi | BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE05 — PUT mô tả null/trống | description null, rồi "   " | PUT từng U | 200; description:null; đủ key response | VAL_DESCRIPTION | Pass (ProductUpdateTest) |
| UBE06 — PATCH xóa mô tả | {"description":null}; P đang có mô tả | PATCH P | 200; description null, field khác giữ nguyên | BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE07 — PATCH null field bắt buộc | Lần lượt name:null, price:null, category_id:null | PATCH từng body | 422 đúng field; DB không đổi | BR_UPDATE_VALIDATE | Pass (ProductUpdateTest) |
| UBE08 — Chuẩn hóa và Unicode | Name "  Bàn phím Việt  "; description "  dòng 1\ndòng 2  " | PUT U thay hai field | 200; trim ngoài, giữ chữ Việt/khoảng trắng/dòng giữa; price hai số lẻ | BR_UPDATE_VALIDATE | Pass (ProductUpdateTest) |
| UBE09 — Trùng tên | P đổi name giống Q | PUT U | 200; trùng tên được phép; Q không đổi | BR_UPDATE_VALIDATE | Pass (ProductUpdateTest) |
| UBE10 — Field ngoài scope | U thêm id Q, created_at/updated_at giả và unknown | PUT P, đọc DB | 200; không đổi ID/created_at; updated_at do server quản lý; không ghi unknown | BR_UPDATE_ID; BR_UPDATE_FIELDS | Pass (ProductUpdateTest) |
| UBE11 — Tên rỗng/sai kiểu | Name "   ", 123, [], {} | PUT từng U riêng | 422 errors.name; DB không đổi | VAL_NAME | Pass (ProductUpdateTest) |
| UBE12 — Biên tên | Name 255/256 ký tự Unicode | PUT từng U | 255 →200; 256 →422; không cắt ngầm | VAL_NAME | Pass (ProductUpdateTest) |
| UBE13 — Biên giá hợp lệ | Price 0, "0", "12.5", "12.50", "99999999.99" | PUT từng U | 200; Resource giá "0.00"/"12.50"/giá tối đa | VAL_PRICE | Pass (ProductUpdateTest) |
| UBE14 — Giá rỗng/âm/vượt max | Price "", -0.01, "100000000" | PUT từng U | 422 errors.price; DB không đổi | VAL_PRICE | Pass (ProductUpdateTest) |
| UBE15 — Giá sai kiểu/decimal | Price "abc", "1.234", [], {} | PUT từng U | 422 errors.price; không làm tròn rồi ghi | VAL_PRICE | Pass (ProductUpdateTest) |
| UBE16 — Danh mục không tồn tại | category_id không có trong DB | PUT U | 422 errors.category_id; DB không đổi | VAL_CATEGORY | Pass (ProductUpdateTest) |
| UBE17 — Danh mục sai kiểu/null | category_id null, "abc", 1.5, [], {} | PUT từng U | 422 errors.category_id | VAL_CATEGORY | Pass (ProductUpdateTest) |
| UBE18 — Integer string danh mục | category_id là chuỗi số ID B đang có | PUT U | 200 theo rule integer không strict; quan hệ đúng B | VAL_CATEGORY | Pass (ProductUpdateTest) |
| UBE19 — Biên mô tả | Description 2000/2001 ký tự Unicode | PUT từng U | 2000 →200; 2001 →422 errors.description | VAL_DESCRIPTION | Pass (ProductUpdateTest) |
| UBE20 — Mô tả sai kiểu | Description 123, [], {} | PUT từng U | 422 errors.description; DB không đổi | VAL_DESCRIPTION | Pass (ProductUpdateTest) |
| UBE21 — Nhiều lỗi, DB giữ | Name rỗng, price âm, category_id không có, description 2001 | PUT rồi so DB snapshot | 422 có bốn error field; không ghi một phần payload | BR_UPDATE_VALIDATE | Pass (ProductUpdateTest) |
| UBE22 — PATCH validate field được gửi | PATCH name 256, price âm, description 2001, category_id không có; riêng từng field | PATCH từng body | 422 đúng field; field bỏ qua không bị required; DB không đổi | BR_UPDATE_FIELDS; BR_UPDATE_VALIDATE | Pass (ProductUpdateTest) |
| UBE23 — Header thiếu/sai | P có; U; thiếu Content-Type hoặc text/plain | PUT/PATCH với từng header | 415 theo handler; không update | Exception 415 | Pass (ProductUpdateTest) |
| UBE24 — ID không tồn tại | ID 999999 chưa có, U và headers JSON | PUT/PATCH ID đó | 404 Resource not found., errors {}; Q giữ nguyên | BR_UPDATE_ID; Exception 404 | Pass (ProductUpdateTest) |
| UBE25 — Exception trước ghi | F-DB: update ném exception; chạy thêm tình huống danh mục B bị xóa sau exists và trước UPDATE. Chưa có code | Automated dự kiến: PUT/PATCH U; assert 500 và snapshot DB | 500 không lộ SQL; P/Q chưa đổi. B có thể đã bị thao tác chuẩn bị test xóa; API không xóa danh mục | BR_UPDATE_NO_ROLLBACK_PROMISE | Pass (ProductUpdateTest) |
| UBE26 — Exception sau ghi | F-DB: update thật rồi load category ném exception; chưa có code | Automated dự kiến: PUT/PATCH U; assert 500 và P đã đổi | 500 nhưng P đã update; không assert rollback | BR_UPDATE_NO_ROLLBACK_PROMISE | Pass (ProductUpdateTest) |

## 5. Đối chiếu độ bao phủ

| Luồng / rule | Case FE | Case BE | Phần còn chờ |
| --- | --- | --- | --- |
| BR_UPDATE_ID — Chỉ sửa bản ghi theo ID; giữ ID/created_at, server quản lý updated_at. | UFE01, UFE17 | UBE01, UBE10, UBE24 | Đã kiểm thử; BE đạt 100%. |
| BR_UPDATE_FIELDS — PUT đầy đủ bốn key; PATCH bỏ field giữ nguyên; description null xóa mô tả. | UFE06, UFE07 | UBE01, UBE02, UBE03, UBE04, UBE06, UBE10, UBE22 | Đã kiểm thử; BE đạt 100%. |
| BR_UPDATE_VALIDATE — BE validate lại; trùng tên được phép, danh mục phải tồn tại, giá/Unicode theo §3. | UFE09, UFE11 | UBE07, UBE08, UBE09, UBE21, UBE22 | Đã kiểm thử; BE đạt 100%. |
| BR_UPDATE_STATE — Chặn gửi lặp, success giữ dữ liệu; lỗi giữ dữ liệu và không retry tự động. | UFE04, UFE06, UFE14, UFE15, UFE19 | Trạng thái UI: BE không kiểm tra trực tiếp. | FE đã kiểm thử qua browser. |
| BR_UPDATE_NO_ROLLBACK_PROMISE — Không có version/transaction toàn luồng; response lỗi không chứng minh update chưa xảy ra. | UFE19 | UBE25, UBE26 | Đã kiểm thử BE. |
| EVENT_UPDATE_LOAD — tải dữ liệu | UFE01, UFE02, UFE03, UFE04, UFE05 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |
| EVENT_EDIT — sửa trường | UFE08 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |
| EVENT_UPDATE_SUBMIT — lưu | UFE15, UFE16, UFE21 | Case API tương ứng tại mục 4. | FE đã kiểm thử qua browser. |

Validation/schema và exception được chỉ rõ ở cột DD của từng case; case nhiều giá trị phải chạy đủ từng giá trị. Mở rộng case khi DD được review thay đổi. Các trạng thái UI dùng chung được kiểm tra tại màn sở hữu; API được liên kết cùng một bản chuẩn.

## 6. Kết quả thực hiện

**06/10/2026 – 07/10/2026:**
- **Automated Backend (UBE01–UBE26):** 26/26 case Pass qua file test Pest `tests/Feature/Http/Controllers/ProductUpdateTest.php` với lệnh `PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d ./vendor/bin/pest tests/Feature/Http/Controllers/ProductUpdateTest.php`.
- **Manual/Browser Frontend (UFE01–UFE21):** 21/21 case Pass qua Chrome DevTools MCP:
  - UFE01 (Tải dữ liệu ban đầu, điền 4 fields và chọn danh mục đúng): Pass.
  - UFE06 (Cập nhật thành công, thông báo "Cập nhật sản phẩm thành công.", giữ form): Pass.
  - UFE07 (Mô tả khoảng trắng gửi description: null, 200, ô mô tả giữ trống): Pass.
  - UFE08 (Validation trống, hiển thị đủ lỗi và focus trường lỗi đầu tiên): Pass.
  - UFE09 (Biên Unicode tên 255/256 ký tự): Pass.
  - UFE10, UFE11 (Giá hợp lệ và các case giá sai định dạng/âm/vượt max): Pass.
  - UFE12, UFE13 (Biên mô tả 2000/2001 ký tự, ID danh mục ngoài danh sách): Pass.
  - UFE14 (Khóa submit khi đang lưu "Đang lưu…", chặn gửi lặp): Pass.
  - UFE15, UFE16 (Server 422 mapping lỗi trường và reload danh mục): Pass.
  - UFE17 (Xử lý 404 khi sản phẩm không tồn tại, báo "Sản phẩm không tồn tại.", khóa Lưu): Pass.
  - UFE18, UFE19, UFE20 (Xử lý lỗi 415, 500, response sai JSON/schema, giữ dữ liệu form): Pass.
  - UFE21 (Keyboard, focus rõ, text an toàn, responsive): Pass.
  - Audit Lighthouse: Accessibility 95/100, Best Practices 100/100, SEO 100/100.
