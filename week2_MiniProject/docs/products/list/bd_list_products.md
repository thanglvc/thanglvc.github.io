# BD — Danh sách sản phẩm

**Trạng thái:** bản nháp để người dùng review; chi tiết kỹ thuật ở [DD](dd_list_products.md).

## 1. Mục đích

Là người dùng, tôi muốn xem danh sách sản phẩm theo từng trang, để tìm sản phẩm cần xem, sửa hoặc xóa.

## 2. Giao diện và các trường

Tiêu đề Danh sách sản phẩm, link Tạo sản phẩm, bảng Mã sản phẩm/Tên/Giá (VNĐ)/Danh mục/Thao tác và phân trang bên dưới.

| Tên trường / thành phần | Dùng để làm gì | Quy tắc hiển thị / nhập |
| --- | --- | --- |
| Tên | Nhận biết sản phẩm, mở chi tiết | Giữ tên đầy đủ; xuống dòng khi dài. |
| Giá / Danh mục | Xem giá và nhóm sản phẩm | Giá hai số lẻ kèm VNĐ; danh mục từ dữ liệu trả về. |
| Trang trước / Trang sau | Chuyển trang | Khóa khi đang tải hoặc không có trang tương ứng. |

## 3. Cách sử dụng

1. Mở danh sách và đợi dữ liệu tải.
2. Xem thông tin; chọn Trang sau/Trang trước nếu có.
3. Chọn tên để xem chi tiết, Sửa để cập nhật hoặc Xóa để xác nhận xóa.

## 4. Kết quả và thông báo

- Có dữ liệu: hiện tối đa 10 sản phẩm, sản phẩm mới trước.
- Không có sản phẩm: hiện Chưa có sản phẩm; vẫn có link Tạo sản phẩm.
- Tải lỗi: hiện Không tải được danh sách sản phẩm; dữ liệu cũ nếu có giữ lại và có nhãn lỗi, không coi là dữ liệu mới.

**Cần review:** Đề xuất màn danh sách và hai nút Trang trước/Trang sau; cần người dùng review bố cục và cách dùng.
