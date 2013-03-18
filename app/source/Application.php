<?php

namespace AppTest;

use Phalcon\Config\Adapter\Ini as Config;
use Phalcon\Loader;
use Phalcon\DI;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use PhalconExt\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Text;

class Application extends \Phalcon\Mvc\Application
{
    public static function init()
    {
        $application = new self();
        return $application->bootstrap();
    }

    public function bootstrap()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'AppTest'    => __DIR__,
            'PhalconExt' => __DIR__ . '/../library/PhalconExt/'
        ))->register();

        $di = new DI\FactoryDefault();

        $di->setShared('router', function() {
            $router = new Router(false);
            $router->add('/{controller:[\w\-]+}/?{action:[\w\-]*}{params:/?.*}')
                ->convert('action', function($action) {
                    return lcfirst(Text::camelize($action));
                });

            return $router;
        });

        $di->setShared('dispatcher', function() {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('AppTest\Controller');
            return $dispatcher;
        });

        $di->setShared('view', function() {
            $view = new View();
            $view->registerEngines(array(
                '.phtml' => 'PhalconExt\Mvc\View\Engine\Php'
            ));
            $view->setViewsDir(__DIR__ . '/../views/');
            return $view;
        });

        $config = new Config(__DIR__ . '/../config/config.ini');

        $di->setShared('db', function() use ($config) {
            return new DbAdapter((array) $config->database);
        });

        $this->setDI($di);

        return $this;
    }

    public function run()
    {
        try {
            echo $this->handle()->getContent();
        } catch(\Exception $e) {
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
        }
    }
}
