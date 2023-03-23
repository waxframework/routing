<?php

namespace WaxFramework\Routing;

use WaxFramework\Routing\Providers\RouteServiceProvider;
use WaxFramework\Routing\Contracts\Middleware as MiddlewareContract;

class Middleware {
    protected static array $middleware = [];

    public static function set_middleware_list( array $middleware ) {
        static::$middleware = $middleware;
    }

    public static function is_user_allowed( array $middleware ) {
        $container = RouteServiceProvider::$container;
        foreach ( $middleware as $middleware_name ) {
            if ( array_key_exists( $middleware_name, static::$middleware ) ) {
                $current_middleware = static::$middleware[$middleware_name];
                $middleware_object  = $container->get( $current_middleware );
        
                if ( ! $middleware_object instanceof MiddlewareContract || ! $container->call( [$middleware_object, 'handle'] ) ) {
                    return false;
                }
            } else {
                return false;
            }
        }
        
        return true;
    }
}