# API — Danh sách sản phẩm có phân trang

**API ID:** API_LIST_PRODUCTS. **DD:** [Thiết kế chức năng](../dd_list_products.md).

**Căn cứ:** route/controller/Form Request/Resource hiện có ngày 06/10/2026. Đây là đặc tả source, không phải biên bản chạy API mới. [Route API](../../../../routes/api.php), [ProductController](../../../../app/Http/Controllers/ProductController.php), [UpdateProductRequest](../../../../app/Http/Requests/UpdateProductRequest.php), [ProductResource](../../../../app/Http/Resources/ProductResource.php), [Handler](../../../../bootstrap/app.php).

## 1. API info

### Overview

- **Method / Endpoint:** `GET /api/products`.
- **Mục đích:** Danh sách sản phẩm có phân trang.
- **Quyền:** API công khai hiện có; không yêu cầu token trong phạm vi mini project.

### Request

**Headers:** `Accept: application/json`; không cần Content-Type vì không gửi body.

| Vị trí | Field | Kiểu | Bắt buộc / nullable | Giới hạn / ý nghĩa |
| --- | --- | --- | --- | --- |
| query | page | integer qua query string | Không / không null | Mặc định 1; không hợp lệ hoặc <1 quy về 1. |
| body | Không có | — | — | Không có search/filter/per_page được controller dùng. |

**Ví dụ request:** `GET /api/products`. ID 42 và category_id 1 là dữ liệu minh họa; khi chạy dùng ID thật từ DB kiểm thử.

Không có body.

### Validation Rules

| Field | Chuẩn hóa / điều kiện | Kết quả khi sai |
| --- | --- | --- |
| page | Paginator chấp nhận số nguyên >=1; sai quy về 1, không trả 422. | 200 trang 1 nếu truy vấn DB thành công. |
| page > last_page | Không clamp ở BE; trả current_page đã yêu cầu với data []. | 200; FE xử lý theo DD. |
| per_page/search | Không có tính năng; controller luôn paginate(10). | Không coi là filter hay lỗi validate. |

### Response

**Success:** 200; JSON object có data. [Schema và lỗi chung](../../../shared/api_conventions.md).

| Field path | Kiểu / nullable | Ý nghĩa |
| --- | --- | --- |
| data | array ProductResource; [] hợp lệ | ID giảm dần, tối đa 10. |
| links.first / last | string URL | Trang đầu/cuối theo URL server. |
| links.prev / next | string URL hoặc null | Trang liền trước/sau. |
| meta.current_page / last_page / per_page / total | integer | last_page tối thiểu 1; per_page luôn 10. |
| meta.from / to | integer hoặc null | Vị trí bản ghi; null khi page không có data. |
| meta.path / links | string / array | URL gốc và các nhãn liên kết do paginator sinh; có thể có metadata bổ sung theo framework. |

### API Flow

Request → kiểm tra điều kiện ở mục 1 → xử lý/DB theo mục 2 → response → FE theo [DD](../dd_list_products.md). Các trường hợp lỗi có một lỗi chính; không cam kết thứ tự ưu tiên khi nhiều lỗi đồng thời.

## 2. Business Logic

1. Controller tạo query products, eager load category:id,name.
2. Sắp xếp id giảm dần, lấy trang qua paginate(10); không ghi products/categories.
3. Resource collection trả data/links/meta với 200, kể cả hệ thống rỗng hoặc trang vượt cuối.
4. Exception truy vấn/Resource trả 500 bằng handler; FE không coi lỗi là danh sách rỗng.

[Schema products/categories](../../../shared/data_model.md). Không có transaction bao trùm ghi và dựng response; lỗi sau ghi không tự chứng minh rollback.

## 3. Success Response Example

**HTTP status:** 200.

```json
{
  "data": [
    {
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
  ],
  "links": {
    "first": "http://localhost:8000/api/products?page=1",
    "last": "http://localhost:8000/api/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/products?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "path": "http://localhost:8000/api/products",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

## 4. Error Responses

| Status | Điều kiện | Message / errors | Ảnh hưởng DB |
| --- | --- | --- | --- |
| 500 | Query/paginator/Resource gặp exception | Internal server error; errors {} | Không ghi DB. |

**Ví dụ lỗi 500:**

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```
