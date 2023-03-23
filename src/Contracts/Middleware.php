<?php

namespace WaxFramework\Routing\Contracts;

use WP_REST_Request;

interface Middleware {
    /**
    * Handle an incoming request.
    *
    * @param WP_REST_Request  $wp_rest_request
    * @return bool
    */
    public function handle( WP_REST_Request $wp_rest_request ): bool;
}