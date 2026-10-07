**Bài 5 tuần 2: API quản lý sản phẩm và danh mục**

Bài này giúp bạn thực hành API CRUD RESTful theo [đề tuần 2](../../Week2/Tuan_2_PHP_OOP_Laravel.docx.pdf): Form Request, middleware, custom exception, JSON response và tránh N+1. Đề chưa quy định nghiệp vụ cụ thể; sản phẩm/danh mục là ví dụ thực hành được chọn cho hướng dẫn này. Phạm vi là CRUD sản phẩm và xem danh sách danh mục.

Hướng dẫn dành cho Laravel 13, PHP 8.3 và MySQL trên máy Ubuntu của bạn. Làm từ trên xuống. Khối **bash** chạy trong terminal; khối **php** dán vào file được ghi ngay phía trên. Các đoạn PHP dưới đây là **toàn bộ nội dung file**, bao gồm dòng mở PHP; thay nội dung file tương ứng rồi lưu.

Ở phần 14, các khối JSON dùng làm request body được dán vào Postman; những khối ghi là response chỉ dùng để đối chiếu kết quả.

Bạn sẽ có các endpoint:

| Method | URL | Công việc | Thành công |
| --- | --- | --- | --- |
| GET | /api/categories | Xem danh mục để lấy category_id | 200 |
| GET | /api/products | Xem sản phẩm, phân trang 10 item | 200 |
| POST | /api/products | Tạo sản phẩm | 201 |
| GET | /api/products/{product} | Xem một sản phẩm | 200 |
| PUT | /api/products/{product} | Thay thế các trường sản phẩm | 200 |
| PATCH | /api/products/{product} | Sửa các trường được gửi lên | 200 |
| DELETE | /api/products/{product} | Xóa sản phẩm | 204, không có body |

Quan hệ: một Category có nhiều Product; một Product thuộc một Category.

**1. Kiểm tra môi trường trong terminal**

```bash
source ~/.bashrc
php -v
composer --version
docker --version
php -r 'echo implode(", ", PDO::getAvailableDrivers()), PHP_EOL;'
```

Dòng cuối cần có mysql. Nếu chưa có, trong chính terminal đang làm bài chạy:

```bash
export PHP_INI_SCAN_DIR=:/home/thanglvc/.local/php/8.3/conf.d
php -r 'echo implode(", ", PDO::getAvailableDrivers()), PHP_EOL;'
```

Đây là đường dẫn cấu hình PHP driver đã cài trong tài khoản của bạn.

**2. Tạo MySQL cho bài thực hành**

Máy đang có MySQL khác ở cổng 3306. Ví dụ này dùng một container tên week2-products-db ở cổng 3307, với database và tài khoản được tạo sẵn. Thay `YOUR_MYSQL_ROOT_PASSWORD` và `YOUR_MYSQL_PASSWORD` bằng mật khẩu bạn chọn trước khi chạy; dùng cùng mật khẩu user khi cấu hình `DB_PASSWORD` trong `.env`.

Chạy một lần:

```bash
docker run -d \
  --name week2-products-db \
  -p 127.0.0.1:3307:3306 \
  -e MYSQL_ROOT_PASSWORD=YOUR_MYSQL_ROOT_PASSWORD \
  -e MYSQL_DATABASE=week2_products \
  -e MYSQL_USER=week2_user \
  -e MYSQL_PASSWORD=YOUR_MYSQL_PASSWORD \
  -v week2-products-db-data:/var/lib/mysql \
  mysql:8.4
```

Xem quá trình khởi tạo:

```bash
docker logs --tail 30 week2-products-db
```

Đợi MySQL khởi tạo xong rồi thử đăng nhập:

```bash
docker exec -it week2-products-db mysql -u week2_user -p week2_products
```

Khi hỏi password, nhập mật khẩu user bạn đã thay cho `YOUR_MYSQL_PASSWORD`. Trong cửa sổ MySQL chạy:

```sql
SELECT DATABASE();
exit;
```

Kết quả tên database phải là week2_products. Các lần học tiếp theo dùng:

```bash
docker start week2-products-db
```

Docker image tạo database/user từ các biến môi trường khi khởi tạo dữ liệu lần đầu. [MySQL Official Image](https://hub.docker.com/_/mysql)

**3. Tạo project và mở bằng VS Code**

```bash
cd /home/thanglvc/Documents/Training_Fresher/Training_DU2/thanglvc.github.io
composer create-project laravel/laravel week2_MiniProject "^13.0"
cd week2_MiniProject
php artisan --version
code .
```

Composer tải khung Laravel và dependency vào thư mục thanglvc.github.io/week2_MiniProject. Thư mục này có thể chưa tồn tại hoặc đang rỗng. Tham số cuối giới hạn phiên bản Laravel ở nhánh 13. [Composer create-project](https://getcomposer.org/doc/03-cli.md#create-project)

Từ đây, chạy các lệnh Artisan trong /home/thanglvc/Documents/Training_Fresher/Training_DU2/thanglvc.github.io/week2_MiniProject. Nếu thư mục đã chứa project Laravel với composer.json và artisan, mở project đó và tiếp tục ở bước cấu hình.

Mở file .env. Tìm và sửa các dòng tương ứng; mỗi tên cấu hình chỉ giữ một dòng:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=week2_products
DB_USERNAME=week2_user
DB_PASSWORD=YOUR_MYSQL_PASSWORD

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Giữ APP_KEY được Composer tạo sẵn. Nếu APP_KEY rỗng, chạy:

```bash
php artisan key:generate
```

Sau khi lưu .env:

```bash
php artisan config:clear
```

**4. Dùng Artisan tạo các file cần viết**

```bash
php artisan make:model Category -m
php artisan make:model Product -m
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
php artisan make:resource ProductResource
php artisan make:controller ProductController --api
php artisan make:controller CategoryController
php artisan make:middleware RequireJson
php artisan make:exception JsonRequiredException
```

Ý nghĩa: model làm việc với dữ liệu; migration tạo bảng; request kiểm tra input; resource định dạng output; controller xử lý endpoint; middleware kiểm tra request trước controller.

**5. Viết migration tạo hai bảng**

Trong database/migrations, mở file có tên kết thúc bằng \_create_categories_table.php. Phần timestamp đầu tên file do Artisan sinh ra.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('categories', function (Blueprint $table): void {
      $table->id();
      $table->string('name')->unique();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('categories');
  }
};
```

Mở file kết thúc bằng \_create_products_table.php:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('products', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('category_id')->constrained()->restrictOnDelete();
      $table->string('name');
      $table->decimal('price', 10, 2);
      $table->text('description')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
```

category_id là khóa ngoại tới categories.id. decimal(10, 2) lưu giá với tối đa 10 chữ số, trong đó có 2 chữ số sau dấu thập phân. Thứ tự migration phải tạo categories trước products; hai lệnh make:model phía trên tạo theo thứ tự đó.

**6. Viết model và relationship**

File app/Models/Category.php:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
  protected $fillable = ['name'];

  public function products(): HasMany
  {
    return $this->hasMany(Product::class);
  }
}
```

File app/Models/Product.php:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
  protected $fillable = [
    'category_id',
    'name',
    'price',
    'description',
  ];

  protected function casts(): array
  {
    return [
      'price' => 'decimal:2',
    ];
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }
}
```

fillable quy định các trường được phép gán hàng loạt bằng create/update. Cast decimal:2 làm price được biểu diễn thành chuỗi như "199000.00" khi trả JSON.

**7. Viết Form Request kiểm tra dữ liệu**

File app/Http/Requests/StoreProductRequest.php:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'category_id' => ['required', 'integer', 'exists:categories,id'],
      'name' => ['required', 'string', 'max:255'],
      'price' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
      'description' => ['nullable', 'string', 'max:2000'],
    ];
  }
}
```

File app/Http/Requests/UpdateProductRequest.php:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $requiredRule = $this->isMethod('PUT') ? 'required' : 'sometimes';
    $descriptionRule = $this->isMethod('PUT') ? 'present' : 'sometimes';

    return [
      'category_id' => [$requiredRule, 'integer', 'exists:categories,id'],
      'name' => [$requiredRule, 'string', 'max:255'],
      'price' => [$requiredRule, 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
      'description' => [$descriptionRule, 'nullable', 'string', 'max:2000'],
    ];
  }
}
```

authorize trả true vì bài này luyện CRUD công khai trên máy cá nhân. required bắt buộc có dữ liệu; sometimes chỉ kiểm tra trường khi trường đó được gửi; present bắt buộc có key nhưng cho phép description là null. PUT gửi đầy đủ bốn trường; PATCH chỉ gửi trường cần sửa.

Laravel kiểm tra Form Request trước khi chạy method controller. Sau đó validated() lấy các trường đã qua validation. [Form Request Validation](https://laravel.com/docs/13.x/validation#form-request-validation)

**8. Viết Resource định dạng JSON**

File app/Http/Resources/ProductResource.php:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'price' => $this->price,
      'description' => $this->description,
      'category' => $this->whenLoaded('category', fn (): array => [
        'id' => $this->category->id,
        'name' => $this->category->name,
      ]),
      'created_at' => $this->created_at?->toISOString(),
      'updated_at' => $this->updated_at?->toISOString(),
    ];
  }
}
```

whenLoaded chỉ đưa category vào JSON khi controller đã tải relationship. Resource này không tự query danh mục cho từng sản phẩm. [Conditional Relationships](https://laravel.com/docs/13.x/eloquent-resources#conditional-relationships)

**9. Viết controller**

File app/Http/Controllers/ProductController.php:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
  public function index(): AnonymousResourceCollection
  {
    $products = Product::query()
      ->with('category:id,name')
      ->orderByDesc('id')
      ->paginate(10);

    return ProductResource::collection($products);
  }

  public function store(StoreProductRequest $request): JsonResponse
  {
    $product = Product::create($request->validated());
    $product->load('category:id,name');

    return (new ProductResource($product))
      ->response()
      ->setStatusCode(201);
  }

  public function show(Product $product): ProductResource
  {
    return new ProductResource($product->load('category:id,name'));
  }

  public function update(
    UpdateProductRequest $request,
    Product $product
  ): ProductResource {
    $product->update($request->validated());

    return new ProductResource($product->load('category:id,name'));
  }

  public function destroy(Product $product): Response
  {
    $product->delete();

    return response()->noContent();
  }
}
```

with tải danh mục cho cả danh sách trong một query riêng, giúp tránh N+1. paginate tạo JSON với data, links và meta. Route model binding tìm Product từ ID trong URL; nếu không tồn tại, Laravel trả lỗi 404.

File app/Http/Controllers/CategoryController.php:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
  public function index(): JsonResponse
  {
    return response()->json([
      'data' => Category::query()->orderBy('id')->get(['id', 'name']),
    ]);
  }
}
```

**10. Viết custom exception và middleware**

File app/Exceptions/JsonRequiredException.php:

```php
<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonRequiredException extends HttpException
{
  public function __construct()
  {
    parent::__construct(415, 'Content-Type must be application/json.');
  }
}
```

File app/Http/Middleware/RequireJson.php:

```php
<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonRequiredException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireJson
{
  public function handle(Request $request, Closure $next): Response
  {
    $hasWriteBody = in_array($request->method(), ['POST', 'PUT', 'PATCH'], true);

    if ($hasWriteBody && !$request->isJson()) {
      throw new JsonRequiredException();
    }

    return $next($request);
  }
}
```

Middleware yêu cầu POST/PUT/PATCH có Content-Type JSON. Nếu sai, middleware ném custom exception với status 415; request dừng trước controller. GET và DELETE không cần gửi JSON body.

**11. Đăng ký route, middleware và Exception Handler**

Tạo file routes/api.php trong VS Code nếu chưa có:

```php
<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('require.json')->group(function (): void {
  Route::get('categories', [CategoryController::class, 'index']);
  Route::apiResource('products', ProductController::class);
});
```

apiResource ánh xạ các HTTP method tới index/store/show/update/destroy. Laravel tự thêm tiền tố /api khi nạp file qua cấu hình api. [Laravel Routing](https://laravel.com/docs/13.x/routing)

File bootstrap/app.php:

```php
<?php

use App\Http\Middleware\RequireJson;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
      'require.json' => RequireJson::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (
      Throwable $exception,
      Request $request
    ): ?JsonResponse {
      if (!$request->is('api', 'api/*')) {
        return null;
      }

      if ($exception instanceof ValidationException) {
        return response()->json([
          'message' => 'Validation failed.',
          'errors' => $exception->errors(),
        ], 422);
      }

      $status = $exception instanceof HttpExceptionInterface
        ? $exception->getStatusCode()
        : 500;

      $headers = $exception instanceof HttpExceptionInterface
        ? $exception->getHeaders()
        : [];

      $message = match ($status) {
        400 => 'Bad request.',
        401 => 'Unauthenticated.',
        403 => 'Forbidden.',
        404 => 'Resource not found.',
        405 => 'Method not allowed.',
        415 => 'Content-Type must be application/json.',
        429 => 'Too many requests.',
        default => $status >= 500 ? 'Internal server error.' : 'Request failed.',
      };

      return response()->json([
        'message' => $message,
        'errors' => (object) [],
      ], $status, $headers);
    });
  })
  ->create();
```

Handler này chuẩn hóa lỗi API thành message/errors; lỗi validation là 422, thiếu tài nguyên là 404, sai Content-Type là 415. Lỗi không dự kiến trả 500 với thông báo chung; xem chi tiết trong storage/logs/laravel.log. HTTP headers của exception được giữ lại, chẳng hạn Allow của lỗi 405.

Laravel 13 cấu hình middleware và exception trong bootstrap/app.php. [Middleware Aliases](https://laravel.com/docs/13.x/middleware#middleware-aliases), [Rendering Exceptions](https://laravel.com/docs/13.x/errors#rendering-exceptions)

**12. Thêm dữ liệu mẫu và bật kiểm tra lazy loading**

File app/Providers/AppServiceProvider.php:

```php
<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  public function boot(): void
  {
    Model::preventLazyLoading($this->app->isLocal());
  }
}
```

Ở môi trường local, Laravel sẽ báo lỗi khi phát hiện lazy loading trên các model được bật cơ chế này. Đây là hỗ trợ phát hiện N+1; vẫn cần kiểm tra các query và cách dùng relationship. [Preventing Lazy Loading](https://laravel.com/docs/13.x/eloquent-relationships#preventing-lazy-loading)

File database/seeders/DatabaseSeeder.php:

```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $electronics = Category::firstOrCreate(['name' => 'Điện tử']);
    $books = Category::firstOrCreate(['name' => 'Sách']);

    for ($index = 1; $index <= 12; $index++) {
      Product::firstOrCreate(
        ['name' => 'Sản phẩm mẫu '.$index],
        [
          'category_id' => $index % 2 === 0 ? $books->id : $electronics->id,
          'price' => $index * 10000,
          'description' => 'Dữ liệu để luyện CRUD.',
        ]
      );
    }
  }
}
```

Tạo bảng và nạp dữ liệu:

```bash
php artisan config:clear
php artisan migrate --seed
php artisan route:list --path=api -v
```

Bạn cần thấy route categories và các route products, có middleware require.json. Seeder tạo hai danh mục và 12 sản phẩm mẫu để luyện phân trang/N+1. Có thể chạy lại seeder bằng php artisan db:seed; firstOrCreate tìm theo name trước khi thêm.

**13. Chạy API**

Trong terminal tại thư mục thanglvc.github.io/week2_MiniProject:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Giữ terminal này chạy. Mở Postman để gọi API theo phần 14; terminal này cần tiếp tục chạy trong lúc bạn bấm Send. Các lệnh Artisan và phần Tinker kiểm tra N+1 vẫn chạy trong terminal.

**14. Thử CRUD lần lượt bằng Postman**

Dùng Postman để chọn HTTP method, gửi dữ liệu và xem JSON response dễ đọc. Giữ terminal chạy `php artisan serve` mở trong suốt quá trình thực hành.

**Bắt đầu lại phần 14 ngày 02/10/2026 theo yêu cầu người học:** dữ liệu đã được dọn về 2 danh mục và 12 sản phẩm mẫu, ID sản phẩm từ 1 đến 12. Sản phẩm thử ID 13 đã được xóa. Làm lại từ **14.1 — chuẩn bị Postman** rồi **14.2 — GET danh mục**; gửi kết quả từng request để kiểm tra. Khi POST tạo sản phẩm mới, lấy ID mới trong response cho các bước xem/sửa/xóa.

**14.1. Chuẩn bị request trong Postman**

1. Mở Postman và tạo một HTTP request.
2. Chọn method trong ô bên trái URL, nhập URL đầy đủ theo từng bước dưới đây.
3. Trong tab **Headers**, thêm `Accept` với giá trị `application/json`.
4. Với POST/PUT/PATCH gửi JSON: trong **Body**, chọn **raw**, rồi chọn **JSON** trong menu định dạng. Postman tự thêm `Content-Type: application/json`. Dán khối JSON của bước tương ứng.
5. Với GET/DELETE: chọn **Body → none**.
6. Bấm **Send**, xem status và JSON ở vùng response. Chọn chế độ hiển thị JSON có thụt lề nếu cần; ở các phiên bản có **Pretty**, chọn **Pretty → JSON**.
7. Gửi status và body response cho AI kiểm tra sau mỗi bước trước khi chuyển tiếp.

`Accept` cho biết bạn muốn nhận JSON; `Content-Type` mô tả định dạng body gửi lên. Đối chiếu thao tác body/header tại [Postman Docs — Request body](https://learning.postman.com/docs/use/send-requests/create-requests/parameters/).

Dùng Postman desktop để gọi API trên máy. Nếu dùng bản web, chọn Desktop Agent đang chạy để gửi request tới địa chỉ local. [Postman Docs — Postman Agent](https://learning.postman.com/latest-v-12/docs/getting-started/basics/about-postman-agent)

**14.2. GET — xem danh mục và phân trang**

Chạy từng request, đều có header `Accept: application/json` và Body `none`:

| Method | URL | Kỳ vọng |
| --- | --- | --- |
| GET | `http://127.0.0.1:8000/api/categories` | 200, data gồm hai danh mục có id/name |
| GET | `http://127.0.0.1:8000/api/products` | 200, trang 1 có tối đa 10 sản phẩm |
| GET | `http://127.0.0.1:8000/api/products?page=2` | 200, trang 2 có các sản phẩm còn lại |

Với 12 sản phẩm ngay sau seed: trang 1 có 10 sản phẩm, trang 2 có 2; meta có current_page tương ứng, per_page 10, total 12, last_page 2. Khi đã POST thêm sản phẩm, total và số sản phẩm trang 2 thay đổi theo dữ liệu thực tế.

Mỗi sản phẩm cần có category gồm id/name và price dạng chuỗi hai chữ số thập phân. JSON có category chưa chứng minh số query; phần 15 kiểm tra N+1 riêng.

Ghi nhận ID danh mục thực tế. Trong buổi học hiện tại, Điện tử có ID 1, Sách có ID 2. Nếu thực hành lại với dữ liệu khác, thay category_id trong các body tiếp theo bằng ID nhận được.

**14.3. POST — tạo sản phẩm**

- Method: **POST**.
- URL: `http://127.0.0.1:8000/api/products`.
- Headers: `Accept: application/json`; Body raw/JSON tự đặt `Content-Type: application/json`.
- Body → raw → JSON:

```json
{
  "name": "Bàn phím",
  "price": 199000,
  "description": "Bàn phím dùng để thực hành",
  "category_id": 1
}
```

Bấm Send. Kỳ vọng **201 Created**, JSON có dạng sau; số 13 trong response minh họa là ví dụ, ID thật có thể khác:

```json
{
  "data": {
    "id": 13,
    "name": "Bàn phím",
    "price": "199000.00",
    "description": "Bàn phím dùng để thực hành",
    "category": {
      "id": 1,
      "name": "Điện tử"
    },
    "created_at": "...",
    "updated_at": "..."
  }
}
```

Ghi nhận ID thật trong response. Trong các URL dưới đây, **thay `<id>` bằng ID sản phẩm vừa tạo** trước khi Send. Ví dụ, nếu response có id 14 thì nhập `/api/products/14`. ID 13 của lần thử trước đã bị xóa; không dùng ID cũ. Mỗi lần Send POST hợp lệ có thể tạo thêm một sản phẩm mới.

**14.4. GET — xem chi tiết sản phẩm vừa tạo**

- Method: **GET**.
- URL: `http://127.0.0.1:8000/api/products/<id>`.
- Header: `Accept: application/json`.
- Body: **none**.

Bấm Send. Kỳ vọng **200 OK**, data khớp sản phẩm vừa tạo: id vừa nhận từ POST, name Bàn phím, price "199000.00", description đã gửi và category Điện tử id 1.

**14.5. PUT — thay đầy đủ bốn trường**

- Method: **PUT**.
- URL: `http://127.0.0.1:8000/api/products/<id>`.
- Headers: `Accept: application/json`; Content-Type JSON do Body raw/JSON đặt.
- Body → raw → JSON:

```json
{
  "name": "Bàn phím mới",
  "price": 299000,
  "description": null,
  "category_id": 1
}
```

Bấm Send. Kỳ vọng **200 OK**, id vẫn là ID sản phẩm vừa tạo, name thành "Bàn phím mới", price thành "299000.00", description là null và category Điện tử id 1.

Trong bài này, PUT phải gửi đủ bốn trường. description dùng present kết hợp nullable: phải có key nhưng giá trị được phép là null.

**14.6. PATCH — chỉ sửa giá**

- Method: **PATCH**.
- URL: `http://127.0.0.1:8000/api/products/<id>`.
- Headers: `Accept: application/json`; Content-Type JSON do Body raw/JSON đặt.
- Body → raw → JSON:

```json
{
  "price": 249000
}
```

Bấm Send. Kỳ vọng **200 OK**, price thành "249000.00". Các trường không gửi giữ giá trị sau PUT: name "Bàn phím mới", description null, category Điện tử id 1.

PATCH dùng sometimes, nên chỉ kiểm tra những trường được gửi lên. Nếu muốn xác nhận dữ liệu đã lưu sau PUT/PATCH, gọi lại GET chi tiết.

**14.7. POST — kiểm tra validation 422**

- Method: **POST**.
- URL: `http://127.0.0.1:8000/api/products`.
- Headers: `Accept: application/json`; Content-Type JSON do Body raw/JSON đặt.
- Body → raw → JSON:

```json
{
  "name": "",
  "price": -1,
  "category_id": 999999
}
```

Bấm Send. Kỳ vọng **422 Unprocessable Content**, message "Validation failed." và errors có name/price/category_id. Tên rỗng, giá âm và danh mục không tồn tại đều bị từ chối. Một số phiên bản có thể hiển thị status text "Unprocessable Entity"; mã cần đối chiếu là 422.

**14.8. POST — kiểm tra middleware/custom exception 415**

- Method: **POST**.
- URL: `http://127.0.0.1:8000/api/products`.
- Header: `Accept: application/json`.
- Body: chọn **x-www-form-urlencoded**, nhập ba dòng:

| Key | Value |
| --- | --- |
| name | Ban phim |
| price | 100000 |
| category_id | 1 |

Postman tự đặt `Content-Type: application/x-www-form-urlencoded` khi chọn loại body này. Nếu request đang có header Content-Type JSON bạn đã thêm thủ công, bỏ chọn hoặc xóa dòng đó để header gửi đi đúng với body dạng form. [Postman Docs — URL-encoded body](https://learning.postman.com/docs/use/send-requests/create-requests/parameters/)

Bấm Send. Kỳ vọng **415 Unsupported Media Type** và response:

```json
{
  "message": "Content-Type must be application/json.",
  "errors": {}
}
```

Đây là đường đi qua RequireJson rồi JsonRequiredException; request bị dừng trước controller.

**14.9. DELETE — xóa sản phẩm vừa tạo**

- Method: **DELETE**.
- URL: `http://127.0.0.1:8000/api/products/<id>`.
- Header: `Accept: application/json`.
- Body: **none**.

Bấm Send. Kỳ vọng **204 No Content**, vùng body response rỗng.

Sau đó đổi method thành **GET**, giữ URL sản phẩm vừa xóa và Body none, bấm Send. Kỳ vọng **404 Not Found** và response:

```json
{
  "message": "Resource not found.",
  "errors": {}
}
```

Lưu lại status/body thực tế sau mỗi bước để ghi bằng chứng ở phần 18. Tiếp tục từng bước từ điểm đang làm; không đánh dấu các bước còn lại đã đạt chỉ vì tài liệu có ví dụ.

**15. Thực hành nhìn thấy N+1**

Mở terminal trong project:

```bash
php artisan tinker
```

Các lệnh sau dán vào Tinker từng dòng. Ví dụ đầu tạm cho phép lazy loading trong phiên Tinker này để nhìn thấy query tăng:

```php
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Model::preventLazyLoading(false);
DB::enableQueryLog();
DB::flushQueryLog();
$products = Product::query()->get();
$products->each(fn (Product $product) => $product->category->name);
count(DB::getQueryLog());
```

Nếu có N sản phẩm, ví dụ này dự kiến N + 1 query: một query lấy sản phẩm, rồi một query lấy danh mục cho mỗi sản phẩm. Ngay cả khi nhiều sản phẩm thuộc cùng một danh mục, từng model vẫn có thể lazy load riêng.

Bây giờ eager load:

```php
DB::flushQueryLog();
$products = Product::query()->with('category')->get();
$products->each(fn (Product $product) => $product->category->name);
count(DB::getQueryLog());
```

Với dữ liệu mẫu hiện có, dự kiến 2 query: lấy sản phẩm và lấy các danh mục liên quan. Index API dùng paginate nên thông thường thêm một query count, thành 3 query cho dữ liệu danh sách; số này không tăng một query cho mỗi sản phẩm.

Kết thúc phiên:

```php
Model::preventLazyLoading(true);
DB::disableQueryLog();
exit
```

Eager loading dùng with để tải relationship theo nhóm. [Eager Loading](https://laravel.com/docs/13.x/eloquent-relationships#eager-loading)

**16. Tự giải thích đường đi của một request**

Với POST /api/products:

1. Route chọn ProductController.store.
2. RequireJson kiểm tra Content-Type; sai thì ném JsonRequiredException.
3. Laravel tạo StoreProductRequest, chạy authorize và rules.
4. Controller lấy validated(), gọi Product::create.
5. Model ghi dữ liệu vào MySQL; load tải danh mục.
6. ProductResource định dạng JSON; response có status 201.
7. Nếu xảy ra exception, Handler trong bootstrap/app.php tạo JSON lỗi.

Với GET /api/products/13, route model binding tìm Product có ID 13 trước khi gọi show. Service Container giải quyết controller và request; route model binding chịu trách nhiệm lấy model tương ứng với tham số URL.

Hãy tự trả lời các câu sau bằng lời của bạn:

- Vì sao create/update dùng validated() cùng fillable?
- Middleware khác Form Request ở đâu trong bài này?
- POST trả 201, DELETE trả 204 vì sao?
- category_id nối hai bảng thế nào?
- with và whenLoaded mỗi cái làm việc gì?
- PUT khác PATCH thế nào trong code bạn vừa viết?
- Khi gửi ID không tồn tại, phần nào tạo lỗi 404?
- Nếu bỏ eager loading rồi truy cập category trong vòng lặp, số query thay đổi ra sao?

**17. Khi gặp lỗi, kiểm tra đúng chỗ**

| Hiện tượng | Kiểm tra |
| --- | --- |
| could not find driver | Bước 1: PDO cần có mysql trong terminal chạy Artisan |
| SQLSTATE Connection refused | MySQL đã khởi tạo xong chưa, .env có port 3307 và host 127.0.0.1 không |
| Access denied | DB_USERNAME/DB_PASSWORD có khớp biến Docker ở bước 2 không |
| Unknown database / table doesn't exist | Tên database và bước migrate --seed |
| 403 khi POST/PATCH | authorize() của Form Request cần trả true trong bài này |
| 415 | Gửi Content-Type: application/json khi POST/PUT/PATCH |
| 422 | Đọc errors, sửa dữ liệu; PUT cần đầy đủ bốn trường |
| 404 cho mọi endpoint | Kiểm tra api trong bootstrap/app.php và route:list |
| 404 chỉ với một ID | Kiểm tra ID thực tế; có thể record đã bị xóa |
| 500 | Đọc storage/logs/laravel.log để tìm exception thật |
| Port 8000 đang dùng | Dùng --port=8001 và đổi các URL request trong Postman sang 8001 |

Xem log Laravel:

```bash
tail -n 80 storage/logs/laravel.log
```

**18. Ghi bằng chứng để review với mentor**

Trong project, tạo file NOTES.md và ghi kết quả thực tế:

```text
Phạm vi: API CRUD sản phẩm, danh mục được seed và có API xem danh sách.

Phần AI hỗ trợ:
- Đề xuất cấu trúc file, Form Request, Resource, middleware và Handler.
- Cung cấp ví dụ code và hướng dẫn kiểm tra API bằng Postman; curl đã dùng cho các bước đầu trong buổi thực hành.

Phần tôi đã tự làm và giải thích được:
- Điền sau khi thực hành.

Kết quả kiểm tra:
- GET list/detail/category: chưa kiểm tra.
- POST 201, PUT/PATCH 200: chưa kiểm tra.
- DELETE 204, GET lại 404: chưa kiểm tra.
- Validation 422: chưa kiểm tra.
- Middleware/custom exception 415: chưa kiểm tra.
- N+1 trước/sau eager loading: chưa kiểm tra.

Lỗi còn lại và cách xử lý:
- Điền kết quả thực tế.
```

Đổi trạng thái sau khi bạn chạy được và lưu output. Hướng dẫn không tự xác nhận bạn đã hoàn thành bài hoặc hiểu toàn bộ logic. Event/Queue đã nằm trong nội dung học ngày 4; đầu ra ngày 5 trong PDF tập trung CRUD và bốn mục review kể trên.
