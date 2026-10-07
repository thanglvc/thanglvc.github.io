# Raw spec — Danh sách sản phẩm

**Trạng thái:** AI soạn ngày 06/10/2026, bản nháp chờ review. Hợp đồng BE bám source hiện có; phần FE mới là thiết kế đề xuất, chưa được duyệt/triển khai bởi lượt này.

## 1. User Story

Là người dùng, tôi muốn xem danh sách sản phẩm theo từng trang, để tìm sản phẩm cần xem, sửa hoặc xóa.

## 2. Yêu cầu và phạm vi

- Xem sản phẩm, danh mục và giá; phân trang 10 sản phẩm/trang, ID giảm dần.
- Liên kết sang tạo, chi tiết, cập nhật; nút Xóa dùng chức năng xóa riêng.
- Không thêm tìm kiếm, lọc, tùy chọn số dòng hoặc CRUD danh mục.

## 3. Dữ liệu / điều kiện

- page mặc định 1; BE không có Form Request cho query, page không hợp lệ được paginator quy về 1.
- Danh sách có thể rỗng hoặc page vượt last_page; GET chỉ đọc DB.

## 4. Giả định và điểm cần review

- Đề xuất route /products và bảng danh sách; cần review trước code.
- Nút Trang trước/Trang sau; không nhận search/per_page làm tính năng.

## 5. Căn cứ

[BD](bd_list_products.md), [DD](dd_list_products.md), [testcase](testcase_list_products.md), [dữ liệu dùng chung](../../shared/data_model.md). Source: [Route API](../../../routes/api.php), [ProductController](../../../app/Http/Controllers/ProductController.php), [ProductResource](../../../app/Http/Resources/ProductResource.php), [Handler](../../../bootstrap/app.php).
