# Quy tắc code giao diện

Áp dụng cho mọi bài HTML, CSS/SCSS và JavaScript trong workspace. Đọc trước khi code hoặc review.

Cập nhật ngày 05/10/2026 theo `REVIEW_AGENTS_MASTER_PERSPECTIVE.md`; các đề xuất được điều chỉnh theo phạm vi bài và căn cứ kỹ thuật ở cuối file.

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

## Tài liệu tham chiếu và ngoại lệ legacy

Tra cứu khi cần chi tiết hoặc xử lý khác biệt:

- `Html,css,js/Ngày 1/Part 2/4. Front-end-coding-convention-new.xlsx`
- `Html,css,js/Ngày 2/Front-end-coding-convention-BEM.xlsx`
- `Html,css,js/Ngày 1/Part 2/5. [Front-end]checklist_code.xlsx`
- `Html,css,js/Ngày 1/Part 2/6. Review HTML-CSS-JS list bug tham khao.xlsx`
- `Html,css,js/Ngày 1/Part 1/3. Front-end-xulyHinh.xlsx`
- `reponsive/ghichu_pc_smart_html.txt`
- `reponsive/luu_y_khi_lam_response.doc`
- `REVIEW_AGENTS_MASTER_PERSPECTIVE.md` — nguồn đề xuất cập nhật, áp dụng theo các điều chỉnh kỹ thuật bên dưới.

Đọc `AI_CONTEXT.md` để biết phạm vi bài. Không tự mang IE cũ, ActiveX, Ruby Sass/Gulp API cũ, khóa zoom, chặn chuột phải/kéo ảnh/in, tracking hoặc yêu cầu riêng khách sạn Nhật sang bài hiện tại. Yêu cầu font/meta/alt/comment tiếng Nhật, tắt JS vẫn xem được và browser matrix của project mẫu chỉ áp dụng khi bài yêu cầu.

Các điểm đã điều chỉnh so với bản review ngày 05/10/2026:

- Đơn vị và viewport: dùng `rem` theo font root, không ép chuyển đơn vị tại 768px; chọn `svh`/`dvh` theo hành vi cần có, không xem `dvh` là cách loại bỏ mọi dịch chuyển. Căn cứ: [MDN — CSS length](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/length).
- Tải ảnh: giữ chỗ chống CLS và phân biệt eager/lazy với mức ưu tiên tải; không gán high priority cho mọi ảnh đầu trang. Căn cứ: [MDN — img](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/img), [web.dev — Fetch Priority](https://web.dev/articles/fetch-priority).
- Focus: áp focus trap cho modal; menu disclosure dùng luồng Tab thông thường. Căn cứ: [WAI — Modal Dialog](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/), [WAI — Disclosure Navigation](https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/examples/disclosure-navigation/).
- Dữ liệu: phân biệt escaping/sanitization, kiểm tra URL theo ngữ cảnh, giữ khoảng trắng có ý nghĩa trong mật khẩu và bảo vệ dữ liệu lưu phía client. Căn cứ: [OWASP — XSS Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html), [OWASP — Authentication](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html), [OWASP — HTML5 Security](https://cheatsheetseries.owasp.org/cheatsheets/HTML5_Security_Cheat_Sheet.html).
- Tổ chức code: ưu tiên responsive theo component cho phần mới; giữ kiến trúc hiện có khi sửa cục bộ. SVGO và sanitizer phải phù hợp toolchain/phạm vi dependency; không giả định bundler tự gom media query. Comment mặc định English, yêu cầu song ngữ tùy bài. Đây là lựa chọn convention của workspace.