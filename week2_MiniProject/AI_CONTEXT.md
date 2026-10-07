**AI_CONTEXT — Mini Project Laravel, tham chiếu tuần 3**

Cập nhật: 06/10/2026, múi giờ Asia/Ho_Chi_Minh. File này lưu quyết định, bằng chứng và điểm tiếp tục của buổi học. Đọc lại khi bắt đầu chat mới hoặc sau khi context bị rút gọn.

**Trọng tâm hiện tại: tuần 3 — bộ tài liệu cho toàn bộ chức năng mini project**

Theo xác nhận mới nhất ngày 05/10/2026, hôm nay là **ngày học 1**; [roadmap tuần 3](../../Week3/WEEK_3_ROADMAP.md) đã tách đủ 5 ngày theo PDF. Ngày 1 học khái niệm/vai trò DD, phân biệt với High Level Design và cấu trúc 4 phần. Ngày 2 viết DD/workflow; ngày 3 testcase; ngày 4 thiết kế AI workflow; ngày 5 tạo command/chạy pipeline. Phần DD, testcase và Pest bên dưới là đầu ra AI đã chuẩn bị trước, chưa phải xác nhận người học hoàn thành ngày 2–3.

**Quyết định thiết kế ngày 06/10/2026:** DD tạo sản phẩm chia theo màn hình/use case nhưng bao quát cả giao diện, workflow FE/BE, validation/nghiệp vụ, DB, response và trạng thái UI. Khi viết/hoàn thiện DD, skill vẽ hoặc cập nhật sơ đồ workflow đi kèm, đặt tại `docs/<module>/<feature>/diagrams/` theo cấu trúc mới và liên kết từ DD; hình phải khớp luồng thành công, validate, exception và kết quả UI/DB. API specs chứa hợp đồng và chi tiết endpoint được DD tham chiếu. Thiết kế FE là phần mới; BE giữ hành vi hiện có. Auth/password/phân quyền nằm ngoài phạm vi bài này.

**Pipeline theo yêu cầu mới nhất:** `raw spec → BD → DD → testcase → code`. API specs nằm trong bước DD; một tài liệu testcase bao gồm case FE/BE, dữ liệu/môi trường, cách test manual hoặc automated, coverage và kết quả. Mã test có thể viết/chạy trong bước kiểm thử theo yêu cầu; code ứng dụng cần yêu cầu triển khai riêng.

**Vị trí User Story đã chốt:** nằm ở đầu [raw spec](docs/products/create/raw_spec_create_product.md), trước yêu cầu/phạm vi; không tách file hoặc thêm bước pipeline. AI đã bổ sung mục 1 và cập nhật skill. Người học đã review đồng ý raw spec/BD ngày 06/10/2026 và yêu cầu chuyển sang DD.

**Yêu cầu mới nhất ngày 06/10/2026:** đổi tên tài liệu theo loại + chức năng, cập nhật skill giữ cấu trúc module/chức năng cho các lần gen sau, và chuẩn bị [prompt_implement_products.md](docs/products/prompt_implement_products.md) để model khác triển khai UI. Rename/link và cập nhật skill đã hoàn thành; không viết code ứng dụng ở lượt này. Pipeline vẫn là raw spec → BD → DD → testcase → code.

**Vị trí tài liệu chức năng:** `docs/products/{list,create,detail,update,delete}/` và `docs/categories/list/`. Mỗi scope có raw_spec/BD/DD/testcase/API/workflow; `docs/shared/` có schema và hợp đồng chung. Report cũ tại `docs/products/create/results/`; code test ở tests. 115 testcase mới chưa chạy. CAT01–CAT03 được tham chiếu lại, không tính là case mới. Week3 giữ nguyên nguồn học/mẫu.

**Lịch sử Pest ngày 05/10/2026:** người học duyệt [bộ 44 testcase API](../../Week3/TESTCASE_CREATE_PRODUCT.md) và yêu cầu AI viết/chạy Pest cho POST products. Các dòng kết quả 44/46 test bên dưới thuộc lịch sử này. Yêu cầu testcase ngày 06/10/2026 mở rộng kiểm thử chức năng sang GET categories và các điểm DD FE/BE; kết quả mới 59 test ở đầu file được ưu tiên cho phạm vi hiện tại.

- Đã cài `pestphp/pest` 4.7.8 và `pestphp/pest-plugin-laravel` 4.1.0; cập nhật composer.json/composer.lock. PHPUnit được Composer điều chỉnh từ 12.5.37 về 12.5.33 theo ràng buộc Pest; Laravel vẫn 13.34.0.
- File mới: [tests/Pest.php](tests/Pest.php), [ProductControllerTest.php](tests/Feature/Http/Controllers/ProductControllerTest.php), [CategoryFactory.php](database/factories/CategoryFactory.php), [ProductFactory.php](database/factories/ProductFactory.php). Factory được gọi trực tiếp, không cần sửa model để thêm HasFactory.
- `tests/Pest.php` áp dụng TestCase/LazilyRefreshDatabase trong `Feature/Http/Controllers`; test giữ mã TC01–TC44 để đối chiếu tài liệu. Guard yêu cầu environment `testing`, driver SQLite và DB `:memory:` trước khi tạo dữ liệu.
- TC42–TC44 ném `PDOException` trước truy vấn DB ở đúng bước, giữ handler thật và kiểm tra dữ liệu sau lỗi. TC44 trả 500 nhưng sản phẩm vẫn tồn tại vì code store chưa có transaction.
- Kết quả AI chạy ngày 05/10/2026: **44/44 testcase Pass, 990 assertions; toàn bộ suite 46/46 Pass, 992 assertions**. Hai test PHPUnit cũ vẫn chạy. Kết quả được ghi nhận từ output Pest trước đó; file JUnit `Week3/pest_results.xml` hiện không có trong workspace, có thể xuất lại bằng lệnh ở mục 8 tài liệu testcase. Pint đạt cho bốn file mới; source app, migration, route và test cũ được kiểm tra hash không đổi.
- Người học gửi ảnh tám cảnh báo Intelephense P1006 tại `createProductFixture($this)`: editor suy luận `$this` thành Pest `TestCall`, khác kiểu `Tests\TestCase` của helper. Đã bỏ tham số helper và dùng `Pest\Laravel\travelTo()`; không tắt diagnostic hoặc sửa vendor. Chạy lại test chức năng: **44/44 Pass, 990 assertions**; Pint đạt. Chưa đọc lại danh sách Problems trực tiếp từ editor sau sửa.
- Người học gửi tiếp P1009/P1013 ở Unit/ExampleTest.php và cảnh báo import stdClass từ PHP Namespace Resolver. Đã xác nhận autoload tìm được `PHPUnit\Framework\TestCase` và `assertTrue()`. Detector của Namespace Resolver 2.1.3 yêu cầu tên class bắt đầu chữ hoa nên bỏ sót stdClass; đổi helper trả về `object` và dùng `toBeObject()` để kiểm tra errors là JSON object, vẫn giữ assertion nội dung rỗng. Pint đã định dạng file test; chạy lại toàn bộ suite **46/46 Pass, 992 assertions**, cập nhật JUnit. Hai lỗi PHPUnit trong editor cần `Intelephense: Index workspace`; chưa xác nhận kết quả Problems sau khi người học chạy lệnh này.
- Test dùng PHP 8.3.6 và SQLite trong bộ nhớ theo phpunit.xml, không ghi vào MySQL bài học. Chưa chạy bộ 44 case trên MySQL; chưa xác nhận người học đã tự viết hoặc hiểu code test.

Chạy trong thư mục project:

~~~bash
PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d vendor/bin/pest tests/Feature/Http/Controllers/ProductControllerTest.php --compact
~~~

**Kiểm tra lượt DD ngày 06/10/2026:** đã đối chiếu source và quan hệ field/UI/API; kiểm tra 7 mục DD, 4 mục mỗi API, 36 link/hình cục bộ, 7 JSON ví dụ, bảng Markdown và XML SVG. Hai hình đã render bằng librsvg và được xem để kiểm tra chữ/đường nối. Toàn bộ 39 file source/test được đối chiếu hash không đổi. Không chạy lại Pest/build/browser UI vì không sửa code; các kết quả Pest cũ không phải bằng chứng kiểm thử FE mới.

**Báo cáo của lượt triển khai trước (chưa xác minh lại ở lượt tài liệu):** đã hoàn thành triển khai giao diện tạo sản phẩm mới (`GET /products/create`) theo DD/API/BD và kiểm thử toàn diện FE01–FE44 trên trình duyệt (Chrome DevTools MCP / Chrome headless). Toàn bộ 44 testcase FE đạt (Pass) với bằng chứng kiểm thử thực tế và Lighthouse Audit đạt 100/100 Accessibility, 100/100 Best Practices, 100/100 SEO. Suite Pest backend 59/59 Pass (1.321 assertions). Database MySQL giữ nguyên vẹn 2 danh mục và 12 sản phẩm mẫu. Bàn giao đầy đủ code, hướng dẫn mở trang và giải thích cấu trúc cho người mới.

**1. Mục tiêu và cách hướng dẫn đã được người học yêu cầu**

Ở buổi học tuần 2 đến ngày 02/10/2026, người học tự thực hành mini project ngày 5. AI hướng dẫn bằng tiếng Việt, câu ngắn, giải thích dễ hiểu; cung cấp lệnh terminal và code để người học tự chạy, tự copy/gõ vào đúng file. Yêu cầu trực tiếp viết/chạy Pest tuần 3 ở đầu file được ưu tiên cho công việc hiện tại.

- Hướng dẫn từng phần theo [BAI_5_THUC_HANH.md](BAI_5_THUC_HANH.md).
- Mỗi lượt chỉ hướng dẫn phần đang làm hoặc giải quyết lỗi của phần đó.
- Khi người học gửi output, kiểm tra và giải thích kết quả; chuyển sang phần tiếp theo khi phần hiện tại đã hoàn thành.
- Không tự làm thay toàn bộ project khi người học đang yêu cầu hướng dẫn thực hành.
- Không đánh dấu người học đã hiểu chỉ vì code đã được AI cung cấp.
- Nếu người học hỏi chen ngang hoặc yêu cầu sửa tài liệu, xử lý yêu cầu đó rồi giữ lại điểm đang học.
- Người học muốn API dễ và quen thuộc; đã chọn API sản phẩm/danh mục.
- Người học chọn Postman vì JSON trong terminal khó đọc, sau đó yêu cầu dọn dữ liệu thử và bắt đầu lại toàn bộ phần 14. BAI_5_THUC_HANH.md đã chuyển phần 14 sang Postman; hướng dẫn từng bước bằng method/URL/Headers/Body/Send từ GET danh mục. Các kết quả curl trước đó chỉ được giữ làm bằng chứng lịch sử.

**2. Vị trí project và tài liệu**

Project chính:

~~~text
/home/thanglvc/Documents/Training_Fresher/Training_DU2/thanglvc.github.io/week2_MiniProject
~~~

Từ thư mục này:

| File | Vai trò |
| --- | --- |
| [AI_CONTEXT.md](AI_CONTEXT.md) | Tiến độ, quyết định và điểm tiếp tục |
| [BAI_5_THUC_HANH.md](BAI_5_THUC_HANH.md) | Hướng dẫn thực hành đầy đủ, 18 phần |
| [AGENTS.md](AGENTS.md) | Hướng dẫn dành cho agent trong project |
| [composer.json](composer.json) | Dependency và script thực tế |
| [Đề tuần 2](../../Week2/Tuan_2_PHP_OOP_Laravel.docx.pdf) | Yêu cầu gốc |
| [Roadmap tuần 2](../../Week2/WEEK_2_ROADMAP.md) | Lộ trình học và xác nhận phạm vi |

Quy tắc ở [AGENTS của repo](../AGENTS.MD) và [AGENTS của workspace](../../AGENTS.md) vẫn cần được đối chiếu khi áp dụng. [AI_CONTEXT gốc](../../AI_CONTEXT.md) lưu trọng tâm học hiện tại và bài DD tuần 3 dựa trên project này.

Người học đã yêu cầu project nằm tại week2_MiniProject và hướng dẫn nằm ngay trong project. File hướng dẫn đã được chuyển khỏi Week2/BAI_5_THUC_HANH.md; đường dẫn Week2/practice/week2-products trước đây không còn là vị trí làm bài.

**3. Phạm vi bài và các quyết định đã chốt**

PDF ngày 5 yêu cầu mini project API CRUD RESTful, tự review N+1, Form Request, Exception Handler và middleware, ghi phần AI hỗ trợ và giải thích logic với mentor. Roadmap ghi mentor đã xác nhận tuần này không cần frontend.

PDF không chọn nghiệp vụ cụ thể. Ví dụ thực hành đã chọn:

- Product: name, price, description, category_id.
- Category: name.
- Category hasMany Product; Product belongsTo Category.
- CRUD sản phẩm; danh mục được seed và có endpoint xem danh sách.
- Người học thấy v1 rườm rà: đã bỏ tiền tố v1 trong toàn bộ hướng dẫn. Dùng /api/products và /api/categories.
- Không tự mở rộng bài sang đăng nhập, giỏ hàng, thanh toán, frontend hoặc CRUD danh mục.

Endpoint đã được đăng ký; trạng thái kiểm tra HTTP được ghi ở mục 5:

| Method | URL | Thành công |
| --- | --- | --- |
| GET | /api/categories | 200 |
| GET | /api/products | 200, phân trang 10 sản phẩm |
| POST | /api/products | 201 |
| GET | /api/products/{product} | 200 |
| PUT | /api/products/{product} | 200, gửi đủ bốn trường |
| PATCH | /api/products/{product} | 200, gửi trường muốn sửa |
| DELETE | /api/products/{product} | 204, không có body |

Thiết kế trong hướng dẫn:

- Form Request riêng cho store/update; dùng validated() và fillable.
- price dùng decimal(10, 2), cast decimal:2; JSON biểu diễn giá thành chuỗi hai chữ số thập phân.
- Eager loading category trong controller; Resource dùng whenLoaded.
- Middleware RequireJson kiểm tra Content-Type của POST/PUT/PATCH.
- JsonRequiredException trả status 415 khi Content-Type không phù hợp.
- Đăng ký API route, alias middleware và JSON error rendering trong bootstrap/app.php của Laravel 13.
- Lỗi có message/errors; validation 422, không tìm thấy 404, sai Content-Type 415, lỗi không dự kiến 500 với thông báo chung.
- Seed hai danh mục và 12 sản phẩm; AppServiceProvider hỗ trợ phát hiện lazy loading ở local.
- Gọi API bằng Postman từ bước PUT phần 14; các bước GET/POST ban đầu đã dùng curl. Khởi động server bằng php artisan serve; Artisan/Tinker vẫn chạy trong terminal.

**4. Môi trường đã xác nhận**

Các giá trị sau có bằng chứng từ output người học trong chat:

| Thành phần | Giá trị |
| --- | --- |
| PHP CLI | 8.3.6 |
| Composer | 2.7.1 |
| Docker | 29.1.3 |
| PHP PDO drivers | mysql, sqlite |
| Laravel Framework | 13.34.0 |
| MySQL của bài | 8.4.11 |

composer.lock cũng xác nhận laravel/framework v13.34.0 khi tạo file context này.

PHP database extensions được cài trong tài khoản người dùng. Nếu một terminal mới không thấy driver mysql, dùng:

~~~bash
source ~/.bashrc
php -r 'echo implode(", ", PDO::getAvailableDrivers()), PHP_EOL;'
~~~

Hoặc nạp đường dẫn cấu hình cho terminal hiện tại:

~~~bash
export PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d
~~~

Docker/MySQL của bài:

| Thuộc tính | Giá trị |
| --- | --- |
| Container | week2-products-db |
| Image đã dùng | mysql:8.4 |
| Host | 127.0.0.1 |
| Cổng host | 3307 |
| Cổng container | 3306 |
| Database | week2_products |
| User mẫu | week2_user |
| Volume | week2-products-db-data |

Container mysql-training của bài khác đang dùng cổng 3306 ở thời điểm khảo sát. Không dùng nhầm cổng đó cho project này.

Mật khẩu ví dụ được ghi ở phần 2 của hướng dẫn; cấu hình thực tế nằm trong .env. Không chép APP_KEY hoặc mật khẩu thực tế vào file context hay output chat. Chỉ kiểm tra các giá trị cần thiết.

Nếu container của bài đã dừng, dùng:

~~~bash
docker start week2-products-db
~~~

Project và container đã được tạo: tiếp tục từ trạng thái hiện có, tránh chạy lại create-project/docker run.

**5. Tiến độ có bằng chứng**

| Phần trong hướng dẫn | Trạng thái | Bằng chứng / phần còn lại |
| --- | --- | --- |
| 1 — Kiểm tra môi trường | Đạt | Người học gửi output PHP, Composer, Docker và PDO mysql/sqlite |
| 2 — Tạo MySQL | Đạt tại thời điểm thực hành | Người học đăng nhập container và SELECT DATABASE() trả week2_products |
| 3 — Tạo project, cấu hình .env | Đạt | Người học gửi config:clear thành công và migrate:status báo Migration table not found; Laravel kết nối được database nhưng chưa có bảng theo dõi migration |
| 4 — Tạo file bằng Artisan | Đạt | Người học gửi output thành công của cả 9 lệnh ngày 02/10/2026; hai migration categories/products có thứ tự đúng |
| 5 — Viết migration | Đạt về nội dung file; chưa chạy migration | Người học báo đã lưu; AI đọc lại hai file ngày 02/10/2026 thấy đủ cột, khóa ngoại và down() đúng; đã nhắc chuẩn hóa foreignID thành foreignId và spacing decimal |
| 6 — Model và relationship | Đạt về nội dung file; chưa kiểm tra runtime | Người học báo đã lưu; AI đọc lại Category/Product thấy fillable, cast price decimal:2 và HasMany/BelongsTo đúng theo tài liệu |
| 7 — Form Request | Đạt về nội dung file; chưa kiểm tra runtime | Người học báo đã lưu; AI đọc lại hai Request thấy authorize true, rule store đầy đủ và phân biệt PUT/PATCH đúng |
| 8 — ProductResource | Đạt về nội dung file; chưa kiểm tra runtime | Người học báo đã lưu; AI đọc lại thấy đủ trường, whenLoaded category với callback và định dạng thời gian đúng |
| 9 — Controller | Đạt về nội dung file; chưa kiểm tra runtime | Người học đã sửa import; AI đọc lại CategoryController xác nhận use Illuminate\Http\JsonResponse đúng; ProductController đã review CRUD đúng |
| 10 — Custom exception và middleware | Đạt về nội dung file; chưa kiểm tra runtime | Người học báo đã lưu; AI đọc lại exception extends HttpException/status 415 và middleware kiểm tra POST/PUT/PATCH/isJson đúng |
| 11 — Route/middleware alias/Exception Handler | Đạt về nội dung file và đăng ký route; chưa kiểm tra HTTP errors | Người học gửi route:list đủ 6 route; AI đọc routes/api.php/bootstrap/app.php thấy API routing, alias và render callback đúng |
| 12 — Seeder và lazy loading | Đạt về migration/dữ liệu mẫu/cấu hình; chưa kiểm tra phát hiện lazy loading runtime | Người học gửi cả 5 migration DONE và db:show --counts xác nhận database week2_products tại 127.0.0.1:3307, categories 2/products 12/migrations 5; provider/seeder đã review đúng |
| 13 — Chạy API | Đạt tại thời điểm thực hành | Người học gửi INFO Server running on http://127.0.0.1:8000; cần giữ terminal server chạy |
| 14 — Postman CRUD và lỗi API | Người học xác nhận đã hoàn tất | Người học nói đã done phần 14 và yêu cầu tiếp tục; lượt Postman mới chỉ có body GET categories gửi trong chat, chưa có toàn bộ status/body CRUD và các ca lỗi để AI đối chiếu độc lập |
| 15 — Kiểm tra N+1 | Đã hướng dẫn, chờ output đếm query | Đã cung cấp hai ví dụ Tinker lazy loading/eager loading, kỳ vọng N+1 so với 2 query; chờ số sản phẩm và số query thực tế |
| 16–18 — Giải thích logic, debug và ghi bằng chứng | Chưa hoàn thành | Làm sau các bước thực hành |

Output MySQL mà người học đã gửi:

~~~text
SELECT DATABASE();
week2_products
~~~

Output kiểm tra cuối phần 3 người học đã gửi ngày 01/10/2026:

~~~text
php artisan config:clear
INFO Configuration cache cleared successfully.

php artisan migrate:status
ERROR Migration table not found.
~~~

Kết quả phù hợp với database mới chưa có bảng theo dõi migration; phần 3 đã hoàn thành. Chưa chạy migrate để tạo bảng.

Ngày 02/10/2026, người học gửi output tạo thành công Category, Product, StoreProductRequest, UpdateProductRequest, ProductResource, ProductController, CategoryController, RequireJson và JsonRequiredException. Hai migration được tạo là 2026_10_02_012235_create_categories_table.php và 2026_10_02_012242_create_products_table.php. AI đọc lại cả hai file và thấy mới có id/timestamps, chưa có cột nghiệp vụ.

Cùng ngày, người học báo đã lưu phần 5. AI đọc lại hai migration thấy đủ cột nghiệp vụ, khóa ngoại constrained/restrictOnDelete và down() tương ứng. Product migration dùng foreignID (PHP gọi method không phân biệt hoa/thường, nên đây không phải lỗi thực thi) và decimal('price',10,2); đã hướng dẫn chuẩn hóa thành foreignId và decimal('price', 10, 2). Đây là review nội dung file, chưa có bằng chứng migration chạy thành công và chưa xác nhận người học hiểu logic.

Người học từng chạy cd week2_MiniProject khi đã ở trong thư mục đó, nên Bash báo không có thư mục con. Đây là lỗi đường dẫn của lệnh cd; project vẫn hoạt động và php artisan --version trả 13.34.0.

Ngày 02/10/2026, người học báo đã lưu phần 6. AI đọc lại Category.php và Product.php thấy fillable đúng, Category.products(): HasMany, Product.category(): BelongsTo và casts() price decimal:2. Đây là review nội dung file, chưa kiểm tra truy vấn relationship/JSON runtime và chưa xác nhận người học hiểu logic.

Ngày 02/10/2026, người học báo đã lưu phần 7. AI đọc lại StoreProductRequest và UpdateProductRequest thấy authorize true và đầy đủ rule theo tài liệu; PUT dùng required/present, PATCH dùng sometimes. Chưa kiểm tra HTTP validation runtime. ProductResource khi đọc vẫn là khung Artisan trả parent::toArray($request).

Ngày 02/10/2026, người học báo đã lưu phần 8. AI đọc lại ProductResource thấy đủ id/name/price/description, category dùng whenLoaded với callback và created_at/updated_at dùng ?->toISOString(). Nội dung đúng theo tài liệu; chưa kiểm tra HTTP JSON/N+1 runtime. ProductController và CategoryController khi đọc vẫn là khung Artisan.

Ngày 02/10/2026, người học báo đã lưu phần 9. AI đọc hai controller thấy ProductController có CRUD, validated(), eager loading và response đúng theo hướng dẫn. CategoryController có nội dung index đúng nhưng thiếu import Illuminate\Http\JsonResponse, đang import Illuminate\Http\Request không dùng. Đã hướng dẫn thay dòng import này và chờ người học lưu; chưa chuyển sang phần 10, chưa kiểm tra HTTP runtime. ProductController cũng có import Request không dùng, có thể bỏ để gọn.

Cùng ngày, người học báo đã sửa. AI đọc lại CategoryController thấy import Illuminate\Http\JsonResponse đã đúng; phần 9 đạt về nội dung file, chưa kiểm tra HTTP runtime. Điểm học chuyển sang phần 10.

Ngày 02/10/2026, người học báo đã lưu phần 10. AI đọc JsonRequiredException và RequireJson thấy nội dung đúng theo hướng dẫn. Chưa kiểm tra HTTP 415 runtime. Khi đọc bootstrap/app.php, file đã có shouldRenderJsonWhen cho api/* hoặc expectsJson, chưa có API routing và alias middleware; routes/api.php chưa tồn tại. Hướng dẫn phần 11 giữ cấu hình shouldRenderJsonWhen, bổ sung typed return bool và nhận diện cả api lẫn api/*.

Ngày 02/10/2026, người học gửi php artisan route:list --path=api --no-interaction hiển thị 6 route: GET|HEAD categories; GET|HEAD và POST products; GET|HEAD, PUT|PATCH và DELETE products/{product}. AI đọc routes/api.php/bootstrap/app.php thấy nhóm require.json, alias RequireJson, nạp API routes và custom render callback đúng. Đạt phần 11 về nội dung và đăng ký route; chưa kiểm tra HTTP CRUD/error runtime. Provider còn boot rỗng; seeder còn tạo Test User mặc định khi AI đọc trước phần 12.

AI đã kiểm tra cú pháp các đoạn PHP/Bash trong hướng dẫn, gồm đoạn route sau khi bỏ v1. Đây là kiểm tra ví dụ trong tài liệu, không phải bằng chứng API runtime đã chạy đúng.

**6. Trạng thái file thực tế lúc lưu context**

AI đã đọc các cấu hình sau từ .env vào ngày cập nhật; không in secret:

~~~dotenv
APP_ENV=local
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=week2_products
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
~~~

APP_KEY và DB_PASSWORD có giá trị. Người học đã xác nhận Laravel kết nối được MySQL bằng output migrate:status ở mục 5.

File hiện có sau phần 4 (02/10/2026):

- app/Models/User.php, Category.php và Product.php.
- app/Http/Controllers/Controller.php, ProductController.php và CategoryController.php.
- app/Http/Requests/StoreProductRequest.php và UpdateProductRequest.php.
- app/Http/Resources/ProductResource.php.
- app/Http/Middleware/RequireJson.php và app/Exceptions/JsonRequiredException.php.
- Ba migration mặc định: users, cache, jobs; hai migration mới: 2026_10_02_012235_create_categories_table.php và 2026_10_02_012242_create_products_table.php.
- routes/web.php, routes/console.php và routes/api.php.
- Hai migration, Category/Product, StoreProductRequest/UpdateProductRequest, ProductResource, hai controller, JsonRequiredException, RequireJson, routes/api.php và bootstrap/app.php đã được người học điền và AI review. API routing, alias và custom render callback đã có; route:list có đủ 6 route.
- AppServiceProvider đã bật preventLazyLoading local; DatabaseSeeder đã có hai danh mục và 12 sản phẩm mẫu dùng firstOrCreate; AI đọc và xác nhận đúng về nội dung file. Người học đã gửi cả 5 migration DONE và tạo migration table thành công. db:show --counts xác nhận categories 2, products 12, migrations 5 trong week2_products tại 127.0.0.1:3307 ngày 02/10/2026. Dữ liệu mẫu đã có; chưa kiểm tra HTTP/lazy loading runtime.

Không suy ra các bảng MySQL đã được migrate từ việc Composer tạo project. post-create-project có thể đã migrate SQLite mặc định trước khi người học đổi .env sang MySQL.

Theo hướng dẫn bootstrap AGENTS.md, AI đã cài laravel/boost v2.10.1 bằng Composer ngày 01/10/2026; composer.json và composer.lock được cập nhật. php artisan boost:install đã tạo hướng dẫn mới trong AGENTS.md và AI đã đọc lại. Installer cấu hình các agent Codex, Claude Code và Antigravity; phần skills/MCP của Codex và Antigravity thất bại do các thư mục .agents và .codex chỉ cho phép đọc trong sandbox. Chưa sửa code chức năng của ứng dụng. Không chạy lại bootstrap cài Boost khi tiếp tục chat.

**7. Điểm dừng lịch sử tuần 2 — chỉ tiếp tục khi người học yêu cầu quay lại**

Phần 12 hoàn thành: sau lỗi Connection refused, người học chạy lại migrate --seed thấy cả 5 migration DONE; db:show --counts xác nhận categories 2/products 12/migrations 5 trong database week2_products tại 127.0.0.1:3307. Không cần chạy lại seed hoặc migration để xác nhận.

Phần 13 hoàn thành: người học gửi INFO Server running on http://127.0.0.1:8000. Phần 14: GET /api/categories đã đạt với output HTTP/1.1 200 OK, Content-Type application/json và data gồm Điện tử id 1, Sách id 2. Các chuỗi Unicode escape trong JSON là biểu diễn hợp lệ của tiếng Việt.

Người học đã gửi GET /api/products?page=2: HTTP 200, data có 2 sản phẩm id 2 và 1, price "20000.00"/"10000.00", category Sách id 2/Điện tử id 1; meta current_page 2/from 11/to 12/per_page 10/last_page 2/total 12, links.next null. Sau đó gửi GET /api/products: HTTP 200, 10 sản phẩm id giảm dần 12..3, đầy đủ category/price đúng, meta current_page 1/from 1/to 10/per_page 10/last_page 2/total 12, links.prev null/next trang 2. Cả hai trang đạt.

POST /api/products của lượt trước đã đạt: người học gửi HTTP 201 Created và data id 13, name Bàn phím, price "199000.00", description Bàn phím dùng để thực hành, category Điện tử id 1, created_at/updated_at 2026-10-02T04:20:26.000000Z. Sản phẩm thử id 13 đã được xóa khi bắt đầu lại phần 14 theo yêu cầu mới.

GET /api/products/13 đã đạt: người học gửi HTTP 200 và data id 13, name Bàn phím, price "199000.00", description Bàn phím dùng để thực hành, category Điện tử id 1 và timestamps khớp POST. Đã xác nhận đọc lại sản phẩm vừa tạo.

Người học đã yêu cầu bắt đầu lại toàn bộ phần 14 bằng Postman ngày 02/10/2026, sau đó xác nhận đã hoàn tất phần 14 và yêu cầu chuyển tiếp. Các kết quả curl kể trên là bằng chứng lịch sử của lượt trước; không coi đó là output Postman của lượt mới.

Người học cho phép AI dọn dữ liệu thử để học lại. AI đọc database thấy hai danh mục Điện tử id 1/Sách id 2, 12 sản phẩm mẫu id 1..12 đúng seeder và một sản phẩm thử id 13, name Bàn phím mới, price 299000.00, description null, category_id 1. Đây là dữ liệu quan sát trong DB, chưa có response HTTP PUT từ người học.

AI đã xóa đúng sản phẩm thử id 13 có name Bàn phím mới trong transaction. Output xác nhận deleted_test_products 1, category_count 2, product_count 12 và product_ids 1..12. Không reset AUTO_INCREMENT; POST tiếp theo phải ghi nhận ID mới từ response, không mặc định 13. BAI_5_THUC_HANH.md đã cập nhật điểm bắt đầu lại ở 14.1/14.2 và URL chi tiết dùng <id> để thay bằng ID thật.

Lượt Postman mới: người học gửi body GET categories có Điện tử id 1/Sách id 2 đúng, chưa gửi status HTTP. Đã nhắc kiểm tra status 200 OK; không mặc định đã xác nhận 200 từ body.

Người học tự xác nhận đã hoàn tất phần 14. Tiếp tục theo xác nhận này; không yêu cầu chạy lại toàn bộ request. Chưa nhận đủ output Postman từng ca để AI kết luận kiểm tra độc lập hoặc xác nhận người học hiểu logic; có thể bổ sung bằng chứng khi ghi NOTES ở phần 18.

**Điểm tiếp tục: phần 15 — nhìn thấy N+1 bằng Tinker.** Đã hướng dẫn mở terminal khác tại project và php artisan tinker để nhập PHP từng dòng. Đây là phiên tương tác người học tự thao tác, không thêm --no-interaction vì PsySH dùng cờ đó để tắt chế độ tương tác. Tạm Model::preventLazyLoading(false) trong phiên này, bật/flush query log; lấy Product::query()->get(), truy cập category trên từng model rồi in số sản phẩm và count(DB::getQueryLog()). Sau đó flush log, lấy Product::query()->with('category')->get(), truy cập category tương tự rồi in số query. Kỳ vọng N+1 so với 2 khi có N sản phẩm và category hợp lệ; nếu còn 12 sản phẩm thì 13 so với 2. Kết thúc bằng Model::preventLazyLoading(true), DB::disableQueryLog() và exit. Không sửa cấu hình AppServiceProvider.

Chờ người học gửi số sản phẩm và hai số query hoặc lỗi Tinker. Khi có output, đối chiếu/giải thích kết quả, cập nhật tiến độ rồi sang phần 16 — tự giải thích logic. Không đánh dấu đã hiểu chỉ vì code được cung cấp hoặc lệnh đã chạy. Chưa có bằng chứng N+1 runtime.

**8. Cách duy trì context**

Sau mỗi phần có output xác nhận, cập nhật bảng tiến độ, bằng chứng và điểm tiếp tục trong file này. Khi thay đổi quyết định, sửa ghi chú cũ để tránh giữ hai hướng dẫn mâu thuẫn. Phân biệt việc đã hướng dẫn, việc đã thấy trong file, kết quả người học đã chạy và phần chưa kiểm tra.

Nếu người học cho phép AI trực tiếp sửa code, ghi rõ phạm vi được phép và các file đã thay đổi. Việc học vẫn cần người học tự giải thích logic trước khi đánh dấu đã hiểu.

Prompt gợi ý để tiếp tục ở chat mới:

~~~text
Đọc AI_CONTEXT.md của workspace và project week2_MiniProject. Tiếp tục tuần 3 ngày học 1 theo roadmap 5 ngày: giải thích DD, vai trò, điểm khác với High Level Design và 4 phần chính; dùng DD_CREATE_PRODUCT.md trong Week3 làm ví dụ.
~~~

**Điểm tiếp tục hiện tại:** đã hoàn thành triển khai toàn bộ giao diện và tích hợp API cho module sản phẩm theo `docs/products/prompt_implement_products.md`:
- **Web Routes:** `/products` (index), `/products/create` (create), `/products/{id}` (show - whereNumber), `/products/{id}/edit` (edit - whereNumber).
- **Giao diện & Assets:**
  - View danh sách: `resources/views/products/index.blade.php`, `resources/js/list_products.js` (bảng sản phẩm đẹp, giá VNĐ, badge danh mục, phân trang 10 sp/trang, delete confirm).
  - View chi tiết: `resources/views/products/show.blade.php`, `resources/js/view_product.js` (khối thông tin, pre-wrap mô tả, timestamps, link Sửa, nút Xóa).
  - View cập nhật: `resources/views/products/edit.blade.php`, `resources/js/update_product.js` (nạp dữ liệu cũ, danh mục, submit PUT đầy đủ, giữ form sau thành công).
  - View tạo mới: `resources/views/products/create.blade.php` (bổ sung header liên kết xem danh sách sản phẩm).
  - Stylesheet BEM chung: `resources/css/products.css`, `resources/css/create_product.css`.
- **Kiểm thử tự động Pest Backend:** 111/111 testcase Feature Test Pass (1.607 assertions) qua các file:
  - `ProductStoreTest.php` (CBE01–CBE60)
  - `ProductIndexTest.php` (LBE01–LBE11)
  - `ProductShowTest.php` (DBE01–DBE07)
  - `ProductUpdateTest.php` (UBE01–UBE26)
  - `ProductDestroyTest.php` (XBE01–XBE08)
- **Kiểm thử Browser (Chrome DevTools MCP):** Toàn bộ luồng CRUD, phân trang trang 1 ↔ trang 2, xem chi tiết, sửa sản phẩm, xóa sản phẩm (hộp thoại confirm Cancel và OK 204), responsive 390px, 768px, 1440px không tràn layout. Lighthouse Audit đạt 100/100 Accessibility, 100/100 Best Practices, 100/100 SEO.
- **Code Style & Build:** `npm run build` thành công; `./vendor/bin/pint` passed 100%. Dữ liệu mẫu ban đầu được bảo toàn.
