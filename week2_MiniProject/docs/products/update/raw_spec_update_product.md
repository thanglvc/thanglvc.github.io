# Raw spec — Cập nhật sản phẩm

**Trạng thái:** AI soạn ngày 06/10/2026, bản nháp chờ review. Hợp đồng BE bám source hiện có; phần FE mới là thiết kế đề xuất, chưa được duyệt/triển khai bởi lượt này.

## 1. User Story

Là người dùng, tôi muốn sửa thông tin một sản phẩm đã có, để thông tin sản phẩm được cập nhật đúng.

## 2. Yêu cầu và phạm vi

- Tải sản phẩm và danh mục; sửa tên/giá/danh mục/mô tả.
- Form gửi PUT đầy đủ bốn field; BE hiện có hỗ trợ cả PUT và PATCH.
- Không đổi ID/created_at, không thêm autosave hoặc kiểm soát phiên bản.

## 3. Dữ liệu / điều kiện

- Rule tên/giá/danh mục/mô tả bám UpdateProductRequest và giới hạn form tạo.
- PUT bắt buộc name/price/category_id; description phải có key nhưng cho phép null.
- PATCH chỉ kiểm tra field được gửi; field bỏ qua giữ nguyên; body {} hiện được chấp nhận.

## 4. Giả định và điểm cần review

- Đề xuất route edit, dùng PUT cho form và giữ form sau thành công; cần review.
- Chưa có cơ chế phát hiện hai người cùng sửa; không mô tả chống ghi đè.

## 5. Căn cứ

[BD](bd_update_product.md), [DD](dd_update_product.md), [testcase](testcase_update_product.md), [dữ liệu dùng chung](../../shared/data_model.md). Source: [Route API](../../../routes/api.php), [ProductController](../../../app/Http/Controllers/ProductController.php), [UpdateProductRequest](../../../app/Http/Requests/UpdateProductRequest.php), [ProductResource](../../../app/Http/Resources/ProductResource.php), [Handler](../../../bootstrap/app.php).
