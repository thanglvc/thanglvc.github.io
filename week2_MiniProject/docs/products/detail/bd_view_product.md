# BD — Chi tiết sản phẩm

**Trạng thái:** bản nháp để người dùng review; chi tiết kỹ thuật ở [DD](dd_view_product.md).

## 1. Mục đích

Là người dùng, tôi muốn xem đầy đủ thông tin một sản phẩm, để hiểu sản phẩm và chọn sửa hoặc xóa khi cần.

## 2. Giao diện và các trường

Tiêu đề Chi tiết sản phẩm; khối thông tin đọc, mô tả nhiều dòng; link về danh sách và thao tác sửa/xóa.

| Tên trường / thành phần | Dùng để làm gì | Quy tắc hiển thị / nhập |
| --- | --- | --- |
| Tên / Giá / Danh mục | Xem thông tin chính | Giá kèm VNĐ; text dài xuống dòng. |
| Mô tả | Đọc thông tin bổ sung | Giữ xuống dòng; trống hiện Chưa có mô tả. |
| Thời gian | Xem thời điểm tạo/cập nhật | Hiển thị giờ quốc tế (UTC) có nhãn; chưa có thời gian thì hiện —. |

## 3. Cách sử dụng

1. Mở sản phẩm từ danh sách hoặc URL.
2. Đợi tải thông tin; đọc các trường.
3. Chọn Sửa, Xóa hoặc quay về danh sách.

## 4. Kết quả và thông báo

- Tải thành công: hiện thông tin đúng sản phẩm.
- Không tìm thấy: hiện Sản phẩm không tồn tại; không hiện dữ liệu sản phẩm khác.
- Tải lỗi: hiện Không tải được thông tin sản phẩm; vẫn có link về danh sách.

**Cần review:** Đề xuất màn chi tiết và cách hiện trường chưa có dữ liệu; cần review trước triển khai.
