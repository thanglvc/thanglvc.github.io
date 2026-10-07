# Mini Project — Quy tắc giao diện và Laravel

Bổ sung ngày 06/10/2026. Phần giao diện được đồng bộ từ [AGENTS.md của workspace](../../AGENTS.md), bản cập nhật 05/10/2026; đặt trực tiếp tại đây để model mở riêng mini project vẫn đọc được. Phần Laravel Boost/PHP ở cuối file tiếp tục áp dụng.

## Áp dụng trong mini project

- Đọc [AI_CONTEXT.md](AI_CONTEXT.md) để xác định yêu cầu, phạm vi và tiến độ. Đọc BD, DD, API specs, workflow và testcase của chức năng trước khi implement; khi chúng không khớp, nêu rõ sai khác, không tự đổi yêu cầu hoặc expected để code đạt test.
- Với chức năng tạo sản phẩm, căn cứ là [BD](docs/products/create/bd_create_product.md), [DD](docs/products/create/dd_create_product.md), [API tạo sản phẩm](docs/products/create/api_specs/api_create_product.md), [API danh mục](docs/categories/list/api_specs/api_load_categories.md) và [testcase FE/BE](docs/products/create/testcase_create_product.md). Trạng thái thiết kế đã duyệt không phải bằng chứng giao diện đã chạy đúng.
- Giữ stack Laravel/Blade, CSS và JavaScript cùng Vite hiện có. Tailwind đã có trong build; giữ cấu hình và style hiện hữu, dùng BEM cho component mới theo quy tắc dưới đây. Không tự chuyển sang React/Vue, thêm Sass/build pipeline hoặc dependency khác.
- Quy tắc 2 khoảng trắng bên dưới áp dụng cho markup Blade/HTML, CSS/SCSS và JavaScript. PHP theo convention/Pint của project; không đổi thụt lề PHP hoặc line ending hàng loạt để đồng nhất với giao diện.
- View ở `resources/views/`, CSS ở `resources/css/`, JavaScript ở `resources/js/`; tái sử dụng layout/component đang có. Nạp asset qua Vite với entry đã khai báo, CSS trước JS, chỉ nạp code tương tác ở trang cần. Có thể bổ sung entry khi chức năng cần, không đổi toàn bộ cấu trúc build.
- Giữ cơ chế escape mặc định của Blade với dữ liệu không tin cậy; không xuất raw HTML từ tên danh mục, nội dung form hoặc message API. JavaScript hiển thị dữ liệu như văn bản theo mục Bảo mật và chuẩn hóa dữ liệu.
- Đăng ký web route cần thiết để mở giao diện được giao. Tích hợp API thật theo DD/API specs; thay đổi UI không tự cho phép sửa hợp đồng API, validation/nghiệp vụ BE, migration hoặc dữ liệu hiện có.
- Validation FE thực hiện đúng thời điểm/giới hạn và thông báo trong DD; hỗ trợ Unicode khi DD quy định. Không dùng giới hạn input hoặc chuyển kiểu để âm thầm cắt/làm tròn dữ liệu trước khi có thể kiểm tra case vượt giới hạn. Loại input và chuẩn hóa từng trường phải bám hợp đồng đã chốt.
- Với form gọi API, giữ đầy đủ trạng thái loading/empty/error/submitting/success theo DD. Kiểm tra submit lặp, lỗi theo field, giữ/reset dữ liệu, focus và kết thúc trạng thái gửi ở mọi nhánh; không bỏ nhánh lỗi vì khó tạo tình huống test.
- Chỉ áp dụng quy tắc ảnh, CMS, modal hoặc animation khi chức năng có phần đó. Không tự thêm các thành phần này để đáp ứng checklist. Khi chưa có design màu/kích thước, ghi rõ lựa chọn trình bày, không báo đã khớp design bằng phép đo chưa thực hiện.
- Helper `mock_create_product_requests.js` đã được người học yêu cầu bỏ; không tự tạo lại. Dùng cách test hiện có trong testcase; nhánh chưa thiết lập được phải ghi chưa chạy cùng lý do, không suy ra FE Pass từ Pest BE.

## Tài liệu cho mọi chức năng của mini project

- Áp dụng pipeline `raw spec → BD → DD → testcase → code` cho mọi chức năng trong phạm vi hiện tại. Đọc [mục lục docs](docs/README.md), rồi đủ bộ tài liệu của chức năng được giao tại `docs/<module>/<feature>/`: `raw_spec_<feature_slug>.md`, `bd_<feature_slug>.md`, `dd_<feature_slug>.md`, `testcase_<feature_slug>.md`, API specs và workflow. Tên file mới có loại tài liệu + chức năng, ví dụ `dd_create_product.md`; API có tiền tố `api_`.
- Bộ tạo sản phẩm đã được người học duyệt; các bộ danh sách/chi tiết/cập nhật/xóa/danh mục là bản nháp mới chờ review. Yêu cầu gen tài liệu không tự cho phép triển khai toàn bộ chức năng; khi được giao code, bám đúng phần đã duyệt và scope được giao.
- API dùng chung có một bản chuẩn, liên kết từ DD thay vì sao chép. Trạng thái testcase và report phải có bằng chứng; giữ kết quả lần chạy cũ, không suy ra UI Pass từ Pest API.

## Phạm vi và cách làm

- Đọc đề, design, asset và code hiện có; ghi rõ giả định, không tự thêm page hoặc chức năng.
- Ưu tiên yêu cầu mới nhất của người dùng và đề bài. Các quy tắc dưới đây là convention áp dụng trong workspace, đã gồm điều chỉnh cho tài liệu cũ và thực hành bổ sung.
- Giữ stack/toolchain của bài; tái sử dụng component, style và hàm chung. Không tự đổi framework hoặc thêm dependency.
- Chỉ sửa đúng phạm vi; giữ nguyên tài liệu nguồn và thay đổi không liên quan.
- Khi gặp khác biệt chưa được giải quyết trong file này, đối chiếu nguồn và nêu rõ; không tự bỏ convention.
- Quy tắc về dữ liệu động, CMS, form, modal và hiệu năng áp dụng cho phần có trong đề hoặc code đang sửa. Tính năng CSS/API mới phải phù hợp browser matrix của bài; không suy ra hỗ trợ mọi trình duyệt.

## Định dạng và naming

- UTF-8, thụt lề 2 khoảng trắng, không tab cứng. File mới dùng LF; không đổi line ending hàng loạt ở file cũ.
- Tag/attribute HTML và class viết thường; SVG giữ đúng tên phân biệt hoa thường như `viewBox`, `preserveAspectRatio`. Attribute HTML và attribute selector CSS dùng nháy kép.
- Class dùng BEM và kebab-case: `product-card`, `product-card__title`, `product-card--featured`.
- Modifier đi cùng class gốc. Không viết `block__element__child`; dùng `block__child` hoặc block độc lập.
- Tên mô tả vai trò, có nghĩa; tránh viết tắt tùy tiện, tên một ký tự hoặc tên chỉ màu sắc.
- Hook JS dùng tiền tố `js-`; tuyệt đối không style `.js-*`. Dùng BEM modifier cho biến thể thiết kế như `button--primary`, `product-card--horizontal`; dùng `is-*`/`has-*` cho trạng thái như `is-expanded`, `is-loading`, `has-error`. State có thể được khởi tạo từ HTML/server và cập nhật bởi JS; selector state phải có phạm vi component. Layout có thể dùng `l-*`, utility dùng `u-*`.
- Biến/hàm JS dùng camelCase. Tên ảnh dùng dấu `_`, không áp kebab-case của class sang ảnh.
- Comment giải thích mục đích/bối cảnh, mặc định dùng English ngắn gọn; dùng ngôn ngữ khác theo yêu cầu dự án. Chỉ bắt buộc English + Japanese khi đề hoặc convention riêng yêu cầu, mỗi ý gồm dòng English rồi dòng Japanese. Cho phép nhiều dòng/JSDoc khi cần mô tả hàm; không thêm comment chỉ để nhắc lại tên class; xóa code chết và code comment-out không cần thiết.

## HTML

- Có doctype, `lang`, UTF-8, viewport và title; kiểm tra description/keyword theo yêu cầu bài.
- Dùng semantic HTML, heading đúng cấp, `ul`/`ol` cho danh sách, `dl`/`dt`/`dd` cho mô tả. Tránh wrapper dư và cấu trúc lồng không hợp lệ.
- Viết gọn danh sách: mỗi `li` ngắn trên một dòng; chỉ xuống dòng khi có nhiều nội dung hoặc cấu trúc phức tạp. Không minify cả `ul`, không thêm `span`/`div` nếu không có vai trò semantic hoặc layout.
- Dùng `p` cho đoạn văn, `strong` cho nội dung nhấn mạnh; không dùng hai `br` liên tiếp để tạo khoảng cách.
- Thứ tự attribute: `class` → `id, name` → `data-*` → `src, srcset, sizes, for, type, href, value` → `width, height, loading, decoding, fetchpriority` → `title, alt` → các attribute chức năng khác → `role, aria-*`.
- Boolean attribute viết rút gọn: `disabled`, `checked`, `selected`. Escape ký tự có ý nghĩa cú pháp HTML.
- ID phải duy nhất, chỉ dùng cho định danh, fragment, label hoặc API; không dùng để style.
- `a` cho điều hướng, `button` cho hành động; vùng click phải bao đúng nội dung theo design.
- Link ngoài mặc định mở tab mới, trừ yêu cầu bài. Link `target="_blank"` phải có `rel="noopener"`; thêm `noreferrer` khi cần không gửi referrer.
- Dùng `figure` cho cụm ảnh độc lập, `figcaption` khi có chú thích; reset margin và đặt kích thước/crop trong CSS. Không thêm `figure` cho mọi icon.
- Ảnh nội dung có alt đúng nghĩa; ảnh trang trí dùng alt rỗng hoặc background. Title ảnh chỉ thêm khi cần hoặc đề yêu cầu.
- Giữ chỗ cho ảnh trước khi tải bằng `width`/`height` đúng tỉ lệ nguồn, hoặc kích thước CSS kết hợp `aspect-ratio` đúng tỉ lệ hiển thị; dùng `object-fit` khi cần crop. Kiểm tra cả ảnh responsive và ảnh lazy-load để tránh CLS.
- Ảnh hiển thị ngay ở đầu trang không dùng `loading="lazy"`; giữ mặc định eager hoặc khai báo `loading="eager"`. Chỉ thêm `fetchpriority="high"` cho ảnh LCP quan trọng đã xác định, không áp đồng loạt cho logo/ảnh đầu trang. Ảnh ngoài vùng nhìn ban đầu dùng `loading="lazy"`, ưu tiên `decoding="async"`; ngoại lệ phải có lý do theo hành vi hiển thị.
- Dùng `srcset`/`sizes` hoặc `picture` khi có nhiều kích thước/crop nguồn phù hợp; khai báo `sizes` theo layout thực tế, tránh tải ảnh lớn không cần thiết.
- CSS/JS ở file ngoài; không inline style, inline event hoặc script nhúng, trừ yêu cầu hạ tầng/bài. Khai báo CSS trước JS.

## CSS/SCSS

- Chia theo component/chức năng; có thể tổ chức Base/Layout/Module/State/Theme. Reset/normalize thống nhất, không reset rải rác.
- Ưu tiên một class BEM cho selector. Tránh ID, selector tag toàn cục ngoài Base/reset, universal selector rộng và attribute selector theo chuỗi khi class đáp ứng được.
- Không phụ thuộc sâu vào DOM; selector tối đa 3 class khi thực sự cần. Style component phải độc lập, không ảnh hưởng phần khác.
- Không dùng `!important` trong Base; phần khác chỉ ngoại lệ override bên thứ ba có lý do ghi cạnh khai báo.
- Mỗi selector nhóm nằm trên dòng riêng; có khoảng trắng trước `{`, sau `:`; mỗi declaration kết thúc bằng `;`, dấu `}` ở dòng riêng.
- Thứ tự property: positioning → box model/layout → typography → visual → animation/miscellaneous.
- Bỏ đơn vị của giá trị 0 khi cú pháp cho phép. Viết `.5`, `-.5px` thay `0.5`, `-0.5px`; giữ đơn vị có ý nghĩa.
- Màu khớp design; dùng eyedropper hoặc công cụ đọc pixel để kiểm tra trực tiếp vùng màu phẳng của ảnh gốc và output, ghi vị trí/mã màu khi nghiệm thu. Không đoán màu từ mắt hoặc lấy pixel chữ anti-alias làm màu nền. Hex chữ thường và rút gọn khi tương đương; dùng biến cho giá trị dùng chung.
- Đưa giá trị dùng chung thành CSS Custom Properties: màu theo vai trò, spacing scale, font family và z-index khi có nhiều lớp. Token toàn cục đặt ở `:root`, token riêng đặt trong component; tái sử dụng hệ token hiện có, không tạo token/theme chưa dùng.
- Có font fallback; ưu tiên line-height không đơn vị tính theo design. Không khai báo trùng hoặc thừa.
- Với `@font-face`, mặc định dùng `font-display: swap`; có thể dùng `optional` khi chấp nhận giữ font fallback và đã kiểm tra thiết kế. Kiểm tra độ lệch metric giữa font chính/fallback để giảm CLS; chỉ preload font quan trọng thực sự dùng ở đầu trang.
- SCSS nesting trong phạm vi component; dùng `&__element`, `&--modifier`. Phép tính đặt trong ngoặc với khoảng trắng quanh toán tử.
- Ưu tiên layout co giãn trước khi thêm media query. Dùng bộ breakpoint chung theo design hoặc điểm layout vỡ, giữ một mốc khi đã đủ; không thêm ngưỡng để vá từng pixel. Mặc định đặt responsive ngay sau khối CSS component hoặc trong SCSS component qua mixin chung; chỉ ghi thuộc tính thay đổi. Nếu bài hiện có hoặc đề yêu cầu `css/responsive.css`, tiếp tục dùng file đó và load sau style chính; không tự di chuyển toàn bộ CSS khi sửa cục bộ.
- Chỉ dùng CSS nesting/container queries khi browser matrix hoặc build hiện có hỗ trợ. Container queries phù hợp component thay đổi theo chiều rộng vùng chứa; đặt containment trên phần tử cha phù hợp và kiểm tra các nơi tái sử dụng.
- Ưu tiên logical properties như `padding-inline`, `margin-block`, `inset-inline-start` khi diễn đạt hướng nội dung; giữ thuộc tính vật lý khi design cần vị trí cố định hoặc code hiện có yêu cầu, tránh khai báo hai loại xung đột.
- Hạn chế `max-width` lặp ở nhiều component; chỉ giới hạn container/ảnh khi cần. Không thêm `max-width: 100%` khi `width: 100%` đã đủ.
- Khi có nhiều lớp chồng, dùng token z-index chung; thang tham chiếu: content `1–9`, header `100`, dropdown `200`, fixed navigation `500`, backdrop `900`, modal `1000`, toast `2000`. Chỉ tạo mức đang dùng; kiểm tra stacking context của ancestor và top layer của dialog/popover, không chữa lỗi bằng số `9999`. Dropdown/tooltip trong modal phải nằm trong lớp tương ứng của modal.
- Căn vị trí bằng layout, gap, padding hoặc margin; không dùng `transform: translate/scale` chỉ để dịch vài pixel hoặc kéo giãn chữ. Transform vẫn được phép nếu chính thiết kế/chức năng yêu cầu xoay, biến đổi hay animation.
- Đối chiếu border, shadow, pseudo-element với mẫu; xóa viền kép, declaration trùng, rule bị override và selector không còn dùng, đặc biệt khi review CSS do AI tạo.
- Thay đổi riêng page dùng modifier/class có phạm vi phù hợp; sửa component chung phải kiểm tra các nơi sử dụng.
- Giữ source theo component; bundle theo build hiện có, không bắt buộc nối thủ công mọi file.

## Dữ liệu động và tích hợp Backend

- Với khối nhận dữ liệu động, kiểm tra text dài/nhiều dòng, chuỗi không có khoảng trắng, dữ liệu thiếu, danh sách rỗng/1/nhiều item. Giữ nguyên dữ liệu nguồn khi thử và không tự thêm chức năng phân trang hay tải dữ liệu.
- Flex/Grid item chứa text cần co lại phải có `min-width: 0` hoặc `min-inline-size: 0`; dùng `overflow-wrap: anywhere` tại vùng có chuỗi dài. Không đặt height cố định làm che chữ; chỉ cắt dòng/ellipsis khi design cho phép và người dùng vẫn có cách tiếp cận nội dung đầy đủ khi cần.
- Dữ liệu rỗng/thiếu ảnh phải giữ layout hợp lý, có alt/fallback theo thiết kế hoặc hợp đồng dữ liệu. Không tạo `src=""`, không lặp vô hạn khi ảnh fallback cũng lỗi. `:empty` chỉ hỗ trợ trình bày, không thay kiểm tra dữ liệu hay thông báo trạng thái rỗng.
- Nội dung rich text từ CMS có namespace riêng, ví dụ `.entry-content`; cho phép selector tag trong namespace để style `p`, heading, list, table, blockquote, media. Xử lý bảng/đoạn code dài trong vùng cuộn riêng khi cần; không để CSS lan ra toàn trang. Namespace CSS không thay thế sanitization HTML.
- Khi bàn giao template cho Backend, ghi rõ trường bắt buộc/tùy chọn, giới hạn text/ảnh, trạng thái rỗng/lỗi và mẫu lặp. Giữ ID duy nhất khi lặp component; không phụ thuộc số item hay nội dung mẫu cố định nếu dữ liệu thực tế có thể thay đổi.

## Responsive và asset

- Layout co giãn theo design/nội dung. Dùng Flexbox cho bố cục một chiều như navbar, hàng icon-text, nhóm nút; dùng Grid cho bố cục hai chiều hoặc lưới card tự co giãn. Chọn cách ít wrapper và dễ bảo trì; với `minmax()` kiểm tra kích thước tối thiểu không vượt vùng chứa. Hạn chế float, dùng thì phải clear.
- Tính width cùng padding/border; ưu tiên border-box. Tránh kích thước cứng gây tràn hoặc che chữ.
- Ảnh giữ tỉ lệ/crop đúng; dùng max-width 100%, height auto khi phù hợp. Tránh ảnh bị kéo giãn trong flex; dùng wrapper/object-fit khi cần.
- Background dành cho ảnh trang trí, icon/bullet và ảnh minh họa/crop có nội dung tương đương đã thể hiện bằng chữ; đặt trên component hoặc pseudo-element thay vì tạo thẻ `img`/wrapper rỗng. Xác định size/position/repeat đúng mẫu, không đổi ảnh nội dung quan trọng sang background chỉ để giảm dòng. Ưu tiên text/CSS cho title/button khi đáp ứng thiết kế.
- Ưu tiên `rem` cho font-size và spacing cần tăng theo cỡ chữ ở mọi viewport; dùng `%`, `fr`, `auto`, `min()`/`max()`/`clamp()` cho layout khi phù hợp. Không bắt buộc đổi mọi `px` sang `rem` tại 768px: `rem` phụ thuộc font root, không tự co theo viewport. Giữ `px` cho viền mỏng/chi tiết cần kích thước ổn định, gồm offset bóng đổ nhỏ `1px`/`2px` khi design yêu cầu. Hàm SCSS đổi đơn vị chỉ dùng khi toolchain có sẵn và đã xác định font root thiết kế.
- Không ép root 62.5%, không coi 1rem luôn bằng 10px; giữ font root mặc định hoặc `100%` khi đề không yêu cầu khác. Không thu nhỏ font root theo chiều rộng màn hình để ép toàn bộ layout vừa mẫu; kiểm tra zoom và cài đặt cỡ chữ lớn.
- Viewport mặc định `width=device-width, initial-scale=1`; cho phép zoom. Kiểm tra cả hướng dọc/ngang, chữ và control sau resize.
- Với vùng cao theo màn hình trên mobile, chọn `svh` khi cần chiều cao ổn định lúc thanh trình duyệt hiện, `dvh` khi cần bám vùng nhìn thay đổi; `dvh` có thể làm layout đổi kích thước khi cuộn. Ưu tiên `min-height` cho vùng chứa nội dung dài; fallback `vh` chỉ khi browser matrix cần.
- Chỉ thêm prefix/bản vá Android/iOS khi xác minh cần thiết; giữ class body có sẵn. Không chép mẫu user-agent/text-stroke từ DOC.
- Asset mới dùng tiền tố đúng loại: `thumb_`, `pic_`, `bnr_`, `bg_`, `fig_`, `icn_`, `logo_`, `btn_`, `ttl_`, `txt_`. Ví dụ: `pic_related_content.jpg`.
- Ảnh trạng thái mới có thể dùng `_on`/`_off`. Giữ tên asset sẵn có nếu không cần đổi.
- Bảo toàn file ảnh gốc và đúng định dạng gốc, không chỉ đổi đuôi file. Bản xuất dùng trên web ưu tiên WebP, giữ kích thước/crop/màu/alpha đúng nguồn; dùng lossless khi cần giữ pixel. Không rasterize SVG chỉ để đổi sang WebP. Kiểm tra file thực sự được encode, đường dẫn, kích thước và 404.
- Inline SVG khi cần điều khiển path/màu/animation trong DOM; dùng `currentColor` cho icon đơn sắc khi phù hợp. Icon cố định dùng SVG file qua `img`/background; icon lặp có thể dùng sprite `<use>` nếu hệ asset đã hỗ trợ. SVG inline trang trí dùng `aria-hidden="true"`, SVG qua `img` áp dụng quy tắc alt của ảnh; control chỉ có icon phải có accessible name.
- Tối ưu bản SVG xuất bằng SVGO/công cụ sẵn có trước khi dùng; không tự thêm dependency chỉ để tối ưu. Giữ file gốc, kiểm tra `viewBox`, ID tham chiếu, gradient/mask và accessibility sau tối ưu; nếu thiếu công cụ, ghi rõ chưa kiểm tra tối ưu tự động. Tối ưu SVG không thay thế kiểm tra an toàn nguồn SVG.
- Release có thay đổi CSS/JS phải làm mới cache bằng version, query hoặc content hash theo toolchain.

## JavaScript và tương tác

- Chỉ tải script ở page cần; dùng defer/module khi phù hợp. Cấu hình chung ở đầu module hoặc file riêng; tránh global ảnh hưởng page khác.
- Code mới dùng const mặc định, let khi gán lại nếu môi trường hỗ trợ.
- Gắn event bằng addEventListener hoặc API của stack. Truy vấn qua hook `.js-*`; data-\* lưu cấu hình. Hook/ref khác chỉ dùng theo quy định dự án.
- Hàm có một nhiệm vụ; tách xử lý lặp, tránh hàm thừa và if/else lồng sâu.
- Comment phía trên mỗi hàm nêu mục đích, tham số/kiểu, có return hay không và ý nghĩa trả về; ghi rõ nếu không có tham số/return. Có thể dùng JSDoc, theo quy tắc ngôn ngữ comment ở trên.
- Tránh timer/listener trùng; dọn khi không còn dùng. Kiểm tra click nhiều lần, trạng thái rỗng/biên/lỗi và resize.
- Form dùng đúng input type, label rõ ràng, validate đúng thời điểm, hỗ trợ Enter; không hiện lỗi vô cớ khi refresh. Mật khẩu dùng `type="password"` và `autocomplete="current-password"`/`"new-password"` đúng mục đích; không tắt autocomplete mặc định cho mọi form. Validation phía client phải khớp hợp đồng API, không thay thế validation phía server.
- Chỉ thêm link đích, xử lý form, hover/active, transition, animation hoặc hiệu ứng khi đề/người dùng yêu cầu. Với bài dựng từ ảnh, giữ hình thức theo mẫu bằng text/class; không tự thêm `href="#"`, link giả, nút ẩn hay hành vi gửi dữ liệu.

## Accessibility và quản lý focus

- Với tương tác đã được yêu cầu, bảo đảm keyboard navigation, thứ tự Tab hợp lý, vùng bấm phù hợp và accessible name. Ưu tiên semantic HTML; dùng ARIA khi cần, đồng bộ trạng thái hiển thị với `aria-expanded`, `aria-selected`, `aria-invalid` theo control. Giữ tương phản, không truyền đạt thông tin chỉ bằng màu.
- Giữ focus mặc định hoặc thay bằng kiểu `:focus-visible` rõ ràng; không dùng `outline: none`/`0` nếu không có thay thế. Không dùng `tabindex` dương để vá thứ tự focus.
- Với dialog, drawer hoặc menu hoạt động như modal: đưa focus vào vị trí phù hợp khi mở, giữ Tab/Shift+Tab trong modal, làm nền không tương tác được, hỗ trợ Escape và nút đóng, trả focus về nút mở hoặc vị trí hợp lý nếu nút đã mất. Ưu tiên `<dialog>`/component sẵn có và kiểm tra hành vi thực tế; chỉ đặt `aria-modal="true"` khi đúng là modal.
- Menu điều hướng dạng disclosure thông thường dùng button với `aria-expanded`/`aria-controls`, cho phép Tab đi tiếp ra ngoài; không áp focus trap hoặc `role="menu"` chỉ vì tên là menu.
- Với trang có khối điều hướng lặp, thêm skip link trỏ tới ID thật của `main` và hiện rõ khi focus. Dùng utility chung `.u-visually-hidden` để ẩn chữ bổ trợ mà screen reader vẫn đọc được; không dùng `display: none` cho nội dung cần đọc, không để control vô hình nhận focus. Đây là hỗ trợ truy cập cho nội dung đã có, không phải chức năng nghiệp vụ mới.
- Nếu có animation được yêu cầu, tôn trọng `prefers-reduced-motion`; với form lỗi, liên kết thông báo với trường tương ứng và chỉ dùng live region khi có cập nhật cần thông báo.

## Bảo mật và chuẩn hóa dữ liệu

- Văn bản không tin cậy phải đi vào `textContent` của phần tử văn bản thông thường hoặc template có auto-escape; không ghép trực tiếp vào `innerHTML`, `outerHTML`, `insertAdjacentHTML` hay `document.write`. Không dùng `eval`/`Function` hoặc timer nhận chuỗi để chạy dữ liệu. Tạo element/attribute bằng tên cố định an toàn; `setAttribute()` không tự làm URL hay event attribute an toàn.
- Rich text cần giữ markup phải qua sanitizer được dự án chấp nhận và còn được bảo trì, ví dụ DOMPurify; không tự viết sanitizer bằng regex. Escape chỉ hiển thị HTML như văn bản, không thay sanitization khi cần render markup. Nếu chưa có sanitizer phù hợp, hiển thị dạng text và nêu phần rich text chưa đáp ứng; việc thêm dependency vẫn theo phạm vi được cho phép.
- URL động phải được parse và kiểm tra allowlist theo ngữ cảnh trước khi gán: link tài nguyên dùng `http:`/`https:`, `mailto:`/`tel:` chỉ cho link đúng mục đích; đường dẫn tương đối resolve theo base đáng tin cậy rồi kiểm tra. Chặn `javascript:`; `data:`/`blob:` chỉ dùng cho luồng cụ thể đã xác định. Với iframe/nguồn bị giới hạn, kiểm tra thêm origin; không đưa URL không tin cậy vào nguồn script.
- Không đưa secret, private API key, mật khẩu hoặc dữ liệu cá nhân thật vào source, HTML/data-\*, log hay fixture. Biến môi trường được bundle vào frontend vẫn là dữ liệu công khai. Không lưu mật khẩu, token đăng nhập hoặc dữ liệu nhạy cảm trong `localStorage`/`sessionStorage`; tích hợp theo cơ chế bảo mật của Backend, không tự thêm luồng xác thực.
- Chuỗi từ form/query/API phải kiểm tra kiểu và chuẩn hóa theo từng trường. Dùng `.trim()` cho tên/từ khóa/email khi hợp đồng dữ liệu bỏ qua khoảng trắng đầu/cuối, sau đó mới kiểm tra rỗng/độ dài và gửi/render giá trị đã chuẩn hóa. Không tự trim mật khẩu, token, nội dung cần giữ định dạng hoặc toàn bộ API payload; không ép `null`/`undefined` thành chuỗi. Trim không thay validation, escaping hay sanitization.

## Kiểm tra và bàn giao

- Sau mỗi lần code xong, bắt buộc test và tự review lại toàn bộ phần đã sửa trước khi bàn giao: HTML/wrapper/list thừa, semantic, CSS trùng/không dùng, border, transform, media query, asset và tương tác ngoài phạm vi.
- Tự review các quy tắc trên; so design tại đúng viewport: text, font, màu, spacing, border, ảnh và tương tác.
- Với responsive, kiểm tra viewport design, kích thước trung gian, breakpoint ±1px và xoay màn hình. Có thể tham khảo 375/390/768/1024/1440px; đây không phải breakpoint bắt buộc.
- Kiểm tra thêm zoom 200%, cỡ chữ lớn và thanh trình duyệt mobile ẩn/hiện với vùng dùng viewport units; không để chữ/control bị che hoặc tràn ngang ngoài vùng cuộn có chủ đích.
- Với dữ liệu động, thử tiêu đề 3–4 dòng, chuỗi 50 ký tự không khoảng trắng, danh sách rỗng/1/nhiều item và ảnh lỗi; với CMS, thử list/table/media dài trong namespace. Với form, kiểm tra chuỗi toàn khoảng trắng và trường phải giữ khoảng trắng; với URL/HTML động, kiểm tra dữ liệu độc hại không được thực thi.
- Với tương tác có trong bài, kiểm tra Tab/Shift+Tab, Enter/Space, Escape, focus sau mở/đóng và ARIA; thử click nhiều lần, resize khi control đang mở và reduced motion nếu có animation.
- Đối chiếu browser/OS với đề và checklist gốc; kiểm tra viewport không thay thế kiểm tra browser/thiết bị.
- Chạy build/lint/test hiện có; kiểm tra HTML và CSS bằng validator phù hợp, dùng CSS đầu ra đã biên dịch nếu viết SCSS. Đối chiếu cảnh báo CSS mới với browser matrix, ghi rõ giới hạn công cụ; kiểm tra console và lỗi tải asset.
- Khi thay đổi tải ảnh/font, layout gây dịch chuyển hoặc xử lý tương tác nặng, đo bằng DevTools/Lighthouse phù hợp; ghi viewport, browser, cấu hình network/CPU và kết quả. Theo dõi CLS, LCP, INP khi có công cụ/dữ liệu phù hợp; không coi điểm Lighthouse 95–100 là bằng chứng đạt mọi tiêu chí hay suy ra INP thực tế từ điểm lab.
- Báo file đổi, kết quả kiểm tra và phần còn thiếu. Ghi đạt/chưa đạt/chưa kiểm tra/không áp dụng kèm lý do; chỉ báo pass khi có bằng chứng.
- Chỉ sửa tài liệu rule thì đối chiếu nội dung, mâu thuẫn, định dạng và diff; build, HTML/CSS validator và browser test là không áp dụng nếu không đổi mã giao diện.

## Nguồn và ngoại lệ trong project

- [AGENTS.md của workspace](../../AGENTS.md) là nguồn convention giao diện; khi bổ sung hoặc sửa convention, đối chiếu hai file để tránh hai hướng dẫn mâu thuẫn. Yêu cầu mới nhất của người dùng và đề bài được ưu tiên.
- [AI_CONTEXT.md](AI_CONTEXT.md) là căn cứ về phạm vi hiện tại. Mẫu và tài liệu học nằm ngoài project không tự trở thành yêu cầu nghiệp vụ của ứng dụng.
- Không mang IE cũ, ActiveX, toolchain legacy, khóa zoom, chặn chuột phải/kéo ảnh/in, tracking hoặc yêu cầu riêng khách sạn Nhật vào bài hiện tại. Font/meta/alt/comment tiếng Nhật, tắt JS vẫn xem được và browser matrix của project mẫu chỉ áp dụng khi bài yêu cầu.
- Chỉ sửa tài liệu rule: đối chiếu nội dung, định dạng, link và diff; build, Pint, HTML/CSS validator và browser test không áp dụng nếu không đổi mã ứng dụng. Khi code giao diện, thực hiện kiểm tra và bàn giao theo mục ở trên.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified PHP files, run `vendor/bin/pint --format agent` with the explicit paths of the PHP files changed by the current task before finalizing. For example, after editing the web route, run `vendor/bin/pint --format agent routes/web.php`.
- Use `--dirty` only when every dirty PHP file belongs to the current task or the user has authorized formatting them all. Do not combine `--dirty` with file paths expecting it to limit formatting to those paths; it selects the dirty files and can change unrelated work.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# Pest and PHPUnit

- The current API feature suite uses Pest with the Laravel plugin, as requested by the user. Write new tests in the existing Pest style and reuse `tests/Pest.php`, fixtures and the isolated test database; keep legacy PHPUnit tests working.
- When generating tests with Artisan, inspect `php artisan make:test --help` and choose the format appropriate to the existing suite. Do not force new tests into PHPUnit class syntax when the task follows the Pest suite.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` with the affected file paths or a case filter for Pest tests. The current feature test commands and environment requirements are in `docs/products/create/testcase_create_product.md`.
- Use `vendor/bin/phpunit` for legacy PHPUnit tests when appropriate. It accepts a file path and `--filter=testName` arguments.
- Browser tests verify UI behavior; the API feature suite verifies request/response and database behavior. Report their results separately and mark unexecuted browser cases with a reason.

</laravel-boost-guidelines>
