# Dữ liệu dùng chung

**Căn cứ:** migration/model của project ngày 06/10/2026; không đề xuất thay schema.

## Danh mục — categories

| Cột | Kiểu / ràng buộc | Sử dụng |
| --- | --- | --- |
| id | ID số nguyên do DB sinh | Giá trị lựa chọn danh mục. |
| name | string, unique | Nhãn danh mục; GET chỉ trả id/name. |
| created_at, updated_at | timestamps | Không trả trong GET danh mục. |

## Sản phẩm — products

| Cột | Kiểu / ràng buộc | Sử dụng |
| --- | --- | --- |
| id | ID số nguyên do DB sinh | Định danh xem/sửa/xóa. |
| category_id | FK tới categories.id, bắt buộc; restrict khi xóa danh mục đang được dùng | Tạo/cập nhật kiểm tra exists trước khi ghi. |
| name | string, tối đa 255; không unique | Cho phép sản phẩm trùng tên. |
| price | decimal(10,2) | Rule hiện có: 0–99999999.99; model cast decimal:2. |
| description | text, nullable | Form Request giới hạn 2000 ký tự. |
| created_at, updated_at | timestamps | Server quản lý; không lấy từ payload client. |

Tạo/cập nhật/xóa sản phẩm không ghi danh mục. Xóa sản phẩm là xóa bản ghi, model không có SoftDeletes. Chưa có transaction bao trùm ghi và tải quan hệ/dựng Resource, cơ chế phiên bản hay chống request trùng.

**Source:** [migration categories](../../database/migrations/2026_10_02_012235_create_categories_table.php), [migration products](../../database/migrations/2026_10_02_012242_create_products_table.php), [Product](../../app/Models/Product.php), [Category](../../app/Models/Category.php).
