<?php

namespace WaxFramework\Routing\Providers;

use WaxFramework\Routing\Response;
use WaxFramework\Routing\DataBinder;
use WaxFramework\Routing\Ajax;
use WaxFramework\Routing\Middleware;

abstract class RouteServiceProvider
{
    public static $container;

    protected static $properties;

    public function boot() {
        add_action( 'rest_api_init', [$this, 'action_rest_api_init'] );
        /**
         * Hook fire priority https://codex.wordpress.org/Plugin_API/Action_Reference
         */
        add_action( 'template_redirect', [$this, 'action_ajax_api_init'], 1 );
    }

    public function action_ajax_api_init() {
        global $wp_query;
        
        if ( isset( $wp_query->query['pagename'] ) && 0 === strpos( $wp_query->query['pagename'], static::$properties['ajax']['namespace'] ) ) {
            static::init_routes( 'ajax' );
            if ( ! Ajax::$route_found ) {
                Response::set_headers( [], 404 );
                echo wp_json_encode(
                    [
                        'code'    => 'ajax_no_route', 
                        'message' => 'No route was found matching the URL and request method.'
                    ] 
                );
            }
            exit;
        }
    }

    /**
     * Fires when preparing to serve a REST API request.
     */
    public function action_rest_api_init(): void {
        static::init_routes( 'rest' );
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