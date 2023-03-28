<p align="center">
<a href="https://packagist.org/packages/waxframework/routing"><img src="https://img.shields.io/packagist/dt/waxframework/routing" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/waxframework/routing"><img src="https://img.shields.io/packagist/v/waxframework/routing" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/waxframework/routing"><img src="https://img.shields.io/packagist/l/waxframework/routing" alt="License"></a>
</p>

# About WaxFrameWork Routing

WaxFramework Routing is a powerful routing system for WordPress plugins that is similar to the popular PHP framework Laravel. This package makes use of the WordPress REST route system and includes its own custom route system, known as the `Ajax Route`.

One of the key features of WaxFramework Routing is its support for middleware. Middleware allows you to perform additional actions before each request.

By using WaxFramework Routing in your WordPress plugin, you can easily create custom routes and middleware to handle a wide variety of requests, including AJAX requests, with ease. This makes it an excellent tool for developing modern and dynamic WordPress plugins that require advanced routing capabilities and additional security measures.

## Requirement

WaxFramework routing requires a dependency injection (DI) container. We do not use any hard-coded library, so you can choose to use any DI library you prefer. However, it is important to follow our DI structure, which includes having the `set`, `get`, and `call` methods in your DI container.

We recommend using [PHP-DI](https://php-di.org/) as it already has these 3 methods implemented in the package.

### Methods structure
Here is the structure of the methods that your DI container should have in order to work with WaxFramework routing:

1. `set` method

	```php
	/**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed $value Value, define objects
     */
    public function set( string $name, $value ) {}
	```
2. `get` method

	```php
	
    /**
     * Returns an entry of the container by its name.
     *
     * @template T
     * @param string|class-string<T> $name Entry name or a class name.
     *
     * @return mixed|T
     */
    public function get( $name ) {}
	```
3. `callback` method
	```php
	 /**
     * Call the given function using the given parameters.
     *
     * Missing parameters will be resolved from the container.
     *
     * @param callable $callable   Function to call.
	 * 
     * @return mixed Result of the function.
     */
    public function call( $callable ) {}
	```
## Installation

```
composer require waxframework/routing
```
## Configuration
1. Your plugin must include a `routes` folder. This folder will contain all of your plugin's route files.

2. Within the `routes` folder, create two subfolders: `ajax` and `rest`. These folders will contain your plugin's route files for AJAX and REST requests, respectively.

3. If you need to support different versions of your routes, you can create additional files within the `ajax` and `rest` subfolders. For example, you might create `v1.php` and `v2.php` files within the `ajax` folder to support different versions of your AJAX routes.

4. Folder structure example:
	```
	routes:
	    ajax:
	        api.php
		    v1.php
		    v2.php
	    rest:
	       api.php
		   v1.php
	```
5. In your `RouteServiceProvider` class, set the necessary properties for your route system. This includes setting the `rest and ajax namespaces`, the versions of your routes, any middleware you want to use, and the directory where your route files are located. Here's an example:
	```php

    <?php

    namespace MyPlugin\Providers;

    use WaxFramework\Contracts\Provider;
    use MyPlugin\Container;
    use WaxFramework\Routing\Providers\RouteServiceProvider as WaxRouteServiceProvider;

    class RouteServiceProvider extends WaxRouteServiceProvider implements Provider {

        public function boot() {

            /**
             * Set Di Container
             */
            parent::$container = new Container;

            /**
             * OR you use PHP-Container 
             * Uses https://php-di.org/doc/getting-started.html
             */
            // parent::$container = new DI\Container();


            /**
             * Set required properties
             */
            parent::$properties = [
                'rest'       => [
                    'namespace' => 'myplugin',
                    'versions'  => ['v1', 'v2']
                ],
                'ajax'       => [
                    'namespace' => 'myplugin',
                    'versions'  => []
                ],
                'middleware' => [
                    'admin' => \MyPlugin\Middleware\EnsureIsUserAdmin::class
                ],
                'routes-dir' => ABSPATH . 'wp-content/plugins/my-plugin/routes'
            ];

            parent::boot();
        }
    }

	```
6. Finally, execute the `boot` method of your `RouteServiceProvider` class using the `init` action hook, like so:

	```php
	add_action('init', function() {
		$route_service_provider = new \MyPlugin\Providers\RouteServiceProvider;
		$route_service_provider->boot();
	});
	```
That's it! Your plugin is now configured with WaxFrameWork Routing system, and you can start creating your own routes and handling requests with ease.