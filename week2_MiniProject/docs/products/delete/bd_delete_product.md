# BD — Xóa sản phẩm

**Trạng thái:** bản nháp để người dùng review; chi tiết kỹ thuật ở [DD](dd_delete_product.md).

## 1. Mục đích

Là người dùng, tôi muốn xóa một sản phẩm không còn cần dùng, để danh sách sản phẩm được cập nhật.

## 2. Giao diện và các trường

Nút Xóa cạnh sản phẩm; hộp thoại trình duyệt hỏi Xóa sản phẩm “tên sản phẩm”?; kết quả hiện trên màn sở hữu.

| Tên trường / thành phần | Dùng để làm gì | Quy tắc hiển thị / nhập |
| --- | --- | --- |
| Tên / Mã sản phẩm | Nhận biết sản phẩm sắp xóa | Hiện đúng sản phẩm người dùng vừa chọn xóa. |
| Xác nhận / Hủy | Quyết định xóa | Hủy không thay dữ liệu. |
| Kết quả | Biết thao tác hoàn tất hay chưa | Chỉ báo thành công khi hệ thống xác nhận đã xóa. |

## 3. Cách sử dụng

1. Bấm Xóa của sản phẩm.
2. Đọc tên trong xác nhận; chọn Hủy hoặc xác nhận.
3. Đợi kết quả; thành công thì danh sách được tải lại hoặc quay về danh sách từ chi tiết.

## 4. Kết quả và thông báo

- Xóa sản phẩm thành công; danh sách được tải lại và sản phẩm không còn hiển thị.
- 404: Sản phẩm không còn tồn tại; tải lại danh sách hoặc về danh sách.
- Lỗi hệ thống/kết nối: Chưa xác nhận được kết quả xóa sản phẩm; không tự xóa hàng trên UI hoặc tự gửi lại.

**Cần review:** Đề xuất hộp xác nhận của trình duyệt; sau xóa tải lại danh sách hoặc quay về danh sách nếu đang xem chi tiết.
