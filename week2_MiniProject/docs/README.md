# Tài liệu chức năng — Mini Project tuần 2

**Cập nhật:** 06/10/2026. Áp dụng pipeline **raw spec → BD → DD → testcase → code** cho mọi chức năng nghiệp vụ hiện có. Mỗi chức năng có trọn bộ tài liệu trong một thư mục; API specs và workflow thuộc bước DD.

## Bắt đầu đọc ở đâu?

1. Đọc raw spec: User Story, yêu cầu, phạm vi và giả định.
2. Review BD: giao diện, field, thao tác và kết quả, viết cho người dùng.
3. Review DD: thiết kế FE/BE, Input/Output, validation, xử lý DB, exception và workflow; mở API specs từ DD để xem hợp đồng endpoint.
4. Đọc testcase: case FE/BE, dữ liệu, cách manual/automated, expected và phần DD được kiểm tra.
5. Sau khi tài liệu được duyệt, triển khai code theo cùng phạm vi; chạy test và bổ sung actual/report. Tài liệu đã soạn không có nghĩa code hoặc test đã hoàn thành.

Người dùng đã yêu cầu tạo toàn bộ bộ tài liệu trong một lượt. Bộ **tạo sản phẩm đã được duyệt** được giữ và chuyển thư mục. Các bộ mới là **bản nháp chờ review**; BE mô tả source hiện có, UI mới là đề xuất. Source giao diện tạo đã có; context của lượt triển khai trước ghi đã chạy 44 FE case Pass. Lượt này chưa chạy lại/đối chiếu log browser và testcase chưa gắn actual/bằng chứng từng case. Không tự thêm auth, ảnh, tìm kiếm/lọc hoặc CRUD danh mục.

## Danh mục theo module và chức năng

| Module / chức năng | Raw spec | BD | DD + workflow | Testcase FE/BE | Trạng thái |
| --- | --- | --- | --- | --- | --- |
| Sản phẩm — danh sách | [Raw](products/list/raw_spec_list_products.md) | [BD](products/list/bd_list_products.md) | [DD](products/list/dd_list_products.md) | [Testcase](products/list/testcase_list_products.md) | Bản nháp; UI /products đề xuất; API đã có |
| Sản phẩm — tạo mới | [Raw](products/create/raw_spec_create_product.md) | [BD](products/create/bd_create_product.md) | [DD](products/create/dd_create_product.md) | [Testcase](products/create/testcase_create_product.md) | Thiết kế đã duyệt; source FE đã có; cần đối chiếu bằng chứng browser cũ |
| Sản phẩm — chi tiết | [Raw](products/detail/raw_spec_view_product.md) | [BD](products/detail/bd_view_product.md) | [DD](products/detail/dd_view_product.md) | [Testcase](products/detail/testcase_view_product.md) | Bản nháp; UI /products/{id} đề xuất; API đã có |
| Sản phẩm — cập nhật | [Raw](products/update/raw_spec_update_product.md) | [BD](products/update/bd_update_product.md) | [DD](products/update/dd_update_product.md) | [Testcase](products/update/testcase_update_product.md) | Bản nháp; UI edit đề xuất; BE có PUT và PATCH |
| Sản phẩm — xóa | [Raw](products/delete/raw_spec_delete_product.md) | [BD](products/delete/bd_delete_product.md) | [DD](products/delete/dd_delete_product.md) | [Testcase](products/delete/testcase_delete_product.md) | Bản nháp; thao tác từ list/detail, không thêm trang riêng |
| Danh mục — tải lựa chọn | [Raw](categories/list/raw_spec_load_categories.md) | [BD](categories/list/bd_load_categories.md) | [DD](categories/list/dd_load_categories.md) | [Testcase](categories/list/testcase_load_categories.md) | Use case dùng chung trong form tạo/sửa; API đã có |

Trang welcome mặc định, health route và route hạ tầng không phải chức năng nghiệp vụ của mini project. Danh mục chỉ có API đọc; không tạo màn quản lý danh mục mới.

## Cấu trúc thư mục

```text
docs/
├── README.md
├── shared/
│   ├── data_model.md
│   └── api_conventions.md
├── products/
│   ├── list/
│   ├── create/
│   ├── detail/
│   ├── update/
│   └── delete/
└── categories/
    └── list/
```

Mỗi thư mục chức năng chứa `raw_spec_<feature_slug>.md`, `bd_<feature_slug>.md`, `dd_<feature_slug>.md`, `testcase_<feature_slug>.md`, `api_specs/` và `diagrams/`. File viết chữ thường, dấu `_`, có loại tài liệu và chức năng để mở riêng vẫn hiểu được. Chỉ tạo `results/` khi có bằng chứng test thật; hiện report cũ được giữ tại `products/create/results/`. Code ứng dụng ở app/resources/routes; code test ở tests, không đặt code vào docs.

## Tên file trong mỗi chức năng

| Thư mục | Slug của bộ tài liệu | DD |
| --- | --- | --- |
| products/list | list_products | dd_list_products.md |
| products/create | create_product | dd_create_product.md |
| products/detail | view_product | dd_view_product.md |
| products/update | update_product | dd_update_product.md |
| products/delete | delete_product | dd_delete_product.md |
| categories/list | load_categories | dd_load_categories.md |

Ví dụ bộ tạo sản phẩm:

```text
docs/products/create/
├── raw_spec_create_product.md
├── bd_create_product.md
├── dd_create_product.md
├── testcase_create_product.md
├── api_specs/api_create_product.md
├── diagrams/
│   ├── fig_create_product_load_workflow.svg
│   └── fig_create_product_workflow.svg
└── results/pest_create_product.xml
```

API luôn có tiền tố `api_`; nếu cùng mục đích có hai method PUT/PATCH, dùng `api_update_product_put.md` và `api_update_product_patch.md`. Method + path trong nội dung vẫn là định danh operation. Quy ước này đã được cập nhật trong skill `dd-api-specs`.

## API chuẩn và nơi dùng

| Method + path | Bản đặc tả chuẩn | DD dùng API |
| --- | --- | --- |
| GET /api/products | [Danh sách](products/list/api_specs/api_list_products.md) | Danh sách; tải lại sau xóa |
| POST /api/products | [Tạo mới](products/create/api_specs/api_create_product.md) | Tạo sản phẩm |
| GET /api/products/{product} | [Chi tiết](products/detail/api_specs/api_get_product.md) | Chi tiết; điền form cập nhật |
| PUT /api/products/{product} | [Cập nhật đầy đủ](products/update/api_specs/api_update_product_put.md) | Form cập nhật |
| PATCH /api/products/{product} | [Cập nhật một phần](products/update/api_specs/api_update_product_patch.md) | Client cập nhật từng field; không có form PATCH riêng |
| DELETE /api/products/{product} | [Xóa](products/delete/api_specs/api_delete_product.md) | Xóa từ danh sách/chi tiết |
| GET /api/categories | [Tải danh mục](categories/list/api_specs/api_load_categories.md) | Form tạo/cập nhật |

Một API chỉ có một bản đặc tả chuẩn. Chức năng khác liên kết tới bản đó; không chép GET danh mục hoặc GET chi tiết vào nhiều thư mục. API specs và DD dùng [schema](shared/data_model.md) và [hợp đồng chung](shared/api_conventions.md) khi cần.

## Kiểm thử và thứ tự triển khai

- Tạo sản phẩm giữ **44 FE case chờ đối chiếu bằng chứng trong testcase** và **56 POST + 3 category case có kết quả Pest cũ**. [JUnit](products/create/results/pest_create_product.xml) ghi 59 case, 1.321 assertions; không chạy lại trong lượt soạn tài liệu này.
- Các bộ mới bổ sung **115 testcase**: danh sách 25, chi tiết 15, cập nhật 47, xóa 17, danh mục 11. Từng case có expected, bước thực hiện và tham chiếu DD. Case có nhiều giá trị phải chạy riêng từng giá trị, không coi một lần chạy là đủ.
- CAT01–CAT03 được dẫn lại ở danh mục cùng bằng chứng cũ; không tính là ba case mới. Case mới chưa có actual/Pass. FE không kế thừa Pass từ API.
- Manual có route, fixture, thao tác và kiểm tra request/response/DB. Lệnh Pest chỉ ghi cho file test thực sự đã có. Các Feature Test mới và tình huống exception chưa có code được đánh dấu chờ triển khai/thiết lập; không có report giả.

Sau review, có thể kiểm tra lại form tạo đã có, triển khai danh sách → chi tiết → cập nhật → xóa. Tải danh mục là dependency chung của hai form. Mỗi lần bàn giao một chức năng, đọc đủ raw spec/BD/DD/API/testcase của nó và các API dùng chung, rồi ghi bằng chứng đúng phạm vi.

## Quy ước áp dụng cho chức năng tiếp theo

Tạo `docs/<module>/<feature>/` theo cấu trúc này, đặt User Story đầu raw spec. BD giữ ngắn, dễ hiểu; DD giữ 7 mục chính và có Input/Output, Exception Case, workflow FE/BE; API specs giữ 4 mục. Testcase là một tài liệu chứa FE/BE và cách test, không tách testplan. Review theo pipeline; không tự đánh dấu tài liệu được duyệt hoặc người học hoàn thành ngày học.

**Prompt triển khai module sản phẩm:** [prompt_implement_products.md](products/prompt_implement_products.md). Prompt giao triển khai UI và kiểm thử từ bộ tài liệu hiện tại; không tự thay trạng thái duyệt hoặc ghi kết quả test chưa chạy.
