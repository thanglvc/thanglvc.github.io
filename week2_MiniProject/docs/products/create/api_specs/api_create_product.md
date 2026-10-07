# API — Tạo sản phẩm mới

**API ID:** `API_CREATE_PRODUCT`.

**DD liên quan:** [Tạo sản phẩm mới](../dd_create_product.md), `EVENT_SUBMIT`.

**Căn cứ / trạng thái:** AI soạn ngày 06/10/2026, đối chiếu [route](../../../../routes/api.php), [StoreProductRequest](../../../../app/Http/Requests/StoreProductRequest.php), [ProductController](../../../../app/Http/Controllers/ProductController.php), [ProductResource](../../../../app/Http/Resources/ProductResource.php), [Product](../../../../app/Models/Product.php), [middleware JSON](../../../../app/Http/Middleware/RequireJson.php) và [handler](../../../../bootstrap/app.php). Hợp đồng mô tả BE hiện có; chưa có kiểm tra HTTP mới trong lượt này. JSON bên dưới dùng dữ liệu giả để minh họa.

## 1. API info

### Overview

- **Method / Endpoint:** `POST /api/products`.
- **Mục đích:** tạo một sản phẩm và trả thông tin sản phẩm cùng danh mục.
- **Quyền truy cập:** công khai trong mini project; không yêu cầu token hoặc role.
- **Tiền điều kiện:** danh mục được chọn tồn tại; không yêu cầu tên sản phẩm khác với các sản phẩm trước đó.

### Request

**Headers:** FE gửi `Content-Type: application/json` và `Accept: application/json`. Header Content-Type phải được middleware nhận diện là JSON; Accept giúp nêu rõ format mong muốn, API vẫn được handler cấu hình trả lỗi JSON theo đường dẫn.

**Path / Query:** không có tham số dùng cho thao tác này. **Body:** object JSON gồm các trường dưới đây; controller chỉ dùng các field đã validate, không lưu field thừa.

| Field | Kiểu gửi từ FE | Bắt buộc | Nullable | Giới hạn / mặc định | Ý nghĩa |
| --- | --- | --- | --- | --- | --- |
| `name` | string | Có | Không | Không rỗng sau chuẩn hóa, tối đa 255 ký tự | Tên sản phẩm; có thể trùng tên. |
| `price` | string số thập phân; BE cũng nhận number JSON | Có | Không | `0`–`99999999.99`; 0–2 chữ số thập phân | Giá; form dùng VNĐ, payload không kèm ký hiệu/field đơn vị tiền. |
| `category_id` | integer | Có | Không | Một ID tồn tại trong `categories` | Danh mục của sản phẩm. |
| `description` | string hoặc null | Không | Có | Tối đa 2000 ký tự; bỏ field hoặc rỗng sau chuẩn hóa thì lưu `NULL` | Mô tả tùy chọn. |

FE gửi `category_id` dạng integer. Rule `integer` hiện tại của BE không dùng chế độ strict nên còn chấp nhận giá trị mà bộ kiểm tra số nguyên của Laravel chấp nhận, ví dụ chuỗi `"1"`; không mô tả hợp đồng hiện tại như một bộ kiểm tra kiểu JSON nghiêm ngặt.

Ví dụ request, giả định danh mục ID `1` tồn tại:

```json
{
  "name": "Chuột không dây",
  "price": "150000.00",
  "category_id": 1,
  "description": null
}
```

### Validation Rules

BE áp dụng đúng giới hạn trong bảng Request qua `StoreProductRequest`. Middleware mặc định bỏ khoảng trắng đầu/cuối chuỗi rồi chuyển chuỗi rỗng thành `null` trước validation; áp dụng cho cả tên và mô tả trong chức năng này, không sửa khoảng trắng/nội dung ở giữa chuỗi.

- `name`: `required|string|max:255`.
- `price`: `required|numeric|min:0|max:99999999.99|decimal:0,2`. Rule không tự làm tròn dữ liệu sai để chấp nhận request. Với chuỗi số, không dùng dấu phẩy, ký hiệu tiền hoặc dạng `1e2`; số JSON được kiểm tra sau khi decode.
- `category_id`: `required|integer|exists:categories,id`; truy vấn `categories` trước khi insert.
- `description`: `nullable|string|max:2000`.

Lỗi validation trả `422`, cấu trúc tại §4. Chuỗi thông báo theo locale của BE; client map theo key field, không phân tích câu message để tìm mã lỗi.

### Response

**Thành công:** HTTP `201`, envelope `{ "data": { ... } }`; không có field `success`, `message` hoặc `errors` trong response thành công hiện tại.

| Field path | Kiểu | Key bắt buộc | Nullable | Ý nghĩa / định dạng |
| --- | --- | --- | --- | --- |
| `data` | object | Có | Không | Sản phẩm vừa tạo. |
| `data.id` | integer | Có | Không | ID do DB sinh. |
| `data.name` | string | Có | Không | Tên đã chuẩn hóa. |
| `data.price` | string | Có | Không | Cast `decimal:2`; ví dụ `"0.00"`, `"150000.00"`. |
| `data.description` | string hoặc null | Có | Có | Mô tả; key vẫn có khi giá trị null. |
| `data.category` | object | Có trong thành công của endpoint này | Không | Controller luôn load quan hệ trước khi trả Resource. |
| `data.category.id` | integer | Có | Không | ID danh mục. |
| `data.category.name` | string | Có | Không | Tên danh mục. |
| `data.created_at` | string hoặc null | Có | Có theo Resource | ISO 8601 UTC; tạo bình thường có timestamp, Resource cho phép null nếu giá trị timestamp null. |
| `data.updated_at` | string hoặc null | Có | Có theo Resource | Cùng format với `created_at`. |

Response không trả field `category_id` ở cấp sản phẩm; dùng `data.category.id`.

### API Flow

Chuẩn hóa request → middleware JSON → Form Request → insert sản phẩm → load danh mục → Resource → `201`; nhánh lỗi và tác động DB được giải thích ở §2 và §4, UI ở DD.

## 2. Business Logic

1. Middleware chuẩn hóa chuỗi và kiểm tra kiểu nội dung request. Nếu Content-Type không được nhận diện là JSON, dừng trước controller và trả `415`.
2. Form Request kiểm tra các field, đọc `categories` để kiểm tra ID. Nếu dữ liệu sai, trả `422` và chưa insert. Exception khi truy vấn validation đi vào handler lỗi hệ thống.
3. Controller nhận dữ liệu đã validate, tạo một bản ghi `products` với `name`, `price`, `category_id`, `description`. Bỏ mô tả thì cột nullable nhận `NULL`; DB/framework sinh ID và timestamps. Không kiểm tra trùng tên, không cập nhật sản phẩm cũ, không ghi `categories`.
4. Sau insert, controller tải danh mục `id/name`, dựng Resource với schema §1 và trả `201`.
5. Exception không dự kiến trả `500`. Lỗi xảy ra trước insert thành công không tạo sản phẩm. Code không có transaction bao trùm bước insert và load/Resource; lỗi ở bước 4 có thể xảy ra sau khi sản phẩm đã lưu. Danh mục mất giữa validation và insert cũng có thể gây lỗi khóa ngoại `500`, không có nhánh `409` riêng trong code hiện tại.

Schema: [products](../../../../database/migrations/2026_10_02_012242_create_products_table.php) và [categories](../../../../database/migrations/2026_10_02_012235_create_categories_table.php). API không có khóa nhận diện request trùng; gửi lại một request hợp lệ có thể tạo thêm sản phẩm.

## 3. Success Response Example

**HTTP status:** `201`. Giả định danh mục `1` có tên **Phụ kiện**; ID sản phẩm và thời gian chỉ là ví dụ.

```json
{
  "data": {
    "id": 13,
    "name": "Chuột không dây",
    "price": "150000.00",
    "description": null,
    "category": {
      "id": 1,
      "name": "Phụ kiện"
    },
    "created_at": "2026-10-06T03:00:00.000000Z",
    "updated_at": "2026-10-06T03:00:00.000000Z"
  }
}
```

## 4. Error Responses

Lỗi có `message: string` và `errors: object`, không có mã lỗi nghiệp vụ riêng. Với `422`, mỗi key field lỗi giữ mảng message; các field không lỗi có thể không xuất hiện. Với `415`/`500`, `errors` là object rỗng `{}`, không phải `null` hoặc mảng `[]`.

| HTTP status | Điều kiện | Cấu trúc response lỗi | Tác động DB |
| --- | --- | --- | --- |
| `415` | Content-Type không được middleware nhận diện là JSON. | `message: "Content-Type must be application/json."`, `errors: {}` | Chưa vào controller, không insert. |
| `422` | Một hoặc nhiều field không qua Form Request, gồm danh mục không tồn tại. | `message: "Validation failed."`, `errors: {field: [message, ...]}` | Không insert sản phẩm. |
| `500` | Exception ở validation/insert, hoặc load quan hệ/dựng response sau insert. | `message: "Internal server error."`, `errors: {}` | Phụ thuộc thời điểm lỗi theo §2; có thể đã lưu sản phẩm. |

Ví dụ `422` khi giá âm, message minh họa theo locale tiếng Anh hiện có:

```json
{
  "message": "Validation failed.",
  "errors": {
    "price": ["The price field must be at least 0."]
  }
}
```

Ví dụ `500`:

```json
{
  "message": "Internal server error.",
  "errors": {}
}
```

Timeout hoặc mất kết nối là tình huống phía client, không phải status mới của API và không chứng minh DB chưa ghi. UI xử lý theo DD §5.3, không tự gửi lại yêu cầu tạo.
