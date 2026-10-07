# BD — Tải danh mục dùng chung

**Trạng thái:** bản nháp để người dùng review; chi tiết kỹ thuật ở [DD](dd_load_categories.md).

## 1. Mục đích

Là người dùng, tôi muốn thấy danh mục có sẵn khi nhập sản phẩm, để chọn đúng nhóm sản phẩm.

## 2. Giao diện và các trường

Select Danh mục và vùng trạng thái bên dưới trên form sở hữu; không thêm page riêng.

| Tên trường / thành phần | Dùng để làm gì | Quy tắc hiển thị / nhập |
| --- | --- | --- |
| Danh mục | Chọn nhóm sản phẩm | Hiển thị tên danh mục; chỉ mở khi có dữ liệu. |
| Trạng thái | Biết danh mục đã tải hay chưa | Loading, danh sách rỗng và lỗi có thông báo khác nhau. |

## 3. Cách sử dụng

1. Mở form tạo/cập nhật; đợi danh mục tải.
2. Chọn danh mục có sẵn.
3. Nếu không có danh mục hoặc tải lỗi, đọc thông báo; chưa thể gửi sản phẩm.

## 4. Kết quả và thông báo

- Có danh mục: hiển thị lựa chọn đúng thứ tự; tạo không tự chọn phần tử đầu, form sửa giữ danh mục cũ nếu vẫn còn.
- Rỗng: Chưa có danh mục để chọn; khóa select/nút lưu/tạo.
- Lỗi: Không tải được danh mục; giữ thông tin đang nhập, khóa select/nút gửi.

**Cần review:** Danh mục dùng trong form tạo/sửa; phạm vi này chỉ chọn danh mục có sẵn.
