<?php
declare(strict_types=1);

/**
 * CakePHP Application Class
 *
 */
namespace App;

use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class
 */
class Application extends BaseApplication
{
    /**
     * Load all the application configuration and bootstrap logic
     *
     */
    public function bootstrap(): void
    {
        parent::bootstrap();

        if (PHP_SAPI !== 'cli') {
            FactoryLocator::add('Table', (new TableLocator())->allowFallbackClass(false));
        }

        // Swagger/OpenAPI documentation
        try {
            if (!$this->getPlugins()->has('SwaggerBake')) {
                $this->addPlugin('SwaggerBake');
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Setup the middleware queue your application will use
     *
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))

            // Handle plugin/theme assets like CakePHP normally does
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies (JSON, XML, etc.)
            ->add(new BodyParserMiddleware());

        return $middlewareQueue;
    }

    /**
     * Register application container services
     *
     */
    public function services(ContainerInterface $container): void
    {
    }
}
