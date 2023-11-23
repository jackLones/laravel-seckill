<?php

namespace App\Providers;

use App\Helpers\SnowflakeHelper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('snowflake', function () {
            return new SnowflakeHelper( config('app.datacenter_id'), config('app.machine_id')); // 设置数据中心ID和工作节点ID
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
