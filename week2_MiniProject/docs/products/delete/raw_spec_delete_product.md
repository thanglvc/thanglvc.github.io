# Raw spec — Xóa sản phẩm

**Trạng thái:** AI soạn ngày 06/10/2026, bản nháp chờ review. Hợp đồng BE bám source hiện có; phần FE mới là thiết kế đề xuất, chưa được duyệt/triển khai bởi lượt này.

## 1. User Story

Là người dùng, tôi muốn xóa một sản phẩm không còn cần dùng, để danh sách sản phẩm được cập nhật.

## 2. Yêu cầu và phạm vi

- Xác nhận trước khi gửi DELETE; hủy thì không gửi request.
- Xóa bản ghi products theo ID; danh mục không bị xóa.
- Không có soft delete, thùng rác hoặc hoàn tác trong code hiện tại.

## 3. Dữ liệu / điều kiện

- ID từ sản phẩm đã được xác nhận trên màn sở hữu.
- DELETE thành công 204 không body; model binding không thấy ID trả 404.
- RequireJson chỉ kiểm tra POST/PUT/PATCH nên DELETE không cần Content-Type JSON.

## 4. Giả định và điểm cần review

- Đề xuất xác nhận bằng hộp thoại trình duyệt và hành vi refresh/điều hướng; cần review.
- Lỗi mạng chưa xác định được thao tác xóa; không tự gửi lại.

## 5. Căn cứ

[BD](bd_delete_product.md), [DD](dd_delete_product.md), [testcase](testcase_delete_product.md), [dữ liệu dùng chung](../../shared/data_model.md). Source: [Route API](../../../routes/api.php), [ProductController](../../../app/Http/Controllers/ProductController.php), [ProductResource](../../../app/Http/Resources/ProductResource.php), [Handler](../../../bootstrap/app.php).
