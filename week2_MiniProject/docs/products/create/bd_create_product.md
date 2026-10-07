# BD — Tạo sản phẩm mới

**Trạng thái:** AI soạn ngày 06/10/2026; người học đã review đồng ý và cho phép chuyển sang [DD](dd_create_product.md). Theo [raw spec](raw_spec_create_product.md); đã chốt VNĐ và đường dẫn `/products/create`, các giới hạn nhập bám code hiện có.

## 1. Mục đích

Giúp người dùng thêm một sản phẩm bằng cách nhập thông tin, chọn danh mục và bấm **Tạo sản phẩm**.

## 2. Giao diện và các trường

Màn hình tại `/products/create` có tiêu đề **Tạo sản phẩm mới**. Các trường xếp theo thứ tự dưới đây; nhãn nằm trên ô nhập. Danh mục là danh sách chọn, Mô tả là ô nhập nhiều dòng. Ban đầu các ô trống và danh mục hiển thị **Chọn danh mục**.

| Tên trường | Dùng để làm gì | Bắt buộc | Quy tắc nhập chính |
| --- | --- | --- | --- |
| Tên sản phẩm | Tên hiển thị của sản phẩm | Có | Không để trống; tối đa 255 ký tự. Có thể trùng tên sản phẩm khác. |
| Giá (VNĐ) | Giá của sản phẩm bằng VNĐ | Có | Số từ `0` đến `99999999.99`; tối đa 2 chữ số sau dấu thập phân. |
| Danh mục | Nhóm sản phẩm thuộc về | Có | Chọn một danh mục có sẵn. |
| Mô tả | Thông tin bổ sung về sản phẩm | Không | Có thể để trống; tối đa 2000 ký tự. |

**Nút và thông báo:** nút **Tạo sản phẩm** nằm dưới form. Lỗi nhập liệu hiển thị dưới trường tương ứng; thông báo kết quả nằm gần nút.

## 3. Cách sử dụng

1. Mở màn hình và chờ danh sách danh mục tải xong.
2. Nhập tên, giá, chọn danh mục và nhập mô tả nếu cần.
3. Bấm **Tạo sản phẩm**. Hệ thống kiểm tra thông tin; nếu có lỗi, người dùng sửa trường được báo rồi bấm lại.
4. Khi thông tin hợp lệ, hệ thống gửi yêu cầu tạo. Nút đổi thành **Đang tạo…** và tạm khóa để người dùng không bấm gửi liên tiếp.
5. Đọc thông báo kết quả trên cùng màn hình.

## 4. Kết quả và thông báo

- **Thành công:** hiện **Tạo sản phẩm thành công**. Xóa các giá trị đã nhập, đưa danh mục về **Chọn danh mục**, giữ danh sách danh mục để tạo tiếp; không chuyển trang.
- **Thiếu/sai dữ liệu:** báo rõ lỗi, ví dụ **Vui lòng nhập tên sản phẩm** hoặc **Giá phải từ 0 đến 99999999.99 và có tối đa 2 chữ số thập phân**. Giữ thông tin đã nhập để sửa, cho phép bấm tạo lại sau khi yêu cầu trước đã kết thúc. Nếu danh mục đã chọn không còn tồn tại, yêu cầu chọn lại danh mục hợp lệ.
- **Danh mục đang tải, rỗng hoặc tải lỗi:** lần lượt hiện **Đang tải danh mục…**, **Chưa có danh mục để chọn** hoặc **Không tải được danh mục**. Chưa cho phép tạo khi chưa có danh mục hợp lệ để chọn.
- **Lỗi kết nối/hệ thống:** hiện **Chưa xác nhận được kết quả tạo sản phẩm**; giữ thông tin đã nhập và không tự gửi lại yêu cầu.

**Đã chốt:** dùng VNĐ và mở màn hình tại `/products/create`.
