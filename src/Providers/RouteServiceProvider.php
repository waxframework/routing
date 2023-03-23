<?php

namespace WaxFramework\Routing\Providers;

use WaxFramework\Routing\Response;
use WaxFramework\Routing\DataBinder;
use WaxFramework\Routing\Ajax;
use WaxFramework\Routing\Middleware;

class RouteServiceProvider
{
    public static $container;

    protected static $properties;

    public function boot() {
        add_action( 'rest_api_init', [$this, 'action_rest_api_init'] );
    }

    /**
     * Fires when preparing to serve a REST API request.
     */
    public function action_rest_api_init(): void {
        static::init_routes( 'rest' );
    }

    public function ajax_init() : void {
        /** Allow for cross-domain requests (from the front end). */
        send_origin_headers();
        header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
        header( 'X-Robots-Tag: noindex' );

        // Require a valid action parameter.
        if ( empty( $_REQUEST['action'] ) || ! is_scalar( $_REQUEST['action'] ) ) {
            Response::set_headers( [], 404 );
            echo wp_json_encode(
                [
                    'code'    => 'ajax_no_route',
                    'message' => 'No route was found matching the URL and request method.'
                ]
            );
            exit;
        }

        static::init_routes( 'ajax' );

        if ( ! Ajax::$route_found ) {
            Response::set_headers( [], 404 );
            echo wp_json_encode(
                [
                    'code'    => 'ajax_no_route', 
                    'message' => 'No route was found matching the URL and request method.'
                ] 
            );
            exit;
        }
    }

    protected static function init_routes( string $type ) {
        Middleware::set_middleware_list( static::$properties['middleware'] );

        $data_binder = static::$container->get( DataBinder::class );
        
        $data_binder->set_namespace( static::$properties[$type]['namespace'] );

        include static::$properties['routes-dir'] . "/{$type}/api.php";

        $versions = static::$properties[$type]['versions'];

        if ( is_array( $versions ) ) {

            foreach ( $versions as $version ) {
                $version_file = static::$properties['routes-dir'] . "/{$type}/{$version}/api.php";

                if ( is_file( $version_file ) ) {
                    $data_binder->set_version( $version );
                    include $version_file;
                }
            }
        }
    }
}