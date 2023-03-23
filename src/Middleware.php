<?php

namespace WaxFramework\Routing;

class Middleware {
    protected static array $middleware = [];

    public static function set_middleware_list( array $middleware ) {
        static::$middleware = $middleware;
    }

    public static function is_user_allowed( array $middleware ) {
        return true;
    }
}