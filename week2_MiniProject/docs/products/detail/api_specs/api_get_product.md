# API — Lấy chi tiết một sản phẩm

**API ID:** API_GET_PRODUCT. **DD:** [Thiết kế chức năng](../dd_view_product.md).

**Căn cứ:** route/controller/Form Request/Resource hiện có ngày 06/10/2026. Đây là đặc tả source, không phải biên bản chạy API mới. [Route API](../../../../routes/api.php), [ProductController](../../../../app/Http/Controllers/ProductController.php), [UpdateProductRequest](../../../../app/Http/Requests/UpdateProductRequest.php), [ProductResource](../../../../app/Http/Resources/ProductResource.php), [Handler](../../../../bootstrap/app.php).

## 1. API info

### Overview

- **Method / Endpoint:** `GET /api/products/{product}`.
- **Mục đích:** Lấy chi tiết một sản phẩm.
- **Quyền:** API công khai hiện có; không yêu cầu token trong phạm vi mini project.

### Request

**Headers:** `Accept: application/json`; không cần Content-Type vì không gửi body.

| Vị trí | Field | Kiểu | Bắt buộc / nullable | Giới hạn / ý nghĩa |
| --- | --- | --- | --- | --- |
| path | product | ID số nguyên | Có / không null | ID sản phẩm cần đọc/ghi; không thấy model →404. |

**Ví dụ request:** `GET /api/products/42`. ID 42 và category_id 1 là dữ liệu minh họa; khi chạy dùng ID thật từ DB kiểm thử.

Không có body.

### Validation Rules

| Field | Chuẩn hóa / điều kiện | Kết quả khi sai |
| --- | --- | --- |
| product | Route model binding lấy model theo ID, không có Form Request. | 404 khi không tìm thấy. |

### Response

**Success:** 200; JSON object có data. [Schema và lỗi chung](../../../shared/api_conventions.md).

| Field path | Kiểu / nullable | Ý nghĩa |
| --- | --- | --- |
| data | object | ProductResource; đủ 8 key ở hợp đồng chung. |
| data.id / name / price | integer / string / string hai số lẻ | Định danh, tên và giá VNĐ. |
| data.description | string hoặc null | Mô tả, key luôn có. |
| data.category | object {id: integer, name: string} | Quan hệ đã load; không trả category_id cùng cấp. |
| data.created_at / updated_at | string UTC ISO 8601 hoặc null | Server quản lý timestamps. |

### API Flow

Request → kiểm tra điều kiện ở mục 1 → xử lý/DB theo mục 2 → response → FE theo [DD](../dd_view_product.md). Các trường hợp lỗi có một lỗi chính; không cam kết thứ tự ưu tiên khi nhiều lỗi đồng thời.

## 2. Business Logic

1. Route model binding đọc products theo ID; không thấy →404.
2. Controller load category:id,name của sản phẩm.
3. ProductResource trả 200 {data: Product}; không ghi DB.
4. Exception đọc/load/Resource →500; không trả sản phẩm khác thay thế.

[Schema products/categories](../../../shared/data_model.md). Không có transaction bao trùm ghi và dựng response; lỗi sau ghi không tự chứng minh rollback.

## 3. Success Response Example

**HTTP status:** 200.

```json
{
  "data": {
    "id": 42,
    "name": "Bàn phím",
    "price": "199000.00",
    "description": "Bàn phím dùng để thực hành",
    "category": {
      "id": 1,
      "name": "Điện tử"
    },
    "created_at": "2026-10-06T02:00:00.000000Z",
    "updated_at": "2026-10-06T03:00:00.000000Z"
  }
}
```

## 4. Error Responses

| Status | Điều kiện | Message / errors | Ảnh hưởng DB |
| --- | --- | --- | --- |
| 404 | Model binding không tìm thấy sản phẩm | Resource not found; errors {} | Không ghi DB. |
| 500 | Đọc DB/load quan hệ/dựng Resource lỗi | Internal server error; errors {} | Không ghi DB. |

**Ví dụ lỗi 500:**

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```
