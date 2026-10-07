# Raw spec — Tạo sản phẩm mới

**Trạng thái:** AI soạn ngày 06/10/2026; người học đã review đồng ý raw spec và [BD tạo sản phẩm](bd_create_product.md), cho phép chuyển sang [DD](dd_create_product.md). Đã chốt VNĐ và đường dẫn `/products/create`.

## 1. User Story

> Là người dùng, tôi muốn nhập thông tin và chọn danh mục trên form tạo sản phẩm, để lưu sản phẩm mới vào hệ thống.

## 2. Nhu cầu và phạm vi đã chốt

Người dùng cần tạo sản phẩm mới bằng một form trên trình duyệt, sử dụng API `POST /api/products` của mini project tuần 2.

- **Trong phạm vi:** một form nhập tên, giá, danh mục và mô tả; gửi yêu cầu tạo; hiển thị kết quả hoặc lỗi. Danh mục lấy từ API có sẵn `GET /api/categories`.
- **Căn cứ thiết kế:** dùng các trường và quy tắc BE (phía máy chủ) hiện có; bố cục/cách thao tác FE (giao diện trình duyệt) trong BD đã được người học review đồng ý. Chi tiết kỹ thuật được thiết kế ở bước DD.
- **Ngoài phạm vi:** đăng nhập/phân quyền, ảnh sản phẩm, xem/sửa/xóa sản phẩm và tạo/sửa/xóa danh mục.

## 3. Dữ liệu và quy tắc hiện có

| Dữ liệu | Trường API | Quy tắc từ code hiện tại |
| --- | --- | --- |
| Tên sản phẩm | `name` | Bắt buộc, chuỗi không rỗng, tối đa 255 ký tự; không cấm trùng tên. |
| Giá (VNĐ) | `price` | Bắt buộc, số từ `0` đến `99999999.99`, tối đa 2 chữ số thập phân. |
| Danh mục | `category_id` | Bắt buộc, số nguyên chỉ đến một danh mục đang tồn tại. |
| Mô tả | `description` | Không bắt buộc; chuỗi tối đa 2000 ký tự, cho phép để trống hoặc `null`. |

API nhận dữ liệu JSON. Thành công trả `201` cùng sản phẩm đã tạo; dữ liệu không hợp lệ trả `422`; sai kiểu nội dung gửi lên trả `415`; lỗi hệ thống không dự kiến trả `500`.

**Lưu ý để thiết kế thông báo:** code hiện tại có thể đã lưu sản phẩm rồi mới gặp lỗi ở bước tiếp theo. Khi lỗi hệ thống hoặc mất kết nối, giao diện cần giữ dữ liệu đã nhập và không tự gửi lại yêu cầu tạo.

## 4. Thiết kế đã duyệt

[BD](bd_create_product.md) mô tả bố cục form, cách chọn danh mục, thông báo lỗi và trạng thái đang gửi; người học đã review đồng ý.

- **Sau thành công:** giữ nguyên màn hình, hiện thông báo và xóa các giá trị đã nhập để tạo sản phẩm tiếp theo, theo BD đã duyệt.
- **Đơn vị tiền:** VNĐ, nhãn **Giá (VNĐ)**; giữ quy tắc API chấp nhận tối đa 2 chữ số thập phân.
- **Đường dẫn màn hình:** `/products/create`, là route FE sẽ được triển khai ở bước code.

## 5. Căn cứ và bước tiếp theo

Đã đối chiếu [route API](../../../routes/api.php), [quy tắc đầu vào](../../../app/Http/Requests/StoreProductRequest.php), [xử lý tạo sản phẩm](../../../app/Http/Controllers/ProductController.php), [schema sản phẩm](../../../database/migrations/2026_10_02_012242_create_products_table.php), [nguồn danh mục](../../../app/Http/Controllers/CategoryController.php), [kiểm tra JSON](../../../app/Http/Middleware/RequireJson.php) và [xử lý lỗi](../../../bootstrap/app.php).

Người học đã cho phép viết DD cùng workflow và API specs; chưa chuyển sang testcase hoặc code.
