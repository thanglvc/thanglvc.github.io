# Raw spec — Chi tiết sản phẩm

**Trạng thái:** AI soạn ngày 06/10/2026, bản nháp chờ review. Hợp đồng BE bám source hiện có; phần FE mới là thiết kế đề xuất, chưa được duyệt/triển khai bởi lượt này.

## 1. User Story

Là người dùng, tôi muốn xem đầy đủ thông tin một sản phẩm, để hiểu sản phẩm và chọn sửa hoặc xóa khi cần.

## 2. Yêu cầu và phạm vi

- Xem ID, tên, giá, danh mục, mô tả và thời gian sản phẩm.
- Có link về danh sách, Sửa và nút Xóa theo DD tương ứng.
- Không thêm ảnh, lịch sử thay đổi hoặc phân quyền.

## 3. Dữ liệu / điều kiện

- ID lấy từ route; route model binding trả 404 khi không có sản phẩm.
- GET trả ProductResource với category đã load; không ghi DB.

## 4. Giả định và điểm cần review

- Đề xuất route /products/{id} và các link về danh sách/sửa; cần review.
- Mô tả null hiện Chưa có mô tả; timestamp null hiện —.

## 5. Căn cứ

[BD](bd_view_product.md), [DD](dd_view_product.md), [testcase](testcase_view_product.md), [dữ liệu dùng chung](../../shared/data_model.md). Source: [Route API](../../../routes/api.php), [ProductController](../../../app/Http/Controllers/ProductController.php), [ProductResource](../../../app/Http/Resources/ProductResource.php), [Handler](../../../bootstrap/app.php).
