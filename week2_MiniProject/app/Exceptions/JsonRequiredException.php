<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

// Exception này biểu diễn lỗi HTTP 415 — Unsupported Media Type. Ta dùng nó khi request gửi dữ liệu với Content-Type không phù hợp.

class JsonRequiredException extends HttpException
{
    public function __construct()
    {
        parent::__construct(415, 'Content-Type must be application/json.');
    }
}
