# Prompt — Triển khai giao diện module sản phẩm

Copy phần bên dưới cho model triển khai. Prompt giao bước code theo tài liệu hiện tại; không thay metadata duyệt hoặc kết quả test khi chưa có bằng chứng.

```text
Hãy triển khai giao diện và tích hợp API cho module sản phẩm trong project:
/home/thanglvc/Documents/Training_Fresher/Training_DU2/thanglvc.github.io/week2_MiniProject

Đọc AGENTS.md của workspace/project, AI_CONTEXT.md và docs/README.md trước. Đọc đủ raw spec, BD, DD, API specs, workflow và testcase trong docs/products/{list,create,detail,update,delete}/ cùng docs/categories/list/; đọc docs/shared/ cho schema và hợp đồng chung. Tên file có chức năng, ví dụ dd_create_product.md và testcase_update_product.md.

Yêu cầu này giao thực hiện code theo các thiết kế hiện tại, kể cả các bộ mới đang mang nhãn bản nháp. Tiến hành trong phạm vi dưới đây; không tự ghi tài liệu đã được review/duyệt. Đọc code đang có rồi triển khai, test và sửa lỗi đến khi hoàn thành; không dừng ở kế hoạch.

Phạm vi:
- /products: danh sách, giá VNĐ, danh mục, phân trang và action theo DD.
- /products/create: đã có source; tái sử dụng, kiểm tra và sửa nếu khác DD/testcase, giữ hành vi thành công của form tạo.
- /products/{id}: chi tiết đúng sản phẩm và trạng thái loading/not-found/error.
- /products/{id}/edit: tải sản phẩm/danh mục, sửa bốn field, lưu bằng PUT đầy đủ; thành công giữ form theo DD.
- Xóa từ danh sách/chi tiết: xác nhận, DELETE đúng ID, xử lý 204/404/lỗi và tải lại/điều hướng theo DD.
- GET /api/categories dùng chung cho form tạo/sửa; không thêm màn CRUD danh mục.

Giữ Laravel/Blade, CSS, JavaScript và Vite hiện có. Tái sử dụng layout/style/hàm chung; BEM, hook js-, semantic HTML, comment, responsive và accessibility theo AGENTS. Bổ sung web route/asset entry cần thiết; route /products/create phải không bị route động bắt nhầm. API BE đã có: tích hợp theo hợp đồng, giữ controller/Form Request/Resource/migration và dependency trong phạm vi hiện tại. Không thêm auth, ảnh, tìm kiếm/lọc hoặc tính năng ngoài DD.

Thực hiện đủ loading/empty/error/submitting/success, lỗi field/focus, chuẩn hóa Unicode, chặn submit lặp và nhánh exception. Không cắt/làm tròn input sai để né testcase. Mọi nhánh kết thúc khôi phục trạng thái control; không tự retry thao tác ghi khi chưa xác nhận kết quả. Không parse JSON cho DELETE 204. Dữ liệu API render như text an toàn. Không tạo lại mock_create_product_requests.js đã bị yêu cầu bỏ.

Kiểm thử và bàn giao:
1. Chạy npm run build; chạy Pint cho đúng các file PHP đã sửa và tự review toàn bộ phần thay đổi.
2. Viết Feature Test Pest cho các case BE mới của GET danh sách/chi tiết, PUT/PATCH và DELETE, tái sử dụng convention/fixture hiện có. Test qua HTTP thật trong Laravel, giữ handler và kiểm tra response cùng DB trước/sau lỗi. Dùng environment testing và SQLite :memory:, không chạy trên DB bài học. Chạy test mới cùng hai file ProductControllerTest.php/CategoryControllerTest.php hiện có, dùng cấu hình PHP_INI_SCAN_DIR trong testcase; ghi lệnh và report thực tế.
3. Test FE bằng công cụ browser sẵn có theo testcase từng chức năng: happy, validate, biên, exception, bấm lặp, keyboard/focus, 390/768/1440px và zoom 200%. Dùng DB kiểm thử riêng cho thao tác CRUD; không migrate:fresh hoặc xóa dữ liệu bài học. Kiểm tra console/network/asset. Mock UI không chứng minh DB; case chưa thiết lập/chạy được ghi rõ lý do, không ghi Pass.
4. Cập nhật từng testcase_<feature>.md: code/lệnh automated, actual, ngày/môi trường, bằng chứng FE và BE riêng; report ở results/ đúng scope. Giữ kết quả cũ khi chưa chạy lại, không ghi Pass hàng loạt từ việc build hoặc API Pass. Cập nhật AI_CONTEXT.md theo việc thực sự hoàn thành.
5. Bàn giao bằng tiếng Việt: file đã đổi, cách chạy/mở từng trang, kết quả build/Pest/browser, sai khác và case còn chờ. Không commit, push hoặc deploy nếu chưa có yêu cầu riêng.
```
