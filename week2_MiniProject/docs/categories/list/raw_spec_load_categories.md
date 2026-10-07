# Raw spec — Tải danh mục dùng chung

**Trạng thái:** AI soạn ngày 06/10/2026, bản nháp chờ review. Hợp đồng BE bám source hiện có; phần FE mới là thiết kế đề xuất, chưa được duyệt/triển khai bởi lượt này.

## 1. User Story

Là người dùng, tôi muốn thấy danh mục có sẵn khi nhập sản phẩm, để chọn đúng nhóm sản phẩm.

## 2. Yêu cầu và phạm vi

- Tải toàn bộ id/name của categories theo ID tăng dần để dựng select.
- Phục vụ form tạo và cập nhật; không thêm tạo/sửa/xóa danh mục.
- Màn sở hữu chịu trách nhiệm giữ giá trị form và chọn ID hiện tại.

## 3. Dữ liệu / điều kiện

- GET không có body/query dùng cho chức năng này; không phân trang.
- 200 với data [] hợp lệ; lỗi GET không được coi là danh sách rỗng.

## 4. Giả định và điểm cần review

- BE/API đã có; UI được dùng theo DD create đã duyệt và DD update nháp.
- Không tạo route /categories hoặc màn CRUD danh mục.

## 5. Căn cứ

[BD](bd_load_categories.md), [DD](dd_load_categories.md), [testcase](testcase_load_categories.md), [dữ liệu dùng chung](../../shared/data_model.md). Source: [Route API](../../../routes/api.php), [CategoryController](../../../app/Http/Controllers/CategoryController.php), [Handler](../../../bootstrap/app.php).
