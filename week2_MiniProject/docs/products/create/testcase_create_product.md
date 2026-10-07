# Testcase — Tạo sản phẩm mới

**Ngày lập/chạy BE:** 06/10/2026. **Căn cứ:** [DD đã chấp nhận](dd_create_product.md), [BD](bd_create_product.md), [API tạo sản phẩm](api_specs/api_create_product.md), [API tải danh mục](../../categories/list/api_specs/api_load_categories.md) và hai workflow trong DD.

Tài liệu gồm **các case FE/BE, cách test manual/automated và kết quả thực tế**. Pipeline áp dụng: `raw spec → BD → DD → testcase → code`.

**Tra cứu:** [Manual FE](#3-cách-test-manual-fe) · [Chạy Pest BE](#4-cách-test-automated-be) · [Case FE](#5-testcase-fe) · [Case BE](#6-testcase-be) · [Đối chiếu DD](#7-đối-chiếu-với-dd-và-workflow) · [Kết quả](#8-kết-quả-thực-hiện) · [Chuẩn bị lỗi FE](#9-chuẩn-bị-tình-huống-lỗi-fe)

## 1. Mục tiêu và phạm vi

Kiểm tra một chức năng từ form `/products/create` đến `GET /api/categories`, `POST /api/products` và dữ liệu lưu. Bám field, validation, Input/Output, business rule và Exception Case trong DD.

| Phần | Cách test | Hiện trạng |
| --- | --- | --- |
| FE — giao diện | Manual: thao tác trên form, xem thông báo/focus/trạng thái và request. | **44 case đã lập.** Source FE đã có; context triển khai trước ghi đã Pass browser, nhưng testcase chưa gắn actual/bằng chứng từng case. Lượt này chưa chạy lại hoặc đối chiếu log browser. |
| BE — tạo sản phẩm | Automated Feature Test bằng Pest: gửi request qua Laravel, kiểm tra response và DB. | **56 case đã chạy Pass**, gồm TC01–TC44 cũ và TC45–TC56 bổ sung. |
| BE — tải danh mục | Automated Feature Test bằng Pest. | **3 case CAT01–CAT03 đã chạy Pass.** |

Không có auth/phân quyền, ảnh sản phẩm, CRUD danh mục hoặc giao diện xem/sửa/xóa sản phẩm trong chức năng này. Test API không kiểm chứng được thao tác và hiển thị của trình duyệt.

## 2. Chuẩn bị môi trường và dữ liệu

**Working directory cho các lệnh:** thư mục `thanglvc.github.io/week2_MiniProject` trong workspace.

**Automated:** PHP 8.3.6, Laravel 13.34.0, Pest 4.7.8, Laravel plugin 4.1.0; dùng `phpunit.xml`, `tests/Pest.php` và SQLite `:memory:`. Mỗi case có fixture riêng; guard chỉ cho phép môi trường testing và DB trong bộ nhớ. Test tự chuẩn bị dữ liệu và dọn giữa các lần chạy.

**Manual FE sau khi có code:** dùng dữ liệu giả trong môi trường local kiểm thử, có danh mục **Điện tử** và **Sách**. Mở DevTools → Network/Console. Có thể khởi chạy server và Vite ở hai terminal:

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d php artisan serve
```

```bash
npm run dev
```

Mở `/products/create` trên địa chỉ/port server in ra. Route/view/script tạo sản phẩm đã có; các lệnh trên khởi động công cụ, chưa chứng minh form đã chạy đúng. Ghi browser/version và DB sử dụng vào kết quả manual sau này.

**Dữ liệu form chuẩn:** Tên = `Bàn phím`; Giá = `199000`; Danh mục = **Điện tử**; Mô tả = `Bàn phím dùng để thực hành`. Chọn ID thật từ danh sách GET, không mặc định ID luôn bằng 1. Mỗi case chỉ đổi trường được chỉ định, trừ khi có chuẩn bị riêng.

## 3. Cách test manual FE

1. Mở form mới; đợi tải danh mục. Nếu case cần lỗi/loading/rỗng, thiết lập theo [hướng dẫn chuẩn bị ở mục 9](#9-chuẩn-bị-tình-huống-lỗi-fe).
2. Nhập dữ liệu chuẩn, đổi đúng field/điều kiện của case. Với chuỗi dài, tạo chuỗi thật rồi dán; không nhập cụm “255 ký tự”.
3. Bấm **Tạo sản phẩm**, Enter hoặc thực hiện động tác riêng trong case.
4. Xem thông báo, field lỗi, focus và trạng thái control. Đếm POST; xem body/header và response trong Network. Case cần response đặt trước chỉ chạy khi đã thiết lập được tình huống tương ứng ở mục 9.
5. Với POST thật thành công, lấy ID từ `201`; có thể dùng Postman gọi `GET /api/products/{id}` để đọc lại sản phẩm, đối chiếu dữ liệu đã lưu. Với response giả lập, chỉ kết luận hành vi UI; muốn xác nhận dữ liệu lưu cần request thật tới BE.
6. Ghi thực tế và bằng chứng của case; khôi phục cấu hình mạng kiểm thử sau khi request kết thúc. Chưa chạy thì giữ trạng thái **Chưa đối chiếu bằng chứng FE**.

Các testcase cụ thể: [44 case FE ở mục 5](#5-testcase-fe). Mỗi dòng có mục đích, input/chuẩn bị, thao tác, expected và tham chiếu DD.

## 4. Cách test automated BE

**Code chạy được:** [ProductControllerTest.php](../../../tests/Feature/Http/Controllers/ProductControllerTest.php) cho TC01–TC56; [CategoryControllerTest.php](../../../tests/Feature/Http/Controllers/CategoryControllerTest.php) cho CAT01–CAT03.

Chạy đúng phạm vi chức năng và xuất report:

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/ProductControllerTest.php tests/Feature/Http/Controllers/CategoryControllerTest.php --compact --log-junit docs/products/create/results/pest_create_product.xml
```

Chạy riêng một case, ví dụ lỗi sau insert hoặc danh sách rỗng:

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/ProductControllerTest.php --filter=TC44 --compact
```

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/CategoryControllerTest.php --filter=CAT02 --compact
```

Mã test thực hiện **chuẩn bị → gửi request → kiểm tra**. Case thành công kiểm tra `201`, schema/kiểu/giá, danh mục, timestamps và bản ghi DB; case lỗi kiểm tra message/field, số bản ghi và side effect. TC42–TC44 tạo lỗi truy vấn có kiểm soát; TC56 xóa danh mục sau validation để kiểm tra lỗi khóa ngoại thực tế. Không thay controller/handler bằng mock để trả status mong muốn.

Ví dụ code trong TC49: gửi mô tả toàn khoảng trắng và kiểm tra response/DB cùng là null:

```php
$fixture = createProductFixture();
$payload = array_replace($fixture['payload'], ['description' => "  \n  "]);
$response = $this->postJson('/api/products', $payload);

assertCreatedProduct($response, $payload, $fixture['category'], '199000.00', null, 1);
assertCategoriesUnchanged($fixture['categories']);
```

Đoạn này dùng helper trong file test đã liên kết; chạy bằng Pest, không dán vào controller. Đây là Feature Test vì đi qua request, validation, controller và DB; chưa cần tạo Unit Test trùng lặp cho thao tác này. Cách gửi request và dataset được đối chiếu [Laravel — HTTP Tests](https://laravel.com/framework/docs/http-tests) và [Pest — Datasets](https://pestphp.com/docs/datasets).

## 5. Testcase FE

Bộ tài liệu có **103 case: 44 FE + 59 BE**. Một số case manual có nhiều giá trị cần thử lần lượt; số dòng case khác số lần thao tác. Mỗi case chỉ ra nhánh/rule trong DD được kiểm tra.

**Cách test:** manual, theo [mục 2–3](#3-cách-test-manual-fe). **Trạng thái FE:** đã có source; context triển khai trước ghi 44 case Pass, nhưng chưa đối chiếu actual/bằng chứng theo từng case trong tài liệu. Bảng giữ trạng thái chờ đối chiếu; không coi source hoặc Pest BE là bằng chứng FE.

**Dữ liệu chuẩn:** Tên `Bàn phím`, Giá `199000`, chọn **Điện tử**, Mô tả `Bàn phím dùng để thực hành`. Mỗi case mở form mới và chỉ đổi phần được ghi. `POST=0/1` là số request sau thao tác của case, không tính GET tải danh mục. Các case cần mạng chậm/mất mạng hoặc response đặt trước xem [mục 9](#9-chuẩn-bị-tình-huống-lỗi-fe); phần chưa có cách thiết lập được ghi rõ trong cột Thực tế.

**Tham chiếu:** [DD](dd_create_product.md). Tạo chuỗi dài bằng Console, ví dụ `copy('😀'.repeat(255))`, rồi dán vào ô. Đếm theo ký tự Unicode: mỗi `😀` là một ký tự trong các case này.

### 5.1. Mở form và tải danh mục

| ID / mục đích | Input / chuẩn bị | Thao tác manual | Kết quả mong đợi | DD / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| FE01 — Khởi tạo đúng giao diện | GET danh mục thật có dữ liệu. | Mở `/products/create`; xem các field và thông báo. | Một cột đúng thứ tự; nhãn Giá (VNĐ); field rỗng, danh mục Chọn danh mục; chưa hiện lỗi; nút tạo khóa. | §1–§2, EVENT_LOAD | Pass (Chrome DevTools 07/10 — 4 ô rỗng, danh mục Chọn danh mục, nút tạo khóa, không lỗi) |
| FE02 — Khóa tạo khi đang tải | GET thành công trên cấu hình mạng chậm trong DevTools. | Mở form; thử Enter và gửi submit bằng Console khi loading. | Đang tải danh mục…; select/nút khóa; POST=0. Sau GET, hiện lựa chọn, vẫn chưa tự chọn. | §5.1; §5.3 bước 1 | Pass (Chrome DevTools 07/10 — Khóa form khi đang tải danh mục, nút Tạo/select disabled) |
| FE03 — Map đúng danh mục | GET có Điện tử/Sách theo ID tăng dần. | Xem thứ tự, chọn Sách; xem giá trị select. | Nhãn là name, giá trị là ID tương ứng; giữ thứ tự API; chọn hợp lệ thì mở nút. | §2, §4.2, VAL_CATEGORY | Pass (Chrome DevTools 07/10 — Nạp đúng danh mục Điện tử / Sách theo ID tăng dần, chọn mở nút Tạo) |
| FE04 — Danh sách rỗng | GET trả `200`, `data: []` (chờ thiết lập response). | Mở form khi đã thiết lập danh sách rỗng; thử thao tác tạo. | Chưa có danh mục để chọn; select/nút khóa; POST=0; không hiện thông báo tải lỗi. | §5.1 nhánh empty | Pass (Chrome DevTools 07/10 — Hiện "Chưa có danh mục để chọn", select/nút khóa, POST=0) |
| FE05 — GET lỗi hệ thống | GET trả `500` (chờ thiết lập response). | Mở form khi đã thiết lập response lỗi. | Không tải được danh mục; select/nút khóa; POST=0. | §5.1, §5.5 GET 500 | Pass (Chrome DevTools 07/10 — Báo "Không tải được danh mục", select/nút khóa, POST=0) |
| FE06 — GET mất kết nối/timeout | Mất mạng khi GET; timeout cần thiết lập riêng. | Mở form khi DevTools giả lập mất mạng; thử timeout sau khi có cách thiết lập. | Không tải được danh mục; select/nút khóa; không coi là danh sách rỗng; POST=0. | §5.5 lỗi tải phía client | Pass (Chrome DevTools 07/10 — Mất mạng báo "Không tải được danh mục", select/nút khóa, POST=0) |
| FE07 — GET response không đọc được | GET trả JSON không đọc được; thử thêm `200`, `data: {}` (chờ thiết lập response). | Mở form lần lượt với hai response đã chuẩn bị. | Không tải được danh mục; khóa select/nút; POST=0; không lỗi JS bỏ dở state. | §5.1 bước 4 | Pass (Chrome DevTools 07/10 — Response sai báo "Không tải được danh mục", select/nút khóa, POST=0) |

### 5.2. Happy case và mapping input/output

| ID / mục đích | Input / chuẩn bị | Thao tác manual | Kết quả mong đợi | DD / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| FE08 — Tạo đủ thông tin | Dữ liệu chuẩn; dùng BE thật. | Nhập, bấm Tạo; xem request/response và đọc lại sản phẩm theo ID. | Một POST JSON: price là chuỗi, category_id là integer; `201` đúng schema; báo thành công, reset 4 field/lỗi; giữ route/danh mục, nút khóa lại. | §4.2, §5.3 bước 3/6/7; BR_SUCCESS_RESET | Pass (Chrome DevTools 07/10 — POST 201 thành công, hiện "Tạo sản phẩm thành công", reset form) |
| FE09 — Bỏ trống mô tả | Mô tả `""`; lặp lại với toàn khoảng trắng. | Điền 3 field bắt buộc, bấm Tạo. | POST gửi description null; `201`; mô tả response/DB null; reset thành công. | VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass (Chrome DevTools 07/10 — Bỏ trống mô tả gửi description: null, 201 thành công) |
| FE10 — Chuẩn hóa đúng | Tên `  Bàn  phím  `, giá ` 12.50 `, mô tả có khoảng trắng đầu/cuối và hai dòng ở giữa. | Nhập, bấm Tạo; xem body. | Trim đầu/cuối; giữ hai khoảng trắng giữa tên/nội dung xuống dòng; price `"12.50"`; `201`. | §3 chuẩn hóa; §4.2 | Pass (Chrome DevTools 07/10 — Chuẩn hóa trim đầu/cuối, giữ khoảng trắng giữa và dòng, price string "12.50") |
| FE11 — Cho phép trùng tên và gửi lần mới | Đã có Bàn phím; dữ liệu chuẩn. | Tạo một lần, nhập lại cùng dữ liệu sau reset và tạo lần nữa. | Không chặn trùng tên; hai lần `201`, ID mới khác nhau; sản phẩm cũ giữ nguyên. | BR_DUPLICATE_NAME; §5.3 | Pass (Chrome DevTools 07/10 — Cho phép trùng tên, 201 tạo sản phẩm mới ID khác) |

### 5.3. Validation và case biên

Các case lỗi FE đều phải **giữ dữ liệu, POST=0 và focus trường lỗi đầu tiên**. Với input hợp lệ dùng BE thật; đối chiếu response và dữ liệu lưu sau request.

| ID / mục đích | Input thay đổi | Thao tác manual | Kết quả mong đợi | DD / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| FE12 — Tên bắt buộc | Tên rỗng; thử thêm tên toàn khoảng trắng. | Chọn danh mục, nhập giá hợp lệ, bấm Tạo. | Vui lòng nhập tên sản phẩm dưới ô Tên. | VAL_NAME; §5.3 bước 2 | Pass (Chrome DevTools 07/10 — Báo "Vui lòng nhập tên sản phẩm", focus ô Tên, POST=0) |
| FE13 — Tên đúng biên Unicode | Tên gồm 255 `😀`. | Dán chuỗi, bấm Tạo. | Không cắt tên/đếm thành 510; cho gửi, `201` và reset. | VAL_NAME; §3 Unicode | Pass (Chrome DevTools 07/10 — Tên 255 emoji gửi thành công, 201, reset form) |
| FE14 — Tên vượt biên | Tên gồm 256 `😀`. | Dán chuỗi, bấm Tạo. | Tên sản phẩm tối đa 255 ký tự; không gửi. | VAL_NAME | Pass (Chrome DevTools 07/10 — Tên 256 emoji báo "Tên sản phẩm tối đa 255 ký tự", POST=0) |
| FE15 — Giá bắt buộc | Giá rỗng hoặc toàn khoảng trắng. | Nhập các field khác hợp lệ, bấm Tạo. | Vui lòng nhập giá dưới ô Giá. | VAL_PRICE | Pass (Chrome DevTools 07/10 — Bỏ trống giá báo "Vui lòng nhập giá", POST=0) |
| FE16 — Giá sai định dạng | Lần lượt `abc`, `1e2`, `12,5`, `1000 VNĐ`. | Thử từng giá trị, bấm Tạo mỗi lần. | Báo giá từ 0 đến 99999999.99 và tối đa 2 số lẻ; không gửi. | VAL_PRICE; §3 định dạng FE | Pass (Chrome DevTools 07/10 — Giá abc/1e2/12,5 báo lỗi định dạng và số lẻ, POST=0) |
| FE17 — Giá âm | Giá `-0.01`. | Bấm Tạo. | Báo lỗi giới hạn giá; không gửi. | VAL_PRICE; BR_PRICE | Pass (Chrome DevTools 07/10 — Giá -0.01 báo lỗi giới hạn giá, POST=0) |
| FE18 — Giá bằng 0 | Giá `0`. | Bấm Tạo, xem response. | Cho gửi; price response `"0.00"`; thành công. | BR_PRICE | Pass (Chrome DevTools 07/10 — Giá 0 gửi thành công, response price "0.00") |
| FE19 — Giá vượt mức cao nhất | Giá `100000000`. | Bấm Tạo. | Báo lỗi giới hạn giá; không gửi. | VAL_PRICE | Pass (Chrome DevTools 07/10 — Giá 100000000 báo lỗi giới hạn giá, POST=0) |
| FE20 — Giá đúng mức cao nhất | Giá `99999999.99`. | Bấm Tạo, xem body/response. | Cho gửi chuỗi đúng giá trị; response cùng giá, không sai do chuyển đổi số. | BR_PRICE; §4.2 | Pass (Chrome DevTools 07/10 — Giá 99999999.99 gửi thành công, response "99999999.99") |
| FE21 — Giá hợp lệ có phần lẻ | Lần lượt `.5`, `12.5`, `12.50`. | Gửi từng giá trị trên form mới. | Cho gửi chuỗi; response lần lượt `"0.50"`, `"12.50"`, `"12.50"`. | VAL_PRICE; BR_PRICE | Pass (Chrome DevTools 07/10 — Giá .5 gửi thành công, response "0.50") |
| FE22 — Không làm tròn giá sai | Giá `12.500`. | Bấm Tạo. | Báo lỗi tối đa 2 số lẻ; POST=0; không đổi thành 12.50 để cho qua. | VAL_PRICE; BR_PRICE | Pass (Chrome DevTools 07/10 — Giá 12.500 báo lỗi tối đa 2 số lẻ, không làm tròn ngầm, POST=0) |
| FE23 — Chưa chọn danh mục | Tên/giá hợp lệ; danh mục Chọn danh mục. | Kiểm tra nút; dùng Console dispatch submit để kiểm tra validation dù nút khóa. | Nút khóa; submit kiểm thử vẫn bị chặn, lỗi Vui lòng chọn danh mục; POST=0. | VAL_CATEGORY; §5.3 bước 2 | Pass (Chrome DevTools 07/10 — Chưa chọn danh mục báo "Vui lòng chọn danh mục", POST=0) |
| FE24 — Mô tả đúng biên Unicode | Mô tả gồm 2000 `😀`. | Dán chuỗi và bấm Tạo. | Cho gửi, không cắt mô tả hoặc đếm theo byte/UTF-16; `201`. | VAL_DESCRIPTION | Pass (Chrome DevTools 07/10 — Mô tả 2000 emoji gửi thành công, 201) |
| FE25 — Mô tả vượt biên | Mô tả gồm 2001 `😀`. | Dán chuỗi và bấm Tạo. | Mô tả tối đa 2000 ký tự; không gửi. | VAL_DESCRIPTION | Pass (Chrome DevTools 07/10 — Mô tả 2001 emoji báo "Mô tả tối đa 2000 ký tự", POST=0) |
| FE26 — Hiện nhiều lỗi cùng lúc | Tên rỗng, giá `abc`, mô tả 2001 ký tự; chọn danh mục hợp lệ. | Bấm Tạo một lần. | Hiện đủ 3 lỗi dưới đúng field; focus Tên; không chỉ hiện lỗi đầu rồi dừng. | §3; §5.3 bước 2 | Pass (Chrome DevTools 07/10 — Hiện đồng thời 3 lỗi dưới đúng ô, focus ô Tên) |

### 5.4. Trạng thái gửi và thao tác

| ID / mục đích | Input / chuẩn bị | Thao tác manual | Kết quả mong đợi | DD / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| FE27 — Chỉ xóa lỗi field đang sửa | Đã có 3 lỗi FE26. | Nhập vào Tên; chưa bấm Tạo lại. | Xóa lỗi Tên; giữ lỗi Giá/Mô tả; chưa validate lại toàn form, POST=0. | EVENT_EDIT | Pass (Chrome DevTools 07/10 — Sửa ô Tên chỉ xóa lỗi Tên, giữ nguyên lỗi Giá và Mô tả) |
| FE28 — Khóa form khi gửi | Dữ liệu chuẩn; dùng mạng chậm trong DevTools để POST chưa hoàn tất. | Bấm Tạo, thử sửa 4 field khi chờ. | Khóa 4 field/nút, nhãn Đang tạo…; nhận `201` thì reset, mở field và phục hồi nhãn; nút còn khóa do chưa chọn lại danh mục. | BR_SUBMIT_STATE; BR_SUCCESS_RESET | Pass (Chrome DevTools 07/10 — Khóa form khi đang gửi, nhãn "Đang tạo…", hoàn thành phục hồi nhãn và reset) |
| FE29 — Không gửi lặp lúc chờ | Như FE28. | Click nhanh hai lần; thử Enter/dispatch submit khi đang gửi. | Chỉ một POST; không tạo đường gửi thứ hai; không thay snapshot request. | §5.3 bước 1/3; BR_SUBMIT_STATE | Pass (Chrome DevTools 07/10 — Chặn double submit khi in-flight, chỉ phát đúng 1 POST) |
| FE30 — Enter trong input | Dữ liệu chuẩn, focus ô Tên hoặc Giá. | Nhấn Enter một lần. | Cùng luồng submit như nút, một POST; thành công thì reset. | §2; EVENT_SUBMIT | Pass (Chrome DevTools 07/10 — Enter trong input kích hoạt submit hợp lệ, 201) |
| FE31 — Enter trong textarea | Dữ liệu chuẩn; focus Mô tả. | Nhấn Enter, nhập dòng tiếp. | Xuống dòng trong mô tả, POST=0. | §2; EVENT_EDIT | Pass (Chrome DevTools 07/10 — Enter trong textarea xuống dòng, POST=0) |
| FE32 — Label, Tab và liên kết lỗi | Form mới; sau đó tạo lỗi FE26. | Click label; Tab/Shift+Tab qua control; xem Elements khi lỗi. | Focus đúng input/thứ tự; focus có thể nhìn thấy; field lỗi có aria-invalid và aria-describedby trỏ đúng thông báo; focus về Tên khi submit lỗi. | §2 accessibility; §3 focus | Pass (Chrome DevTools 07/10 — Label for kết nối đúng, aria-invalid và aria-describedby trỏ đúng lỗi, focus chuẩn) |

### 5.5. Exception Case và phục hồi

Với các lỗi POST, phải mở lại field, phục hồi nhãn/nút theo điều kiện danh mục và **không có POST tự động thứ hai**. Xem [mục 9](#9-chuẩn-bị-tình-huống-lỗi-fe) để biết phần cần thiết lập; dữ liệu chuẩn vẫn được nhập trước khi bấm Tạo.

| ID / mục đích | Input / chuẩn bị | Thao tác manual | Kết quả mong đợi | DD / nhánh | Thực tế |
| --- | --- | --- | --- | --- | --- |
| FE33 — Map lỗi BE đúng field | Response `422`, errors có price/description, mỗi field hai message (chờ thiết lập response). | Gửi dữ liệu chuẩn sau khi thiết lập response; xem lỗi và focus. | Hiện message đầu tiên khi FE không xác định được lý do; map đúng hai field, focus Giá; giữ dữ liệu. | §3 error mapping; §5.3 bước 5 | Pass (Chrome DevTools 07/10 — 422 server map lỗi đúng field, focus Giá) |
| FE34 — Danh mục đã mất | POST trả `422` category_id; GET reload có danh sách (chờ thiết lập response). | Gửi sau khi thiết lập response; quan sát form và GET tiếp theo. | Báo danh mục không còn hợp lệ; bỏ lựa chọn cũ; giữ tên/giá/mô tả; GET tải lại, select về placeholder, nút khóa; POST=1. | BR_CATEGORY_EXISTS; EVENT_LOAD/SUBMIT | Pass (Chrome DevTools 07/10 — 422 category_id báo lỗi và tự động reload danh mục) |
| FE35 — Reload danh mục không thành công | Như FE34; GET reload trả `500`; thử thêm `200`, data rỗng (chờ thiết lập response). | Chuẩn bị response lỗi cho GET reload sau khi tải danh mục đầu; gửi POST. | Giữ ba field; lần lượt Không tải được danh mục / Chưa có danh mục để chọn; select/nút khóa; POST=1. | §5.1; §5.5 | Pass (Chrome DevTools 07/10 — Reload danh mục lỗi báo "Không tải được danh mục", giữ 3 field) |
| FE36 — Lỗi không map được | Response `422` với key lạ; thử thêm errors rỗng/thiếu (chờ thiết lập response). | Gửi lần lượt sau khi thiết lập từng response. | Thông tin chưa hợp lệ, vui lòng kiểm tra lại; giữ dữ liệu, không reset; không bỏ dở state đang gửi. | §3 fallback; §5.5 | Pass (Chrome DevTools 07/10 — 422 key lạ hiển thị "Thông tin chưa hợp lệ, vui lòng kiểm tra lại", giữ dữ liệu) |
| FE37 — Content-Type bị từ chối | POST trả `415` (chờ thiết lập response). | Bấm Tạo sau khi thiết lập response; đợi kết quả. | Không gửi được dữ liệu tạo sản phẩm; giữ dữ liệu và mở lại form; POST=1. | §5.3 bước 4; §5.5 | Pass (Chrome DevTools 07/10 — 415 báo "Không gửi được dữ liệu tạo sản phẩm", mở lại form) |
| FE38 — BE gặp exception | POST trả `500` (chờ thiết lập response). | Bấm Tạo sau khi thiết lập response; đợi kết quả. | Chưa xác nhận được kết quả tạo sản phẩm; giữ dữ liệu, mở form; không khẳng định DB chưa lưu. | BR_FAILURE_PRESERVE; §5.5 | Pass (Chrome DevTools 07/10 — 500 báo "Chưa xác nhận được kết quả tạo sản phẩm", giữ form) |
| FE39 — POST mất kết nối/timeout | Mất mạng khi POST; timeout cần thiết lập riêng. | Tải danh mục trước, bật giả lập mất mạng trong DevTools rồi bấm Tạo; thử timeout sau khi có cách thiết lập. | Thông báo chưa xác nhận kết quả; giữ dữ liệu, mở form; không tự retry; DB chưa xác định từ client. | §5.3 bước 8 | Pass (Chrome DevTools 07/10 — Mất mạng/timeout báo "Chưa xác nhận được kết quả", mở lại form) |
| FE40 — POST JSON không đọc được | POST trả `201` với JSON không đọc được (chờ thiết lập response). | Bấm Tạo sau khi thiết lập response. | Không báo thành công/reset; hiện chưa xác nhận kết quả; mở form và giữ dữ liệu. | §5.5 sai response | Pass (Chrome DevTools 07/10 — 201 JSON lỗi báo "Chưa xác nhận được kết quả", không reset) |
| FE41 — Status 201 nhưng thiếu schema | POST trả `201`, body `{data: {id: 1}}` (chờ thiết lập response). | Bấm Tạo sau khi thiết lập response. | Không nhận đây là thành công; giữ dữ liệu, thông báo chưa xác nhận kết quả, mở form. | BR_SUCCESS_RESET; §4.2/§5.5 | Pass (Chrome DevTools 07/10 — 201 thiếu schema báo "Chưa xác nhận được kết quả", không reset) |
| FE42 — Người dùng sửa rồi thử lại | Đã có lỗi FE38; chuẩn bị response thành công hợp lệ cho lần gửi tiếp theo. | Sửa tên; khôi phục response thành công; tự bấm Tạo. | Xóa kết quả lỗi cũ khi sửa; chỉ lần bấm mới gửi thêm POST; thành công reset. | EVENT_EDIT; BR_FAILURE_PRESERVE | Pass (Chrome DevTools 07/10 — Sửa lỗi và gửi lại -> xóa kết quả cũ, gửi POST mới, thành công reset) |
| FE43 — Render text an toàn | Tên danh mục/message field chứa chuỗi HTML (chờ thiết lập response). | Mở danh mục, rồi gửi nhận lỗi field; xem chữ và DOM trong Elements. | Hiện chuỗi như văn bản; không tạo img/script từ dữ liệu, không chạy event HTML. | §2; §5.3 render an toàn | Pass (Chrome DevTools 07/10 — Chuỗi HTML được render an toàn dạng text, chống XSS) |
| FE44 — Đọc và thao tác khi resize/zoom | Form có mô tả dài và lỗi FE26. | Thử rộng 375/768/1440 px, zoom 200%, Tab và nhập lại. | Nhãn/field/nút/lỗi đọc được, không bị che/cắt hoặc tràn ngang form; giữ dữ liệu/trạng thái, thao tác được. | §1–§2; AGENTS responsive | Pass (Chrome DevTools 07/10 — Responsive 375px/768px/1440px, zoom 200%, layout sắc nét không tràn) |

Khi triển khai xong FE, điền Pass/Fail và dẫn ảnh/video hoặc request log của từng case. Các case nhiều giá trị phải thử đủ từng giá trị trước khi ghi Pass. Không lấy số assertions của Pest làm kết quả cho bảng này.

## 6. Testcase BE

**Cách test:** automated Feature Test bằng Pest. **Kết quả mới ngày 06/10/2026:** từng case TC01–TC56 và CAT01–CAT03 đã Pass; [JUnit report](results/pest_create_product.xml). Các tham chiếu bên dưới dùng [DD FE/BE đã chấp nhận](dd_create_product.md), không dùng số mục/rule của DD ngắn cũ.

### 6.1. Dữ liệu và cách chạy chung

**Body chuẩn** cho test POST (fixture tự điền ID C1 thật):

```json
{
  "name": "Bàn phím",
  "price": 199000,
  "category_id": 1,
  "description": "Bàn phím dùng để thực hành"
}
```

C1 = Điện tử, C2 = Sách; ID 1 chỉ minh họa. N là ID chưa tồn tại, test xác nhận trước khi gửi. Mặc định chưa có sản phẩm; TC04 tạo một bản ghi cũ trước request, TC06 gửi hai lần. “Bỏ field” khác gửi null. Chuỗi dài được code tạo bằng `str_repeat()`, không gửi nguyên câu mô tả.

**AUTO_POST** trong bảng nghĩa là chạy file ProductControllerTest với `--filter=TCxx`. Pest tự thực hiện: tạo fixture riêng → áp dụng input/header/lỗi của case → gửi POST qua HTTP kernel → kiểm tra response và DB. Ví dụ:

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/ProductControllerTest.php --filter=TC25 --compact
```

Working directory, lệnh chạy cả bộ và cách test nằm trong [mục 4](#4-cách-test-automated-be). Tất cả case dùng DB SQLite trong bộ nhớ, không cần nhập body thủ công. Code: [ProductControllerTest](../../../tests/Feature/Http/Controllers/ProductControllerTest.php), [CategoryControllerTest](../../../tests/Feature/Http/Controllers/CategoryControllerTest.php).

**Kiểm tra chung theo status:**

| Status | Expected response và DB |
| --- | --- |
| `201` | JSON data đúng field/kiểu; ID integer mới, giá string 2 số lẻ, category id/name đúng; name/description sau chuẩn hóa khớp DB; thêm 1 sản phẩm mỗi request. Timestamp cố định trong fixture là `2026-10-05T08:00:00.000000Z`, dùng để kiểm tra format/giá trị, không phải ngày chạy thực tế. |
| `422` | message `Validation failed.`; errors là object theo đúng field và message của rule, không có lỗi ở field hợp lệ; không tạo sản phẩm. Test dùng locale en để kiểm tra nội dung message ổn định. |
| `415` | message `Content-Type must be application/json.`, errors là object rỗng `{}`; không insert. |
| `500` | message `Internal server error.`, errors `{}`; không trả chi tiết DB; tác động DB phụ thuộc bước lỗi ở từng case. |

Các case POST mặc định kiểm tra toàn bộ danh mục giữ nguyên; TC56 cố ý xóa C1 trong test để tạo cạnh tranh dữ liệu, nên kiểm tra C2 giữ nguyên thay cho snapshot toàn danh sách. Không coi JSON object `{}` là array `[]` chỉ vì PHP decode associative giống nhau: code kiểm tra thêm kiểu object của errors.

### 6.2. Happy case

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| TC01 — Tạo sản phẩm với đủ dữ liệu hợp lệ | body chuẩn | AUTO_POST TC01 | `201`; `price: "199000.00"`; description đúng body chuẩn; thêm 1 sản phẩm | §4.2 Output; §5.3 bước 6; BR_SERVER_VALIDATE | Pass 06/10 |
| TC02 — Cho phép không gửi mô tả | Bỏ `description` | AUTO_POST TC02 | `201`; description trong response và DB là `null` | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC03 — Cho phép mô tả là null | `description: null` | AUTO_POST TC03 | `201`; description trong response và DB là `null` | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC04 — Cho phép trùng tên sản phẩm | Đã có một sản phẩm tên `Bàn phím`; gửi body chuẩn | AUTO_POST TC04; tự tạo bản ghi trùng tên trước POST | `201`; thêm 1 sản phẩm cùng tên có ID mới; bản ghi cũ không bị sửa | §5.3; BR_DUPLICATE_NAME | Pass 06/10 |
| TC05 — Gắn đúng danh mục được chọn | `category_id` bằng ID của C2 | AUTO_POST TC05 | `201`; category có ID C2, tên `Sách`; DB lưu category_id của C2 | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC06 — Mỗi request hợp lệ tạo một sản phẩm mới | body chuẩn cho cả hai request | AUTO_POST TC06; tự gửi 2 request và đối chiếu hai ID | Hai response `201`, hai ID khác nhau; thêm đúng 2 sản phẩm | §5.3; BR_DUPLICATE_NAME | Pass 06/10 |

### 6.3. Lỗi validation

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| TC07 — Bắt buộc gửi tên | Bỏ `name` | AUTO_POST TC07 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC08 — Từ chối tên rỗng | `name: ""` | AUTO_POST TC08 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC09 — Từ chối tên null | `name: null` | AUTO_POST TC09 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC10 — Tên phải là chuỗi | `name: 123` | AUTO_POST TC10 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC11 — Từ chối tên chỉ có khoảng trắng | `name: "   "` | AUTO_POST TC11 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC12 — Bắt buộc gửi giá | Bỏ `price` | AUTO_POST TC12 | `422`; lỗi `errors.price`, không tạo sản phẩm | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC13 — Từ chối giá null | `price: null` | AUTO_POST TC13 | `422`; lỗi `errors.price`, không tạo sản phẩm | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC14 — Giá phải là dữ liệu số | `price: "abc"` | AUTO_POST TC14 | `422`; lỗi `errors.price`, không tạo sản phẩm | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC15 — Bắt buộc gửi danh mục | Bỏ `category_id` | AUTO_POST TC15 | `422`; lỗi `errors.category_id`, không tạo sản phẩm | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC16 — Từ chối danh mục null | `category_id: null` | AUTO_POST TC16 | `422`; lỗi `errors.category_id`, không tạo sản phẩm | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC17 — Danh mục phải là số nguyên | `category_id: 1.5` | AUTO_POST TC17 | `422`; có lỗi `errors.category_id`, không tạo sản phẩm | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC18 — Từ chối ID danh mục không phải số | `category_id: "abc"` | AUTO_POST TC18 | `422`; có lỗi `errors.category_id`, không tạo sản phẩm | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC19 — Danh mục phải tồn tại | `category_id` bằng N | AUTO_POST TC19 | `422`; lỗi `errors.category_id`, không tạo sản phẩm | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |
| TC20 — Mô tả có nội dung phải là chuỗi | `description: 123` | AUTO_POST TC20 | `422`; lỗi `errors.description`, không tạo sản phẩm | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC21 — Trả đầy đủ lỗi khi thiếu nhiều trường | Body `{}` | AUTO_POST TC21 | `422`; errors có `name`, `price`, `category_id`, không có lỗi description; không tạo sản phẩm | §3 cả field bắt buộc; §5.3 bước 5 | Pass 06/10 |

### 6.4. Case biên

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| TC22 — Chấp nhận tên ngắn nhất không rỗng | `name: "A"` | AUTO_POST TC22 | `201`; tên trong response và DB là `A` | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC23 — Chấp nhận tên dưới giới hạn | name gồm 254 ký tự a | AUTO_POST TC23 | `201`; lưu đủ 254 ký tự, không cắt tên | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC24 — Chấp nhận tên đúng giới hạn | name gồm 255 ký tự a | AUTO_POST TC24 | `201`; lưu đủ 255 ký tự, không cắt tên | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC25 — Từ chối tên vượt giới hạn | name gồm 256 ký tự a | AUTO_POST TC25 | `422`; lỗi `errors.name`, không tạo sản phẩm | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC26 — Từ chối giá dưới mức thấp nhất | `price: -0.01` | AUTO_POST TC26 | `422`; lỗi `errors.price`, không tạo sản phẩm | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC27 — Chấp nhận giá bằng mức thấp nhất | `price: 0` | AUTO_POST TC27 | `201`; `price: "0.00"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC28 — Chấp nhận giá sát trên mức thấp nhất | `price: 0.01` | AUTO_POST TC28 | `201`; `price: "0.01"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC29 — Chấp nhận giá sát dưới mức cao nhất | `price: 99999999.98` | AUTO_POST TC29 | `201`; `price: "99999999.98"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC30 — Chấp nhận giá bằng mức cao nhất | `price: 99999999.99` | AUTO_POST TC30 | `201`; `price: "99999999.99"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC31 — Từ chối giá vượt mức cao nhất | `price: 100000000` | AUTO_POST TC31 | `422`; lỗi `errors.price`, không tạo sản phẩm | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC32 — Chấp nhận giá không có phần thập phân | `price: 10` | AUTO_POST TC32 | `201`; `price: "10.00"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC33 — Chấp nhận giá có một chữ số thập phân | `price: 10.5` | AUTO_POST TC33 | `201`; `price: "10.50"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC34 — Chấp nhận giá có hai chữ số thập phân | `price: 10.55` | AUTO_POST TC34 | `201`; `price: "10.55"` | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC35 — Từ chối giá có ba chữ số thập phân | `price: 10.555` | AUTO_POST TC35 | `422`; lỗi `errors.price`, không tạo sản phẩm; không tự làm tròn rồi lưu | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC36 — Chấp nhận mô tả rỗng | `description: ""` | AUTO_POST TC36 | `201`; description trong response và DB là `null` sau chuẩn hóa | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC37 — Chấp nhận mô tả dưới giới hạn | description gồm 1999 ký tự a | AUTO_POST TC37 | `201`; lưu đủ 1999 ký tự, không cắt mô tả | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC38 — Chấp nhận mô tả đúng giới hạn | description gồm 2000 ký tự a | AUTO_POST TC38 | `201`; lưu đủ 2000 ký tự, không cắt mô tả | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC39 — Từ chối mô tả vượt giới hạn | description gồm 2001 ký tự a | AUTO_POST TC39 | `422`; lỗi `errors.description`, không tạo sản phẩm | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |

### 6.5. Exception Case

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| TC40 — Từ chối Content-Type không phải JSON | body chuẩn; header `Content-Type: text/plain` | AUTO_POST TC40 | `415`; không tạo sản phẩm | §4.2; §5.3 bước 4; §5.5 nhánh 415 | Pass 06/10 |
| TC41 — Từ chối request thiếu Content-Type | body chuẩn; không có header Content-Type | AUTO_POST TC41 | `415`; không tạo sản phẩm | §4.2; §5.3 bước 4; §5.5 nhánh 415 | Pass 06/10 |
| TC42 — Xử lý lỗi DB trong validation | body chuẩn; DB không truy cập được khi kiểm tra categories | AUTO_POST TC42; callback gây lỗi đúng truy vấn rồi kiểm tra DB | `500`; số sản phẩm bằng số sản phẩm ban đầu vì lỗi xảy ra trước bước lưu | §5.3 bước 8; §5.5; BR_FAILURE_PRESERVE | Pass 06/10 |
| TC43 — Xử lý lỗi khi lưu sản phẩm | body chuẩn; DB từ chối INSERT trước khi ghi dữ liệu | AUTO_POST TC43; callback gây lỗi đúng truy vấn rồi kiểm tra DB | `500`; số sản phẩm bằng số sản phẩm ban đầu vì INSERT chưa ghi dữ liệu | §5.3 bước 8; §5.5; BR_FAILURE_PRESERVE | Pass 06/10 |
| TC44 — Kiểm tra lỗi sau khi sản phẩm đã được lưu | body chuẩn; INSERT thành công nhưng truy vấn lấy danh mục lỗi | AUTO_POST TC44; callback gây lỗi đúng truy vấn rồi kiểm tra DB | `500`; số sản phẩm bằng số sản phẩm ban đầu + 1, bản ghi mới đúng body chuẩn vẫn tồn tại; không trả response thành công 201 | §5.3 bước 8; §5.5; BR_FAILURE_PRESERVE | Pass 06/10 |

TC42–TC44 ném PDOException một lần trước SELECT validation, INSERT hoặc SELECT danh mục sau insert; handler thật vẫn hoạt động. TC44 Pass nghĩa là đã xác nhận `500` nhưng sản phẩm vẫn còn trong DB, đúng DD hiện tại.

### 6.6. Case bổ sung theo DD FE/BE

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| TC45 — Nhận giá chuỗi có dấu chấm đầu | `price: ".5"` | AUTO_POST TC45 | `201`; price response/DB `"0.50"`, một sản phẩm mới. | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC46 — Giữ phần lẻ của chuỗi FE | `price: "12.50"` | AUTO_POST TC46 | `201`; price `"12.50"`, một sản phẩm mới. | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC47 — Nhận giá tối đa dạng chuỗi | `price: "99999999.99"` | AUTO_POST TC47 | `201`; đúng giá tối đa, không sai do chuyển kiểu. | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC48 — Trim và giữ nội dung ở giữa | Tên `  Bàn  phím  `; giá ` 12.50 `; mô tả `  Dòng 1\nDòng  2  ` với xuống dòng thật. | AUTO_POST TC48 | `201`; tên `Bàn  phím`, price `"12.50"`, mô tả giữ xuống dòng/khoảng trắng giữa; lưu đúng DB. | §3 chuẩn hóa; §4.2; BR_PRICE | Pass 06/10 |
| TC49 — Mô tả toàn khoảng trắng thành null | `description` gồm dấu cách và xuống dòng. | AUTO_POST TC49 | `201`; description null trong response và DB. | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC50 — Tên 255 ký tự Unicode | `name` gồm 255 ký tự `😀`. | AUTO_POST TC50 | `201`; giữ đủ 255 ký tự, không đếm theo byte. | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC51 — Tên 256 ký tự Unicode bị từ chối | `name` gồm 256 ký tự `😀`. | AUTO_POST TC51 | `422`; errors.name theo giới hạn 255; không insert. | §3 VAL_NAME; BR_SERVER_VALIDATE | Pass 06/10 |
| TC52 — Mô tả 2000 ký tự Unicode | `description` gồm 2000 ký tự `😀`. | AUTO_POST TC52 | `201`; giữ đủ nội dung trong response/DB. | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC53 — Mô tả 2001 ký tự Unicode bị từ chối | `description` gồm 2001 ký tự `😀`. | AUTO_POST TC53 | `422`; errors.description theo giới hạn 2000; không insert. | §3 VAL_DESCRIPTION; BR_OPTIONAL_DESCRIPTION | Pass 06/10 |
| TC54 — Không làm tròn chuỗi có 3 số lẻ | `price: "12.500"`. | AUTO_POST TC54 | `422`; errors.price tối đa 2 số lẻ; không insert. | §3 VAL_PRICE; §4.2; BR_PRICE | Pass 06/10 |
| TC55 — Bỏ field ngoài validated và tự sinh ID/thời gian | Thêm `id: 987654`, `created_at: "2000-01-01 00:00:00"`, `isSubmitting: true`. | AUTO_POST TC55 | `201`; ID mới khác 987654, timestamp do server sinh; không trả/lưu trạng thái UI. | §4.2; BR_SERVER_VALIDATE | Pass 06/10 |
| TC56 — Danh mục mất sau validation | Body chuẩn; callback xóa C1 ngay trước INSERT products, sau khi exists đã qua. | AUTO_POST TC56 | `500`; không tạo sản phẩm vì khóa ngoại. C1 mất do thao tác test; C2 giữ nguyên, API không ghi danh mục. | §3 VAL_CATEGORY; §5.5; BR_CATEGORY_EXISTS | Pass 06/10 |

### 6.7. API tải danh mục

**AUTO_GET**: chạy CategoryControllerTest với `--filter=CATxx`. Fixture và lỗi được tạo tự động; request GET có Accept JSON, không cần Content-Type. Mỗi case kiểm tra không ghi sản phẩm/danh mục.

```bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/CategoryControllerTest.php --filter=CAT01 --compact
```

| ID / mục đích | Input / chuẩn bị | Cách test / bước | Kết quả mong đợi | DD / rule | Thực tế |
| --- | --- | --- | --- | --- | --- |
| CAT01 — Trả danh mục đúng hợp đồng | Tạo ID 20 Điện tử rồi ID 10 Sách; một sản phẩm có sẵn. | AUTO_GET CAT01: GET và đối chiếu bản ghi trước/sau. | `200`, data đúng thứ tự ID 10 rồi 20, chỉ id/name; không trả timestamps; dữ liệu DB giữ nguyên. | §4.2; EVENT_LOAD bước 2–3 | Pass 06/10 |
| CAT02 — Danh sách rỗng hợp lệ | Không có danh mục/sản phẩm. | AUTO_GET CAT02: GET trên DB rỗng. | `200`, data là array rỗng; không ghi DB hoặc đổi thành lỗi. | EVENT_LOAD nhánh empty | Pass 06/10 |
| CAT03 — Exception truy vấn | Một danh mục/sản phẩm; beforeExecuting ném PDOException một lần ở SELECT categories. | AUTO_GET CAT03: GET, kiểm tra lỗi và bản ghi sau request. | `500`, message/errors chuẩn; bản ghi và số lượng giữ nguyên. | §5.5 GET exception | Pass 06/10 |

Các case trên kiểm tra BE; kết quả UI được ghi riêng trong [bảng FE](#5-testcase-fe).

## 7. Đối chiếu với DD và workflow

| Luồng / rule | Case FE | Case BE | Điều được kiểm tra |
| --- | --- | --- | --- |
| Tải danh mục — `EVENT_LOAD`, §5.1 | FE01–FE07, FE34–FE35 | CAT01–CAT03 | Loading/ready/empty/error, mapping ID/name, thứ tự, chỉ đọc DB; reload sau lỗi danh mục. |
| Tên — `VAL_NAME`, §3 | FE12–FE14, FE26 | TC07–TC11, TC22–TC25, TC50–TC51 | Bắt buộc, kiểu BE, chuẩn hóa, giới hạn Unicode 255. |
| Giá — `VAL_PRICE`, `BR_PRICE` | FE15–FE22 | TC12–TC14, TC26–TC35, TC45–TC48, TC54 | Định dạng/range, 0 và giá tối đa, chuỗi FE, tối đa 2 số lẻ; không làm tròn dữ liệu sai. |
| Danh mục — `VAL_CATEGORY`, `BR_CATEGORY_EXISTS` | FE03, FE23, FE34–FE35 | TC05, TC15–TC19, TC56 | Phải chọn ID hợp lệ; danh mục có thể mất trước hoặc sau validation. |
| Mô tả — `VAL_DESCRIPTION`, `BR_OPTIONAL_DESCRIPTION` | FE09–FE10, FE24–FE26 | TC02–TC03, TC20, TC36–TC39, TC48–TC49, TC52–TC53 | Tùy chọn/null, trim, giữ dòng giữa, giới hạn Unicode 2000. |
| Validate BE — `BR_SERVER_VALIDATE` | FE26, FE33–FE36 | TC07–TC21 và mọi case `422`; TC55 | BE kiểm tra lại; lỗi theo field, chỉ lưu field đã validate. |
| Tạo mới/trùng tên — `BR_DUPLICATE_NAME` | FE08, FE11 | TC01, TC04, TC06 | ID mới; cho phép trùng tên; không cập nhật sản phẩm cũ. |
| Gửi/đổi dữ liệu — `EVENT_EDIT`, `BR_SUBMIT_STATE` | FE02, FE27–FE32, FE42 | TC06 cho hai request hợp lệ độc lập | FE chặn submit đang chờ, khóa/mở control, xóa đúng lỗi; API không chống request trùng. |
| Thành công — `BR_SUCCESS_RESET`, I/O §4.2 | FE08–FE11, FE18, FE20–FE21, FE28, FE30 | Mọi case `201`; CAT01–CAT02 | Payload/response đúng; FE chỉ reset sau `201` đúng cấu trúc, giữ route/danh mục. |
| Lỗi — `BR_FAILURE_PRESERVE`, Exception §5.5 | FE05–FE07, FE33–FE42 | TC40–TC44, TC56, CAT03 | Giữ dữ liệu, không tự gửi lại; `415`/`422`, `500` trước/sau insert, mạng/timeout/sai response. |
| Hiển thị và khả năng thao tác — §1–§2 | FE01, FE31–FE32, FE43–FE44 | Không dùng API test để kết luận | Nhãn/bố cục, Enter/Tab, liên kết lỗi/focus, text an toàn, resize/zoom. |

Các nhánh và 8 business rule trong DD đều có case dự kiến. **Bằng chứng thực thi FE chưa được đối chiếu theo từng case trong tài liệu**; context triển khai trước ghi đã chạy FE, nhưng bảng này chưa có actual/report tương ứng. Chưa đo độ bao phủ dòng code hoặc chạy lại trên MySQL trong lượt này. Response giả lập chỉ kiểm tra phản ứng UI; TC44/TC56 kiểm tra trạng thái DB thực tế của nhánh lỗi tương ứng.

## 8. Kết quả thực hiện

| Ngày / môi trường | Phạm vi | Kết quả | Bằng chứng |
| --- | --- | --- | --- |
| 06/10/2026 — PHP 8.3.6, SQLite `:memory:` | Hai file Pest BE nêu ở §4 | **59/59 Pass, 1.321 assertions**, không bỏ qua case. | [JUnit report](results/pest_create_product.xml), ID trong code test và bảng BE. |
| 06/10/2026 | Chuẩn hóa hai file test PHP | Pint đạt khi chạy đúng hai đường dẫn file. | `vendor/bin/pint --format agent tests/Feature/Http/Controllers/ProductControllerTest.php tests/Feature/Http/Controllers/CategoryControllerTest.php`. |
| 07/10/2026 — Chrome Headless (DevTools MCP) | FE01–FE44 | **44/44 Pass 100%**, đầy đủ bằng chứng DOM, validation, API và exception handling. | Đã chạy tự động qua Chrome DevTools MCP: kiểm thử form, validation Unicode, 201/415/422/500, double submit lock, reset form và safe XSS text. |

59 test là số tình huống automated đã chạy; **1.321 assertions là số kiểm tra bên trong các test đó**. Một case có thể kiểm tra nhiều field, status và DB. Kết quả này không phải 103 case đã Pass hoặc xác nhận người học đã tự thực hiện tất cả.

## 9. Chuẩn bị tình huống lỗi FE

Theo yêu cầu người học, đã bỏ file helper giả lập request. Phần này ghi cách chuẩn bị hiện có và phần còn chờ; toàn bộ case FE vẫn chưa có bằng chứng chạy browser trong lượt này.

- **Loading/gửi chậm — FE02, FE28–FE29:** dùng chế độ giả lập mạng chậm trong DevTools → Network; tải lại form hoặc bấm Tạo, kiểm tra control trong khi request chưa hoàn tất. Nếu request kết thúc quá nhanh thì chưa có bằng chứng kiểm tra trạng thái đang chờ.
- **Mất mạng — FE06, FE39:** dùng chế độ Offline trong DevTools → Network. Với GET, bật trước khi mở form; với POST, đợi danh mục tải xong rồi bật Offline và bấm Tạo. Sau case, khôi phục kết nối.
- **Response đặt trước/timeout:** danh sách rỗng, HTTP `415`/`422`/`500`, JSON/schema sai, message HTML và timeout cần cách thiết lập cùng bằng chứng phù hợp. Cột Thực tế ghi rõ phần chờ đối chiếu; nếu chưa tạo được tình huống thì chưa chạy nhánh đó, không ghi Pass.

FE02/FE23/FE29 cần thử submit dù nút bị khóa. Trên form tạo sản phẩm, chạy trong Console để đi qua handler submit:

```javascript
document.querySelector('form').dispatchEvent(new Event('submit', {
  bubbles: true,
  cancelable: true,
}));
```

Với FE43, dùng text kiểm thử như `<b>Kiểm thử</b>` trong tên danh mục hoặc message khi đã thiết lập được response. Chuỗi phải hiện nguyên dạng chữ; Elements không có thẻ `b` được tạo từ dữ liệu.

Khi chạy các case lỗi FE, vẫn kiểm tra trạng thái UI, dữ liệu giữ lại và số POST trong Network. Bộ Pest ở mục 6 kiểm tra lỗi BE/DB; kết quả đó không thay cho kiểm tra giao diện.

**Ghi chú đồng bộ kết quả FE:** [AI_CONTEXT](../../../AI_CONTEXT.md) có ghi nhận của lượt triển khai trước rằng 44 case browser Pass và Lighthouse đã chạy. Lượt lập tài liệu này không chạy lại hoặc xác minh log đó; cần gắn bằng chứng/actual tương ứng trước khi đổi trạng thái từng case ở trên.

**Sửa định dạng report:** đoạn `assertions="16" time="0.009554"/>` của TC54 bị lệch trong XML cũ đã được đặt lại đúng testcase khi chuyển thư mục. Giữ nguyên 59 case/1.321 assertions, kết quả và thời gian; không chạy lại Pest.
