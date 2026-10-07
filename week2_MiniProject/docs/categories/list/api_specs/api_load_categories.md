# API — Tải danh mục

**API ID:** `API_LOAD_CATEGORIES`.

**DD dùng API:** [Tải danh mục](../dd_load_categories.md), [Cập nhật sản phẩm](../../../products/update/dd_update_product.md), [Tạo sản phẩm mới](../../../products/create/dd_create_product.md), `EVENT_LOAD`.

**Căn cứ / trạng thái:** AI soạn ngày 06/10/2026 từ [route](../../../../routes/api.php), [CategoryController](../../../../app/Http/Controllers/CategoryController.php) và [handler](../../../../bootstrap/app.php). Đây là API hỗ trợ dùng chung cho form tạo/cập nhật, không mở rộng sang CRUD danh mục. JSON ví dụ dùng dữ liệu giả, chưa có kiểm tra HTTP mới trong lượt này.

## 1. API info

### Overview

- **Method / Endpoint:** `GET /api/categories`.
- **Mục đích:** cung cấp danh sách để người dùng chọn danh mục khi tạo sản phẩm.
- **Quyền truy cập:** công khai; không yêu cầu token hoặc role.

### Request

FE gửi `Accept: application/json`. Không có path/query parameter dùng cho chức năng này, không có body và không cần Content-Type cho GET.

### Validation Rules

Không có dữ liệu đầu vào để validate. Middleware JSON trong nhóm route chỉ kiểm tra các method ghi, không chặn GET này vì thiếu Content-Type.

### Response

**Thành công:** HTTP `200`, envelope `{ "data": [...] }`. `data` luôn là mảng, có thể rỗng; không phân trang.

| Field path | Kiểu | Key bắt buộc | Nullable | Ý nghĩa |
| --- | --- | --- | --- | --- |
| `data` | array | Có | Không | Danh mục theo ID tăng dần; `[]` nếu không có danh mục. |
| `data[].id` | integer | Có ở mỗi phần tử | Không | ID đưa vào `category_id` của API tạo sản phẩm. |
| `data[].name` | string | Có ở mỗi phần tử | Không | Nhãn hiển thị trong select. |

Không trả timestamps hoặc thông tin sản phẩm trong danh sách này.

### API Flow

Đọc `categories.id/name`, sắp theo `id` tăng dần → JSON `200`; exception → `500`. Chi tiết tại §2.

## 2. Business Logic

1. Controller truy vấn toàn bộ `categories`, chọn `id/name` và sắp xếp theo `id` tăng dần; không lọc theo người dùng, không phân trang.
2. Thành công trả `200` với mảng `data`; không có bản ghi thì vẫn trả `200` và `data: []`.
3. Exception truy vấn được handler trả `500` theo §4. Thao tác này chỉ đọc DB, không thay đổi sản phẩm hoặc danh mục.

Schema: [migration categories](../../../../database/migrations/2026_10_02_012235_create_categories_table.php).

## 3. Success Response Example

**HTTP status:** `200`, danh sách có dữ liệu:

```json
{
  "data": [
    { "id": 1, "name": "Phụ kiện" },
    { "id": 2, "name": "Thiết bị" }
  ]
}
```

**HTTP status:** `200`, danh sách rỗng:

```json
{
  "data": []
}
```

## 4. Error Responses

| HTTP status | Điều kiện | Cấu trúc response lỗi | Tác động DB |
| --- | --- | --- | --- |
| `500` | Exception không dự kiến khi tải danh mục. | `message: "Internal server error."`, `errors: {}` | Chỉ đọc, không ghi DB. |

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```

Lỗi mạng/timeout không có response HTTP xác nhận. FE giữ trạng thái lỗi tải và khóa thao tác tạo như DD §5.1; không coi lỗi tải là danh sách rỗng.
