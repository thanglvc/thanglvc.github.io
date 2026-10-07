# API — Cập nhật toàn bộ field sản phẩm

**API ID:** API_PUT_PRODUCT. **DD:** [Thiết kế chức năng](../dd_update_product.md).

**Căn cứ:** route/controller/Form Request/Resource hiện có ngày 06/10/2026. Đây là đặc tả source, không phải biên bản chạy API mới. [Route API](../../../../routes/api.php), [ProductController](../../../../app/Http/Controllers/ProductController.php), [UpdateProductRequest](../../../../app/Http/Requests/UpdateProductRequest.php), [ProductResource](../../../../app/Http/Resources/ProductResource.php), [Handler](../../../../bootstrap/app.php).

## 1. API info

### Overview

- **Method / Endpoint:** `PUT /api/products/{product}`.
- **Mục đích:** Cập nhật toàn bộ field sản phẩm.
- **Quyền:** API công khai hiện có; không yêu cầu token trong phạm vi mini project.

### Request

**Headers:** `Accept: application/json`; `Content-Type: application/json` bắt buộc cho thao tác ghi.

| Vị trí | Field | Kiểu | Bắt buộc / nullable | Giới hạn / ý nghĩa |
| --- | --- | --- | --- | --- |
| path | product | ID số nguyên | Có / không null | ID sản phẩm cần đọc/ghi; không thấy model →404. |
| body | name | string | Có / không null | Trim đầu/cuối; không rỗng; <=255 ký tự. |
| body | price | number hoặc numeric string | Có / không null | 0..99999999.99; decimal:0,2; Resource trả string hai số lẻ. |
| body | category_id | integer hoặc integer string theo rule hiện có | Có / không null | exists:categories,id; FE gửi integer. |
| body | description | string hoặc null | Key phải có / cho phép null | Trim đầu/cuối; rỗng→null; <=2000 ký tự, giữ xuống dòng giữa. |

**Ví dụ request:** `PUT /api/products/42`. ID 42 và category_id 1 là dữ liệu minh họa; khi chạy dùng ID thật từ DB kiểm thử.

```json
{
  "name": "Bàn phím",
  "price": "199000",
  "category_id": 1,
  "description": "Bàn phím dùng để thực hành"
}
```

### Validation Rules

| Field | Chuẩn hóa / điều kiện | Kết quả khi sai |
| --- | --- | --- |
| name / price / category_id | PUT bắt buộc từng field; string/max255; numeric/min0/max99999999.99/decimal0,2; integer/exists. | 422 + errors theo field; không update. |
| description | present/nullable/string/max2000 | 422 nếu sai rule. |
| Field lạ / id / timestamps | Chỉ dùng validated; các field ngoài rule không được ghi. | Không thêm lỗi chỉ vì có field lạ. |
| Tên trùng / body {} | Tên trùng được phép; {} thiếu field →422. | Không có rule unique name. |

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

Request → kiểm tra điều kiện ở mục 1 → xử lý/DB theo mục 2 → response → FE theo [DD](../dd_update_product.md). Các trường hợp lỗi có một lỗi chính; không cam kết thứ tự ưu tiên khi nhiều lỗi đồng thời.

## 2. Business Logic

1. Middleware kiểm tra JSON và route binding lấy sản phẩm; lỗi tương ứng 415/404.
2. UpdateProductRequest chọn rule theo method: PUT required ba field và present description.
3. Validation lỗi →422, chưa update; hợp lệ thì controller update bằng validated, không mass-assign field lạ.
4. Chỉ thay sản phẩm được bind; giữ ID/created_at, Eloquent quản lý updated_at khi model thay đổi. Không cam kết timestamp tăng nếu ghi trong cùng giây.
5. Load category:id,name, trả ProductResource 200. Exception sau update có thể xảy ra sau khi DB đã ghi; không có transaction toàn luồng.
6. PATCH {} hoặc field bằng giá trị cũ không làm dữ liệu nghiệp vụ thay đổi; không dùng response để khẳng định đã có chỉnh sửa mới.

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
| 415 | Thiếu/sai Content-Type JSON; dùng ID tồn tại để kiểm tra riêng nhánh này | Content-Type must be application/json; errors {} | Chưa gọi controller. |
| 422 | Field không đạt rule của method | Validation failed; errors {field: [message]} | Chưa update. |
| 404 | Không tìm thấy model theo ID | Resource not found; errors {} | Không thay sản phẩm khác. |
| 500 | Exception trước hoặc sau update/load Resource | Internal server error; errors {} | Trước ghi: chưa đổi; sau ghi: có thể đã đổi. |

**Ví dụ lỗi 500:**

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```

**Ví dụ 422 — giá không hợp lệ:**

```json
{
  "message": "Validation failed.",
  "errors": {
    "price": [
      "The price field must be at least 0."
    ]
  }
}
```

Message từng field do Laravel sinh; FE map bằng key và rule, không phụ thuộc câu tiếng Anh.
