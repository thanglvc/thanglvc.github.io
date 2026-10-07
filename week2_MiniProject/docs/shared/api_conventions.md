# Hợp đồng API dùng chung

**Căn cứ:** [ProductResource](../../app/Http/Resources/ProductResource.php), [handler](../../bootstrap/app.php), [RequireJson](../../app/Http/Middleware/RequireJson.php). Đây là mô tả source, không phải kết quả HTTP mới.

## Request và lỗi

- API trong phạm vi hiện tại công khai; không thêm auth/role theo mẫu tham khảo.
- Client gửi Accept: application/json. POST/PUT/PATCH cần Content-Type được nhận diện là JSON; sai/thiếu trả 415. GET/DELETE không bị RequireJson kiểm tra Content-Type.
- Chuỗi được middleware trim đầu/cuối rồi chuỗi rỗng thành null. Không đổi khoảng trắng/nội dung ở giữa. Rule integer hiện có không strict kiểu JSON; FE gửi category_id dạng integer.
- Lỗi có `{message: string, errors: object}`. 422: message Validation failed., errors chứa field → mảng message. 404: Resource not found; 415: Content-Type must be application/json; 500: Internal server error; errors của ba lỗi này là object `{}`.
- Handler có thêm status chung, nhưng không coi 401/403 là nhánh nghiệp vụ của chức năng không có auth. Lỗi mạng/timeout không có response xác nhận và không tự chứng minh thao tác ghi chưa xảy ra.

## ProductResource

| Field trong data | Kiểu / nullable | Ý nghĩa |
| --- | --- | --- |
| id | integer | ID sản phẩm. |
| name | string | Tên sau chuẩn hóa. |
| price | string | Hai chữ số thập phân, ví dụ "12.50". |
| description | string hoặc null | Key vẫn có khi giá trị null. |
| category | object `{id: integer, name: string}` | Endpoint hiện tại load quan hệ; không trả category_id ở cấp sản phẩm. |
| created_at, updated_at | string ISO 8601 UTC hoặc null | Resource cho phép null; dữ liệu bình thường có timestamps. |

Danh sách dùng `data: ProductResource[]`, thêm `links` và `meta`. Chi tiết/cập nhật dùng `data: ProductResource`, status 200. Tạo dùng cùng object, status 201. Xóa thành công trả 204 không body; client không gọi JSON parser cho nhánh 204.

## Quy tắc kiểm tra phía FE

Kiểm tra status và schema trước khi công nhận kết quả. Hiển thị dữ liệu bằng text an toàn; lỗi map theo key, không phân tích câu message tiếng Anh. Chỉ số trang lấy từ số trong meta; không đưa URL trả về từ API vào HTML mà không kiểm tra. Thiết kế form chuẩn hóa/validate theo DD của chức năng; price giữ dạng chuỗi, mô tả trống gửi null, độ dài tính theo ký tự Unicode.

**Schema:** [Dữ liệu dùng chung](data_model.md). Các operation được đặc tả tại thư mục chức năng tương ứng; không sao chép một API danh mục dưới nhiều form.
