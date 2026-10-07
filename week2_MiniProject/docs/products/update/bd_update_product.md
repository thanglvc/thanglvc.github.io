# BD — Cập nhật sản phẩm

**Trạng thái:** bản nháp để người dùng review; chi tiết kỹ thuật ở [DD](dd_update_product.md).

## 1. Mục đích

Là người dùng, tôi muốn sửa thông tin một sản phẩm đã có, để thông tin sản phẩm được cập nhật đúng.

## 2. Giao diện và các trường

Form một cột cùng field của form tạo, điền dữ liệu sản phẩm; nút Lưu thay đổi và link về danh sách.

| Tên trường / thành phần | Dùng để làm gì | Quy tắc hiển thị / nhập |
| --- | --- | --- |
| Tên / Giá (VNĐ) / Danh mục | Sửa dữ liệu chính | Không trống; giới hạn giống form tạo; giá không làm tròn dữ liệu sai. |
| Mô tả | Sửa hoặc bỏ mô tả | Có thể trống; tối đa 2000 ký tự. |
| Lưu thay đổi | Ghi dữ liệu đã nhập | Đang gửi khóa form, tránh bấm lặp. |

## 3. Cách sử dụng

1. Mở Sửa từ sản phẩm; đợi tải sản phẩm/danh mục.
2. Sửa các trường cần thay đổi.
3. Bấm Lưu thay đổi; sửa dữ liệu được báo lỗi nếu có.
4. Đọc kết quả trên form; thành công giữ dữ liệu đã cập nhật.

## 4. Kết quả và thông báo

- Thành công: Cập nhật sản phẩm thành công; giữ form/danh mục đã chọn.
- Dữ liệu sai: báo dưới field, giữ dữ liệu để sửa.
- Sản phẩm không còn: Sản phẩm không tồn tại; khóa lưu.
- Lỗi hệ thống/kết nối: Chưa xác nhận được kết quả cập nhật sản phẩm; giữ dữ liệu và không tự gửi lại.

**Cần review:** Đề xuất màn sửa; sau thành công giữ thông tin đã cập nhật trên form để người dùng kiểm tra.
