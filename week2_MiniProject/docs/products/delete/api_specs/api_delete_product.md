# API — Xóa một sản phẩm

**API ID:** API_DELETE_PRODUCT. **DD:** [Thiết kế chức năng](../dd_delete_product.md).

**Căn cứ:** route/controller/Form Request/Resource hiện có ngày 06/10/2026. Đây là đặc tả source, không phải biên bản chạy API mới. [Route API](../../../../routes/api.php), [ProductController](../../../../app/Http/Controllers/ProductController.php), [UpdateProductRequest](../../../../app/Http/Requests/UpdateProductRequest.php), [ProductResource](../../../../app/Http/Resources/ProductResource.php), [Handler](../../../../bootstrap/app.php).

## 1. API info

### Overview

- **Method / Endpoint:** `DELETE /api/products/{product}`.
- **Mục đích:** Xóa một sản phẩm.
- **Quyền:** API công khai hiện có; không yêu cầu token trong phạm vi mini project.

### Request

**Headers:** `Accept: application/json`; không cần Content-Type vì không gửi body.

| Vị trí | Field | Kiểu | Bắt buộc / nullable | Giới hạn / ý nghĩa |
| --- | --- | --- | --- | --- |
| path | product | ID số nguyên | Có / không null | ID sản phẩm cần đọc/ghi; không thấy model →404. |

**Ví dụ request:** `DELETE /api/products/42`. ID 42 và category_id 1 là dữ liệu minh họa; khi chạy dùng ID thật từ DB kiểm thử.

Không có body.

### Validation Rules

| Field | Chuẩn hóa / điều kiện | Kết quả khi sai |
| --- | --- | --- |
| product | Model binding lấy sản phẩm; ID không thấy →404. | 404; không validate form. |
| Headers / body | GET/DELETE được middleware JSON bỏ qua; DELETE không cần body. | Không thêm 415 chỉ vì thiếu Content-Type. |

### Response

**Success:** 204; không body, không JSON envelope. [Schema và lỗi chung](../../../shared/api_conventions.md).

| Field path | Kiểu / nullable | Ý nghĩa |
| --- | --- | --- |
| Body | Rỗng | 204 không có data/message; không parse JSON. |

### API Flow

Request → kiểm tra điều kiện ở mục 1 → xử lý/DB theo mục 2 → response → FE theo [DD](../dd_delete_product.md). Các trường hợp lỗi có một lỗi chính; không cam kết thứ tự ưu tiên khi nhiều lỗi đồng thời.

## 2. Business Logic

1. Model binding đọc sản phẩm theo ID; không có →404.
2. Controller gọi delete() trên model; xóa thật bản ghi, không SoftDeletes.
3. Trả 204 không body. Không xóa categories hoặc sản phẩm khác.
4. Exception trước/sau DELETE có thể trả 500; client lỗi mạng cũng có thể không biết DB đã xóa. Không có cơ chế Undo/idempotency riêng.

[Schema products/categories](../../../shared/data_model.md). Không có transaction bao trùm ghi và dựng response; lỗi sau ghi không tự chứng minh rollback.

## 3. Success Response Example

**HTTP status:** 204.

Body rỗng (0 byte); FE không gọi response.json().

## 4. Error Responses

| Status | Điều kiện | Message / errors | Ảnh hưởng DB |
| --- | --- | --- | --- |
| 404 | ID không có hoặc đã xóa | Resource not found; errors {} | Không xóa bản ghi khác. |
| 500 | Exception xóa hoặc dựng response | Internal server error; errors {} | Nếu lỗi trước DELETE: chưa xóa; sau DELETE: có thể đã xóa. |

**Ví dụ lỗi 500:**

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```
