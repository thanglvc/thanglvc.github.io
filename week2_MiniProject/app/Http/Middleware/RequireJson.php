<?php

namespace App\Http\Middleware;

use App\Exceptions\JsonRequiredException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireJson
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasWriteBody = in_array(
            $request->method(),
            ['POST', 'PUT', 'PATCH'],
            true
        ); // Kiểm tra request có dùng POST, PUT hoặc PATCH không

        if ($hasWriteBody && ! $request->isJson()) { // Kiểm tra header Content-Type có chỉ định JSON không
            throw new JsonRequiredException; // Dừng request và ném lỗi 415 khi Content-Type không phù hợp
        }

        return $next($request); // Cho request đi tiếp tới bước xử lý sau
    }
}
