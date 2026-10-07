<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading($this->app->isLocal());
    }
    /* Bật cơ chế phát hiện lazy loading ở môi trường local
    giúp tìm những chỗ truy cập relationship chưa được tải trước.
    Đây là công cụ hỗ trợ kiểm tra N+1. */
}
